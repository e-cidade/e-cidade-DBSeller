<?php

namespace App\Domain\RecursosHumanos\Pessoal\Services\Relatorios;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\RecursosHumanos\Pessoal\Repository\TipoGuiaPrevidenciaRepository;
use Carbon\Carbon;

use Exception;
use BusinessException;
use stdClass;

class TipoGuiaPrevidenciaService
{

    /**
     * @var int
     */
    private $ano;
    
    /**
     * @var int
     */
    private $mes;

    /**
     * @var string
     */
    private $tipo;

    /**
     * @var []
     */
    private $lotacoes;

    /**
     * @var int
     */
    private $selecao;

    /**
     * @var string
     */
    private $arquivo;

    /**
     * @var string
     */
    private $nomeInstituicao;

    /**
     * @var int
     */
    private $codigonstituicao;

    /**
     * @var string
     */
    private $dataVencimento;


    /**
     * @var string
     */
    private $tipoGuia;

    /**
     * dados processados para impressão
     * @var array
     */
    private $dados = [];

    /**
     * @var array
     */
    private $tabelaPrevidencia = [];

    public function setFiltrosRequest(array $filtros)
    {
        $instituicao = str_replace('\"', '"', $filtros['DB_instit']);
        $this->codigonstituicao = \JSON::create()->parse($instituicao);
        $this->nomeInstituicao = DBConfig::find($this->codigonstituicao)->nomeinstabrev;

        $this->dataVencimento = $filtros['dataVencimento'];
        $this->ano = str_replace('\"', '"', $filtros['ano']);
        $this->mes = str_replace('\"', '"', $filtros['mes']);
        $this->tipoGuia = str_replace('\"', '"', $filtros['tipoGuia']);
        $this->arquivo = str_replace('\"', '"', $filtros['arquivo']);
        if (isset($filtros['selecao'])) {
            $this->selecao = str_replace('\"', '"', $filtros['selecao']);
        } else {
            $this->lotacoes = str_replace('\"', '"', $filtros['lotacoes']);
        }
        $this->tabelaPrevidencia = str_replace('\"', '"', $filtros['tabelas']);
    }

    public function emitir()
    {
        $dadosService = [];
        $repository = new TipoGuiaPrevidenciaRepository();
        $repository->setAno($this->ano);
        $repository->setMes($this->mes);
        $repository->setArquivo($this->arquivo);
        $repository->setCodigoInstituicao($this->codigonstituicao);
        $dataVencimento = date('d/m/Y', strtotime($this->dataVencimento));
        $repository->setDataVencimento($dataVencimento);
        $repository->setTipoGuia($this->tipoGuia);
        $repository->setTipo($this->tipo);
        if (!empty($this->selecao)) {
            $repository->setSelecao($this->selecao);
            $lotacoes = [];
            $dadosService['patronal_pocent'] =  $repository->dadosPatronal(
                $this->ano,
                $this->mes,
                $this->tabelaPrevidencia
            );
            $dadosService['lotacoes'] = $lotacoes;
        } else {
            $repository->setLotacoes($this->lotacoes);
            
            $lotacoes = [];
            foreach ($this->lotacoes as $lotacao) {
                $lotacoes[] = ['total_lotacoes' => $repository
                ->getCountRhLota($lotacao, $this->ano, $this->mes, $this->tabelaPrevidencia),
                'nome_lotacoes' =>  $repository->getNomeLota($lotacao),
                ];
                $dadosService['patronal_pocent'] =  $repository->dadosPatronal(
                    $this->ano,
                    $this->mes,
                    $this->tabelaPrevidencia
                );
                $dadosService['lotacoes'] = $lotacoes;
            }
        }
        // Adequacao dos codigos das tabelas para busca nas movimentacoes
        // Velharias do sistema =\
        $tabelas = [];
        if (is_array($this->tabelaPrevidencia)) {
            foreach ($this->tabelaPrevidencia as $tabela) {
                $tabelas[] = $tabela - 2;
            }
        } else {
            $tabelas[] = $this->tabelaPrevidencia - 2;
        }
        $dadosService['tabelas'] = $repository->setTabelaPrevidencia($tabelas);
        $dadosService['instituicao'] = $repository->getInstituicao();
        $dadosService['dados'] =  $repository->getDados();
        
        $this->dados = $dadosService;
        return $this->dados;
    }
}
