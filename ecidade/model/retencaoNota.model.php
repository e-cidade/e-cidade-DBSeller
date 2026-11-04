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
 * @author Iuri Guntchnigg
 * @version $Revision: 1.75 $
 * @package empenho
 */

use App\Domain\Financeiro\Empenho\Models\RetencaoReceitasProdutorRural;
use App\Domain\Financeiro\Empenho\Services\RetencaoReceitasSubcontratacaoService;
use App\Domain\Financeiro\Empenho\Services\TipoServicoObraService;
use ECidade\Financeiro\Empenho\Retencao\Apropriacao\Apropriacao;

class retencaoNota {


    /**
     * Instancia da DAo da classe empnota
     *
     * @var object
     */
    private $instanciaNota;

    /**
     * Codigo da nota fiscal
     *
     * @var integer
     */
    private $iCodNota ;

    /**
     * cpf/cnpj do credor
     *
     * @var integer
     */
    private $iCpfCnpj;

    /**
     * grava os dados em sessao
     *
     * @var boolean
     */
    private $lInSession;

    /**
     * Codigo da nota de liquidacao (ordem de pagamento)
     *
     * @var integer
     */
    private $iNotaLiquidacao;

    /**
     * codigo do grupo das autenticações
     *
     * @var intger
     */
    private $iGrupoAutenticacao= 0;

    /**
     * Codigo da conta a recolher as retencoes
     *
     * @var integer
     */
    private $iConta = null;

    /**
     * Codigo do movimento a agenda;
     * refere a tabela empagemov
     *
     * @var integer
     */
    private $iCodMovimento = null;

    /**
     * Codigo do cgm
     *
     * @var integer
     */
    private $iNumCgm       = null;
    /**
     * Codifica variaveis do tipo strings com urlencode
     *
     * @var boolean
     */
    private $lEncodeUrl = false;

    /**
     * data  base do recolhimento
     *
     * @var string
     */
    private $dtDataBase = null;

    /**
     * lista das Retencoes cadastradas
     *
     * @var array
     */
    private $aRetencoes = array();

    private $iCodigoNovoMovimento = null;

    private $codigoOrdem;

    /**
     * retencaoNota constructor.
     * @param $iCodNota
     * @throws Exception
	 */
	public function __construct($iCodNota) {

		if (empty($iCodNota)) {
			throw new Exception("Erro [1] - Nota não Informada");
		}

		$this->iCodNota      = $iCodNota;
		$this->instanciaNota = new cl_empnota;
		$this->dtDataBase    = date("Y-m-d",db_getsession("DB_datausu"));
	}

