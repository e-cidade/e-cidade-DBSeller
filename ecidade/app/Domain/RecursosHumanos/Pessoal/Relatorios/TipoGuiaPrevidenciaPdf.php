<?php

/**
 * Php version 7.2
 * TipoGuiaPrevidenciaPdf
 *
 * @category PDF
 * @package  Imprimir_Dados_Da_Previdência
 * @author   Hugo Silva <hugo.silva@dbseller.com.br>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD
 * @link     http://pear.php.net/pepr/pepr-proposal-show.php?id=198
 * Classe de imprimir PDF
 */

namespace App\Domain\RecursosHumanos\Pessoal\Relatorios;

use ECidade\Pdf\Pdf;

/**
 * Php version 7.2
 * TipoGuiaPrevidenciaPdf
 *
 * @category PDF
 * @package  Imprimir_Dados_Da_Previdência
 * @author   Hugo Silva <hugo.silva@dbseller.com.br>
 * @license  http://www.opensource.org/licenses/bsd-license.php BSD
 * @link     http://pear.php.net/pepr/pepr-proposal-show.php?id=198
 * Classe de imprimir PDF
 */

class TipoGuiaPrevidenciaPdf extends Pdf
{
    private $dados;
    private $cnpj;
    private $competencia;
    private $desconto;
    private $valor;
    private $nomeInst;
    private $total;
    private $contribuicao;
    private $dataVencimento;
    private $lotacao;
    private $prevDados;
    private $descricao;
    private $parcelaPatronal;
    private $percentual;
    private $porcentagem;

    /**
     * Contrutor da classe
     *
     * @param array $dados para a montagem do formulario
     */
    public function __construct(array $dados)
    {
        parent::__construct();
        $this->dados = $dados;
        if (!empty($this->dados[0]['lotacoes'])) {
            $this->lotacao = $dados[0]['lotacoes'];
            $this->prevDados = $dados[0]['dados'];
        }
        $this->descricao  = $this->dados[0]['patronal_pocent'];
        $this->porcentagem = $this->dados[0]['patronal_pocent'][0]['porcent'];
        $cnpj = $this->dados[0]['instituicao']->cgc;
        $mask = '$1.$2.$3/$4-$5';
        $expressao = '/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/';

        if ($this->dados[0]['dados'] === 0) {
            $desconto = [];
        } else {
            $desconto = $this->dados[0]['dados'][0]->desco;
        }
      
        if (!empty($this->dados[0]['dados'][0]->parcela_patronal)) {
            $patronalF = floatval($this->dados[0]['dados'][0]->parcela_patronal);
            $this->parcelaPatronal = number_format($patronalF, 2, ",", ".");
        } else {
            $this->parcelaPatronal = '';
        }

        $this->cnpj = preg_replace($expressao, $mask, $cnpj);
        $this->competencia = $this->dados['mes'] . '/' . $this->dados['ano'];
        if (!empty($this->desconto)) {
            $this->desconto = number_format(floatval($desconto), 2, ",", ".");
        } else {
            $this->desconto = '';
        }
        $this->nomeInst = $this->dados[0]['instituicao']->nomeinst;
        
        if (!empty($this->parcelaPatronal)) {
            $this->total = number_format(floatval($desconto) + floatval($this->parcelaPatronal), 2, ",", ".");
        } else {
            $this->total = '';
        }

        if (!empty($this->dados[0]['dados'][0]->base)) {
            $base = $this->dados[0]['dados'][0]->base;
            $this->contribuicao = number_format(floatval($base), 2, ",", ".");
        } else {
            $base = '';
        }
        $vencimento = date('d/m/Y', strtotime($this->dados['dataVencimento']));
        $this->dataVencimento = $vencimento;
    }

    /**
     * Metodo responsavel por retornar o tipo de impressão
     *
     * @return Impressao
     */
    public function emitir()
    {
        if ($this->lotacao) {
            $this->imprimeLotacao();
        } else {
            $this->imprimeSelecao();
        }

        return $this->imprimir();
    }

