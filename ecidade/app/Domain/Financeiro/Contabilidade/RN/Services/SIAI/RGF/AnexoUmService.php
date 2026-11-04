<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RGF;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use App\Domain\Financeiro\Contabilidade\Services\Relatorios\LRF\RGF\AnexoUm\AnexoUmMdfService as RelatorioBase;
use Periodo;
use stdClass;

class AnexoUmService extends AnexosService
{
    const SIGLA = "A15";
    const MOVIMENTO_ENTE_FEDERACAO = 1;
    const APURACAO_CUMPRIMENTO_LIMITE = 2;

    protected $dePara = [
        self::MOVIMENTO_ENTE_FEDERACAO => [
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4,
            5 => 6,
            6 => 7,
            7 => 8,
            8 => 9,
            9 => 10,
            10 => 11,
            11 => 12,
            12 => 13,
            13 => 14,
            14 => 15,
            15 => 17
        ],
        self::APURACAO_CUMPRIMENTO_LIMITE => [
            16 => 18,
            17 => 19,
            18 => 20,
            19 => 21,
            20 => 22,
            21 => 23,
            22 => 24,
            23 => 25
        ]

    ];

    public function __construct($filtros)
    {
        $bimestre = new Periodo($filtros['periodo']);

        $filtros['periodo'] = $this->converteBimestreParaQuadrimestre($filtros['periodo']);
        $filtros["instituicoes"] = $this->buscaInstituicoes([], $filtros["desvincularDadosInstituicaoCamara"]);

        $relatorio = new RelatorioBase($filtros);
        $this->linhasProcessadas = $relatorio->getLinhasProcessadas();
        $this->exercicio = $filtros['DB_anousu'];
        $this->periodo = $relatorio->getPeriodo();
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        $this->nomeArquivo = self::SIGLA."_{$this->exercicio}".str_pad($bimestre->getOrdem(), 2, "0", STR_PAD_LEFT);
        $this->setfilePath("tmp/{$this->nomeArquivo}.TXT");
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
        $header->brancos = str_repeat(" ", 69);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }

    public function makeDetalhes()
    {
        $this->makeDetalhes1();
        $this->makeDetalhes2();
    }

    private function makeDetalhes1()
    {
        $tipoRegistro = self::MOVIMENTO_ENTE_FEDERACAO;
        foreach ($this->linhasProcessadas as $linha => $dadosLinha) {
            if (!$this->utilizaLinhaPorDetalhe($linha, $tipoRegistro, $this->dePara)) {
                continue;
            }

            $detalhe = new stdClass();
            $detalhe->tipoRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->campo = $this->getCodigoCampo($dadosLinha->linha, $this->dePara);
            $detalhe->brancos2 = str_repeat(" ", 7);
            $detalhe->mesReferencia11 = $this->formataValor($dadosLinha->mes_1);
            $detalhe->mesReferencia10 = $this->formataValor($dadosLinha->mes_2);
            $detalhe->mesReferencia9 = $this->formataValor($dadosLinha->mes_3);
            $detalhe->mesReferencia8 = $this->formataValor($dadosLinha->mes_4);
            $detalhe->mesReferencia7 = $this->formataValor($dadosLinha->mes_5);
            $detalhe->mesReferencia6 = $this->formataValor($dadosLinha->mes_6);
            $detalhe->mesReferencia5 = $this->formataValor($dadosLinha->mes_7);
            $detalhe->mesReferencia4 = $this->formataValor($dadosLinha->mes_8);
            $detalhe->mesReferencia3 = $this->formataValor($dadosLinha->mes_9);
            $detalhe->mesReferencia2 = $this->formataValor($dadosLinha->mes_10);
            $detalhe->mesReferencia1 = $this->formataValor($dadosLinha->mes_11);
            $detalhe->mesReferencia = $this->formataValor($dadosLinha->mes_12);
            $detalhe->Ult12Meses = $this->formataValor($dadosLinha->total_meses);
            $detalhe->RPNP = $this->formataValor($dadosLinha->inscricao_menos_anulacao_rp_nao_processado);
            $detalhe->brancos3 = str_repeat(" ", 2);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

    private function makeDetalhes2()
    {
        $tipoRegistro = self::APURACAO_CUMPRIMENTO_LIMITE;
        foreach ($this->linhasProcessadas as $linha => $dadosLinha) {
            if (!$this->utilizaLinhaPorDetalhe($linha, $tipoRegistro, $this->dePara)) {
                continue;
            }

            $detalhe = new stdClass();
            $detalhe->tipoRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->campo = $this->getCodigoCampo($dadosLinha->linha, $this->dePara);
            $detalhe->brancos2 = str_repeat(" ", 7);
            $detalhe->ValorApuracao = $this->formataValor($dadosLinha->total_meses);
            $detalhe->PercentualSobreRCLAjustada = $this->formataValor($dadosLinha->percentual);
            $detalhe->brancos3 = str_repeat(" ", 170);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "9";
        $trailler->brancos = str_repeat(" ", 208);
        $trailler->numRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
}