	/**
	 * Adiciona uma retencao a nota, caso a nota exista.
	 *
	 * @param  object  $oRetencao objeto stClass com as seguintes propriedades (iCodRetencao)
	 * @param  boolean $lInSession define se o objeto sera adicionado apenas na sessao, ou sera apenas adicionado a classe.
	 * @param  boolean $isUpdate   define se devera ser modificado ou nao os registros (true para apenas modificar )
	 *                              e false para adicionar novo.
	 * @return boolean
	 */
	function addRetencao($oRetencao, $lInSession = false, $isUpdate = false) {

        /* Algumas regras:
         *  1 - caso o usuario cadastrou uma retencao de tipo 1, ou 2 (Imposto de Renda)
        *      ele não pode mais cadastrar uma retencao do tipo 3 ou 4 (INSS), pois as
        *      retencoes do tipo 3, 4 deduzem da base de cálculo.
        *  2 - Não Podemos lancar uma retencao duas vezes.
        *  3 - Sempre devemos ver o calculo da retencao por CGM(cnpj/cpf) dentro do mes , nunca por nota.
        */

        /*
         * fazemos uma copia das retencoes cadastradas, para fazermos algumas validações
        */
        $this->lInSession = $lInSession;
        if ($lInSession) {
            if (isset($_SESSION["retencaoNota{$this->iCodNota}"])) {
                $aRetencoes = $_SESSION["retencaoNota{$this->iCodNota}"];
            } else {
                $aRetencoes = array();
            }
        } else {
            $aRetencoes = $this->aRetencoes;
        }

        /*
         * Validamos a segunda regra
        */
        if (key_exists($oRetencao->iCodigoRetencao, $aRetencoes) && !$isUpdate ) {
            throw new Exception("Erro [1] - Retenção já cadastrada!");
        }

        /*
         * selecionamos as informacoes da retencao escolhida pelo usuário
        */
        $oDaoRetencao = new cl_retencaotiporec;
        $sSqlRetencao = $oDaoRetencao->sql_query($oRetencao->iCodigoRetencao,"tabrec.*,retencaotiporec.*");
        $rsRetencao   = $oDaoRetencao->sql_record($sSqlRetencao);
        if ($oDaoRetencao->numrows == 0) {
            throw new Exception("Erro [2] - informações da retenção não encontradas!");
        }

        $oDadosRetencao   = db_utils::fieldsMemory($rsRetencao, 0);

        /*
         * Validamos a primeira regra;
        */

        /*
         * Verificamos se o credor refere-se a pessoa juridica
         */

        $clPagOrdem = new cl_pagordem();
        $sqlCgm = $clPagOrdem->sql_query_pag($oRetencao->codigoOrdem,
                                             "case when e49_numcgm is null then e60_numcgm else e49_numcgm end as cgm");
        $sqlCgm = "select distinct cgm from ({$sqlCgm}) as x";
        $rsCgm = $clPagOrdem->sql_record($sqlCgm);
        if ($clPagOrdem->numrows == 0) {
            throw new Exception("Erro [3] - Erro ao buscar os dados do credor");
        }

        $iNumCgm = db_utils::fieldsMemory($rsCgm, 0)->cgm;

        $isPessoaJuridica = false;

        $cgm = CgmFactory::getInstanceByCgm($iNumCgm);
        if ($cgm instanceof CgmJuridico) {
            $isPessoaJuridica = true;
        }

        if (($oDadosRetencao->e21_retencaotipocalc == 3 || $oDadosRetencao->e21_retencaotipocalc == 4
                || $oDadosRetencao->e21_retencaotipocalc == 7) && !$isUpdate ) {

            foreach ($aRetencoes as $oRetencaoAtiva) {

                if ( (   $oRetencaoAtiva->e21_retencaotipocalc == 1
                       || $oRetencaoAtiva->e21_retencaotipocalc == 2 )
                     && !$isPessoaJuridica) {

                    $sMsg  = "Erro [3] - Retenção de INSS não pode ser cadastrada, pois já foi informado ";
                    $sMsg .=  "uma retenção de imposto de renda.\n(INSS reduz a base de cálculo do IRRF)";
                    throw new Exception($sMsg);

                }
            }
        }

        /**
         * Cadastro dos dados Reinf
         */
        if (isset($oRetencao->dadosReinf) &&  $oRetencao->dadosReinf != null) {

            $dadosReinf = $oRetencao->dadosReinf;
            $oDadosRetencao->dadosReinf = new stdClass;
            $oDadosRetencao->dadosReinf->evento = (isset($dadosReinf->evento)?$dadosReinf->evento:null);

            switch ($dadosReinf->evento) {
                case 'R-2010':
                    $oDadosRetencao->dadosReinf->e19_tiposerviconotafiscal   = $dadosReinf->tipoServicoNotaFiscal;
                    $oDadosRetencao->dadosReinf->e18_descricao               = $dadosReinf->tipoServicoNotaFiscal_descricao;
                    $oDadosRetencao->dadosReinf->e19_valornaoretidoprincipal = $dadosReinf->valorNaoRetidoPrincipal;
                    $oDadosRetencao->dadosReinf->e19_valorservico15          = $dadosReinf->valorServico15;
                    $oDadosRetencao->dadosReinf->e19_valorservico20          = $dadosReinf->valorServico20;
                    $oDadosRetencao->dadosReinf->e19_valorservico25          = $dadosReinf->valorServico25;
                    $oDadosRetencao->dadosReinf->e19_valornaoretidoadicional = $dadosReinf->valorNaoRetidoAdicional;
                    $oDadosRetencao->dadosReinf->e154_tipo                   = $dadosReinf->indObra;
                    $oDadosRetencao->dadosReinf->e154_cno                    = $dadosReinf->indObraCNO;
                    $oDadosRetencao->dadosReinf->e154_numemp                 = $dadosReinf->numemp;
                    $oDadosRetencao->dadosReinf->e19_indvalorbase            = true;
                    break;
                case 'R-2055':
                    $oDadosRetencao->dadosReinf->e158_vlrrat     = $dadosReinf->vlrrat ;
                    $oDadosRetencao->dadosReinf->e158_vlrsenar   = $dadosReinf->vlrsenar;
                    $oDadosRetencao->dadosReinf->e158_empnota    = $dadosReinf->empnota;
                    $oDadosRetencao->dadosReinf->e158_vlrcp      = $dadosReinf->vlrcp;
                    break;
                case 'R-4010-4020':
                    $oDadosRetencao->dadosReinf->e167_sequencial = $dadosReinf->id;
                    $oDadosRetencao->dadosReinf->e167_codigo = $dadosReinf->codigo;
                    $oDadosRetencao->dadosReinf->e167_descricao = $dadosReinf->descricao;
                    break;
            }
        }

        /**
         * Cadastro das Subcontratacoes
         */
        if (isset($oRetencao->subcontratacoes) && is_array($oRetencao->subcontratacoes)) {
            $oDadosRetencao->subcontratacoes = $oRetencao->subcontratacoes;
        }

        $oDadosRetencao->e23_valorretencao = $oRetencao->nValorRetencao;
        $oDadosRetencao->e23_deducao       = $oRetencao->nValorDeducao;
        $oDadosRetencao->e23_valor         = $oRetencao->nValorNota;
        $oDadosRetencao->e23_valorbase     = $oRetencao->nValorbase;
        $oDadosRetencao->e23_aliquota      = $oRetencao->nAliquota;
        $oDadosRetencao->aMovimentos       = $oRetencao->aMovimentos;
        if ($lInSession) {
            $_SESSION["retencaoNota{$this->iCodNota}"][$oRetencao->iCodigoRetencao] = $oDadosRetencao;
        } else {
            $this->aRetencoes[$oRetencao->iCodigoRetencao] = $oDadosRetencao;
        }
        return true;
    }
	/**
	 * persiste a retencao na base de dados.
	 *
	 * @param  integer $iNotaLiquidacao Código da nota de liquidacao (e50_codord)
	 * @param  array   $aMovimentosAuxiliares outros movimentos que compoe a base de calculo.
	 * @return boolean
	 */
	function salvar($iNotaLiquidacao, $aMovimentosAuxiliares = null) {

		if (!db_utils::inTransaction()) {
			throw new Exception("Erro [0] - Não Existe transação ativa");
		}

		if (empty($iNotaLiquidacao)) {
			throw new Exception("Erro [1]- Código da nota de Liquidação Informado.\nRetenções não salvas");
		}

		if ($this->getCodigoMovimento() == null) {
			throw  new Exception("Erro [4] - Não foi informado o código do movimento da agenda.\n");
		}

		$aRetencoes = $this->getRetencoes();
		if (count($aRetencoes) > 0) {

			/*
			 * percorremos as retencoes cadastradas, e verificamos para ver se a retencao já foi
			* recolhida ou já exista.
			* Caso já exista,marcamos ela como inativa, e incluimos novamente.
			* Caso ja esteja recolhido dentro do mes, apenas passamos para a próxima retenção
			* caso nenhum dos dois casos,
			* incluimos nas tabelas retencaopagordem,
			* e na retencaoreceitas
			*/
			foreach ($aRetencoes as $oRetencao) {

				$lJaRecolhido        = false;
				$oDaoRetencaoReceita = new cl_retencaoreceitas;
				$aDataUsu = explode("-", date("Y-m-d",db_getsession("DB_datausu")));
				list($iAnoUsu, $iMesUsu, $iDiaCalculo) = $aDataUsu;
				$sSqlRetencao        = $oDaoRetencaoReceita->sql_query_notas(null,
						"e23_sequencial,
						e23_ativo,
						e23_dtcalculo,
						e23_recolhido",
						null,
						"e27_empagemov={$this->iCodMovimento}
				and e23_ativo     = true
				and e23_recolhido = false
				and e27_principal = true
				and e23_retencaotiporec = {$oRetencao->e21_sequencial}
				"
				);

				$rsRetencao = $oDaoRetencaoReceita->sql_record($sSqlRetencao);
				$iNumRowsRetencao = $oDaoRetencaoReceita->numrows;
				/*
				 * Percorremos as retenções encontradas, e que nao foram baixas e desativamos
				*/
				if ($iNumRowsRetencao > 0) {

					$aRetencoesAntigas = db_utils::getCollectionByRecord($rsRetencao);
					foreach ($aRetencoesAntigas as $oRetencaoAntiga) {

						$oDaoRetencaoReceita = new cl_retencaoreceitas;
						$oDaoRetencaoReceita->e23_sequencial = $oRetencaoAntiga->e23_sequencial;
						$oDaoRetencaoReceita->e23_ativo      = "false";
						$oDaoRetencaoReceita->alterar($oRetencaoAntiga->e23_sequencial);
						unset($oDaoRetencaoReceita);

					}
				}
				//Incluimos na retencaopagordem
				$dtDataUsu                      = date("Y-m-d",db_getsession("DB_datausu"));
				$oDaoRetencaoNota               = new cl_retencaopagordem;
				$oDaoRetencaoNota->e20_pagordem = $iNotaLiquidacao;
				$oDaoRetencaoNota->e20_data     = $dtDataUsu;
				$oDaoRetencaoNota->incluir(null);
				if ($oDaoRetencaoNota->erro_status == 0) {

					$sMsg  = "Erro[2] - Não foi possível incluir Retencao {$oRetencao->e21_sequencial}.\n";
					$sMsg .= "Erro Técnico: {$oDaoRetencaoNota->erro_msg}";
					throw new Exception($sMsg);

				}

				/*
				 * Incluimos na retencaoreceitas
				*/
				$oDaoRetencaoReceita = new cl_retencaoreceitas;
				$oDaoRetencaoReceita->e23_dtcalculo        = $dtDataUsu;
				$oDaoRetencaoReceita->e23_ativo            = "true";
				$oDaoRetencaoReceita->e23_retencaotiporec  = $oRetencao->e21_sequencial;
				$oDaoRetencaoReceita->e23_retencaopagordem = $oDaoRetencaoNota->e20_sequencial;
				$oDaoRetencaoReceita->e23_valor            = "{$oRetencao->e23_valor}";
				$oDaoRetencaoReceita->e23_deducao          = "{$oRetencao->e23_deducao}";
				$oDaoRetencaoReceita->e23_valorbase        = "{$oRetencao->e23_valorbase}";
				$oDaoRetencaoReceita->e23_valorretencao    = "{$oRetencao->e23_valorretencao}";
				$oDaoRetencaoReceita->e23_aliquota         = "{$oRetencao->e23_aliquota}";
				$oDaoRetencaoReceita->e23_recolhido        = "false";
				$oDaoRetencaoReceita->incluir(null);
				if ($oDaoRetencaoReceita->erro_status == 0) {

					$sMsg  = "Erro[3] - Não foi possível incluir Retencao {$oRetencao->e21_sequencial}.\n";
					$sMsg .= "Erro Técnico: {$oDaoRetencaoReceita->erro_msg}";
					throw new Exception($sMsg);

				}

				/*
				 * Ligamos a retencao ao movimento da agenda;
				*/
				$oDaoRetencaoMov = new cl_retencaoempagemov;
				$oDaoRetencaoMov->e27_empagemov        = $this->getCodigoMovimento();
				$oDaoRetencaoMov->e27_retencaoreceitas = $oDaoRetencaoReceita->e23_sequencial;
				$oDaoRetencaoMov->e27_principal        = "true";
				$oDaoRetencaoMov->incluir(null);
				if ($oDaoRetencaoMov->erro_status == 0) {

					$sMsg  = "Erro[5] - Não foi possível incluir Retencao {$oRetencao->e21_sequencial}.\n";
					$sMsg .= "Erro Técnico: {$oDaoRetencaoMov->erro_msg}";
					throw new Exception($sMsg);

				}
				if (is_array($oRetencao->aMovimentos) && count($oRetencao->aMovimentos) > 0) {
					for ($i = 0; $i < count($oRetencao->aMovimentos); $i++) {

						$oDaoRetencaoMov = new cl_retencaoempagemov;
						$oDaoRetencaoMov->e27_empagemov        = $oRetencao->aMovimentos[$i];
						$oDaoRetencaoMov->e27_retencaoreceitas = $oDaoRetencaoReceita->e23_sequencial;
						$oDaoRetencaoMov->e27_principal        = "false";
						$oDaoRetencaoMov->incluir(null);
						if ($oDaoRetencaoMov->erro_status == 0) {
							$sMsg  = "Erro[6] - Não foi possível incluir Retencao {$oRetencao->e21_sequencial}.\n";
							$sMsg .= "Erro Técnico: {$oDaoRetencaoMov->erro_msg}";
							throw new Exception($sMsg);
						}
					}
				}

                // Dados do EFD-REINF
                if (isset($oRetencao->dadosReinf)) {
                    switch ($oRetencao->dadosReinf->evento) {
                        case 'R-2010':
                            if (!empty($oRetencao->dadosReinf->e19_tiposerviconotafiscal)) {
                                $oDaoRetencaoReceitasAdicionais = new cl_retencaoreceitasadicionais();
                                $oDaoRetencaoReceitasAdicionais->e19_retencaoreceitas        = $oDaoRetencaoReceita->e23_sequencial;
                                $oDaoRetencaoReceitasAdicionais->e19_tiposerviconotafiscal   = $oRetencao->dadosReinf->e19_tiposerviconotafiscal;
                                $oDaoRetencaoReceitasAdicionais->e19_valornaoretidoprincipal = str_replace(',', '.', $oRetencao->dadosReinf->e19_valornaoretidoprincipal);
                                $oDaoRetencaoReceitasAdicionais->e19_valorservico15          = str_replace(',', '.', $oRetencao->dadosReinf->e19_valorservico15);
                                $oDaoRetencaoReceitasAdicionais->e19_valorservico20          = str_replace(',', '.', $oRetencao->dadosReinf->e19_valorservico20);
                                $oDaoRetencaoReceitasAdicionais->e19_valorservico25          = str_replace(',', '.', $oRetencao->dadosReinf->e19_valorservico25);
                                $oDaoRetencaoReceitasAdicionais->e19_valornaoretidoadicional = str_replace(',', '.', $oRetencao->dadosReinf->e19_valornaoretidoadicional);
                                $oDaoRetencaoReceitasAdicionais->e19_indvalorbase            = true;

                                $oDaoRetencaoReceitasAdicionais->incluir(null);
                                if ($oDaoRetencaoReceitasAdicionais->erro_status == 0) {
                                    $sMsg  = "Erro[7] - Não foi possível incluir Retencao {$oRetencao->e21_sequencial}.\n";
                                    $sMsg .= "Erro Técnico: {$oDaoRetencaoReceitasAdicionais->erro_msg}";
                                    throw new Exception($sMsg);
                                }

                                try {
                                    $tipoServicoObra = new TipoServicoObraService();
                                    $tipoServicoObra->setNumemp($oRetencao->dadosReinf->e154_numemp);
                                    $tipoServicoObra->setTipo($oRetencao->dadosReinf->e154_tipo);
                                    $tipoServicoObra->setCNO($oRetencao->dadosReinf->e154_cno);
                                    $tipoServicoObra->save();
                                } catch (Exception $e) {
                                    $sMsg  = "Erro[9] - Não foi possível incluir Retencao {$oRetencao->e21_sequencial}.\n";
                                    $sMsg .= "Erro Técnico: {$e->getMessage()}";
                                    throw new Exception($sMsg);
                                }
                            }
                            break;
                        case 'R-2055':
                            $retencaoReceitasProdutorRural = new RetencaoReceitasProdutorRural();
                            $retencaoReceitasProdutorRural->e158_retencaoreceitas = $oDaoRetencaoReceita->e23_sequencial;
                            $retencaoReceitasProdutorRural->e158_vlrrat           = $oRetencao->dadosReinf->e158_vlrrat;
                            $retencaoReceitasProdutorRural->e158_vlrsenar         = $oRetencao->dadosReinf->e158_vlrsenar;
                            $retencaoReceitasProdutorRural->e158_vlrcp            = $oRetencao->dadosReinf->e158_vlrcp;
                            $retencaoReceitasProdutorRural->e158_empnota          = $oRetencao->dadosReinf->e158_empnota;

                            try {
                                $retencaoReceitasProdutorRural->save();
                            } catch (Exception $e) {
                                $sMsg  = "Erro[10] - Não foi possível incluir Retencao {$oRetencao->e21_sequencial}.\n";
                                $sMsg .= "Erro Técnico: {$e->getMessage()}";
                                throw new Exception($sMsg);
                            }
                            break;
                        case 'R-4010-4020':
                            $this->vincularRetencaoNaturezaRendimento(
                                $oDaoRetencaoReceita->e23_sequencial,
                                $oRetencao->dadosReinf->e167_sequencial
                            );
                            break;
                    }
                }

                // Dados de Subcontratacoes
                if (isset($oRetencao->subcontratacoes)) {
                    try {
                        foreach ($oRetencao->subcontratacoes as $subcontratacao) {
                            $subcontratacao->e163_retencaoreceitas = $oDaoRetencaoReceita->e23_sequencial;
                            $oSubcontratacaoService = new RetencaoReceitasSubcontratacaoService;
                            $oSubcontratacaoService->saveWithOldConnection($subcontratacao);
                        }
                    } catch (Exception $e) {
                        $sMsg  = "Erro[11] - Não foi possível incluir Retencao {$oRetencao->e21_sequencial}.\n";
                        $sMsg .= "Erro Técnico: {$e->getMessage()}";
                        throw new Exception($sMsg);
                    }
                }
			}
		}

        $this->unsetSession();
		return true;
	}
	/**
	 * Seta  o Codigo do movimento da agenda;
	 *
	 * @param integer $iCodMovimento Código do movimento
	 */
	function setCodigoMovimento($iCodMovimento) {
		if (!empty($iCodMovimento)) {
			$this->iCodMovimento = $iCodMovimento;
		}
	}

	/**
	 * retorna o codigo do movimento da retencao.
	 *
	 * @return integer
	 */
	function getCodigoMovimento() {
		return $this->iCodMovimento;
	}

	/**
         * Seta  o Codigo do novo movimento da agenda gerado para a retencao
         *
         * @param integer $iCodigoNovoMovimento Código do movimento
         */
        function setCodigoNovoMovimento($iCodigoNovoMovimento) {
           $this->iCodigoNovoMovimento = $iCodigoNovoMovimento;
        }

        /**
         * retorna o Codigo do novo movimento da agenda gerado para a retencao
         *
         * @return integer
         */
        function getCodigoNovoMovimento() {
           return $this->iCodigoNovoMovimento;
        }

