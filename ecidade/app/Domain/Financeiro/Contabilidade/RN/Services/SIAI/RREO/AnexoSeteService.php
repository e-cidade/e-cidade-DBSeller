<?php
namespace App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\RREO;

use App\Domain\Financeiro\Contabilidade\RN\Services\SIAI\AnexosService;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2017\AnexoVII;
use ECidade\Financeiro\Contabilidade\Relatorio\RREO\V2017\LinhaAnexoVII;
use stdClass;

class AnexoSeteService extends AnexosService
{
    const SIGLA = "A07";
    const EXECUTIVO = 1;
    const LEGISLATIVO = 2;
    const JUDICIARIO = 3;
    const MINISTERIO_PUBLICO = 4;
    const RP_INTRA_ORCAMENTARIA = 5;
    const DEFENSORIA_PUBLICA = 6;
    
    protected $deParaCodigoPoder = [
        LinhaAnexoVII::PODER_EXECUTIVO => self::EXECUTIVO,
        LinhaAnexoVII::PODER_LEGISLATIVO => self::LEGISLATIVO,
        LinhaAnexoVII::PODER_JUDICIARIO => self::JUDICIARIO,
        LinhaAnexoVII::MINISTERIO_PUBLICO => self::MINISTERIO_PUBLICO
    ];
    
    public function __construct($exercicio, $filtros)
    {
        $this->exercicio = $exercicio;
        $this->periodo = new \Periodo($filtros["periodo"]);
        $this->codigoOrgao = $filtros['codigoOrgaoTCE'];
        $this->nomeOrgao = $filtros['nomeOrgaoTCE'];
        $this->nomeArquivo = self::SIGLA."_{$exercicio}".str_pad($this->periodo->getOrdem(), 2, "0", STR_PAD_LEFT);
        $this->setfilePath("tmp/{$this->nomeArquivo}.TXT");
        
        $relatorio = new AnexoVII($exercicio, $this->periodo);
        $relatorio->setAno($exercicio);
        $relatorio->setPeriodo($this->periodo);
        
        $instituicoes = \InstituicaoRepository::getInstituicoes();
        foreach ($instituicoes as $instituicao) {
            //Não considera os dados da instituição camara
            if ($filtros["desvincularDadosInstituicaoCamara"] && $instituicao->getTipo() == 2) {
                continue;
            }
            $relatorio->adicionarInstituicao($instituicao);
        }
        
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
        $header->brancos = str_repeat(" ", 268);
        $header->numRegistroLido = $this->getQtdLinhas();
        $this->setHeader($header);
    }
    
    public function makeDetalhes()
    {
        $this->detalhes = [];
        
        $linhasConsideradas = [
        AnexoVII::LINHA_EXCETO_INTRA,
        AnexoVII::LINHA_INTRA
        ];
        
        $sequencia = 0;
        foreach ($this->linhasProcessadas as $indice => $linha) {
            if (!in_array($indice, $linhasConsideradas)) {
                continue;
            }
                
            foreach ($linha->getLinhas() as $registro) {
                $sequencia++;
                
                /*
                 * RESTOS A PAGAR (EXCETO INTRA-ORÇAMENTÁRIOS) (I)
                 */
                $codigoPoder = $this->deParaCodigoPoder[$registro->getTipo()];
                /*
                 * RESTOS A PAGAR (INTRA-ORÇAMENTÁRIOS) (II)
                 */
                if ($indice == AnexoVII::LINHA_INTRA) {
                    $codigoPoder = self::RP_INTRA_ORCAMENTARIA;
                }
                
                $detalhe = new stdClass();
                $detalhe->tipRegistro = 1;
                $detalhe->brancos1 = " ";
                $detalhe->codigo_poder = $codigoPoder;
                $detalhe->brancos2 = " ";
                $detalhe->seq = str_pad($sequencia, 14, "0", STR_PAD_LEFT);
                $detalhe->orgao = str_pad($registro->getDescricao(), 50, " ", STR_PAD_RIGHT);
                /*
                 * Inicio RP Processados
                 */
                $detalhe->RPPInscritosExercAnt = $this->formataValor(
                    $registro->getValorProcessadoEmExerciciosAnteriores()
                );
                $detalhe->RPPInscritosExercRef = $this->formataValor(
                    $registro->getValorProcessadoNoExercicioAnterior()
                );
                $detalhe->RPPCancelados = $this->formataValor($registro->getValorCanceladoProcessado());
                $detalhe->RPPPagos = $this->formataValor($registro->getValorPagoProcessado());
                $detalhe->brancos3 = str_repeat(" ", 14);
                /*
                 * Fim RP Processados
                 */
                
                /*
                 * Inicio RP Nao Processados
                 */
                $detalhe->RPNPInscritosExercAnt = $this->formataValor(
                    $registro->getValorNaoProcessadoEmExerciciosAnteriores()
                );
                $detalhe->RPNPInscritos = $this->formataValor($registro->getValorNaoProcessadoNoExercicioAnterior());
                $detalhe->RPNPLiquidados = $this->formataValor($registro->getValorLiquidadoNaoProcessado());
                $detalhe->RPNPCancelados = $this->formataValor($registro->getValorCanceladoNaoProcessado());
                $detalhe->RPNPPagos = $this->formataValor($registro->getValorPagoNaoProcessado());
                /*
                 * Fim RP Nao Processados
                 */
                
                $detalhe->brancos4 = str_repeat(" ", 200);
                $detalhe->numRegistroLido = $this->getQtdLinhas();
                $this->addDetalhe($detalhe);
            }
        }
    }
    
    
    public function makeTrailler()
    {
        $trailler = new stdClass();
        $trailler->tipoRegistro = "9";
        $trailler->brancos = str_repeat(" ", 407);
        $trailler->numRegistroLido = $this->getQtdLinhas();
        $this->setTrailler($trailler);
    }
}
