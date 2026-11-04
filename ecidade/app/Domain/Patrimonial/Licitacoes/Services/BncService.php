<?php

namespace App\Domain\Patrimonial\Licitacoes\Services;

use App\Domain\Patrimonial\Licitacoes\Clients\BncClient;
use App\Domain\Patrimonial\Licitacoes\Clients\ExportacaoBncClient;
use App\Domain\Patrimonial\Licitacoes\Clients\ImportacaoBncClient;
use App\Domain\Patrimonial\Licitacoes\Models\Licitacao;
use Carbon\Carbon;
use DBException;
use Exception;
use ParameterException;

class BncService
{
    private $version;

    public function __construct($params = [])
    {
        $this->version = !empty($params['version']) ? $params['version'] : 2;
    }

    /**
     * @param Licitacao $licitacao
     * @return bool|void
     * @throws Exception
     */
    public function importar(Licitacao $licitacao)
    {
        $client = new ImportacaoBncClient();
        $idProcesso = $client->buscarIdProcesso($licitacao->l20_codigo);
        $processo = $client->buscarDadosProcesso($idProcesso);

        $verificaApenasStatus = !($processo->idStatus === 24);
        $this->verificarLicitacaoElegivelImportacao($licitacao, $verificaApenasStatus);
        if ($processo->idStatus !== 24) {
            return $this->salvarSituacaoValida($licitacao, $processo);
        }

        $resultadoProcesso = $client->buscarResultadoProcesso($idProcesso);
        $this->salvarOrcamento($resultadoProcesso, $processo, $licitacao);
    }

    /**
     * @param Licitacao $licitacao
     * @param bool $verificaApenasStatus
     * @return void
     * @throws Exception
     */
    private function verificarLicitacaoElegivelImportacao(Licitacao $licitacao, $verificaApenasStatus = false)
    {
        if ($licitacao->l20_licsituacao !== 0) {
            throw new Exception(
                'A licitação não se encontra em uma situação válida para importação ou atualização, verifique.'
            );
        }

        if ($verificaApenasStatus) {
            return;
        }

        $modelLicitacao = new \licitacao($licitacao->l20_codigo);
        if (!empty($modelLicitacao->getFornecedor())) {
            throw new Exception(
                'Não será possível a importação pois foram incluídos fornecedores à licitação, verifique.'
            );
        }
    }

    /**
     * @param Licitacao $licitacao
     * @param $processo
     * @return bool
     * @throws Exception
     */
    private function salvarSituacaoValida(Licitacao $licitacao, $processo)
    {
        if (empty($processo->idStatus)) {
            return false;
        }

        $params = [];
        switch ($processo->idStatus) {
            case 5:
                $params['situacao'] = 3; // Deserta
                $params['observacao'] = 'Licitação Deserta Automaticamente (BNC).';

                $paramsLicitacao = [];
                $paramsLicitacao['data'] = Carbon::parse($processo->DisputeStart);
                $paramsLicitacao['tipo'] = 6;
                $paramsLicitacao['fase'] = 4;
                $this->salvarEventoLicitacao($licitacao, $paramsLicitacao);

                break;
            case 26:
                $params['situacao'] = 4; // Fracassada
                $params['observacao'] = 'Licitação Fracassada Automaticamente (BNC).';
                break;
            case 28:
                $params['situacao'] = 2; // Revogada
                $params['observacao'] = 'Licitação Revogada Automaticamente (BNC).';
                break;
            case 29:
                $params['situacao'] = 5; // Anulada
                $params['observacao'] = 'Licitação Anulada Automaticamente (BNC).';
                break;
        }

        if (!empty($params['situacao'])) {
            $params['data'] = Carbon::now();
            $this->salvarSituacaoLicitacao($licitacao, $params);
            $this->atualizarSituacaoLicitacao($licitacao, $params);

            return true;
        }

        throw new Exception(
            "Não é possível importar licitações com situação " . mb_strtolower($processo->Status)
        );
    }

