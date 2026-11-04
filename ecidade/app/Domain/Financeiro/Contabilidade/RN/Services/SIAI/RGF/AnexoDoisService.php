<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RGF;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoDoisRgfFactory;
use Periodo;
use stdClass;

class AnexoDoisService extends AnexosService
{
    const SIGLA = "A16";
    const MOVIMENTO_ENTE_FEDERACAO = 1;
    
    protected $dePara = [
        self::MOVIMENTO_ENTE_FEDERACAO => [
             1 => 1,
             2 => 2,
             3 => 3,
             4 => 4,
             5 => 5,
             6 => 6,
             7 => 7,
             8 => 8,
             9 => 9,
            10 => 10,
            11 => 11,
            12 => 12,
            13 => 13,
            14 => 14,
            15 => 15,
            16 => 16,
            17 => 17,
            18 => 18,
            19 => 19,
            20 => 20,
            21 => 21,
            22 => 22,
            23 => 23,
            24 => 24,
            25 => 25,
            26 => 26,
            27 => 27,
            28 => 28,
            29 => 29,
            30 => 30,
            31 => 31,
            32 => 32,
            33 => 33,
            34 => 34,
            35 => 35,
            36 => 36,
            37 => 37,
            38 => 38,
            39 => 39,
            40 => 40
            ]
    ];

    public function __construct($filtros)
    {
        $bimestre = new Periodo($filtros['periodo']);

        $filtros['periodo'] = $this->converteBimestreParaQuadrimestre($filtros['periodo']);
        $filtros["instituicoes"] = $this->buscaInstituicoes([], $filtros["desvincularDadosInstituicaoCamara"]);
        $relatorio = AnexoDoisRgfFactory::getService($filtros['DB_anousu'], $filtros);
        $this->linhasProcessadas = $relatorio->getLinhasProcessadas();
        $this->calculaLinhasTotalizadoras();
        $this->exercicio = $filtros['DB_anousu'];
        $this->periodo = $relatorio->getPeriodo();
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        $this->nomeArquivo = self::SIGLA."_{$this->exercicio}".str_pad($bimestre->getOrdem(), 2, "0", STR_PAD_LEFT);
        $this->setfilePath("tmp/{$this->nomeArquivo}.TXT");
    }
    
    private function calculaLinhasTotalizadoras()
    {
        $this->calculaLinha01();
        $this->calculaLinha20();
        $this->calculaLinha21();
        $this->calculaLinha26();
        $this->calculaLinha29();
        $this->calculaLinha30();
        $this->calculaLinha31();
        $this->calculaLinha32();
        $this->calculaLinha33();
    }

    /*
     * DÍVIDA CONSOLIDADA - DC (I)
     */
    private function calculaLinha01()
    {
        $this->linhasProcessadas[1]->saldo_anterior_acumulado =
        $this->linhasProcessadas[2]->saldo_anterior_acumulado
        + $this->linhasProcessadas[3]->saldo_anterior_acumulado
        + $this->linhasProcessadas[18]->saldo_anterior_acumulado
        + $this->linhasProcessadas[19]->saldo_anterior_acumulado;

        $this->linhasProcessadas[1]->primeiro_periodo =
        $this->linhasProcessadas[2]->primeiro_periodo
        + $this->linhasProcessadas[3]->primeiro_periodo
        + $this->linhasProcessadas[18]->primeiro_periodo
        + $this->linhasProcessadas[19]->primeiro_periodo;

        $this->linhasProcessadas[1]->segundo_periodo =
        $this->linhasProcessadas[2]->segundo_periodo
        + $this->linhasProcessadas[3]->segundo_periodo
        + $this->linhasProcessadas[18]->segundo_periodo
        + $this->linhasProcessadas[19]->segundo_periodo;

        $this->linhasProcessadas[1]->terceiro_periodo =
        $this->linhasProcessadas[2]->terceiro_periodo
        + $this->linhasProcessadas[3]->terceiro_periodo
        + $this->linhasProcessadas[18]->terceiro_periodo
        + $this->linhasProcessadas[19]->terceiro_periodo;
    }
    
    /*
     * DEDUÇÕES (II)
     */
    private function calculaLinha20()
    {
        $this->calculaLinha21();

        $this->linhasProcessadas[20]->saldo_anterior_acumulado = $this->linhasProcessadas[21]->saldo_anterior_acumulado
        + $this->linhasProcessadas[25]->saldo_anterior_acumulado;

        $this->linhasProcessadas[20]->primeiro_periodo = $this->linhasProcessadas[21]->primeiro_periodo
        + $this->linhasProcessadas[25]->primeiro_periodo;

        $this->linhasProcessadas[20]->segundo_periodo = $this->linhasProcessadas[21]->segundo_periodo
        + $this->linhasProcessadas[25]->segundo_periodo;

        $this->linhasProcessadas[20]->terceiro_periodo = $this->linhasProcessadas[21]->terceiro_periodo
        + $this->linhasProcessadas[25]->terceiro_periodo;
    }