	/**
	 * Seta o codigo da nota de liquidacao (pagordem)
	 *
	 * @param integer $iNotaLiquidacao código da nota de liquidação
	 */
	function setINotaLiquidacao($iNotaLiquidacao) {
		$this->iNotaLiquidacao = $iNotaLiquidacao;
	}
	/**
	 * Seta a propriedade lEncodeUrl
	 *
	 * @param boolean $lEncode
	 */
	function setEncodeUrl($lEncode) {
		$this->lEncodeUrl = $lEncode;
	}
	/**
	 * Define a data base das retencoes
	 * Usado para o recolhimento da base
	 *
	 * @param string $dtBase data no formato YYYY-MM-DD
	 */
	function setDataBase($dtBase) {
		$this->dtDataBase = $dtBase;
	}

	function getDataBase() {
		return $this->dtDataBase;
	}
	/**
	 * Retorna uma collection com as retencoes cadastradas
	 *
	 * @return mixed;
	 */
	function getRetencoes() {

		$aRetencoes = [];
		if ($this->lInSession) {
			if (isset($_SESSION["retencaoNota{$this->iCodNota}"])) {
				foreach ($_SESSION["retencaoNota{$this->iCodNota}"] as $iRetencao => $oDados) {
					$aRetencoes[] = $oDados;
				}
			}
			return $aRetencoes;
		} else {
			foreach ($this->aRetencoes as $iRetencao => $oDados ) {
				$aRetencoes[] = $oDados;
			}
		}
		return $this->aRetencoes;
	}

	/**
	 * Seta se o objeto deve ser mantido em sessao.
	 *
	 * @param boolean $lInSession
	 */
	function setInSession($lInSession) {
		$this->lInSession = $lInSession;
	}

	/**
	 * destroi as informacoes das retencoes que estão em sessão.
	 *
	 */
	function unsetSession() {

		if (isset($_SESSION["retencaoNota{$this->iCodNota}"])) {
			unset($_SESSION["retencaoNota{$this->iCodNota}"]);
		}
	}

	/**
	 * retorna as retencoes cadastradass para a op;
	 *
	 *
	 * @param integer $iNotaLiquidacao código da ordem de pagamento
	 * @param boolean $lInSession define se os dados sera retornados na sessao (true), ou num array (false)
	 * @param integer $iTipo define o tipo da pesquisa 0 - retornar as retencoes recolhidas ou nao. 1 somente Recolhidas.
	 *                                                 2 - nao recolhidas
	 * @param boolean $lPrincipal retorna somente as retenções calculadas como principais
	 * @return boolean| array retorna m boolean caso $lInSession = true ou um array de objetos caso false;
	 */
	function getRetencoesFromDB($iNotaLiquidacao, $lInSession = true, $iTipo = 2,$iMes = "", $iAno = "", $lPrincipal = false) {

        $aRetencoes = array();
        if (!isset($_SESSION["retencaoNota{$this->iCodNota}"]) || $lInSession == false) {

            $sWhere = "";
            if ($iMes != "" && $iAno != "") {

                $sWhere .= " and extract(month from e23_dtcalculo) = {$iMes} ";
                $sWhere .= " and extract(year  from e23_dtcalculo) = {$iAno} ";

            }
            if ($lPrincipal == true) {
                $sWhere .= " and e27_principal is true ";
            }

            $sRecolhida = "";
            if ($iTipo == 1) {
                $sRecolhida = " and e23_recolhido is true ";
            } else if ($iTipo == 2) {
                $sRecolhida = " and e23_recolhido is false";
            }

            $oDaoRetencaoEmpAgeMov = new cl_retencaoempagemov;
            $oDaoRetencaoReceitas = new cl_retencaoreceitas;

            $sCampos = "distinct
                  e48_cgm,
                  tabrec.*,
                  retencaotiporec.*,
                  retencaoreceitas.*,
                  e27_empagemov,
                  e27_principal,
                  retencaoreceitasadicionais.*,
                  tiposerviconotafiscal.e18_descricao,
                  retencaoreceitasprodutorrural.*,
                  emptiposervicoobra.*";

            $sWhereRetencaoReceitas = "e20_pagordem = {$iNotaLiquidacao}
                                 and e23_ativo     = true
                                 and e71_anulado   = false
                                 {$sRecolhida}
                                 {$sWhere}";

            $sSqlRetencaoReceitas = $oDaoRetencaoReceitas->sql_query_notas(
                null,
                $sCampos,
                "e21_sequencial",
                $sWhereRetencaoReceitas
            );

            $rsRetencoes = $oDaoRetencaoReceitas->sql_record($sSqlRetencaoReceitas);

            if ($oDaoRetencaoReceitas->numrows > 0) {
                for ($iInd = 0; $iInd < $oDaoRetencaoReceitas->numrows; $iInd++) {
                    $oRetencao = db_utils::fieldsMemory($rsRetencoes, $iInd, false, false, $this->lEncodeUrl);

                    /**
                     * Mapeia dados EFD-Reinf
                     */
                    if (isset($oRetencao->e19_sequencial) && !empty($oRetencao->e19_sequencial)) {
                        // R-2010
                        $oRetencao->dadosReinf = new stdClass;
                        $oRetencao->dadosReinf->evento = 'R-2010';
                        $oRetencao->dadosReinf->e19_retencaoreceitas = $oRetencao->e23_sequencial;
                        $oRetencao->dadosReinf->e19_tiposerviconotafiscal = $oRetencao->e19_tiposerviconotafiscal;
                        $oRetencao->dadosReinf->e18_descricao = $oRetencao->e18_descricao;
                        $oRetencao->dadosReinf->e19_valornaoretidoprincipal = $oRetencao->e19_valornaoretidoprincipal;
                        $oRetencao->dadosReinf->e19_valorservico15 = $oRetencao->e19_valorservico15;
                        $oRetencao->dadosReinf->e19_valorservico20 = $oRetencao->e19_valorservico20;
                        $oRetencao->dadosReinf->e19_valorservico25 = $oRetencao->e19_valorservico25;
                        $oRetencao->dadosReinf->e19_valoradicional = $oRetencao->e19_valoradicional;
                        $oRetencao->dadosReinf->e19_valornaoretidoadicional = $oRetencao->e19_valornaoretidoadicional;

                        $oRetencao->dadosReinf->e154_tipo = $oRetencao->e154_tipo;
                        $oRetencao->dadosReinf->e154_cno  = $oRetencao->e154_cno;
                        $oRetencao->dadosReinf->e154_numemp = $oRetencao->e154_numemp;
                    } elseif (isset($oRetencao->e158_sequencial) && !empty($oRetencao->e158_sequencial)) {
                        // R-2055
                        $oRetencao->dadosReinf = new stdClass;
                        $oRetencao->dadosReinf->evento = 'R-2055';
                        $oRetencao->dadosReinf->e158_retencaoreceitas = $oRetencao->e23_sequencial;
                        $oRetencao->dadosReinf->e158_vlrrat = $oRetencao->e158_vlrrat;
                        $oRetencao->dadosReinf->e158_vlrsenar = $oRetencao->e158_vlrsenar;
                        $oRetencao->dadosReinf->e158_vlrcp = $oRetencao->e158_vlrcp;
                        $oRetencao->dadosReinf->e158_empnota = $oRetencao->e158_empnota;
                    }

                    /**
                     * Dados da subcontratacao
                     */
                    $oSubcontratacaoService = new RetencaoReceitasSubcontratacaoService;
                    $aSubcontratacoes = $oSubcontratacaoService->getSubcontratacao($oRetencao->e23_sequencial);
                    $oRetencao->subcontratacoes = $aSubcontratacoes;

                    /**
                     * Buscamos todos os movimentos que foram usados para fazer parte do calculo.
                     */
                    $sSqlMovimentos = $oDaoRetencaoEmpAgeMov->sql_query_file(
                        null,
                        "*",
                        null,
                        "e27_retencaoreceitas = {$oRetencao->e23_sequencial} and e27_principal is false"
                    );
                    $rsMovimentos = $oDaoRetencaoEmpAgeMov->sql_record($sSqlMovimentos);
                    $oRetencao->aMovimentos = array();
                    if ($oDaoRetencaoEmpAgeMov->numrows > 0) {
                        for ($i = 0; $i < $oDaoRetencaoEmpAgeMov->numrows; $i++) {
                            $oRetencao->aMovimentos[] = $aMovimentos = db_utils::fieldsMemory($rsMovimentos, $i)->e27_empagemov;
                        }
                    }
                    if ($lInSession) {
                        $_SESSION["retencaoNota{$this->iCodNota}"][$oRetencao->e21_sequencial] = $oRetencao;
                    } else {
                        $aRetencoes[] = $oRetencao;
                    }
                }
            }
        }

        if ($lInSession) {
            return true;
        } else {
            return $aRetencoes;
        }
    }
	/**
	 * Desativa o calculo da retencao
	 *
	 * @param integer $iCodigoRetencao Codigo da retencao
	 * @param integer [$iNotaLiquidacao]
	 */
	function desativarRetencao($iCodigoRetencao, $iNotaLiquidacao=null) {

		if (!db_utils::inTransaction()) {
			throw new Exception("Erro [0] - Não Existe transação ativa");
		}
		if (!empty($iNotaLiquidacao)) {

			$oDaoRetencaoReceitas  = new cl_retencaoreceitas;
			$sSqlRetencaoRecieitas = $oDaoRetencaoReceitas->sql_query_notas(null,
					"*",
					null,
					"e20_pagordem = {$iNotaLiquidacao}
			and e23_recolhido = false
			and e23_retencaotiporec = {$iCodigoRetencao}
			and e23_ativo = true
			and e71_anulado = false"
			);
			$rsRetencoes          = $oDaoRetencaoReceitas->sql_record($sSqlRetencaoRecieitas);
			if ($oDaoRetencaoReceitas->numrows > 0) {

				$oRetencaoAtiva                       = db_utils::fieldsMemory($rsRetencoes, 0);
				$oDaoRetencaoReceitas->e23_ativo      = "false";
				$oDaoRetencaoReceitas->e23_sequencial = $oRetencaoAtiva->e23_sequencial;
				$oDaoRetencaoReceitas->alterar($oRetencaoAtiva->e23_sequencial);
				if ($oDaoRetencaoReceitas->erro_status == 0) {

					throw new Exception("Erro [1] - Não foi possivel desativar retenção\nErro: {$oDaoRetencaoReceitas->erro_status}");
				}
			}
		}
		unset($_SESSION["retencaoNota{$this->iCodNota}"][$iCodigoRetencao]);
	}

	/**
	 * retorna o valor total das retencoes da nota de liquidacao
	 * @param integer $iNotaLiquidacao Código da nota de liquidacao
	 * @return Float
	 */
	function getValorRetencao($iNotaLiquidacao) {

		if (empty($iNotaLiquidacao)){
			throw new Exception("Erro [1] - Nota de liquidação nao informado");
		}
		$sSqlTotalRetencao = "select fc_valorretencaonota({$iNotaLiquidacao}) as valortotal";
		return db_utils::fieldsMemory(db_query($sSqlTotalRetencao), 0)->valortotal;

	}

	/**
	 * Retorna o valor total das retencoes calculadas para o movimento
	 *
	 * @param integer $iCodMov Código do movimento
	 * @param boolean $lrecolhido se soma somente as recolhidas
	 * @return float
	 */
	function getValorRetencaoMovimento($iCodMov, $lrecolhido = false, $database = null) {

		if (empty($iCodMov)){
			throw new Exception("Erro [1] - movimento da Agenda não informado");
		}

		$database = $database == null?"null":"'{$database}'";
		$sRecolhido = $lrecolhido?"true":"false";
		$sSqlTotalRetencao = "select fc_valorretencaomov({$iCodMov}, {$sRecolhido}, $database) as valortotal";

        $resource = db_query($sSqlTotalRetencao);
        return db_utils::fieldsMemory($resource, 0)->valortotal;
	}


