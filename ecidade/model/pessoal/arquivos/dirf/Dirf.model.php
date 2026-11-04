<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

/**
 * classe para processamento da DIRF
 * @package Pessoal
 * @subpackage dirf
 *
 */

class Dirf
{

    protected $iAno;
    protected $iMes;
    protected $sCnpj;
    protected $sMatriculas;
    protected $iCodigoDirf;
    protected $iInstituicao;
    protected $aInconsistentes = array();
    protected $nValorLimite;
    protected $sCodigoArquivo;
    protected $iCodigoLayout;
    protected $aCredores;
    protected $aDesdobramentos;
    protected $sAnoMesAlterFerias = "";
    protected $sel_B901  = "0";
    protected $sel_B903  = "0";
    protected $sel_B904  = "0";
    protected $sel_B905  = "0";
    protected $sel_B906  = "0";
    protected $sel_B907  = "0";
    protected $sel_B908  = "0";
    protected $sel_B909  = "0";
    protected $sel_B910  = "0";
    protected $sel_B911  = "0";
    protected $sel_B912  = "0";
    protected $sel_B913  = "0";
    protected $sel_B914  = "0";
    protected $sel_B915  = "0";

  /**
   * Codigo sequencial gerado para cada cgm
   * @var array
   */
    protected $aCodigosGeracaoPessoalPorCgm = array();

  /**
   * Rubricas que são processadas como rais (B904), descontada na Rais
   * @var array
   */
    protected $aRubricasBaseRais = array();

  /**
   * Rubricas que são processadas como Pensao Alimenticia B905
   * @var array
   */
    protected $aRubricasPensaoAlimenticia = array();

  /**
   * Rubricas que são processadas como Previdencia Privada B910
   * @var array
   */
    protected $aRubricasPrevidenciaPrivada = array();

    protected $aGruposRRA = array("RTRT"    => 17 ,
                                "RTPO"    => 18 ,
                                "RTIRF"   => 20 ,
                                "DAJUD"   => 21 ,
                                "QTMESES" => 22 ,
                                "RIMOG"   => 23 );

  /**
   *
   */
    function __construct($iAno, $sCnpj)
    {

        $this->iAno        = $iAno;
        $this->iMes        = DBPessoal::getMesFolha();
        $this->sCnpj       = $sCnpj;
        $this->sMatriculas = "";
        $_SESSION["ignoreAccount"] = true;
    }

  /**
   * Seta matriculas selecionadas na geração da dirf.
   * @return $this->sMatriculas
   */
    public function getMatriculas()
    {
        return $this->sMatriculas;
    }

  /**
   * Retorna matriculas selecionadas na geração da dirf.
   * @param string_type $sMatriculas
   */
    public function setMatriculas($sMatriculas)
    {
        $this->sMatriculas = $sMatriculas;
    }

  /**
   * Define valor limite
   *
   * @param float $nValorLimite
   * @access public
   * @return void
   */
    public function setValorLimite($nValorLimite)
    {
        $this->nValorLimite = $nValorLimite;
    }

  /**
   * Retorna valor limite
   *
   * @access public
   * @return float
   */
    public function getValorLimite()
    {
        return $this->nValorLimite;
    }

  /**
   * Define codigo do arquivo
   *
   * @param string $sCodigoArquivo
   * @access public
   * @return void
   */
    public function setCodigoArquivo($sCodigoArquivo)
    {
        $this->sCodigoArquivo = $sCodigoArquivo;
    }

  /**
   * Retorna codigo do arquivo
   *
   * @access public
   * @return string
   */
    public function getCodigoArquivo()
    {
        return $this->sCodigoArquivo;
    }

  /**
   * Define codigo do layout
   *
   * @param integer $iCodigoLayout
   * @access public
   * @return void
   */
    public function setCodigoLayout($iCodigoLayout)
    {
        $this->iCodigoLayout = $iCodigoLayout;
    }

  /**
   * Retorna codigo do layout
   *
   * @access public
   * @return integer
   */
    public function getCodigoLayout()
    {
        return $this->iCodigoLayout;
    }

  /**
   * Define os credores
   *
   * @param Array $aCredores
   * @access public
   * @return void
   */
    public function setCredores(Array $aCredores)
    {
        $this->aCredores = $aCredores;
    }

  /**
   * Retorna array com os credores
   *
   * @access public
   * @return Array
   */
    public function getCredores()
    {
        return $this->aCredores;
    }

  /**
   * Define os desdobramentos
   *
   * @param Array $aDesdobramentos
   * @access public
   * @return void
   */
    public function setDesdobramentos(Array $aDesdobramentos)
    {
        $this->aDesdobramentos = $aDesdobramentos;
    }

  /**
   * Retorna array com os desdobramentos
   *
   * @access public
   * @return array
   */
    public function getDesdobramentos()
    {
        return $this->aDesdobramentos;
    }

  /**
   * Gera o processamento dos dados para a emissao dos relatorios e arquivo da Dirf
   *
   * @param boolean $lProcessarEmpenhos rotina deve processar os pagamentos de PF e PJ
   * @throws \DBException
   * @throws \Exception
   */
    public function processar($lProcessarEmpenhos = true)
    {

        $db_debug = true;
        LogDirf::write('Iniciando processamentod a DIRF');

        $oDaoRhDirfGeracao           = new cl_rhdirfgeracao;
        $oDaoRhDirfDadosPessoal      = new cl_rhdirfgeracaodadospessoal;
        $oDaoRhDirfDadosPessoalValor = new cl_rhdirfgeracaodadospessoalvalor;

      /**
       *  Limpa propriedade de Inconsistentes
       */
        $this->clearInconsistente();

        LogDirf::write('Consulta existencia de geração anterior para período selecionado');
        $sWhere               = "rh95_ano               = {$this->iAno} ";
        $sWhere              .= "and rh95_fontepagadora = '{$this->sCnpj}' ";
        $sWhere              .= "and rh95_instit = " . db_getsession('DB_instit');
        $sSqlVerificaGeracao  = $oDaoRhDirfGeracao->sql_query_file(null, "*", null, $sWhere);
        $rsVerificacaoGeracao = db_query($sSqlVerificaGeracao);

        if (!$rsVerificacaoGeracao) {
            throw new DBException("Não foi possível verificar a existência de geração da DIRF");
        }

        $iNumRowsDirf = pg_num_rows($rsVerificacaoGeracao);
        if ($iNumRowsDirf > 0) {
            for ($i = 0; $i < $iNumRowsDirf; $i++) {
                $oDirf = db_utils::fieldsMemory($rsVerificacaoGeracao, $i);
              /**
               * deletamos da tabela que liga as matriculas ao valor
               */
                $sDeleteMatriculasDirf  = "delete from rhdirfgeracaopessoalregist ";
                $sDeleteMatriculasDirf .= " using rhdirfgeracaodadospessoalvalor,rhdirfgeracaodadospessoal  ";
                $sDeleteMatriculasDirf .= " where rh99_rhdirfgeracaodadospessoalvalor = rh98_sequencial ";
                $sDeleteMatriculasDirf .= "   and rh98_rhdirfgeracaodadospessoal = rh96_sequencial";
                $sDeleteMatriculasDirf .= "   and rh96_rhdirfgeracao={$oDirf->rh95_sequencial}";
                $rsDeleteMatriculas     = db_query($sDeleteMatriculasDirf);
                LogDirf::write('Deletando os dados da tabela rhdirfgeracaopessoalregist');
                if (!$rsDeleteMatriculas) {
                     throw new Exception("Erro[1] - Erro ao excluir valores da DIRF.\n".pg_last_error());
                }

              /**
               * deletamos da tabela que liga as matriculas ao valor
               */
                $sDeletePrevidenciaDirf  = "delete from rhdirfgeracaopessoalvalorprevidencia ";
                $sDeletePrevidenciaDirf .= " using rhdirfgeracaodadospessoalvalor,rhdirfgeracaodadospessoal  ";
                $sDeletePrevidenciaDirf .= " where rh204_rhdirfgeracaodadospessoalvalor = rh98_sequencial ";
                $sDeletePrevidenciaDirf .= "   and rh98_rhdirfgeracaodadospessoal = rh96_sequencial";
                $sDeletePrevidenciaDirf .= "   and rh96_rhdirfgeracao={$oDirf->rh95_sequencial}";
                $rsDeletePrevidenciaDirf      = db_query($sDeletePrevidenciaDirf);
                LogDirf::write('Deletando os dados da tabela rhdirfgeracaopessoalvalorprevidencia');
                if (!$rsDeletePrevidenciaDirf) {
                    throw new Exception("Erro[2] - Erro ao excluir valores da DIRF.\n".pg_last_error());
                }

              /**
               * deletamos da tabela que liga os pensionistas  ao valor
               */
                $sDeleteValorPensionistasDirf  = "delete from rhdirfgeracaopessoalpensionistavalor";
                $sDeleteValorPensionistasDirf .= " using rhdirfgeracaodadospessoalvalor,rhdirfgeracaodadospessoal  ";
                $sDeleteValorPensionistasDirf .= " where rh203_rhdirfgeracaodadospessoalvalor = rh98_sequencial ";
                $sDeleteValorPensionistasDirf .= "   and rh98_rhdirfgeracaodadospessoal = rh96_sequencial";
                $sDeleteValorPensionistasDirf .= "   and rh96_rhdirfgeracao={$oDirf->rh95_sequencial}";
                $rsDeleteValorPensionistas     = db_query($sDeleteValorPensionistasDirf);
                LogDirf::write('Deletando os dados da tabela rhdirfgeracaopessoalpensionistavalor');
                if (!$rsDeleteValorPensionistas) {
                    throw new Exception("Erro[3] - Erro ao excluir valores da DIRF, removendo valores de pensionistas.\n".pg_last_error());
                }

                $sDeleteValoresDirf   = "delete from rhdirfgeracaodadospessoalvalor ";
                $sDeleteValoresDirf  .= " using rhdirfgeracaodadospessoal  ";
                $sDeleteValoresDirf  .= " where rh98_rhdirfgeracaodadospessoal = rh96_sequencial";
                $sDeleteValoresDirf  .= "   and rh96_rhdirfgeracao={$oDirf->rh95_sequencial}";
                $rsDeleteValoresDirf  = db_query($sDeleteValoresDirf);
                LogDirf::write('Deletando os dados da tabela rhdirfgeracaodadospessoalvalor');
                if (!$rsDeleteValoresDirf) {
                    throw new Exception("Erro[4] - Erro ao excluir valores da DIRF.\n".pg_last_error());
                }

              /**
               * deletamos da tabela que liga os pensionistas ao servidor
               */
                $sDeleteValorPensionistasDirf  = "delete from rhdirfgeracaopessoalpensionista";
                $sDeleteValorPensionistasDirf .= " using rhdirfgeracaodadospessoal  ";
                $sDeleteValorPensionistasDirf .= " where rh202_rhdirfgeracaopessoal = rh96_sequencial ";
                $sDeleteValorPensionistasDirf .= "   and rh96_rhdirfgeracao={$oDirf->rh95_sequencial}";
                $rsDeleteValorPensionistas     = db_query($sDeleteValorPensionistasDirf);
                LogDirf::write('Deletando os dados da tabela rhdirfgeracaopessoalregist');
                if (!$rsDeleteValorPensionistas) {
                    throw new Exception("Erro[5] - Erro ao excluir valores da DIRF.\n".pg_last_error());
                }

                $oDaoRhDirfDadosPessoal->excluir(null, "rh96_rhdirfgeracao = {$oDirf->rh95_sequencial}");
                if ($oDaoRhDirfDadosPessoal->erro_status == 0) {
                    throw new Exception("Erro[6] - Erro ao excluir valores da DIRF.\n{$oDaoRhDirfDadosPessoalValor->erro_msg}");
                }
                $oDaoRhDirfGeracao->excluir($oDirf->rh95_sequencial);
                if ($oDaoRhDirfGeracao->erro_status == 0) {
                    throw new Exception("Erro[7] - Erro ao excluir valores da DIRF.\n{$oDaoRhDirfDadosPessoalValor->erro_msg}");
                }
                unset($oDirf);
            }
        }

      /**
       * inclui uma nova geracao da Dirf
       */
        $oDaoRhDirfGeracao->rh95_ano           = $this->iAno;
        $oDaoRhDirfGeracao->rh95_fontepagadora = $this->sCnpj;
        $oDaoRhDirfGeracao->rh95_datageracao   = date("Y-m-d", db_getsession("DB_datausu"));
        $oDaoRhDirfGeracao->rh95_id_usuario    = db_getsession("DB_id_usuario");
        $oDaoRhDirfGeracao->rh95_instit        = db_getsession("DB_instit");
        $oDaoRhDirfGeracao->incluir(null);
        LogDirf::write("Iniciando Geração Ano: {$this->iAno}");
        if ($oDaoRhDirfGeracao->erro_status == 0) {
            throw new Exception("Erro[8] - Erro ao incluir valores da DIRF.\n{$oDaoRhDirfGeracao->erro_msg}");
        }

        $this->iCodigoDirf = $oDaoRhDirfGeracao->rh95_sequencial;

      /**
       * processa os dados da folha de pagamento
       */
        $this->processarDadosFolha();

        if ($lProcessarEmpenhos) {
          /**
           * processa os pagamentos realizados na contabilidade
           */
            $this->processarDadosContabilidade();
        }
    }

  /**
   * processa os dados da folha de pagamento
   * @throws \Exception
   */
    public function processarDadosFolha()
    {

        $oDaoRhDirfGeracaoPessoal = new cl_rhdirfgeracaodadospessoal;
        $oDaoRhDirfGeracaoPessoalMatricula = new cl_rhdirfgeracaopessoalregist;
        $oDaoBasesr = new cl_basesr;
        $oDaoCfpess = new cl_cfpess;
        $oDaoGerfsal = new cl_gerfsal;
        $oDaoGerfcom = new cl_gerfcom;
        $oDaoGerfres = new cl_gerfres;
        $oDaoGerffer = new cl_gerffer;
        $oDaoGerfs13 = new cl_gerfs13;

        LogDirf::write("Chamando função processarDadosFolha.");

      /**
       * processamos os dados da Folha
       * trecho de codigo Copiado do fonte pes4_geradirf002.php
       * TODO Reescrever o codigo.
       */
        $ano_base = $this->iAno;
      /**
       * carrega dos dados da configuracao do modulo pessoal.
       */
        global $subpes;

        $mes_atual = DBPessoal::getMesFolha();
        $ano_atual = DBPessoal::getAnoFolha();

        $rsDaoCfpess = $oDaoCfpess->sql_record($oDaoCfpess->sql_query_file(
            $ano_atual,
            $mes_atual,
            db_getsession("DB_instit"),
            "r11_altfer"
        ));
        $this->sAnoMesAlterFerias = db_utils::fieldsMemory($rsDaoCfpess, 0)->r11_altfer;

        LogDirf::write("Consultando dados da cfpess. ");

        $subpes = $this->iAno.'/'.$this->iMes;

        $subini = $subpes;

        LogDirf::write("subini: {$subini}");
        LogDirf::write("Verificando bases utilizadas na DIRF");

      /**
       *
       * verifica quais são as rubricas de cada base utilizada na DIRF.
       */

       // transformar em função

        $sBases = "('B901','B903','B904','B905','B906','B907','B908','B909','B910','B911','B912','B913','B914','B915')";
        $sWhereBases = "r09_mesusu = {$mes_atual} and
                        r09_anousu = {$ano_atual} and
                        r09_base in {$sBases} and
                        r09_instit = ".db_getsession("DB_instit");

        $sDaoBasesrSql = $oDaoBasesr->sql_query_file(
            null,
            null,
            null,
            null,
            null,
            "r09_base, r09_rubric",
            "r09_base, r09_rubric",
            $sWhereBases
        );

        $sBasesrSql = "select r09_base, ";
        $sBasesrSql .= "''''||array_to_string(array_accum(distinct r09_rubric),''',''')||'''' as r09_rubric ";
        $sBasesrSql .= "from ( ";
        $sBasesrSql .= "      {$sDaoBasesrSql} ";
        $sBasesrSql .= "     ) as x ";
        $sBasesrSql .= "group by r09_base";

        $rsDaoBasesr = $oDaoBasesr->sql_record($sBasesrSql);

        if (!$rsDaoBasesr && $oDaoBasesr->numrows == 0) {
            throw new Exception("Bases da Dirf não estão configuradas. Verifique!");
        }

        while ($basesr = pg_fetch_object($rsDaoBasesr)) {
            switch ($basesr->r09_base) {
                case "B901":
                    $this->sel_B901 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B901 {$basesr->r09_rubric}");

                    break;

                case "B903":
                    $this->sel_B903 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B903 {$basesr->r09_rubric}");

                    break;

                case "B904":
                    $this->aRubricasBaseRais = explode(',', $basesr->r09_rubric);
                    $this->sel_B904 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B904 {$basesr->r09_rubric}");

                    break;

                case "B905":
                    $this->aRubricasPensaoAlimenticia = explode(',', $basesr->r09_rubric);
                    $this->sel_B905 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B905 {$basesr->r09_rubric}");

                    break;

                case "B906":
                    $this->sel_B906 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B906 {$basesr->r09_rubric}");

                    break;

                case "B907":
                    $this->sel_B907 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B907 {$basesr->r09_rubric}");

                    break;

                case "B908":
                    $this->sel_B908 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B908 {$basesr->r09_rubric}");

                    break;

                case "B909":
                    $this->sel_B909 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B909 {$basesr->r09_rubric}");

                    break;

                case "B910":
                    $this->aRubricasPrevidenciaPrivada = explode(',', $basesr->r09_rubric);
                    $this->sel_B910 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B910 {$basesr->r09_rubric}");

                    break;

                case "B911":
                    $this->sel_B911 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B911 {$basesr->r09_rubric}");

                    break;

                case "B912":
                    $this->sel_B912 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B912 {$basesr->r09_rubric}");

                    break;

                case "B913":
                    $this->sel_B913 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B913 {$basesr->r09_rubric}");

                    break;

                case "B914":
                    $this->sel_B914 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B914 {$basesr->r09_rubric}");

                    break;

                case "B915":
                    $this->sel_B915 = $basesr->r09_rubric;
                    LogDirf::write("Base sel_B915 {$basesr->r09_rubric}");

                    break;
            }
        }

      /* Devido a problemas de estouro de memória em alguns clientes com muitos registros para processar,
       foi necessário quebrar o processamento
      */

        $sSqlPessoal    = "with cgms_funcionarios as (
                              select rh01_numcgm,
                                     rh01_regist,
                                     rh02_seqpes,
                                     rh02_lota
                              from rhpessoalmov
                                   join rhpessoal on rhpessoal.rh01_regist = rhpessoalmov.rh02_regist
                              where rh02_anousu = {$this->iAno}
                                and extract(year from rh01_admiss) <= {$this->iAno}
                            ),

