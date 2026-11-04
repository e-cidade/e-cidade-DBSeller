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

class RelatorioCompensacoes
{

	/** @var PDFDocument */
	private $oPdf;

	/** @var integer */
	private $iLarguraPagina;

	/** @var integer */
	private $iAlturaLinha;

	/* @var DBDate */
	private $oDataInicial;

	/* @var DBDate */
	private $oDataFinal;

	/* @var integer */
	private $iCgm;

	/* @var integer */
	private $iTipoDebito;

	/**
	 * @param int $iCgm
	 */
	public function setCgm($iCgm)
	{
		$this->iCgm = $iCgm;
	}

	/**
	 * @return int
	 */
	public function getCgm()
	{
		return $this->iCgm;
	}

	/**
	 * @param int $iTipoDebito
	 */
	public function setTipoDebito($iTipoDebito)
	{
		$this->iTipoDebito = $iTipoDebito;
	}

	/**
	 * @return int
	 */
	public function getTipoDebito()
	{
		return $this->iTipoDebito;
	}

	/**
	 * @param DBDate $oDataInicial
	 */
	public function setDataInicial(DBDate $oDataInicial)
	{
		$this->oDataInicial = $oDataInicial;
	}

	/**
	 * @return DBDate
	 */
	public function getDataInicial()
	{
		return $this->oDataInicial;
	}

	/**
	 * @param DBDate $oDataFinal
	 */
	public function setDataFinal(DBDate $oDataFinal)
	{
		$this->oDataFinal = $oDataFinal;
	}

	/**
	 * @return DBDate
	 */
	public function getDataFinal()
	{
		return $this->oDataFinal;
	}

	private function processarDados()
	{

		$rsDados = $this->getDados();

		$aCompensacoes = array();
		$aValorReceitaDestinoCompensacao = array();
		$aValidaNumpreNumparReceita = array();
		$iQuantidadeRegistros = pg_num_rows($rsDados);
		for ($iRegistro = 0; $iRegistro < $iQuantidadeRegistros; $iRegistro++) {

			$oStdRegistro = db_utils::fieldsMemory($rsDados, $iRegistro);
			$oStdPessoa = (object) array(
				'nome' => $oStdRegistro->nome_cgm,
				'cgm' => $oStdRegistro->numero_cgm,
			);

			if (!isset($aValorReceitaDestinoCompensacao[$oStdRegistro->compensacao][$oStdRegistro->receita_debito])) {

				$aValidaNumpreNumparReceita[$oStdRegistro->compensacao][$oStdRegistro->receita_debito . "-" . $oStdRegistro->numpar_compensado . "-" . $oStdRegistro->numpre_compensado] = $oStdRegistro->valor_compensado;
				$aValorReceitaDestinoCompensacao[$oStdRegistro->compensacao][$oStdRegistro->receita_debito] = $oStdRegistro->valor_compensado;
			} else {

				if (!isset($aValidaNumpreNumparReceita[$oStdRegistro->compensacao][$oStdRegistro->receita_debito . "-" . $oStdRegistro->numpar_compensado . "-" . $oStdRegistro->numpre_compensado])) {

					$aValidaNumpreNumparReceita[$oStdRegistro->compensacao][$oStdRegistro->receita_debito . "-" . $oStdRegistro->numpar_compensado . "-" . $oStdRegistro->numpre_compensado] = $oStdRegistro->valor_compensado;
					$aValorReceitaDestinoCompensacao[$oStdRegistro->compensacao][$oStdRegistro->receita_debito] += $oStdRegistro->valor_compensado;
				}

			}

			$oCompensacao = (object) array(
				'credito' => $oStdRegistro->credito,
				'estrutural' => $oStdRegistro->estrutural,
				'descr_estrutural' => $oStdRegistro->descricao_estrutural,
				'receita' => $oStdRegistro->receita_debito,
				'descricao' => $oStdRegistro->receita_debito_descricao,
				'valor' => $aValorReceitaDestinoCompensacao[$oStdRegistro->compensacao][$oStdRegistro->receita_debito],
				'tipo_debito' => $oStdRegistro->tipo_debito,
				'data_compensacao' => $oStdRegistro->data_compensacao,
			);

			if (isset($aCompensacoes[$oStdRegistro->compensacao])) {

				$aCompensacoes[$oStdRegistro->compensacao]->aPessoas[$oStdRegistro->numero_cgm] = $oStdPessoa;
				$aCompensacoes[$oStdRegistro->compensacao]->aReceitas[$oStdRegistro->receita_debito] = $oCompensacao;
			} else {

				$oStdCompensacao = new stdClass;
				$oStdCompensacao->aPessoas = array($oStdRegistro->numero_cgm => $oStdPessoa);
				$oStdCompensacao->aReceitas = array($oStdRegistro->receita_debito => $oCompensacao);
				$aCompensacoes[$oStdRegistro->compensacao] = $oStdCompensacao;
			}
		}

		return $aCompensacoes;
	}