  /**
   *
   * verifica se ja foi lancado um slip para um retencao
   * return true se ja existe
   */
  public function verificaRetencaoSlip($retencaoReceita){

    $oDao = new cl_slipretencaoreceitas;
    $sql = $oDao->sql_query_file(null, "*", null, "k206_retencaoreceitas = {$retencaoReceita}");
    $oDao->sql_record($sql);
    if ($oDao->numrows > 0){
      return true;
    }
    return false;
  }


  /**
   *  irá gerar slips automatico para cada retencao se a conta dela for uma extra
   */
  public function gerarSlipsDasRetencoesReceitaExtra( $oRetencao )
  {

      // para cada retenção será gerado um slip
      // somente criar slip se a retenção possuir receita extra

    //if ( ReceitaExtraOrcamentaria::isExtra( $oRetencao->e21_receita  )) {
    if ( $oRetencao->k02_tipo == "E" ) {

      // dump("SIM: RECEITA {$oRetencao->e21_receita} é EXTRA", $oRetencao);

      // receita da retencao
      $retencaoReceita = $oRetencao->e23_sequencial;

      //se ja existe um slip para receita da retencao, não precisa emitir outro
      if ($this->verificaRetencaoSlip($retencaoReceita)) {
          return ;
      }

       // conta a credito:
       // pela conta  do movimento:  "$oMovimento->iContaSaltes": "7913" verifica se
       // tem extra vinculado (k109_saltesextra) e usa a credito
       $oContaBancaria = new contaTesouraria($this->getConta());
       $iCodigoCredito = $oContaBancaria->getCodigoConta();

       if (!empty($oContaBancaria->getContaExtra())) {
         $iCodigoCredito = $oContaBancaria->getContaExtra();
       }

       $oReceita = new ReceitaExtraOrcamentaria($oRetencao->e21_receita);
       $iCodigoDebito = $oReceita->getContaPlanoPCASP()->getReduzido();
       /**
        * se a credito for nula buscamos pelo vinculo do recurso da conta extra
        */
       if (empty($iCodigoCredito)) {

         $oContaExtra = new ReceitaExtraOrcamentaria($oRetencao->e21_receita); //contaTesouraria( $iCodigoDebito);
         $iRecursoExtra = $oContaExtra->getContaPlanoPCASP()->getRecurso();
         $iCodigoCredito = $this->getReduzidoPorRecursoExtra($iRecursoExtra);
       }
       $oNotaLiquidacao = new ordemPagamento($this->iNotaLiquidacao);
       $oNotaLiquidacao->getDadosOrdem();

       $nota = $this->iCodNota;
       $codord = $this->iNotaLiquidacao;
       $empenho = "{$oNotaLiquidacao->getDadosOrdem()->e60_codemp}/{$oNotaLiquidacao->getDadosOrdem()->e60_anousu}";

       $oNota = new NotaLiquidacao($nota);

       $sObservacao = "Valor Correspondente a Retenção de Pagamento: Empenho: {$empenho}  OP: {$codord} Nota: {$oNota->getNumeroNota()}.";

       $oSlip = new slip();
       $oSlip->setContaCredito($iCodigoCredito);
       $oSlip->setContaDebito($iCodigoDebito);
       $oSlip->setHistorico(9017);
       $oSlip->setCaracteristicaPeculiarCredito("000");
       $oSlip->setCaracteristicaPeculiarDebito("000");
       $oSlip->setValor($oRetencao->e23_valorretencao);
       $oSlip->setTipoPagamento(3);
       $oSlip->setSituacao(1);
       $oSlip->setNumCgm($oRetencao->e48_cgm);
       $oSlip->setObservacoes($sObservacao);
       $oSlip->save();
       Slip::vincularTipoOperacaoSlip($oSlip->getSlip(), 13);
       Slip::vincularSlipReceitaRetencao($oSlip->getSlip(), $retencaoReceita);

     }

  }


  /**
   * busca o reduzido pelo recurso da extra
   * buscamos o primeiro reduzido que achar para o recurso da conta extra pela conta 1111119
   *
   */

   public function getReduzidoPorRecursoExtra($iRecurso)
   {

    $iAnousu = db_getsession("DB_anousu");
    $iInstit = db_getsession("DB_instit");
    $oDaoSaltes = new cl_saltes;

    $sWhere  = "c61_codigo = $iRecurso" ;
    $sWhere .= " and substr(c60_estrut,1,7) in ('1111106', '1111119') limit 1" ;

    $sql = $oDaoSaltes->sql_query_orcamento_recurso(null, $iInstit,$iAnousu ,"c61_reduz",null, $sWhere, null);
    $rs = $oDaoSaltes->sql_record($sql);
    if ($oDaoSaltes->numrows <= 0) {

      $msg =   "Erro ao buscar Reduzido para credito para o slip da retençao. \n";
      $msg .= " Não foi encontrado reduzido para o estrutural: ('1111106...', '1111109...') \n";
      $msg .= " com recurso código: {$iRecurso} ";
      throw new Exception($msg);
    }

    return db_utils::fieldsMemory($rs, 0)->c61_reduz;
   }