    /**
     * Informações conteudo do PDF
     *
     * @return void
     */
    private function dadosPdf()
    {
        $this->headerPage();
        $this->Ln(5);
        $this->dadosInstituicao();
    }

    /**
     * Informações comuns a qualquer tipo de previdência
     *
     * @return void
     */
    private function headerPage()
    {
        $this->Rect(3, 3, 204, 291);
        $this->SetFont('Arial', "B", 15);
        $this->Cell(0, 0, $this->tiposPrevidencia(), 0, 0, "C");
        $this->Ln(5);

        $this->SetFont("Arial", "B", 12);
        $this->Cell(90, 10, $this->nomeInst, 0, 0, "L", false, "");

        $this->Rect(50, 29, 50, 50);
        $this->SetFont('Arial', 'I', 7);
        $this->Text(52, 32, '1 - Carimbo Padronizado do CGC');

        $this->SetFont("Arial", "", 12);
        $this->Cell(5, 8, '', 0, 0, 'L');
        $this->Cell(52, 8, '8 - CGC', 1, 0, 'L');
        $this->Cell(43, 8, $this->cnpj, 1, 1, 'L');

        $this->Cell(95, 8, '', 0, 0);
        $this->Cell(52, 8, utf8_decode('9 - MÊS/ANO'), 1, 0, 'L');
        $this->Cell(43, 8, $this->competencia, 1, 1, 'L');

        $this->Cell(95, 8, '', 0, 0);
        $this->Cell(52, 8, '10 - CD. PAGTO', 1, 0, 'L');
        $this->Cell(43, 8, $this->dados['codigoPagamento'], 1, 1, 'L');

        $this->Cell(95, 8, '', 0, 0);
        $this->Cell(52, 8, '11 - DATA DE PAGTO', 1, 0, 'L');
        $this->Cell(43, 8, $this->dataVencimento, 1, 1, 'L');
    }
    /**
     *  Informações da prefeitura caixa a esquerda no meio da pagina
     *  (Informações comuns a qualquer tipo de previdência)
     *
     * @return PDF
     */
    private function dadosInstituicao()
    {
        $this->Rect(10, 84, 89, 8);
        $this->SetFont('Arial', 'I', 7);
        $this->Text(12, 86, utf8_decode('2 - Nome / Razão Social'));
        $this->SetFont("Arial", "", 12);
        $this->Text(12, 91, $this->nomeInst);

        $this->Rect(10, 92, 89, 8);
        $this->SetFont('Arial', 'I', 7);
        $this->Text(12, 94, utf8_decode('3 - Endereço'));
        $this->SetFont("Arial", "", 12);
        $endereco = $this->dados[0]['instituicao']->ender;
        $numero = $this->dados[0]['instituicao']->numero;
        $this->Text(12, 99, $endereco . ', ' . $numero);

        $this->Rect(10, 100, 30, 8);
        $this->SetFont('Arial', 'I', 7);
        $this->Text(12, 102, '4 - CEP');
        $this->SetFont("Arial", "", 12);
        $this->Text(12, 107, $this->dados[0]['instituicao']->cep);

        $this->Rect(40, 100, 45, 8);
        $this->SetFont('Arial', 'I', 7);
        $this->Text(42, 102, utf8_decode('5 - Município'));
        $this->SetFont("Arial", "", 12);
        $this->Text(42, 107, $this->dados[0]['instituicao']->munic);

        $this->Rect(85, 100, 14, 8);
        $this->SetFont('Arial', 'I', 7);
        $this->Text(87, 102, '6 - UF');
        $this->SetFont("Arial", "", 12);
        $this->Text(87, 107, $this->dados[0]['instituicao']->uf);

        $this->Rect(105, 110, 95, 60);
    }

