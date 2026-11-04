<?php
namespace app\Domain\Financeiro\Contabilidade\RN\Services\SIAI;

use App\Domain\Configuracao\Instituicao\Model\DBConfig;
use StdClass;

abstract class AnexosService
{
    protected $exercicio;
    
    protected $periodo;
    
    protected $codigoOrgao;
    
    protected $nomeOrgao;
    
    protected $nomeArquivo;
    
    protected $filePath;
    
    protected $header;
    
    protected $detalhes = [];
    
    protected $trailler;
    
    protected $qtdLinhas = 0;
    
    protected $linhasProcessadas;
    
    protected $mesesProcessados;
    
    protected $arquivoXlsCarregado;
    
    protected $dePara = [];
    
    public function __construct()
    {
    }
    
    public $bimestres = [
        6  => "1º Bimestre",
        7  => "2º Bimestre",
        8  => "3º Bimestre",
        9  => "4º Bimestre",
        10 => "5º Bimestre",
        11 => "6º Bimestre"
    ];
    
    public $bimestreParaQuadrimestre = [
        6 => 14,
        7 => 14,
        8 => 15,
        9 => 15,
        10 => 16,
        11 => 16
    ];
    
    public function getNomeArquivo()
    {
        return $this->nomeArquivo;
    }
    
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
    }
    
    public function setArquivoXlsCarregado($reader)
    {
        $this->arquivoXlsCarregado = $reader;
    }
    
    public function setHeader(stdClass $header)
    {
        $this->header = $header;
    }
    
    public function setDetalhes(stdClass $detalhes)
    {
        $this->detalhes = $detalhes;
    }
    
    public function setTrailler(stdClass $trailler)
    {
        $this->trailler = $trailler;
    }
    
    public function addDetalhe(stdClass $detalhe)
    {
        $this->detalhes[] = $detalhe;
    }
    
    public function getDetalhes()
    {
        return $this->detalhes;
    }
    
    public function getQtdLinhas($pad = 10)
    {
        $this->qtdLinhas++;
        return str_pad($this->qtdLinhas, $pad, " ", STR_PAD_LEFT);
    }
    
    /*
     * $tipoRetorno = array ou lista
     * array $tipoRetorno = [1,2,3,4];
     * lista $tipoRetorno = 1,2,3,4;
     */
    public function buscaInstituicoes($tipoRetorno = null, $desvincularDadosInstituicaoCamara = false)
    {

        $listaInstituicoes = [];
        $instituicoes = DBConfig::all();
        foreach ($instituicoes as $instituicao) {
            //Não retorna a instituição Camara para compor os dados dos arquivos
            if ($desvincularDadosInstituicaoCamara && $instituicao->getTipoInstituicao() == 2) {
                continue;
            }
            $listaInstituicoes[] = $instituicao->codigo;
        }
        sort($listaInstituicoes);
        
        if (is_array($tipoRetorno) || $tipoRetorno == "array") {
            return $listaInstituicoes;
        } else {
            return implode(",", $listaInstituicoes);
        }
    }
    
    public function getValorCelulaArquivoXls($cell)
    {
        if (empty($cell)) {
            $valor = 0;
        } else {
            $valor = $this->arquivoXlsCarregado->getActiveSheet()->getCell($cell)->getCalculatedValue();
            if (!is_numeric($valor)) {
                $valor = 0;
            }
        }
        return $this->formataValor($valor);
    }
    
    public function utilizaLinhaPorDetalhe($linha, $detalhe = null, $dePara = null)
    {
        if (empty($dePara)) {
            $dePara = $this->dePara;
        }
        
        $array = $dePara;
        if (!empty($detalhe)) {
            $array = $dePara["{$detalhe}"];
        }
         
        if (array_key_exists("{$linha}", $array)) {
            return true;
        }
        return false;
    }
    
    public function getCodigoCampo($codigo, $dePara = null, $pad = 2)
    {
        if (empty($dePara)) {
            $dePara = $this->dePara;
        }
        foreach ($dePara as $indice => $para) {
            if (isset($para[$codigo])) {
                return str_pad($para[$codigo], $pad, "0", STR_PAD_LEFT);
            }
        }
        return str_pad("0", $pad, "0", STR_PAD_LEFT);
    }
    
    public function formataValor($valor = 0, $pad = 14)
    {
        $valor = (!is_numeric($valor)?0:$valor);
        $negativo = "";
        if ($valor < 0) {
            $negativo = "-";
            $pad--;
        }
        $valor = str_pad(preg_replace('/[^0-9]/', '', number_format($valor, 2, ".", "")), $pad, "0", STR_PAD_LEFT);
        return $negativo.$valor;
    }
    
    public function converteBimestreParaQuadrimestre($bimestre)
    {
        if (key_exists($bimestre, $this->bimestreParaQuadrimestre)) {
            return $this->bimestreParaQuadrimestre[$bimestre];
        }
        return null;
    }
    
    public function gerarArquivo()
    {
        $this->processar();
        
        /*
         * Verificamos se o arquivo existe
         * Se existir, sera excluido e recriado
         */
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
        
        $handle = fopen($this->filePath, 'x+');
        
        /*
         * Escreve os dados do header
         */
        $this->escreveHeader($handle);
        
        /*
         * Escreve os dados dos detalhes
         */
        $this->escreveDetalhe($handle);
        
        /*
         * Escreve os dados do trailler
         */
        $this->escreveTrailler($handle);
        
        return $this->filePath;
    }

    protected function processar()
    {
        $this->makeHeader();
        $this->makeDetalhes();
        $this->makeTrailler();
    }
    
    abstract protected function makeHeader();

    abstract protected function makeDetalhes();

    abstract protected function makeTrailler();

    private function escreveHeader($handle)
    {
        foreach ($this->header as $info) {
            fwrite($handle, $info);
        }
        fwrite($handle, "\n");
    }
    
    private function escreveDetalhe($handle)
    {
        /*
         * O detalhe eh um objeto
         * Convertemos para array e implodimos para uma string para que seja escrito
         */
        foreach ($this->detalhes as $detalhe) {
            fwrite($handle, implode((array)$detalhe, null)."\n");
        }
    }
    
    private function escreveTrailler($handle)
    {
        foreach ($this->trailler as $info) {
            fwrite($handle, $info);
        }
        fwrite($handle, "\n");
    }
}