    /**
     * @param Licitacao $licitacao
     * @param $params
     * @return \cl_liclicitaevento
     * @throws Exception
     */
    private function salvarEventoLicitacao(Licitacao $licitacao, $params)
    {
        $eventoLicitacao = new \cl_liclicitaevento();
        $eventoLicitacao->l46_liclicita = $licitacao->l20_codigo;
        $eventoLicitacao->l46_fase = $params['fase'];
        $eventoLicitacao->l46_liclicitatipoevento = $params['tipo'];
        $eventoLicitacao->l46_dataevento = $params['data']->format('Y-m-d');
        $eventoLicitacao->incluir(null);
        $this->verificarOperacaoBemSucedida($eventoLicitacao);

        return $eventoLicitacao;
    }

    /**
     * @param $classe
     * @return void
     * @throws Exception
     */
    private function verificarOperacaoBemSucedida($classe)
    {
        if ($classe->erro_status === "0") {
            throw new Exception($classe->erro_msg);
        }
    }

    /**
     * @param Licitacao $licitacao
     * @param $params
     * @return void
     * @throws Exception
     */
    private function salvarSituacaoLicitacao(Licitacao $licitacao, $params)
    {
        $licitacaoSituacao = new \cl_liclicitasituacao();
        $licitacaoSituacao->l11_id_usuario = db_getsession('DB_id_usuario');
        $licitacaoSituacao->l11_liclicita = $licitacao->l20_codigo;
        $licitacaoSituacao->l11_licsituacao = $params['situacao'];
        $licitacaoSituacao->l11_data = $params['data']->format('Y-m-d');
        $licitacaoSituacao->l11_hora = $params['data']->format('H:i');
        $licitacaoSituacao->l11_obs = $params['observacao'];
        $licitacaoSituacao->incluir(null);
        $this->verificarOperacaoBemSucedida($licitacaoSituacao);
    }

    /**
     * @param Licitacao $licitacao
     * @param $params
     * @return void
     * @throws Exception
     */
    public function atualizarSituacaoLicitacao(Licitacao $licitacao, $params)
    {
        $liclicita = new \cl_liclicita();
        $liclicita->l20_licsituacao = $params['situacao'];
        $liclicita->l20_codigo = $licitacao->l20_codigo;
        $liclicita->alterar($licitacao->l20_codigo);
        $this->verificarOperacaoBemSucedida($liclicita);
    }

    /**
     * @param object $resultadoProcesso
     * @param object $processo
     * @param Licitacao $licitacao
     * @return void
     * @throws Exception
     */
    private function salvarOrcamento($resultadoProcesso, $processo, Licitacao $licitacao)
    {
        $params = [];
        $params['dataAtual'] = Carbon::now();
        $params['dataInicioDisputa'] = Carbon::parse($processo->DisputeStart);
        $params['dataHomologacao'] = Carbon::parse($processo->HomologationDate);
        $params['dataAdjudicacao'] = Carbon::parse($processo->AdjudicationDate);
        $params['codigoOrcamento'] = $this->salvarOrcamentoCompra($params);

        $params['dadosParticipantes'] = $this->salvarDadosParticipantes($resultadoProcesso, $params);
        $params['itensLicitacao'] = $this->montarItensLicitacao($licitacao);
        $params['linksRelatorio'] = $processo->SessionReportLinks ?: [];

        $this->salvarSituacoesLicitacao($licitacao, $params);
        $this->salvarDocumentosLicitacao($licitacao, $params);
        $this->salvarPropostas($licitacao, $resultadoProcesso, $params);
    }

    /**
     * @return int|mixed
     * @throws Exception
     */
    private function salvarOrcamentoCompra($params)
    {
        $orcamentoCompra = new \cl_pcorcam();
        $orcamentoCompra->pc20_dtate = $params['dataAtual']->format('Y-m-d');
        $orcamentoCompra->pc20_hrate = $params['dataAtual']->format('H:i');
        $orcamentoCompra->pc20_obs = "Orçamento automático (BNC)";
        $orcamentoCompra->incluir(null);
        $this->verificarOperacaoBemSucedida($orcamentoCompra);

        return $orcamentoCompra->pc20_codorc;
    }

