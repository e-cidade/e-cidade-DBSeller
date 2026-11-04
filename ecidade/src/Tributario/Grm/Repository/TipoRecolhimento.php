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

namespace ECidade\Tributario\Grm\Repository;

use db_utils;
use ECidade\Financeiro\Tesouraria\Repository\Receita;
use ECidade\Tributario\Grm\RecolhimentoUnidadeGestora;
use \ECidade\Tributario\Grm\TipoRecolhimento as TipoRecolhimentoModel;
use \ECidade\Tributario\Grm\UnidadeGestora as UnidadeGestoraModel;
use ECidade\Tributario\Arrecadacao\Repository\TaxasLancadasRepository;

class TipoRecolhimento
{

    protected static $itens = array();
  /**
   * Retorna os dados do tipo de recolhimento por codigo
   * @param $codigo
   * @return \ECidade\Tributario\Grm\TipoRecolhimento
   * @throws \ParameterException
   * @internal param $iCodigo
   *
   */
    public function getTipoRecolhimento($codigo)
    {

        if (empty($codigo)) {
            throw  new \ParameterException('Código do tipo recolhimento não informado.');
        }
        if (!empty(self::$itens[$codigo])) {
            return self::$itens[$codigo];
        }
        $oDaoTipoRecolhimento = new \cl_tiporecolhimento();
        $oDados               = $oDaoTipoRecolhimento->findBydId($codigo);
        if (empty($oDados)) {
            return null;
        }
        $oTipoRecolhimento    = $this->make($oDados);
        self::$itens[$codigo] = $oTipoRecolhimento ;
        return $oTipoRecolhimento;
    }

  /**
   * Retorna todos os tipos de Recolhimento de uma Unidade Gestora
   * @param \Ecidade\Tributario\Grm\UnidadeGestora $unidadeGestora
   * @return \Ecidade\Tributario\Grm\RecolhimentoUnidadeGestora[]
   * @throws \DBException
   */
    public function getTiposRecolhimentoDaUnidadeGestora(UnidadeGestoraModel $unidadeGestora)
    {

        $oDaoRecolhimentoUnidaeGestora = new \cl_unidadegestoratiporecolhimento();
        $sWhere = "K173_unidadegestora = {$unidadeGestora->getCodigo()}";
        $sSqlRecolhimentos = $oDaoRecolhimentoUnidaeGestora->sql_query(null, "*", 'k173_tiporecolhimento', $sWhere);
        $rsRecolhimentos = db_query($sSqlRecolhimentos);
        if (!$rsRecolhimentos) {
            throw new \DBException('Erro ao pesquisar recolhimentos vinculados a unidade gestora');
        }
        $aRetorno     = array();
        $iTotalLinhas = pg_num_rows($rsRecolhimentos);
        for ($iRecolhimento = 0; $iRecolhimento < $iTotalLinhas; $iRecolhimento++) {
            $oDadosRecolhimento = \db_utils::fieldsMemory($rsRecolhimentos, $iRecolhimento);
            $oRecolhimento      = $this->make($oDadosRecolhimento);
            $oReceita           = Receita::getById($oDadosRecolhimento->k173_receita);
            $oRecolhimentoUnidade = new RecolhimentoUnidadeGestora();
            $oRecolhimentoUnidade->setReceita($oReceita);
            $oRecolhimentoUnidade->setTipoRecolhimento($oRecolhimento);
            $oRecolhimentoUnidade->isTaxa = false;
            $aRetorno[]         = $oRecolhimentoUnidade;
        }

        $taxasLancadasRepository = TaxasLancadasRepository::getInstance();
        $rsTaxasUg = $taxasLancadasRepository->getTaxasUgGrm($unidadeGestora->getCodigo());

        if (!$rsTaxasUg) {
            throw new \DBException('Erro ao pesquisar as taxas vinculadas a unidade gestora');
        }

        $aTaxasUg = db_utils::getColectionByRecord($rsTaxasUg);

        $aTaxas = array_map(function ($oTaxa) {
            return $oTaxa->ar44_sequencial;
        }, $aTaxasUg);

        foreach ($aTaxas as $taxa) {
            $oTaxa = $taxasLancadasRepository->getTaxa($taxa);

            $oRecolhimento        = $this->make($oTaxa);
            $oReceita             = Receita::getById($oTaxa->ar44_receita);
            $oRecolhimentoUnidade = new RecolhimentoUnidadeGestora();
            $oRecolhimentoUnidade->setReceita($oReceita);
            $oRecolhimentoUnidade->setTipoRecolhimento($oRecolhimento);
            $oRecolhimentoUnidade->isTaxa = true;
            $oRecolhimentoUnidade->isFixa = ($oTaxa->ar44_tipo == 0);
            $oRecolhimentoUnidade->valorFixo = $oTaxa->i02_valor;
            $oRecolhimentoUnidade->dataVencimento = date("d/m/Y", strtotime("+{$oTaxa->ar44_diasvencimento} days"));

            $aRetorno[]           = $oRecolhimentoUnidade;
        }

        return $aRetorno;
    }

