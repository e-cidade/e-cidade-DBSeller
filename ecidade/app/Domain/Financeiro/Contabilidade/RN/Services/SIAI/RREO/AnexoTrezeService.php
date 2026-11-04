<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\Factory\AnexoXIII;
use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use stdClass;

class AnexoTrezeService extends AnexosService
{
    const SIGLA = "A39";
    const IMPACTOS_CONTRATACOES_PPP = 1;
    const DESPESAS_PPP = 2;
    const RECEITA_CORRENTE_LIQUIDA = 3;
    
    protected $dePara = [
        self::IMPACTOS_CONTRATACOES_PPP =>
        [
          1 => 01,
          2 => 02,
          3 => 03,
          4 => 04,
          5 => 05,
          6 => 06,
          7 => 07,
          8 => 08,
          9 => 17,
          10 => 09,
          11 => 18
        ],
        
        /*
         * Informado apenas para gerar a linha.
         * A linha será gerada sem informações
         */
        self::DESPESAS_PPP => [19 => 19],

        self::RECEITA_CORRENTE_LIQUIDA => [19 => 19]
    ];
                
    public function __construct($exercicio, $filtros)
    {
        $relatorio = AnexoXIII::getInstance($exercicio, $filtros["periodo"]);
        $relatorio->setInstituicoes(
            $this->buscaInstituicoes("lista", $filtros["desvincularDadosInstituicaoCamara"])
        );
        $this->linhasProcessadas =$relatorio->getLinhas();
        $this->exercicio = $exercicio;
        $this->periodo = $relatorio->getPeriodo();
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        $this->nomeArquivo = self::SIGLA."_{$exercicio}".str_pad($this->periodo->getOrdem(), 2, "0", STR_PAD_LEFT);
        $this->setFilePath("tmp/{$this->nomeArquivo}.TXT");
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
        $header->brancos = str_repeat(" ", 275);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }
    
    public function makeDetalhes()
    {
        $this->makeDetalhe1();
        $this->makeDetalhe2();
        $this->makeDetalhe3();
    }
    
    private function makeDetalhe1()
    {
        $tipoRegistro = self::IMPACTOS_CONTRATACOES_PPP;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->codigo, $tipoRegistro, $this->dePara)) {
                continue;
            }
            $detalhe = new stdClass();
            $detalhe->tipRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->campo = $this->getCodigoCampo($linha->codigo);
            $detalhe->brancos2 = str_repeat(" ", 7);
            $detalhe->saldoTotalExercicioAnterior = $this->formataValor($linha->saldo_anterior);
            $detalhe->saldoFinalAteOBimestre = $this->formataValor($linha->saldo_final);
            $detalhe->brancos3 = str_repeat(" ", 376);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    private function makeDetalhe2()
    {
        $tipoRegistro = self::DESPESAS_PPP;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->codigo, $tipoRegistro, $this->dePara)) {
                continue;
            }
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->tipoContratante = "10";
            $detalhe->situacao = "2";
            $detalhe->despesa = str_pad(" ", 255, " ", STR_PAD_RIGHT);
            $detalhe->valorAnoAnterior = $this->formataValor();
            $detalhe->valorAnoReferencia = $this->formataValor();
            $detalhe->valorAnoSubsequente1 = $this->formataValor();
            $detalhe->valorAnoSubsequente2 = $this->formataValor();
            $detalhe->valorAnoSubsequente3 = $this->formataValor();
            $detalhe->valorAnoSubsequente4 = $this->formataValor();
            $detalhe->valorAnoSubsequente5 = $this->formataValor();
            $detalhe->valorAnoSubsequente6 = $this->formataValor();
            $detalhe->valorAnoSubsequente7 = $this->formataValor();
            $detalhe->valorAnoSubsequente8 = $this->formataValor();
            $detalhe->valorAnoSubsequente9 = $this->formataValor();
            $detalhe->brancos2 = " ";
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }

    private function makeDetalhe3()
    {
        $tipoRegistro = self::RECEITA_CORRENTE_LIQUIDA;
        foreach ($this->linhasProcessadas as $linha) {
            if (!$this->utilizaLinhaPorDetalhe($linha->codigo, $tipoRegistro, $this->dePara)) {
                continue;
            }
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->tipo = "14";
            $detalhe->brancos2 = " ";
            $detalhe->valorAnoAnterior = $this->formataValor($linha->exercicio_anterior);
            $detalhe->valorAnoReferencia = $this->formataValor($linha->exercicio_corrente);
            $detalhe->valorAnoSubsequente1 = $this->formataValor($linha->exercicio_corrente_1);
            $detalhe->valorAnoSubsequente2 = $this->formataValor($linha->exercicio_corrente_2);
            $detalhe->valorAnoSubsequente3 = $this->formataValor($linha->exercicio_corrente_3);
            $detalhe->valorAnoSubsequente4 = $this->formataValor($linha->exercicio_corrente_4);
            $detalhe->valorAnoSubsequente5 = $this->formataValor($linha->exercicio_corrente_5);
            $detalhe->valorAnoSubsequente6 = $this->formataValor($linha->exercicio_corrente_6);
            $detalhe->valorAnoSubsequente7 = $this->formataValor($linha->exercicio_corrente_7);
            $detalhe->valorAnoSubsequente8 = $this->formataValor($linha->exercicio_corrente_8);
            $detalhe->valorAnoSubsequente9 = $this->formataValor($linha->exercicio_corrente_9);
            $detalhe->brancos3 = str_repeat(" ", 256);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "10";
        $trailler->brancos = str_repeat(" ", 414);
        $trailler->numRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
}