    /**
     * Efetua a baixa (recolhimento) das retencoes.
     * emite os recibos avulsos, e planilhas de retencao.
     *
     * @param array $aRetencoes array com as retencoes a sere baixadas
     * @return bool
     * @throws BusinessException
     * @throws DBException
     */
    function baixarRetencoes($aRetencoes) {

        if (!is_array($aRetencoes)) {
            throw new Exception("Erro [1] - aRetenções deve ser um Array");
        }

        /**
         * Buscamos qual instituição está logado o usuário.
         * caso o usuário esta logado com uma instituição que nao seja a prefeitura,
         * todos os recibos seram avulsos.
         * Apenas para a prefeitura que devemos emitir planilha de retencao e
         * recibo de debito ;
         */

        require_once(modification('std/db_stdClass.php'));
        require_once(modification("model/recibo.model.php"));
        $oInstit = db_stdClass::getDadosInstit();
        /*
         * Conta original do pagamento do empenho
        * apenas usamos essa variavel quando temos uma retencao que possua uma conta extra-orcamentária
        * cadastrada para fazer a baixa e  uma receita extra.
        */
        $iContaOriginal = $this->getConta();

        require_once(modification('classes/ordemPagamento.model.php'));
        $oNotaLiquidacao = new ordemPagamento($this->iNotaLiquidacao);
        $oNotaLiquidacao->getDadosOrdem();

        $empenho = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($oNotaLiquidacao->oDadosOrdem->e50_numemp);

        foreach ($aRetencoes as $oRetencao) {


            if (empty($oRetencao->e23_sequencial)){
                throw new Exception("Erro [2] - Retenção não Informada");
            }
            /*
             * Caso a conta foi modificado por uma retencao com receita extra, setamos a conta que
            * foi realizado a baixa do empenho.
            */
            $this->setConta($iContaOriginal);


            /**
             * se o parametro do caixa Utiliza Conta Extra Orçamentária: for SIM
             *   vamos alterar para o debito ser a extra da conta da tesouraria
             */
            $ParametroContaExtra = ParametroCaixa::getUtilizaContaExtraOrcamentaria(db_getsession("DB_instit"));
            $sParametroSlipAuto = ParametroCaixa::getParametroMomentoSlipAutomatico(db_getsession("DB_instit"));

            $oContaTesouraria = new contaTesouraria($iContaOriginal);


            if ($sParametroSlipAuto == 1) {
                $this->gerarSlipsDasRetencoesReceitaExtra($oRetencao);
            }

            $oDaoRetencaoReceitas                 = new cl_retencaoreceitas;
            $oDaoRetencaoReceitas->e23_sequencial = $oRetencao->e23_sequencial;
            $oDaoRetencaoReceitas->e23_recolhido  = "true";
            $aDataCalculo   = explode("-", $oRetencao->e23_dtcalculo);
            $aDataPagamento = explode("-", $this->getDataBase());
            if ($aDataCalculo[1] != $aDataPagamento[1]) {
                $oDaoRetencaoReceitas->e23_dtcalculo = $this->getDataBase();
            }
            $oDaoRetencaoReceitas->alterar($oRetencao->e23_sequencial);
            if ($oDaoRetencaoReceitas->erro_status == 0) {

                $sErroMsg  = "Erro [2] - Não Foi possível recolher a retenção ({$oRetencao->e23_sequencial})";
                throw new Exception($sErroMsg);
            }

            if ($oRetencao->e23_valorretencao > 0) {

                /*
                 * Verificamos se a receita da retencao não e uma receita-extra(receita encontra-se na tabela tabplan)
                */
                $receitaExtra = $this->getTipoReceita($oRetencao->k02_codigo) == 2;
                if ( $ParametroContaExtra && $oRetencao->k02_tipo == "E" &&
                    !APROPRIACAO_RETENCAO ) {


                            /**
                            * validar a conta extra com data limite
                            */
                            if ($oContaTesouraria->getContaExtra() != null) {
                                $validaContaExtra = new contaTesouraria($oContaTesouraria->getContaExtra());
                                if (  $validaContaExtra->getDataLimite() != null) {
                                    $dataLimiteExtra = db_formatar($validaContaExtra->getDataLimite(), "d");
                                    $erro = "Erro [3] - Conta extra: {$oContaTesouraria->getContaExtra()} da conta: {$iContaOriginal} possui data limite: {$dataLimiteExtra}" ;
                                    throw new Exception($erro);
                                }
                            }

                            $iContaExtra = $oContaTesouraria->getContaExtra();
                            $this->setConta($iContaExtra);

                            /*
                             * Pesquisamos se foi incluido valores para fazer transferencia bancaria.
                            * caso exista transferencia, devemos deduzir o valor da retencao do valor a transferir
                            */
                            if ($oRetencao->e27_empagemov != "") {

                                $oDaoEmpageMovSlips = new cl_empagemovslips;
                                $sSqlSlipMovimento  = $oDaoEmpageMovSlips->sql_query_file(null,"*",null,"k107_empagemov = {$oRetencao->e27_empagemov}");
                                $rsMovimento        = $oDaoEmpageMovSlips->sql_record($sSqlSlipMovimento);
                                if ($oDaoEmpageMovSlips->numrows > 0) {

                                    $oMovimentoSlip = \db_utils::fieldsMemory($rsMovimento,0);
                                    $nValorRetencao                      = round(($oMovimentoSlip->k107_valor - $oRetencao->e23_valorretencao),2);
                                    $oDaoEmpageMovSlips->k107_valor      = "$nValorRetencao";
                                    $oDaoEmpageMovSlips->k107_sequencial = $oMovimentoSlip->k107_sequencial;
                                    $oDaoEmpageMovSlips->k107_ctacredito = $oMovimentoSlip->k107_ctacredito;
                                    $oDaoEmpageMovSlips->k107_ctadebito = $oMovimentoSlip->k107_ctadebito;
                                    $oDaoEmpageMovSlips->alterar($oMovimentoSlip->k107_sequencial);
                                    if ($oDaoEmpageMovSlips->erro_status == 0) {

                                        $sErroMsg  = "Erro [10] - Não Foi possível recolher a retenção ({$oRetencao->e23_sequencial})";
                                        throw new \Exception($sErroMsg);

                                    }
                                }
                            }

                }

                require_once(modification("model/Dotacao.model.php"));

                $oDotacao        = new Dotacao($oNotaLiquidacao->oDadosOrdem->e60_coddot,
                                               $oNotaLiquidacao->oDadosOrdem->e60_anousu
                                              );

                $sHistoricoRecibo  = "Neste pagamento  foi lançada uma retenção ";
                $sHistoricoRecibo .= "para o empenho {$oNotaLiquidacao->oDadosOrdem->e60_codemp}/{$oNotaLiquidacao->oDadosOrdem->e60_anousu} ";
                $sHistoricoRecibo .= "no valor de R$ ".trim(db_formatar($oRetencao->e23_valorretencao,"f"));
                $sHistoricoRecibo .= " pela Ordem de Pagamento n° {$oNotaLiquidacao->oDadosOrdem->e50_codord}";
                $sHistoricoRecibo .= " correspondente a Nota Fiscal n° {$oNotaLiquidacao->oDadosOrdem->e69_numero} ";
                $sHistoricoRecibo .= "de ".db_formatar($oNotaLiquidacao->oDadosOrdem->e69_dtnota,"d");
                $sHistoricoRecibo .= " CGM: ".$oNotaLiquidacao->oDadosOrdem->z01_numcgm." - ".str_replace("'","",$oNotaLiquidacao->oDadosOrdem->z01_nome);
                if ($oRetencao->e21_retencaotipocalc != 5 || $oInstit->prefeitura == "f") {


                    $oReciboAvulso = new \recibo(1, $this->getNumCgm());

                    //$iConta =
                    //dump("Conta: {$this->getConta()} ");

                    $oReciboAvulso->setConta($this->getConta());
                    $oReciboAvulso->adicionarRecurso($oDotacao->getRecurso());
                    $oReciboAvulso->setDataRecibo($this->getDataBase());
                    $oReciboAvulso->setDataVencimentoRecibo($this->getDataBase());
                    $oReciboAvulso->setGrupoAutenticacao($this->getGrupoAutenticacao());
                    $oReciboAvulso->setHistorico($sHistoricoRecibo);
                    $oReciboAvulso->adicionarReceita($oRetencao->k02_codigo, $oRetencao->e23_valorretencao, 0, '000');
                    if (isset($oReciboAvulso)) {

                        /*
                         *Pesquisamos o recurso do recibo. que deve ser o mesmo da conta pagadora.
                        */
                        $oDaoDotacao = new cl_orcdotacao;
                        $sSqlDotacao = $oDaoDotacao->sql_query_file(
                                $oNotaLiquidacao->oDadosOrdem->e60_anousu,
                                $oNotaLiquidacao->oDadosOrdem->e60_coddot,
                                "o58_codigo");
                        $rsRecurso  = $oDaoDotacao->sql_record($sSqlDotacao);
                        if ($oDaoDotacao->numrows == 1) {
                            $oReciboAvulso->adicionarRecurso(db_utils::fieldsMemory($rsRecurso, 0)->o58_codigo);
                        }
                        $oReciboAvulso->emiteRecibo();
                        $codigoRetencao = '';
                        if (APROPRIACAO_RETENCAO) {

                            $codigoRetencao = $oRetencao->e21_sequencial;
                            if ($receitaExtra) {
                                $oReciboAvulso->setExecutaLancamentoContabil(false);
                            }
                        }
                        $oReciboAvulso->autenticarRecibo($this->getDataBase(), $oNotaLiquidacao->oDadosOrdem->e60_concarpeculiar, $oNotaLiquidacao->oDadosOrdem->e50_numemp, $codigoRetencao);
                    }

                    $nValorRecibo = $oReciboAvulso->getTotalRecibo();
                    if ($nValorRecibo != $oRetencao->e23_valorretencao) {

                        $sMenuAcessado      = db_stdClass::getCaminhoMenu((int)db_getsession("DB_itemmenu_acessado"));
                        $sMsgValorRetencao  = "A retenção {$oRetencao->k02_codigo} com valor {$oRetencao->e23_valorretencao} é ";
                        $sMsgValorRetencao .= "diferente do valor total do recibo {$nValorRecibo}.\n\n{$sMenuAcessado}";
                        throw new Exception($sMsgValorRetencao);
                    }

                } else if ($oRetencao->e21_retencaotipocalc == 5 ) {

                    $sSqlCgm = "select cgc, numcgm from db_config where codigo = ".db_getsession("DB_instit");
                    $rsCgm   = db_query($sSqlCgm);
                    $oCgm    = db_utils::fieldsMemory($rsCgm, 0);
                    /*
                     * Consultamos o cnpj do credor da ordem de pagamento
                    */
                    $sSqlCnpjCredor = "select z01_cgccpf from cgm where z01_numcgm = {$oNotaLiquidacao->oDadosOrdem->z01_numcgm}";
                    $rsCnpjCredor   = db_query($sSqlCnpjCredor);
                    $oCgmCredor     = db_utils::fieldsMemory($rsCnpjCredor, 0);
                    if ($oCgmCredor->z01_cgccpf == "") {
                        throw new Exception("Não Foi possível efetuar a baixa da Retenção. Credor com CPF/CNPJ nulo ou inválido.");
                    }
                    require_once(modification('model/planilhaRetencao.model.php'));
                    require_once(modification('model/recibo.model.php'));
                    //Incluimos uma nova planilha de retencao

                    $oPlanilha     = new planilhaRetencao(null, $oCgm->numcgm);
                    $oNotaPlanilha = new \stdClass();

                    $oNotaPlanilha->sCnpj               = $oNotaLiquidacao->oDadosOrdem->z01_cgccpf;
                    $oNotaPlanilha->dtNota              = $oNotaLiquidacao->oDadosOrdem->e69_dtnota;
                    $oNotaPlanilha->sNumeroNota         = $oNotaLiquidacao->oDadosOrdem->e69_numero;
                    $oNotaPlanilha->nValor              = $oNotaLiquidacao->oDadosOrdem->e53_valor;
                    $oNotaPlanilha->sNome               = str_replace("'","\'",$oNotaLiquidacao->oDadosOrdem->z01_nome);
                    $oNotaPlanilha->nValorTotalRetencao = $oRetencao->e23_valorretencao;
                    $oNotaPlanilha->nValorBase          = $oRetencao->e23_valorbase;
                    $oNotaPlanilha->nValorDeducao       = $oRetencao->e23_deducao;
                    $oNotaPlanilha->nAliquota           = $oRetencao->e23_aliquota;
                    $oNotaPlanilha->iNotaLiquidacao     = $this->iNotaLiquidacao;
                    $oPlanilha->adicionaNota($oNotaPlanilha);
                    $oPlanilha->setDatausu($this->getDataBase());
                    $oPlanilha->gerarDebito($sHistoricoRecibo);
                    $iNumpre = $oPlanilha->getNumpre();

                    //Incluimos o recibo e o autenticamos.
                    $oReciboDebito = new recibo(2, $oCgm->numcgm, 25);
                    $oReciboDebito->addNumpre($iNumpre, 1);
                    $oDaoDotacao = new cl_orcdotacao;
                    $rsRecurso  = $oDaoDotacao->sql_record($oDaoDotacao->sql_query_file(
                            $oNotaLiquidacao->oDadosOrdem->e60_anousu,
                            $oNotaLiquidacao->oDadosOrdem->e60_coddot,
                            "o58_codigo"));
                    if ($oDaoDotacao->numrows == 1) {
                        $oReciboDebito->adicionarRecurso(db_utils::fieldsMemory($rsRecurso, 0)->o58_codigo);
                    }

                    $oReciboDebito->setConta($this->getConta());
                    $oReciboDebito->adicionarRecurso($oDotacao->getRecurso());
                    $oReciboDebito->setDataRecibo($this->getDataBase());
                    $oReciboDebito->setDataVencimentoRecibo($this->getDataBase());
                    $oReciboDebito->setGrupoAutenticacao($this->getGrupoAutenticacao());
                    $oReciboDebito->setHistorico(str_replace("'","",$sHistoricoRecibo));
                    $oReciboDebito->emiteRecibo();
                    $codigoRetencao = '';
                    if (APROPRIACAO_RETENCAO) {

                        $codigoRetencao = $oRetencao->e21_sequencial;
                        if ($receitaExtra) {
                            $oReciboDebito->setExecutaLancamentoContabil(false);
                        }
                    }

                    $oReciboDebito->autenticarRecibo($this->getDataBase(), $oNotaLiquidacao->oDadosOrdem->e60_concarpeculiar, $oNotaLiquidacao->oDadosOrdem->e50_numemp, $codigoRetencao);
                    $nValorRecibo = $oReciboDebito->getTotalRecibo();
                    if (($nValorRecibo == 0) || $nValorRecibo != $oRetencao->e23_valorretencao) {

                        $sMenuAcessado      = db_stdClass::getCaminhoMenu((int)db_getsession("DB_itemmenu_acessado"));
                        $sMsgValorRetencao  = "A retenção {$oRetencao->k02_codigo} com valor {$oRetencao->e23_valorretencao} é ";
                        $sMsgValorRetencao .= "diferente do valor total do recibo {$nValorRecibo}.\n\n{$sMenuAcessado}";
                        throw new Exception($sMsgValorRetencao);
                    }
                }

                /**
                 * Vinculamos a retencao ao grupo do lancamento
                 */
                $oDaoCorrenteGrupo = new cl_corgrupocorrente;
                $sSqlCorgrupo      = $oDaoCorrenteGrupo->sql_query_file(null,
                                                                            "*",
                                                                            "k105_autent desc limit 1",
                                                                            "k105_corgrupo = ".$this->getGrupoAutenticacao());
                $rsCorGrupo        = $oDaoCorrenteGrupo->sql_record($sSqlCorgrupo);
                if ($oDaoCorrenteGrupo->numrows == 0) {

                    throw new Exception("Não Foi possivel encontrar grupo de lancamentos.");
                }
                $oDaoRetencaoCorrente  = new cl_retencaocorgrupocorrente;
                $oDaoRetencaoCorrente->e47_corgrupocorrente = db_utils::fieldsMemory($rsCorGrupo,0)->k105_sequencial;
                $oDaoRetencaoCorrente->e47_retencaoreceita  = $oRetencao->e23_sequencial;
                $oDaoRetencaoCorrente->incluir(null);
                if ($oDaoRetencaoCorrente->erro_status == 0) {
                    throw new Exception("Não Foi possivel vincular a retencao a autenticação.\n{$oDaoRetencaoCorrente->erro_msg}");
                }
            }
        }
        return true;
    }

    /**
     * Retorna as retencoes do movimento
     *
     * @param integer $iCodMovimento Código do movimennto da Agenda
     * @param string $iTipoRetencao tipo do calculo da retencao
     * @param bool $lRecolhida
     * @param bool $lPrincipal
     * @return _db_fields|bool|stdClass|stdClass[]
     */
    function getRetencoesByMovimento($iCodMovimento, $iTipoRetencao='', $lRecolhida = false, $lPrincipal = false) {

        $sRecolhida = $lRecolhida?"true":"false";
        $oDaoRetencaoReceitas = new cl_retencaoreceitas;
        $sWhere               = "";
        if ($iTipoRetencao != '') {
            $sWhere .= " and e23_retencaotiporec = {$iTipoRetencao}";
        }
        if ($lPrincipal) {
            $sWhere .= " and e27_principal is true ";
        }
        $sSqlRetencaoReceitas = $oDaoRetencaoReceitas->sql_query_notas(null,
                "distinct tabrec.*,retencaotiporec.*,
                retencaoreceitas.*,e27_principal,k108_slip,null as k17_slip ",
                "e21_sequencial
                ",
                "e27_empagemov = {$iCodMovimento}
        and e23_recolhido = {$sRecolhida}
        and e23_ativo     = true
        and e71_anulado   = false {$sWhere}"
        );

        $rsRetencoes = $oDaoRetencaoReceitas->sql_record($sSqlRetencaoReceitas);
        $aRetencoes  = false;
        if ($oDaoRetencaoReceitas->numrows > 0) {

            if ($iTipoRetencao != "") {

                $aRetencoes = db_utils::fieldsMemory($rsRetencoes, 0, null,null, $this->lEncodeUrl);
            } else {
                $aRetencoes = db_utils::getCollectionByRecord($rsRetencoes, null,null, $this->lEncodeUrl);
            }
        }
        return $aRetencoes;
    }

