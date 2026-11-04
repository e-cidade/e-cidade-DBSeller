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

require_once modification("model/configuracao/InstituicaoRepository.model.php");
 
/**
 * Planilha de Arrecadacao
 * Eh um conjunto de receita do municipio para fins de arrecadacao.
 * @package caixa
 * @author Andrio Costa <andrio.costa@dbseller.com.br>
 * @version $Revision: 1.17 $
 */
class PlanilhaArrecadacao
{

    /**
     * Codigo da planilha
     * @var integer
     */
    private $iCodigo;

    /**
     * Data de inclusao da planilha
     * @var DBDate
     */
    private $oDataCriacao;

    /**
     * Data de autencicacao da planilha
     * @var DBDate
     */
    private $oDataAutenticacao;

    /**
     * Instituicao a qual a planilha pertence / foi criada
     * @var Instituicao
     */
    private $oInstituicao;

    /**
     * Colecao de Receitas pertencentes a planilha
     * @var array de ReceitaPlanilha
     */
    private $aReceitaPlanilha = array();

    /**
     * @var string
     */
    private $sProcessoAdministrativo;

    /**
     * Contrutor da classe
     * Se recebido um codigo de planilha, carrega as receitas da planilha
     * @param integer $iCodigo da planilha
     * @throws ParameterException
     */
    public function __construct($iCodigo = null)
    {

        if (!empty($iCodigo)) {
            $oDaoPlanilha = new cl_placaixa();
            $sSqlPlanilha = $oDaoPlanilha->sql_query_processo($iCodigo);
            $rsPlanilha = $oDaoPlanilha->sql_record($sSqlPlanilha);

            if ($rsPlanilha && $oDaoPlanilha->numrows > 0) {
                $oPlanilha = db_utils::fieldsMemory($rsPlanilha, 0);
                $this->iCodigo = $oPlanilha->k80_codpla;
                $this->oInstituicao = InstituicaoRepository::getInstituicaoByCodigo($oPlanilha->k80_instit);
                $this->oDataCriacao = new DBDate($oPlanilha->k80_data);

                if (!empty($oPlanilha->k80_dtaut)) {
                    $this->oDataAutenticacao = new DBDate($oPlanilha->k80_dtaut);
                }

                if (!empty($oPlanilha->k144_numeroprocesso)) {
                    $this->sProcessoAdministrativo = trim($oPlanilha->k144_numeroprocesso);
                }
            }
        }
        return $this;
    }

    /**
     * Retorna o codigo da planilha
     * @return integer
     */
    public function getCodigo()
    {
        return $this->iCodigo;
    }


    /**
     * Retorna a Instituicao a qual planilha pertence
     * @return Instituicao
     */
    public function getInstituicao()
    {
        return $this->oInstituicao;
    }

    /**
     * define a instituicao a qual a planilha esta sendo criada
     * @param Instituicao oInstituicao
     */
    public function setInstituicao(Instituicao $oInstituicao)
    {
        $this->oInstituicao = $oInstituicao;
    }

    /**
     * Retorna uma instancia de DBData com a data da autenticacao
     * @return DBDate
     */
    public function getDataCriacao()
    {
        return $this->oDataCriacao;
    }

    /**
     * define a data da criacao da planilha
     * a string recebida deve estar no seguinte formato:
     *      dia/mes/ano  --> 21/03/2012
     *      ano-mes-dia  --> 2012-03-21
     * @param string $sDataCriacao
     * @throws ParameterException
     */
    public function setDataCriacao($sDataCriacao)
    {
        $this->oDataCriacao = new DBDate($sDataCriacao);
    }

    /**
     * Retorna uma instancia de DBData com a data da autenticacao
     * @return DBDate
     */
    public function getDataAutenticacao()
    {
        return $this->oDataAutenticacao;
    }