    /**
     * Retorna a descrição do tipo de Guia
     *
     * @return array
     */
    private function tipoGuia()
    {
        if ($this->dados['tipoGuia'] == 'patronal') {
            $this->Rect(105, 50, 52, 8);
            $this->Text(106, 57, '12 - EMPRESA');
            $this->Text(158, 57, 'R$ ' . $this->parcelaPatronal);
            $this->Rect(157, 50, 43, 8);
        } else {
            $this->Rect(105, 50, 52, 8);
            $this->Text(106, 57, '12 - SEGURADOS');
            $this->Text(158, 57, 'R$ ' . $this->desconto);
            $this->Rect(157, 50, 43, 8);
        }
    }

    /**
     * Retorna a descrição da previdência
     *
     * @return array
     */
    private function tiposPrevidencia()
    {
        $nomes = array_map(function ($nome) {
            return $nome['nome'];
        }, $this->descricao);

        return $nomes[0];
    }
    /**
     * Retorna os dados de lotacao
     *
     * @return $items
     */
    private function getLotacao()
    {
        $items = [];
        foreach ($this->lotacao as $dados) {
            foreach ($dados['nome_lotacoes'] as $lotacao) {
                $items[] = [
                    'total' => $dados['total_lotacoes'],
                    'id' => $lotacao->r70_codigo,
                    'nome' => $lotacao->r70_descr,
                    'items' =>  $this->getPrevDados($lotacao->r70_codigo)
                ];
            }
        }
        return $items;
    }
    /**
     * Dados da previdência
     *
     * @param  array $codigo codigo da lotacao
     * @return array $item
     */
    private function getPrevDados($codigo)
    {
        $items = [];
        if ($this->prevDados) {
            foreach ($this->prevDados as $dados) {
                if ($codigo === $dados->lotacao) {
                    if (!$dados) {
                        continue;
                    }
                    $items[] = [
                        'base' => $dados->base,
                        'soma' => $dados->soma,
                        'ded' => $dados->ded,
                        'dev' => $dados->dev,
                        'desco' => $dados->desco,
                        'parcela_patronal' => $dados->parcela_patronal,
                        'lotacao' => $dados->lotacao
                    ];
                }
            }
        }
        return $items;
    }

    private function getTotalEmpregador($item)
    {
        return ($item['base'] * $this->porcentagem)/100;
    }

