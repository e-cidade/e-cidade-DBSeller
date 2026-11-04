<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoSeisFactory;
use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use stdClass;

class AnexoSeisService extends AnexosService
{
    const SIGLA = "A06";
    const RECEITAS_PRIMARIAS = 1;
    const DESPESAS_PRIMARIAS = 2;
    const META_FISCAL_RESULTADO_PRIMARIO = 3;
    const JUROS_NOMINAIS = 4;
    const META_FISCAL_RESULTADO_NOMINAL = 5;
    const CALCULO_RESULTADO_NOMINAL = 6;
    const AJUSTE_METODOLOGICO = 7;
    const INFORMACOES_ADICIONAIS = 8;
    
    protected $dePara = [
        self::RECEITAS_PRIMARIAS => [
            1  => 1 ,
            2  => 2 ,
            3  => 3 ,
            4  => 4 ,
            5  => 5 ,
            6  => 9 ,
            7  => 10,
            8  => 11,
            9  => 12,
            10 => 13,
            11 => 14,
            12 => 15,
            13 => 16,
            14 => 18,
            15 => 19,
            16 => 20,
            17 => 21,
            18 => 22,
            19 => 23,
            20 => 24,
            21 => 25,
            22 => 26,
            23 => 27,
            24 => 28,
            25 => 29,
            26 => 30,
            27 => 31,
            28 => 32,
            29 => 33,
            30 => 34,
            31 => 35,
            32 => 36,
            33 => 37,
            34 => 38,
            35 => 39,
            36 => 40,
            37 => 41,
            38 => 42,
            39 => 43,
            40 => 44,
            41 => 45,
            42 => 46,
            43 => 47
        ],
        self::DESPESAS_PRIMARIAS => [
            44 => 48,
            45 => 49,
            46 => 50,
            47 => 51,
            48 => 54,
            49 => 55,
            50 => 56,
            51 => 57,
            52 => 58,
            53 => 59,
            54 => 60,
            55 => 61,
            56 => 62,
            57 => 63,
            58 => 64,
            59 => 65,
            60 => 66,
            61 => 67,
            62 => 68,
            63 => 69,
            64 => 70
        ],
        self::META_FISCAL_RESULTADO_PRIMARIO => [
            67 => 73
        ],
        self::JUROS_NOMINAIS => [
            68 => 74,
            69 => 75,
            70 => 76
        ],
        self::CALCULO_RESULTADO_NOMINAL => [
            71 => 77,
            72 => 78,
            73 => 79,
            74 => 80,
            75 => 81,
            76 => 82,
            77 => 83,
            78 => 84,
            79 => 85
        ],
        self::META_FISCAL_RESULTADO_NOMINAL => [
            80 => 86
        ],
        self::AJUSTE_METODOLOGICO => [
            81 => 87,
            82 => 88,
            83 => 89,
            84 => 90,
            85 => 91,
            86 => 92,
            87 => 93,
            88 => 94
        ],
        self::INFORMACOES_ADICIONAIS => [
            89 => 95,
            90 => 96,
            91 => 97,
            92 => 98
        ]
    ];
    
    public function __construct($exercicio, $filtros)
    {
        
        /*
         * Linhas 52,53,65,66 do detalhe 2 - DESPESAS_PRIMARIAS
         * São desconsideradas
         */
        $filtros["instituicoes"] = $this->buscaInstituicoes([], $filtros["desvincularDadosInstituicaoCamara"]);
        $relatorio = AnexoSeisFactory::getService($exercicio, $filtros);
        $this->linhasProcessadas = $relatorio->getLinhasProcessadas();
        $this->calculaLinhasTotalizadoras();
        $this->exercicio = $exercicio;
        $this->periodo = $relatorio->getPeriodo();
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        $this->nomeArquivo = self::SIGLA."_{$exercicio}".str_pad($this->periodo->getOrdem(), 2, "0", STR_PAD_LEFT);
        $this->setfilePath("tmp/{$this->nomeArquivo}.TXT");
    }
    
    private function calculaLinhasTotalizadoras()
    {
        $this->calculaLinha65();
        $this->calculaLinha66();
        $this->calculaLinha70();
        $this->calculaLinha79();
    }
    
    private function calculaLinha65()
    {
        $this->linhasProcessadas[65]->total_creditos = 0;
        $this->linhasProcessadas[65]->empenhado_liquido_acumulado = 0;
        $this->linhasProcessadas[65]->liquidado_acumulado = 0;
        $this->linhasProcessadas[65]->pago_acumulado = 0;
        $this->linhasProcessadas[65]->pagamento_rp_processado = 0;
        $this->linhasProcessadas[65]->liquidacoes_rp = 0;
        $this->linhasProcessadas[65]->pagamento_rp_nao_processado = 0;
    }
    
