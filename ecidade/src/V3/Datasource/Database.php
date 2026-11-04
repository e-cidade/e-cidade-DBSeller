<?php

namespace ECidade\V3\Datasource;

use ECidade\Lib\Config\DbConn;
use ECidade\V3\Extension\Registry;
use Exception;
use stdClass;

class Database
{
    const USUARIO_PADRAO = 'postgres';

    /**
     * @var Database
     */
    public static $oInstance;

    /**
     * Resource da conexão com o banco
     * @var resource
     */
    private $oConnection;

    /**
     * Base de dados
     * @var string
     */
    private $sBase = "";

    /**
     * Servidor do banco de dados
     * @var string
     */
    private $sServidor = "";

    /**
     * Porta da base de dados
     * @var string
     */
    private $sPorta = "";

    /**
     * Usuário do banco
     * @var string
     */
    private $sUsuario = "";

    /**
     * Senha da base de dados
     * @var string
     */
    private $sSenha = "";

    /**
     * @param $sBase
     * @return $this
     */
    public function setBase($sBase)
    {
        $this->sBase = $sBase;
        return $this;
    }

    /**
     * @param $sServidor
     * @return $this
     */
    public function setServidor($sServidor)
    {
        $this->sServidor = $sServidor;
        return $this;
    }

    /**
     * @param $sPorta
     * @return $this
     */
    public function setPorta($sPorta)
    {
        $this->sPorta = $sPorta;
        return $this;
    }

    /**
     * @param $sUsuario
     * @return $this
     */
    public function setUsuario($sUsuario)
    {
        $this->sUsuario = $sUsuario;
        return $this;
    }

    /**
     * @param $sSenha
     * @return $this
     */
    public function setSenha($sSenha)
    {
        $this->sSenha = $sSenha;
        return $this;
    }

    public function getBase()
    {
        return $this->sBase;
    }

    /**
     * @return resource
     */
    public function getConnection()
    {
        return $this->oConnection;
    }

    /**
     * @param resource $oConnection
     */
    public function setConnection($oConnection)
    {
        $this->oConnection = $oConnection;
    }

    /**
     * Retorna o resource de conexão com o banco
     * @return resource
     */
    public function getResource()
    {
        return $this->oConnection;
    }

    /**
     * @return string
     */
    public function getServidor()
    {
        return $this->sServidor;
    }

    /**
     * @return string
     */
    public function getPorta()
    {
        return $this->sPorta;
    }

    /**
     * @return string
     */
    public function getUsuario()
    {
        return $this->sUsuario;
    }

    /**
     * @return string
     */
    public function getSenha()
    {
        return $this->sSenha;
    }

    /**
     * Database constructor.
     */
    protected function __construct()
    {
    }

    /**
     * Conecta na base de dados
     * @return resource
     * @throws Exception
     */
    public function connect()
    {
        /**
         * adicionado operador de supressão para não exibir dados da base em produção.
         * @todo achar uma alternativa melhor.
         * Talvez seja interessante logar o erro completo?
         * Mostrar o erro normalmente caso o .env esteja diferente de production?
         */
        $this->oConnection = @pg_connect(
            "host={$this->sServidor}
            port={$this->sPorta}
            dbname={$this->sBase}
            user={$this->sUsuario}
            password={$this->sSenha}"
        );

        if (!$this->oConnection) {
            throw new Exception("Não foi possível conectar na base de dados.");
        }

        return $this->oConnection;
    }

    /**
     * Desconecta da base de dados
     * @return bool
     */
    public function disconnect()
    {
        if ($this->oConnection) {
            return pg_close($this->oConnection);
        }
    }

    /**
     * @param string $encoding
     * @return bool
     */
    public function setEncoding($encoding)
    {
        return pg_set_client_encoding($this->oConnection, $encoding);
    }

    /**
     * Executa uma query na base de dados
     * @param string $sQuery Query a ser executada
     * @return resource
     * @throws Exception
     */
    public function execute($sQuery)
    {
        $rsResultSet = @pg_query($this->oConnection, $sQuery);

        if ($rsResultSet === false) {
            throw new Exception(pg_last_error($this->oConnection));
        }

        return $rsResultSet;
    }

    /**
     * Retorna um array de objetos com os registros do recordset
     * @param resource $rsRecordset
     * @return array
     */
    public function getCollectionByRecord($rsRecordset)
    {
        return pg_fetch_all($rsRecordset);
    }