    /**
     * Método que imprime a Lotação
     *
     * @return Lotacao
     */
    private function imprimeLotacao()
    {
        foreach ($this->getLotacao() as $lotacoes) {
            $this->addPage();
            $this->dadosPdf();

            $this->Rect(10, 110, 90, 60);

            $this->SetFont('Arial', 'I', 7);
            $this->Text(12, 112, utf8_decode('7 - Outras Informações'));

            $this->SetFont('Arial', 'I', 7);
            $this->Text(14, 117, utf8_decode('8 - Lotação'));
            $this->SetFont("Arial", "", 12);
            $this->Text(16, 122, $lotacoes['nome']);
            $this->Rect(13, 115, 83, 8);

            $this->Text(16, 130, utf8_decode('Nº de Funcionários'));
            $this->Rect(13, 125, 40, 8);
            $this->Text(73, 130, $lotacoes['total']);
            $this->Rect(53, 125, 44, 8);

            $this->Text(35, 143, utf8_decode('Salário Contribuição'));
            $this->Rect(13, 137, 84, 10);
            $this->Text(23, 152, utf8_decode('Funcionários'));
            $this->Rect(13, 147, 42, 8);

            if (count($lotacoes['items']) === 0) {
                $this->imprimeVazio();
            } else {
                $totalDesc = '';
                foreach ($lotacoes['items'] as $item) {
                    $this->Text(59, 153, 'R$ ' . number_format($item['base'], 2, ",", "."));

                    $this->Rect(105, 50, 52, 8);
                    if ($this->dados['tipoGuia'] == 'patronal') {
                        $this->Text(106, 57, '12 - EMPRESA');
                        $totalDesc = number_format($this->getTotalEmpregador($item), 2, ",", ".");
                        $this->Text(158, 57, 'R$ ' . $totalDesc);
                    } else {
                        $this->Text(106, 57, '12 - SEGURADOS');
                        $totalDesc = number_format($item['desco'], 2, ",", ".");
                        $this->Text(158, 57, 'R$ ' .  $totalDesc);
                    }
                    $this->Rect(157, 50, 43, 8);

                    $this->Text(106, 65, '13 - TERCEIROS');
                    $this->Rect(105, 58, 52, 8);
                    $this->Text(158, 65, 'R$ 0,00');
                    $this->Rect(157, 58, 43, 8);

                    $this->Text(106, 73, utf8_decode('14 - DEDUÇÕES'));
                    $this->Rect(105, 66, 52, 8);
                    $this->Text(158, 73, 'R$ 0,00');
                    $this->Rect(157, 66, 43, 8);

                    $this->Text(106, 81, utf8_decode('15 - TOTAL LÍQUIDO'));
                    $this->Rect(105, 74, 52, 8);
                    $somaPD = number_format($item['parcela_patronal'] + $item['desco'], 2, ",", ".");
                    $this->Text(158, 81, 'R$ ' .  0, 00);
                    $this->Rect(157, 74, 43, 8);

                    $this->Text(106, 89, '16 - ATUAL. MONETRIA');
                    $this->Rect(105, 82, 52, 8);
                    $this->Text(158, 89, 'R$ 0,00');
                    $this->Rect(157, 82, 43, 8);

                    $this->Text(106, 97, '17 - JUROS/MULTA');
                    $this->Rect(105, 90, 52, 8);
                    $this->Text(158, 97, 'R$ 0,00');
                    $this->Rect(157, 90, 43, 8);

                    $this->Text(106, 105, '18 - TOTAL');
                    $this->Rect(105, 98, 52, 8);
                    $this->Text(158, 105, 'R$ ' . $totalDesc);
                    $this->Rect(157, 98, 43, 8);
                }
            }

            $this->Rect(55, 147, 42, 8);

            $this->Text(23, 161, utf8_decode('Autônomos'));
            $this->Rect(13, 155, 42, 8);
            $this->Text(59, 155, '');
            $this->Rect(55, 155, 42, 8);
        }
    }
    /**
     * Imprime
     *
     * @return Seleçao
     */
    private function imprimeSelecao()
    {
        $this->addPage();

        $this->dadosPdf();

        $this->tipoGuia();
        $this->dadosSelecao();

        $this->Rect(10, 110, 90, 60);

        $this->SetFont('Arial', 'I', 7);
        $this->Text(12, 112, utf8_decode('7 - Outras Informações'));
        $this->SetFont("Arial", "", 12);

        $this->Text(16, 120, utf8_decode('Lotação'));
        $this->Rect(13, 115, 40, 8);
        $this->Text(73, 120, '');
        $this->Rect(53, 115, 44, 8);

        $this->Text(16, 130, utf8_decode('Nº de Funcionráios'));
        $this->Rect(13, 125, 40, 8);
        $this->Text(73, 130, floatval($this->dados[0]['dados'][0]->soma));
        $this->Rect(53, 125, 44, 8);

        $this->Text(35, 143, utf8_decode('Salário Contribuio'));
        $this->Rect(13, 137, 84, 10);
        $this->Text(23, 152, utf8_decode('Funcionários'));
        $this->Rect(13, 147, 42, 8);
        $this->Text(59, 153, 'R$ ' . number_format(floatval($this->dados[0]['dados'][0]->base), 2, ",", "."));
        $this->Rect(55, 147, 42, 8);

        $this->Text(23, 161, utf8_decode('Autônomos'));
        $this->Rect(13, 155, 42, 8);
        $this->Text(59, 155, '');
        $this->Rect(55, 155, 42, 8);

        $this->Rect(105, 110, 95, 60);
    }

