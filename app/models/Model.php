<?php

namespace app\models;

use PDO;
use PDOException;

abstract class Model {

    protected $table;

    public function findAll() {
        $query = "SELECT * FROM $this->table";
        return $this->fetchAll($query);
    }

    private function connect() {

        $type = 'mysql';
        $port = '8889';
        $charset = 'utf8mb4';

        $dsn = "$type:hostname=" . DBHOST . ";dbname=" . DBNAME . ";port=$port;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        try {
            return new PDO($dsn, DBUSER, DBPASS, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }

    }

    public function fetch($query) {
        $connectedPDO = $this->connect();
        $statementObject = $connectedPDO->query($query);
        return $statementObject->fetch();
    }

    public function fetchAll($query) {
        $connectedPDO = $this->connect();
        $statementObject = $connectedPDO->query($query);
        return $statementObject->fetchAll();
    }

    public function fetchAllWithParams($query, $data = []) {
        $connection = $this->connect();
        $statementObject = $connection->prepare($query);
        $successOrFail = $statementObject->execute($data);
        if ($successOrFail) {
            $result = $statementObject->fetchAll(PDO::FETCH_OBJ);
            if (is_array($result) && count($result)) {
                return $result;
            }
        }
        return false;
    }

    public function fetchOneWithParams($query, $data = []) {
        $connection = $this->connect();
        $statementObject = $connection->prepare($query);
        $statementObject->execute($data);
        return $statementObject->fetch(PDO::FETCH_ASSOC);
    }

    public function executeWithParams($query, $data = []) {
        $connection = $this->connect();
        $statementObject = $connection->prepare($query);
        return $statementObject->execute($data);
    }
}