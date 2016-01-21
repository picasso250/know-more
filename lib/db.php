<?php

/**
 * a very small db wrapper
 * v0.0.1
 */
class db
{
    static $pdo;
    static $debug = false;
    public static function connect($config)
    {
        $options = array(Pdo::MYSQL_ATTR_INIT_COMMAND => 'set names utf8');
        self::$pdo = new Pdo($config['dsn'], $config['username'], $config['password'], $options);
        self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    public function reset()
    {
        $this->select = '*';
        $this->join = null;
        $this->where = [[], []];
        $this->order = null;
        $this->limit = 1111;
        $this->fetchMode = [PDO::FETCH_ASSOC];
        $this->vals = [];
    }
    public static function from($table)
    {
        if (!self::$pdo) {
            self::connect();
        }
        $o = new self;
        if (is_array($table)) {
            $table = key($table).' AS '.reset($table);
        }
        $o->table = $table;
        $o->reset();
        return $o;
    }
    public function id($id)
    {
        return $this->where(compact('id'))->limit(1)->queryRow();
    }
    public function __call($name, $args): self
    {
        if (count($args) !== 1) {
            throw new Exception("$name should have 1 arg", 1);
        }
        $this->$name = $args[0];
        return $this;
    }
    public function where(array $args): self
    {
        foreach ($args as $key => $value) {
            if (is_int($key)) {
                $this->where[0][] = $value;
            } else {
                $a = explode('.', $key);
                if (count($a) > 1) {
                    assert(count($a) == 2);
                    $keyparam = $a[1];
                } else {
                    $keyparam = $key;
                }
                if (is_array($value)) {
                    $in_arr = [];
                    foreach ($value as $k => $v) {
                        $in_arr[] = ":$keyparam$k";
                        $this->where[1]["$keyparam$k"] = $v;
                    }
                    $in_str = implode(',', $in_arr);
                    $str = "$key IN ($in_str)";
                } else {
                    $str = "$key=:$keyparam";
                    $this->where[1][$keyparam] = $value;
                }
                $this->where[0][] = $str;
            }
        }
        return $this;
    }
    public function queryAll()
    {
        $this->buildSql();
        $stmt = $this->execute($this->sql, $this->vals);
        return $stmt->fetchAll();
    }
    public function queryRow()
    {
        $this->limit(1);
        $this->buildSql();
        $stmt = $this->execute($this->sql, $this->vals);
        return $stmt->fetch();
    }
    public function queryColumn()
    {
        $this->buildSql();
        $stmt = $this->execute($this->sql, $this->vals);
        $ret = [];
        while (($row = $stmt->fetch(Pdo::FETCH_NUM)) !== false) {
            $ret[] = $row[0];
        }
        return $ret;
    }
    public function queryScalar()
    {
        $this->limit(1);
        $this->buildSql();
        $stmt = $this->execute($this->sql, $this->vals);
        $a = $stmt->fetch(Pdo::FETCH_NUM);
        if ($a) {
            return $a[0];
        }
        return $a;
    }
    public function setFetchMode(): self
    {
        $this->fetchMode = func_get_args();
        return $this;
    }
    public function execute(string $sql, array $vals): PDOStatement
    {
        if (self::$debug === true) {
            $logsql = str_replace('?', '%s', $sql);
            $args = array_merge([$sql], $vals);
            $logsql = call_user_func_array('sprintf', $args);
            foreach ($vals as $key => $value) {
                $logsql = str_replace(":$key", self::$pdo->quote($value), $logsql);
            }
            $this->sql = $logsql;
            error_log($logsql);
        }
        $stmt = self::$pdo->prepare($sql);
        $stmt->execute($vals);
        call_user_func_array([$stmt, 'setFetchMode'], $this->fetchMode);
        return $stmt;
    }
    public function buildEqual(array $data): array
    {
        $arr = [];
        foreach ($data as $key => $value) {
            if (is_int($key)) {
                $arr[] = $value;
            } else {
                $arr[] = "$key=:$key";
            }
        }
        return $arr;
    }
    public function buildWhere(): string
    {
        $where = implode(' AND ', $this->where[0]);
        return " WHERE $where ";
    }
    public function buildSql()
    {
        $sql = "SELECT $this->select FROM $this->table";
        if ($this->join) {
            $sql .= " JOIN $this->join";
        }
        if ($this->where[0]) {
            $sql .= $this->buildWhere();
            $this->vals = $this->where[1];
        }
        if ($this->order) {
            $sql .= " ORDER $this->order";
        }
        $sql .= " LIMIT $this->limit";
        $this->sql = $sql;
    }

    public function buildSet(array $data): string
    {
        $set = implode(',', $this->buildEqual($data));
        return "SET $set";
    }
    public function insert(array $data): string
    {
        $set = $this->buildSet($data);
        $sql = "INSERT INTO `$this->table` $set";
        $this->execute($sql, $data);
        return db::$pdo->lastInsertId();
    }
    function update(array $data): int
    {
        $set = $this->buildSet($data);
        $where = $this->buildWhere();
        $sql = "UPDATE $this->table $set $where";
        $stmt = $this->execute($sql, array_merge($this->where[1], $data));
        return $stmt->rowCount();
    }
}
