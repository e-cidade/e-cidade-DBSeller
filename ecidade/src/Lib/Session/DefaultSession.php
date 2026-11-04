<?php


namespace ECidade\Lib\Session;

use DateTime;
use DBException;
use DBSeller\Session\Session;
use Exception;
use InstituicaoRepository;
use ParametroPCASP;
use UsuarioSistema;
use ECidade\Lib\Config\DbConn;

class DefaultSession extends Session
{
    const DB_CODIGO_USUARIO_PADRAO = 1;
    const DB_USE_PCASP = 'DB_use_pcasp';
    const DB_ANO_PCASP = 'DB_ano_pcasp';
    const DB_INSTIT = 'DB_instit';
    const DB_CODDEPTO = 'DB_coddepto';
    const DB_DATAUSU = 'DB_datausu';
    const DB_ANOUSU = 'DB_anousu';
    const DB_ID_USUARIO = 'DB_id_usuario';
    const DB_LOGIN = 'DB_login';
    const DB_IP = 'DB_ip';
    const DB_MODULO = 'DB_modulo';
    const DB_NOME_MODULO = 'DB_nome_modulo';
    const DB_UOL_HORA = 'DB_uol_hora';
    const DB_REQUEST_FROM_API = 'DB_request_from_api';
    const DB_ACESSADO = 'DB_acessado';
    const DB_BASE = 'DB_base';
    const DB_NBASE = 'DB_NBASE';
    const DB_ITEMMENU_ACESSADO = 'DB_itemmenu_acessado';
    const DB_PROCESSAMENTO_AUTOMATICO = 'DB_processamento_automatico';

    /**
     * @var DefaultSession
     */
    public static $instance;

    /**
     * @var DateTime
     */
    private $dataSessao;

    /**
     * DefaultSession constructor.
     */
    protected function __construct()
    {
        parent::__construct();
        $this->dataSessao = new DateTime();
    }

    /**
     * @return DefaultSession
     */
    public static function getInstance()
    {
        if (is_null(static::$instance)) {
            static::$instance = new self();
        }
        return static::$instance;
    }

    /**
     * @param $key
     * @param $value
     */
    private function addParam($key, $value)
    {
        if ($this->has($key)) {
            $this->set($key, $value);
        } else {
            $this->add(array($key => $value));
        }
    }

    private function addDefaultDadosPcasp()
    {
        if (!$this->has(self::DB_ANO_PCASP)) {
            $this->addParam(self::DB_ANO_PCASP, ParametroPCASP::getAnoInicioPCASP());
        }
        if (!$this->has(self::DB_USE_PCASP)) {
            $this->addParam(
                self::DB_USE_PCASP,
                ParametroPCASP::utilizaPCASPNoAno($this->dataSessao->format('Y')) ? 't' : 'f'
            );
        }
    }

    private function addDefaultInstituicao()
    {
        if (!$this->has(self::DB_INSTIT)) {
            $this->addParam(self::DB_INSTIT, InstituicaoRepository::getInstituicaoPrefeitura()->getCodigo());
        }
    }

    /**
     * @param $idUsuario
     * @param bool $sobrescreve
     * @throws DBException
     */
    private function addDadosUsuario($idUsuario, $sobrescreve = false)
    {
        if ($sobrescreve || !$this->has(self::DB_ID_USUARIO)) {
            $this->addParam(self::DB_ID_USUARIO, $idUsuario);
        }
        if ($sobrescreve || !$this->has(self::DB_LOGIN)) {
            $usuario = new UsuarioSistema($this->get(self::DB_ID_USUARIO));
            $this->addParam(self::DB_LOGIN, $usuario->getLogin());
        }
        if ($sobrescreve || !$this->has(self::DB_DATAUSU)) {
            $this->addParam(self::DB_DATAUSU, time());
        }
        if ($sobrescreve || !$this->has(self::DB_ANOUSU)) {
            $this->addParam(self::DB_ANOUSU, $this->dataSessao->format('Y'));
        }
    }

    /**
     * @param integer $idUsuario
     * @throws DBException
     */
    public function atualizaDadosUsuario($idUsuario)
    {
        $this->addDadosUsuario($idUsuario, true);
    }

    /**
     * @throws DBException
     */
    private function addDefaultDadosUsuario()
    {
        $this->addDadosUsuario(self::DB_CODIGO_USUARIO_PADRAO);
    }