    /**
     * Realiza o estorno da retencao, anulando seus recibos, e planilhas
     * , caso houver
     *
     * @param  integer $oRetencao Objeto com informaçõs da retencao
     * @return bool
     * @throws BusinessException
     * @throws DBException
     * @throws ParameterException
     */
    function estornarRetencoes($oRetencao) {

        if (!is_object($oRetencao)) {
            throw new Exception("Erro [1] - aRetenções deve ser um objeto");
        }

        $oRetencao     = $this->getRetencaoByCodigo($oRetencao->iRetencao, true);
        $iContaOriginal = $this->getConta();
        require_once(modification("model/recibo.model.php"));
        require_once(modification("model/Dotacao.model.php"));
        require_once(modification('classes/ordemPagamento.model.php'));
        $oNotaLiquidacao = new ordemPagamento($this->iNotaLiquidacao);
        $oNotaLiquidacao->getDadosOrdem();
        $empenho = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($oNotaLiquidacao->oDadosOrdem->e50_numemp);

        $oDaoRetencaoReceitas                 = new cl_retencaoreceitas;
        $oDaoRetencaoReceitas->e23_sequencial = $oRetencao->e23_sequencial;
        $oDaoRetencaoReceitas->e23_recolhido  = "true";
        $oDaoRetencaoReceitas->e23_ativo      = "false";
        $oDaoRetencaoReceitas->alterar($oRetencao->e23_sequencial);
        if ($oDaoRetencaoReceitas->erro_status == 0) {

            $sErroMsg  = "Erro [2] - Não Foi possível recolher a retenção ({$oRetencao->e23_sequencial})";
            throw new Exception($sErroMsg);
        }

        $oDotacao        = new Dotacao($oNotaLiquidacao->oDadosOrdem->e60_coddot,
        $oNotaLiquidacao->oDadosOrdem->e60_anousu
        );
        /*
         * Verificamos se a receita da retencao não e uma receita-extra(receita encontra-se na tabela tabplan)
        */
        if ($this->getTipoReceita($oRetencao->k02_codigo) == 2 && (!APROPRIACAO_RETENCAO)) {

            /*
             * Buscamos na tabela saltesextra para verificar se foi cadastrado uma conta
            * para realizar o recolhimento de receita.
            */

            $oDaoSaltesExtra = new cl_saltesextra;
            $sSqlContaExtra  = $oDaoSaltesExtra->sql_query_file(null, "*",null,"k109_saltes = {$iContaOriginal}");
            $rsContaExtra    = $oDaoSaltesExtra->sql_record($sSqlContaExtra);
            if ($oDaoSaltesExtra->numrows == 1) {

                $iContaExtra = db_utils::fieldsMemory($rsContaExtra, 0)->k109_contaextra;
                if (!empty($iContaExtra)) {

                    $this->setConta($iContaExtra);

                    /**
                     * Pesquisamos se foi incluido valores para fazer transferencia bancaria.
                     * caso exista transferencia, devemos deduzir o valor da retencao do valor a transferir
                     */
                    if ($oRetencao->e27_empagemov != "") {

                        $oDaoEmpageMovSlips = new cl_empagemovslips;
                        $sSqlSlipMovimento  = $oDaoEmpageMovSlips->sql_query_file(null,"*",null,"k107_empagemov = {$oRetencao->e27_empagemov}");
                        $rsMovimento        = $oDaoEmpageMovSlips->sql_record($sSqlSlipMovimento);
                        if ($oDaoEmpageMovSlips->numrows > 0) {

                            $oMovimentoSlip = db_utils::fieldsMemory($rsMovimento,0);
                            $nValorRetencao                      = round(($oMovimentoSlip->k107_valor + $oRetencao->e23_valorretencao),2);
                            $oDaoEmpageMovSlips->k107_valor      = "$nValorRetencao";
                            $oDaoEmpageMovSlips->k107_sequencial = $oMovimentoSlip->k107_sequencial;
                            $oDaoEmpageMovSlips->k107_ctacredito = $oMovimentoSlip->k107_ctacredito;
                            $oDaoEmpageMovSlips->k107_ctadebito = $oMovimentoSlip->k107_ctadebito;
                            $oDaoEmpageMovSlips->alterar($oMovimentoSlip->k107_sequencial);

                            if ($oDaoEmpageMovSlips->erro_status == 0) {

                                $sErroMsg  = "Erro [10] - Não Foi possível estornar o recolhimento a retenção ({$oRetencao->e23_sequencial})";
                                throw new Exception($sErroMsg);

                            }
                        }
                    }
                }
            }
        }

        require_once(modification('std/db_stdClass.php'));
        $oInstit = db_stdClass::getDadosInstit();

        if ($oRetencao->e23_valorretencao > 0) {

            $iTipo = "";
            if ($oRetencao->e21_retencaotipocalc == 5 && $oInstit->prefeitura == "f") {

                require_once(modification('model/planilhaRetencao.model.php'));
                $oReciboDebito = new  recibo(2, null,25);
                $oReciboDebito->setConta($this->getConta());
                $oReciboDebito->setGrupoAutenticacao($this->getGrupoAutenticacao());
                $iNumpre = retencaoNota::getNumpreRetencao($oRetencao->e23_sequencial);
                $oReciboDebito->adicionarRecurso($oDotacao->getRecurso());

                $sSqlBuscaCaracteristica = "select empempenho.e60_concarpeculiar
                                              from retencaoreceitas
                                                   inner join retencaoempagemov on retencaoempagemov.e27_retencaoreceitas = retencaoreceitas.e23_sequencial
                                                   inner join empagemov         on empagemov.e81_codmov = retencaoempagemov.e27_empagemov
                                                  inner join empempenho        on empempenho.e60_numemp = empagemov.e81_numemp
                                              where retencaoreceitas.e23_sequencial = {$oRetencao->e23_sequencial}";
                $sCaracteristicaPeculiar = db_utils::fieldsMemory(db_query($sSqlBuscaCaracteristica), 0)->e60_concarpeculiar;

                if (APROPRIACAO_RETENCAO) {
                    $codigoRetencao = $oRetencao->e21_sequencial;
                    if ($oRetencao->k02_tipo === 'E') {
                        $oReciboDebito->setExecutaLancamentoContabil(false);
                    }
                }
                $oReciboDebito->estornarRecibo($iNumpre, $sCaracteristicaPeculiar, $oNotaLiquidacao->oDadosOrdem->e50_numemp, $codigoRetencao, 8);
                $oPlanilha = retencaoNota::getPlanilhaRetencao($oRetencao->e23_sequencial);
                if ($oPlanilha) {
                    $oPlanilhaRetencao  = new planilhaRetencao($oPlanilha->q20_planilha);
                    $oPlanilhaRetencao->anularPlanilha("Estorno de recolhimento de Retenção");
                }
            } else {

                       // echo 'seg'; die();
                       $oReciboAvulso = new recibo(1, $this->getNumCgm());
                       $oReciboAvulso->setConta($this->getConta());
                       $oReciboAvulso->setGrupoAutenticacao($this->getGrupoAutenticacao());
                       $oReciboAvulso->adicionarRecurso($oDotacao->getRecurso());
                       if (isset($oReciboAvulso)) {
                           $iNumpre = retencaoNota::getNumpreRetencao($oRetencao->e23_sequencial);
                           $sSqlBuscaCaracteristica = "select empempenho.e60_concarpeculiar
                                                         from retencaoreceitas
                                                              inner join retencaoempagemov on retencaoempagemov.e27_retencaoreceitas = retencaoreceitas.e23_sequencial
                                                              inner join empagemov         on empagemov.e81_codmov = retencaoempagemov.e27_empagemov
                                                              inner join empempenho        on empempenho.e60_numemp = empagemov.e81_numemp
                                                        where retencaoreceitas.e23_sequencial = {$oRetencao->e23_sequencial}";
                           $sCaracteristicaPeculiar = db_utils::fieldsMemory(db_query($sSqlBuscaCaracteristica), 0)->e60_concarpeculiar;
                           $codigoRetencao = null;


                           if (APROPRIACAO_RETENCAO) {

                               $codigoRetencao = $oRetencao->e21_sequencial;
                               if ($oRetencao->k02_tipo === 'E') {
                                   $oReciboAvulso->setExecutaLancamentoContabil(false);
                               }
                           }
                           $oReciboAvulso->estornarRecibo($iNumpre, $sCaracteristicaPeculiar, $oNotaLiquidacao->oDadosOrdem->e50_numemp, $codigoRetencao);



                           /**
                            * alteracao no estorno do recibo devido a apropriacao
                            * normalmente ele retorna o debito do recibo na arrecad isso não pode acontecer
                            * na apropriacao pois ele gera outro debito devido ao valor poder ser diferente
                            * deveremos gerar as cancdebitos e remover da arrecad
                            */
                           if ($codigoRetencao != "" && APROPRIACAO_RETENCAO) {

                             $oRetencao = (object)$oRetencao;
                             $numeroEmpenho = "{$empenho->getCodigo()}/{$empenho->getAno()}";
                             $sMensagemCancelamento = "Estorno pagto do emprenho {$numeroEmpenho} retenção.";

                             $receita = $oRetencao->e21_receita;

                             $aDebitos = [];
                             $aDadosDebitos = array();
                             $aDadosDebitos['Numpre']  = $iNumpre;
                             $aDadosDebitos['Numpar']  = 1;
                             $aDadosDebitos['Receita'] = $receita;

                             $aDebitos[] = $aDadosDebitos;

                             $oCancelamentoDebitos = new cancelamentoDebitos;
                             $oCancelamentoDebitos->setArreHistTXT($sMensagemCancelamento);
                             $oCancelamentoDebitos->setTipoCancelamento(2);
                             $oCancelamentoDebitos->setCadAcao(1);
                             $oCancelamentoDebitos->cancelaReciboRetencao($aDebitos);

                           }

               }

            }


            /**
             * Vinculamos a retencao ao grupo do lancamento
             */
            $oDaoCorrenteGrupo = new cl_corgrupocorrente;
            $sSqlCorgrupo      = $oDaoCorrenteGrupo->sql_query_file(null,
                    "*",
                    "k105_autent desc limit 1",
                    "k105_corgrupo = ".$this->getGrupoAutenticacao());
            $rsCorGrupo        = $oDaoCorrenteGrupo->sql_record($sSqlCorgrupo);
            if ($oDaoCorrenteGrupo->numrows == 0) {

                throw new Exception("Não Foi possivel encontrar grupo de lancamentos.");
            }
            $oDaoRetencaoCorrente  = new cl_retencaocorgrupocorrente;
            $oDaoRetencaoCorrente->e47_corgrupocorrente = db_utils::fieldsMemory($rsCorGrupo,0)->k105_sequencial;
            $oDaoRetencaoCorrente->e47_retencaoreceita  = $oRetencao->e23_sequencial;
            $oDaoRetencaoCorrente->incluir(null);
            if ($oDaoRetencaoCorrente->erro_status == 0) {
                throw new Exception("Não Foi possivel vincular a retencao a autenticação.\n{$oDaoRetencaoCorrente->erro_msg}");
            }
        }
        return true;
    }

