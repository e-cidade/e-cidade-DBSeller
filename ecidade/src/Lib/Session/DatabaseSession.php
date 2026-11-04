<?php

namespace ECidade\Lib\Session;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use ECidade\V3\Datasource\Database;
use ECidade\V3\Window\Session;
use Exception;

class DatabaseSession
{
    /**
     * @var DatabaseSession
     */
    public static $instance;

    public static $sessionExecutar = [];

    /**
     * DatabaseSession constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @return DatabaseSession
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * @throws Exception
     */
    private function start()
    {
        self::$sessionExecutar['start'] = 'fc_startsession()';
    }

    /**
     * @param $key
     * @param $value
     * @throws Exception
     */
    private function add($key, $value)
    {
        $escapedValue = pg_escape_string($value);
        $function = "fc_putsession('{$key}', '{$escapedValue}')";
        self::$sessionExecutar[$key] = $function;
    }

    /**
     * @param $value
     * @throws Exception
     */
    private function adicionarDataHora($value)
    {
        $time = microtime(true);
        $microTime = sprintf("%06d", ($time - floor($time)) * 1000000);
        $timeNow = date("H:i:s");
        $dateTime = date("Y-m-d {$timeNow}.{$microTime}O", $value);

        $this->add('DB_DATAHORAUSU', $dateTime);
    }

    /**
     * @param $value
     * @throws Exception
     */
    private function adicionarData($value)
    {
        $data = date("Y-m-d", $value);
        $this->add('DB_DATAUSU', $data);
    }

    /**
     * @throws Exception
     */
    public function addSessionToDatabase()
    {
        if (session_status() !== Session::ACTIVE) {
            throw new Exception('Sessão não está ativa.');
        }

        if (!Database::getInstance()->isConnected()) {
            throw new Exception('Não existe conexão com o banco de dados.');
        }

        self::$sessionExecutar = [];
        $this->start();

        foreach ($_SESSION as $prop => $value) {
            $key = strtoupper($prop);

            switch ($key) {
                case 'DB_DATAUSU':
                    $this->adicionarDataHora($value);
                    $this->adicionarData($value);
                    break;
                default:
                    if (substr($key, 0, 2) == "DB") {
                        $this->add($key, pg_escape_string($value));
                    }
                    break;
            }
        }

        $this->run();
        return true;
    }

    private function run()
    {
        $sql = sprintf(' %s', implode(', ', self::$sessionExecutar));

        Database::getInstance()->execute("select $sql");
        DBConfig::selectRaw($sql)->first(); // Laravel
    }
}
