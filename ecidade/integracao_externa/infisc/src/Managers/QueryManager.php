<?php

namespace IntegracaoExterna\Infisc\Managers;

class QueryManager
{

    private $connection;

    private $queryRecord;

    /**
     * @param $connection
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return $this
     */
    public function begin()
    {
        $this->runRawQuery("BEGIN;");

        return $this;
    }

    /**
     * @return $this
     */
    public function commit()
    {
        $this->runRawQuery("COMMIT;");

        return $this;
    }

    /**
     * @return $this
     */
    public function rollback()
    {
        $this->runRawQuery("ROLLBACK;");

        return $this;
    }

    /**
     * @param $tableName
     * @param $fields
     * @param $where
     * @param $limit
     * @return $this
     */
    public function query($tableName, $fields = "*", $where = [], $limit = null, $orderBy = null)
    {
        $sql = "select {$fields} from {$tableName}";

        if ($where) {
            $sql .= $this->buildQueryWhere($where);
        }

        if ($orderBy != "") {
            $sql .= " order by {$orderBy}";
        }

        if ($limit != null) {
            $sql .= " limit {$limit}";
        }

        $this->runRawQuery($sql);

        return $this;
    }

    /**
     * @param $tableName
     * @param $fields
     * @param $where
     * @return bool
     */
    public function update($tableName, $fields = [], $where = [])
    {
        $sql = "update {$tableName} set ";

        if ($fields) {
            $sql .= implode(" , ", $fields);
        }

        if ($where) {
            $sql .= $this->buildQueryWhere($where);
        }

        $this->runRawQuery($sql);

        return !$this->hasError();
    }

    /**
     * @throws \Exception
     */
    public function insert($tableName, $fields)
    {
        if (!$tableName) {
            throw new \Exception("Informe o nome da tabela.");
        }

        if (!$fields || count($fields) == 0) {
            throw new \Exception("Informe os campos para processar a inserção.");
        }

        $fieldsName = [];
        $fieldsValue = [];

        foreach ($fields as $field) {
            $value = $field[1];
            $type = isset($field[2]) ? $field[2] : null;

            if ($type == "s") {
                $value = "'{$value}'";
            }

            $fieldsName[] = $field[0];
            $fieldsValue[] = $value;
        }

        $fields = implode(", ", $fieldsName);
        $values = implode(", ", $fieldsValue);

        $sql = "INSERT INTO {$tableName} ({$fields}) VALUES ({$values});";
        $this->runRawQuery($sql);

        return !$this->hasError();
    }

    /**
     * @param $sql
     * @return $this
     */
    public function runRawQuery($sql)
    {
        $this->queryRecord = db_query_integracao_externa($this->connection, $sql, "", false);

        return $this;
    }

    /**
     * @return stdClass[]|null
     */
    public function getCollection()
    {
        if ($this->hasError() || !$this->queryRecord) {
            return null;
        }

        return \db_utils::getCollectionByRecord($this->queryRecord);
    }

    /**
     * @return stdClass|null
     */
    public function get()
    {
        if ($this->hasError() || pg_num_rows($this->queryRecord) == 0) {
            return null;
        }

        return \db_utils::fieldsMemory($this->queryRecord, 0);
    }

    public function exists()
    {
        if ($this->get()) {
            return true;
        }

        return false;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return pg_last_error($this->connection);
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return !!pg_last_error($this->connection) || !$this->queryRecord;
    }

    private function buildQueryWhere($wheres)
    {
        if (count($wheres) == 0) {
            return "";
        }

        if (!is_array($wheres[0])) {
            return sprintf(" where %s", implode(" AND ", $wheres));
        }

        $sqlWhere = [];

        foreach ($wheres as $where) {
            $campo = $where[0];
            $valor = $where[1];
            $tipo = isset($where[2]) ? $where[2] : "%d";
            $condicao = isset($where[3]) ? $where[3] : "=";

            if ($tipo == "%s") {
                $valor = "'{$valor}'";
            }

            $sqlWhere[] = sprintf("{$campo} {$condicao} {$tipo}", $valor);
        }

        return sprintf(" where %s", implode(" AND ", $sqlWhere));
    }
}
