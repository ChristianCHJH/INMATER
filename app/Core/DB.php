<?php

namespace App\Core;

use PDO;
use PDOException;

class DB
{
    private static $instances = [];

    private function __construct() {}

    private static function loadConfig()
    {
        return include __DIR__ . '/../../config/database.php';
    }

    public static function getConnection($connectionName = 'appinmater_modulo')
    {
        if (!isset(self::$instances[$connectionName])) {
            $config = self::loadConfig();

            if (!isset($config['connections'][$connectionName])) {
                throw new PDOException("No se pudo encontrar la configuración para la conexión '{$connectionName}'");
            }

            $dbConfig = $config['connections'][$connectionName];
            $dsn = "{$dbConfig['driver']}:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instances[$connectionName] = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $options);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instances[$connectionName];
    }

    public static function beginTransaction($connectionName = 'appinmater_modulo')
    {
        self::getConnection($connectionName)->beginTransaction();
    }

    public static function commit($connectionName = 'appinmater_modulo')
    {
        self::getConnection($connectionName)->commit();
    }

    public static function rollBack($connectionName = 'appinmater_modulo')
    {
        self::getConnection($connectionName)->rollBack();
    }

    public static function prepare($sql, $connectionName = 'appinmater_modulo')
    {
        return self::getConnection($connectionName)->prepare($sql);
    }

    public static function query($sql, $connectionName = 'appinmater_modulo')
    {
        return self::getConnection($connectionName)->query($sql);
    }

    public static function table($table, $connectionName = 'appinmater_modulo')
    {
        return new class($table, $connectionName) {
            private $table;
            private $connection;
            private $wheres = [];
            private $raws = [];
            private $updates = [];

            public function __construct($table, $connectionName)
            {
                $this->table = $table;
                $this->connection = DB::getConnection($connectionName);
            }

            public function insert(array $data)
            {
                $columns = implode(',', array_keys($data));
                $placeholders = ':' . implode(',:', array_keys($data));
                $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($data);
                return $this->connection->lastInsertId();
            }

            public function update(array $data)
            {
                $set = implode(',', array_map(function ($key) {
                    return "{$key}=:{$key}";
                }, array_keys($data)));

                $where = $this->buildWhereClause();
                $params = array_merge($data, $this->buildWhereParams());

                $sql = "UPDATE {$this->table} SET {$set} {$where}";
                $stmt = $this->connection->prepare($sql);
                return $stmt->execute($params);
            }

            public function select($columns = '*')
            {
                $where = $this->buildWhereClause();
                $params = $this->buildWhereParams();

                $sql = "SELECT {$columns} FROM {$this->table} {$where}";
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetchAll();
            }

            public function first()
            {
                $where = $this->buildWhereClause();
                $params = $this->buildWhereParams();

                $sql = "SELECT * FROM {$this->table} {$where} LIMIT 1";
                $stmt = $this->connection->prepare($sql);
                $stmt->execute($params);
                return $stmt->fetch();
            }

            public function where($column, $operator = '=', $value = null)
            {
                if ($value === null) {
                    $value = $operator;
                    $operator = '=';
                }

                $this->wheres[] = [
                    'column' => $column,
                    'operator' => $operator,
                    'value' => $value,
                ];
                return $this;
            }

            public function raw($expression)
            {
                $this->raws[] = $expression;
                return $this;
            }

            private function buildWhereClause()
            {
                if (empty($this->wheres) && empty($this->raws)) {
                    return '';
                }

                $clauses = array_map(function ($where) {
                    return "{$where['column']} {$where['operator']} :where_{$where['column']}";
                }, $this->wheres);

                if (!empty($this->raws)) {
                    $clauses = array_merge($clauses, $this->raws);
                }

                return 'WHERE ' . implode(' AND ', $clauses);
            }

            private function buildWhereParams()
            {
                $params = [];
                foreach ($this->wheres as $where) {
                    $params["where_{$where['column']}"] = $where['value'];
                }
                return $params;
            }
        };
    }

    public static function raw($expression)
    {
        return $expression;
    }
}
?>
