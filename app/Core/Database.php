<?php
// ============================================
// Database — PDO Singleton
// ============================================

class Database {
    private static ?PDO $pdo = null;

    public static function pdo(): PDO {
        if (self::$pdo === null) {
            self::$pdo = new PDO(
                'mysql:host=127.0.0.1;port=3306;dbname=piulcell_db;charset=utf8mb4',
                'root',
                '', // <- biarkan kosong kalau Laragon default. Isi password kalau Anda set password MySQL
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        }
        return self::$pdo;
    }

    public static function q(string $sql, array $params = []): PDOStatement {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function row(string $sql, array $params = []): ?array {
        $r = self::q($sql, $params)->fetch();
        return $r === false ? null : $r;
    }

    public static function all(string $sql, array $params = []): array {
        return self::q($sql, $params)->fetchAll();
    }

    public static function val(string $sql, array $params = []) {
        $r = self::q($sql, $params)->fetchColumn();
        return $r === false ? null : $r;
    }

    public static function insert(string $table, array $data): int {
        $cols         = array_keys($data);
        $placeholders = array_map(fn($c) => ':' . $c, $cols);
        $sql          = "INSERT INTO {$table} (" . implode(',', $cols) . ") VALUES (" . implode(',', $placeholders) . ")";
        self::q($sql, $data);
        return (int) self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams = []): int {
        $set = [];
        $params = [];
        foreach ($data as $k => $v) {
            $set[] = "{$k} = :_set_{$k}";
            $params["_set_{$k}"] = $v;
        }

        // Convert positional WHERE params to named params to avoid mixing with named SET params.
        if ($whereParams && array_keys($whereParams) === range(0, count($whereParams) - 1)) {
            $whereSql = $where;
            foreach ($whereParams as $idx => $value) {
                $name = ":_where_{$idx}";
                $whereSql = preg_replace('/\?/', $name, $whereSql, 1);
                $params["_where_{$idx}"] = $value;
            }
        } else {
            $whereSql = $where;
            $params = array_merge($params, $whereParams);
        }

        $sql = "UPDATE {$table} SET " . implode(',', $set) . " WHERE {$whereSql}";
        return self::q($sql, $params)->rowCount();
    }

    public static function delete(string $table, string $where, array $params = []): int {
        return self::q("DELETE FROM {$table} WHERE {$where}", $params)->rowCount();
    }
}