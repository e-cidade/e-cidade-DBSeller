<?php

namespace App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RREO\AnexoTres;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use App\Domain\Financeiro\Contabilidade\Relatorios\LRF\RREO\XlsAnexoTres;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\AnexosExecucaoMensalService;
use PhpOffice\PhpWord\Settings;
use NcJoes\OfficeConverter\OfficeConverter;
use Exception;

abstract class AnexoTresService extends AnexosExecucaoMensalService
{
    /**
     * @var XlsAnexoTres
     */
    protected $parser;

    protected $colunaMesReceita = [
        'mes_1' => 'arrecadado_periodo',
        'mes_2' => 'arrecadado_periodo',
        'mes_3' => 'arrecadado_periodo',
        'mes_4' => 'arrecadado_periodo',
        'mes_5' => 'arrecadado_periodo',
        'mes_6' => 'arrecadado_periodo',
        'mes_7' => 'arrecadado_periodo',
        'mes_8' => 'arrecadado_periodo',
        'mes_9' => 'arrecadado_periodo',
        'mes_10' => 'arrecadado_periodo',
        'mes_11' => 'arrecadado_periodo',
        'mes_12' => 'arrecadado_periodo',
    ];

    /**
     * Ordem da linha - Receitas Correntes(i)
     * @var int
     */
    protected $linhaReceitasCorrentes = 1;
    /**
     * Ordem da linha - DEDUÇÕES(II)
     * @var int
     */
    protected $linhaDeducoes = 25;
    /**
     * Ordem da linha - RECEITA CORRENTE LÍQUIDA (III) = (I-II)
     * @var int
     */
    protected $linhaRcl = 29;

    /**
     * Mapa das linhas que totaliza outras linhas
     * @var \int[][]
     */
    protected $totalizar = [];
    /**
     * Mapa das linhas que vão para o anexo simplificado
     * Esse array é indexado pela ordem da linha e seu valor é a descrição.
     * @example
     * [
     *  29 => 'nome da coluna no simplificado'
     * ]
     * @var array
     */
    protected $linhasSimplificado = [];
    protected $linhasSimplificadoCompleto = [];

    protected $linhasOrganizadas = [];

    /**
     * Linhas que devem ser positivas mesmo que o sinal seja negativo.
     * No exemplo do anexo três a linha "Dedução de Receita para Formação do FUNDEB" deve ser positiva para somar na
     * linha "DEDUÇÕES(II)"
     * @var int[]
     */
    protected $linhasAplicarAbs = [];

    /**
     * @param $filtros
     * @throws Exception
     */
    public function __construct($filtros)
    {
        $this->exercicio = $filtros['DB_anousu'];
        $this->emissor = \InstituicaoRepository::getInstituicaoByCodigo($filtros['DB_instit']);
        $this->constructAssinaturas($filtros['DB_instit']);
        if (isset($filtros["desvincularDadosInstituicaoCamara"]) && $filtros["desvincularDadosInstituicaoCamara"]) {
            $this->constructInstituicoes(DBConfig::all()->where('db21_tipoinstit', '<>', 2));
        } else {
            $this->constructInstituicoes(DBConfig::all());
        }
        $this->constructPeriodo($filtros['periodo']);
        $this->getMesesProcessar();
        $this->constructRelatorio($filtros['codigo_relatorio']);

        $this->processaEnteFederativo();
    }

    public function emitir()
    {
        $this->processar();

        foreach ($this->linhasOrganizadas as $section => $linhas) {
            $this->parser->addCollection($section, $linhas);
        }

        $mesesProcessar = $this->getMesesProcessar();

        foreach ($mesesProcessar as $mesProcessar) {
            $mes = "{$mesProcessar->nome_abreviado}/{$mesProcessar->ano}";
            $this->parser->setVariavel($mesProcessar->coluna, $mes);
        }

        $mes1 = $mesesProcessar[0];
        $mes12 = $mesesProcessar[11];

        $mesesPeriodo = sprintf(
            '%s - %s',
            "{$mes1->nome}/{$mes1->ano}",
            "{$mes12->nome}/{$mes12->ano}"
        );
        $this->parser->setVariavel('exercicio', $this->exercicio);
        $this->parser->setEnteFederativo($this->enteFederativo);
        $this->parser->setEmissor($this->emissor);
        $this->parser->setPeriodo($this->periodo->getDescricao());
        $this->parser->setAnoReferencia($this->exercicio);
        $this->parser->setMesesPeriodo($mesesPeriodo);
        $this->parser->setNotaExplicativa($this->getNotaExplicativa());
        $this->parser->setNomePrefeito($this->assinatura->assinaturaPrefeito());
        $this->parser->setNomeContador($this->assinatura->assinaturaContador());
        $this->parser->setNomeOrdenador($this->assinatura->assinaturaSecretarioFazenda());

        $filename = $this->parser->gerar();

        Settings::setTempDir('tmp/');
        $filePdf = basename($filename, ".xlsx").".pdf";
        $converter = new OfficeConverter($filename);
        $converter->convertTo($filePdf);

        return [
            'xls' => $filename,
            'xlsLinkExterno' => ECIDADE_REQUEST_PATH . $filename,
            'pdf' => Settings::getTempDir().$filePdf
        ];
    }

    public function processar()
    {
        $this->processaLinhas($this->linhas);
        $this->processaLinhasMensais($this->linhas);
        $this->criaProriedadesValor();
        $this->organizaLinhas();
        $this->aplicarAbsLinha();
        $this->calculaTotalUltimos12Meses();

        $this->totalizarLinhas();
    }

