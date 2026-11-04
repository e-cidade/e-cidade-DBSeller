<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use App\Domain\Financeiro\Contabilidade\Factories\AnexoTresFactory;
use stdClass;

class AnexoTresService extends AnexosService
{
    const SIGLA = "A03";
    protected $dePara = [
        1 => 1,
        2 => 11,
        3 => 1104,
        5 => 1105,
        4 => 1106,
        6 => 1107,
        7 => 1108,
        8 => 12,
        9 => 13,
        10 => 1301,
        11 => 1302,
        12 => 14,
        13 => 15,
        14 => 16,
        15 => 17,
        16 => 1702,
        17 => 1703,
        18 => 1704,
        19 => 1705,
        20 => 1707,
        21 => 1708,
        22 => 1709,
        23 => 18,
        24 => 2,
        25 => 22,
        26 => 23,
        28 => 24,
        27 => 25,
        29 => 3,
        30 => 4,
        31 => 5,
        32 => 6,
        33 => 7
    ];
    
    
    public function __construct($exercicio, $filtros)
    {
        $relatorio = AnexoTresFactory::getService($exercicio, $filtros);
        
        $this->exercicio = $exercicio;
        $this->periodo = $relatorio->getPeriodo();
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        $this->linhasProcessadas = $relatorio->getLinhasProcessadas();
        $this->mesesProcessados = $relatorio->getMesesProcessar();
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
        $header->brancos = str_repeat(" ", 10);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }
    
    public function makeDetalhes()
    {
        foreach ($this->linhasProcessadas as $linha) {
          /*
           * Verificamos se a linha sera considerada
           */
            if (!array_key_exists($linha->linha, $this->dePara)) {
                continue;
            }
            $codReceita = $this->dePara[$linha->linha];
          
          
          /*
           * Percorremos todos os meses processados
           */
            foreach ($this->mesesProcessados as $mes) {
                $infoMes = $mes->mes.$mes->ano;
              
                $detalhe = new stdClass();
                $detalhe->tipoRegistro = "1";
                $detalhe->brancos1 = " ";
                $detalhe->campo = str_pad($codReceita, 4, " ", STR_PAD_RIGHT);
                $detalhe->mesAno = $infoMes;
                $detalhe->brancos2 = str_repeat(" ", 13);
                $detalhe->valor = $this->formataValor($linha->{$mes->coluna});
                $detalhe->brancos3 = str_repeat(" ", 111);
                $detalhe->numRegistroLido = $this->getQtdLinhas();
                $this->addDetalhe($detalhe);
            }
          
          /*
           * Informação ref a previsão atualizada
           */
            $detalhe = new stdClass();
            $detalhe->tipoRegistro = "1";
            $detalhe->brancos1 = " ";
            $detalhe->campo = str_pad($codReceita, 4, " ", STR_PAD_RIGHT);
            $detalhe->mesAno = str_pad(
                "PA".str_pad($this->periodo->getOrdem(), 2, "0", STR_PAD_LEFT),
                6,
                " ",
                STR_PAD_RIGHT
            );
            $detalhe->brancos2 = str_repeat(" ", 13);
            $detalhe->valor = $this->formataValor($linha->previsao_atualizada);
            $detalhe->brancos3 = str_repeat(" ", 111);
            $detalhe->numRegistroLido = $this->getQtdLinhas();
            $this->addDetalhe($detalhe);
        }
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
