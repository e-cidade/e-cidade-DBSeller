<?php

namespace App\Domain\Tributario\ISSQN\Controller\SimplesNacional;

use App\Http\Controllers\Controller;
use App\Domain\Core\Base\Http\Response\DBJsonResponse;
use App\Domain\Tributario\ISSQN\Requests\InscricaoMunicipal\GetHorarioAtualizacoesRequest;
use App\Domain\Tributario\ISSQN\Requests\SimplesNacional\BuscaArquivosOptantesRequest;
use App\Domain\Tributario\ISSQN\Requests\SimplesNacional\BuscarArquivoOptantesPorIdRequest;
use App\Domain\Tributario\ISSQN\Requests\SimplesNacional\ExportacaoArquivoOptantesRequest;
use App\Domain\Tributario\ISSQN\Requests\SimplesNacional\ImportacaoOptantesRequest;
use App\Domain\Tributario\ISSQN\Requests\SimplesNacional\SetInfoAtualizacoesSimplesNacionalRequest;
use App\Domain\Tributario\ISSQN\Services\SimplesNacional\AtualizaDataCadastrosService;
use App\Domain\Tributario\ISSQN\Services\SimplesNacional\SimplesNacionalService;

/**
 * Classe para lidar com a busca de innformacoes e armazenamento do arquivo de novos
 * optantes pelo simples nacional
 */
class SimplesNacionalController extends Controller
{
    protected $service;
    protected $atualizaDataCadastrosService;

    /**
     * Construtor da classe
     *
     * @param SimplesNacionalService $simplesNacionalService
     * @return void
     */
    public function __construct(
        SimplesNacionalService $simplesNacionalService,
        AtualizaDataCadastrosService $atualizaDataCadastrosService
    ) {
        $this->service = $simplesNacionalService;
        $this->atualizaDataCadastrosService = $atualizaDataCadastrosService;
    }

    /**
     * Metodo para importacao de dados de optantes
     *
     * @param ImportacaoOptantesRequest $request
     */
    public function importaOptantes(ImportacaoOptantesRequest $request)
    {
        $registros = $request->registros;

        return new DBJsonResponse($this->service->importaOptantes($registros));
    }

    /**
     * Metodo para exportacao e armazenamento do arquivo de optantes
     * pelo simples nacional
     *
     * @param ExportacaoArquivoOptantesRequest $request
     */
    public function exportaArquivo(ExportacaoArquivoOptantesRequest $request)
    {
        $nomeArquivo = $request->nomeArquivo;
        $dataImportacao = $request->dataImportacao;
        $periodoApuracaoDe = $request->periodoApuracaoDe;
        $periodoApuracaoAte = $request->periodoApuracaoAte;
        $dataLimite = $request->dataLimite;
        $registros = $request->registros;

        return new DBJsonResponse($this->service->exportaArquivo(
            $nomeArquivo,
            $dataImportacao,
            $periodoApuracaoDe,
            $periodoApuracaoAte,
            $dataLimite,
            $registros
        ));
    }

    /**
     * Lista os arquivos armazenados no sistema referente ao simples nacional
     *
     * @param BuscaArquivosOptantesRequest $request
     */
    public function buscaTodosArquivos(BuscaArquivosOptantesRequest $request)
    {
        $nomeArquivo = $request->nomeArquivo ? $request->nomeArquivo : "";
        $dataImportacao = $request->dataImportacao ? $request->dataImportacao : "";
        $dataLimite = $request->dataLimite ? $request->dataLimite : "";

        return new DBJsonResponse($this->service->buscaTodosArquivos(
            $nomeArquivo,
            $dataImportacao,
            $dataLimite
        ));
    }

    /**
     * @param BuscaArquivoOptantesPorIdRequest $idArquivo
     */
    public function buscarArquivoPorId(
        BuscarArquivoOptantesPorIdRequest $request
    ) {
        $idArquivo = $request->id;

        return new DBJsonResponse($this->service->buscarArquivoPorId($idArquivo));
    }

    /**
     * Metodo para salvar dados de dia e horario referente a atualizacao
     * automatica de cadastro simples nacional
     *
     * @param SetInfoAtualizacoesSimplesNacionalRequest $request
     */
    public function setFrequenciaAtualizacoes(SetInfoAtualizacoesSimplesNacionalRequest $request)
    {
        $frequenciaAtualizacoes = $request->frequenciaAtualizacoes;
        $diaDoMes = $request->diaDoMes;
        $diaDaSemana = $request->diaDaSemana;
        $horario = $request->horario;
        $idUsuario = $request->idUsuario;

        $resposta = $this->atualizaDataCadastrosService->setFrequenciaAtualizacoes(
            $frequenciaAtualizacoes,
            $diaDoMes,
            $diaDaSemana,
            $horario,
            $idUsuario
        );

        return new DBJsonResponse($resposta);
    }

    /**
     * Metodo para buscar dados de dia e horario referente a atualizacao
     * automatica de cadastro simples nacional
     */
    public function getInfoAtualizacoes(GetHorarioAtualizacoesRequest $request)
    {
        $idUsuario = $request->idUsuario;
        $resposta = $this->atualizaDataCadastrosService->getHorarioAtualizacoes($idUsuario);

        return new DBJsonResponse($resposta);
    }
}