    /*
     * Disponibilidade de Caixa
     */
    private function calculaLinha21()
    {
        $this->linhasProcessadas[21]->saldo_anterior_acumulado = $this->linhasProcessadas[22]->saldo_anterior_acumulado
        - $this->linhasProcessadas[23]->saldo_anterior_acumulado
        - $this->linhasProcessadas[24]->saldo_anterior_acumulado;

        $this->linhasProcessadas[21]->primeiro_periodo = $this->linhasProcessadas[22]->primeiro_periodo
        - $this->linhasProcessadas[23]->primeiro_periodo
        - $this->linhasProcessadas[24]->primeiro_periodo;

        $this->linhasProcessadas[21]->segundo_periodo = $this->linhasProcessadas[22]->segundo_periodo
        - $this->linhasProcessadas[23]->segundo_periodo
        - $this->linhasProcessadas[24]->segundo_periodo;

        $this->linhasProcessadas[21]->terceiro_periodo = $this->linhasProcessadas[22]->terceiro_periodo
        - $this->linhasProcessadas[23]->terceiro_periodo
        - $this->linhasProcessadas[24]->terceiro_periodo;
    }

    /*
     * DÍVIDA CONSOLIDADA LÍQUIDA² (DCL) (III) = (I - II)
     */
    private function calculaLinha26()
    {
        $this->linhasProcessadas[29]->primeiro_periodo = $this->linhasProcessadas[01]->primeiro_periodo
        - $this->linhasProcessadas[20]->primeiro_periodo;
    }

    /*
     * RECEITA CORRENTE LÍQUIDA AJUSTADA PARA CÁLCULO DOS LIMITES DE ENDIVIDAMENTO (VI) = (IV - V)
     */
    private function calculaLinha29()
    {
        $this->linhasProcessadas[29]->saldo_anterior_acumulado = $this->linhasProcessadas[27]->saldo_anterior_acumulado
        - $this->linhasProcessadas[28]->saldo_anterior_acumulado;

        $this->linhasProcessadas[29]->primeiro_periodo = $this->linhasProcessadas[27]->primeiro_periodo
        - $this->linhasProcessadas[28]->primeiro_periodo;

        $this->linhasProcessadas[29]->segundo_periodo = $this->linhasProcessadas[27]->segundo_periodo
        - $this->linhasProcessadas[28]->segundo_periodo;

        $this->linhasProcessadas[29]->terceiro_periodo = $this->linhasProcessadas[27]->terceiro_periodo
        - $this->linhasProcessadas[28]->terceiro_periodo;
    }

    /*
     * % da DC sobre a RCL AJUSTADA (I/VI)
     */
    private function calculaLinha30()
    {
        $this->calculaLinha01();
        $this->calculaLinha29();
        
        $this->linhasProcessadas[30]->saldo_anterior_acumulado = 0;
        $this->linhasProcessadas[30]->primeiro_periodo = 0;
        $this->linhasProcessadas[30]->segundo_periodo = 0;
        $this->linhasProcessadas[30]->terceiro_periodo = 0;

        if ($this->linhasProcessadas[29]->saldo_anterior_acumulado > 0) {
            $this->linhasProcessadas[30]->saldo_anterior_acumulado =
            (
                ($this->linhasProcessadas[1]->saldo_anterior_acumulado
                / $this->linhasProcessadas[29]->saldo_anterior_acumulado)*100
            );
        }

        if ($this->linhasProcessadas[29]->primeiro_periodo > 0) {
            $this->linhasProcessadas[30]->primeiro_periodo =
            (($this->linhasProcessadas[1]->primeiro_periodo / $this->linhasProcessadas[29]->primeiro_periodo)*100);
        }

        if ($this->linhasProcessadas[29]->segundo_periodo > 0) {
            $this->linhasProcessadas[30]->segundo_periodo =
            (($this->linhasProcessadas[1]->segundo_periodo / $this->linhasProcessadas[29]->segundo_periodo)*100);
        }

        if ($this->linhasProcessadas[29]->terceiro_periodo > 0) {
            $this->linhasProcessadas[30]->terceiro_periodo =
            (($this->linhasProcessadas[1]->terceiro_periodo / $this->linhasProcessadas[29]->terceiro_periodo)*100);
        }
    }