    /**
     * @param object $resultadoProcesso
     * @return array
     * @throws Exception
     */
    private function salvarDadosParticipantes($resultadoProcesso, $params)
    {
        $participantes = [];
        foreach ($resultadoProcesso->ExpProposal as $proposta) {
            $cgm = new \cl_cgm();
            $participante = $proposta->Participant;
            $documentoParticipante = preg_replace('/\D/', '', $participante->Document1);
            $registroGeralInscricaoMunic = preg_replace('/\D/', '', $participante->Document2);

            $condicoes = [];
            $condicoes[] = "z01_cgccpf = '$documentoParticipante'";
            $condicoes = implode(' AND ', $condicoes);

            $sqlParticipanteExiste = $cgm->sql_query(null, '*', null, $condicoes);
            $cgmParticipante = $cgm->sql_record($sqlParticipanteExiste);

            if ($cgmParticipante === false) {
                $cgm->z01_munic = substr($this->tratarStringRetorno($participante->CityName), 0, 40);
                $cgm->z01_nome = substr($this->tratarStringRetorno($participante->Name), 0, 40);
                $cgm->z01_nomecomple = substr($this->tratarStringRetorno($participante->Name), 0, 40);
                $cgm->z01_nomefanta = substr($this->tratarStringRetorno($participante->Tradename), 0, 100);
                $cgm->z01_cgccpf = $documentoParticipante;
                $cgm->z01_uf = substr($participante->StateAcronym, 0, 2);
                $cgm->z01_identorgao = substr($participante->Issuer, 0, 50);
                $cgm->z01_cep = preg_replace('/\D/', '', $participante->PostalCode);
                $cgm->z01_ender = substr($this->tratarStringRetorno($participante->Address), 0, 100);
                $cgm->z01_bairro = substr($this->tratarStringRetorno($participante->District), 0, 40);
                $cgm->z01_compl = substr($this->tratarStringRetorno($participante->Complement), 0, 100);
                $cgm->z01_email = substr($participante->Email, 0, 100);
                $cgm->z01_telef = preg_replace('/\D/', '', $participante->Phone);
                $cgm->z01_telcel = preg_replace('/\D/', '', $participante->Celphone);

                if (strlen($documentoParticipante) > 11) {
                    $cgm->z01_incest = $registroGeralInscricaoMunic;
                } else {
                    $cgm->z01_ident = $registroGeralInscricaoMunic;
                }

                $cgm->incluir(null);
                $this->verificarOperacaoBemSucedida($cgm);
            } else {
                $cgm = \db_utils::fieldsMemory($cgmParticipante, 0);
            }

            $cgm->pc21_orcamforne = $this->salvarFornecedorOrcamento($cgm, $params);
            $participantes[$documentoParticipante] = $cgm;
        }

        return $participantes;
    }

    /**
     * @param $string
     * @return string
     */
    private function tratarStringRetorno($string)
    {
        $fromEncoding = mb_detect_encoding($string) ?: 'UTF-8';
        return pg_escape_string(mb_convert_encoding($string, 'ISO-8859-1', $fromEncoding));
    }

    /**
     * @param $participante
     * @param $params
     * @return int|mixed
     * @throws Exception
     */
    private function salvarFornecedorOrcamento($participante, $params)
    {
        $orcamentoFornecedor = new \cl_pcorcamforne();
        $orcamentoFornecedor->pc21_importado = 't';
        $orcamentoFornecedor->pc21_codorc = $params['codigoOrcamento'];
        $orcamentoFornecedor->pc21_numcgm = $participante->z01_numcgm;
        $orcamentoFornecedor->incluir(null);
        $this->verificarOperacaoBemSucedida($orcamentoFornecedor);

        $orcamentoFornecedorLicitacao = new \cl_pcorcamfornelic();
        $orcamentoFornecedorLicitacao->pc31_liclicitatipoempresa = 1;
        $orcamentoFornecedorLicitacao->pc31_horaretira = $params['dataAtual']->format('H:i');
        $orcamentoFornecedorLicitacao->pc31_dtretira = $params['dataAtual']->format('Y-m-d');
        $orcamentoFornecedorLicitacao->incluir($orcamentoFornecedor->pc21_orcamforne);
        $this->verificarOperacaoBemSucedida($orcamentoFornecedorLicitacao);

        return $orcamentoFornecedor->pc21_orcamforne;
    }