	/**
	 * @return bool|resource
	 * @throws DBException
	 */
	private function getDados()
	{

		$oDaoAbatimentoUtilizacao = new cl_abatimento();

		$sCampos = implode(',', array(
			"k157_abatimento    			 as credito",
			"k170_utilizacao    			 as compensacao",
			"z01_numcgm         			 as numero_cgm",
			"COALESCE(k02_estorc,c60_estrut) as estrutural",
			"COALESCE(o57_descr, c60_descr)  as descricao_estrutural",
			"k170_numpre        			 as numpre_compensado",
			"k170_numpar        			 as numpar_compensado",
			"k170_receit        			 as receita_debito",
			"k157_data          			 as data_compensacao",
			"z01_nome           			 as nome_cgm",
			"k170_valor         			 as valor_compensado",
			"recibo.k00_numpre  			 as origem",
			"k02_descr          			 as receita_debito_descricao",
			"arretipo.k00_descr 			 as tipo_debito",
		));

		$aWhere = array(
			"k157_tipoutilizacao = '" . CreditoCompensacao::TIPO_UTILIZACAO_COMPENSACAO . "'",
		);

		if ($this->getCgm()) {
			$aWhere[] = "arrenumcgm.k00_numcgm = {$this->getCgm()}";
		}

		if ($this->getTipoDebito()) {
			$aWhere[] = "abatimentoutilizacaodestino.k170_tipo in ({$this->getTipoDebito()})";
		}

		if ($this->getDataInicial()) {
			$aWhere[] = "abatimentoutilizacao.k157_data >= '{$this->getDataInicial()->getDate()}'";
		}

		if ($this->getDataFinal()) {
			$aWhere[] = "abatimentoutilizacao.k157_data <= '{$this->getDataFinal()->getDate()}'";
		}

		$sWhere = implode(' and ', $aWhere);
		$sOrder = 'k157_abatimento, recibo.k00_receit, k157_data';

		$sSqlUtilizacoes = $oDaoAbatimentoUtilizacao->sql_query_utilizacao($sCampos, $sWhere, $sOrder);
		$rsUtilizacoes = db_query($sSqlUtilizacoes);

		if (!$rsUtilizacoes) {
			throw new DBException("Não foi possível encontrar as compensações.");
		}

		return $rsUtilizacoes;
	}