    /**
     * Retorna um objeto do registro buscado
     * @param resource $resource
     * @param int $index
     * @return stdClass
     */
    public function fetchRow($resource, $index)
    {
        $oObject = pg_fetch_object($resource, $index);

        // Tratamento feito para não dar erro nos campos texto vazios que são not null
        foreach ($oObject as $mValue) {
            $mValue = trim($mValue);
        }

        return $oObject;
    }

    /**
     * Get Instance
     * @param bool $withDbConn
     * @param null $conexao
     * @return Database
     * @throws Exception
     */
    public static function getInstance($withDbConn = false, $conexao = null, $withCookie = false)
    {
        if (self::$oInstance == null) {
            self::$oInstance = self::build($withDbConn, $conexao, $withCookie);
        }

        return self::$oInstance;
    }

    /**
     * @return Database Retorna o getInstance
     * @throws Exception
     * @author Jeferson Belmiro <jeferson.belmiro@dbseller.com.br>
     */
    public static function init()
    {
        return self::getInstance();
    }

    private static function buildDbConn()
    {
        $connection = DbConn::getInstance();
        $database = new Database();
        $database->setBase($connection->base());
        $database->setPorta($connection->porta());
        $database->setSenha($connection->senha());
        $database->setUsuario($connection->usuario());
        $database->setServidor($connection->servidor());

        return $database;
    }


    private static function buildSession()
    {
        $session = Registry::get('app.request')->session();
        $database = new Database();
        $database->setBase($session->get('DB_NBASE', $session->get('DB_base')));
        $database->setServidor($session->get('DB_servidor'));
        $database->setPorta($session->get('DB_porta'));
        $database->setUsuario($session->get('DB_user'));
        $database->setSenha($session->get('DB_senha'));
        return $database;
    }

    /**
     * @param bool $withDbConn
     * @return Database
     * @throws Exception
     */
    private static function build($withDbConn = false, $conexao = null, $withCookie = false)
    {
        if ($withDbConn) {
            $database = static::buildDbConn();
        } else {
            $database = static::buildSession();
        }

        if ($withCookie) {
            $servidor = !empty($_COOKIE['DB_servidor']) ? $_COOKIE['DB_servidor'] : $database->getServidor();
            $base = !empty($_COOKIE['DB_base']) ? $_COOKIE['DB_base'] : $database->getBase();
            $porta = !empty($_COOKIE['DB_porta']) ? $_COOKIE['DB_porta'] : $database->getPorta();
            $usuario = !empty($_COOKIE['DB_usuario']) ? base64_decode($_COOKIE['DB_usuario']) : $database->getUsuario();
            $senha = !empty($_COOKIE['DB_senha']) ? base64_decode($_COOKIE['DB_senha']) : $database->getSenha();

            $database->setBase($base);
            $database->setServidor($servidor);
            $database->setPorta($porta);
            $database->setUsuario(empty($usuario) ? self::USUARIO_PADRAO : $usuario);
            $database->setSenha($senha);
        }

        if (is_null($conexao)) {
            $database->connect();
        } else {
            $database->setConnection($conexao);
        }

        $database->setEncoding(Registry::get('app.config')->get('db.client_encoding'));

        $database->execute("SELECT set_config('search_path', current_setting('search_path') || ',plugins', false);");
        return $database;
    }

    /**
     * @throws Exception
     */
    public function begin()
    {
        if (!$this->inTransation()) {
            $this->execute('BEGIN');
        }
    }

    /**
     * @throws Exception
     */
    public function commit()
    {
        if ($this->inTransation()) {
            $this->execute('COMMIT');
        }
    }

    /**
     * @throws Exception
     */
    public function rollback()
    {
        if ($this->inTransation()) {
            $this->execute('ROLLBACK');
        }
    }

    /**
     * @return bool
     */
    public function hasFailed()
    {
        switch (pg_transaction_status($this->oConnection)) {
            case PGSQL_TRANSACTION_INERROR:
                return true;
                break;
        }

        return false;
    }

    /**
     * @return bool
     */
    public function inTransation()
    {
        switch (pg_transaction_status($this->oConnection)) {
            case PGSQL_TRANSACTION_ACTIVE:
            case PGSQL_TRANSACTION_INTRANS:
                return true;
                break;
        }

        return false;
    }

    /**
     * @param resource $resource
     * @return int
     */
    public function numRows($resource)
    {
        return pg_num_rows($resource);
    }

    /**
     * @throws Exception
     */
    public function disableAudit()
    {
        $this->execute("SELECT fc_putsession('__disable_audit__', 'on');");
    }

    /**
     * @return bool
     */
    public function isConnected()
    {
        return is_resource($this->oConnection);
    }
}
