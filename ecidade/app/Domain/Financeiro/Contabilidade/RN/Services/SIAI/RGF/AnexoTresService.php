<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RGF;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use ECidade\Financeiro\Contabilidade\Relatorio\RGF\Factory\AnexoIII as RelatorioBase;
use Periodo;
use stdClass;

class AnexoTresService extends AnexosService
{
    const SIGLA = "A17";
    const MOVIMENTO = 1;
    const MEDIDAS_CORRETIVAS = 2;
    
    protected $dePara = [
        self::MOVIMENTO => [
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
            "12.1" => 13,
            "12.2" => 14,
            "13" => 15,
            "15" => 17,
            "14" => 16,
            "16" => 18,
            "17" => 19,
            "18" => 20,
            "19" => 21,
            "20" => 22,
            "21" => 23,
            "22" => 24,
            "23" => 25,
            "24" => 26,
            "25" => 27,
            "26" => 28,
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
        $this->linhasProcessadas = $relatorio->getDados();
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
        $header->brancos = str_repeat(" ", 10);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }
    
    public function makeDetalhes()
    {
        $this->makeDetalheMovimento();
        $this->makeDetalheMedidasCorretivas();
    }
    
    public function makeDetalheMovimento()
    {
        $tipoRegistro = self::MOVIMENTO;
        foreach ($this->linhasProcessadas as $linha => $dadosLinha) {
            if (!$this->utilizaLinhaPorDetalhe($linha, $tipoRegistro, $this->dePara)) {
                continue;
            }
            
            $campo = $this->getCodigoCampo($linha, $this->dePara);
            
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = $tipoRegistro;
            $detalhe->brancos1 = " ";
            $detalhe->campo = str_pad($campo, 2, "0", STR_PAD_LEFT);
            $detalhe->zeros1 = str_repeat("0", 7);
            $detalhe->SaldoExercAnt = $this->formataValor($dadosLinha->saldo_exercicio_anterior);
            $detalhe->SaldoAte1QuadSem = $this->formataValor($dadosLinha->ate_1_quadrimestre);
            $detalhe->saldoAte2QuadSEm = $this->formataValor($dadosLinha->ate_2_quadrimestre);
            $detalhe->saldoAte3QuadSem = $this->formataValor($dadosLinha->ate_3_quadrimestre);
            $detalhe->brancos2 = str_repeat(" ", 83);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
    }
    
    /*
     * Metodo não esta sendo utilizado
     * Criado para ser implementado futuramente
     */
    public function makeDetalheMedidasCorretivas()
    {
        $tipoRegistro = self::MEDIDAS_CORRETIVAS;
        $detalhe = new stdClass();
        $detalhe->tipRegistro = $tipoRegistro;
        $detalhe->descricaoMedidaCorretiva = str_pad(" ", 255, " ", STR_PAD_RIGHT);
        $detalhe->numRegistroLido = $this->getQtdLinhas();
        //$this->addDetalhe($detalhe);
    }
        
    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "9";
        $trailler->brancos = str_repeat(" ", 149);
        $trailler->numRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
}