    /**
     * retorna o numpre que a retencao foi autenticada
     *
     * @param integer $iRetencao código da retencao (retencaoreceitas.e23_sequencial);
     * @return integer
     */
    public static function getNumpreRetencao($iRetencao, $tipo = 3) {

        $oDaoRetencaoCorrente = new cl_retencaocorgrupocorrente;
        $iNumpre              = 0;
        $sSqlNumpre           = $oDaoRetencaoCorrente->sql_query_numpre(null,
                "max(k12_numpre) as k12_numpre",
                null,
                "e23_sequencial = {$iRetencao}
        and k105_corgrupotipo = {$tipo}"
        );

        $rsNumpre = $oDaoRetencaoCorrente->sql_record($sSqlNumpre);
        if ($oDaoRetencaoCorrente->numrows == 1) {

            $iNumpre = db_utils::fieldsMemory($rsNumpre, 0)->k12_numpre;
        }
        return $iNumpre;
    }

    /**
     * Retorna o codigo da planilha de reclhimentos da retencao
     *
     * @param integer $iRetencao codigo da retencao (retencaoreceitas.e23_sequencial)
     * @return _db_fields|stdClass codigo da planilha
     */
    public static function getPlanilhaRetencao($iRetencao, $tipo = 3) {

        $sSqlPlanilha  = "select * ";
        $sSqlPlanilha .= "  from issplan      ";
        $sSqlPlanilha .= " where q20_numpre = ". retencaoNota::getNumpreRetencao($iRetencao, $tipo);
        $rsPlanilha    = db_query($sSqlPlanilha);
        $oPlanilha      = false;
        if (pg_num_rows($rsPlanilha) > 0) {

            $oPlanilha = db_utils::fieldsMemory($rsPlanilha, 0);
        }
        return $oPlanilha;
    }

    /**
     * Define o grupo de autenticação
     *
     * @param integer $iCorGrupo
     */
    function setGrupoAutenticacao($iCorGrupo) {
        $this->iGrupoAutenticacao = $iCorGrupo;
    }

    /**
     * Retorna o grupo de autenticação
     *
     * @return unknown
     */
    function getGrupoAutenticacao() {
        return $this->iGrupoAutenticacao;
    }

    /**
     * Define a conta pagadora
     *
     * @param integer $iConta
     */
    function setConta($iConta) {
        $this->iConta = $iConta;
    }

    /**
     * Retorna a conta definida pelo usuario
     *
     * @return integer
     */
    function getConta() {
        return $this->iConta;
    }

    /**
     * Define o cgm da retencao. é usando para informacao na emissao dos recibos e planilhas
     *
     * @param integer $iNumCgm
     */
    function setNumCgm ($iNumCgm) {
        $this->iNumCgm = $iNumCgm;
    }

    /**
     * Retorna o cgm informado.
     *
     * @return integr
     */
    function getNumCgm() {
        return $this->iNumCgm;
    }

    /**
     * Busca uma conta pagadora para a apropriação compatível com o empenho.
     * Caso não encontre, retorna do recurso livre.
     *
     * @param integer $codigoRecurso
     * @return integer
     */
    public function buscaSugestaoContaPagadora($codigoRecurso)
    {
        $ano = db_getsession('DB_anousu');
        $instit = db_getsession('DB_instit');

        // busca todos os recursos compatíveis com o recurso do empenho
        $sqlFiltraRecurso = "
            select o15_codigo
              from ( select o15_recurso, gestao
                      from orctiporec
                      join fonterecurso on orctiporec_id = o15_codigo and exercicio = {$ano}
                     where o15_codigo = {$codigoRecurso}
                   ) as x
              join fonterecurso on exercicio = {$ano}
                   and x.gestao = fonterecurso.gestao
              join orctiporec on fonterecurso.orctiporec_id = orctiporec.o15_codigo
                   and x.o15_recurso = orctiporec.o15_recurso
        ";

        $sql = $this->getSqlSugereConta($ano, $instit, $sqlFiltraRecurso);
        $rs = db_query($sql);
        // se não encontrar nenhuma conta, retorna vazio
        if ($rs && pg_num_rows($rs) === 0) {
            return '';
        }

        $rs = db_query($sql);
        return db_utils::fieldsMemory($rs, 0)->e83_codtipo;
    }

    /**
     * Configura o pagamento das retenções ativas do movimento
     * Cria um movimento novo com as informações do movimento original,
     * e configura o movimento atual com as informações das retenções
     *
     */
    function configurarPagamentoRetencoes() {
        if (!db_utils::inTransaction()) {
            throw new Exception("Erro [0 - RetencaoNotas] - Não Existe transação ativa");
        }


        $iCodigoMovimento = $this->getCodigoMovimento();
        if ($iCodigoMovimento == null) {
            throw new Exception("Erro [2] - Movimento não Informado");
        }

        $aRetencoes          = $this->getRetencoesFromDB($this->iNotaLiquidacao,false);
        $nValorTotalRetencao = $this->getValorRetencaoMovimento($iCodigoMovimento,false);

        if ($nValorTotalRetencao == 0) {
            throw new Exception("Erro [2] - Movimento sem Retenções");
        }

        require_once(modification(Modification::getFile('model/agendaPagamento.model.php')));
        $oAgendaPagamento    = new agendaPagamento();
        $sJoin  = " left join empagenotasordem on e81_codmov  = e43_empagemov     ";
        $sJoin .= " left join empageordem      on e43_ordempagamento = e42_sequencial ";
        $oMovimento = $oAgendaPagamento->getMovimentosAgenda("e81_codmov = {$iCodigoMovimento}", $sJoin, false,false);
        /**
         * Quando não encontrar existir uma conta pagadora para a instituição, busca uma compativel com o recurso
         * do empenho
         */
        foreach ($oMovimento as $index => $movimento) {
            if (empty($movimento->e85_codtipo)) {
                $oMovimento[$index]->e85_codtipo = $this->buscaSugestaoContaPagadora($movimento->o15_codigo);
            }
        }

        $oAgendaPagamento->setCodigoAgenda($oMovimento[0]->e80_codage);
        /**
         * Verificamos se o movimento já está configurado, caso nao esteje, devemos cancelar a operação
         */
        $formaPagamento = '';

        if (!APROPRIACAO_RETENCAO) {
            $sSqlForma = "select e97_codforma from empagemovforma where e97_codmov = {$iCodigoMovimento}";

            $rsForma = db_query($sSqlForma);

            $iCodForma = db_utils::fieldsMemory($rsForma, 0)->e97_codforma;
            if ($iCodForma == '') {
                $sMsg = "Erro [7] - Movimento não configurado\n";
                $sMsg .= "As retenções só poderão ter seu pagamento configurado após atualização do movimento ";
                $sMsg .= "através do menu Agenda de Pagamento > Manutenção de Pagamentos.";
                throw new Exception($sMsg);
            }
            $sSqlContaPagadora = "select e85_codtipo from empagepag where e85_codmov = {$iCodigoMovimento}";
            $rsContaPagadora = db_query($sSqlContaPagadora);
            $iContaPagadora = db_utils::fieldsMemory($rsContaPagadora, 0)->e85_codtipo;
            if ($iContaPagadora == '') {
                throw new Exception("Erro [7] - Movimento sem conta pagadora configurada");
            }

            $formaPagamento = $oMovimento[0]->e97_codforma;
        }

       // desconta o valor anulado
       // eese valor de desconto ja foi tratado na liquidação do empenho, ali quando cria a empagemov
       // ja é aplicado o desconto
        $valorLiquidoMovimento = round($oMovimento[0]->e81_valor -
                                       $nValorTotalRetencao -
                                       $oMovimento[0]->e53_vlranu, 2);

        $oNovoMovimento = new stdClass();
        $oNovoMovimento->iCodTipo = $oMovimento[0]->e85_codtipo;
        $oNovoMovimento->iNumEmp  = $oMovimento[0]->e60_numemp;
        $oNovoMovimento->nValor   = 0;
        $oNovoMovimento->iCodNota = $oMovimento[0]->e50_codord;
        $oNovoMovimento->iForma   = $formaPagamento;

        $iCodigoNovoMovimento     = $oAgendaPagamento->addMovimentoAgenda(1, $oNovoMovimento);
        $this->setCodigoNovoMovimento($iCodigoNovoMovimento);

        $dao = new cl_empagemovretencoes();
        $dao->e145_pagordem_id = $oMovimento[0]->e50_codord;
        $dao->e145_movimento_original = $iCodigoMovimento;
        $dao->e145_movimento_retencao = $iCodigoNovoMovimento;
        $dao->e145_valor_retencao = $nValorTotalRetencao;
        $dao->incluir();

        if ($dao->erro_status == 0) {
            throw new Exception("Não foi possível vincular a origem da retenção");
        }

        /*
         * Alteramos o movimento atual com as informações do pagamento a retencao
        */
        $oDaoEmpageMov             = new cl_empagemov;
        $oDaoEmpageMov->e81_codmov = $iCodigoMovimento;
        $oDaoEmpageMov->e81_valor  = $valorLiquidoMovimento;
        $oDaoEmpageMov->alterar($iCodigoMovimento);
        if ($oDaoEmpageMov->erro_status == 0) {
            throw new Exception("Erro [5] - Erro ao Configurar retenções. Não Foi Possível configurar movimento.");
        }
        /*
         * Incluimos empageconf para o movimento novo,
         */
        $oDaoEmpageConf  = new cl_empageconf;
        $oDaoEmpageConf->e86_cheque  = "0";
        $oDaoEmpageConf->e86_data    = $oMovimento[0]->e80_data;
        $oDaoEmpageConf->e86_codmov  = $iCodigoNovoMovimento;
        $oDaoEmpageConf->e86_correto = "true";
        $oDaoEmpageConf->incluir($iCodigoNovoMovimento);
        if ($oDaoEmpageConf->erro_status == 0) {
            throw new Exception("Erro [6:retencaoNota] - Erro ao Configurar retenções. Não Foi Possível configurar movimento.");
        }
        /**
         * Verificamos se o movimento nao esta em nenhuma ordem de pagamento.
         */

        if ($oMovimento[0]->e43_sequencial != "") {
            $sUpdateOrdemPag  = " update empagenotasordem set e43_valor = ". $valorLiquidoMovimento;
            $sUpdateOrdemPag .= " where  e43_empagemov = {$iCodigoMovimento}";
            $rsUpdateOrdemPag = db_query($sUpdateOrdemPag);
            if (!$rsUpdateOrdemPag) {
                throw new Exception("Erro [4] - Erro ao Configurar Ordem de pagamento. Não Foi Possivel vincular Ordem de pagamento Auxiliar.");
            }
        }
        /**
         * Verificamos se o usuario já configurou a forma de pagamento.
         * Caso já esteja configurado , modificamos para a forma de debito em conta;
         * senao devemso incluir novamente.
         */
        if (empty($oNovoMovimento->iForma)) {
            $oDaoEmpagemovForma = new cl_empagemovforma;
            $oDaoEmpagemovForma->e97_codforma = 4;
            $oDaoEmpagemovForma->incluir($iCodigoNovoMovimento);
            if ($oDaoEmpagemovForma->erro_status == 0) {
                $sErroMsg = "Erro [6] - Erro Ao Definir forma de pagamento  (Movimento {$iCodigoNovoMovimento})";
                throw new Exception($sErroMsg);
            }
        }
        /*
         * Iteramos sobre as retencoes informadas para vincular o movimento novo as retenções.
        */
        $oDaoRetencaoEmpageMov = new cl_retencaoempagemov();
        foreach ($aRetencoes as $oRetencao) {
            $oDaoRetencaoEmpageMov->e27_empagemov        =  $iCodigoNovoMovimento;
            $oDaoRetencaoEmpageMov->e27_retencaoreceitas = $oRetencao->e23_sequencial;

            $where = " e27_empagemov = {$iCodigoMovimento} and e27_retencaoreceitas = {$oRetencao->e23_sequencial}";
            $oDaoRetencaoEmpageMov->alterar(null, $where);
            if ($oDaoRetencaoEmpageMov->erro_status == 0) {
                throw new Exception("Erro [7:retencaoNota] - Erro ao vincular retenções ao movimento.\nErro:{$oDaoRetencaoEmpageMov->erro_msg}");
            }
        }

        $oEmpenhoFinanceiro = new EmpenhoFinanceiro($oMovimento[0]->e60_numemp);

        if (APROPRIACAO_RETENCAO) {

            $oDaoEmpAgePag = new cl_empagepag();
            $oDaoEmpAgePag->excluir($iCodigoMovimento);

            $oApropriacao = new Apropriacao($oEmpenhoFinanceiro, db_getsession('DB_anousu'));
            $sDataEvento  = new \DateTime(date('Y-m-d', db_getsession('DB_datausu')));
            $oApropriacao->setDataEvento($sDataEvento);
            $oApropriacao->apropriar($this->iCodNota, $this->iNotaLiquidacao, null, $iCodigoNovoMovimento);
        }
	}


