<?php

namespace TclUpdates;

class SQLiteWriter
{
    private $dbFile;
    private $pdo;

    public function __construct()
    {
        $this->dbFile = 'otadb.db3';
        $this->pdo = new \PDO('sqlite:' . $this->dbFile, 0666, $sqlerror);
        if ($this->pdo === false) {
            return $sqlerror;
        }
    }

    private function insertArray($table, $data)
    {
        $placeholders = array_fill(0, count($data), '?');
        $sql = 'INSERT INTO "' . $table . '" (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', $placeholders) . ')';
        $stmt = $this->pdo->prepare($sql);
        $ok = $stmt->execute(array_values($data));
        return $ok;
    }

    public function addGotu(GotuObject $g)
    {
        $ok = $this->insertArray('updates', array(
            'tv' => $g->tv,
            'fv' => $g->fv,
            'svn' => $g->svn,
            'pubDate' => $g->time,
            'publisher' => $g->publisher,
            'fwId' => $g->fw_id,
            'file_id' => $g->file_id,
            'file_name' => $g->filename,
            'file_size' => $g->file_size,
            'file_sha1' => $g->file_chksum,
            'type' => $g->type,
            'note' => json_encode(array(
                'en' => $g->description_en,
                'ja' => $g->description_ja,
                'zh' => $g->description_zh,
            ))
        ));
        if ($ok) {
            $key = $this->pdo->lastInsertId();
            echo "Added entry " . $key . PHP_EOL;
        } else {
            echo "FAILED inserting." . PHP_EOL;
        }
    }
}