    /**
     * @param $sDataAutenticacao
     * @throws ParameterException
     */
    public function setDataAutenticacao($sDataAutenticacao)
    {
        $this->oDataAutenticacao = new DBDate($sDataAutenticacao);
    }

    /**
     * Retorna uma colecao de receitas vinculadas a planilha
     * @return ReceitaPlanilha[]
     * @throws BusinessException
     */
    public function getReceitasPlanilha()
    {
        if (!empty($this->iCodigo) && count($this->aReceitaPlanilha) == 0) {
            $oDaoPlanilhaReceita = new cl_placaixarec;
            $sWhere = " k81_codpla = {$this->iCodigo}";
            $sSqlPlanilhaReceita = $oDaoPlanilhaReceita->sql_query_file(null, " k81_seqpla ", "k81_seqpla", $sWhere);
            $rsPlanilhaReceita = $oDaoPlanilhaReceita->sql_record($sSqlPlanilhaReceita);
            $iNumeroRegistro = $oDaoPlanilhaReceita->numrows;

            if ($rsPlanilhaReceita && $iNumeroRegistro > 0) {
                for ($i = 0; $i < $iNumeroRegistro; $i++) {
                    $oReceitaPlanilha = new ReceitaPlanilha(db_utils::fieldsMemory($rsPlanilhaReceita, $i)->k81_seqpla);
                    $this->aReceitaPlanilha[] = $oReceitaPlanilha;
                }
            }
        }
        return $this->aReceitaPlanilha;
    }


    /**
     * Adiciona uma instacia de ReceitaPlanilha a Planilha
     * @param ReceitaPlanilha $oReceitaPlanilha
     */
    public function adicionarReceitaPlanilha(ReceitaPlanilha $oReceitaPlanilha)
    {

        $this->aReceitaPlanilha[] = $oReceitaPlanilha;
    }

    /**
     * Persiste a planilha no banco
     * @return boolean
     * @throws BusinessException em caso de erro
     */
    public function salvar()
    {

        if (!db_utils::inTransaction()) {
            throw new BusinessException("Sem transação ativa.");
        }

        $oDaoPlanilha = new cl_placaixa;

        if (empty($this->iCodigo)) {
            $oDaoPlanilha->k80_instit = $this->oInstituicao->getSequencial();
            $oDaoPlanilha->k80_data = $this->oDataCriacao->getDate();
            $oDaoPlanilha->incluir(null);
            $this->iCodigo = $oDaoPlanilha->k80_codpla;

            $placaixaUsu = new cl_placaixausu;
            $placaixaUsu->k214_sequencial = null;
            $placaixaUsu->k214_placaixa = $this->iCodigo;
            $placaixaUsu->k214_db_usuario = db_getsession('DB_id_usuario');
            $placaixaUsu->k214_db_depart = db_getsession('DB_coddepto');
            $placaixaUsu->incluir(null);
        } else {
            $oDaoPlanilha->k80_codpla = $this->iCodigo;
            $oDaoPlanilha->alterar($this->iCodigo);
        }

        if ($oDaoPlanilha->erro_status == 0) {
            throw new BusinessException($oDaoPlanilha->erro_msg);
        }


        $this->salvarProcessoAdministrativo();

        if (count($this->aReceitaPlanilha) > 0) {
            foreach ($this->aReceitaPlanilha as $oReceitaPlanilha) {
                $oReceitaPlanilha->salvar($this->iCodigo);
            }
        }

        return true;
    }

