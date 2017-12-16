<?php

namespace TclUpdates;

class SQLiteReader
{
    const OTA_ONLY = 0;
    const FULL_ONLY = 1;
    const BOTH =2;
    private $dbFile;
    private $pdo;

    public function __construct()
    {
        $this->dbFile = 'otadb.db3';
        $this->pdo = new \PDO('sqlite:' . $this->dbFile);
        if ($this->pdo === false) {
            return false;
        }
        $this->pdo->exec('PRAGMA foreign_keys=on;');
    }

    public function getAllRefs()
    {
        $sql = 'SELECT DISTINCT curef FROM updates ORDER BY curef;';
        $sqlresult = $this->pdo->query($sql);
        $result = array();
        foreach ($sqlresult as $row) {
            $result[] = $row[0];
        }
        return $result;
    }

    public function getAllKnownRefs()
    {
        $sql = 'SELECT DISTINCT ref FROM devices ORDER BY ref;';
        $sqlresult = $this->pdo->query($sql);
        $result = array();
        foreach ($sqlresult as $row) {
            $result[] = $row[0];
        }
        return $result;
    }

    public function getUnknownRefs()
    {
        $knownPrds = $this->getAllKnownRefs();
        $allPrds   = $this->getAllRefs();
        $unknownPrds = array_diff($allPrds, $knownPrds);
        return $unknownPrds;
    }

    public function getAllVariants()
    {
        $sql = 'SELECT f.name, m.name, d.ref, d.name FROM families f LEFT JOIN models m ON f.familyId=m.familyId LEFT JOIN devices d ON m.modelId=d.modelId;';
        $sqlresult = $this->pdo->query($sql);
        $result = array();
        foreach ($sqlresult as $row) {
            $family = $row[0];
            $model  = $row[1];
            $ref = $row[2];
            $variant = $row[3];
            if (!isset($result[$family])) {
                $result[$family] = array();
            }
            if (!isset($result[$family][$model])) {
                $result[$family][$model] = array();
            }
            $result[$family][$model][$ref] = $variant;
        }
        return $result;
    }

    public function getAllUpdates($ref, $which = self::BOTH)
    {
        $sql = 'SELECT * FROM updates u LEFT JOIN files f ON u.file_sha1=f.sha1 WHERE curef=?';
        if ($which == self::OTA_ONLY) {
            $sql .= ' AND fv IS NOT null';
        } elseif ($which == self::FULL_ONLY) {
            $sql .= ' AND fv IS null';
        }
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute(array($ref));
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function getLatestUpdate($ref, $which = self::BOTH)
    {
        $sql = 'SELECT * FROM updates u LEFT JOIN files f ON u.file_sha1=f.sha1 WHERE curef=?';
        if ($which == self::OTA_ONLY) {
            $sql .= ' AND fv IS NOT null';
        } elseif ($which == self::FULL_ONLY) {
            $sql .= ' AND fv IS null';
        }
        $sql .= ' ORDER BY tv DESC, fv DESC LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute(array($ref));
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }

    public function getAllVersionsForRef($ref = null)
    {
        $sql = 'SELECT fv, tv FROM updates u LEFT JOIN files f ON u.file_sha1=f.sha1';
        $params_arr = array();
        if (!is_null($ref)) {
            $sql .= ' WHERE curef=?';
            $params_arr[] = $ref;
        }
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute($params_arr);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $version = array();
        foreach ($result as $row) {
            if (!is_null($row['fv'])) {
                $version[] = $row['fv'];
            }
            $version[] = $row['tv'];
        }
        $version = array_unique($version);
        sort($version);
        return $version;
    }

    public function getAllVersionsForModel($model)
    {
        $sql = 'SELECT fv, tv FROM models m LEFT JOIN devices d ON m.modelId=d.modelId LEFT JOIN updates u ON d.ref=u.curef LEFT JOIN files f ON u.file_sha1=f.sha1 WHERE m.name=?';
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute(array($model));
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $version = array();
        foreach ($result as $row) {
            if (!is_null($row['fv'])) {
                $version[] = $row['fv'];
            }
            if (!is_null($row['tv'])) {
                $version[] = $row['tv'];
            }
        }
        $version = array_unique($version);
        sort($version);
        return $version;
    }
}
