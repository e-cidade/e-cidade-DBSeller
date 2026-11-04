<?php

/**
 * Class Portaria Responsavel por gerar o  Objeto Portaria
 *
 * @author Augusto Oliveira <augusto.oliveira@dbseller.com.br>
 */
class Portaria
{

    /**
     * @var $sequencial
     */
    private $sequencial;

    /**
     * @var $usuario
     */
    private $usuario;
    /**
     * @var $anousu
     */
    private $anousu;
    /**
     * @var $dtportaria
     */
    private $dtportaria;
    /**
     * @var $dtinicio
     */
    private $dtinicio;
    /**
     * @var $dtlanc
     */
    private $dtlanc;
    /**
     * @var $amparolegal
     */
    private $amparolegal;

    /**
     * @var $amparolegal
     */
    private $numeroportaria;

    /**
     * @var $portariatipo
     */
    private $portariatipo;

    /**
     * @return mixed
     */
    public function getPortariatipo()
    {
        return $this->portariatipo;
    }

    /**
     * @param mixed $portariatipo
     */
    public function setPortariatipo($portariatipo)
    {
        $this->portariatipo = $portariatipo;
    }

    /**
     * @return mixed
     */
    public function getSequencial()
    {
        return $this->sequencial;
    }

    /**
     * @param mixed $sequencial
     */
    public function setSequencial($sequencial)
    {
        $this->sequencial = $sequencial;
    }


    /**
     * @return mixed
     */
    public function getNumeroportaria()
    {
        return $this->numeroportaria;
    }

    /**
     * @param mixed $numeroportaria
     */
    public function setNumeroportaria($numeroportaria)
    {
        $this->numeroportaria = $numeroportaria;
    }


    /**
     * @return mixed
     */
    public function getUsuario()
    {
        return $this->usuario;
    }

    /**
     * @param mixed $usuario
     */
    public function setUsuario($usuario)
    {
        $this->usuario = $usuario;
    }

    /**
     * @return mixed
     */
    public function getAnousu()
    {
        return $this->anousu;
    }

    /**
     * @param mixed $anousu
     */
    public function setAnousu($anousu)
    {
        $this->anousu = $anousu;
    }

    /**
     * @return mixed
     */
    public function getDtportaria()
    {
        return $this->dtportaria;
    }

    /**
     * @param mixed $dtportaria
     */
    public function setDtportaria($dtportaria)
    {
        $this->dtportaria = $dtportaria;
    }

    /**
     * @return mixed
     */
    public function getDtinicio()
    {
        return $this->dtinicio;
    }

    /**
     * @param mixed $dtinicio
     */
    public function setDtinicio($dtinicio)
    {
        $this->dtinicio = $dtinicio;
    }

    /**
     * @return mixed
     */
    public function getDtlanc()
    {
        return $this->dtlanc;
    }

    /**
     * @param mixed $dtlanc
     */
    public function setDtlanc($dtlanc)
    {
        $this->dtlanc = $dtlanc;
    }

    /**
     * @return mixed
     */
    public function getAmparolegal()
    {
        return $this->amparolegal;
    }

    /**
     * @param mixed $amparolegal
     */
    public function setAmparolegal($amparolegal)
    {
        $this->amparolegal = $amparolegal;
    }

    /**
     * Salva uma portaria no banco de dados
     */
    public function persist()
    {
        $oDaoPortaria = new \cl_portaria();

        $oDaoPortaria->h31_portariatipo = $this->getPortariatipo();
        $oDaoPortaria->h31_usuario = $this->getUsuario();
        $oDaoPortaria->h31_anousu = $this->getAnousu();
        $oDaoPortaria->h31_dtportaria = $this->getDtportaria();
        $oDaoPortaria->h31_dtinicio = $this->getDtinicio();
        $oDaoPortaria->h31_dtlanc = $this->getDtLanc();
        $oDaoPortaria->h31_amparolegal = $this->getAmparolegal();
        $oDaoPortaria->h31_numero = $this->getNumeroportaria();

        if (empty($this->sequencial)) {

            $oDaoPortaria->incluir(null);

            if ($oDaoPortaria->erro_status == "0") {
                return $oDaoPortaria->erro_msg;
            }

            $this->setSequencial($oDaoPortaria->h31_sequencial);
        } else {

            $oDaoPortaria->h31_sequencial = $this->getSequencial();
            $oDaoPortaria->alterar($this->getSequencial());

            if ($oDaoPortaria->erro_status == "0") {
                return $oDaoPortaria->erro_msg;
            }
        }

        return $this;
    }


    /**
     * Metodo gera um numero novo para portaria utilizando sequencial
     *
     * @return mixed
     */
    public function gerarNumeroPortaria()
    {

        $sSqlSequence = " select nextval('rhparam_h36_ultimaportaria_seq') as seq ";
        $rsConsultaSequence = db_query($sSqlSequence);
        $oSeqPortaria = db_utils::fieldsMemory($rsConsultaSequence, 0);
        $iNroPort = $oSeqPortaria->seq;

        $this->setNumeroportaria($iNroPort);

        return $iNroPort;
    }

    /**
     * Busca o tipo de portaria buscando pelo assetamento
     *
     * @param $iTipoAssetamento
     */
    public function buscaTipoPortariaPorTipoAssetamento($iTipoAssetamento)
    {

       $sSql =  "select h30_sequencial from portariatipo where h30_tipoasse = {$iTipoAssetamento};";

       $rsPortariaTipo = db_query($sSql);

       if (!$rsPortariaTipo ||  pg_num_rows($rsPortariaTipo) == 0) {

           throw  new \Exception("Erro ao bucar o tipo da portaria.");
       }


       return  db_utils::fieldsMemory($rsPortariaTipo, 0)->h30_sequencial;
    }


    /**
     * Verifiva se o numero da portaria e gerado automatico.
     *
     * @return boolean
     */
    public function  isAutomatico()
    {
        $clrhparam		             = new \cl_rhparam;
        $sWhereRhParam  = " h36_ultimaportaria is not null and  h36_ultimaportaria != 0    and h36_instit = ".db_getsession("DB_instit");
        $sSqlRhParam    = $clrhparam->sql_query_file(null, "h36_ultimaportaria", null, $sWhereRhParam);
        $rsDadosRhParam = $clrhparam->sql_record($sSqlRhParam);

        return  ($clrhparam->numrows > 0);
    }
}