    /**
     * retorna os slips gerados para as receitas extras de uma planilha
     * existe a placaixarecslip que ao que vimos para todas receitas da planilha idependente de extra ou nao
     * ele gera um slip com receitas agrupadas
     * criamos essa estrutura diferente para guardar somente os que são de extra
     */
    public function getSlipsAutomaticosDasExtras($situacao = null)
    {

        $oDao = new cl_slipplacaixarec;
        $aWhere = array();
        $aWhere[] = "k81_codpla = {$this->getCodigo()}";
        if (!empty($situacao)) {
            $aWhere[] = "k17_situacao = {$situacao}";
        }
        $sWhere = implode(" and ", $aWhere);
        $sql = $oDao->sql_query(null, "k207_slip, placaixarec.*", "k207_slip", $sWhere);
        $rs = $oDao->sql_record($sql);
        $aSlips = array();
        if ($oDao->numrows > 0) {
            for ($i = 0; $i < $oDao->numrows; $i++) {
                $aSlips[] = db_utils::fieldsMemory($rs, $i)->k207_slip;
            }
        }
        return $aSlips;
    }


    /**
     * @return bool
     * @throws BusinessException
     */
    private function salvarProcessoAdministrativo()
    {

        $this->excluirProcessoAdministrativo();
        $this->sProcessoAdministrativo = trim($this->sProcessoAdministrativo);
        if (empty($this->sProcessoAdministrativo)) {
            return false;
        }

        $oDaoPlanilhaProcesso = new cl_placaixaprocesso();
        $oDaoPlanilhaProcesso->k144_sequencial = null;
        $oDaoPlanilhaProcesso->k144_placaixa = $this->getCodigo();
        $oDaoPlanilhaProcesso->k144_numeroprocesso = $this->sProcessoAdministrativo;
        $oDaoPlanilhaProcesso->incluir(null);
        if ($oDaoPlanilhaProcesso->erro_status == "0") {
            throw new BusinessException(_M("financeiro.caixa.PlanilhaArrecadacao.vincular_processo_administrativo"));
        }
        return true;
    }

    /**
     * Autentica uma planilha de arrecadação
     * @return boolean
     * @throws BusinessException
     * @throws ParameterException
     */
    public function autenticar()
    {

        $oAutenticacaoPlanilha = new AutenticacaoPlanilha($this);
        $oAutenticacaoPlanilha->autenticar();
        return true;
    }

    /**
     * Estorna uma planilha autenticada.
     * @return boolean
     * @throws BusinessException
     * @throws ParameterException
     */
    public function estornar()
    {

        $oAutenticacaoPlanilha = new AutenticacaoPlanilha($this);
        $oAutenticacaoPlanilha->estornar();
        return true;
    }


    /**
     * Método que verifica se as receitas da planilha possuem lancamento contabil
     * @return boolean
     */
    public function existeLancamentoContabil()
    {

        $oDaoCorPlaCaixa = new cl_corplacaixa;
        $sWherePlanilha = "placaixarec.k81_codpla = {$this->getCodigo()}";
        $sSqlBuscaAutenticacao = $oDaoCorPlaCaixa->sql_query_planilha_receita(
            null,
            null,
            null,
            "*",
            null,
            $sWherePlanilha
        );
        $rsBuscaAutenticacao = $oDaoCorPlaCaixa->sql_record($sSqlBuscaAutenticacao);
        if ($oDaoCorPlaCaixa->numrows == 0) {
            return false;
        }
        return true;
    }

    /**
     * metodo de exclusão de planilha não autenticada
     * @return boolean
     * @throws DBException|BusinessException
     */
    public function excluir()
    {

        if ($this->existeLancamentoContabil()) {
            throw new BusinessException(_M("financeiro.caixa.PlanilhaArrecadacao.possui_lancamento_contabil"));
        }

        $this->excluirReceitas();
        $this->excluirProcessoAdministrativo();

        $oDaoPlacaixa = new cl_placaixa;
        $clPlanilhaDepartamento = new cl_placaixausu();
        $clPlanilhaDepartamento->excluir(null, "k214_placaixa = {$this->getCodigo()}");
        $oDaoPlacaixa->excluir($this->getCodigo());
        if ($oDaoPlacaixa->erro_status == '0' || $oDaoPlacaixa->erro_status == 0) {
            throw new DBException("Não foi possível excluir a planilha.");
        }
        return true;
    }