    /**
     * @param Licitacao $licitacao
     * @return array
     */
    private function montarItensLicitacao(Licitacao $licitacao)
    {
        $itensLicitacao = [];

        $licitacao->load('itens');
        foreach ($licitacao->itens as $item) {
            $itensLicitacao[$item->l21_ordem] = $item;
        }

        return $itensLicitacao;
    }

    /**
     * @param Licitacao $licitacao
     * @param array $params
     * @return void
     * @throws Exception
     */
    private function salvarSituacoesLicitacao(Licitacao $licitacao, $params)
    {
        $paramsLicitacao = [];
        $paramsLicitacao['observacao'] = 'Licitação julgada automaticamente (BNC).';
        $paramsLicitacao['situacao'] = 1;
        $paramsLicitacao['data'] = $params['dataAdjudicacao'];
        $this->salvarSituacaoLicitacao($licitacao, $paramsLicitacao);

        $paramsLicitacao['observacao'] = 'Licitação adjudicada automaticamente (BNC).';
        $paramsLicitacao['situacao'] = 6;
        $this->salvarSituacaoLicitacao($licitacao, $paramsLicitacao);

        $paramsLicitacao['observacao'] = 'Licitação homologada automaticamente (BNC).';
        $paramsLicitacao['situacao'] = 7;
        $paramsLicitacao['data'] = $params['dataHomologacao'];
        $this->salvarSituacaoLicitacao($licitacao, $paramsLicitacao);

        $paramsLicitacao['situacao'] = 7;
        $this->atualizarSituacaoLicitacao($licitacao, $paramsLicitacao);
    }

    /**
     * @param Licitacao $licitacao
     * @param $params
     * @return void
     * @throws Exception
     */
    private function salvarDocumentosLicitacao(Licitacao $licitacao, $params)
    {
        $paramsLicitacao = [];
        $paramsLicitacao['data'] = $params['dataInicioDisputa'];
        $paramsLicitacao['tipo'] = 23;
        $paramsLicitacao['fase'] = 4;
        $eventoLicitacao = $this->salvarEventoLicitacao($licitacao, $paramsLicitacao);

        foreach ($params['linksRelatorio'] as $linkRelatorio) {
            $nomeArquivo = basename($linkRelatorio);
            $caminhoArquivo = "/tmp/{$nomeArquivo}";

            $arquivoRelatorio = file_get_contents($linkRelatorio);
            if ($arquivoRelatorio === false) {
                throw new Exception("Não foi possível encontrar o arquivo: $linkRelatorio");
            }

            $arquivoTemporario = file_put_contents($caminhoArquivo, $arquivoRelatorio);
            if ($arquivoTemporario === false) {
                throw new Exception("Não foi possível salvar o arquivo: $nomeArquivo");
            }

            $oid = pg_lo_import($caminhoArquivo);
            if ($oid === false) {
                throw new Exception("Não foi possível importar o arquivo: $nomeArquivo");
            }

            $documentoEventoLicitacao = new \cl_liclicitaeventodocumento();
            $documentoEventoLicitacao->l47_liclicitaevento = $eventoLicitacao->l46_sequencial;
            $documentoEventoLicitacao->l47_nomearquivo = $nomeArquivo;
            $documentoEventoLicitacao->l47_arquivo = $oid;
            $documentoEventoLicitacao->l47_tipodocumento = 9;
            $documentoEventoLicitacao->incluir(null);
            $this->verificarOperacaoBemSucedida($documentoEventoLicitacao);

            unlink($caminhoArquivo);
        }
    }

