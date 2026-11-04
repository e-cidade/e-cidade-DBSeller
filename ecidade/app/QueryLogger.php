<?php

namespace App;

class QueryLogger
{
    protected static $queries = [];

    protected static $queriesError = [];

    public static function logQueryError($sql, $bindings)
    {
        self::$queriesError[] = [
            'sql' => $sql,
            'bindings' => $bindings
        ];
    }

    public static function logQuery($sql, $bindings, $time)
    {
        self::$queries[] = [
            'sql' => $sql,
            'bindings' => $bindings,
            'time' => $time,
        ];
    }

    public static function getQueries()
    {
        return self::$queries;
    }


    public static function getQueriesError()
    {
        return self::$queriesError;
    }

    public static function clearQueriesError()
    {
        self::$queriesError = [];
    }

    public static function clearQueries()
    {
        self::$queries = [];
    }
}