	/**
	 * Configurar Emissão e Filtros no Cabeçalho do Relatório
	 */
	private function configurar()
	{

		$this->oPdf->Open();
		$this->oPdf->addHeaderDescription("COMPENSAÇÕES");
		$this->oPdf->addHeaderDescription("");

		if ($this->getDataInicial() && $this->getDataFinal()) {
			$this->oPdf->addHeaderDescription(
				"PERÍODO: {$this->getDataInicial()->getDate(DBDate::DATA_PTBR)}" .
				" ATÉ {$this->getDataFinal()->getDate(DBDate::DATA_PTBR)}"
			);
		}

		if ($this->getDataInicial() && !$this->getDataFinal()) {
			$this->oPdf->addHeaderDescription("PERÍODO: {$this->getDataInicial()->getDate(DBDate::DATA_PTBR)}");
		}

		if (!$this->getDataInicial() && $this->getDataFinal()) {
			$this->oPdf->addHeaderDescription("PERÍODO: {$this->getDataFinal()->getDate(DBDate::DATA_PTBR)}");
		}

		if ($this->getTipoDebito()) {

			$oDaoArreTipo = new cl_arretipo();
			$rsArretipo = db_query($oDaoArreTipo->sql_query_file(null, "*", null, "arretipo.k00_tipo in (" . $this->getTipoDebito() . ")"));

			if (!$rsArretipo) {
				throw new DBException("Não foi possível encontrar as informações do Tipo de Débito.");
			}

			$this->oPdf->addHeaderDescription("TIPO DE DÉBITO: {$this->getTipoDebito()}");
		}

		if ($this->getCgm()) {
			$this->oPdf->addHeaderDescription("CGM: {$this->getCgm()}");
		}

		$this->oPdf->setAutoNewLineMulticell(true);
		$this->oPdf->SetFillColor(235);
		$this->oPdf->setFontFamily("arial");
		$this->oPdf->SetFontSize(6);
		$this->oPdf->AddPage();

		$this->iAlturaLinha = 4;
		$this->iLarguraPagina = $this->oPdf->getAvailWidth();
	}

	/**
	 * Escreve o cabeçalho das compensações
	 */
	private function escreveCabecalhoCompensacao()
	{

		$this->oPdf->SetFillColor(235);
		$this->oPdf->Cell($this->iLarguraPagina * 0.10, $this->iAlturaLinha, 'CRÉDITO', 'TBLR', 0, 'C', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.30, $this->iAlturaLinha, 'RECEITA COMPENSADA', 'TBR', 0, 'C', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.30, $this->iAlturaLinha, 'TIPO DE DÉBITO', 'TBR', 0, 'C', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.15, $this->iAlturaLinha, 'DATA COMPENSAÇÃO', 'TBR', 0, 'C', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.15, $this->iAlturaLinha, 'VALOR', 'TBR', 1, 'C', 1);
		$this->oPdf->SetFillColor(255);
	}

	/**
	 * Escreve o cabeçalho dos CGMs
	 */
	private function escreveCabecalhoCgm()
	{

		$this->oPdf->SetFillColor(235);
		$this->oPdf->Cell($this->iLarguraPagina * 0.10, $this->iAlturaLinha, 'CGM', 'TBLR', 0, 'C', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.90, $this->iAlturaLinha, 'NOME', 'TBR', 1, 'C', 1);
		$this->oPdf->SetFillColor(255);
	}

	/**
	 * Escreve registros de compensações
	 *
	 * @param stdClass $oStdReceita
	 */
	private function escreveRegistroCompensacao($oStdReceita)
	{

		$this->oPdf->Cell($this->iLarguraPagina * 0.10, $this->iAlturaLinha, $oStdReceita->credito, 'BLR', 0, 'C');
		$this->oPdf->Cell($this->iLarguraPagina * 0.30, $this->iAlturaLinha, $oStdReceita->receita . ' - ' . $oStdReceita->descricao, 'BR', 0, 'L');
		$this->oPdf->Cell($this->iLarguraPagina * 0.30, $this->iAlturaLinha, $oStdReceita->tipo_debito, 'BR', 0, 'L');
		$this->oPdf->Cell($this->iLarguraPagina * 0.15, $this->iAlturaLinha, db_formatar($oStdReceita->data_compensacao, 'd'), 'BR', 0, 'C');
		$this->oPdf->Cell($this->iLarguraPagina * 0.15, $this->iAlturaLinha, db_formatar($oStdReceita->valor, 'f'), 'BR', 1, 'R');
	}