    private function calculaLinha66()
    {
        $this->linhasProcessadas[66]->total_creditos = 0;
        $this->linhasProcessadas[66]->empenhado_liquido_acumulado = 0;
        $this->linhasProcessadas[66]->liquidado_acumulado = 0;
        $this->linhasProcessadas[66]->pago_acumulado = 0;
        $this->linhasProcessadas[66]->pagamento_rp_processado = 0;
        $this->linhasProcessadas[66]->liquidacoes_rp = 0;
        $this->linhasProcessadas[66]->pagamento_rp_nao_processado = 0;
    }
    
    private function calculaLinha70()
    {
        $this->linhasProcessadas[70]->saldo_final_acumulado = $this->linhasProcessadas[70]->valor;
    }
    
    private function calculaLinha79()
    {
        $this->linhasProcessadas[79]->saldo_anterior_acumulado = 0;
        $this->linhasProcessadas[79]->saldo_final_acumulado = $this->linhasProcessadas[79]->valor;
    }
    
    private function buscaValorInformacoesAdicionais($linhaRelatorio)
    {
        /*
         * Verificamos a propriedade a ser utilizada como valor
         */
        if (isset($linhaRelatorio->valor)) {
            return $linhaRelatorio->valor;
        } elseif (isset($linhaRelatorio->previsao_atualizada)) {
            return $linhaRelatorio->previsao_atualizada;
        } elseif (isset($linhaRelatorio->saldo_final_acumulado)) {
            return $linhaRelatorio->saldo_final_acumulado;
        } elseif (isset($linhaRelatorio->total_creditos)) {
            return $linhaRelatorio->total_creditos;
        } else {
            throw new Exception("Propriedade utilizada como valor nÃ£o encontrada.\nLinha:{$linhaRelatorio->linha}");
        }
    }
    
    public function makeHeader()
    {
        $header = new stdClass();
        $header->tipoRegistro = "0";
        $header->nomeArquivo = $this->nomeArquivo;
        $header->bimReferencia = explode("_", $this->nomeArquivo)[1];
        $header->tipoArquivo = "O";
        $header->dataGeracaoArq = date("d/m/Y");
        $header->horaGeracaoArq = date("H:i:s");
        $header->codigoOrgao = str_pad($this->codigoOrgao, 4, " ", STR_PAD_LEFT);
        $header->nomeOrgao = str_pad(substr($this->nomeOrgao, 0, 100), 100, " ");
        $header->brancos = str_repeat(" ", 268);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }
    