    /*
     * % da DCL sobre a RCL AJUSTADA (III/VI)
     */
    private function calculaLinha31()
    {
        $this->calculaLinha26();
        $this->calculaLinha29();

        $this->linhasProcessadas[31]->saldo_anterior_acumulado = 0;
        $this->linhasProcessadas[31]->primeiro_periodo = 0;
        $this->linhasProcessadas[31]->segundo_periodo = 0;
        $this->linhasProcessadas[31]->terceiro_periodo = 0;

        if ($this->linhasProcessadas[29]->saldo_anterior_acumulado > 0) {
            $this->linhasProcessadas[31]->saldo_anterior_acumulado =
            (
                ($this->linhasProcessadas[26]->saldo_anterior_acumulado
                / $this->linhasProcessadas[29]->saldo_anterior_acumulado)*100
            );
        }
        if ($this->linhasProcessadas[29]->primeiro_periodo > 0) {
            $this->linhasProcessadas[31]->primeiro_periodo =
            (($this->linhasProcessadas[26]->primeiro_periodo / $this->linhasProcessadas[29]->primeiro_periodo)*100);
        }

        if ($this->linhasProcessadas[29]->segundo_periodo > 0) {
            $this->linhasProcessadas[31]->segundo_periodo =
            (($this->linhasProcessadas[26]->segundo_periodo / $this->linhasProcessadas[29]->segundo_periodo)*100);
        }

        if ($this->linhasProcessadas[29]->terceiro_periodo > 0) {
            $this->linhasProcessadas[31]->terceiro_periodo =
            (($this->linhasProcessadas[26]->terceiro_periodo / $this->linhasProcessadas[29]->terceiro_periodo)*100);
        }
    }

    /*
     * LIMITE DEFINIDO POR RESOLUÇÃO DO SENADO FEDERAL - 120%
     */
    private function calculaLinha32()
    {
        $this->linhasProcessadas[32]->saldo_anterior_acumulado =
        round($this->linhasProcessadas[29]->saldo_anterior_acumulado * 1.2, 2);
        $this->linhasProcessadas[32]->primeiro_periodo = round($this->linhasProcessadas[29]->primeiro_periodo * 1.2, 2);
        $this->linhasProcessadas[32]->segundo_periodo = round($this->linhasProcessadas[29]->segundo_periodo * 1.2, 2);
        $this->linhasProcessadas[32]->terceiro_periodo = round($this->linhasProcessadas[29]->terceiro_periodo * 1.2, 2);
    }
    
    /*
     * LIMITE DE ALERTA (inciso III do § 1º do art. 59 da LRF) - 108%
     */
    private function calculaLinha33()
    {
        $this->linhasProcessadas[33]->saldo_anterior_acumulado =
        round($this->linhasProcessadas[29]->saldo_anterior_acumulado * 1.08, 2);
        $this->linhasProcessadas[33]->primeiro_periodo =
        round($this->linhasProcessadas[29]->primeiro_periodo * 1.08, 2);
        $this->linhasProcessadas[33]->segundo_periodo = round($this->linhasProcessadas[29]->segundo_periodo * 1.08, 2);
        $this->linhasProcessadas[33]->terceiro_periodo =
        round($this->linhasProcessadas[29]->terceiro_periodo * 1.08, 2);
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
        $header->brancos = str_repeat(" ", 269);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }
    
    public function makeDetalhes()
    {
        $tipoRegistro = self::MOVIMENTO_ENTE_FEDERACAO;
        foreach ($this->linhasProcessadas as $linhaRelatorio) {
            if (!$this->utilizaLinhaPorDetalhe($linhaRelatorio->linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $campo = $this->getCodigoCampo($linhaRelatorio->linha, $this->dePara);
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->campo = str_pad($campo, 2, "0", STR_PAD_LEFT);
            $detalhe->brancos2 = str_repeat(" ", 7);
            $detalhe->saldoExercAnt = $this->formataValor($linhaRelatorio->saldo_anterior_acumulado);
            $detalhe->saldoAte1QuadSem = $this->formataValor($linhaRelatorio->primeiro_periodo);
            $detalhe->saldoAte2QuadSEm = $this->formataValor($linhaRelatorio->segundo_periodo);
            $detalhe->saldoAte3QuadSem = $this->formataValor($linhaRelatorio->terceiro_periodo);
            $detalhe->brancos3 = str_repeat(" ", 342);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
        
    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "9";
        $trailler->brancos = str_repeat(" ", 408);
        $trailler->numRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
}