    private function dadosSelecao()
    {
        $this->Text(106, 65, '13 - TERCEIROS');
        $this->Rect(105, 58, 52, 8);
        $this->Text(158, 65, 'R$ 0,00');
        $this->Rect(157, 58, 43, 8);

        $this->Text(106, 73, utf8_decode('14 - DEDUÇÕES'));
        $this->Rect(105, 66, 52, 8);
        $this->Text(158, 73, 'R$ 0,00');
        $this->Rect(157, 66, 43, 8);

        $this->Text(106, 81, utf8_decode('15 - TOTAL LÍQUIDO'));
        $this->Rect(105, 74, 52, 8);
        $this->Text(158, 81, 'R$ ' .  floatval($this->total));
        $this->Rect(157, 74, 43, 8);

        $this->Text(106, 89, '16 - ATUAL. MONETRIA');
        $this->Rect(105, 82, 52, 8);
        $this->Text(158, 89, 'R$ 0,00');
        $this->Rect(157, 82, 43, 8);

        $this->Text(106, 97, '17 - JUROS/MULTA');
        $this->Rect(105, 90, 52, 8);
        $this->Text(158, 97, 'R$ 0,00');
        $this->Rect(157, 90, 43, 8);

        $this->Text(106, 105, '18 - TOTAL');
        $this->Rect(105, 98, 52, 8);
        $this->Text(158, 105, 'R$ ' . floatval($this->total));
        $this->Rect(157, 98, 43, 8);
    }

    private function imprimeVazio()
    {
        $this->Text(59, 153, 'R$ 0,00');

        $this->Rect(105, 50, 52, 8);
        if ($this->dados['tipoGuia'] == 'patronal') {
            $this->Text(106, 57, '12 - EMPRESA');
        } else {
            $this->Text(106, 57, '12 - SEGURADOS');
        }
        $this->Text(158, 57, 'R$ 0,00');
        $this->Rect(157, 50, 43, 8);



        $this->Text(106, 65, '13 - TERCEIROS');
        $this->Rect(105, 58, 52, 8);
        $this->Text(158, 65, 'R$ 0,00');
        $this->Rect(157, 58, 43, 8);

        $this->Text(106, 73, utf8_decode('14 - DEDUÇÕES'));
        $this->Rect(105, 66, 52, 8);
        $this->Text(158, 73, 'R$ 0,00');
        $this->Rect(157, 66, 43, 8);

        $this->Text(106, 81, utf8_decode('15 - TOTAL LÍQUIDO'));
        $this->Rect(105, 74, 52, 8);
        $this->Text(158, 81, 'R$ 0,00');
        $this->Rect(157, 74, 43, 8);

        $this->Text(106, 89, '16 - ATUAL. MONETRIA');
        $this->Rect(105, 82, 52, 8);
        $this->Text(158, 89, 'R$ 0,00');
        $this->Rect(157, 82, 43, 8);

        $this->Text(106, 97, '17 - JUROS/MULTA');
        $this->Rect(105, 90, 52, 8);
        $this->Text(158, 97, 'R$ 0,00');
        $this->Rect(157, 90, 43, 8);

        $this->Text(106, 105, '18 - TOTAL');
        $this->Rect(105, 98, 52, 8);
        $this->Text(158, 105, 'R$ 0,00');
        $this->Rect(157, 98, 43, 8);
    }
    /**
     * Imprimi PDF
     *
     * @return PDF
     */
    private function imprimir()
    {
        $nomeArquivo = 'tmp/guia_previdencia' . time() . '.pdf';
        $this->output('F', $nomeArquivo);
        return [
            "name" => "Relatório da Previdência",
            "path" => $nomeArquivo,
            'pathExterno' => ECIDADE_REQUEST_PATH . $nomeArquivo
        ];
    }
}