    /**
     * Exclui as receitas vinculadas na planilha
     * @return true
     * @throws BusinessException
     */
    public function excluirReceitas()
    {

        $aReceitas = $this->getReceitasPlanilha();
        if (count($aReceitas) > 0) {
            foreach ($aReceitas as $oReceitaPlanilha) {
                $oReceitaPlanilha->excluir();
            }
        }
        return true;
    }

    /**
     * @return bool
     * @throws BusinessException
     */
    private function excluirProcessoAdministrativo()
    {

        $oDaoPlanilhaProcesso = new cl_placaixaprocesso();
        $oDaoPlanilhaProcesso->excluir(null, "k144_placaixa = {$this->getCodigo()}");
        if ($oDaoPlanilhaProcesso->erro_status == "0") {
            throw new BusinessException(_M("financeiro.caixa.PlanilhaArrecadacao.exclusao_processo_administrativo"));
        }
        return true;
    }

    /**
     * @param $sProcesso
     */
    public function setProcessoAdministrativo($sProcesso)
    {
        $this->sProcessoAdministrativo = $sProcesso;
    }

    /**
     * @return string
     */
    public function getProcessoAdministrativo()
    {
        return $this->sProcessoAdministrativo;
    }

    public function validaReceitasInativas()
    {
        $ano = db_getsession('DB_anousu');
        $data = date('Y-m-d', db_getsession("DB_datausu"));

        $sql = "
            select distinct o57_fonte
              from placaixarec
              join tabrec on k02_codigo = k81_receita
              join taborc ON tabrec.k02_codigo = taborc.k02_codigo
                   and taborc.k02_anousu = {$ano}
              join orcreceita on (o70_anousu, o70_codrec) = (k02_anousu, k02_codrec)
              join orcfontes on (o57_codfon, o57_anousu) = (o70_codfon, o70_anousu)
            where k81_codpla = {$this->iCodigo}
              and (k02_limite is not null and k02_limite <= '{$data}')
        ";

        $rs = db_query($sql);
        return db_utils::makeCollectionFromRecord($rs, function ($data) {
            return $data->o57_fonte;
        });
    }

    public function validaContasExtas()
    {
        $problemas = [];
        if (self::devePrepararSlipsParaCoberturaExtraOrcamentaria()) {
            $receitas = $this->getReceitasPlanilha();
            foreach ($receitas as $receita) {
                if (ReceitaExtraOrcamentaria::isExtra($receita->getTipoReceita())) {
                    $contaTesouraria = $receita->getContaTesouraria();
                    $contaDebito = $contaTesouraria->getContaExtra();

                    if (empty($contaDebito)) {
                        $problemas[$contaTesouraria->getCodigoConta()] = $contaTesouraria->getCodigoConta();
                    }
                }
            }
        }

        if (!empty($problemas)) {
            throw new Exception(sprintf(
                'Conta bancária sem vínculo com conta extra: %s',
                implode(', ', $problemas)
            ));
        }
        return true;
    }

    /**
     * Validação se o sistema deve preparar os slips para cobertura Extra-Orçamentária
     * Quando o cliente POSSUI Apropriação de retenção ativa e os parâmetros abaixo estiverem configurados como
     * demonstrado:
     * - k29_gerarslipautomaticoreceitaretencao: 0 (Não gera Slips automaticamente)
     * - k29_utilizarcontaextra: t (SIM)
     * Devemos prepara os dados para gerar o slip de transferência para cobertura extra orçamentária
     * @return bool
     */
    public static function devePrepararSlipsParaCoberturaExtraOrcamentaria()
    {
        $parametros = slip::getParametros();
        if (APROPRIACAO_RETENCAO &&
            $parametros->k29_gerarslipautomaticoreceitaretencao == 0 &&
            $parametros->k29_utilizarcontaextra == 't') {
            return true;
        }

        return false;
    }
}
