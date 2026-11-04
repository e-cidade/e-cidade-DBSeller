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

use App\Domain\Tributario\ISSQN\Enums\Integracao\Infisc\IntegracaoInfiscStatusProcessamento;
use App\Domain\Tributario\ISSQN\Enums\Integracao\Infisc\IntegracaoInfiscTipoDebito;

class ImportacaoDiversosCobrancaAdministrativa extends ImportacaoGeralDiversos
{

    private $iTipoDebitoOrigem;

    /**
     * Define se vai unificar os débitos agrupando por receita
     * @var bool
     */
    protected $lUnificarDebitos = false;

    /**
     * Debitos que serão utilizados para processamento
     * @var stdClass[]
     */
    protected $debitosProcessar = array();

    /**
     * Define qual data de vencimento será utilizada para unificar os débitos.
     * @see ImportacaoDiversos
     * @var integer
     */
    private $iOrdemDataVencimento;

    /**
     * @var DBDate
     */
    private $oDataVencimento;

    /**
     * @var integer
     */
    protected $iListaDebitos;

    public function getTipoDebitoOrigem()
    {
        return $this->iTipoDebitoOrigem;
    }

    public function setTipoDebitoOrigem($iTipoDebitoOrigem)
    {
        $this->iTipoDebitoOrigem = $iTipoDebitoOrigem;
    }

    /**
     * @param $lUnificarDebito
     */
    public function setUnificarDebitos($lUnificarDebito)
    {
        $this->lUnificarDebitos = $lUnificarDebito;
    }

    /**
     * Define qual a data de vencimento será utilizada para unificação dos débitos
     * @param $iOrdemDataVencimento
     * @throws ParameterException
     */
    public function setOrdemDataVencimento($iOrdemDataVencimento)
    {
        if (!in_array($iOrdemDataVencimento,
          array(ImportacaoDiversos::MENOR_DATA_VENCIMENTO, ImportacaoDiversos::MAIOR_DATA_VENCIMENTO, ImportacaoDiversos::ESCOLHA_DATA_VENCIMENTO))) {
            throw new ParameterException(_M("tributario.diversos.ImportacaoGeralDiversos.tipo_vencimento_invalido"));
        }
        $this->iOrdemDataVencimento = $iOrdemDataVencimento;
    }

    /**
     * @param stdClass[] $aDebitos
     */
    public function setDebitos(array $aDebitos)
    {
        $this->debitosProcessar = $aDebitos;
    }

    /**
     * @return DBDate
     */
    public function getDataVencimento()
    {
        return $this->oDataVencimento;
    }

    /**
     * @param DBDate $oDataVencimento
     */
    public function setDataVencimento($oDataVencimento)
    {
        $this->oDataVencimento = $oDataVencimento;
    }

    /**
     * @param integer $listaDebitos
     */
    public function setListaDebitos($listaDebitos)
    {
        $this->iListaDebitos = $listaDebitos;
    }

    /**
     * @return bool
     */
    protected function menorDataVencimento()
    {
        return $this->iOrdemDataVencimento == ImportacaoDiversos::MENOR_DATA_VENCIMENTO;
    }

    /**
     * @return bool
     */
    protected function maiorDataVencimento()
    {
        return $this->iOrdemDataVencimento == ImportacaoDiversos::MAIOR_DATA_VENCIMENTO;
    }

    /**
     * @return bool
     */
    protected function escolhaDataVencimento()
    {
        return $this->iOrdemDataVencimento == ImportacaoDiversos::ESCOLHA_DATA_VENCIMENTO;
    }