    /**
     * Método público para acessar o make dos tipos de recolhimento
     * que já tinha sido adaptado para a taxas quando filtrado por unidade gestora
     * @param object $oTaxas
     * @return \ECidade\Tributario\Grm\TipoRecolhimento
     */

    public function makeTaxas($oTaxas)
    {
        return $this->make($oTaxas);
    }

  /**
   * @param $oDados
   * @return \ECidade\Tributario\Grm\TipoRecolhimento
   */
    private function make($oDados)
    {

        $oTipoRecolhimento = new TipoRecolhimentoModel();

        $oTipoRecolhimento->setCodigo(
            (isset($oDados->k172_sequencial) ? $oDados->k172_sequencial
                : (isset($oDados->ar44_sequencial) ? $oDados->ar44_sequencial : ""))
        );

        $oTipoRecolhimento->setCodigoRecolhimento(
            (isset($oDados->k172_codigorecolhimento) ? $oDados->k172_codigorecolhimento
                : (isset($oDados->ar44_sequencial) ? $oDados->ar44_sequencial : ""))
        );

        $oTipoRecolhimento->setNome(
            (isset($oDados->k172_nome) ? $oDados->k172_nome
                : (isset($oDados->ar44_descricao) ? $oDados->ar44_descricao : ""))
        );

        $oTipoRecolhimento->setTituloReduzido(
            (isset($oDados->k172_tituloreduzido) ? $oDados->k172_tituloreduzido
                : (isset($oDados->ar44_descricao) ? $oDados->ar44_descricao : ""))
        );

        $oTipoRecolhimento->setEspecieIngresso(
            (isset($oDados->k172_especieingresso) ? $oDados->k172_especieingresso : 1)
        );

        $oTipoRecolhimento->setTipoPessoa((isset($oDados->k172_tipopessoa) ? $oDados->k172_tipopessoa : 3));

        $oTipoRecolhimento->setObrigaNumeroReferencia(
            (isset($oDados->k172_obriganumeroreferencia) ? $oDados->k172_obriganumeroreferencia == 't' : false)
        );

        $oTipoRecolhimento->setInformaDesconto((isset($oDados->k172_desconto) ? $oDados->k172_desconto == 't' : false));
        $oTipoRecolhimento->setInformaMulta((isset($oDados->k172_multa) ? $oDados->k172_multa == 't' : false));
        $oTipoRecolhimento->setInformaJuros((isset($oDados->k172_juros) ? $oDados->k172_juros == 't' : false));
        $oTipoRecolhimento->setInformaOutrosAcrescimos(
            (isset($oDados->k172_outrosacrescimos) ? $oDados->k172_outrosacrescimos == 't' : false)
        );

        $oTipoRecolhimento->setInformaOutrasDeducoes(
            (isset($oDados->k172_outrasdeducoes) ? $oDados->k172_outrasdeducoes == 't' : false)
        );

        $oTipoRecolhimento->setInstrucoes((isset($oDados->k172_instrucoes) ? $oDados->k172_instrucoes : ""));
        $oTipoRecolhimento->setData($oDados);
        return $oTipoRecolhimento;
    }