    /**
     * @param Licitacao $licitacao
     * @param $resultadoProcesso
     * @param $params
     * @return void
     * @throws Exception
     */
    private function salvarPropostas(Licitacao $licitacao, $resultadoProcesso, $params)
    {
        $orcamentoJulgamentoLog = new \cl_pcorcamjulgamentolog();
        $orcamentoJulgamentoLog->pc92_datajulgamento = $params['dataAtual']->format('Y-m-d');
        $orcamentoJulgamentoLog->pc92_usuario = db_getsession('DB_id_usuario');
        $orcamentoJulgamentoLog->pc92_ativo = 't';
        $orcamentoJulgamentoLog->pc92_hora = $params['dataAtual']->format('H:i');
        $orcamentoJulgamentoLog->incluir(null);
        $this->verificarOperacaoBemSucedida($orcamentoJulgamentoLog);

        foreach ($resultadoProcesso->ExpBatch as $lote) {
            foreach ($lote->ExpBatchProposal as $proposta) {
                $participante = $params['dadosParticipantes'][$proposta->ParticipantDocument];

                $params['codigoFornecedor'] = $participante->pc21_orcamforne;
                $params['codigoJulgamentoLog'] = $orcamentoJulgamentoLog->pc92_sequencial;
                $this->salvarOrcamentoItens($licitacao, $proposta, $params);
            }
        }
    }

    /**
     * @param Licitacao $licitacao
     * @param $proposta
     * @param $params
     * @return void
     * @throws Exception
     */
    private function salvarOrcamentoItens(Licitacao $licitacao, $proposta, $params)
    {
        $itens = $proposta->ExpBatchItem;
        foreach ($itens as $item) {
            $orcamentoItemCompra = $params['itensLicitacao'][$item->Number]->orcamentoItemCompra ?: [];

            if (empty($orcamentoItemCompra)) {
                $orcamentoItemCompra = new \cl_pcorcamitem();
                $orcamentoItemCompra->pc22_codorc = $params['codigoOrcamento'];
                $orcamentoItemCompra->incluir(null);
                $this->verificarOperacaoBemSucedida($orcamentoItemCompra);

                $orcamentoItemLicitacao = new \cl_pcorcamitemlic();
                $orcamentoItemLicitacao->pc26_liclicitem = $params['itensLicitacao'][$item->Number]->l21_codigo;
                $orcamentoItemLicitacao->pc26_orcamitem = $orcamentoItemCompra->pc22_orcamitem;
                $orcamentoItemLicitacao->incluir();
                $this->verificarOperacaoBemSucedida($orcamentoItemLicitacao);

                $params['itensLicitacao'][$item->Number]->orcamentoItemCompra = $orcamentoItemCompra;
            }

            if ($proposta->DisqualReason) {
                $orcamentoDesqualificado = new \cl_pcorcamdescla();
                $orcamentoDesqualificado->pc32_motivo = $proposta->DisqualReason;
                $orcamentoDesqualificado->incluir(
                    $orcamentoItemCompra->pc22_orcamitem,
                    $params['codigoFornecedor']
                );

                continue;
            }

            $valorUnitario = $item->ProposalValue;
            if ($licitacao->l20_tipojulg === 1) {
                $valorUnitario = $proposta->LastBid;
            }

            $orcamentoValorCompra = new \cl_pcorcamval();
            $orcamentoValorCompra->pc23_vlrun = $valorUnitario;
            $orcamentoValorCompra->pc23_quant = $item->Quantity;
            $orcamentoValorCompra->pc23_valor = $item->Quantity * $orcamentoValorCompra->pc23_vlrun;
            $orcamentoValorCompra->pc23_data = $params['dataAtual']->format('Y-m-d');
            $orcamentoValorCompra->pc23_obs = $item->Brand;
            $orcamentoValorCompra->incluir(
                $params['codigoFornecedor'],
                $orcamentoItemCompra->pc22_orcamitem
            );
            $this->verificarOperacaoBemSucedida($orcamentoValorCompra);

            if ($proposta->RankingPosition === 1) {
                $orcamentoJulgamento = new \cl_pcorcamjulg();
                $orcamentoJulgamento->pc24_pontuacao = 1;
                $orcamentoJulgamento->incluir(
                    $orcamentoItemCompra->pc22_orcamitem,
                    $params['codigoFornecedor']
                );
                $this->verificarOperacaoBemSucedida($orcamentoJulgamento);

                $orcamentoJulgamentoLogItem = new \cl_pcorcamjulgamentologitem();
                $orcamentoJulgamentoLogItem->pc93_pcorcamjulgamentolog = $params['codigoJulgamentoLog'];
                $orcamentoJulgamentoLogItem->pc93_valorunitario = $valorUnitario;
                $orcamentoJulgamentoLogItem->pc93_pcorcamforne = $params['codigoFornecedor'];
                $orcamentoJulgamentoLogItem->pc93_pcorcamitem = $orcamentoItemCompra->pc22_orcamitem;
                $orcamentoJulgamentoLogItem->pc93_pontuacao = 1;
                $orcamentoJulgamentoLogItem->incluir(null);
                $this->verificarOperacaoBemSucedida($orcamentoJulgamentoLogItem);
            }
        }
    }