                            busca_funcionarios as (
                              select rh01_numcgm,
                                     rh02_lota
                              from cgms_funcionarios
                                    left join rhpesrescisao on rhpesrescisao.rh05_seqpes = cgms_funcionarios.rh02_seqpes
                               where (rh05_recis is null or (rh05_recis is not null
                                                             and exists
                                                               (select 1
                                                                from gerfres
                                                                where r20_regist = rh01_regist
                                                                  and r20_anousu = {$this->iAno})))
                            ),

                            busca_lotacao_cnpj as (
                              select distinct rh01_numcgm
                              from busca_funcionarios
                                   join rhlota on r70_codigo = rh02_lota
                                   join rhlotaexe on rh26_codigo = r70_codigo
                                                 and rh26_anousu = {$this->iAno}
                                   join orcunidade on o41_unidade = rh26_unidade
                                                  and o41_orgao = rh26_orgao
                                                  and o41_anousu = rh26_anousu
                              where o41_cnpj = '{$this->sCnpj}'
                            ),

                            registros_dirf as (
                              select rh01_numcgm,
                                     z01_nome,
                                     trim(z01_cgccpf) as z01_cgccpf,
                                     0 as processado
                              from busca_lotacao_cnpj
                                   join cgm on z01_numcgm = rh01_numcgm
                            )

                       select * from registros_dirf order by rh01_numcgm
                      ";

        $sSqlPessoal2 = "select count(*) as qtdregistros from ({$sSqlPessoal}) as x ";

        $rsDadosPessoal = db_query($sSqlPessoal2);

        $qtdregistros = 0;
        if (!$rsDadosPessoal || pg_num_rows($rsDadosPessoal) == 0) {
            throw new DBException("Nenhum funcionário encontrado para o processamento da Dirf");
        } else {
            $qtdregistros = (int)db_utils::fieldsMemory($rsDadosPessoal, 0)->qtdregistros;
        }

        $iQtdLimit = 5000;
        if ($qtdregistros < $iQtdLimit) {
            $iQtdProcessamento = 1;
        } else {
            $iQtdProcessamento = (int)ceil($qtdregistros / $iQtdLimit);
        }