    /**
     * Retorna as linhas necessárias para uso no Anexo I da RGF
     * @return \stdClass[]
     */
    public function getApuracaoCumprimentoLimiteLegal()
    {
        $this->processar();

        $linhas = [];
        foreach ($this->linhasApuracaoCumprimentoLegal as $ordem => $descricao) {
            $linhas[] = (object)['descricao' => $descricao, 'total_meses' => $this->linhas[$ordem]->total_meses];
        }

        return $linhas;
    }

    protected function totalizarLinhas()
    {
        $this->calcularTotalizadores();
        $this->posTotalizarLinhas();
    }

    /**
     * Encapsular lista de funções de cálculos das linhas que será executada após somar
     * Exemplo as subtrações das linhas
     * @return mixed
     */
    abstract protected function posTotalizarLinhas();

    /**
     * @throws Exception
     */
    protected function calcularTotalizadores()
    {
        $mesesProcessar = $this->getMesesProcessar();
        // realiza a soma das linhas
        foreach ($this->totalizar as $linha => $somar) {
            $linhaTotalizar = $this->linhas[$linha];

            foreach ($somar as $idLinhaSoma) {
                $linhaSomar = $this->linhas[$idLinhaSoma];
                // totaliza os meses
                foreach ($mesesProcessar as $mesProcessar) {
                    $linhaTotalizar->{$mesProcessar->coluna} += $linhaSomar->{$mesProcessar->coluna};
                    $linhaTotalizar->total_meses += $linhaSomar->{$mesProcessar->coluna};
                }
                $linhaTotalizar->previsao_atualizada += $linhaSomar->previsao_atualizada;
            }
        }
    }

    /**
     * Soma a coluna meses para totalizar os ultimos 12 meses
     * @throws Exception
     */
    protected function calculaTotalUltimos12Meses()
    {
        foreach ($this->linhas as $linha) {
            foreach ($this->getMesesProcessar() as $mesProcessar) {
                $linha->total_meses += $linha->{$mesProcessar->coluna};
            }
        }
    }

    /**
     * Calcula a subtração onde o resultado é $a = $b - $c
     * @param int $a ordem da linha
     * @param int $b ordem da linha
     * @param int $c ordem da linha
     * @throws Exception
     */
    protected function calculaLinhaSubtracao($a, $b, $c)
    {
        $linhaA = $this->linhas[$a];
        $linhaB = $this->linhas[$b];
        $linhaC = $this->linhas[$c];
        $mesesProcessar = $this->getMesesProcessar();
        foreach ($mesesProcessar as $mes) {
            $linhaA->{$mes->coluna} = $linhaB->{$mes->coluna} - $linhaC->{$mes->coluna};
        }
        $linhaA->total_meses = $linhaB->total_meses - $linhaC->total_meses;
        $linhaA->previsao_atualizada = $linhaB->previsao_atualizada - $linhaC->previsao_atualizada;
    }

    /**
     * Calcula a subtração onde o resultado é $a = $b + $c
     * @param int $a ordem da linha
     * @param int $b ordem da linha
     * @param int $c ordem da linha
     * @throws Exception
     */
    protected function calculaLinhaSoma($a, $b, $c)
    {
        $linhaA = $this->linhas[$a];
        $linhaB = $this->linhas[$b];
        $linhaC = $this->linhas[$c];
        $mesesProcessar = $this->getMesesProcessar();
        foreach ($mesesProcessar as $mes) {
            $linhaA->{$mes->coluna} = $linhaB->{$mes->coluna} + $linhaC->{$mes->coluna};
        }
        $linhaA->total_meses = $linhaB->total_meses + $linhaC->total_meses;
        $linhaA->previsao_atualizada = $linhaB->previsao_atualizada + $linhaC->previsao_atualizada;
    }

    public function processaLinhasSimplificado()
    {
        $this->processar();
        $linhas = [];
        foreach ($this->linhasSimplificado as $ordem => $descricao) {
            $linhas[] = $this->createObjetoSimplificado($descricao, $this->linhas[$ordem]->total_meses);
        }
        return $linhas;
    }

    public function processaLinhasSimplificadoCompleto()
    {
        $this->processar();
        $linhas = [];
        foreach ($this->linhasSimplificadoCompleto as $ordem => $descricao) {
            $linhas[] = $this->createObjetoSimplificado($descricao, $this->linhas[$ordem]->total_meses);
        }
        return $linhas;
    }

    private function createObjetoSimplificado($descricao, $valor)
    {
        return (object)[
            'descricao' => $descricao,
            'ate_bimestre' => $valor,
            'totalizar' => false,
            'nivel' => 1,
        ];
    }

    private function aplicarAbsLinha()
    {
        foreach ($this->linhasAplicarAbs as $linhaAplicarAbs) {
            $linha = $this->linhas[$linhaAplicarAbs];
            $mesesProcessar = $this->getMesesProcessar();
            foreach ($mesesProcessar as $mes) {
                $linha->{$mes->coluna} = abs($linha->{$mes->coluna});
            }
            $linha->total_meses = abs($linha->total_meses);
            $linha->previsao_atualizada = abs($linha->previsao_atualizada);
        }
    }
    
    public function getLinhasProcessadas()
    {
        $this->processar();
        return $this->linhas;
    }
}