	/**
	 * verifica o tipo da receita
	 * retorna 1 para orcamentaria
	 *         2 para extra-orcamentaria
	 *
	 * @param integer $iReceita codigo da receita tabrec.k02_codigo
	 * @return integer
	 */
	function getTipoReceita($iReceita) {


		$sSqlTipoRec  = " SELECT k02_tipo                                                   ";
		$sSqlTipoRec .= "  from tabrec                                                      ";
		$sSqlTipoRec .= "       inner join tabplan on tabrec.k02_codigo = tabplan.k02_codigo ";
		$sSqlTipoRec .= " where tabrec.k02_codigo = {$iReceita} and k02_anousu = ".db_getsession("DB_anousu");
		$rsTipoRec   = db_query($sSqlTipoRec);
		if (pg_num_rows($rsTipoRec) == 1) {
			$iTipoReceita = 2;
		} else {
			$iTipoReceita = 1;
		}
		return $iTipoReceita;
	}

	/**
	 * Retorna as retencoes pelo codigo de lancamento
	 *
	 * @param integer $iCodigoRetencao codigo da retencao (retencaoreceitas.e23_sequencial)
	 * @param boolean $lRecolhida retencao recolhida ou nao
	 * @return array collection de retencoes
	 */
	function getRetencaoByCodigo($iCodigoRetencao, $lRecolhida = false) {

		$sRecolhida = $lRecolhida?"true":"false";
		$oDaoRetencaoReceitas = new cl_retencaoreceitas;
		$sWhere               = "";
		$sWhere              .= " and e27_principal is true ";
		$sSqlRetencaoReceitas = $oDaoRetencaoReceitas->sql_query_notas(null,
				"distinct tabrec.*,retencaotiporec.*,
				retencaoreceitas.*,e27_principal,k108_slip,e27_empagemov ",
				"e21_sequencial
				",
				"e23_sequencial = {$iCodigoRetencao}
		and e23_recolhido = {$sRecolhida}
		and e23_ativo     = true
		and e71_anulado   = false {$sWhere}"
		);

		$rsRetencoes = $oDaoRetencaoReceitas->sql_record($sSqlRetencaoReceitas);
		$oRetencao   = false;
		if ($oDaoRetencaoReceitas->numrows > 0) {
			$oRetencao = db_utils::fieldsMemory($rsRetencoes, 0, null,null, $this->lEncodeUrl);
		}
		return $oRetencao;
	}

	/**
	 * Retorna se a nota possui retencoes do mes anterior
	 * @return boolean
	 */
	function hasRetencoesMesAnterior() {

		$lRetorno = false;
		if ($this->getCodigoMovimento() != null) {

			$iCodMov = $this->getCodigoMovimento();
			$sSqlTotalRetencao = "select fc_validaretencoesmesanterior({$iCodMov},null) as validar";
			$oRetorno = db_utils::fieldsMemory(db_query($sSqlTotalRetencao), 0);
			$lRetorno = $oRetorno->validar == "t"?true:false;

		}
		return $lRetorno;

	}

	/**
	 * Valida se o valor total de todas as retenções da nota não ultrapassa o valor total da nota.
	 * @param stdClass $oNovaRetencao Objeto contando as informações de uma nova retenção que está sendo adicionada.
	 *
	 * @return bool True caso o valor total da retenção seja compatível com o valor da nota.
	 */
	public function validaValorRetencoes($oNovaRetencao = null) {

        $sql = "
        select e21_retencaotipocalc, e21_receita
          from retencaopagordem
          join pagordemnota on pagordemnota.e71_codord = retencaopagordem.e20_pagordem
          join empenho.retencaoreceitas on retencaoreceitas.e23_retencaopagordem = retencaopagordem.e20_sequencial
          join retencaotiporec ON retencaotiporec.e21_sequencial = retencaoreceitas.e23_retencaotiporec
          join empenho.retencaoempagemov on retencaoempagemov.e27_retencaoreceitas = retencaoreceitas.e23_sequencial
          join empenho.empagemov on empagemov.e81_codmov = retencaoempagemov.e27_empagemov
         where e20_pagordem = {$this->codigoOrdem}
           and e81_cancelado is null
           and e23_ativo is true
        ";

        $rs = db_query($sql);

        if (pg_num_rows($rs) > 0 && !is_null($oNovaRetencao)) {
            while ($data = pg_fetch_assoc($rs)) {
                if ($data['e21_retencaotipocalc'] != 6
                    && $data['e21_retencaotipocalc'] == $oNovaRetencao->tipoCalculo) {
                    $str = "Para esta nota, já foram lançadas retenções do mesmo tipo de cálculo e as mesmas ";
                    $str .="já foram pagas/apropriadas.";
                    throw new Exception($str);
                }
            }
        }

        /**
         * @buscar as retenções da nota salva no banco e validar se não esta lançando a mesma
         */
		$nValorNota        = 0;
		$nValorRetidoTotal = 0;
		if ($oNovaRetencao != null) {
			$nValorNota = $oNovaRetencao->nValorNota;
		}

		$aRetencoes = $this->getRetencoes();

		//Não há o que validar.
		if ($oNovaRetencao == null && empty($aRetencoes)) {
			return true;
		}

		if (!empty($aRetencoes)) {
			$nValorNota = current($aRetencoes)->e23_valor;
		}

		foreach ($aRetencoes as $oRetencao) {
			//Pula retenção se a "nova" já existe no array, pois esta será alterada pela nova.
			if (!is_null($oNovaRetencao) &&
                $oRetencao->e21_sequencial == $oNovaRetencao->iCodigoRetencao) {
                throw new Exception("Você já lançou essa retenções para essa nota.");
			}

            if (!is_null($oNovaRetencao) &&
                $oNovaRetencao->tipoCalculo != 6 &&
                $oRetencao->e21_retencaotipocalc == $oNovaRetencao->tipoCalculo) {
                throw new Exception("Você não pode adicionar retenções do mesmo tipo de cálculo.");
            }

			$nValorRetidoTotal += $oRetencao->e23_valorretencao;
		}

		if ($oNovaRetencao != null) {
			$nValorRetidoTotal += $oNovaRetencao->nValorRetencao;
		}

		return round($nValorRetidoTotal, 2) <= round($nValorNota, 2);
	}

    /**
     * retorna a receita contabil vinculada a receita
     * @param $receita
     * @param $ano
     * @param $instituicao
     * @return null
     * @throws Exception
     */
	public static function getContaContabilDaReceitaDaTesouraria($receita, $ano, $instituicao)
    {
        $daoTabrec = new cl_tabrec;
        $where = "tabrec.k02_codigo = {$receita}";
        $sqlContaContabil = $daoTabrec->sql_query_conta_contabil($ano, $instituicao, "c61_reduz", $where);
        $rsReceitaCOntabil = db_query($sqlContaContabil);
        if (!$rsReceitaCOntabil) {
            throw new \Exception("Erro ao pesquisar dados da conta Contabil da Receita da tesouraria {$receita}");
        }
        if (pg_num_rows($rsReceitaCOntabil) == 0) {
            return null;
        }
        return db_utils::fieldsMemory($rsReceitaCOntabil, 0)->c61_reduz;
    }

    public function validaDataEFD($oRetencao, $evento) {
        $instit = db_getsession('DB_instit');
        $daoDataEnvioEFD = new cl_dataenvioefd;
        $where = array();
        $where[] = "efd06_arquivo = '{$evento}'";
        $where[] = "(efd06_dataenvio::date) <= ('{$this->dtDataBase}'::date)";
        $where[] = "efd06_instituicao = {$instit}";
        $sqlDataEnvioEFD = $daoDataEnvioEFD->sql_query(null, "efd06_dataenvio", null, implode(' AND ', $where));
        $rsDataenvioEFD = db_query($sqlDataEnvioEFD);

        if(!$rsDataenvioEFD) {
            return false;
        }

        $envioValido = pg_num_rows($rsDataenvioEFD) == 1;
        $pessoaJuridica = strlen($oRetencao->iCpfCnpj) != 14 ? false : true;

        if ($envioValido && isset($oRetencao->dadosReinf) && empty($oRetencao->dadosReinf->tipoServicoNotaFiscal) && $pessoaJuridica) {
            $oDaoRetencao = new cl_retencaotiporec;
            $sSqlRetencao = $oDaoRetencao->sql_query($oRetencao->iCodigoRetencao,"retencaotiporec.*");
            $rsRetencao   = $oDaoRetencao->sql_record($sSqlRetencao);
            if ($oDaoRetencao->numrows == 0) {
                return false;
            }
            $oDadosRetencao   = db_utils::fieldsMemory($rsRetencao, 0);
            if ($oDadosRetencao->e21_retencaotipocalc == 4) {
                $dataDeEnvioValido = db_utils::fieldsMemory($rsDataenvioEFD, 0);
                return $dataDeEnvioValido->efd06_dataenvio;
            }
        }
        return false;
    }

    public function setCodigoOrdem($codigoOrdem)
    {
        $this->codigoOrdem = $codigoOrdem;
    }

    /**
     * @param $ano
     * @param $instit
     * @param $sqlFiltraRecurso
     * @return string
     */
    public function getSqlSugereConta($ano, $instit, $sqlFiltraRecurso)
    {
        $sql = "
            select e83_codtipo
                from conplano
                join conplanoreduz on (c61_codcon, c61_anousu) = (c60_codcon, c60_anousu)
                join saltes on saltes.k13_conta = (c61_reduz)
                join empagetipo on empagetipo.e83_conta = saltes.k13_conta
               where c60_estrut like '111%'
                 and c60_anousu = {$ano}
                 and c61_instit = {$instit}
                 and c61_codigo in ({$sqlFiltraRecurso})
                 limit 1
            ";
        return $sql;
    }

    private function vincularRetencaoNaturezaRendimento($idRetencaoReceita, $idNaturezaRendimento)
    {
        $dao = new cl_retencaonaturezarendimento();
        $dao->e168_retencaoreceitas = $idRetencaoReceita;
        $dao->e168_naturezarendimento = $idNaturezaRendimento;
        $dao->incluir(null);
        if ($dao->erro_status == 0) {
            throw new Exception($dao->erro_msg);
        }
    }
}