    /**
     * Função que realiza a consulta dos débitos que serão importados
     * @param $sWhere
     * @return bool|resource
     * @throws DBException
     */
    protected function buscarDebitos($sWhereArrecad)
    {
        $sSqlCaseValor  = " case                                                          ";
        $sSqlCaseValor .= "   when arretipo.k03_tipo = 3 and arrecad.k00_valor = 0 then ( ";
        $sSqlCaseValor .= "     select case                                               ";
        $sSqlCaseValor .= "              when issvar.q05_valor > 0 then issvar.q05_valor  ";
        $sSqlCaseValor .= "              when issvar.q05_valor = 0 then issvar.q05_vlrinf ";
        $sSqlCaseValor .= "            end                                                ";
        $sSqlCaseValor .= "       from issvar                                             ";
        $sSqlCaseValor .= "      where issvar.q05_numpre = arrecad.k00_numpre             ";
        $sSqlCaseValor .= "        and issvar.q05_numpar = arrecad.k00_numpar             ";
        $sSqlCaseValor .= "   )                                                           ";
        $sSqlCaseValor .= "   else arrecad.k00_valor                                      ";
        $sSqlCaseValor .= " end                                                           ";

        $campos = implode(", ", array(
            "arrecad.k00_numpre",
            "1 as k00_numpar",
            "arrecad.k00_numcgm",
            "array_to_string(array_accum(arrecad.k00_dtoper), ',') AS colecao_dtoper",
            "arrecad.k00_receit",
            "array_to_string(array_accum(k00_hist), ',') as colecao_historico",
            "arrecad.k00_tipo",
            "arretipo.k03_tipo",
            "arrecad.k00_tipojm",
            "arrecad.k00_numtot",
            "array_to_string(array_accum(arrecad.k00_numdig), ',') as k00_numdig",
            "sum({$sSqlCaseValor}) as k00_valor",
            "min(arrecad.k00_dtvenc) as menor_data_vencimento",
            "max(arrecad.k00_dtvenc) as maior_data_vencimento",
            "array_to_string(array_accum(arrecad.k00_numpar), ',') as colecao_numpar",
            "array_to_string(array_accum({$sSqlCaseValor}), ',') as colecao_valor",
            "(select array_to_string(array_accum(k00_matric || '|' ||k00_perc), ',') from arrematric where arrematric.k00_numpre = arrecad.k00_numpre) as colecao_matricula",
            "(select array_to_string(array_accum(k00_inscr || '|' ||k00_perc), ',') from arreinscr where arreinscr.k00_numpre = arrecad.k00_numpre) as colecao_inscricao",
            "(select array_to_string(array_accum(distinct arrenumcgm.k00_numcgm), ',') from arrenumcgm where arrenumcgm.k00_numpre = arrecad.k00_numpre) as colecao_cgm",
            "(select array_to_string(array_accum(q05_mes||'/'||q05_ano), ',')) as colecao_competencia",
            "array_to_string(array_accum(k00_dtvenc), ',') as data_vencimento",
            "(select dv05_obs from diversos where dv05_numpre = arrecad.k00_numpre) as obs",
            "(select dv05_exerc as exercicio from diversos where dv05_numpre = arrecad.k00_numpre) AS exercicio_divida"
          )
        );

        $subsql = "select ";

        $campos_sql = explode("#", $campos);
        $virgula = "";

        for($i = 0; $i < sizeof($campos_sql); $i++) {
            $subsql .= $virgula.$campos_sql[$i];
            $virgula = ",";
        }

        $subsql .= " from arretipo ";
        $subsql .= "      inner join arrecad on arrecad.k00_tipo = arretipo.k00_tipo          ";
        $subsql .= "                        and ($sWhereArrecad) ";
        $subsql .= "      inner join arreinstit on arreinstit.k00_numpre = arrecad.k00_numpre ";
        $subsql .= "                           and arreinstit.k00_instit = ".db_getsession('DB_instit');
        $subsql .= "      left join diversos on arrecad.k00_numpre = diversos.dv05_numpre";
        $subsql .= "      left join diverimportaold on dv05_coddiver = dv13_diversos and dv13_numpar = arrecad.k00_numpar";
        $subsql .= "      left join issvar on dv13_numpre = q05_numpre and q05_numpar = dv13_numpar";

        $subsql .= " group by arrecad.k00_numpre, arrecad.k00_numcgm, arrecad.k00_receit, ";
        $subsql .= " arrecad.k00_tipo, arrecad.k00_tipojm, arrecad.k00_numtot, arretipo.k03_tipo ";

        $sql  = " select k00_numpre,               ";
        $sql .= "        k00_numpar,               ";
        $sql .= "        k00_numcgm,               ";
        $sql .= "        colecao_dtoper,           ";
        $sql .= "        k00_receit,               ";
        $sql .= "        colecao_historico,        ";
        $sql .= "        k00_tipo,                 ";
        $sql .= "        k03_tipo,                 ";
        $sql .= "        k00_tipojm,               ";
        $sql .= "        k00_numtot,               ";
        $sql .= "        k00_numdig,               ";
        $sql .= "        k00_valor,                ";
        $sql .= "        menor_data_vencimento,    ";
        $sql .= "        maior_data_vencimento,    ";
        $sql .= "        colecao_numpar,           ";
        $sql .= "        colecao_valor,            ";
        $sql .= "        colecao_matricula,        ";
        $sql .= "        colecao_inscricao,        ";
        $sql .= "        colecao_cgm,              ";
        $sql .= "        colecao_competencia,      ";
        $sql .= "        data_vencimento,          ";
        $sql .= "        obs,                      ";
        $sql .= "        exercicio_divida          ";
        $sql .= "  from ({$subsql}) as x           ";
        $sql .= " where k00_valor > 0              ";
        $sql .= " order by k00_numpre              ";

        $rsBuscaDebitos = db_query($sql);

        if (!$rsBuscaDebitos) {
            throw new DBException("Não foi possível consultar os dados a serem importados.");
        }

        return $rsBuscaDebitos;
    }

