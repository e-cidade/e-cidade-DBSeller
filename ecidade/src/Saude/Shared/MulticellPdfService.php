<?php

namespace ECidade\Saude\Shared;

use ECidade\Pdf\Pdf;

/**
 * Esta classe visa facilitar a quebra de linhas utilizando o multicell do FPDF. Caso haja quebra as demais células
 * devem ter uma altura adequada para acompanhar a célula que quebrou, não importando se foi a primeira ou última célula
 * da linha que quebrou.
 * Essa classe pode apresentar limitações dependendo do contexto em que for utilizada.
 */
class MulticellPdfService
{
    /**
     * Essa altura é para caso todas as células de uma mesma linha tenham a mesma altura inicial.
     * @var int
     */
    protected $alturaCelulas;

    /**
     * Representa quantas linhas uma célula ocupa de espaço.
     * @var int
     */
    protected $numeroLinhasOcupadas = 1;

    /**
     * Array chave-valor contendo arrays internos também chave valor.
     * Cada Chave do array externo será um nome para representar uma célula.
     * Os arrays internos são compostos pelo valor da largura e texto a ser impresso.
     * @var array
     */
    protected $dadosCelulas;

    /**
     * Valor da célula com maior altura dentre as células envolvidas.
     * @var int
     */
    protected $linhaAlturaMaxima;

    /**
     * @var Pdf
     */
    private $pdf;

    /**
     * @param Pdf $pdf Utilizar a classe PDF nova.
     *
     * @param array $dadosCelulas Array chave-valor contendo arrays internos também chave valor.
     *  Cada Chave do array externo será um nome para representar uma célula.
     *  Os arrays internos são compostos pelas chaves largura e texto e seus respectivos valores.
     *
     * @param int $alturaCelulas Mesma altura utilizada pelas céluas de uma mesma linha.
     */
    public function __construct(Pdf $pdf, $dadosCelulas, $alturaCelulas)
    {
        $this->pdf = $pdf;
        $this->dadosCelulas = $dadosCelulas;
        $this->alturaCelulas = $alturaCelulas;
        $this->calculaAlturaLinhas();
    }

    /**
     * Etapa inicial para verificar a altura de cada célula de acordo com o conteúdo a ser impresso.
     * @return void
     */
    protected function calculaAlturaLinhas()
    {
        $dados = [];

        foreach ($this->dadosCelulas as $key => $celula) {
            $dados[$key] = [
                'alturaTexto' => $this->pdf->nbLines($celula['largura'], $celula['texto']),
                'possuiMaisLinhas' => false,
                'texto' => $celula['texto']
            ];
        }

        $this->dadosCelulas = $dados;
        $this->verificaLinhaComMaiorAltura();
    }

    /**
     * Etapa para verificar qual célula terá a maior altura.
     * @return void
     */
    protected function verificaLinhaComMaiorAltura()
    {
        $dados = [];

        foreach ($this->dadosCelulas as $key => $celula) {
            $dados[$key]['possuiMaisLinhas'] = $celula['possuiMaisLinhas'];
            $dados[$key]['alturaTexto'] = $celula['alturaTexto'];
            $dados[$key]['texto'] = $celula['texto'];

            if ($celula['alturaTexto'] > $this->getNumeroLinhasOcupadas()) {
                $this->setNumeroLinhasOcupadas($celula['alturaTexto']);
                $dados[$key]['possuiMaisLinhas'] = true;
            }
        }

        $this->setLinhaAlturaMaxima();
        $this->dadosCelulas = $dados;
    }

    /**
     * Retorna a altura que a célula terá, informando o respectivo nome usado para
     * identificar essa célula no construtor.
     * @param  string  $nomeCelula
     * @return int
     */
    public function validaAlturaLinha($nomeCelula)
    {
        return ($this->dadosCelulas[$nomeCelula]['possuiMaisLinhas'] ?
            $this->alturaCelulas :
            ($this->dadosCelulas[$nomeCelula]['alturaTexto'] > 1 ?
                $this->getLinhaAlturaMaxima() / $this->dadosCelulas[$nomeCelula]['alturaTexto'] :
                4 * $this->getNumeroLinhasOcupadas()
            )
        );
    }

    /**
     * @return int
     */
    protected function getNumeroLinhasOcupadas()
    {
        return $this->numeroLinhasOcupadas;
    }

    /**
     * @param $numeroLinhasOcupadas
     * @return void
     */
    protected function setNumeroLinhasOcupadas($numeroLinhasOcupadas)
    {
        $this->numeroLinhasOcupadas = $numeroLinhasOcupadas;
    }

    /**
     * @return void
     */
    protected function setLinhaAlturaMaxima()
    {
        $this->linhaAlturaMaxima = $this->getNumeroLinhasOcupadas() * $this->alturaCelulas;
    }

    /**
     * @return int
     */
    protected function getLinhaAlturaMaxima()
    {
        return $this->linhaAlturaMaxima;
    }
}
