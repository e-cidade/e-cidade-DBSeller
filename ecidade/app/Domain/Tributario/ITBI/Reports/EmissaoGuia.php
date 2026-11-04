<?php

namespace App\Domain\Tributario\ITBI\Reports;

abstract class EmissaoGuia
{
    /**
     * @var \db_impcarne|null
     */
    private $pdf;

    /**
     * @var string
     */
    protected $arquivo;

    /**
     * @var object
     */
    protected $dados;

    /**
     * @var integer
     */
    protected $numeroGuia;

    /**
     * @var DefaultSession
     */
    protected $defaultSession;

    /**
     * @var regraEmissao
     */
    protected $regraEmissao;

    /**
     * @var integer
     */
    protected $instituicao;

    /**
     * @var integer
     */
    protected $datausu;

    /**
     * @var integer
     */
    protected $anousu;

    /**
     * @var convenio
     */
    protected $convenio;

    /**
     * @var boolean
     */
    private $mostraArquivo = false;

    public function __construct(\regraEmissao $regraEmissao)
    {
        $this->pdf = $regraEmissao->getObjPdf();
    }

    /**
     * @param $label
     * @param $value
     */
    protected function setAtributo($label, $value)
    {
        $this->pdf->$label = $value;
    }

    /**
     * @return string
     */
    public function getArquivo()
    {
        return $this->arquivo;
    }