    /**
     * Método responsável por processar a importação de diversos unificando os débitos
     * @param $sWhere
     * @return bool
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     */
    protected function importar($sWhere)
    {
        $iCodDiverImporta = $this->salvarDiverImporta(ImportacaoDiversos::PROCESSAMENTO_GERAL);
        $rsBuscaDebitos = $this->buscarDebitos($sWhere);
        $iTotalRegistros = pg_num_rows($rsBuscaDebitos);

        for ($iRowDebitos = 0; $iRowDebitos < $iTotalRegistros; $iRowDebitos++) {

            $stdDebitoOrigem = db_utils::fieldsMemory($rsBuscaDebitos, $iRowDebitos);
            $oProcedencia = $this->aReceitaProcedencia[$stdDebitoOrigem->k00_receit];

            $oDaoNumpref = new cl_numpref();
            $iNumPreNovo = $oDaoNumpref->sql_numpre();

            $aDatasOperacoes = explode(',', $stdDebitoOrigem->colecao_dtoper);

            /** Inclui Diversos */
            $oDaoDiversos = new cl_diversos();
            $oDaoDiversos->dv05_numcgm = $stdDebitoOrigem->k00_numcgm;
            $oDaoDiversos->dv05_dtinsc = date('Y-m-d', db_getsession('DB_datausu'));
            $oDaoDiversos->dv05_vlrhis = $stdDebitoOrigem->k00_valor;
            $oDaoDiversos->dv05_valor = $stdDebitoOrigem->k00_valor;
            $oDaoDiversos->dv05_procdiver = $oProcedencia->getProcedenciaDiverso();
            $oDaoDiversos->dv05_exerc = $this->getExercicio($aDatasOperacoes, $stdDebitoOrigem->k00_numpre);
            $oDaoDiversos->dv05_numpre = $iNumPreNovo;
            $oDaoDiversos->dv05_numtot = $stdDebitoOrigem->k00_numtot;
            $oDaoDiversos->dv05_privenc = $stdDebitoOrigem->menor_data_vencimento;

            if ($this->escolhaDataVencimento() && $this->lUnificarDebitos) {
                $oDaoDiversos->dv05_privenc = $this->getDataVencimento()->getDate();
            }

            $oDaoDiversos->dv05_provenc = $stdDebitoOrigem->maior_data_vencimento;
            $oDaoDiversos->dv05_diaprox = substr($stdDebitoOrigem->maior_data_vencimento, 8, 2);
            $oDaoDiversos->dv05_oper = $aDatasOperacoes[0];
            $oDaoDiversos->dv05_obs = "";

            if ($this->lUnificarDebitos) {

                $parcelas = explode(",", $stdDebitoOrigem->colecao_numpar);
                sort($parcelas);
                $oDaoDiversos->dv05_obs = "Este débito refere-se às parcelas " . implode(", ", $parcelas) . ". ";
            }

            $sObservacaoDebito = isset($stdDebitoOrigem->obs) ? $stdDebitoOrigem->obs : "";
            $sObservacaoGeral = isset($this->sObservacoes) ? $this->sObservacoes : "";
            $sDivisoria = $sObservacaoDebito != "" && $sObservacaoGeral != "" ? "-" : "";
            $sObservacao = trim("{$sObservacaoDebito} {$sDivisoria} {$sObservacaoGeral}");

            $oDaoDiversos->dv05_obs .= pg_escape_string($sObservacao);
            $oDaoDiversos->dv05_instit = db_getsession('DB_instit');
            $oDaoDiversos->incluir(null);

            if ($oDaoDiversos->erro_status == '0') {
                throw new DBException($oDaoDiversos->erro_msg);
            }

            $this->salvarIntegracaoInfiscSeNecessario($oDaoDiversos->dv05_coddiver, $stdDebitoOrigem->k00_tipo);

            $oDaoDiverImportaReg = new cl_diverimportareg();
            $oDaoDiverImportaReg->dv12_diversos = $oDaoDiversos->dv05_coddiver;
            $oDaoDiverImportaReg->dv12_diverimporta = $iCodDiverImporta;
            $oDaoDiverImportaReg->incluir(null);

            if ($oDaoDiverImportaReg->erro_status == '0') {
                throw new DBException($oDaoDiverImportaReg->erro_msg);
            }

            $aDatasOperacoes = explode(',', $stdDebitoOrigem->colecao_dtoper);
            $historicoCalculoProcedencia = $oProcedencia->getHistoricoCalculo();

            $aValoresParcelas = explode(',', $stdDebitoOrigem->colecao_valor);
            $aDatasVencimentos = explode(',', $stdDebitoOrigem->data_vencimento);
            $aHistoricos = explode(',', $stdDebitoOrigem->colecao_historico);
            $aNumdig = explode(',', $stdDebitoOrigem->k00_numdig);


            foreach (explode(',', $stdDebitoOrigem->colecao_numpar) as $indiceParcela => $codigoParcela) {

                if($aValoresParcelas[$indiceParcela] == 0){
                    continue;
                }

                $oDaoDiverImportaOld = new cl_diverimportaold();
                $oDaoDiverImportaOld->dv13_diversos = $oDaoDiversos->dv05_coddiver;
                $oDaoDiverImportaOld->dv13_numpre = $stdDebitoOrigem->k00_numpre;
                $oDaoDiverImportaOld->dv13_numpar = $codigoParcela;
                $oDaoDiverImportaOld->dv13_receita = $stdDebitoOrigem->k00_receit;
                $oDaoDiverImportaOld->incluir(null);
                if ($oDaoDiverImportaOld->erro_status === "0") {
                    throw new DBException("Ocorreu um erro ao vincular débito de diversos com suas origens.");
                }

                /* Exclui ARRECAD original e inclui na ARREOLD */
                $oDaoArrecad = new cl_arrecad();
                $oDaoArrecad->excluir_arrecad($stdDebitoOrigem->k00_numpre, $codigoParcela, true,
                  $stdDebitoOrigem->k00_receit);

                if (!$this->lUnificarDebitos) {

                    if(empty($historicoCalculoProcedencia)) {
                        $historicoCalculoProcedencia = $aHistoricos[$indiceParcela];
                    }

                    $daoArrecad = new cl_arrecad();
                    $daoArrecad->k00_numpre = $iNumPreNovo;
                    $daoArrecad->k00_numpar = $codigoParcela;
                    $daoArrecad->k00_numcgm = $stdDebitoOrigem->k00_numcgm;
                    $daoArrecad->k00_dtoper = $aDatasOperacoes[$indiceParcela];
                    $daoArrecad->k00_receit = $oProcedencia->getReceita();
                    $daoArrecad->k00_hist = $historicoCalculoProcedencia;
                    $daoArrecad->k00_valor = $aValoresParcelas[$indiceParcela];
                    $daoArrecad->k00_dtvenc = $aDatasVencimentos[$indiceParcela];
                    $daoArrecad->k00_numtot = $stdDebitoOrigem->k00_numtot;
                    $daoArrecad->k00_numdig = $aNumdig[$indiceParcela];
                    $daoArrecad->k00_tipo = $oProcedencia->getTipoDebito();
                    $daoArrecad->k00_tipojm = '0';
                    $daoArrecad->incluir();
                    if ($daoArrecad->erro_status === "0") {
                        throw new DBException("Não foi possível salvar os dados do novo débito.");
                    }

                }
            }

            if ($this->lUnificarDebitos) {

                if(empty($historicoCalculoProcedencia)) {
                    $historicoCalculoProcedencia = min(explode(',', $stdDebitoOrigem->colecao_historico));
                }

                $daoArrecad = new cl_arrecad();
                $daoArrecad->k00_numpre = $iNumPreNovo;
                $daoArrecad->k00_numpar = 1;
                $daoArrecad->k00_numcgm = $stdDebitoOrigem->k00_numcgm;
                $daoArrecad->k00_dtoper = $aDatasOperacoes[0];
                $daoArrecad->k00_receit = $oProcedencia->getReceita();
                $daoArrecad->k00_hist = $historicoCalculoProcedencia;
                $daoArrecad->k00_valor = $stdDebitoOrigem->k00_valor;
                $daoArrecad->k00_dtvenc = $this->maiorDataVencimento() ? $stdDebitoOrigem->maior_data_vencimento : $stdDebitoOrigem->menor_data_vencimento;

                if ($this->escolhaDataVencimento()) {
                    $daoArrecad->k00_dtvenc = $this->getDataVencimento()->getDate();
                }

                $daoArrecad->k00_numtot = 1;
                $daoArrecad->k00_numdig = 1;
                $daoArrecad->k00_tipo = $oProcedencia->getTipoDebito();
                $daoArrecad->k00_tipojm = '0';
                $daoArrecad->incluir();
                if ($daoArrecad->erro_status === "0") {
                    throw new DBException("Não foi possível salvar os dados do novo débito.");
                }
            }

            /* Salva Vinculo com Matricula */
            if (!empty($stdDebitoOrigem->colecao_matricula)) {

                foreach (explode(',', $stdDebitoOrigem->colecao_matricula) as $indice => $matriculas) {

                    list($codigoMatricula, $percentualMatricula) = explode('|', $matriculas);
                    $daoArreMatricMatricula = new cl_arrematric();
                    $daoArreMatricMatricula->k00_numpre = $iNumPreNovo;
                    $daoArreMatricMatricula->k00_matric = $codigoMatricula;
                    $daoArreMatricMatricula->k00_perc = $percentualMatricula;
                    $daoArreMatricMatricula->incluir($daoArreMatricMatricula->k00_numpre,
                      $daoArreMatricMatricula->k00_matric);
                    if ($daoArreMatricMatricula->erro_status === '0') {
                        throw new ParameterException("Não foi possível vincular o débito {$iNumPreNovo} na matricula {$codigoMatricula}.");
                    }
                }
            }

            /* Salva Vinculo com Inscrição */
            if (!empty($stdDebitoOrigem->colecao_inscricao)) {

                foreach (explode(',', $stdDebitoOrigem->colecao_inscricao) as $indice => $inscricoes) {

                    list($codigoInscricao, $percentualInscricao) = explode('|', $inscricoes);
                    $daoArreInscricao = new cl_arreinscr();
                    $daoArreInscricao->k00_numpre = $iNumPreNovo;
                    $daoArreInscricao->k00_inscr = $codigoInscricao;
                    $daoArreInscricao->k00_perc = $percentualInscricao;
                    $daoArreInscricao->incluir($daoArreInscricao->k00_numpre, $daoArreInscricao->k00_inscr);
                    if ($daoArreInscricao->erro_status === '0') {
                        throw new ParameterException("Não foi possível vincular o débito {$iNumPreNovo} na inscrição {$codigoInscricao}.");
                    }
                }
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws ParameterException
     */
    public function importacaoParcial()
    {
        if (empty($this->debitosProcessar)) {
            throw new ParameterException("Informe os débitos que devem ser processados.");
        }

        $aWhere = array();
        foreach ($this->debitosProcessar as $stdDebito) {

            $this->adicionarReceita($stdDebito->iReceita, null,
              new ProcedenciaDiversos($stdDebito->iCodigoProcedencia));
            $aWhere[] = "(arrecad.k00_numpre = {$stdDebito->iNumpre} and arrecad.k00_numpar = {$stdDebito->iNumpar} and arrecad.k00_receit = {$stdDebito->iReceita})";
        }

        return $this->importar(implode(' or ', $aWhere));
    }

    /**
     * @return bool
     */
    public function importacaoGeral()
    {
        $sReceitas = implode(',', array_keys($this->aReceitaProcedencia));

        $aWhere = array("arrecad.k00_receit in ({$sReceitas})");

        $sWhereTipo = "arrecad.k00_tipo = {$this->getTipoDebitoOrigem()}";

        if ( !empty($this->iListaDebitos) ) {
            $sWhereTipo = "arrecad.k00_numpre in (select distinct k61_numpre from listadeb where k61_codigo = $this->iListaDebitos)";
            $sWhereTipo .= "and arrecad.k00_numpar in (select distinct k61_numpar from listadeb where k61_codigo = $this->iListaDebitos and k61_numpre = arrecad.k00_numpre)";
        }

        $aWhere[] = $sWhereTipo;

        return $this->importar(implode(" and ", $aWhere));
    }

    /**
     * Função responsável por buscar o exercício do débito de origem para que a cobrança adm siga no mesmo exercício
     * @param array $datasOperacao
     * @param integer $numpre
     * @return bool|string
     * @throws DBException
     */
    public function getExercicio($datasOperacao, $numpre)
    {
        $exercicio = substr($datasOperacao[0], 0, 4);

        $sSqlTipo = "select arrecad.k00_tipo,
                            arretipo.k03_tipo
                       from arrecad inner join arretipo on arrecad.k00_tipo = arretipo.k00_tipo
                      where arrecad.k00_numpre = {$numpre} limit 1";

        $rsTipo = db_query($sSqlTipo);
        $oTipo = db_utils::fieldsMemory($rsTipo, 0);
        $iArretipo = $oTipo->k00_tipo;
        $iCadtipo =  $oTipo->k03_tipo;

        $daoIssvar = new cl_issvar();
        $sqlIss = $daoIssvar->sql_query(null, "min(q05_ano) as exercicio", null, "q05_numpre = $numpre");
        $rsIss = db_query($sqlIss);

        if (empty($rsIss)) {
            throw new DBException("Erro ao buscar ano da competência do ISSQN");
        }

        if (pg_num_rows($rsIss) != 0) {
            $issqn = db_utils::fieldsMemory($rsIss, 0);

            if (!empty($issqn->exercicio)){
                $exercicio = $issqn->exercicio;
            }
        }

        $daoDiversos = new cl_diversos();
        $sqlDiversos = $daoDiversos->sql_query_file(null, "min(dv05_exerc) as exercicio", null, "dv05_numpre = $numpre");
        $rsDiversos = db_query($sqlDiversos);

        if (empty($rsDiversos)) {
            throw new DBException("Erro ao buscar ano da competência do Diversos");
        }

        if (pg_num_rows($rsDiversos) != 0) {
            $diversos = db_utils::fieldsMemory($rsDiversos, 0);

            if (!empty($diversos->exercicio)){
                $exercicio = $diversos->exercicio;
            }
        }

        if ($iCadtipo == 2 && $iArretipo != 13) {
            $daoIsscalc = new cl_isscalc();
            $sqlIsscalc = $daoIsscalc->sql_query_file(null,
                                                      null,
                                                      null,
                                                      null,
                                                      null,
                                                      "min(q01_anousu) as exercicio",
                                                      null,
                                                      "q01_numpre = $numpre");
            $rsIsscalc = db_query($sqlIsscalc);

            if (empty($rsIsscalc)) {
                throw new DBException("Erro ao buscar ano da competência do Cálculo do ISS");
            }

            if (pg_num_rows($rsIsscalc) != 0) {
                $diversosCalc = db_utils::fieldsMemory($rsIsscalc, 0);

                if (!empty($diversosCalc->exercicio)){
                    $exercicio = $diversosCalc->exercicio;
                }
            }
        } elseif ($iCadtipo == 1 && $iArretipo != 13) {

            $sSqlIptu = "select j20_anousu as exercicio from iptunump where j20_numpre = {$numpre} limit 1";
            $rsIptu = db_query($sSqlIptu);
            if (pg_num_rows($rsIptu) != 0) {
                $oIptu = db_utils::fieldsMemory($rsIptu, 0);
                $exercicio = $oIptu->exercicio;
            }

            $sSqlIptu = "select j20_anousu as exercicio
                           from iptunump
                          where j20_numpre = {$numpre} limit 1";

            $rsIptu = db_query($sSqlIptu);
            if (pg_num_rows($rsIptu) != 0) {
                $oIptu = db_utils::fieldsMemory($rsIptu, 0);
                $exercicio = $oIptu->exercicio;
            }

            $sSqlIptuTaxa = "select j08_anousu as exercicio
                               from iptutaxanump inner join iptucadtaxaexe
                                 on iptutaxanump.j151_iptucadtaxaexe = iptucadtaxaexe.j08_iptucadtaxaexe
                              where iptutaxanump.j151_numpre = {$numpre} limit 1;";

            $rsIptuTaxa = db_query($sSqlIptuTaxa);
            if (pg_num_rows($rsIptuTaxa) != 0) {
                $oIptuTaxa = db_utils::fieldsMemory($rsIptuTaxa, 0);
                $exercicio = $oIptuTaxa->exercicio;
            }

        } elseif (($iCadtipo  == 0 || $iCadtipo == 20) && $iArretipo != 13) {
            $sSqlAguaCalc = "select x22_exerc as exercicio
                               from aguacalc
                              where x22_numpre = {$numpre} limit 1;";

            $rsAguaCalc = db_query($sSqlAguaCalc);
            if (pg_num_rows($rsAguaCalc) != 0) {
                $oAguaCalc = db_utils::fieldsMemory($rsAguaCalc, 0);
                $exercicio = $oAguaCalc->exercicio;
            }

        } elseif ($iCadtipo == 19 && $iArretipo != 13) {
            $sSqlVistoria = "select distinct extract(year from vistorias.y70_data) as exercicio
                               from arrecad inner join arretipo
                                 on arrecad.k00_tipo = arretipo.k00_tipo inner join vistorianumpre
                                 on arrecad.k00_numpre = vistorianumpre.y69_numpre inner join vistorias
                                 on vistorianumpre.y69_codvist = vistorias.y70_codvist
                              where vistorianumpre.y69_numpre = {$numpre} limit 1;";

            $rsVistoria = db_query($sSqlVistoria);
            if (pg_num_rows($rsVistoria) != 0) {
                $oVistoria = db_utils::fieldsMemory($rsVistoria, 0);
                $exercicio = $oVistoria->exercicio;
            }
        }

        return $exercicio;
    }

    /**
     * @throws DBException
     * @throws ReflectionException
     */
    private function salvarIntegracaoInfiscSeNecessario($codigoDiversos, $tipoDebito)
    {
        if (!IntegracaoInfiscTipoDebito::deveIntegrar(intval($tipoDebito))) {
            return;
        }

        $dao_inscricao_debito_infisc_integra_debitos_mov = new cl_inscricao_debito_infisc_integra_debitos_mov();
        $dao_inscricao_debito_infisc_integra_debitos_mov->q197_diversos = $codigoDiversos;
        $dao_inscricao_debito_infisc_integra_debitos_mov->q197_status = IntegracaoInfiscStatusProcessamento::PENDENTE;
        $dao_inscricao_debito_infisc_integra_debitos_mov->incluir();

        if ($dao_inscricao_debito_infisc_integra_debitos_mov->erro_status === '0') {
            throw new DBException("Não foi possível incluir o diversos para integração com a INFISC.");
        }
    }
}
