<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RGF;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Factory\AnexoIV as RelatorioBase;
use Periodo;
use stdClass;

class AnexoQuatroService extends AnexosService
{
    const SIGLA = "A18";
    const OPERACAO_CREDITO = 1;
    const APURACAO_CUMPRIMENTO_LIMITES = 2;
    const OUTRAS_OPERACOES_DIVIDA_CONSOLIDADA = 3;
    
    protected $dePara = [
        self::OPERACAO_CREDITO => [
            "1" => 1,
            "2" => 2,
            "3" => 3,
            "4" => 4,
            "5" => 5,
            "6" => 6,
            "7" => 7,
            "8" => 8,
            "9" => 9,
            "10" => 10,
            "11" => 11,
            "12" => 12,
            "13" => 13,
            "14" => 14,
            "15" => 15,
            "16" => 16,
            "17" => 17
        ],
        self::APURACAO_CUMPRIMENTO_LIMITES => [
            "18" => 18,
            "18.1" => 19,
            "18.2" => 20,
            "19" => 21,
            "20" => 22,
            "21" => 23,
            "22" => 24,
            "23" => 25,
            "24" => 26
        ],
        self::OUTRAS_OPERACOES_DIVIDA_CONSOLIDADA => [
            "25" => 27,
            "26" => 28,
            "27" => 29,
            "28" => 30,
            "29" => 31,
            "30" => 32,
        ]
    ];
    
    public function __construct($filtros)
    {
        $bimestre = new Periodo($filtros['periodo']);

        $filtros['periodo'] = $this->converteBimestreParaQuadrimestre($filtros['periodo']);
        $filtros["instituicoes"] = $this->buscaInstituicoes([], $filtros["desvincularDadosInstituicaoCamara"]);
        
        $this->exercicio = $filtros['DB_anousu'];
        $this->periodo = new Periodo($filtros['periodo']);
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        $this->nomeArquivo = self::SIGLA."_{$this->exercicio}".str_pad($bimestre->getOrdem(), 2, "0", STR_PAD_LEFT);
        $this->setfilePath("tmp/{$this->nomeArquivo}.TXT");
        
        $relatorio = RelatorioBase::getInstance($this->exercicio, $this->periodo);
        $this->linhasProcessadas = $relatorio->getLinhasProcessadas();
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
        $header->brancos = str_repeat(" ", 12);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }
    
    public function makeDetalhes()
    {
        $this->makeDetalheOperacaoCredito();
        $this->makeDetalheApuracaoCumprimentoLimites();
        $this->makeDetalheOutrasOperacoesDividaConsolidada();
    }
    
    public function makeDetalheOperacaoCredito()
    {
        $tipoRegistro = self::OPERACAO_CREDITO;
        foreach ($this->linhasProcessadas as $linha => $dadosLinha) {
            if (!$this->utilizaLinhaPorDetalhe($linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->campo = $this->getCodigoCampo($linha, $this->dePara);
            $detalhe->brancos2 = str_repeat(" ", 6);
            $detalhe->noQuadrimestre = $this->formataValor($dadosLinha->noperiodo);
            $detalhe->ateQuadrimestre = $this->formataValor($dadosLinha->ateperiodo);
            $detalhe->brancos3 = str_repeat(" ", 114);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    public function makeDetalheApuracaoCumprimentoLimites()
    {
        $tipoRegistro = self::APURACAO_CUMPRIMENTO_LIMITES;
        foreach ($this->linhasProcessadas as $linha => $dadosLinha) {
            if (!$this->utilizaLinhaPorDetalhe($linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->campo = $this->getCodigoCampo($linha, $this->dePara);
            $detalhe->brancos2 = str_repeat(" ", 6);
            $detalhe->valor = $this->formataValor($dadosLinha->valor);
            $detalhe->brancos3 = str_repeat(" ", 128);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    public function makeDetalheOutrasOperacoesDividaConsolidada()
    {
        $tipoRegistro = self::OUTRAS_OPERACOES_DIVIDA_CONSOLIDADA;
        foreach ($this->linhasProcessadas as $linha => $dadosLinha) {
            if (!$this->utilizaLinhaPorDetalhe($linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->campo = $this->getCodigoCampo($linha, $this->dePara);
            $detalhe->brancos2 = str_repeat(" ", 6);
            $detalhe->noQuadrimestre = $this->formataValor($dadosLinha->noperiodo);
            $detalhe->ateQuadrimestre = $this->formataValor($dadosLinha->ateperiodo);
            $detalhe->brancos3 = str_repeat(" ", 114);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

        
    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "9";
        $trailler->brancos = str_repeat(" ", 151);
        $trailler->numRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
}