	/**
	 * Escreve registros de CGMs
	 *
	 * @param  stdClass $oStdPessoa
	 */
	private function escreveRegistroCgm($oStdPessoa)
	{

		$this->oPdf->Cell($this->iLarguraPagina * 0.10, $this->iAlturaLinha, $oStdPessoa->cgm, 'BLR', 0, 'C');
		$this->oPdf->Cell($this->iLarguraPagina * 0.90, $this->iAlturaLinha, $oStdPessoa->nome, 'BR', 1, 'C');
	}

	public function getReceitas()
	{

		$aDados = $this->processarDados();
		$aReceitas = array();

		// agrupa um array de obj por receitas
		foreach ($aDados as $oStdCompensacao) {

			foreach ($oStdCompensacao->aReceitas as $oMaOe) {

				$oReceitas = new stdClass();
				$oReceitas->iCodigo = $oMaOe->receita;
				$oReceitas->sDescricao = $oMaOe->descricao;
				$oReceitas->nValor = $oMaOe->valor;
				$aReceitas[str_pad($oMaOe->receita, 10, " ", STR_PAD_LEFT) . " - " . $oMaOe->descricao][] = $oReceitas;
			}
		}

		return $aReceitas;
	}

	public function getTotalizadores()
	{

		$aReceitas = $this->getReceitas();
		$aDadosTotais = array();

		//  Inicia com o Cabecalho do totalizador
		$oDadosTotais = new stdClass();
		$oDadosTotais->sReceita = "Receita";
		$oDadosTotais->nTotal = "Total";
		$oDadosTotais->sBold = "B";
		$oDadosTotais->sAlignA = "C";
		$oDadosTotais->sAlignB = "C";
		$aDadosTotais[] = $oDadosTotais;

		$nTotalGeral = 0;
		foreach ($aReceitas as $sReceita => $aDados) {

			$oDadosTotais = new stdClass();
			$oDadosTotais->sReceita = $sReceita;
			$nTotal = 0;

			foreach ($aDados as $oDados) {

				//calcula o valor por receita e o geral
				$nTotal += $oDados->nValor;
				$nTotalGeral += $oDados->nValor;
			}

			$oDadosTotais->nTotal = db_formatar($nTotal, "f");
			$oDadosTotais->sBold = null;
			$oDadosTotais->sAlignA = "L";
			$oDadosTotais->sAlignB = "R";
			$aDadosTotais[] = $oDadosTotais;
		}

		//  adiciona a ultima posicao do array com o tatal geral
		$oDadosTotais = new stdClass();
		$oDadosTotais->sReceita = "Total Geral";
		$oDadosTotais->nTotal = db_formatar($nTotalGeral, "f");
		$oDadosTotais->sBold = "B";
		$oDadosTotais->sAlignA = "R";
		$oDadosTotais->sAlignB = "R";
		$aDadosTotais[] = $oDadosTotais;

		return $aDadosTotais;
	}

	public function escreveTotalizadores()
	{

		$aDadosTotais = $this->getTotalizadores();

		foreach ($aDadosTotais as $aDados) {

			$this->oPdf->setfont('arial', $aDados->sBold);
			$this->oPdf->Cell(60, $this->iAlturaLinha, $aDados->sReceita, 'TBLR', 0, $aDados->sAlignA);
			$this->oPdf->Cell(40, $this->iAlturaLinha, $aDados->nTotal, 'TBLR', 1, $aDados->sAlignB);
			$this->oPdf->setfont('arial', '');

		}
		$this->oPdf->SetFillColor(235);
		$this->oPdf->Cell($this->iLarguraPagina * 0.25, $this->iAlturaLinha, "", 'BL', 0, 'R', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.50, $this->iAlturaLinha, "TOTAL: ", 'BL', 0, 'L', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.25, $this->iAlturaLinha, db_formatar($nValorTotal, 'f'), 'TBLR', 1, 'R', 1);
		$this->oPdf->SetFillColor(255);
	}

