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

    public function getAllPrds()
    {
        $sql = 'SELECT DISTINCT curef FROM updates ORDER BY curef;';
        $sqlresult = $this->pdo->query($sql);
        $result = array();
        foreach ($sqlresult as $row) {
            $result[] = $row[0];
        }
        return $result;
    }

    public function getAllKnownPrds()
    {
        $sql = 'SELECT DISTINCT ref FROM devices ORDER BY ref;';
        $sqlresult = $this->pdo->query($sql);
        $result = array();
        foreach ($sqlresult as $row) {
            $result[] = $row[0];
        }
        return $result;
    }

    public function getUnknownPrds()
    {
        $knownPrds = $this->getAllKnownPrds();
        $allPrds   = $this->getAllPrds();
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
}