    /**
     * @param Licitacao $licitacao
     * @return array
     * @throws Exception
     */
    public function exportar(Licitacao $licitacao)
    {
        $this->verificarLicitacaoElegivelExportacao($licitacao);

        $client = new ExportacaoBncClient();
        $dadosProcesso = $this->montarDadosProcesso($licitacao);
        $idProcesso = $client->salvarProcesso($dadosProcesso);
        $dadosLote = $this->montarDadosLote($licitacao, $idProcesso);
        $client->salvarLote($dadosLote);

        return $dadosProcesso;
    }

    /**
     * @param Licitacao $licitacao
     * @return void
     * @throws DBException
     * @throws Exception
     */
    private function verificarLicitacaoElegivelExportacao(Licitacao $licitacao)
    {
        if ($licitacao->l20_licsituacao !== 0) {
            throw new Exception(
                'Verifique a situação da licitação, '
                . 'somente é possível o envio de licitações "Em Andamento" ao BNC.'
            );
        }

        if (!$licitacao->itens()->exists()) {
            throw new Exception(
                'Não foi possível identificar os itens da licitação, '
                . 'verifique se existe processo de compras vinculado à mesma.'
            );
        }

        if ($licitacao->itens()->doesntHave('lote')->exists()) {
            throw new Exception(
                'Não foi possível identificar os lotes da licitação, verifique.'
            );
        }

        $licitacaoBase = new \licitacao($licitacao->l20_codigo);
        $orcamentoLicitacao = new \OrcamentoLicitacao($licitacaoBase);
        if (empty($orcamentoLicitacao->getValorTotalEstimado())) {
            throw new Exception(
                'Não foi possível identificar o valor de referência para a licitação, verifique '
                . ' se todos os itens possuem valores ou se encontram em um orçamento de processo de compras julgado.'
            );
        }
    }