    /**
     * @param string $arquivo
     */
    private function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;
    }

    /**
     * @param bool $mostraArquivo
     */
    public function setMostraArquivo($mostraArquivo)
    {
        $this->mostraArquivo = $mostraArquivo;
    }

    /**
     * @param string $label
     * @param string | integer | float | boolean | object | array $value
     */
    public function setDados($label, $value)
    {
        if (!isset($this->dados)) {
            $this->dados = new \stdClass();
        }

        $this->dados->$label = $value;
    }

    /**
     * Método que gera o PDF do recibo após
     */
    protected function gerar()
    {
        $this->carregaAtributos();

        $sNomeArquivo = md5($this->pdf->itbi).".pdf";
        $sLocalArquivo = ECIDADE_PATH."tmp/{$sNomeArquivo}";

        $this->pdf->imprime();

        if ($this->mostraArquivo) {
            $this->pdf->objpdf->output($sLocalArquivo);
        }

        $this->pdf->objpdf->output($sLocalArquivo, false, true);

        $sArquivo = base64_encode(file_get_contents($sLocalArquivo));

        unlink($sLocalArquivo);

        $this->setArquivo($sArquivo);
    }

    /**
     * Carrega os atributos que o mod_imprime espera para a geração do recibo
     */
    private function carregaAtributos()
    {
        // Instituição
        $this->setAtributo("nomeinst", $this->dados->oInstituicao->nomeinst);
        $this->setAtributo("logoitbi", $this->dados->oInstituicao->logo);
        $this->setAtributo("cgc", $this->dados->oInstituicao->cgc);

        // Recibo
        $this->setAtributo("nomeDepartamento", $this->dados->oDepart->descrdepto);
        $this->setAtributo("lUtilizaModeloDefault", true);
        $this->setAtributo("dataemissao", str_replace("-", "/", $this->datausu));
        $this->setAtributo("ano", $this->anousu);
        $this->setAtributo("it04_descr", $this->dados->oItbi->it04_descr);
        $this->setAtributo("tipoitbi", (!empty($this->dados->oItbi->it05_guia) ? "urbano" : "rural"));
        $this->setAtributo("processo", $this->dados->oItbi->it01_processo);
        $this->setAtributo("titularProcesso", $this->dados->oItbi->it01_tituprocesso);
        $this->setAtributo("dataProcesso", $this->dados->oItbi->it01_dtprocesso);
        $this->setAtributo("it18_nomelograd", $this->dados->oItbi->it18_nomelograd);

        if ($this->dados->bLiberado) {
            $this->setAtributo("numpreitbi", $this->dados->iNumpre);
            $this->setAtributo("linha_digitavel", $this->convenio->getLinhaDigitavel());
            $this->setAtributo("codigo_barras", $this->convenio->getCodigoBarra());
            $this->setAtributo("usuarioCodigoLiberado", "");
            $this->setAtributo("usuarioNomeLiberado", "");
            $this->setAtributo("codigobarras", $this->dados->codigoDeBarrasPix);

            if ($this->regraEmissao->isCobranca()) {
                $this->setAtributo("nosso_numero", $this->convenio->getNossoNumero());
                $this->setAtributo("agencia_cedente", $this->convenio->getAgenciaCedente());
                $this->setAtributo("carteira", $this->convenio->getCarteira());
                $this->setAtributo("tipo_convenio", $this->convenio->getTipoConvenio());
                $this->setAtributo("imagemlogo", $this->convenio->getImagemBanco());
                $sNumbanco = "{$this->convenio->getBanco()->db90_codban}-{$this->convenio->getBanco()->db90_digban}";
                $this->setAtributo("descr9", "{$this->dados->iNumpre}001");
                $this->setAtributo("numbanco", $sNumbanco);
                $this->setAtributo("valor_cobrado", db_formatar($this->dados->oItbi->it14_valorpaga, "f"));
                $this->setAtributo("tipo_exerc", "{$this->dados->oArretipo->k00_descr} / {$this->anousu}");
                $this->setAtributo("descr12_1", $this->dados->sHistorico);
                $this->setAtributo("desconto_abatimento", db_formatar($this->dados->desconto_abatimento, "f"));
                $this->setAtributo("aTaxas2", $this->dados->aTaxas2);
                $this->setAtributo("aTaxas", $this->dados->aTaxas);
                $this->setAtributo("descr11_1", $this->dados->oAdquirente->z01_nome);
                $sEnderecoComprador = "{$this->dados->oAdquirente->it03_endereco}, ";
                $sEnderecoComprador .= "{$this->dados->oAdquirente->it03_bairro}";
                $this->setAtributo("descr11_2", $sEnderecoComprador);
                $this->setAtributo("munic", $this->dados->oAdquirente->it03_munic);
                $this->setAtributo("ufcgm", $this->dados->oAdquirente->it03_uf);
                $this->setAtributo("cep", $this->dados->oAdquirente->it03_cep);
                $this->setAtributo("cnpjBeneficiario", db_formatar($this->dados->oInstituicao->cgc, "cnpj"));
                $this->setAtributo("prefeitura", $this->dados->oInstituicao->nomeinst);
                $sEnderecoInstit = "{$this->dados->oInstituicao->ender} - ";
                $sEnderecoInstit .= "{$this->dados->oInstituicao->munic}/{$this->dados->oInstituicao->uf} - ";
                $sEnderecoInstit .= "CEP: {$this->dados->oInstituicao->cep}  ";
                $this->setAtributo("enderecoInstituicao", $sEnderecoInstit);
                $this->setAtributo("especie", "R$");
                $sFormaPagamento = ($this->dados->bLiberado ? $this->dados->sFormaPagamento : "");
                $this->setAtributo("sFormaPagamento", $sFormaPagamento);
                $this->setAtributo("sMensagemCaixa", null);
                $this->setAtributo("descr10", "1 / 1");
                $this->setAtributo("dtparapag", db_formatar($this->dados->oItbi->it14_dtvenc, "d"));
                $this->setAtributo("data_processamento", db_formatar($this->datausu, "d"));
                $this->setAtributo("numero_emissoes", count($this->dados->aItbinumpre));
            }

            $this->setAtributo("sMsgSituacaoImovel", $this->dados->sMsgSituacaoImovel);
            $this->setAtributo("sMensagemRecibo", $this->dados->oArretipo->k00_msgrecibo);
        }


        // Dados ITBI
        $this->setAtributo("lLiberado", $this->dados->bLiberado);
        $this->setAtributo("itbi", $this->numeroGuia);
        $this->setAtributo("datavencimento", $this->dados->oItbi->it14_dtvenc);
        $this->setAtributo("it01_valorterreno", $this->dados->oItbi->it01_valorterreno);
        $this->setAtributo("it01_valorconstr", $this->dados->oItbi->it01_valorconstr);
        $this->setAtributo("it01_valortransacao", $this->dados->oItbi->it01_valortransacao);
        $this->setAtributo("usuarioNomeIncluido", "");
        $this->setAtributo("areaterreno", $this->dados->oItbi->it01_areaterreno);
        $this->setAtributo("areatran", $this->dados->oItbi->it01_areatrans);
        $this->setAtributo("it01_data", $this->dados->oItbi->it01_data);
        $this->setAtributo("Rhora", $this->dados->oItbi->it01_hora);

        // Transmitente principal
        $this->setAtributo("transmitente", $this->dados->oTransmitente->it03_nome);
        $this->setAtributo("z01_nome", $this->dados->oTransmitente->it03_nome);
        $this->setAtributo("outrostransmitentes", $this->dados->sOutrosTransmitentes);
        $this->setAtributo("fonetransmitente", $this->dados->oTransmitente->z01_telef);
        $this->setAtributo("mailtransmitente", $this->dados->oTransmitente->it03_mail);
        $this->setAtributo("z01_cgccpf", $this->dados->oTransmitente->it03_cpfcnpj);
        $sEndereco = "{$this->dados->oTransmitente->it03_endereco}, {$this->dados->oTransmitente->it03_numero}";
        $sEndereco .= (!empty($this->dados->oTransmitente->it03_compl)
            ?
            "/{$this->dados->oTransmitente->it03_compl}"
            :
            "");
        $this->setAtributo("z01_ender", $sEndereco);
        $this->setAtributo("z01_bairro", $this->dados->oTransmitente->it03_bairro);
        $this->setAtributo("z01_munic", $this->dados->oTransmitente->it03_munic);
        $this->setAtributo("z01_uf", $this->dados->oTransmitente->it03_uf);
        $this->setAtributo("z01_cep", $this->dados->oTransmitente->it03_cep);

        // Adquirente principal
        $this->setAtributo("nomecompprinc", $this->dados->oAdquirente->it03_nome);
        $this->setAtributo("outroscompradores", $this->dados->sOutrosAdquirentes);
        $this->setAtributo("fonecomprador", $this->dados->oAdquirente->z01_telef);
        $this->setAtributo("mailcomprador", $this->dados->oAdquirente->it03_mail);
        $this->setAtributo("cgccpfcomprador", $this->dados->oAdquirente->it03_cpfcnpj);
        $this->setAtributo("enderecocomprador", $this->dados->oAdquirente->it03_endereco);
        $this->setAtributo("numerocomprador", $this->dados->oAdquirente->it03_numero);
        $this->setAtributo("complcomprador", $this->dados->oAdquirente->it03_compl);
        $this->setAtributo("bairrocomprador", $this->dados->oAdquirente->it03_bairro);
        $this->setAtributo("municipiocomprador", $this->dados->oAdquirente->it03_munic);
        $this->setAtributo("ufcomprador", $this->dados->oAdquirente->it03_uf);
        $this->setAtributo("cepcomprador", $this->dados->oAdquirente->it03_cep);
        $this->setAtributo("numcgm", $this->dados->oAdquirente->z01_numcgm);
        $iRefAnt = (
        (isset($this->dados->oIptuant->j40_refant) && !empty($this->dados->oIptuant->j40_refant))
            ?
            $this->dados->oIptuant->j40_refant
            :
            $this->dados->oItbi->it06_matric
        );
        $this->setAtributo("pretitulo8", $iRefAnt);

        // Dados imóvel / Dados Terra
        $this->setAtributo("it06_matric", $this->dados->oItbi->it06_matric);
        $this->setAtributo("j39_numero", $this->dados->oItbi->it22_numero);
        $this->setAtributo("j34_setor", $this->dados->oItbi->it22_setor);
        $this->setAtributo("j34_quadra", $this->dados->oItbi->it22_quadra);
        $this->setAtributo("j34_lote", $this->dados->oItbi->it22_lote);
        $iMatricri = ($this->dados->oItbi->it22_matricri ? $this->dados->oItbi->it22_matricri : "");
        $this->setAtributo("it22_matricri", $iMatricri);
        $this->setAtributo("it22_quadrari", $this->dados->oItbi->it22_quadrari);
        $this->setAtributo("it22_loteri", $this->dados->oItbi->it22_loteri);
        $this->setAtributo("j13_descr", $this->dados->oItbi->j13_descr);
        $this->setAtributo("j14_tipo", ""); // deve ser validado
        $this->setAtributo("j14_nome", $this->dados->oItbi->it22_descrlograd);
        $this->setAtributo("it07_descr", $this->dados->oItbi->it07_descr);
        $this->setAtributo("it05_frente", $this->dados->oItbi->it05_frente);
        $this->setAtributo("it05_fundos", $this->dados->oItbi->it05_fundos);
        $this->setAtributo("it05_direito", $this->dados->oItbi->it05_direito);
        $this->setAtributo("it05_esquerdo", $this->dados->oItbi->it05_esquerdo);
        $this->setAtributo("it18_distcidade", $this->dados->oItbi->it18_distcidade);
        $this->setAtributo("it18_localimovel", $this->dados->oItbi->it18_localimovel);
        $this->setAtributo("lFrenteVia", $this->dados->oItbi->lfrentevia);
        $this->setAtributo("it18_frente", $this->dados->oItbi->it18_frente);
        $this->setAtributo("it18_fundos", $this->dados->oItbi->it18_fundos);
        $this->setAtributo("it18_prof", $this->dados->oItbi->it18_prof);
        $this->setAtributo("aDadosRuralCaractUtil", $this->dados->aDadosRuralCaractUtil);
        $this->setAtributo("aDadosRuralCaractDist", $this->dados->aDadosRuralCaractDist);
        $this->setAtributo("fracaoIdeal", (isset($this->dados->iFracaoIdeal) ? $this->dados->iFracaoIdeal : ""));
        $this->setAtributo("it22_numero", $this->dados->oDadosImovel->it22_numero);
        $this->setAtributo("it22_compl", $this->dados->oDadosImovel->it22_compl);
        $this->setAtributo("it22_setor", $this->dados->oDadosImovel->it22_setor);
        $this->setAtributo("informacoesImovel", $this->dados->oInformacoesImovel);
        $this->setAtributo("it29_setorloc", $this->dados->oItbi->it29_setorloc);

        // Observações
        $this->setAtributo("observacaoIncluido", $this->dados->oItbi->it01_obs);
        $this->setAtributo("observacaoLiberado", $this->dados->oItbi->it14_obs);
        $this->setAtributo("adquirintes", $this->dados->sAdquirentesSecundarios);
        $this->setAtributo("transmitentes", $this->dados->sTransmitentesSecundarios);
        $this->setAtributo("arrayAdquirentes", $this->dados->arrayAdquirentes);
        $this->setAtributo("arrayTransmitentes", $this->dados->arrayTransmitentes);

        // Avaliação
        $sDataLiberacao = date("d/m/Y", strtotime($this->dados->oItbi->it14_dtliber));
        $this->setAtributo("dataLiberado", ($this->dados->bLiberado ? $sDataLiberacao : ""));
        $this->setAtributo("it14_valoraval", $this->dados->oItbi->it14_valoraval);
        $this->setAtributo("it14_valoravalconstr", $this->dados->oItbi->it14_valoravalconstr);
        $this->setAtributo("it14_valoravalter", $this->dados->oItbi->it14_valoravalter);
        $this->setAtributo("it14_valorpaga", $this->dados->oItbi->it14_valorpaga);
        $this->setAtributo("tx_banc", 0);
        $this->setAtributo("aDadosFormasPgto", ($this->dados->bLiberado ? $this->dados->aDadosFormasPgto : []));

        // Intermediador
        if (is_object($this->dados->oItbiintermediador)) {
            $this->setAtributo("intermediadorCpf", $this->dados->oItbiintermediador->it35_cnpj_cpf);
            $this->setAtributo("intermediadorNome", utf8_decode($this->dados->oItbiintermediador->it35_nome));
            $this->setAtributo("intermediadorCreci", $this->dados->oItbiintermediador->it35_creci);
        }

        // Benfeitorias
        $this->setAtributo("linhasresultcons", $this->dados->oBenfeitorias->iQtdCons);
        $this->setAtributo("areatrans", $this->dados->oBenfeitorias->fAreaTrans);
        $this->setAtributo("areatotal", $this->dados->oBenfeitorias->fAreaTotal);
        $this->setAtributo("arrayit09_codigo", $this->dados->oBenfeitorias->aConstrEspecie);
        $this->setAtributo("arrayit10_codigo", $this->dados->oBenfeitorias->aConstrTipo);
        $this->setAtributo("arrayit08_area", $this->dados->oBenfeitorias->aConstrArea);
        $this->setAtributo("arrayit08_areatrans", $this->dados->oBenfeitorias->aConstrAreatrans);
        $this->setAtributo("arrayit08_ano", $this->dados->oBenfeitorias->aConstrAno);
    }
}