        /**
         * verifica a quantidade de registros para fazer o offset
         */
        $offset = $iQtdLimit;
        for ($qtd = 1; $qtd <= $iQtdProcessamento; $qtd++) {
            $sSqlPessoal2 = "";
            if ($iQtdProcessamento > 1) {
                if ($qtd == 1) {
                    $sSqlPessoal2 = " limit {$iQtdLimit}";
                } else {
                    $offset = $iQtdLimit * ($qtd - 1);
                    $sSqlPessoal2 = " limit {$iQtdLimit} offset {$offset}";
                }
            }

            $rsDadosPessoal = db_query($sSqlPessoal.$sSqlPessoal2);

            if (!$rsDadosPessoal || pg_num_rows($rsDadosPessoal) == 0) {
                $aPessoas       = array();
            } else {
                $aPessoas       = db_utils::getCollectionByRecord($rsDadosPessoal);
            }

            LogDirf::write('Seleciona os servidores que estão vinculados a unidade deste CNPJ: '.$this->sCnpj);

            /**
             * calcula os valores de todos os meses
             */
            for ($ind = 1; $ind <= 12; $ind++) {
                LogDirf::write('');
                LogDirf::write('<----------------------->');
                LogDirf::write('Calculando valor mensal, m?s: '. $ind);

                global $diversos;
                $subpes = $ano_base . "/" . db_str($ind, 2, 0, "0");
                $condicaoaux = " and r07_codigo = 'D902'";

                if (db_selectmax("diversos", "select r07_valor from pesdiver ".bb_condicaosubpes("r07_").$condicaoaux)) {
                    $D902 = $diversos[0]["r07_valor"];
                } else {
                    $D902 = 0;
                }

                $condicaoaux = " and r07_codigo = 'D950'";

                if (db_selectmax("diversos", "select r07_valor from pesdiver ".bb_condicaosubpes("r07_").$condicaoaux)) {
                    $D950 = $diversos[0]["r07_valor"];
                } else {
                    $D950 = 0;
                }

                LogDirf::write('Valor da rubrica diversos D902: '. $D902);
                LogDirf::write('Valor da rubrica diversos D950: '. $D950);

                $atual = 0;

                if ($ind < 13) {
                    $diasn = db_str(ndias(db_str($ind, 2, 0, "0")."/".$ano_base), 2, 0, "0");
                    $datet = db_ctod($diasn."/".db_str($ind, 2, 0, "0")."/".$ano_base) ;

                    LogDirf::write('Entrando na condição $ind < 13 $diasn:' . $diasn . ' $datet: ' . $datet);
                }

                LogDirf::write('Iniciando o processamento para os servidores');
                foreach ($aPessoas as $oPessoa) {
                    $oPessoa->registros = '';
                    LogDirf::write('Processando o Servidor CGM: ' . $oPessoa->rh01_numcgm . ' Nome: ' . $oPessoa->z01_nome);

                    /**
                     * incluimos os dados pessoais
                     */

                    if (trim($oPessoa->z01_cgccpf) == "") {
                        LogDirf::write('Cpf vazio adiciona o cgm a lista de inconsistências. E vai para o próximo');
                        $this->addInconsistente($oPessoa->rh01_numcgm, $oPessoa->z01_nome, 'CPF Inválido');
                        continue;
                    }

                    if ($oPessoa->processado == 0) {
                        LogDirf::write('$oPessoa->processado == 0');

                        $oDaoRhDirfGeracaoPessoal->rh96_cpfcnpj = $oPessoa->z01_cgccpf;
                        $oDaoRhDirfGeracaoPessoal->rh96_numcgm  = $oPessoa->rh01_numcgm;
                        $oDaoRhDirfGeracaoPessoal->rh96_regist  = '0';
                        $oDaoRhDirfGeracaoPessoal->rh96_tipo    = 1;
                        $oDaoRhDirfGeracaoPessoal->rh96_rhdirfgeracao = $this->iCodigoDirf;
                        $oDaoRhDirfGeracaoPessoal->incluir(null);
                        LogDirf::write('Inclui na tabela rhdirfgeracaodadospessoal');

                        if ($oDaoRhDirfGeracaoPessoal->erro_status == 0) {
                            throw new Exception("Erro[9] - Erro ao incluir valores(CGM: {$oPessoa->rh01_numcgm} com CPF/CNPJ Inválido) da DIRF.\n{$oDaoRhDirfGeracaoPessoal->erro_msg}");
                        }
                        $oPessoa->codigodirf = $oDaoRhDirfGeracaoPessoal->rh96_sequencial;
                        $oPessoa->processado = 1;
                    }

                    global $pess,$Ipes;

                    $atual += 1;
                    $condicaoaux  = " and extract(year from rh01_admiss) <= ".db_sqlformat($ano_base);
                    $condicaoaux .= " and ( rh05_recis is null ";
                    $condicaoaux .= "      or  ( rh05_recis is not null and exists (select 1 from gerfres where r20_regist = rh01_regist ";
                    $condicaoaux .= "            and r20_anousu = {$ano_base} limit 1)))";
                    $condicaoaux .= " and rh01_numcgm = {$oPessoa->rh01_numcgm}";
                    $condicaoaux .= " and o41_cnpj='{$this->sCnpj}'";
                    $condicaoaux .= "order by rh01_numcgm ";

                    $campos_pessoal   = "rh01_regist as r01_regist, ";
                    $campos_pessoal  .= "rh01_numcgm as r01_numcgm, ";
                    $campos_pessoal  .= "trim(to_char(rh02_lota,'9999')) as r01_lotac, ";
                    $campos_pessoal  .= "rh01_nasc     as r01_nasc, ";
                    $campos_pessoal  .= "rh01_admiss   as r01_admiss, ";
                    $campos_pessoal  .= "rh01_instru   as r01_instru, ";
                    $campos_pessoal  .= "rh05_recis    as r01_recis, ";
                    $campos_pessoal  .= "rh30_vinculo  as r01_tpvinc, ";
                    $campos_pessoal  .= "rh02_tbprev   as r01_tbprev, ";
                    $campos_pessoal  .= "exists (select 1 from gerfres where r20_regist = rh01_regist and r20_anousu = {$ano_base} limit 1) as tem_resciscao_calculada_no_ano, ";
                    $campos_pessoal  .= "rh02_portadormolestia as r01_pmolestia";

                    $sSqlComplementoPessoal  = "select {$campos_pessoal}";
                    $sSqlComplementoPessoal .= "  from rhpessoalmov  ";
                    $sSqlComplementoPessoal .= "       inner join rhpessoal    on rh01_regist = rhpessoalmov.rh02_regist ";
                    $sSqlComplementoPessoal .= "       inner join cgm          on z01_numcgm  = rhpessoal.rh01_numcgm ";
                    $sSqlComplementoPessoal .= "       left join rhpesrescisao on rh05_seqpes = rhpessoalmov.rh02_seqpes ";
                    $sSqlComplementoPessoal .= "       left join rhregime      on rh30_codreg = rhpessoalmov.rh02_codreg ";
                    $sSqlComplementoPessoal .= "                              and rh30_instit = rhpessoalmov.rh02_instit ";
                    $sSqlComplementoPessoal .= "       inner join rhlota       on rh02_lota   = r70_codigo  ";
                    $sSqlComplementoPessoal .= "       inner join rhlotaexe    on r70_codigo  = rh26_codigo ";
                    $sSqlComplementoPessoal .= "                              and rh26_anousu = {$this->iAno}";
                    $sSqlComplementoPessoal .= "       inner join orcunidade   on o41_unidade = rh26_unidade ";
                    $sSqlComplementoPessoal .= "                              and o41_orgao   = rh26_orgao ";
                    $sSqlComplementoPessoal .= "                              and o41_anousu  = rh26_anousu ";

                    $sSqlComplementoPessoal .= bb_condicaosubpesproc("rh02_", $this->iAno.'/'.$ind).$condicaoaux;
                    $rsComplementoPessoal    = db_query($sSqlComplementoPessoal);

                    unset($aComplementoPessoal);
                    if (!$rsComplementoPessoal || pg_num_rows($rsComplementoPessoal) == 0) {
                        $aComplementoPessoal = array();
                    } else {
                        $aComplementoPessoal     = db_utils::getCollectionByRecord($rsComplementoPessoal);
                    }
                    unset($rsComplementoPessoal);

                    if (isset($aComplementoPessoal[0])) {
                        LogDirf::write('Verifica informações do servidor:');
                        LogDirf::write('--> Matricula---------------------> ' .$aComplementoPessoal[0]->r01_regist);
                        LogDirf::write('--> Admissão----------------------> ' .$aComplementoPessoal[0]->r01_admiss);
                        LogDirf::write('--> Rescisão----------------------> ' .(empty($aComplementoPessoal[0]->r01_recis)? 'Não' : 'Sim'));
                        LogDirf::write('--> Tipo Vinculo------------------> ' .$aComplementoPessoal[0]->r01_tpvinc);
                        LogDirf::write('--> Tabela Previdencia------------> ' .$aComplementoPessoal[0]->r01_tbprev);
                        LogDirf::write('--> tem_rescis_calc_ano-----------> ' .$aComplementoPessoal[0]->tem_resciscao_calculada_no_ano);
                        LogDirf::write('--> Molestia----------------------> ' .$aComplementoPessoal[0]->r01_pmolestia);
                    }

                    $oPessoa->aValorGrupo   = array();
                    $oPessoa->aValorGrupo[1] = 0;

                    if (!empty($aComplementoPessoal[0]->r01_nasc)) {
                        $oPessoa->idade = ver_idade(db_dtoc($datet), db_dtoc($aComplementoPessoal[0]->r01_nasc));
                    } else {
                        $oPessoa->idade = null;
                    }

                    LogDirf::write('Verificando idade do servidor, $oPessoa->idade: '. $oPessoa->idade);

                    $iContador                     = 0;
                    $oPessoa->vdep13               = 0;
                    $oPessoa->vdeducao65_13        = 0;
                    $oPessoa->vpensao13            = 0;
                    $oPessoa->tributado13          = 0;
                    $oPessoa->base13               = 0;
                    $oPessoa->previdencia13        = 0;
                    $oPessoa->previdenciaprivada13 = 0;

                    if (!isset($oPessoa->aValorGrupo13)) {
                        $oPessoa->aValorGrupo13 = array();
                    }
                    $aMatriculas = array();

                    LogDirf::write("Iniciando agrupamento de valores, agora vai.");

                    foreach ($aComplementoPessoal as $oDados) {
                        $oPessoa->aValorGrupo13         = array();
                        $oPessoa->aValorGrupo           = array();
                        $oPessoa->aValorGrupo[1]        = 0;
                        $oPessoa->aValorGrupo[17]       = 0;
                        $oPessoa->aValorGrupo[18]       = 0;
                        $oPessoa->aValorGrupo[19]       = 0;
                        $oPessoa->aValorGrupo[20]       = 0;
                        $oPessoa->aValorGrupo[23]       = 0;
                        $oPessoa->matricula_corrente    = $oDados->r01_regist;
                        $oPessoa->lInativoOuPensionista = false;
                        $oPessoa->inativo               = false;
                        $lInativoPensionistaMolestia    = false;
                        $lPortadorMolestia              = false;

                        if ($oDados->r01_tpvinc == 'P' || $oDados->r01_tpvinc == 'I' || $oDados->r01_pmolestia == 'true' || $oDados->r01_pmolestia == 't') {
                            LogDirf::write("Servidor é Pensionista ou Inativo ou possui moléstia.");
                            $lInativoPensionistaMolestia    = true;
                            $oPessoa->inativo               = true;
                        }

                        if ($oDados->r01_pmolestia == 'true' || $oDados->r01_pmolestia == 't') {
                            LogDirf::write("Servidor possui moléstia.");
                            $oPessoa->lInativoOuPensionista = true;
                            $lPortadorMolestia              = true;
                        }

                        $oPessoa->mtributo     = 0;
                        $oPessoa->mtribs13     = 0;
                        $oPessoa->deducao65    = 0;
                        $oPessoa->deducao65_13 = 0;

                        if ((db_year($oDados->r01_admiss) <= db_val($ano_base) &&
                        (db_empty($oDados->r01_recis) || (!db_empty($oDados->r01_recis)) &&
                        db_year($oDados->r01_recis) >= db_val($ano_base)) || $oDados->tem_resciscao_calculada_no_ano == 't') || $ind == 13) {
                            LogDirf::write('Condições: ');
                            LogDirf::write('-- Admissão <= ano base | '. (int)(db_year($oDados->r01_admiss) <= db_val($ano_base)));
                            LogDirf::write('-- e  (não possui recisão ou tem recisão && ano da rescisão >= ano base) | '. (int)((db_empty($oDados->r01_recis) ||
                               (!db_empty($oDados->r01_recis)) && db_year($oDados->r01_recis) >= db_val($ano_base))));
                            LogDirf::write('-- ou (tem rescisão calculada no ano) | '. (int)($oDados->tem_resciscao_calculada_no_ano == 't'));

                            if ($iContador < 9) {
                                LogDirf::write('($iContador < 9) Deve permanecer aqui isso??');
                                $oPessoa->registros .= db_str($oDados->r01_regist, 6)." / ";
                            }

                            if (!in_array($oDados->r01_regist, $aMatriculas)) {
                                LogDirf::write('(!in_array($oDados->r01_regist, $aMatriculas)) Deve permanecer aqui isso??');
                                $aMatriculas[] = $oDados->r01_regist;
                            }

                            $condicaoaux = " and r33_codtab = ".db_sqlformat($oDados->r01_tbprev+2);
                            global $inssirf;
                            db_selectmax("inssirf", "select r33_tipo from inssirf ".bb_condicaosubpesproc("r33_", $subini).$condicaoaux);

                            /**
                             * calcula os valores de folha de pagmento para o mes.
                             */
                            $condicaoaux = " and r14_lotac = '{$oDados->r01_lotac}' and r14_regist = ".$oDados->r01_regist;
                            $sCampos = "r14_valor as valor, r14_rubric as rubric, r14_pd as pd, r14_regist as regist, r14_mesusu as mesusu";
                            $gerfsal = $oDaoGerfsal->sql_record($oDaoGerfsal->sql_query_file($ano_base, db_str($ind, 2, 0, "0"), $oDados->r01_regist, null, $sCampos));

                            if ($gerfsal != false && $oDaoGerfsal->numrows > 0) {
                                LogDirf::write('');
                                LogDirf::write('Consultando dados da gerfsal: Condição: '.bb_condicaosubpes("r14_").$condicaoaux);
                                $this->calculaValoresDirfPessoal($gerfsal, "r14_", $oPessoa);
                            }

                            $condicaoaux = " and r48_lotac = '{$oDados->r01_lotac}' and r48_regist = ".$oDados->r01_regist;
                            $sCampos = "r48_valor as valor, r48_rubric as rubric, r48_pd as pd, r48_regist as regist, r48_mesusu as mesusu";
                            $gerfcom = $oDaoGerfcom->sql_record($oDaoGerfcom->sql_query_file($ano_base, db_str($ind, 2, 0, "0"), $oDados->r01_regist, null, $sCampos));

                            if ($gerfcom != false && $oDaoGerfcom->numrows > 0) {
                                LogDirf::write('');
                                LogDirf::write('Consultando dados da gerfcom: Condição: '.bb_condicaosubpes("r48_").$condicaoaux);
                                $this->calculaValoresDirfPessoal($gerfcom, "r48_", $oPessoa);
                            }

                            $condicaoaux = " and r20_lotac = '{$oDados->r01_lotac}' and r20_regist = ".$oDados->r01_regist;
                            $sCampos = "r20_valor as valor, r20_rubric as rubric, r20_pd as pd, r20_regist as regist, r20_mesusu as mesusu";
                            $gerfres = $oDaoGerfres->sql_record($oDaoGerfres->sql_query_file($ano_base, db_str($ind, 2, 0, "0"), $oDados->r01_regist, null, null, $sCampos));

                            if ($gerfres != false && $oDaoGerfres->numrows > 0) {
                                LogDirf::write('');
                                LogDirf::write('Consultando dados da gerfres: Condição: '.bb_condicaosubpes("r20_").$condicaoaux);
                                $this->calculaValoresDirfPessoal($gerfres, "r20_", $oPessoa);
                            }

                            if (db_empty($this->sAnoMesAlterFerias) || $subpes < $this->sAnoMesAlterFerias) {
                                $condicaoaux = " and r31_lotac = '{$oDados->r01_lotac}' and r31_regist = {$oDados->r01_regist}";
                                $sCampos = "r31_valor as valor, r31_rubric as rubric, r31_pd as pd, r31_regist as regist, r31_mesusu as mesusu";
                                $gerffer = $oDaoGerffer->sql_record($oDaoGerffer->sql_query_file($ano_base, db_str($ind, 2, 0, "0"), $oDados->r01_regist, null, null, $sCampos));

                                if ($gerffer != false && $oDaoGerffer->numrows > 0) {
                                    LogDirf::write('');
                                    LogDirf::write('Consultando dados da gerffer: Condição: '.bb_condicaosubpes("r31_").$condicaoaux);
                                    $this->calculaValoresDirfPessoal($gerffer, "r31_", $oPessoa);
                                }
                            }

                            $condicaoaux = " and r35_lotac = '{$oDados->r01_lotac}' and r35_regist = {$oDados->r01_regist}";
                            $sCampos = "r35_valor as valor, r35_rubric as rubric, r35_pd as pd, r35_regist as regist, r35_mesusu as mesusu";
                            $gerfs13 = $oDaoGerfs13->sql_record($oDaoGerfs13->sql_query_file($ano_base, db_str($ind, 2, 0, "0"), $oDados->r01_regist, null, $sCampos));

                            if ($gerfs13 != false && $oDaoGerfs13->numrows > 0) {
                                LogDirf::write('');
                                LogDirf::write('Consultando dados da gerfs13: Condição: '.bb_condicaosubpes("r35_").$condicaoaux);
                                $this->calculaValoresDirfPessoal($gerfs13, "r35_", $oPessoa);
                            }
                        }

                        $iContador ++;

                        if (db_at(strtolower($oDados->r01_tpvinc), "ip") > 0 && ($oPessoa->idade >= 65)) {
                            if (!isset($ina)) {
                                $ina = 0;
                            }

                            $sWhereVerificabaseinativo  = " where r14_lotac = '{$oDados->r01_lotac}' and r14_regist = ".$oDados->r01_regist;
                            $sWhereVerificabaseinativo .= "   and r14_mesusu = {$ind} and r14_anousu = {$this->iAno} and r14_rubric = 'R997'";

                            $rsVerificabaseinativo = db_query("select r14_valor from gerfsal ".$sWhereVerificabaseinativo);

                            if ($rsVerificabaseinativo && pg_num_rows($rsVerificabaseinativo) > 0) {
                                $folhaSalarioBaseInativo = db_utils::fieldsMemory($rsVerificabaseinativo, 0);
                            }

                            $sWhereVerificabaseComplementarinativo  = " where r48_lotac = '{$oDados->r01_lotac}' and r48_regist = ".$oDados->r01_regist;
                            $sWhereVerificabaseComplementarinativo .= "   and r48_mesusu = {$ind} and r48_anousu = {$this->iAno} and r48_rubric = 'R997'";

                            $rsVerificabaseComplementarinativo = db_query("select r48_valor from gerfcom ".$sWhereVerificabaseComplementarinativo);

                            if ($rsVerificabaseComplementarinativo && pg_num_rows($rsVerificabaseComplementarinativo) > 0) {
                                $folhaComplementarBaseInativo = db_utils::fieldsMemory($rsVerificabaseComplementarinativo, 0);
                            }

                            LogDirf::write('Valor do grupo 1: ' . $oPessoa->aValorGrupo[1]);

                            if (isset($oPessoa->aValorGrupo[1])) {
                                $ina     += $D902;

                                if (isset($folhaComplementarBaseInativo->r48_valor)) {
                                    LogDirf::write('$folhaComplementarBaseInativo->r48_valor');
                                    $oPessoa->aValorGrupo[1] -= $folhaComplementarBaseInativo->r48_valor;
                                    LogDirf::write('Atualizando valor do tipo 1: ' . $oPessoa->aValorGrupo[1]);
                                }

                                if (isset($folhaSalarioBaseInativo->r14_valor)) {
                                    LogDirf::write('$folhaSalarioBaseInativo->r14_valor');
                                    $oPessoa->aValorGrupo[1] -= $folhaSalarioBaseInativo->r14_valor;
                                    LogDirf::write('Atualizando valor do tipo 1: ' . $oPessoa->aValorGrupo[1]);
                                }

                                if ($oPessoa->aValorGrupo[1] < 0) {
                                    $oPessoa->aValorGrupo[1] = 0;
                                }

                                if (isset($folhaComplementarBaseInativo)) {
                                    unset($folhaComplementarBaseInativo);
                                }

                                if (isset($folhaSalarioBaseInativo)) {
                                    unset($folhaSalarioBaseInativo);
                                }
                            } else {
                                $sWhereVerificairf  = " where r14_lotac = '{$oDados->r01_lotac}' and r14_regist = ".$oDados->r01_regist;
                                $sWhereVerificairf .= "   and r14_mesusu = {$ind} r14_anousu = {$this->iAno} and r14_pd = 2 and r14_rubric = 'R913'";

                                unset($rsVerificairf);
                                $rsVerificairf = db_query("select r14_regist from gerfsal ".$sWhereVerificairf);

                                if ($rsVerificairf && pg_num_rows($rsVerificairf) == 0) {
                                            $sWherebaseirf  = " where r14_lotac = '{$oDados->r01_lotac}' and r14_regist = ".$oDados->r01_regist;
                                            $sWherebaseirf .= " and r14_mesusu = {$ind} and r14_anousu = {$this->iAno} and r14_rubric = 'R981'";

                                            $folhaSalarioBase = db_utils::getCollectionByRecord(db_query("select r14_valor from gerfsal ".$sWherebaseirf));
                                    if ($folhaSalarioBase) {
                                          $oPessoa->aValorGrupo[1] -= $folhaSalarioBase[0]->r14_valor;
                                          LogDirf::write('Atualizando valor do tipo 1: ' . $oPessoa->aValorGrupo[1]);
                                    }
                                            unset($folhaSalarioBase);
                                }

                                $ina      += $oPessoa->aValorGrupo[1];
                                $tributo  = 0;
                            }

                            if (isset($oPessoa->aValorGrupo13[1]) && $oPessoa->aValorGrupo13[1] >= $D902) {
                                $ina      += $D902;
                            } elseif (isset($oPessoa->aValorGrupo13[1]) && $oPessoa->aValorGrupo13[1] > 0) {
                                $mtribs13 = 0;
                            }
                            if (isset($oPessoa->aValorGrupo13[1]) && $oPessoa->aValorGrupo13[1] < 0) {
                                $oPessoa->aValorGrupo13[1] = 0;
                            }
                        }
                        if (isset($oPessoa->aValorGrupo[25]) && $oPessoa->aValorGrupo[25] > 0) {
                            $oPessoa->aValorGrupo[25] = $D950;
                        }
                
                        if (isset($oPessoa->aValorGrupo13[25]) && $oPessoa->aValorGrupo13[25] > 0) {
                            $oPessoa->aValorGrupo13[25] = $D950;
                        }

                  /**
                   * Processando RRA
                   */
                        LogDirf::write('Montando objeto servidor para processar pagamentos de RRA');
                        $oServidor = ServidorRepository::getInstanciaByCodigo(
                            $oDados->r01_regist,
                            $this->iAno,
                            $ind,
                            null,
                            true,
                            true
                        );

                        $this->processarRRA($oServidor, $lPortadorMolestia, $oPessoa);

                      /**
                       * Inclui o mes para a pessoa valor base
                       */
                        foreach ($oPessoa->aValorGrupo as $iIndice => $nValor) {
                            LogDirf::write('Valor do grupo['.$iIndice.'] : '.$nValor);

                            //Tipo de IRRF para RRA
                            $sTipoirrf      = '1889';

                            if (!in_array($iIndice, $this->aGruposRRA)) {
                                $sTipoirrf    = '0561';

                                if ($lInativoPensionistaMolestia) {
                                    //Tipo de IRRF para Proventos de Aposentadoria, Reserva, Reforma ou Pensão e com moléstia
                                    $sTipoirrf  = '3533';
                                }
                                $oPessoa->tipoDirf[$ind] = $sTipoirrf;
                            } else {
                                if (empty($nValor)) {
                                    continue;
                                }
                            }
                            LogDirf::write('Tipo de receita : '.$sTipoirrf);

                            $oDaoRhDirfGeracaoPessoalValor = new cl_rhdirfgeracaodadospessoalvalor;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_mes                       = $ind;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_rhdirftipovalor           = $iIndice;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_tipoirrf                  = $sTipoirrf;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_instit                    = db_getsession("DB_instit");
                            $oDaoRhDirfGeracaoPessoalValor->rh98_rhdirfgeracaodadospessoal = $oPessoa->codigodirf;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_valor                     = "{$nValor}";
                            $oDaoRhDirfGeracaoPessoalValor->incluir(null);
                            if ($oDaoRhDirfGeracaoPessoalValor->erro_status == 0) {
                                    throw new Exception("Erro[10] - Erro ao incluir valores bases da DIRF.\n{$oDaoRhDirfGeracaoPessoalValor->erro_msg}");
                            }

                  /**
                   * vincula as matriculas ao valor calculado para o cpf.
                   */

                            $oDaoRhDirfGeracaoPessoalMatricula->rh99_regist                         = $oDados->r01_regist;
                            $oDaoRhDirfGeracaoPessoalMatricula->rh99_rhdirfgeracaodadospessoalvalor = $oDaoRhDirfGeracaoPessoalValor->rh98_sequencial;
                            $oDaoRhDirfGeracaoPessoalMatricula->incluir(null);
                            if ($oDaoRhDirfGeracaoPessoalMatricula->erro_status == 0) {
                                $sMsg  = "Erro[11] - Erro ao incluir matriculas para calculo da DIRF.";
                                $sMsg .= "\n{$oDaoRhDirfGeracaoPessoalMatricula->erro_msg}";
                                throw new Exception($sMsg);
                            }
                            unset($oDaoRhDirfGeracaoPessoalValor);
                        }

                        foreach ($oPessoa->aValorGrupo13 as $iIndice => $nValor) {
                            LogDirf::write('Valor do grupo13['.$iIndice.'] : '.$nValor);

                            $sTipoirrf   = '0561';

                            if ($lInativoPensionistaMolestia) {
                                  //Tipo de IRRF para Proventos de Aposentadoria, Reserva, Reforma ou Pensão e com moléstia
                                  $sTipoirrf = '3533';
                            }

                            LogDirf::write('Tipo de receita : '.$sTipoirrf);

                            $oDaoRhDirfGeracaoPessoalValor = new cl_rhdirfgeracaodadospessoalvalor;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_mes                       = 13;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_rhdirftipovalor           = $iIndice;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_tipoirrf                  = $sTipoirrf;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_rhdirfgeracaodadospessoal = $oPessoa->codigodirf;
                            $oDaoRhDirfGeracaoPessoalValor->rh98_valor                     = "{$nValor}";
                            $oDaoRhDirfGeracaoPessoalValor->rh98_instit                    = db_getsession("DB_instit");
                            $oDaoRhDirfGeracaoPessoalValor->incluir(null);
                            if ($oDaoRhDirfGeracaoPessoalValor->erro_status == 0) {
                                throw new Exception("Erro[12] - Erro ao incluir valores bases da DIRF para 13.\n{$oDaoRhDirfGeracaoPessoalValor->erro_msg}");
                            }

                /**
                 * vincula as matriculas ao valor calculado para o cpf.
                 */

                            $oDaoRhDirfGeracaoPessoalMatricula->rh99_regist =  $oDados->r01_regist;
                            $oDaoRhDirfGeracaoPessoalMatricula->rh99_rhdirfgeracaodadospessoalvalor = $oDaoRhDirfGeracaoPessoalValor->rh98_sequencial;
                            $oDaoRhDirfGeracaoPessoalMatricula->incluir(null);
                            if ($oDaoRhDirfGeracaoPessoalMatricula->erro_status == 0) {
                                  $sMsg  = "Erro[13] - Erro ao incluir matriculas para calculo da DIRF.";
                                  $sMsg .= "\n{$oDaoRhDirfGeracaoPessoalMatricula->erro_msg}";
                                  throw new Exception($sMsg);
                            }
                            unset($oDaoRhDirfGeracaoPessoalValor);
                        }
                    }//Fim do for complementos pessoal
                    LogDirf::write('Valor do grupo 1 depois de muitos acontecimentos: ' . $oPessoa->aValorGrupo[1]);
                }// Fim do for pessoas
            }// Fim do for dos meses

            unset($rsDadosPessoal);
        } // Fim do for do offset
    }
  /**
   * calcula os valores para a matricula
   *
   * @param PgSql\Result $arq
   * @param string $sigla
   * @param object $oPessoa
   */
    public function calculaValoresDirfPessoal($arq, $sigla, $oPessoa)
    {

        LogDirf::write("Iniciando função calculaValoresDirfPessoal. Par?metros:");
        LogDirf::write('--   $sigla: ' . $sigla);
        LogDirf::write('-- $oPessoa: ' . $oPessoa->rh01_numcgm);

        global $tributo,$subpes,$subini,$inssirf,$pess,$Ipes,
        $vdeducao65_13, $vdeducao65_13, $mtribs13;

      // situacao de ferias novas e nova forma de ver as bases da complementar;
        $lercomplementar = ($subpes >= $this->sAnoMesAlterFerias?true:false);
        LogDirf::write("Verificando como está o valor da complementar: ".($lercomplementar? "true": "false"));

        while ($arquivo = pg_fetch_object($arq)) {
          // salario + ferias (base bruta p/ irf);
            if (!isset($oPessoa->aValorGrupo[1])) {
                $oPessoa->aValorGrupo[1] = 0;
            }

            LogDirf::write('Rubrica: '. $arquivo->rubric .' Valor: '. $arquivo->valor);

            if ($arquivo->rubric == "R981" || ( $arquivo->rubric == "R983"  && $sigla != "r20_")) {
                LogDirf::write('Verificando rubrica: ' . $arquivo->rubric);

                if (( $sigla != "r48_"  || ( $sigla == "r48_" && $lercomplementar ))) {
                    LogDirf::write('Verificando sigla: ' . $sigla);
                    LogDirf::write("$sigla == r48_ && (\$lercomplementar=".($lercomplementar? "true": "false"));

                    if (!isset($oPessoa->aValorGrupo[1])) {
                        $oPessoa->aValorGrupo[1] = 0;
                    }

                    $oPessoa->aValorGrupo[1] += $arquivo->valor;
                    $oPessoa->mtributo += $arquivo->valor;

                    LogDirf::write('Atribunto valor ao grupo 1: ' . $oPessoa->aValorGrupo[1]);
                    LogDirf::write('Atribuindo valor ao $oPessoa->mtributo: ' . $oPessoa->mtributo);
                }
            } else {
              // 13o salario (base bruta p/ irf);
                if ($arquivo->rubric == "R982") {
                    LogDirf::write('Verificando rubrica: R982');

                    $iTipoArquivo = 1;
                    if ($oPessoa->lInativoOuPensionista) {
                        $iTipoArquivo = 12;
                    }

                    LogDirf::write('Tipo de arquivo: ' . $iTipoArquivo);

                    if (!isset($oPessoa->aValorGrupo13[$iTipoArquivo])) {
                        $oPessoa->aValorGrupo13[$iTipoArquivo] = 0;
                    }
                    if ($sigla != "r48_" || ( $sigla == "r48_" && $lercomplementar )) {
                        LogDirf::write('Verificando (sigla != de r48_) ou ( sigla == r48 && $lercomplementar)');
                        LogDirf::write('Verificando Sigla: ' . $sigla);

                        $oPessoa->aValorGrupo13[$iTipoArquivo] += $arquivo->valor;
                        $oPessoa->mtribs13         += $arquivo->valor;

                        LogDirf::write('Atribuindo valor ao aValorGrupo13['.$iTipoArquivo.']= ' . $oPessoa->aValorGrupo13[$iTipoArquivo]);
                        LogDirf::write('Atribuindo valor ao mtribs13= ' . $oPessoa->mtribs13);

                        if (!isset($oPessoa->aValorGrupo[$iTipoArquivo])) {
                            $oPessoa->aValorGrupo[$iTipoArquivo] = 0;
                        }
                    }
                } else {
                  // vlr ref dependentes p/ irf;
                    if ($arquivo->rubric == "R984") {
                        LogDirf::write('? rubrica R984 ');

                        if (!isset($oPessoa->aValorGrupo[4])) {
                              $oPessoa->aValorGrupo[4] = 0;
                        }

                        if (!isset($oPessoa->aValorGrupo13[4])) {
                            $oPessoa->aValorGrupo13[4] = 0;
                        }
                        if ($sigla == "r35_") {
                            LogDirf::write('? sigla r35_');
                            if (!db_empty($oPessoa->aValorGrupo13[4]) && !db_empty($arquivo->valor)) {
                                $oPessoa->aValorGrupo13[4] = 0;
                            }
                            $oPessoa->aValorGrupo13[4] += $arquivo->valor;
                        } elseif ($sigla == "r20_" && $oPessoa->mtribs13 > 0) {
                            LogDirf::write('? sigla r20_ && $oPessoa->mtribs13 > 0');
                            if (!isset($oPessoa->aValorGrupo13[4])) {
                                $oPessoa->aValorGrupo13[4] = 0;
                            }
                            if (!db_empty($oPessoa->aValorGrupo13[4]) && !db_empty($arquivo->valor)) {
                                $oPessoa->aValorGrupo13[4] = 0;
                            }
                            $oPessoa->aValorGrupo13[4]  += $arquivo->valor;
                        } elseif ($sigla == "r48_" && $lercomplementar) {
                            LogDirf::write('? sigla 48_ && $lercomplementar ? verdadeiro');
                          // somente ler o dependente da complementar se este nao;
                          // estiver no salario ( que foi lido primeiro );
                            if (db_empty($oPessoa->aValorGrupo[4])) {
                                $oPessoa->aValorGrupo[4] += $arquivo->valor;
                            }
                        } elseif ($sigla != "r48_") {
                            LogDirf::write('Não ? r48_');
                            if (db_empty($oPessoa->aValorGrupo[4])) {
                                $oPessoa->aValorGrupo[4] += $arquivo->valor;
                            }
                        }
                        LogDirf::write('Valor do Grupo13[4]: '. $oPessoa->aValorGrupo13[4]);
                    }

                    if ($arquivo->rubric == "R957") {
                        LogDirf::write('? rubrica R957 ');

                        if (!isset($oPessoa->aValorGrupo[25])) {
                              $oPessoa->aValorGrupo[25] = 0;
                        }

                        if (!isset($oPessoa->aValorGrupo13[25])) {
                            $oPessoa->aValorGrupo13[25] = 0;
                        }
                        if ($sigla == "r35_") {
                            LogDirf::write('? sigla r35_');
                            if (!db_empty($oPessoa->aValorGrupo13[25]) && !db_empty($arquivo->valor)) {
                                $oPessoa->aValorGrupo13[25] = 0;
                            }
                            $oPessoa->aValorGrupo13[25] += $arquivo->valor;
                        } elseif ($sigla == "r20_" && $oPessoa->mtribs13 > 0) {
                            LogDirf::write('? sigla r20_ && $oPessoa->mtribs13 > 0');
                            if (!isset($oPessoa->aValorGrupo13[25])) {
                                $oPessoa->aValorGrupo13[25] = 0;
                            }
                            if (!db_empty($oPessoa->aValorGrupo13[25]) && !db_empty($arquivo->valor)) {
                                $oPessoa->aValorGrupo13[25] = 0;
                            }
                            $oPessoa->aValorGrupo13[25]  += $arquivo->valor;
                        } elseif ($sigla == "r48_" && $lercomplementar) {
                            LogDirf::write('? sigla 48_ && $lercomplementar ? verdadeiro');
                          // somente ler o dependente da complementar se este nao;
                          // estiver no salario ( que foi lido primeiro );
                            if (db_empty($oPessoa->aValorGrupo[25])) {
                                $oPessoa->aValorGrupo[25] += $arquivo->valor;
                            }
                        } elseif ($sigla != "r48_") {
                            LogDirf::write('Não ? r48_');
                            if (db_empty($oPessoa->aValorGrupo[25])) {
                                $oPessoa->aValorGrupo[25] += $arquivo->valor;
                            }
                        }
                        LogDirf::write('Valor do Grupo13[25]: '. $oPessoa->aValorGrupo13[25]);
                    }
                  // deducao +65 anos para salario e 13.salario;
                    if ($lercomplementar && $arquivo->rubric == "R997" || $arquivo->rubric == "R999") {
                        LogDirf::write('Verificando rubrica se R997 ou R999 --> '.$arquivo->rubric);

                        if (!isset($oPessoa->aValorGrupo[7])) {
                            $oPessoa->aValorGrupo[7] = 0;
                        }
                        if (!isset($oPessoa->aValorGrupo13[7])) {
                            $oPessoa->aValorGrupo13[7] = 0;
                        }
                        if ($sigla == "r35_" ||  $arquivo->rubric == "R999") {
                            LogDirf::write('Verificando se a sigla ? r35_  ou a rubrica R999');

                            if (!db_empty($oPessoa->aValorGrupo13[7]) && !db_empty($arquivo->valor)) {
                                $oPessoa->aValorGrupo13[7] = 0;
                            }
                            $oPessoa->aValorGrupo13[7] += $arquivo->valor;
                            if (isset($oPessoa->aValorGrupo13[1])) {
                                $oPessoa->aValorGrupo13[1] -= $arquivo->valor;
                            }
                        } elseif ($sigla == "r48_" && $lercomplementar) {
                            LogDirf::write('Verificando se a sigla ? r48_ e $lercomplementar = true');

                            $oPessoa->aValorGrupo[7] += $arquivo->valor;
                        } elseif ($sigla == "r31_") {
                            LogDirf::write('Sigla ? r31_');
                            if (db_empty($oPessoa->vdeducao65)) {
                                LogDirf::write('$oPessoa->vdeducao65 ? vazio');
                                $oPessoa->aValorGrupo[7] += $arquivo->valor;
                            }
                          /**
                           * Alteracoes tarefa 43944
                           * Soma valor complementar(r48_) ou salario(r14_) ou rescisao(r20_)
                           */
                        } elseif ($sigla == "r48_" || $sigla == "r14_" || $sigla == "r20_") {
                            LogDirf::write('Verificando se é sigla r48_ ou r14_ ou r20_');
                            $oPessoa->aValorGrupo[7] += $arquivo->valor;
                        }

                        LogDirf::write('Valor atribuido ao grupo 7: ' . $oPessoa->aValorGrupo[7]);
                    }
                    $mrubr = $arquivo->rubric;

                    if ($mrubr == "R975") {
                        if (!isset($oPessoa->aValorGrupo[12])) {
                            $oPessoa->aValorGrupo[12] = 0;
                        }
                        if ($arquivo->pd == 2) {
                            $oPessoa->aValorGrupo[12] -= $arquivo->valor;
                        } else {
                            $oPessoa->aValorGrupo[12] += $arquivo->valor;
                        }
                    }
                //*** o arquivo bases e lido a partir do mes de processamento (inicial);
                // busca valores de base bruta fora da folha (menos 13o salario);
                // exemplos: precatorios, dsd de riogrande. se lancar o precatorio;
                // como base de ir, esta nao devera estar marcada nesta base. ;
                    if (db_at($mrubr, $this->sel_B911) > 0) {
                        if ($arquivo->pd == 2) {
                            $oPessoa->aValorGrupo[1] -= $arquivo->valor;
                        } else {
                            $oPessoa->aValorGrupo[1] += $arquivo->valor;
                        }
                    }
                    if (($sigla != "r48_" || ($sigla == "r48_" && $lercomplementar ) )) {
                      // busca irf (menos 13o salario);
                        if (db_at($mrubr, $this->sel_B906) > 0) {
                            if (!isset($oPessoa->aValorGrupo[6])) {
                                  $oPessoa->aValorGrupo[6] = 0;
                            }
                            if ($arquivo->pd == 2) {
                                $oPessoa->aValorGrupo[6] += $arquivo->valor;
                            } else {
                                $oPessoa->aValorGrupo[6] -= $arquivo->valor;
                            }
                        }
                      // busca irf (13o salario);
                        if (db_at($mrubr, $this->sel_B909) > 0) {
                            if (!isset($oPessoa->aValorGrupo13[6])) {
                                $oPessoa->aValorGrupo13[6] = 0;
                            }
                            if ($arquivo->pd == 2) {
                                $oPessoa->aValorGrupo13[6] += $arquivo->valor;
                            } else {
                                $oPessoa->aValorGrupo13[6] -= $arquivo->valor;
                            }
                        }

                  // prev 13o salario;
                        if (db_at($mrubr, $this->sel_B908) > 0) {
                            if (!isset($oPessoa->aValorGrupo13[2])) {
                                $oPessoa->aValorGrupo13[2] = 0;
                            }
                            if ($arquivo->pd == 2) {
                                $oPessoa->aValorGrupo13[2]+= $arquivo->valor;
                            } else {
                                $oPessoa->aValorGrupo13[2] -= $arquivo->valor;
                            }
                        }
                  // busca previd (menos de 13o salario);
                        if (db_at($mrubr, $this->sel_B907) > 0) {
                            if (!isset($oPessoa->aValorGrupo[2])) {
                                $oPessoa->aValorGrupo[2] = 0;
                            }
                            if (strtolower($inssirf[0]["r33_tipo"]) == "o" && $pess[$Ipes]["r01_tbprev"] != '0') {
                                if ($arquivo->pd == 2) {
                                    $oPessoa->aValorGrupo[2] += $arquivo->valor;
                                } else {
                                    $oPessoa->aValorGrupo[2] -= $arquivo->valor;
                                }
                            } else {
              // previdencia privada ;
              // mantive como no comprovante porem neste todas as previdencia;
              // ficam como deducoes ;
                                if (!isset($oPessoa->aValorGrupo[2])) {
                                          $oPessoa->aValorGrupo[2] = 0;
                                }
                                if ($arquivo->pd == 2) {
                                    $oPessoa->aValorGrupo[2] += $arquivo->valor;
                                } else {
                                    $oPessoa->aValorGrupo[2] -= $arquivo->valor;
                                }
                            }
                        }

                        // nao estava lendo esta base na dirf e no comprovante estava...;
                        // previdencia privada tambem e deducao;
                        $this->processarDadosPrevidenciaPrivada($oPessoa, $mrubr, 0, '', $arquivo);

                        if (db_at($mrubr, $this->sel_B903) > 0) {
                            if (!isset($oPessoa->aValorGrupo[8])) {
                                $oPessoa->aValorGrupo[8] = 0;
                            }

                            if ($arquivo->pd == 1 || $arquivo->pd == 3) {
                                $oPessoa->aValorGrupo[8] += $arquivo->valor;
                            } else {
                                $oPessoa->aValorGrupo[8] -= $arquivo->valor;
                            }
                        }
                        if (db_at($mrubr, $this->sel_B912) > 0) {
                            if (!isset($oPessoa->aValorGrupo[10])) {
                                $oPessoa->aValorGrupo[10] = 0;
                            }
                            if ($arquivo->pd == 2) {
                                $oPessoa->aValorGrupo[10] -= $arquivo->valor;
                            } else {
                                $oPessoa->aValorGrupo[10] += $arquivo->valor;
                            }
                        }
                    }
                //                    ;
                // busca vlrs pensao alimenticia ;
                    $this->processarDadosPensaoAlimenticia($mrubr, $oPessoa, $sigla, 0, $arquivo);
                    if (db_at($mrubr, $this->sel_B901) > 0) {
                        if (!isset($oPessoa->aValorGrupo[13])) {
                            $oPessoa->aValorGrupo[13] = 0;
                        }
                        if ($arquivo->pd == 1) {
                            $oPessoa->aValorGrupo[13] -= $arquivo->valor;
                        } else {
                            $oPessoa->aValorGrupo[13] += $arquivo->valor;
                        }
                    }
                    if (db_at($mrubr, $this->sel_B914) > 0) {
                        if (!isset($oPessoa->aValorGrupo[14])) {
                            $oPessoa->aValorGrupo[14] = 0;
                        }
                        if ($arquivo->pd == 1) {
                            $oPessoa->aValorGrupo[14] -= $arquivo->valor;
                        } else {
                            $oPessoa->aValorGrupo[14] += $arquivo->valor;
                        }
                    }

                    if (db_at($mrubr, $this->sel_B915) > 0) {
                        if (!isset($oPessoa->aValorGrupo[15])) {
                            $oPessoa->aValorGrupo[15] = 0;
                        }

                        if ($arquivo->pd == 2) {
                            $oPessoa->aValorGrupo[15] -= $arquivo->valor;
                        } else {
                            $oPessoa->aValorGrupo[15] += $arquivo->valor;
                        }
                    }

                /**
                 * @todo verificar esta solução.
                 *
                 * solução paliativa utilizada para o cliente Os?rio. Verifica se os dados que estão sendo
                 * processados são referentes a Rescisão(gerfres) caso sejam, irá considerar os valores para as rubricas da base
                 * B913, que são apenas para os casos de? 'Rendimentos Isentos é Indenizações por Rescisão de Contrato de Trabalho, inclusive a título de PDV(RIIRP)'.
                 * Pois rubricas que estavam lançadas no ponto de férias, estavam sendo consideradas como RIIRP,
                 * quando o correto é apenas rescisão ser considerada RIIRP.
                 *
                 * O problema ocorre pelo fato das rubricas que estão na B913 serem utilizadas tanto para rescisão quanto para férias.
                 */

                    if (db_at($mrubr, $this->sel_B913) > 0 && $sigla == 'r20_') {
                        if (!isset($oPessoa->aValorGrupo[9])) {
                            $oPessoa->aValorGrupo[9] = 0;
                        }
                        if ($arquivo->pd == 1) {
                            $oPessoa->aValorGrupo[9] += $arquivo->valor;
                        } elseif ($arquivo->pd == 2) {
                            $oPessoa->aValorGrupo[9] -= $arquivo->valor;
                        }
                    }
                }
            }
        }
    }

  /**
   * processa todos os pagamentos e retencoes na contabilidade
   *
   */
    public function processarDadosContabilidade()
    {
        $oDaoRhDirfGeracaoPessoal = new cl_rhdirfgeracaodadospessoal;

        $sSqlDadosContabilidade  = "with cte_conlancam as ( ";
        $sSqlDadosContabilidade .= "        select c70_codlan, ";
        $sSqlDadosContabilidade .= "               z01_numcgm, ";
        $sSqlDadosContabilidade .= "               trim(z01_cgccpf) as z01_cgccpf, ";
        $sSqlDadosContabilidade .= "               trim(z01_nome) as z01_nome, ";
        $sSqlDadosContabilidade .= "               extract(month from c70_data) as mes, ";
        $sSqlDadosContabilidade .= "               coalesce((case when c53_tipo = 30 then c70_valor ";
        $sSqlDadosContabilidade .= "                              else 0 ";
        $sSqlDadosContabilidade .= "                         end), 0) as valor_pago, ";
        $sSqlDadosContabilidade .= "               coalesce((case when c53_tipo = 31 then c70_valor ";
        $sSqlDadosContabilidade .= "                              else 0 ";
        $sSqlDadosContabilidade .= "                         end), 0) as valor_estornado, ";
        $sSqlDadosContabilidade .= "               c80_codord ";
        $sSqlDadosContabilidade .= "        from conlancam ";
        $sSqlDadosContabilidade .= "             join conlancamdoc on c71_codlan = c70_codlan ";
        $sSqlDadosContabilidade .= "             join conhistdoc on c53_coddoc = c71_coddoc ";
        $sSqlDadosContabilidade .= "             join conlancamord on c80_codlan = c70_codlan ";
        $sSqlDadosContabilidade .= "             join conlancamemp on c75_codlan = c70_codlan ";
        $sSqlDadosContabilidade .= "             join empempenho on e60_numemp = c75_numemp ";
        $sSqlDadosContabilidade .= "                            and e60_instit = " . db_getsession("DB_instit");
        $sSqlDadosContabilidade .= "             join orcdotacao on o58_coddot = e60_coddot ";
        $sSqlDadosContabilidade .= "                            and o58_anousu = e60_anousu ";
        $sSqlDadosContabilidade .= "             join orcunidade on o41_unidade = o58_unidade ";
        $sSqlDadosContabilidade .= "                            and o41_anousu = o58_anousu ";
        $sSqlDadosContabilidade .= "                            and o41_orgao = o58_orgao ";
        $sSqlDadosContabilidade .= "             join cgm on z01_numcgm = e60_numcgm ";
        $sSqlDadosContabilidade .= "        where c70_data between '{$this->iAno}-01-01' and '{$this->iAno}-12-31'";
        $sSqlDadosContabilidade .= "          and c53_tipo in (30, 31) ";
        $sSqlDadosContabilidade .= "          and o41_cnpj = '{$this->sCnpj}'";
        $sSqlDadosContabilidade .= "     ), ";

        $sSqlDadosContabilidade .= "     cte_retencao as ( ";
        $sSqlDadosContabilidade .= "        select distinct c80_codord, ";
        $sSqlDadosContabilidade .= "               e30_codigo ";
        $sSqlDadosContabilidade .= "        from cte_conlancam ";
        $sSqlDadosContabilidade .= "             join retencaopagordem on e20_pagordem = c80_codord ";
        $sSqlDadosContabilidade .= "             join retencaoreceitas on e23_retencaopagordem = e20_sequencial ";
        $sSqlDadosContabilidade .= "                                  and e23_recolhido is true ";
        $sSqlDadosContabilidade .= "             join retencaotiporec on e23_retencaotiporec = e21_sequencial ";
        $sSqlDadosContabilidade .= "                                 and e21_retencaotipocalc in (1,2) ";
        $sSqlDadosContabilidade .= "                                 and e21_retencaotiporecgrupo = 1 ";
        $sSqlDadosContabilidade .= "             join retencaonaturezatiporec on e31_retencaotiporec = e21_sequencial ";
        $sSqlDadosContabilidade .= "             join retencaonatureza on e31_retencaonatureza = e30_sequencial ";
        $sSqlDadosContabilidade .= "                                  and e31_retencaonatureza is not null ";
        $sSqlDadosContabilidade .= "     ), ";

        $sSqlDadosContabilidade .= "     cte_rhpessoal as ( ";
        $sSqlDadosContabilidade .= "        select distinct rh01_numcgm ";
        $sSqlDadosContabilidade .= "        from cte_conlancam ";
        $sSqlDadosContabilidade .= "             join rhpessoal on rh01_numcgm = z01_numcgm ";
        $sSqlDadosContabilidade .= "             join cte_retencao  ";
        $sSqlDadosContabilidade .= "               on cte_retencao.c80_codord = cte_conlancam.c80_codord ";
        $sSqlDadosContabilidade .= "     ), ";

        $sSqlDadosContabilidade .= "     cte_valida_retencao as ( ";
        $sSqlDadosContabilidade .= "        select cte_conlancam.*, ";
        $sSqlDadosContabilidade .= "               case when cte_retencao.e30_codigo is not null ";
        $sSqlDadosContabilidade .= "                    then cte_retencao.e30_codigo ";
        $sSqlDadosContabilidade .= "                    else case when length(z01_cgccpf) = 14 then '1708' ";
        $sSqlDadosContabilidade .= "                         else '0588' ";
        $sSqlDadosContabilidade .= "                         end ";
        $sSqlDadosContabilidade .= "               end as tipo ";
        $sSqlDadosContabilidade .= "        from cte_conlancam ";
        $sSqlDadosContabilidade .= "             left join cte_retencao ";
        $sSqlDadosContabilidade .= "             on cte_retencao.c80_codord = cte_conlancam.c80_codord ";
        $sSqlDadosContabilidade .= "     ), ";

        $sSqlDadosContabilidade .= "     cte_somatorio as ( ";
        $sSqlDadosContabilidade .= "        select z01_numcgm, ";
        $sSqlDadosContabilidade .= "               z01_cgccpf, ";
        $sSqlDadosContabilidade .= "               z01_nome, ";
        $sSqlDadosContabilidade .= "               sum(valor_pago) as valor_pago, ";
        $sSqlDadosContabilidade .= "               sum(valor_estornado) as valor_estornado, ";
        $sSqlDadosContabilidade .= "               mes, ";
        $sSqlDadosContabilidade .= "               tipo, ";
        $sSqlDadosContabilidade .= "               c80_codord ";
        $sSqlDadosContabilidade .= "        from cte_valida_retencao ";
        $sSqlDadosContabilidade .= "             left join cte_rhpessoal ";
        $sSqlDadosContabilidade .= "                    on cte_rhpessoal.rh01_numcgm = cte_valida_retencao.z01_numcgm ";
        $sSqlDadosContabilidade .= "             left join rhpessoal ";
        $sSqlDadosContabilidade .= "                    on rhpessoal.rh01_numcgm = cte_valida_retencao.z01_numcgm ";
        $sSqlDadosContabilidade .= "        where rhpessoal.rh01_numcgm is null ";
        $sSqlDadosContabilidade .= "           or cte_rhpessoal.rh01_numcgm is not null ";
        $sSqlDadosContabilidade .= "        group by z01_numcgm, z01_cgccpf, z01_nome, mes, tipo, c80_codord ";
        $sSqlDadosContabilidade .= "     ), ";

        $sSqlDadosContabilidade .= "     cte_abate_estorno as ( ";
        $sSqlDadosContabilidade .= "        select z01_numcgm, ";
        $sSqlDadosContabilidade .= "               z01_cgccpf, ";
        $sSqlDadosContabilidade .= "               z01_nome, ";
        $sSqlDadosContabilidade .= "               case when valor_pago >= valor_estornado ";
        $sSqlDadosContabilidade .= "                    then (valor_pago - valor_estornado) ";
        $sSqlDadosContabilidade .= "                    else valor_pago ";
        $sSqlDadosContabilidade .= "               end as valor_pago, ";
        $sSqlDadosContabilidade .= "               case when valor_pago >= valor_estornado then 0 ";
        $sSqlDadosContabilidade .= "                    else valor_estornado ";
        $sSqlDadosContabilidade .= "               end as valor_estornado, ";
        $sSqlDadosContabilidade .= "               mes, ";
        $sSqlDadosContabilidade .= "               tipo, ";
        $sSqlDadosContabilidade .= "               c80_codord ";
        $sSqlDadosContabilidade .= "        from cte_somatorio ";
        $sSqlDadosContabilidade .= "     ), ";

        $sSqlDadosContabilidade .= "     cte_demais_estornos as ( ";
        $sSqlDadosContabilidade .= "        select c80_codord as codord, ";
        $sSqlDadosContabilidade .= "               mes as mes_estorno, ";
        $sSqlDadosContabilidade .= "               valor_estornado as vl_estornado ";
        $sSqlDadosContabilidade .= "        from cte_abate_estorno ";
        $sSqlDadosContabilidade .= "        where valor_estornado > 0 ";
        $sSqlDadosContabilidade .= "     ), ";

        $sSqlDadosContabilidade .= "     cte_ajusta_estorno as ( ";
        $sSqlDadosContabilidade .= "        select b.z01_numcgm, ";
        $sSqlDadosContabilidade .= "               b.z01_cgccpf, ";
        $sSqlDadosContabilidade .= "               b.z01_nome, ";
        $sSqlDadosContabilidade .= "               round((b.valor_pago - a.vl_estornado), 2) as valor_pago, ";
        $sSqlDadosContabilidade .= "               0 as valor_estornado, ";
        $sSqlDadosContabilidade .= "               b.mes, ";
        $sSqlDadosContabilidade .= "               b.tipo, ";
        $sSqlDadosContabilidade .= "               b.c80_codord, ";
        $sSqlDadosContabilidade .= "               a.* ";
        $sSqlDadosContabilidade .= "        from cte_demais_estornos a ";
        $sSqlDadosContabilidade .= "             join cte_abate_estorno b on b.c80_codord = a.codord ";
        $sSqlDadosContabilidade .= "                                     and b.mes <= a.mes_estorno ";
        $sSqlDadosContabilidade .= "                                     and b.valor_pago >= a.vl_estornado ";
        $sSqlDadosContabilidade .= "     ), ";

        $sSqlDadosContabilidade .= "     cte_final as ( ";
        $sSqlDadosContabilidade .= "        select a.z01_numcgm, ";
        $sSqlDadosContabilidade .= "               a.z01_cgccpf, ";
        $sSqlDadosContabilidade .= "               a.z01_nome, ";
        $sSqlDadosContabilidade .= "               a.c80_codord, ";
        $sSqlDadosContabilidade .= "               a.mes, ";
        $sSqlDadosContabilidade .= "               a.tipo, ";
        $sSqlDadosContabilidade .= "               case when b.c80_codord is not null then round(b.valor_pago, 2) ";
        $sSqlDadosContabilidade .= "                    else round(a.valor_pago, 2) ";
        $sSqlDadosContabilidade .= "               end as valor_pago, ";
        $sSqlDadosContabilidade .= "               case when c.codord is not null then c.valor_estornado ";
        $sSqlDadosContabilidade .= "                    else a.valor_estornado ";
        $sSqlDadosContabilidade .= "               end as valor_estornado ";
        $sSqlDadosContabilidade .= "        from cte_abate_estorno a ";
        $sSqlDadosContabilidade .= "             left join cte_ajusta_estorno b on b.c80_codord = a.c80_codord ";
        $sSqlDadosContabilidade .= "                                           and b.mes = a.mes ";
        $sSqlDadosContabilidade .= "             left join cte_ajusta_estorno c on c.codord = a.c80_codord ";
        $sSqlDadosContabilidade .= "                                           and c.mes_estorno = a.mes ";
        $sSqlDadosContabilidade .= "     ) ";

        $sSqlDadosContabilidade .= "select z01_numcgm, ";
        $sSqlDadosContabilidade .= "       z01_cgccpf, ";
        $sSqlDadosContabilidade .= "       z01_nome, ";
        $sSqlDadosContabilidade .= "       valor_pago, ";
        $sSqlDadosContabilidade .= "       valor_estornado, ";
        $sSqlDadosContabilidade .= "       mes, ";
        $sSqlDadosContabilidade .= "       tipo, ";
        $sSqlDadosContabilidade .= "       c80_codord ";
        $sSqlDadosContabilidade .= "from cte_final ";
        $sSqlDadosContabilidade .= "where valor_pago > 0 ";
        $sSqlDadosContabilidade .= "order by z01_numcgm, mes, c80_codord; ";

        $rsDadosContabilidade    = db_query($sSqlDadosContabilidade);
        if (!$rsDadosContabilidade || pg_num_rows($rsDadosContabilidade) == 0) {
            $aOrdensPagamento        = array();
        } else {
            $aOrdensPagamento        = db_utils::getCollectionByRecord($rsDadosContabilidade);
        }

        $aDadosDirf = array();

        foreach ($aOrdensPagamento as $oContribuinte) {
            if ($oContribuinte->valor_pago == 0) {
                continue;
            }
            if (!isset($aDadosDirf[$oContribuinte->z01_numcgm])) {
                $oDeclaracaoDirf  = new stdClass();
                $oDeclaracaoDirf->cnpj         = $oContribuinte->z01_cgccpf;
                $oDeclaracaoDirf->nome         = $oContribuinte->z01_nome;
                $oDeclaracaoDirf->valores      = array();
                $oDeclaracaoDirf->retencaomes  = array();
                $aDadosDirf[$oContribuinte->z01_numcgm] = $oDeclaracaoDirf;
            }
           /**
             * Agrupamos os valores por tipo .
             * 1 - Valores de base de calculo (o valor pago total)
             */
            $oValorMesBase           = new stdClass();
            $oValorMesBase->valor    = $oContribuinte->valor_pago;
            $oValorMesBase->mes      = $oContribuinte->mes;
            $oValorMesBase->retencao = $oContribuinte->tipo;

           /**
            * total do valor retido para o mes.
            * calculamos apenas se o valor retido no mes ainda nao foi calculado
            */
            if (!in_array($oContribuinte->mes, $aDadosDirf[$oContribuinte->z01_numcgm]->retencaomes)) {
                $sSqlDadosIRRF  = " SELECT coalesce(sum(e23_valorretencao), 0) as retido ";
                $sSqlDadosIRRF .= "  from retencaotiporec ";
                $sSqlDadosIRRF .= "       join retencaoreceitas         on e21_sequencial  = e23_retencaotiporec  ";
                $sSqlDadosIRRF .= "       join retencaocorgrupocorrente on e23_sequencial  = e47_retencaoreceita  ";
                $sSqlDadosIRRF .= "       join corgrupocorrente         on k105_sequencial = e47_corgrupocorrente ";
                $sSqlDadosIRRF .= "       join retencaopagordem         on e20_sequencial  = e23_retencaopagordem ";
                $sSqlDadosIRRF .= "       join pagordem                 on e50_codord      = e20_pagordem ";
                $sSqlDadosIRRF .= "       join empempenho               on e50_numemp      = e60_numemp ";
                $sSqlDadosIRRF .= "       join orcdotacao               on e60_coddot      = o58_coddot ";
                $sSqlDadosIRRF .= "                                    and o58_anousu      = e60_anousu ";
                $sSqlDadosIRRF .= "       join orcunidade               on o41_unidade     = o58_unidade ";
                $sSqlDadosIRRF .= "                                    and o58_orgao       = o41_orgao   ";
                $sSqlDadosIRRF .= "                                    and o41_anousu = o58_anousu       ";
                $sSqlDadosIRRF .= "  where e21_retencaotiporecgrupo = 1  ";
                $sSqlDadosIRRF .= "    and e23_recolhido is true         ";
                $sSqlDadosIRRF .= "    and e23_ativo     is true         ";
                $sSqlDadosIRRF .= "    and e21_retencaotipocalc in(1, 2) ";
                $sSqlDadosIRRF .= "    and o41_cnpj = '{$this->sCnpj}'   ";
                $sSqlDadosIRRF .= "    and extract(month from k105_data) = {$oContribuinte->mes} ";
                $sSqlDadosIRRF .= "    and e60_numcgm                    = {$oContribuinte->z01_numcgm} ";
                $sSqlDadosIRRF .= "    and extract(year from k105_data)  = {$this->iAno} ";
                $sSqlDadosIRRF .= "    and e60_instit = ".db_getsession("DB_instit");
                $rsDadosIRRF    = db_query($sSqlDadosIRRF);
                if (!$rsDadosIRRF || pg_num_rows($rsDadosIRRF) == 0) {
                    $nValorRetido     = 0;
                } else {
                    $nValorRetido     = db_utils::fieldsMemory($rsDadosIRRF, 0)->retido;
                }

          /**
           * calculamos o valor total de inss para o mes, do cgm.
           */
                $sSqlDadosInss  = " SELECT coalesce(sum(e23_valorretencao), 0) as retido ";
                $sSqlDadosInss .= "  from retencaotiporec ";
                $sSqlDadosInss .= "       join retencaoreceitas         on e21_sequencial  = e23_retencaotiporec  ";
                $sSqlDadosInss .= "       join retencaocorgrupocorrente on e23_sequencial  = e47_retencaoreceita  ";
                $sSqlDadosInss .= "       join corgrupocorrente         on k105_sequencial = e47_corgrupocorrente ";
                $sSqlDadosInss .= "       join retencaopagordem         on e20_sequencial  = e23_retencaopagordem ";
                $sSqlDadosInss .= "       join pagordem                 on e50_codord      = e20_pagordem ";
                $sSqlDadosInss .= "       join empempenho               on e50_numemp      = e60_numemp ";
                $sSqlDadosInss .= "       join orcdotacao               on e60_coddot      = o58_coddot ";
                $sSqlDadosInss .= "                                          and o58_anousu      = e60_anousu ";
                $sSqlDadosInss .= "       join orcunidade               on o41_unidade     = o58_unidade ";
                $sSqlDadosInss .= "                                    and o58_orgao       = o41_orgao   ";
                $sSqlDadosInss .= "                                    and o41_anousu = o58_anousu       ";
                $sSqlDadosInss .= "  where e21_retencaotiporecgrupo = 1  ";
                $sSqlDadosInss .= "    and e23_recolhido is true         ";
                $sSqlDadosInss .= "    and e23_ativo     is true         ";
                $sSqlDadosInss .= "    and e21_retencaotipocalc in(3, 7) ";
                $sSqlDadosInss .= "    and o41_cnpj = '{$this->sCnpj}'";
                $sSqlDadosInss .= "    and extract(month from k105_data) = {$oContribuinte->mes} ";
                $sSqlDadosInss .= "    and e60_numcgm                    = {$oContribuinte->z01_numcgm} ";
                $sSqlDadosInss .= "    and extract(year from k105_data)  = {$this->iAno} ";
                $sSqlDadosInss .= "    and e60_instit = ".db_getsession("DB_instit");

                $rsDadosInss    = db_query($sSqlDadosInss);
                if (!$rsDadosInss || pg_num_rows($rsDadosInss) == 0) {
                    $nValorInss     = 0;
                } else {
                    $nValorInss     = db_utils::fieldsMemory($rsDadosInss, 0)->retido;
                }

                if ($nValorInss > 0) {
                    $oValorMesPrevidencia           = new stdClass();
                    $oValorMesPrevidencia->valor    = $nValorInss;
                    $oValorMesPrevidencia->mes      = $oContribuinte->mes;
                    $oValorMesPrevidencia->retencao = $oContribuinte->tipo;
                    $aDadosDirf[$oContribuinte->z01_numcgm]->valores[2][] = $oValorMesPrevidencia;
                }

                if ($nValorRetido > 0) {
                    $oValorMesRetido           = new stdClass();
                    $oValorMesRetido->valor    = $nValorRetido;
                    $oValorMesRetido->mes      = $oContribuinte->mes;
                    $oValorMesRetido->retencao = $oContribuinte->tipo;
                    $aDadosDirf[$oContribuinte->z01_numcgm]->valores[6][] = $oValorMesRetido;
                }
            }
            $aDadosDirf[$oContribuinte->z01_numcgm]->valores[1][]  = $oValorMesBase;
            $aDadosDirf[$oContribuinte->z01_numcgm]->retencaomes[] = $oContribuinte->mes;
            unset($nValorRetido);
        }
      /**
       * realizamos a inclusão conforme do tipo.
       */
        foreach ($aDadosDirf as $iNumCgm => $oDirf) {
            if (trim($oDirf->cnpj) == "") {
                $this->addInconsistente($iNumCgm, $oDirf->nome, 'CPF Inválido');
                continue;
            }

            $oDaoRhDirfGeracaoPessoal->rh96_cpfcnpj = $oDirf->cnpj;
            $oDaoRhDirfGeracaoPessoal->rh96_numcgm  = $iNumCgm;
            $oDaoRhDirfGeracaoPessoal->rh96_regist  = '0';
            $oDaoRhDirfGeracaoPessoal->rh96_tipo    = 2;
            $oDaoRhDirfGeracaoPessoal->rh96_rhdirfgeracao = $this->iCodigoDirf;
            $oDaoRhDirfGeracaoPessoal->incluir(null);

            if ($oDaoRhDirfGeracaoPessoal->erro_status == 0) {
                $sMsg  = "Erro[14] -  Erro ao incluir valores(CGM: {$iNumCgm} com CPF/CNPJ Inválido) da DIRF.\n";
                $sMsg .= "{$oDaoRhDirfGeracaoPessoal->erro_msg}";
                throw new Exception($sMsg);
            }

            $oDirf->codigodirf = $oDaoRhDirfGeracaoPessoal->rh96_sequencial;
            foreach ($oDirf->valores as $iTipo => $aValor) {
                foreach ($aValor as $oValor) {
                    $oDaoRhDirfGeracaoPessoalValor = new cl_rhdirfgeracaodadospessoalvalor;
                    $oDaoRhDirfGeracaoPessoalValor->rh98_mes                       = $oValor->mes;
                    $oDaoRhDirfGeracaoPessoalValor->rh98_rhdirftipovalor           = $iTipo;
                    $oDaoRhDirfGeracaoPessoalValor->rh98_tipoirrf                  = "$oValor->retencao";
                    $oDaoRhDirfGeracaoPessoalValor->rh98_rhdirfgeracaodadospessoal = $oDirf->codigodirf;
                    $oDaoRhDirfGeracaoPessoalValor->rh98_instit                    = db_getsession("DB_instit");
                    $oDaoRhDirfGeracaoPessoalValor->rh98_valor                     = "{$oValor->valor}";
                    $oDaoRhDirfGeracaoPessoalValor->incluir(null);
                    if ($oDaoRhDirfGeracaoPessoalValor->erro_status == 0) {
                        $sMsg  = "Erro[15] - Erro ao incluir valores bases da DIRF para .\n";
                        $sMsg .= $oDaoRhDirfGeracaoPessoalValor->erro_msg;
                        throw new Exception($sMsg);
                    }
                    unset($oDaoRhDirfGeracaoPessoalValor);
                }
            }
        }
    }


    public function gerarArquivo($oDados, $lGerarContabil = true)
    {

        $oDaoCgm = new cl_cgm;

        $aArquivosGerar     = array("Dirf",
                                "DECPJ",
                                "RESPO",
                                "IDREC",
                                "BPFDEC",
                                "BPJDEC",
                                "RTRT",
                                "FIMDirf",
                                "PSE",
                                "RIO",
                                "OPSE",
                                "TPSE");
      /**
       * tipo de registros de valores gerados.
       * os tipos usados por mes vao de 1 até 11.
       * os demais são valores unicos, em outro registro.
       */
        $aSiglasTipoArquivo = array( 1 => "RTRT",
                                 2 => "RTPO" ,
                                 3 => "RTPP",
                                 4 => "RTDP",
                                 5 => "RTPA",
                                 6 => "RTIRF",
                                 7 => "RIP65",
                                 8 => "RIDAC",
                                 9 => "RIIRP",
                                10 => "RIAP",
                                11 => "MOLA",
                                12 => "RIMOG",
                                13 => "SAUDE1",
                                14 => "SAUDE2",
                                15 => "RIO",
                                25 => "RTDS",
                                );
        $aMeses            = array( 1 => "janeiro",
                                 2 => "fevereiro" ,
                                 3 => "marco",
                                 4 => "abril",
                                 5 => "maio",
                                 6 => "junho",
                                 7 => "julho",
                                 8 => "agosto",
                                 9 => "setembro",
                                10 => "outubro",
                                11 => "novembro",
                                12 => "dezembro",
                                13 => "decimo_terceiro",
                                );

        $sSqlDadosInstituicao = " select z01_cgccpf as cgc,
                                     z01_nome   as nomeinst,
                                     z01_ender  as ender,
                                     z01_telef  as telef,
                                     z01_munic  as munic
                                from orcunidade
                               inner join rhlotaexe on rh26_orgao   = o41_orgao
                                                   and rh26_unidade = o41_unidade
                                                   and o41_anousu   = rh26_anousu
                               inner join rhlota    on r70_codigo   = rh26_codigo
                               inner join cgm       on r70_numcgm   = z01_numcgm
                               where o41_cnpj   = '{$this->sCnpj}'
                                 and z01_cgccpf = '{$this->sCnpj}'";
        $rsDadosInstituicao    = db_query($sSqlDadosInstituicao);
        if (!$rsDadosInstituicao) {
            $iNumRowsDadosInstituicao = null;
        } else {
            $iNumRowsDadosInstituicao = pg_num_rows($rsDadosInstituicao);
        }

        if ($iNumRowsDadosInstituicao > 0) {
            $oDadosInstituicao  = db_utils::fieldsMemory($rsDadosInstituicao, 0);
        } else {
            $oDadosInstituicao = db_stdClass::getDadosInstit(db_getsession("DB_instit"));
        }

        require_once(modification("dbforms/db_layouttxt.php"));
    /**
     * processamos os pagamentos dos fornecedores
     */
        $sTipo = "1";

        if ($lGerarContabil) {
            $sTipo .= ", 2";
        }

        $sSqlTipoReceitas  = " SELECT distinct rh98_tipoirrf,                 ";
        $sSqlTipoReceitas .= "        length(trim(z01_cgccpf)) as tipopessoa, ";
        $sSqlTipoReceitas .= "        rh96_numcgm,                            ";
        $sSqlTipoReceitas .= "        trim(z01_cgccpf) as z01_cgccpf,         ";
        $sSqlTipoReceitas .= "        z01_nome,                               ";
        $sSqlTipoReceitas .= "        rh95_sequencial,                        ";
        $sSqlTipoReceitas .= "        case                                    ";
        $sSqlTipoReceitas .= "          when exists ( select 1               ";
        $sSqlTipoReceitas .= "                           from rhdirfgeracaodadospessoalvalor z ";
        $sSqlTipoReceitas .= "                                inner join rhdirfgeracaodadospessoal x on x.rh96_sequencial = z.rh98_rhdirfgeracaodadospessoal ";
        $sSqlTipoReceitas .= "                          where x.rh96_rhdirfgeracao   = rhdirfgeracaodadospessoal.rh96_rhdirfgeracao ";
        $sSqlTipoReceitas .= "                            and z.rh98_rhdirftipovalor = rhdirfgeracaodadospessoalvalor.rh98_rhdirftipovalor ";
        $sSqlTipoReceitas .= "                            and x.rh96_numcgm          = rhdirfgeracaodadospessoal.rh96_numcgm   ";
        $sSqlTipoReceitas .= "                            and x.rh96_tipo in (1,2)                                             ";
        $sSqlTipoReceitas .= "                            and z.rh98_tipoirrf < rhdirfgeracaodadospessoalvalor.rh98_tipoirrf   ";
        $sSqlTipoReceitas .= "                       ) then false                                                              ";
        $sSqlTipoReceitas .= "          else true                                                                              ";
        $sSqlTipoReceitas .= "        end as sem_retencao                                                                      ";
        $sSqlTipoReceitas .= "   from rhdirfgeracaodadospessoalvalor                                                           ";
        $sSqlTipoReceitas .= "        inner join rhdirfgeracaodadospessoal  on rh98_rhdirfgeracaodadospessoal      = rh96_sequencial ";
        $sSqlTipoReceitas .= "        left  join rhdirfgeracaopessoalregist on rh99_rhdirfgeracaodadospessoalvalor = rh98_sequencial ";
        $sSqlTipoReceitas .= "        inner join rhdirfgeracao              on rh96_rhdirfgeracao                  = rh95_sequencial ";
        $sSqlTipoReceitas .= "        inner join cgm                        on z01_numcgm                          = rh96_numcgm     ";
        $sSqlTipoReceitas .= "  where rh95_ano = {$this->iAno}                ";
        $sSqlTipoReceitas .= "    and (rh98_rhdirftipovalor in (6) ";

        if ($oDados->sAcima6000 == "S") {
            $sSqlTipoReceitas .= "      or  ((select sum(case when rh98_rhdirftipovalor <> 7 then z.rh98_valor else z.rh98_valor*(-1) end) as valor         ";
            $sSqlTipoReceitas .= "          from  rhdirfgeracaodadospessoalvalor z   ";
            $sSqlTipoReceitas .= "                inner join rhdirfgeracaodadospessoal a ";
            $sSqlTipoReceitas .= "                        on z.rh98_rhdirfgeracaodadospessoal = a.rh96_sequencial ";
            $sSqlTipoReceitas .= "           inner join rhdirfgeracao b on a.rh96_rhdirfgeracao  = b.rh95_sequencial ";
            $sSqlTipoReceitas .= "          where a.rh96_numcgm = rhdirfgeracaodadospessoal.rh96_numcgm ";
            $sSqlTipoReceitas .= "            and b.rh95_fontepagadora   = '{$this->sCnpj}' ";
            $sSqlTipoReceitas .= "            and b.rh95_ano   = {$this->iAno} ";
            $sSqlTipoReceitas .= "            and b.rh95_instit = ". db_getsession('DB_instit');
            $sSqlTipoReceitas .= "            and z.rh98_rhdirftipovalor  in (1, 7, 12)";
            $sSqlTipoReceitas .= "            and a.rh96_tipo  = 1) >= ". $this->getValorLimite() .")";
        } else {
            $sSqlTipoReceitas .= "      or  ((select sum(z.rh98_valor) as valor         ";
            $sSqlTipoReceitas .= "          from  rhdirfgeracaodadospessoalvalor z   ";
            $sSqlTipoReceitas .= "                inner join rhdirfgeracaodadospessoal a ";
            $sSqlTipoReceitas .= "                          on z.rh98_rhdirfgeracaodadospessoal = a.rh96_sequencial ";
            $sSqlTipoReceitas .= "           inner join rhdirfgeracao b on a.rh96_rhdirfgeracao  = b.rh95_sequencial ";
            $sSqlTipoReceitas .= "          where a.rh96_numcgm = rhdirfgeracaodadospessoal.rh96_numcgm ";
            $sSqlTipoReceitas .= "            and b.rh95_fontepagadora   = '{$this->sCnpj}' ";
            $sSqlTipoReceitas .= "            and b.rh95_ano   = {$this->iAno} ";
            $sSqlTipoReceitas .= "            and b.rh95_instit = ". db_getsession('DB_instit');
            $sSqlTipoReceitas .= "            and z.rh98_rhdirftipovalor  in (1)";
            $sSqlTipoReceitas .= "            and a.rh96_tipo  = 1) >= 0.01)";
        }

        $sSqlTipoReceitas .= "         ) ";
        $sSqlTipoReceitas .= "    and rh96_tipo in({$sTipo})                  ";
        $sSqlTipoReceitas .= "    and rh95_fontepagadora   = '{$this->sCnpj}' ";
        $sSqlTipoReceitas .= "    and rh95_instit = ". db_getsession('DB_instit');

        $sMatriculaSelecionadas = $this->getMatriculas();
        if (!empty($sMatriculaSelecionadas)) {
            $sSqlTipoReceitas .= "  and rh99_regist in({$this->sMatriculas})";
        }

        $sSqlTipoReceitas .= "   order by rh98_tipoirrf,1,                    ";
        $sSqlTipoReceitas .= "            z01_cgccpf                          ";
        $rsTipoReceitas    = db_query($sSqlTipoReceitas);
        if (!$rsTipoReceitas) {
            $iTotalLinhas      = 0;
        } else {
            $iTotalLinhas      = pg_num_rows($rsTipoReceitas);
        }

        $aLinhasDirf       = array();

        for ($i = 0; $i < $iTotalLinhas; $i++) {
            $oTipoReceita = db_utils::fieldsMemory($rsTipoReceitas, $i);

            if (!isset($aLinhasDirf[$oTipoReceita->rh98_tipoirrf])) {
                $oLinhaDirf = new stdClass();
                $oLinhaDirf->receita  = $oTipoReceita->rh98_tipoirrf;
                $oLinhaDirf->fisica   = array();
                $oLinhaDirf->juridica = array();

                $aLinhasDirf[$oTipoReceita->rh98_tipoirrf] = $oLinhaDirf;
            }

            $oPessoa = new stdClass();
            $oPessoa->nome        = $oTipoReceita->z01_nome;
            $oPessoa->cgm         = $oTipoReceita->rh96_numcgm;
            $oPessoa->totalsaude1 = 0;
            $oPessoa->totalsaude2 = 0;
            $oPessoa->totaloutros = 0;

            $this->calculaValoresMensaisTipo($oTipoReceita->rh95_sequencial, $oPessoa, $oTipoReceita->rh98_tipoirrf, ($oTipoReceita->sem_retencao=='t'?true:false));

            if ($oTipoReceita->tipopessoa == 11) {
                if (!isset($aLinhasDirf[$oTipoReceita->rh98_tipoirrf]->fisica[$oTipoReceita->rh96_numcgm])) {
                    $oPessoa->portadormolestia = false;
                    $oPessoa->deficientefisico = false;
                    $oPessoa->datalaudo        = '';

                    $sSqlMolestias  = "SELECT rh02_deficientefisico, ";
                    $sSqlMolestias .= "       rh02_portadormolestia, ";
                    $sSqlMolestias .= "       rh02_datalaudomolestia ";
                    $sSqlMolestias .= "  from rhpessoal ";
                    $sSqlMolestias .= "       inner join rhpessoalmov on rh01_regist = rh02_regist ";
                    $sSqlMolestias .= " where rh02_anousu = ".$this->iAno;
                    $sSqlMolestias .= "   and rh02_mesusu = ".$this->iMes;
                    $sSqlMolestias .= "   and rh01_numcgm = {$oTipoReceita->rh96_numcgm}";

                    $rsMolestias   = db_query($sSqlMolestias);

                    if ($rsMolestias && pg_num_rows($rsMolestias) > 0) {
                          $oDadosMolestia = db_utils::fieldsMemory($rsMolestias, 0);
                          $oPessoa->portadormolestia = $oDadosMolestia->rh02_portadormolestia=="t"?true:false;
                          $oPessoa->deficientefisico = $oDadosMolestia->rh02_deficientefisico=="t"?true:false;
                          $oPessoa->datalaudo        = $oDadosMolestia->rh02_datalaudomolestia;
                          unset($oDadosMolestia);
                    }

                    $oPessoa->cpf        = $oTipoReceita->z01_cgccpf;
                    $oPessoa->data_laudo = "";
                    $aLinhasDirf[$oTipoReceita->rh98_tipoirrf]->fisica[$oTipoReceita->rh96_numcgm] = $oPessoa;
                }
            } elseif ($oTipoReceita->tipopessoa == 14) {
                if (!isset($aLinhasDirf[$oTipoReceita->rh98_tipoirrf]->juridica[$oTipoReceita->rh96_numcgm])) {
                            $oPessoa->cnpj = $oTipoReceita->z01_cgccpf;
                            $aLinhasDirf[$oTipoReceita->rh98_tipoirrf]->juridica[$oTipoReceita->rh96_numcgm] = $oPessoa;
                }
            }
            unset($oTipoReceita);
        }

        $sNomeArquivo  = "dirf_{$this->iAno}_{$this->sCnpj}.txt";
        $iCodigoLayout = $this->getCodigoLayout();
        $oLayout       = new db_layouttxt($iCodigoLayout, "tmp/{$sNomeArquivo}", implode(" ", $aArquivosGerar));

    /**
     * escrevemos o header do txt
     */
        $oLayout->setCampoTipoLinha(1);
        $oLayout->setCampoIdentLinha("Dirf");
        $oLayout->setCampo("identificador_registro", 'Dirf');
        $oLayout->setCampo("ano_referencia", $this->iAno+1);
        $oLayout->setCampo("ano_calendario", $this->iAno);

        $sRetificadora = 'N';

        if ($oDados->TipoDeclaracao == "R") {
            $sRetificadora = 'S';
        }

        $oLayout->setCampo("idetificador_retificadora", $sRetificadora);
        $oLayout->setCampo("numero_recibo", $oDados->iNumeroRecibo);
        $oLayout->setCampo("identificador_estrutura_layout", $this->getCodigoArquivo());
        $oLayout->geraDadosLinha();

        $oLayout->setCampoTipoLinha(3);
        $oLayout->setCampoIdentLinha("RESPO");
        $oLayout->setCampo("identificador_registro", 'RESPO');
        $oLayout->setCampo("cpf", $oDados->sCpfResponsavel);
        $oLayout->setCampo("nome", urldecode(db_stdClass::db_stripTagsJson($oDados->sNomeResponsavel)));
        $oLayout->setCampo("ddd", $oDados->sDDDResponsavel);
        $oLayout->setCampo("telefone", $oDados->sFoneResponsavel);
        $oLayout->geraDadosLinha();

        $oLayout->setCampoTipoLinha(3);
        $oLayout->setCampoIdentLinha("DECPJ");
        $oLayout->setCampo("identificador_registro", 'DECPJ');
        $oLayout->setCampo("responsavel_perante_cnpj", $oDados->sCpfResponsavelCNPJ);
        $oLayout->setCampo("cnpj", $this->sCnpj);
        $oLayout->setCampo("nome_empresarial", $oDadosInstituicao->nomeinst);
        if ($oDados->iNumeroANS > 0) {
            $oLayout->setCampo("plano_privado_assistencia", "S");
        }
        $oLayout->geraDadosLinha();
        foreach ($aLinhasDirf as $oLinhaDirf) {
            $oLayout->setCampoTipoLinha(3);
            $oLayout->setCampoIdentLinha("IDREC");

            $oLayout->setCampo("identificador_registro", 'IDREC');
            $oLayout->setCampo("codigo_receita", $oLinhaDirf->receita);
            $oLayout->geraDadosLinha();

            foreach ($oLinhaDirf->fisica as $oPessoaFisica) {
                $oLayout->setCampoTipoLinha(3);
                $oLayout->setCampoIdentLinha("BPFDEC");
                $oLayout->setCampo("identificador_registro", 'BPFDEC');
                $oLayout->setCampo("nome", $oPessoaFisica->nome);
                $oLayout->setCampo("cpf", $oPessoaFisica->cpf);
                $oLayout->setCampo("data_laudo", $oPessoaFisica->data_laudo);
                $oLayout->geraDadosLinha();

              /**
               * carregamos as informações dos pagamentos
               */
                foreach ($oPessoaFisica->pagamentos as $iTipo => $oPagamento) {
                    $oLayout->setCampoTipoLinha(3);
                    $oLayout->setCampoIdentLinha("RTRT");
                    $iSiglaRegistro = $aSiglasTipoArquivo[$iTipo];
                    $oLayout->setCampo("idetificador_registro", $iSiglaRegistro);

                      /**
                       * escreve os meses com cada valor
                       */
                    for ($iMes = 1; $iMes <= 13; $iMes++) {
                        $aMes[$iMes] = '';
                        foreach ($oPagamento as $oMes) {
                            if ($oMes->rh98_mes == $iMes) {
                                $nValorDeducao65 = 0;

                                if ($oMes->rh98_rhdirftipovalor == 1) {
                                        $nValorDeducao65 = $this->getValorDeducaoRIP65($iMes, $oPessoaFisica->pagamentos);
                                }
                                $nValorLancar = ( ( $oMes->valor - $nValorDeducao65 ) > 0 ? ( $oMes->valor - $nValorDeducao65 ) : 0  );

                                $aMes[$iMes] = db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($nValorLancar, 'f')))), 's', '0', 8, 'e', 2);
                            }
                        }
                        $oLayout->setCampo($aMeses[$iMes], $aMes[$iMes]);
                    }
                    $oLayout->geraDadosLinha();
                }
              /*
               * Outros dados.
               */
                if ($oPessoaFisica->totaloutros > 0) {
                    $nValorAno = db_formatar(str_replace(',', '', str_replace(
                        '.',
                        '',
                        trim(db_formatar($oPessoaFisica->totaloutros, 'f'))
                    )), 's', '0', 13, 'e', 2);

                    $oLayout->setCampoTipoLinha(3);
                    $oLayout->setCampoIdentLinha("RIO");
                    $oLayout->setCampo("identificador_registro", "RIO");
                    $oLayout->setCampo("valor_anual", $nValorAno);
                    $oLayout->setCampo("descricao_rend_isentos", "");
                    $oLayout->geraDadosLinha();
                }
            }

            foreach ($oLinhaDirf->juridica as $oPessoaFisica) {
                $oLayout->setCampoTipoLinha(3);
                $oLayout->setCampoIdentLinha("BPJDEC");
                $oLayout->setCampo("identificador_registro", 'BPJDEC');
                $oLayout->setCampo("nome", $oPessoaFisica->nome);
                $oLayout->setCampo("cnpj", $oPessoaFisica->cnpj);
                $oLayout->geraDadosLinha();
             /**
              * carregamos as informações dos pagamentos
              */
                foreach ($oPessoaFisica->pagamentos as $iTipo => $oPagamento) {
                    $oLayout->setCampoTipoLinha(3);
                    $oLayout->setCampoIdentLinha("RTRT");

                    $oLayout->limpaCampos();

                    $iSiglaRegistro = $aSiglasTipoArquivo[$iTipo];


                    $oLayout->setCampo("idetificador_registro", $iSiglaRegistro);
                   /**
                    * escreve os meses com cada valor
                    */
                    foreach ($oPagamento as $oMes) {
                         $oLayout->setCampo($aMeses[$oMes->rh98_mes], db_formatar(str_replace(',', '', str_replace('.', '', trim(db_formatar($oMes->valor, 'f')))), 's', '0', 8, 'e', 2));
                    }
                    $oLayout->geraDadosLinha();
                }
            }
        }

    /**
     * geramos as linhas do plano de saude
     */

        if (trim($oDados->iNumeroANS) != "" || trim($oDados->iNumeroANS2) != "") {
            $oLayout->setCampoTipoLinha(3);
            $oLayout->setCampoIdentLinha("PSE");
            $oLayout->setCampo("identificador_registro", 'PSE');
            $oLayout->geraDadosLinha();

            if (trim($oDados->iNumeroANS) != "") {
                $sSqlNome  = $oDaoCgm->sql_query_file($oDados->iCcgmSaude, "z01_nome, z01_cgccpf");
                $rsNome    = db_query($sSqlNome);

                $oOperador = new \stdClass();
                $oOperador->z01_cgccpf = 0;
                $oOperador->z01_nome = 'Não foi possível buscar o nome';
                if ($rsNome && pg_num_rows($rsNome)  > 0) {
                    $oOperador = db_utils::fieldsMemory($rsNome, 0);
                }


                $oLayout->setCampoTipoLinha(3);
                $oLayout->setCampoIdentLinha("OPSE");
                $oLayout->setCampo("identificador_registro", 'OPSE');
                $oLayout->setCampo("cnpj", str_pad($oOperador->z01_cgccpf, 14, "0", STR_PAD_LEFT));
                $oLayout->setCampo("nome", $oOperador->z01_nome);
                $oLayout->setCampo("registro_ans", str_pad($oDados->iNumeroANS, 6, "0", STR_PAD_LEFT));
                $oLayout->geraDadosLinha();

              /**
               * geramos todas as pessoas que possuem valor do plano de saude maior que zero.
               */
                foreach ($aLinhasDirf as $oLinhaDirf) {
                    foreach ($oLinhaDirf->fisica as $oPessoaFisica) {
                        if ($oPessoaFisica->totalsaude1 > 0) {
                            $nValorAno = db_formatar(str_replace(',', '', str_replace(
                                '.',
                                '',
                                trim(db_formatar($oPessoaFisica->totalsaude1, 'f'))
                            )), 's', '0', 13, 'e', 2);
                            $oLayout->setCampoTipoLinha(3);
                            $oLayout->setCampoIdentLinha("TPSE");
                            $oLayout->setCampo("identificador_registro", 'TPSE');
                            $oLayout->setCampo("cnpj", str_pad($oPessoaFisica->cpf, 11, "0", STR_PAD_LEFT));
                            $oLayout->setCampo("nome", $oPessoaFisica->nome);
                            $oLayout->setCampo("valor_ano", $nValorAno);
                            $oLayout->geraDadosLinha();
                        }
                    }
                }
            }
            if (trim($oDados->iNumeroANS2) != "") {
                $sSqlNome  = $oDaoCgm->sql_query_file($oDados->iCcgmSaude2, "z01_nome, z01_cgccpf");
                $rsNome    = db_query($sSqlNome);
                if (!$rsNome || pg_num_rows($rsNome) == 0) {
                    $oOperador->z01_cgccpf = 0;
                    $oOperador->z01_nome = 'Não foi possível buscar o nome';
                }
                $oOperador = db_utils::fieldsMemory($rsNome, 0);

                $oLayout->setCampoTipoLinha(3);
                $oLayout->setCampoIdentLinha("OPSE");
                $oLayout->setCampo("identificador_registro", 'OPSE');
                $oLayout->setCampo("cnpj", str_pad($oOperador->z01_cgccpf, 14, "0", STR_PAD_LEFT));
                $oLayout->setCampo("nome", $oOperador->z01_nome);
                $oLayout->setCampo("registro_ans", str_pad($oDados->iNumeroANS2, 6, "0", STR_PAD_LEFT));
                $oLayout->geraDadosLinha();

          /**
           * geramos todas as pessoas que possuem valor do plano de saude maior que zero.
           */
                foreach ($aLinhasDirf as $oLinhaDirf) {
                    foreach ($oLinhaDirf->fisica as $oPessoaFisica) {
                        if ($oPessoaFisica->totalsaude2 > 0) {
                            $nValorAno = db_formatar(str_replace(',', '', str_replace(
                                '.',
                                '',
                                trim(db_formatar($oPessoaFisica->totalsaude2, 'f'))
                            )), 's', '0', 13, 'e', 2);
                                $oLayout->setCampoTipoLinha(3);
                                $oLayout->setCampoIdentLinha("TPSE");
                                $oLayout->setCampo("identificador_registro", 'TPSE');
                                $oLayout->setCampo("cnpj", str_pad($oPessoaFisica->cpf, 11, "0", STR_PAD_LEFT));
                                $oLayout->setCampo("nome", $oPessoaFisica->nome);
                                $oLayout->setCampo("valor_ano", $nValorAno);
                                $oLayout->geraDadosLinha();
                        }
                    }
                }
            }
        }

        $oLayout->setCampoTipoLinha(4);
        $oLayout->setCampoIdentLinha("FIMDirf");
        $oLayout->setCampo("identificador_registro", 'FIMDirf');
        $oLayout->geraDadosLinha();
        return $sNomeArquivo;
    }

    public function calculaValoresMensaisTipo($iCodigoDirf, $oPessoa, $iTipoIRRF, $lSemRetencao = true)
    {

        $sSqlPagamentos  = " select rh98_rhdirftipovalor,                 ";
        $sSqlPagamentos .= "        sum(rh98_valor) as valor,             ";
        $sSqlPagamentos .= "        rh98_mes                              ";
        $sSqlPagamentos .= "  from  rhdirfgeracaodadospessoalvalor        ";
        $sSqlPagamentos .= "        inner join rhdirfgeracaodadospessoal on rh98_rhdirfgeracaodadospessoal = rh96_sequencial";
        $sSqlPagamentos .= "  where rh96_numcgm        = {$oPessoa->cgm}  ";
        $sSqlPagamentos .= "    and rh96_rhdirfgeracao = {$iCodigoDirf}   ";

        if ($lSemRetencao) {
            $sSqlPagamentos .= "  and rh98_tipoirrf in ('0','{$iTipoIRRF}') ";
        } else {
            $sSqlPagamentos .= "  and rh98_tipoirrf in ('{$iTipoIRRF}')     ";
        }

        $sSqlPagamentos .= "  group by rh98_rhdirftipovalor,              ";
        $sSqlPagamentos .= "        rh98_mes                              ";
        $sSqlPagamentos .= "        having sum(rh98_valor) > 0            ";
        $sSqlPagamentos .= "  order by rh98_rhdirftipovalor,rh98_mes      ";

        $rsPagamentos = db_query($sSqlPagamentos);
        if (!$rsPagamentos || pg_num_rows($rsPagamentos) == 0) {
            $aPagamentos  = array();
        } else {
            $aPagamentos  = db_utils::getCollectionByRecord($rsPagamentos);
        }

        if (!empty($aPagamentos)) {
            foreach ($aPagamentos as $oPagamento) {
              /*
               * 13 ? pagamento de plano de saude.
               */
                if ($oPagamento->rh98_rhdirftipovalor != 13
                && $oPagamento->rh98_rhdirftipovalor != 14
                && $oPagamento->rh98_rhdirftipovalor != 15
                ) {
                    if (!isset($oPessoa->pagamentos[$oPagamento->rh98_rhdirftipovalor])) {
                        $oPessoa->pagamentos[$oPagamento->rh98_rhdirftipovalor] = array();
                    }

                    $oPessoa->pagamentos[$oPagamento->rh98_rhdirftipovalor][] = $oPagamento;
                } elseif ($oPagamento->rh98_rhdirftipovalor == 13) {
                    $oPessoa->totalsaude1 += $oPagamento->valor;
                } elseif ($oPagamento->rh98_rhdirftipovalor == 14) {
                    $oPessoa->totalsaude2 += $oPagamento->valor;
                } elseif ($oPagamento->rh98_rhdirftipovalor == 15) {
                    $oPessoa->totaloutros += $oPagamento->valor;
                }
            }

            unset($aPagamentos);
        }
    }


    public function getValorDeducaoRIP65($iMes, $aPagamentos)
    {

        foreach ($aPagamentos as $aDadosMeses) {
            foreach ($aDadosMeses as $oDadosMes) {
                if ($oDadosMes->rh98_mes == $iMes && $oDadosMes->rh98_rhdirftipovalor == 7) {
                    return $oDadosMes->valor;
                }
            }
        }
        return 0;
    }


  /**
   *  Adiciona inconsistências
   */
    public function addInconsistente($iNumCgm, $sNome, $sMotivo)
    {

        $oInconsistencia = new stdClass();
        $oInconsistencia->iNumCgm = $iNumCgm;
        $oInconsistencia->sNome   = $sNome;
        $oInconsistencia->sMotivo = $sMotivo;

        $this->aInconsistentes[$iNumCgm] = $oInconsistencia;
    }

    public function clearInconsistente()
    {

        LogDirf::write('Limpando array de inconsistências...');
        $this->aInconsistentes = array();
    }

  /**
   * Verifica se existe inconsistências no processamento
   */
    public function hasInconsistencias()
    {

        if (count($this->aInconsistentes) > 0) {
            return true;
        } else {
            return false;
        }
    }

  /**
   *  Gerar relatério de Inconsist?ncias
   */
    public function geraArquivoInconsistencias()
    {

        global $head2;

        $sNomeArquivo = "tmp/inconsistencias_dirf_".date('Ymdi').".pdf";

        $oPdf = new PDF();
        $oPdf->Open();
        $oPdf->AliasNbPages();
        $oPdf->SetFillColor(235);

        $iFonte    = 8;
        $iAlt      = 5;
        $iPreenche = 1;

        $head2 = "Relatório de Inconsistências";

        $oPdf->AddPage();
        $oPdf->SetFont('Arial', 'b', $iFonte);
        $oPdf->Cell(30, $iAlt, "CGM", 1, 0, 'C', 1);
        $oPdf->Cell(120, $iAlt, "Nome", 1, 0, 'C', 1);
        $oPdf->Cell(40, $iAlt, "Motivo", 1, 1, 'C', 1);

        foreach ($this->aInconsistentes as $oInconsistencia) {
            if ($oPdf->gety() > $oPdf->h - 30) {
                $oPdf->AddPage();
                $oPdf->SetFont('Arial', 'b', $iFonte);
                $oPdf->Cell(30, $iAlt, "CGM", 1, 0, 'C', 1);
                $oPdf->Cell(120, $iAlt, "Nome", 1, 0, 'C', 1);
                $oPdf->Cell(40, $iAlt, "Motivo", 1, 1, 'C', 1);
            }

            if ($iPreenche == 1) {
                $iPreenche = 0;
            } else {
                $iPreenche = 1;
            }

            $oPdf->SetFont('Arial', '', $iFonte);
            $oPdf->Cell(30, $iAlt, $oInconsistencia->iNumCgm, 0, 0, 'C', $iPreenche);
            $oPdf->Cell(120, $iAlt, $oInconsistencia->sNome, 0, 0, 'L', $iPreenche);
            $oPdf->Cell(40, $iAlt, $oInconsistencia->sMotivo, 0, 1, 'L', $iPreenche);
        }

        ob_start();
        $oPdf->Output($sNomeArquivo);
        ob_end_clean();

        return $sNomeArquivo;
    }

  /**
   * retorna os dados das unidades com cnj diferente da institui??o
   *
   * @return array
   */
    function retornarUnidadesCnpjInvalido()
    {

        $sSqlVerificaUnidades  = "select * from (SELECT distinct ";
        $sSqlVerificaUnidades .= "                      o41_orgao, ";
        $sSqlVerificaUnidades .= "                      o41_unidade, ";
        $sSqlVerificaUnidades .= "                      o41_descr, ";
        $sSqlVerificaUnidades .= "                      o41_cnpj , ";
        $sSqlVerificaUnidades .= "                      cgc as cnpj_instituicao, ";
        $sSqlVerificaUnidades .= "                      codigo, ";
        $sSqlVerificaUnidades .= "                      o41_anousu ";
        $sSqlVerificaUnidades .= "                 from orcunidade ";
        $sSqlVerificaUnidades .= "                      inner join orcdotacao on o41_orgao   = o58_orgao ";
        $sSqlVerificaUnidades .= "                                           and o41_unidade = o58_unidade ";
        $sSqlVerificaUnidades .= "                                           and o41_anousu  = o58_anousu ";
        $sSqlVerificaUnidades .= "                      inner join empempenho on o58_coddot  = e60_coddot ";
        $sSqlVerificaUnidades .= "                                           and o58_anousu  = e60_anousu ";
        $sSqlVerificaUnidades .= "                      inner join db_config  on o41_instit  = codigo ";
        $sSqlVerificaUnidades .= "                order by o41_orgao,o41_unidade) as x ";
        $sSqlVerificaUnidades .= " where o41_cnpj <> cnpj_instituicao ";
        $rsVerificacao         = db_query($sSqlVerificaUnidades);
        if (!$rsVerificacao || pg_num_rows($rsVerificacao) == 0) {
            return array();
        }
        return db_utils::getCollectionByRecord($rsVerificacao, false, false, true);
    }

    function retornaMatriculasDirf($lGerarContabil = true, $sAcima)
    {

        $sTipo = "1";
        if ($lGerarContabil) {
            $sTipo .= ", 2";
        }

        $sSqlMatriculasDirf  = " select distinct rh99_regist, ";
        $sSqlMatriculasDirf .= "        z01_nome              ";
        $sSqlMatriculasDirf .= "   from rhdirfgeracaodadospessoalvalor                                                                 ";
        $sSqlMatriculasDirf .= "        inner join rhdirfgeracaodadospessoal  on rh98_rhdirfgeracaodadospessoal      = rh96_sequencial ";
        $sSqlMatriculasDirf .= "        inner  join rhdirfgeracaopessoalregist on rh99_rhdirfgeracaodadospessoalvalor = rh98_sequencial ";
        $sSqlMatriculasDirf .= "        inner join rhdirfgeracao              on rh96_rhdirfgeracao                  = rh95_sequencial ";
        $sSqlMatriculasDirf .= "        inner join cgm                        on z01_numcgm                          = rh96_numcgm     ";
        $sSqlMatriculasDirf .= "  where rh95_ano = {$this->iAno}                ";
        $sSqlMatriculasDirf .= "    and (rh98_rhdirftipovalor in (6) ";

        if ($sAcima == "S") {
            $sSqlMatriculasDirf .= "      or  ((select sum(case when rh98_rhdirftipovalor <> 7 then z.rh98_valor else z.rh98_valor*(-1) end) as valor         ";
            $sSqlMatriculasDirf .= "          from  rhdirfgeracaodadospessoalvalor z   ";
            $sSqlMatriculasDirf .= "                inner join rhdirfgeracaodadospessoal a ";
            $sSqlMatriculasDirf .= "                          on z.rh98_rhdirfgeracaodadospessoal = a.rh96_sequencial ";
            $sSqlMatriculasDirf .= "           inner join rhdirfgeracao b            on a.rh96_rhdirfgeracao  = b.rh95_sequencial ";
            $sSqlMatriculasDirf .= "          where a.rh96_numcgm = rhdirfgeracaodadospessoal.rh96_numcgm ";
            $sSqlMatriculasDirf .= "            and b.rh95_fontepagadora   = '{$this->sCnpj}' ";
            $sSqlMatriculasDirf .= "            and b.rh95_ano   = {$this->iAno} ";
            $sSqlMatriculasDirf .= "            and b.rh95_instit = ". db_getsession('DB_instit');
            $sSqlMatriculasDirf .= "            and z.rh98_rhdirftipovalor  in (1, 7, 12)";
            $sSqlMatriculasDirf .= "            and a.rh96_tipo  = 1) >= ". $this->getValorLimite() .")";
        } else {
            $sSqlMatriculasDirf .= "      or  ((select sum(z.rh98_valor) as valor         ";
            $sSqlMatriculasDirf .= "          from rhdirfgeracaodadospessoalvalor z   ";
            $sSqlMatriculasDirf .= "               join rhdirfgeracaodadospessoal a ";
            $sSqlMatriculasDirf .= "                 on z.rh98_rhdirfgeracaodadospessoal = a.rh96_sequencial ";
            $sSqlMatriculasDirf .= "               join rhdirfgeracao b on a.rh96_rhdirfgeracao  = b.rh95_sequencial ";
            $sSqlMatriculasDirf .= "          where a.rh96_numcgm = rhdirfgeracaodadospessoal.rh96_numcgm ";
            $sSqlMatriculasDirf .= "            and b.rh95_fontepagadora = '{$this->sCnpj}' ";
            $sSqlMatriculasDirf .= "            and b.rh95_ano = {$this->iAno} ";
            $sSqlMatriculasDirf .= "            and b.rh95_instit = ". db_getsession('DB_instit');
            $sSqlMatriculasDirf .= "            and z.rh98_rhdirftipovalor  in (1)";
            $sSqlMatriculasDirf .= "            and a.rh96_tipo  = 1) >= 0.00)";
        }

        $sSqlMatriculasDirf .= "          ) ";
        $sSqlMatriculasDirf .= "    and rh96_tipo in({$sTipo})                  ";
        $sSqlMatriculasDirf .= "    and rh95_fontepagadora   = '{$this->sCnpj}' ";
        $sSqlMatriculasDirf .= "    and rh95_instit = ". db_getsession('DB_instit');
        $sSqlMatriculasDirf .= "   order by rh99_regist, z01_nome       ";


        $rsMatriculasDirf    = db_query($sSqlMatriculasDirf);
        if (!$rsMatriculasDirf) {
            return array();
        }
        $iTotalLinhas        = pg_num_rows($rsMatriculasDirf);
        $aMatriculasDirf     = array();
        if ($iTotalLinhas > 0) {
            $aMatriculasDirf = db_utils::getCollectionByRecord($rsMatriculasDirf);
        }

        return $aMatriculasDirf;
    }

  /**
   * Processa os valores de RRA no ano para o servidor
   *
   * @param  Servidor $oServidor
   * @return
   */
    public function getValorBaseServidor(Servidor $oServidor, Base $oBase)
    {

        LogDirf::write('');
        LogDirf::write('');
        LogDirf::write('Processando valores de RRA');

        $nValor = 0;

        $oCalculoFolhaSalario      = new CalculoFolhaSalario($oServidor);
        $oCalculoFolhaComplementar = new CalculoFolhaComplementar($oServidor);
        $oCalculoFolhaRescisao     = new CalculoFolhaRescisao($oServidor);
        $oCalculoFolha13o          = new CalculoFolha13o($oServidor);

        if (!empty($oBase)) {
            $nValor += $oBase->getValorTotalNoCalculo($oCalculoFolhaSalario, true);
            $nValor += $oBase->getValorTotalNoCalculo($oCalculoFolhaComplementar, true);
            $nValor += $oBase->getValorTotalNoCalculo($oCalculoFolhaRescisao, true);
            $nValor += $oBase->getValorTotalNoCalculo($oCalculoFolha13o, true);
        }

        return $nValor;
    }

    public function processarRRA(Servidor $oServidor, $lPortadorMolestia, $oPessoa)
    {

        $oCompetenciaAtual              = DBPessoal::getCompetenciaFolha();
        $oParametros                    = ParametrosPessoalRepository::getParametros($oCompetenciaAtual);


        if ($oParametros->getBaseRraRendimentosTributaveis() != '') {
            $oBaseRendimentosTributaveis = BaseRepository::getBase($oParametros->getBaseRraRendimentosTributaveis()->getCodigo());
            if ($lPortadorMolestia) {
                $oPessoa->aValorGrupo[23] += $this->getValorRendimentosTributaveisRRA($oServidor, $oBaseRendimentosTributaveis); // Grupo: 23

                LogDirf::write('--> Valor parcial de RRA Molestia ----------------->'.$oPessoa->aValorGrupo[23]);
            } else {
                $oPessoa->aValorGrupo[17]  += $this->getValorRendimentosTributaveisRRA($oServidor, $oBaseRendimentosTributaveis); // Grupo: 17
            }
        }

        if ($oParametros->getBaseRraPrevidenciaSocial() != '') {
            if (!$lPortadorMolestia) {
                $oBasePrevidencia          = BaseRepository::getBase($oParametros->getBaseRraPrevidenciaSocial()->getCodigo());
                $oPessoa->aValorGrupo[18] += $this->getValorPrevidenciaRRA($oServidor, $oBasePrevidencia);                       // Grupo: 18
            }
        }

        if ($oParametros->getBaseRraPensaoAlimenticia() != '') {
            if (!$lPortadorMolestia) {
                $oBasePensao = BaseRepository::getBase($oParametros->getBaseRraPensaoAlimenticia()->getCodigo());
                $oPessoa->aValorGrupo[19] += $this->getValorPensaoAlimenticiaRRA($oServidor, $oBasePensao);  // Grupo: 19
            }
        }

        if ($oParametros->getBaseRraIrrf() != '') {
            if (!$lPortadorMolestia) {
                $oBaseIRRF = BaseRepository::getBase($oParametros->getBaseRraIrrf()->getCodigo());
                $oPessoa->aValorGrupo[20] += $this->getValorIrrfRRA($oServidor, $oBaseIRRF); // Grupo: 20
            }
        }
        LogDirf::write('--> Valor parcial de RRA Tributavel ------------->'.$oPessoa->aValorGrupo[17]);
        LogDirf::write('--> Valor parcial de RRA de Previdencia Social -->'.$oPessoa->aValorGrupo[18]);
        LogDirf::write('--> Valor parcial de RRA de IRRF ---------------->'.$oPessoa->aValorGrupo[20]);


        return;
    }

    public function getValorRendimentosTributaveisRRA(Servidor $oServidor, Base $oBaseRendimentosTributaveisRRA)
    {
        return $this->getValorBaseServidor($oServidor, $oBaseRendimentosTributaveisRRA);
    }

    public function getValorPrevidenciaRRA(Servidor $oServidor, Base $oBasePrevidenciaRRA)
    {
        return $this->getValorBaseServidor($oServidor, $oBasePrevidenciaRRA);
    }

    public function getValorPensaoAlimenticiaRRA(Servidor $oServidor, Base $oBasePensaoAlimenticiaRRA)
    {
        return 0;
    }

    public function getValorIrrfRRA(Servidor $oServidor, Base $oBaseIrrfRRA)
    {
        return $this->getValorBaseServidor($oServidor, $oBaseIrrfRRA);
    }

    public function getGruposRRA()
    {
        return $this->aGruposRRA;
    }

    protected function processarDadosPensaoAlimenticia($mrubr, $oPessoa, $sigla, $Iarq, $arq)
    {

        if (in_array($mrubr, $this->aRubricasPensaoAlimenticia)) {
            if ($sigla == "r35_" || (db_val($mrubr) >= 4000 && db_val($mrubr) < 6000 )) {
                if (!isset($oPessoa->aValorGrupo13[5])) {
                    $oPessoa->aValorGrupo13[5] = 0;
                }
                if ($arq->pd == 1) {
                    $oPessoa->aValorGrupo13[5] -= $arq->valor;
                } else {
                    $oPessoa->aValorGrupo13[5] += $arq->valor;
                }
            } else {
                if (!isset($oPessoa->aValorGrupo[5])) {
                    $oPessoa->aValorGrupo[5] = 0;
                }
                if ($arq->pd == 1) {
                    $oPessoa->aValorGrupo[5] -= $arq->valor;
                } else {
                    $oPessoa->aValorGrupo[5] += $arq->valor;
                }

              // se for forma antiga (R994) deve levar em conta que a;
              // pensao ja estava descontada na base "bruta";
                if (in_array($mrubr, $this->aRubricasBaseRais)) {
                    if (!isset($oPessoa->aValorGrupo[1])) {
                        $oPessoa->aValorGrupo[1] = 0;
                    }
                    if ($arq->pd == 1) {
                        $oPessoa->aValorGrupo[1] -= $arq->valor;
                    } else {
                        $oPessoa->aValorGrupo[1] += $arq->valor;
                    }
                }
            }
        }
    }

  /**
   * PRocessa os dados da Previdencia Privada
   * @param $oPessoa
   * @param $sRubrica
   * @param $iLinhaProcessamento
   * @param $sSiglaTabela
   * @param $aRegistros
   */
    protected function processarDadosPrevidenciaPrivada($oPessoa, $sRubrica, $iLinhaProcessamento, $sSiglaTabela, $aRegistros)
    {

        if (in_array($sRubrica, $this->aRubricasPrevidenciaPrivada)) {
            if (!isset($oPessoa->aValorGrupo[2])) {
                $oPessoa->aValorGrupo[2] = 0;
            }
            if ($aRegistros->pd == 2) {
                $oPessoa->aValorGrupo[2] += $aRegistros->valor;
            } else {
                $oPessoa->aValorGrupo[2] -= $aRegistros->valor;
            }
        }
    }
}