    /**
     * @param Licitacao $licitacao
     * @return array
     */
    private function montarDadosProcesso(Licitacao $licitacao)
    {
        $dataRecebimento = "{$licitacao->l20_dataaber} {$licitacao->l20_horaaber}";
        $dataRecebimento = Carbon::createFromFormat('Y-m-d H:i', $dataRecebimento)->format('c');

        $numeroAdministrativo = $this->tratarStringEnvio($licitacao->l20_procadmin);
        $numeroAdministrativo = str_pad($numeroAdministrativo, 7, "0", STR_PAD_LEFT);

        $client = new BncClient();
        $options = [];
        $options['http_errors'] = false;
        $idProcesso = $client->buscarIdProcesso($licitacao->l20_codigo, $options);

        $dados = [];
        $dados['ProposalReceivingStart'] = $dataRecebimento;
        $dados['ProductOrService'] = $this->tratarStringEnvio($licitacao->l20_objeto);
        $dados['fkContractKind'] = $licitacao->l20_usaregistropreco ? 2 : 1;
        $dados['YearReference'] = $licitacao->l20_anousu;
        $dados['ExpProcessId'] = $licitacao->l20_codigo;
        $dados['fkModality'] = 1;
        $dados['AdmNumber'] = $numeroAdministrativo;
        $dados['Version'] = $this->version;
        $dados['Number'] = str_pad($licitacao->l20_numero, 7, "0", STR_PAD_LEFT);

        if ($idProcesso) {
            $dados['IdIntegProcess'] = $idProcesso;
        }

        $dados['body'] = json_encode(
            $dados,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        return $dados;
    }

    /**
     * @param $string
     * @return string
     */
    private function tratarStringEnvio($string)
    {
        return mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
    }

    /**
     * @param Licitacao $licitacao
     * @param $processoId
     * @return array
     * @throws DBException
     * @throws ParameterException
     */
    private function montarDadosLote(Licitacao $licitacao, $processoId)
    {
        $licitacao->load([
            'itens' => function ($query) {
                $query->orderBy('l21_ordem');
                $query->with('lote');
            },
            'itens.itemProcessoCompra.itemSolicitacao.itemProcessoMaterial.processoCompraMaterial',
            'itens.itemProcessoCompra.itemSolicitacao.itemUnidade.materialUnidade'
        ]);

        $dados = [];
        $dados['IdIntegProcess'] = $processoId;
        $dados['IntegBatch'] = [];

        foreach ($licitacao->itens as $key => $item) {
            $loteId = 0;
            $tituloLote = $this->tratarStringEnvio($item->lote->l04_descricao);

            if ($licitacao->l20_tipojulg === 1) {
                $loteId = $key;

                $tituloLote = "LOTE {$item->l21_ordem}";
                $dados['IntegBatch'][$loteId]['Number'] = $item->l21_ordem;
            }

            if ($licitacao->l20_tipojulg === 3) {
                $loteId = $this->tratarStringEnvio($item->lote->l04_descricao);

                if (array_key_exists($loteId, $dados['IntegBatch'])) {
                    $dados['IntegBatch'][$loteId]['Quantity'] += 1;
                }
            }

            $licitacaoBase = new \licitacao($licitacao->l20_codigo);
            $itemLicitacao = new \ItemLicitacao($item->l21_codigo);

            $orcamentoLicitacao = new \OrcamentoLicitacao($licitacaoBase);
            $orcamentoLicitacao->setCodigoItem($item->l21_codigo);

            $itemSolicitacao = $item->itemProcessoCompra->itemSolicitacao;
            $material = $itemSolicitacao->itemProcessoMaterial->processoCompraMaterial;

            $descricaoMaterial = trim($material->pc01_descrmater);
            $descricaoItem = trim($itemSolicitacao->pc11_resum);

            if (!empty($descricaoItem) && $descricaoItem !== $descricaoMaterial) {
                $descricaoMaterial .= " - {$descricaoItem}";
            }

            if (empty($dados['IntegBatch'][$loteId]['Quantity'])) {
                $dados['IntegBatch'][$loteId]['Quantity'] = 1;
            }

            $dados['IntegBatch'][$loteId]['fkBidKind'] = $licitacao->l20_tipojulg;
            $dados['IntegBatch'][$loteId]['Title'] = $tituloLote;

            if ($itemLicitacao->hasCota(true)) {
                $dados['IntegBatch'][$loteId]['IsMeExclusive'] = true;
            }

            $dadosItem = [];
            $dadosItem['Description'] = $this->tratarStringEnvio($descricaoMaterial);
            $dadosItem['BaseValue'] = $orcamentoLicitacao->getValorUnitarioEstimado() ?: $itemSolicitacao->pc11_vlrun;
            $dadosItem['Quantity'] = $itemSolicitacao->pc11_quant;
            $dadosItem['Number'] = $item->l21_ordem;
            $dadosItem['Unity'] = $this->tratarStringEnvio($itemSolicitacao->itemUnidade->materialUnidade->m61_descr);

            $dados['IntegBatch'][$loteId]['IntegBatchItem'][] = $dadosItem;
        }

        $dados['IntegBatch'] = array_values($dados['IntegBatch']);
        $dados['body'] = json_encode(
            $dados,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
        );

        return $dados;
    }

    /**
     * @param Licitacao $licitacao
     * @return array
     */
    public function buscar(Licitacao $licitacao)
    {
        $client = new BncClient();
        $idProcesso = $client->buscarIdProcesso($licitacao->l20_codigo);

        $dados = [];
        $dados['processo'] = $client->buscarDadosProcesso($idProcesso);
        $dados['licitacao'] = $licitacao->load('itens');

        return $dados;
    }

    /**
     * @param Licitacao $licitacao
     * @return bool
     */
    public function excluir(Licitacao $licitacao)
    {
        $client = new BncClient();
        $idProcesso = $client->buscarIdProcesso($licitacao->l20_codigo);
        return $client->excluirProcesso($idProcesso);
    }
}