    private function makeDetalheReceitasPrimarias()
    {
        $tipoRegistro = self::RECEITAS_PRIMARIAS;
        foreach ($this->linhasProcessadas as $linhaRelatorio) {
            if (!$this->utilizaLinhaPorDetalhe($linhaRelatorio->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = str_pad($tipoRegistro, 2, "0", STR_PAD_LEFT);
            $detalhe->campo = $this->getCodigoCampo($linhaRelatorio->linha, $this->dePara);
            $detalhe->previsaoAtualizada = $this->formataValor($linhaRelatorio->previsao_atualizada);
            $detalhe->receitasRealizadas = $this->formataValor($linhaRelatorio->arrecadado_acumulado);
            $detalhe->brancos = str_repeat(" ", 376);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalheDespesasPrimarias()
    {
        $tipoRegistro = self::DESPESAS_PRIMARIAS;
        foreach ($this->linhasProcessadas as $linhaRelatorio) {
            if (!$this->utilizaLinhaPorDetalhe($linhaRelatorio->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = str_pad($tipoRegistro, 2, "0", STR_PAD_LEFT);
            $detalhe->campo = $this->getCodigoCampo($linhaRelatorio->linha, $this->dePara);
            $detalhe->dotacaoAtualizada = $this->formataValor($linhaRelatorio->total_creditos);
            $detalhe->zeros = str_repeat("0", 35);
            $detalhe->despesasEmpenhadas = $this->formataValor($linhaRelatorio->empenhado_liquido_acumulado);
            $detalhe->despesasLiquidadas = $this->formataValor($linhaRelatorio->liquidado_acumulado);
            $detalhe->despesasPagas = $this->formataValor($linhaRelatorio->pago_acumulado);
            $detalhe->restosPagarProcessadosPagos = $this->formataValor($linhaRelatorio->pagamento_rp_processado);
            $detalhe->restosPagarNaoProcessadosLiquidados = $this->formataValor($linhaRelatorio->liquidacoes_rp);
            $detalhe->restosPagarNaoProcessadosPagos = $this->formataValor(
                $linhaRelatorio->pagamento_rp_nao_processado
            );
            $detalhe->brancos = str_repeat(" ", 271);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalheMetaFiscalResultadoPrimario()
    {
        $tipoRegistro = self::META_FISCAL_RESULTADO_PRIMARIO;
        foreach ($this->linhasProcessadas as $linhaRelatorio) {
            if (!$this->utilizaLinhaPorDetalhe($linhaRelatorio->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = str_pad($tipoRegistro, 2, "0", STR_PAD_LEFT);
            $detalhe->campo = $this->getCodigoCampo($linhaRelatorio->linha, $this->dePara);
            $detalhe->valorCorrente = $this->formataValor($linhaRelatorio->valor);
            $detalhe->brancos = str_repeat(" ", 390);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalheJurosNominais()
    {
        $tipoRegistro = self::JUROS_NOMINAIS;
        foreach ($this->linhasProcessadas as $linhaRelatorio) {
            if (!$this->utilizaLinhaPorDetalhe($linhaRelatorio->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = str_pad($tipoRegistro, 2, "0", STR_PAD_LEFT);
            $detalhe->campo = $this->getCodigoCampo($linhaRelatorio->linha, $this->dePara);
            $detalhe->valorIncorrido = $this->formataValor($linhaRelatorio->saldo_final_acumulado);
            $detalhe->brancos = str_repeat(" ", 390);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalheMetaFiscalResultadoNominal()
    {
        $tipoRegistro = self::META_FISCAL_RESULTADO_NOMINAL;
        foreach ($this->linhasProcessadas as $linhaRelatorio) {
            if (!$this->utilizaLinhaPorDetalhe($linhaRelatorio->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = str_pad($tipoRegistro, 2, "0", STR_PAD_LEFT);
            $detalhe->campo = $this->getCodigoCampo($linhaRelatorio->linha, $this->dePara);
            $detalhe->valorCorrente = $this->formataValor($linhaRelatorio->valor);
            $detalhe->brancos = str_repeat(" ", 395);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalheCalculoResultadoNominal()
    {
        $tipoRegistro = self::CALCULO_RESULTADO_NOMINAL;
        foreach ($this->linhasProcessadas as $linhaRelatorio) {
            if (!$this->utilizaLinhaPorDetalhe($linhaRelatorio->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = str_pad($tipoRegistro, 2, "0", STR_PAD_LEFT);
            $detalhe->campo = $this->getCodigoCampo($linhaRelatorio->linha, $this->dePara);
            $detalhe->saldoExercicioAnterior = $this->formataValor($linhaRelatorio->saldo_anterior_acumulado);
            $detalhe->saldoAteBimestre = $this->formataValor($linhaRelatorio->saldo_final_acumulado);
            $detalhe->brancos = str_repeat(" ", 376);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalheAjusteMetodologico()
    {
        $tipoRegistro = self::AJUSTE_METODOLOGICO;
        foreach ($this->linhasProcessadas as $linhaRelatorio) {
            if (!$this->utilizaLinhaPorDetalhe($linhaRelatorio->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = str_pad($tipoRegistro, 2, "0", STR_PAD_LEFT);
            $detalhe->campo = $this->getCodigoCampo($linhaRelatorio->linha, $this->dePara);
            $detalhe->valorAteBimestre = $this->formataValor($linhaRelatorio->saldo_final_acumulado);
            $detalhe->brancos = str_repeat(" ", 390);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalheInformacoesAdicionais()
    {
        $tipoRegistro = self::INFORMACOES_ADICIONAIS;
        foreach ($this->linhasProcessadas as $linhaRelatorio) {
            if (!$this->utilizaLinhaPorDetalhe($linhaRelatorio->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = str_pad($tipoRegistro, 2, "0", STR_PAD_LEFT);
            $detalhe->campo = $this->getCodigoCampo($linhaRelatorio->linha, $this->dePara);
            $detalhe->previsaoOrcamentaria = $this->formataValor(
                $this->buscaValorInformacoesAdicionais($linhaRelatorio)
            );
            $detalhe->brancos = str_repeat(" ", 390);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    public function makeDetalhes()
    {
        $this->makeDetalheReceitasPrimarias();
        $this->makeDetalheDespesasPrimarias();
        $this->makeDetalheMetaFiscalResultadoPrimario();
        $this->makeDetalheJurosNominais();
        $this->makeDetalheMetaFiscalResultadoNominal();
        $this->makeDetalheCalculoResultadoNominal();
        $this->makeDetalheAjusteMetodologico();
        $this->makeDetalheInformacoesAdicionais();
    }
        
    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "10";
        $trailler->brancos = str_repeat(" ", 406);
        $trailler->numRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
}