	private function escreveTotalEstrutural($aDados)
	{

		$aValor = array();
		foreach ($aDados as $oDados) {

			$aDadosReceita = $oDados->aReceitas;
			foreach ($aDadosReceita as $oDadosReceita) {

				if (!array_key_exists($oDadosReceita->estrutural, $aValor)) {

					$aValor[$oDadosReceita->estrutural]['valor'] = $oDadosReceita->valor;
					$aValor[$oDadosReceita->estrutural]['descricao'] = $oDadosReceita->descr_estrutural;
				} else {

					$aValor[$oDadosReceita->estrutural]['valor'] += $oDadosReceita->valor;
				}
			}

		}

		$this->oPdf->SetFillColor(235);
		$this->oPdf->Cell($this->iLarguraPagina * 1, $this->iAlturaLinha, 'RESUMO POR ESTRUTURAL', 'TBLR', 1, 'C', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.25, $this->iAlturaLinha, 'ESTRUTURAL', 'TBLR', 0, 'C', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.50, $this->iAlturaLinha, 'DESCRIÇÃO', 'TBR', 0, 'C', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.25, $this->iAlturaLinha, 'VALOR', 'TBR', 1, 'C', 1);
		$this->oPdf->SetFillColor(255);
		$nValorTotal = 0;
		foreach ($aValor as $sEstrutural => $aValores) {

			if ($this->oPdf->getAvailHeight() < 20) {
				$this->oPdf->AddPage();
			}

			$nValorTotal += $aValores['valor'];
			$this->oPdf->Cell($this->iLarguraPagina * 0.25, $this->iAlturaLinha, $sEstrutural, 'TBLR', 0, 'C', 1);
			$this->oPdf->Cell($this->iLarguraPagina * 0.50, $this->iAlturaLinha, $aValores['descricao'], 'TBR', 0, 'L', 1);
			$this->oPdf->Cell($this->iLarguraPagina * 0.25, $this->iAlturaLinha, db_formatar($aValores['valor'], 'f'), 'TBR', 1, 'R', 1);
		}
		$this->oPdf->SetFillColor(235);
		$this->oPdf->Cell($this->iLarguraPagina * 0.25, $this->iAlturaLinha, "", 'BL', 0, 'R', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.50, $this->iAlturaLinha, "TOTAL: ", 'BL', 0, 'L', 1);
		$this->oPdf->Cell($this->iLarguraPagina * 0.25, $this->iAlturaLinha, db_formatar($nValorTotal, 'f'), 'TBLR', 1, 'R', 1);
		$this->oPdf->SetFillColor(255);
	}

	/**
	 * Emitor Relatório
	 */
	public function emitir()
	{

		$this->oPdf = new PDFDocument(PDFDocument::PRINT_PORTRAIT);
		$this->configurar();

		$aDados = $this->processarDados();
		foreach ($aDados as $oStdCompensacao) {

			if ($this->oPdf->getAvailHeight() < 20) {
				$this->oPdf->AddPage();
			}

			$this->escreveCabecalhoCompensacao();
			foreach ($oStdCompensacao->aReceitas as $oMaOe) {
				$this->escreveRegistroCompensacao($oMaOe);
			}

			$this->escreveCabecalhoCgm();
			foreach ($oStdCompensacao->aPessoas as $oStdPessoa) {
				$this->escreveRegistroCgm($oStdPessoa);
			}

			$this->oPdf->ln(4);
		}

		if (count($aDados) > 0) {
			$this->escreveTotalEstrutural($aDados);
		}

		$this->oPdf->showPDF("RelatorioCompensacoes_" . time());
	}
}