  /**
   * Persiste o tipo Recolhimento.
   * @param \ECidade\Tributario\Grm\TipoRecolhimento $tipoRecolhimento
   * @throws \BusinessException
   */
    public function persist(TipoRecolhimentoModel $tipoRecolhimento)
    {

        if ($tipoRecolhimento->getCodigoRecolhimento() == null) {
            throw new \BusinessException('Campo Código Recolhimento não pode ser vazio!');
        }

        if ($tipoRecolhimento->getNome()==null) {
            throw new \BusinessException('Campo Nome não pode ser vazio!');
        }

        if ($tipoRecolhimento->getTipoPessoa()==null) {
            throw new \BusinessException('Campo Tipo Recolhedor não pode ser vazio!');
        }

        if ($tipoRecolhimento->obrigaNumeroReferencia()===null) {
            throw new \BusinessException('Informar Número de Referência!');
        }

        $oDaoTipoRecolhimento  = new \cl_tiporecolhimento();
        $oDaoTipoRecolhimento->k172_sequencial = $tipoRecolhimento->getCodigo();
        $oDaoTipoRecolhimento->k172_nome = $tipoRecolhimento->getNome();
        $oDaoTipoRecolhimento->k172_codigorecolhimento = $tipoRecolhimento->getCodigoRecolhimento();
        $oDaoTipoRecolhimento->k172_tipopessoa = $tipoRecolhimento->getTipoPessoa();
        $oDaoTipoRecolhimento->k172_obriganumeroreferencia =
        $tipoRecolhimento->obrigaNumeroReferencia() ? 'true' : 'false';
        $oDaoTipoRecolhimento->k172_tituloreduzido = $tipoRecolhimento->getTituloReduzido();
        $oDaoTipoRecolhimento->k172_instrucoes = $tipoRecolhimento->getInstrucoes();
        $oDaoTipoRecolhimento->k172_especieingresso = $tipoRecolhimento->getEspecieIngresso();
        $oDaoTipoRecolhimento->k172_multa = $tipoRecolhimento->informaMulta() ? 'true' : 'false';
        $oDaoTipoRecolhimento->k172_desconto = $tipoRecolhimento->informaDesconto() ? 'true' : 'false';
        $oDaoTipoRecolhimento->k172_juros = $tipoRecolhimento->informaJuros() ? 'true' : 'false';
        $oDaoTipoRecolhimento->k172_outrosacrescimos = $tipoRecolhimento->informaOutrosAcrescimos() ? 'true' : 'false';
        $oDaoTipoRecolhimento->k172_outrasdeducoes = $tipoRecolhimento->informaOutrasDeducoes() ? 'true' : 'false';
        $oDaoTipoRecolhimento->k172_workflow = 'null';


        if ($tipoRecolhimento->getWorkflow() != '') {
            $oDaoTipoRecolhimento->k172_workflow = $tipoRecolhimento->getWorkflow()->getCodigo();
        }

        $codigoTipoRecolhimento = $tipoRecolhimento->getCodigo();
        if (empty($codigoTipoRecolhimento)) {
            $oDaoTipoRecolhimento->incluir(null);
            $tipoRecolhimento->setCodigo($oDaoTipoRecolhimento->k172_sequencial);
        } else {
            $oDaoTipoRecolhimento->alterar($tipoRecolhimento->getCodigo());
        }


        if ($tipoRecolhimento->getAtributoDinamico() != null) {
            $oDaoTipoRecolhimentoAtributo                         = new \cl_tiporecolhimentoatributosdinamicos();
            $oDaoTipoRecolhimentoAtributo->excluir(null, "k176_tiporecolhimento = {$tipoRecolhimento->getCodigo()}");

            if ($oDaoTipoRecolhimentoAtributo->erro_status == 0) {
                throw new \BusinessException("Erro ao excluir atributos dinâmicos 
                ao tipo de recolhimento {$tipoRecolhimento->getCodigoRecolhimento()}.");
            }

            $oDaoTipoRecolhimentoAtributo->k176_tiporecolhimento  = $tipoRecolhimento->getCodigo();
            $oDaoTipoRecolhimentoAtributo->k176_db_cadattdinamico =
            $tipoRecolhimento->getAtributoDinamico()->getCodigo();
            $oDaoTipoRecolhimentoAtributo->incluir();

            if ($oDaoTipoRecolhimentoAtributo->erro_status == 0) {
                throw new \BusinessException("Erro ao vincular atributos dinâmicos ao 
                tipo de recolhimento {$tipoRecolhimento->getCodigoRecolhimento()}.");
            }
        }

        if ($oDaoTipoRecolhimento->erro_status == 0) {
            throw  new \BusinessException($oDaoTipoRecolhimento->erro_msg);
        }
    }

  /**
   * Remove o tipo de recolhimento
   * @param \ECidade\Tributario\Grm\TipoRecolhimento $tipoRecolhimento
   * @throws \BusinessException
   */
    public function remove(TipoRecolhimentoModel $tipoRecolhimento)
    {

        $oRecibo = new Recibo();
        $dadosRecibo = $oRecibo->getRecibosDoTipoDeRecolhimento($tipoRecolhimento);

        $boVinculado = false;
        $arVinculado = array();
        if (count($dadosRecibo)) {
            $boVinculado = true;
            $arVinculado[] = 'Recibo';
        }

        $oUnidadeGestora = new UnidadeGestora();
        $dadosUnidadeGestora = $oUnidadeGestora->getUnidadeGestoraTipoDeRecolhimento($tipoRecolhimento);
        if (count($dadosUnidadeGestora)) {
            $boVinculado = true;
            $arVinculado[] = 'Unidade Gestora';
        }

        if ($boVinculado) {
            $msgVinculado = implode(', ', $arVinculado);
            throw new \BusinessException("Erro ao remover Tipo de Recolhimento. 
            Há os seguinte(s) vínculo(s): ".$msgVinculado.".");
        }

        $oDaoTipoRecolhimentoAtributos = new \cl_tiporecolhimentoatributosdinamicos();
        $oDaoTipoRecolhimentoAtributos->excluir(null, "k176_tiporecolhimento=".$tipoRecolhimento->getCodigo());
        if ($oDaoTipoRecolhimentoAtributos->erro_status == 0) {
            throw  new \BusinessException($oDaoTipoRecolhimentoAtributos->erro_msg);
        }
        $oDaoTipoRecolhimento  = new \cl_tiporecolhimento();
        $oDaoTipoRecolhimento->excluir($tipoRecolhimento->getCodigo());
        if ($oDaoTipoRecolhimento->erro_status == 0) {
            throw  new \BusinessException($oDaoTipoRecolhimento->erro_msg);
        }
    }

  /**
   * @param \ECidade\Tributario\Grm\TipoRecolhimento                                                 $tipoRecolhimento
   * @param \ECidade\Tributario\Grm\Repository\UnidadeGestora|\ECidade\Tributario\Grm\UnidadeGestora $unidadeGestora
   * @throws \BusinessException
   */
    public function removerRecolhimentoDaUnidade(
        TipoRecolhimentoModel $tipoRecolhimento,
        UnidadeGestoraModel $unidadeGestora
    ) {

        $oDaoRecolhimentoUnidade = new \cl_unidadegestoratiporecolhimento();
        $where = "k173_unidadegestora = {$unidadeGestora->getCodigo()}";
        $where .= " and k173_tiporecolhimento = {$tipoRecolhimento->getCodigo()}";
        $oDaoRecolhimentoUnidade->excluir(null, $where);
        if ($oDaoRecolhimentoUnidade->erro_status == 0) {
            throw new \BusinessException("Erro ao remover recolhimento 
            {$tipoRecolhimento->getCodigoRecolhimento()} da Unidade 
            {$unidadeGestora->getNome()}");
        }
    }

  /**
   * Retorno o cadastro de atributo dinamico do recolhimento
   * @param \ECidade\Tributario\Grm\TipoRecolhimento $tipoRecolhimento
   * @return \DBAttDinamico
   * @throws \BusinessException
   */
    public static function getAtributosDoRecolhimento(TipoRecolhimentoModel $tipoRecolhimento)
    {

        $oDaoTipoRecolhimento = new \cl_tiporecolhimentoatributosdinamicos();
        $sSqlAtributoDinamico = $oDaoTipoRecolhimento->sql_query_file(
            null,
            "k176_db_cadattdinamico",
            null,
            "k176_tiporecolhimento = {$tipoRecolhimento->getCodigo()}"
        );
        $rsAtributos         = db_query($sSqlAtributoDinamico);
        if (!$rsAtributos) {
            throw new \BusinessException('Erro ao consultar dados dos atributos dinamicos do tipo de recolhimento '
            .$tipoRecolhimento->getCodigoRecolhimento());
        }
        $iTotalLinhas = pg_num_rows($rsAtributos);
        if ($iTotalLinhas === 0) {
            return null;
        }

        $oAtributoDinamico = new \DBAttDinamico(\db_utils::fieldsMemory($rsAtributos, 0)->k176_db_cadattdinamico);
        return $oAtributoDinamico;
    }
}