    private function addDefaultIp()
    {
        if (!$this->has(self::DB_IP)) {
            $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : "";

            if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            }

            $this->addParam('DB_ip', $ip);
        }
    }

    private function addDefaultDadosUsuarioOnline()
    {
        if (!$this->has(self::DB_UOL_HORA)) {
            $this->addParam(self::DB_UOL_HORA, time());
        }
    }

    public function addDefaultBase()
    {
        if (!$this->has(self::DB_BASE)) {
            $this->addParam(self::DB_BASE, DbConn::getInstance()->base());
            $this->addParam(self::DB_NBASE, DbConn::getInstance()->base());
        }
    }

    /**
     * @throws DBException
     */
    private function addDefaultParams()
    {
        $this->addDefaultDadosPcasp();
        $this->addDefaultInstituicao();
        $this->addDefaultDadosUsuario();
        $this->addDefaultIp();
        $this->addDefaultDadosUsuarioOnline();
        $this->addDefaultBase();
    }

    /**
     * @return $this|Session
     * @throws Exception
     */
    public function start()
    {
        parent::start();
        $this->addDefaultParams();
        return $this;
    }

    /**
     * Função que recebe uma Request do laravel e seta os valores enviados na sessao
     *
     * @param array $request
     * @return $this|Session
     * @throws Exception
     */
    public function addFromRequest($request)
    {
        //Valores default
        $sessaoMenu = array(
            DefaultSession::DB_MODULO => 1,
            DefaultSession::DB_NOME_MODULO => 'Configuração',
            DefaultSession::DB_ACESSADO => 24
        );

        if (isset($request[self::DB_INSTIT])) {
            $sessaoMenu[self::DB_INSTIT] = $request[self::DB_INSTIT];
        }

        if (isset($request[self::DB_CODDEPTO])) {
            $sessaoMenu[self::DB_CODDEPTO] = $request[self::DB_CODDEPTO];
        }

        if (isset($request[self::DB_USE_PCASP])) {
            $sessaoMenu[self::DB_USE_PCASP] = $request[self::DB_USE_PCASP];
        }

        if (isset($request[self::DB_ANO_PCASP])) {
            $sessaoMenu[self::DB_ANO_PCASP] = $request[self::DB_ANO_PCASP];
        }

        if (isset($request[self::DB_DATAUSU])) {
            $sessaoMenu[self::DB_DATAUSU] = $request[self::DB_DATAUSU];
        }

        if (isset($request[self::DB_ANOUSU])) {
            $sessaoMenu[self::DB_ANOUSU] = $request[self::DB_ANOUSU];
        }

        if (isset($request[self::DB_ID_USUARIO])) {
            $sessaoMenu[self::DB_ID_USUARIO] = $request[self::DB_ID_USUARIO];
        }

        if (isset($request[self::DB_LOGIN])) {
            $sessaoMenu[self::DB_LOGIN] = $request[self::DB_LOGIN];
        }

        if (isset($request[self::DB_IP])) {
            $sessaoMenu[self::DB_IP] = $request[self::DB_IP];
        }

        if (isset($request[self::DB_MODULO])) {
            $sessaoMenu[self::DB_MODULO] = $request[self::DB_MODULO];
        }

        if (isset($request[self::DB_NOME_MODULO])) {
            $sessaoMenu[self::DB_NOME_MODULO] = $request[self::DB_NOME_MODULO];
        }

        if (isset($request[self::DB_UOL_HORA])) {
            $sessaoMenu[self::DB_UOL_HORA] = $request[self::DB_UOL_HORA];
        }

        if (isset($request[self::DB_REQUEST_FROM_API])) {
            $sessaoMenu[self::DB_REQUEST_FROM_API] = $request[self::DB_REQUEST_FROM_API];
        }

        if (isset($request[self::DB_ACESSADO])) {
            $sessaoMenu[self::DB_ACESSADO] = $request[self::DB_ACESSADO];
        }

        if (isset($request[self::DB_BASE])) {
            $sessaoMenu[self::DB_BASE] = $request[self::DB_BASE];
            $sessaoMenu[self::DB_NBASE] = $request[self::DB_BASE];
        }

        if (isset($request[self::DB_ITEMMENU_ACESSADO])) {
            $sessaoMenu[self::DB_ITEMMENU_ACESSADO] = $request[self::DB_ITEMMENU_ACESSADO];
        }


        $this->add($sessaoMenu, true);

        return $this;
    }
}
