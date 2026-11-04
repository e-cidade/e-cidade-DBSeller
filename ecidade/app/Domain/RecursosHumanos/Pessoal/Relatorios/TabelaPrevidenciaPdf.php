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

class TabelaPrevidenciaPdf extends Pdf
{
    private $dados;
    public $rgps;
    public $totalServidores;
    /**
     * Contrutor da classe
     *
     * @param array $dados para a montagem do formulario
     */
    public function __construct(array $dados)
    {
        parent::__construct("P", "mm", array(205, 400));
        $this->dados = (array)$dados;
    }

    /**
     * Metodo responsavel por retornar o tipo de impressão
     *
     * @return Impressao
     */
    public function emitir()
    {
        $this->dadosRelatorio($this->dados['relatorio']);
        return $this->imprimir();
    }

    /**
     * Informações comuns a qualquer tipo de previdência
     *
     * @return void
     */
    private function headerPage()
    {
        $this->setFont('Arial', 'B', 12);
        $this->setY(5);
        $this->setX(60);
        $this->Cell(60, 5, $this->convertUTF8Decode("Relatórios Previdência e Encargos"), 0, 1, 'C', 0);

        $this->setFont('Arial', 'B', 10);
        $this->setX(6);

        $pathLogo = "imagens/files/{$this->dados['instituicao'][0]['logo']}";
        if (file_exists($pathLogo)) {
            $this->Image($pathLogo, 10, 10, 30);
        } else {
            $this->Cell(30, 30, "Sem Imagem", 1, 0, 'C', 0);
        }
        
        $this->setY(5);
        $this->setX(40);
        $this->Cell(10, 30, $this->convertUTF8Decode("Empresa: {$this->dados['instituicao'][0]['nomeinst']}"), 0, 1);
        $this->setY(10);
        $this->setX(40);
        $this->Cell(10, 30, $this->convertUTF8Decode("Endereço: {$this->dados['instituicao'][0]['ender']}"), 0);
        $this->setY(15);
        $this->setX(40);
        $tel = $this->dados['instituicao'][0]['telef'];
        $cnpj = $this->dados['instituicao'][0]['cgc'];
        $telCnpj = "Telefone: {$tel}    CNPJ: {$cnpj}";
        $this->Cell(10, 30, $this->convertUTF8Decode($telCnpj), 0);
        $this->setY(20);
        $this->setX(40);
        $this->Cell(10, 30, $this->convertUTF8Decode("Email: {$this->dados['instituicao'][0]['email']}"), 0);

        $this->setY(35);
        $this->setX(6);
        $this->Cell(10, 30, $this->convertUTF8Decode("Período: {$this->dados['mes']}/{$this->dados['ano']}"), 0);
        $this->SetDrawColor(188, 188, 188);
        $this->Line(5, 55, 200, 55);

        $tipoLinha1 = [];
        for ($i = 0; $i < sizeof($this->dados['tipo']); $i++) {
            if (isset($this->dados['tipo'][$i]['sa'])) {
                $tipoLinha1[] = $this->dados['tipo'][$i]['sa'];
            }
            if (isset($this->dados['tipo'][$i]['co'])) {
                $tipoLinha1[] = $this->dados['tipo'][$i]['co'];
            }
            if (isset($this->dados['tipo'][$i]['su'])) {
                $tipoLinha1[] = $this->dados['tipo'][$i]['su'];
            }
            if (isset($this->dados['tipo'][$i]['re'])) {
                $tipoLinha1[] = $this->dados['tipo'][$i]['re'];
            }
            if (isset($this->dados['tipo'][$i]['d13'])) {
                $tipoLinha1[] = $this->dados['tipo'][$i]['d13'];
            }
            if (isset($this->dados['tipo'][$i]['fe'])) {
                $tipoLinha1[] = $this->dados['tipo'][$i]['fe'];
            }
        }

        $textoArquivo = $this->Text(52, 51, $this->convertUTF8Decode("Arquivo:"));
        $this->setFont('Arial', 'I', 9);
        $this->Text(70, 51, $this->convertUTF8Decode($textoArquivo . implode(',', $tipoLinha1)));
    }

    public function footerPage()
    {
        $this->aliasNbPages();
        
        $pageHeight = $this->getPageHeight();

        // Ajusta a posição do rodapé para 15 unidades acima do fim da página
        $footerY = $pageHeight - 15;
        
        $this->setFont("Arial", "", 10);
        // Desenha a linha do rodapé
        $this->SetDrawColor(188, 188, 188);
        $this->Line(5, $footerY, 200, $footerY);
    
        // Define a posição Y para o texto
        $this->setY($footerY + 3);
    
        // Imprime o número da página com a formatação correta
        $this->Cell(0, 10, $this->convertUTF8Decode("Página :" . $this->pageNo() . " de {nb}"), 0, 0, "C");
    }

    private function dadosRelatorio($dadosRelatorio)
    {
        // dd($dadosRelatorio);
        $gRgps = []; // Acumulador para o patronal total RGPS
        $grupoRpps = []; // Acumulador para o patronal total RPPS
        foreach ($dadosRelatorio as $codigo => $regimes) {
            $this->addPage();
            $this->headerPage();
            $this->setFont("Arial", "", 10);
            $this->setY($this->getY() + 20);
            $this->setX(5);
            $firstRegime = reset($regimes);
            $regimesReset = $firstRegime['lotacao'] . ' ( ' . $firstRegime['codigo'] . ' )';
            $this->Cell(0, 10, $regimesReset);
            foreach ($regimes as $dados) {
                // Cabeçalhos das colunas
                $this->SetDrawColor(188, 188, 188);
                $this->Line(5, 55, 200, 55);
                $this->setFont('Arial', 'B', 8);
                $this->SetY($this->getY() + 2);
                $this->SetX(6);
                $desc = $dados['regime'];
                $this->cell(strlen($desc), 15, $this->convertUTF8Decode($desc));

                // Colunas dos dados
                $this->SetY($this->getY() + 10);
                $this->SetX(6);
                $this->Cell(20, 5, 'Quant. Func.', 1, 0, 'L', 0);
                $this->Cell(21, 5, $this->convertUTF8Decode('Base Cálculo'), 1, 0, 'L', 0);
                $this->Cell(27, 5, 'Desc. Segurados', 1, 0, 'L', 0);
                $this->Cell(24, 5, 'Patronal+Gilrat', 1, 0, 'L', 0);
                $this->Cell(27, 5, 'Sal. Maternidade', 1, 0, 'L', 0);
                $this->Cell(19, 5, 'Sal. Familia', 1, 0, 'L', 0);
                $this->Cell(40, 5, 'Apos.Esp.(Agentes Nocivos)', 1, 0, 'L', 0);
                $this->Cell(18, 5, 'FGTS', 1, 1, 'L', 0);


                // Preenchimento das colunas
                $this->setFont('Arial', '', 8);
                $this->SetX(10);
                $this->Cell(17, 5, $dados['total_servidores'], 0, 0, 'L', 0);
                $this->SetX(27);
                $this->Cell(20, 5, number_format($dados['total_liquido'], 2, ",", "."), 0, 0, 'L', 0);
                $this->SetX(48);
                $this->Cell(30, 5, number_format($dados['salario_deducao'], 2, ",", "."), 0, 0, 'L', 0);

                // Cálculo do valor patronal e preenchimento
                $this->SetX(75);
                $patronal = ($dados['patronal'] * ($dados['total_liquido'] / 100));
                $this->Cell(20, 5, number_format($patronal, 2, ",", "."), 0, 0, 'L', 0);
                // return number_format(floatval($fValor), $decimais, ",", ".");
                //SAL. MATERNIDADE
                $this->SetX(100);
                $this->Cell(30, 5, number_format($dados['salario_maternidade'], 2, ",", "."), 0, 0, 'L', 0);
                $this->SetX(127);

                $this->Cell(30, 5, number_format($dados['salario_familia'], 2, ",", "."), 0, 0, 'L', 0);

                $this->SetX(145);
                $this->Cell(19, 5, number_format($dados['salario_agente_nocivo'], 2, ",", "."), 0, 0, 'L', 0);
                $this->SetX(185);
                $this->Cell(19, 5, number_format($dados['salario_fgts'], 2, ",", "."), 0, 0, 'L', 0);


                if ($dados['rgps'] > 0) {
                    $codigo = $dados['codigo'];
                    if (empty($gRgps[$codigo])) {
                        $gRgps[$codigo] = $this->inicializarGrupodDadosRgps();
                    }
                    $this->atualizarValor($gRgps[$codigo], 'rgps_salario_cfpess', $dados['total_liquido']);
                    $gRgps[$codigo]['rgps_patronal'] =  $dados['patronal'];
                    //AVULSO
                    if ($dados['categorias'] > 0) {
                        $this->atualizarValor($gRgps[$codigo], 'rgps_salario_avulso', $dados['total_liquido']);
                        $this->atualizarValor($gRgps[$codigo], 'rgps', $dados['rgps']);
                        $this->atualizarValor($gRgps[$codigo], 'rgps_salario_familia', $dados['salario_familia']);
                        $this->atualizarValor($gRgps[$codigo], 'rgps_sal_maternidade', $dados['salario_maternidade']);
                        $this->atualizarValor($gRgps[$codigo], 'rgps_sal_deducao_avulso', $dados['salario_deducao']);
                    } else {
                        $this->atualizarValor($gRgps[$codigo], 'rgps_salario_individual', $dados['total_liquido']);
                        $this->atualizarValor($gRgps[$codigo], 'rgps', $dados['rgps']);
                        $this->atualizarValor($gRgps[$codigo], 'rgps_sal_maternidade', $dados['salario_maternidade']);
                        $this->atualizarValor($gRgps[$codigo], 'rgps_salario_familia', $dados['salario_familia']);
                        $this->atualizarValor($gRgps[$codigo], 'rgps_sal_deducao_ind', $dados['salario_deducao']);
                    }
                }

                if ($dados['rpps'] > 0) {
                    $codigo = $dados['codigo'];

                    if (empty($grupoRpps[$codigo])) {
                        $grupoRpps[$codigo] = $this->inicializarGrupodDadosRpps();
                    }
                    $this->atualizarValor($grupoRpps[$codigo], 'rpps_salario_cfpess', $dados['total_liquido']);
                    $grupoRpps[$codigo]['rpps_patronal'] =  $dados['patronal'];

                    if ($dados['categorias'] > 0) {
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps_salario_patronal', $dados['total_liquido']);
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps_salario_deducao', $dados['salario_deducao']);
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps', $dados['rpps']);

                        $this->atualizarValor(
                            $grupoRpps[$codigo],
                            'rpps_sal_maternidade',
                            $dados['salario_maternidade']
                        );
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps_salario_familia', $dados['salario_familia']);
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps_sal_deducao_ind', $dados['salario_deducao']);
                    } else {
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps_salario_patronal', $dados['total_liquido']);
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps_salario_deducao', $dados['salario_deducao']);
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps', $dados['rpps']);

                        $this->atualizarValor(
                            $grupoRpps[$codigo],
                            'rpps_sal_maternidade',
                            $dados['salario_maternidade']
                        );
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps_salario_familia', $dados['salario_familia']);
                        $this->atualizarValor($grupoRpps[$codigo], 'rpps_sal_deducao_ind', $dados['salario_deducao']);
                    }
                }
            }
           
            $this->setFont('Arial', 'B', 8);
            $this->SetY($this->GetY() + 10);
            $this->SetX(6);
            $this->Cell(97, 5, $this->convertUTF8Decode('Regime Geral - RGPS'), 1, 0, 'C', 0);

            $this->SetY($this->GetY() + 5);
            $this->SetX(6);
            $this->Cell(60, 5, $this->convertUTF8Decode('Descrição'), 1, 0, 'C', 0);
            $this->Cell(37, 5, 'Valor', 1, 0, 'C', 0);
            //relatorio GERAL - RGPS (Empregados/Avulsos)
            $patronal_individual_rgps = 0;
            $patronal_avulso_rgps = 0;
            $rgps_sal_deducao_avulso = 0;
            $rgps_sal_deducao_ind = 0;

            $this->SetY($this->GetY() + 5);
            $this->SetX(6);
            
            // Exibe o valor de Patronal - Empregados/Avulsos
            $this->Cell(60, 5, 'Patronal - Empregador/RPA', 'LR', 0, 'L', 0);
            $patronal_avulso_rgps = isset($gRgps[$codigo]['rgps_salario_avulso']) ?
                $gRgps[$codigo]['rgps_patronal'] * ($gRgps[$codigo]['rgps_salario_avulso'] / 100)
                : 0;
            $this->Cell(37, 5, number_format($patronal_avulso_rgps, 2, ",", "."), 'LR', 0, 'R', 0);

            // Move para a próxima linha
            $this->SetY($this->GetY() + 5);
            $this->SetX(6);

            // Exibe o valor de Patronal - Contribuinte Individual RGPS
            $this->Cell(60, 5, 'Patronal - Empregador', 'L', 0, 'L', 0);
            $patronal_individual_rgps = (isset($gRgps[$codigo]['rgps_salario_individual'])) ?
                $gRgps[$codigo]['rgps_patronal'] * ($gRgps[$codigo]['rgps_salario_individual'] / 100)
                : 0;
            $this->Cell(37, 5, number_format($patronal_individual_rgps, 2, ",", "."), 'LR', 0, 'R', 0);

            $this->SetY($this->GetY() + 5);
            $this->SetX(6);
            $this->Cell(60, 5, 'Segurados - RPA', 'LR', 0, 'L', 0);

            $rgps_sal_deducao_avulso = isset($gRgps[$codigo]['rgps_sal_deducao_avulso']) ?
                $gRgps[$codigo]['rgps_sal_deducao_avulso']
                : 0;
            $this->Cell(37, 5, number_format($rgps_sal_deducao_avulso, 2, ",", "."), 'LR', 0, 'R', 0);
            $this->SetY($this->GetY() + 5);
            $this->SetX(6);
            $this->Cell(60, 5, 'Segurados - Contrib. Individual', 'LR', 0, 'L', 0);
            $rgps_sal_deducao_ind = isset($gRgps[$codigo]['rgps_sal_deducao_ind']) ?
                $gRgps[$codigo]['rgps_sal_deducao_ind']
                : 0;
            $this->Cell(37, 5, number_format($rgps_sal_deducao_ind, 2, ",", "."), 'LR', 0, 'R', 0);

            $this->SetY($this->GetY() + 5);
            $this->SetX(6);
            $this->Cell(60, 5, ' ', 'LRB', 0, 'C', 0);
            $this->Cell(37, 5, '', 'LR', 0, 'R', 0);
            // Exibe os totais
            $this->SetY($this->GetY() + 5);
            $this->SetX(6);
            $this->Cell(40, 5, 'TOTAL', 'LB', 0, 'L', 0);
            if (isset($gRgps[$codigo]['rgps'])) {
                $this->Cell(20, 5, $gRgps[$codigo]['rgps'], 'TRB', 0, 'R', 0);
            } else {
                $this->Cell(20, 5, 0, 'TRB', 0, 'R', 0);
            }
            $total_rgps = ($patronal_avulso_rgps
            + $patronal_individual_rgps
            + $rgps_sal_deducao_avulso
            + $rgps_sal_deducao_ind);
            $this->Cell(37, 5, (number_format($total_rgps, 2, ",", ".")), 'TRB', 0, 'R', 0);

            //==========================RPPS==========================================
            $rpps_salario_patronal = 0;
            $this->setFont('Arial', 'B', 8);
            $this->SetY($this->GetY() - 35);
            $this->SetX(105);
            $this->Cell(97, 5, $this->convertUTF8Decode('Regime Próprio - RPPS'), 1, 0, 'C', 0);

            $this->SetY($this->GetY() + 5);
            $this->SetX(105);
            $this->Cell(60, 5, $this->convertUTF8Decode('Descrição'), 1, 0, 'C', 0);
            $this->Cell(37, 5, 'Valor', 1, 0, 'C', 0);
            //relatorio GERAL - RGPS (Empregados/Avulsos)
            $this->SetY($this->GetY() + 5);
            $this->SetX(105);

            $rpps_salario_patronal = (isset($grupoRpps[$codigo]['rpps_salario_patronal'])) ?
                $grupoRpps[$codigo]['rpps_patronal'] * ($grupoRpps[$codigo]['rpps_salario_patronal'] / 100)
                : 0;

            $rpps_salario_deducao = (isset($grupoRpps[$codigo]['rpps_salario_deducao'])) ?
                $grupoRpps[$codigo]['rpps_salario_deducao']
                : 0;

            // Exibe o valor de Patronal - Empregados/Avulsos

            $this->Cell(60, 5, 'Patronal - Empregador', 'LR', 0, 'L', 0);
            $salario_rpps = ($rpps_salario_patronal > 0) ?  $rpps_salario_patronal : 0;
            // print_r($salario_rpps);
            $this->Cell(37, 5, number_format($salario_rpps, 2, ",", "."), 'LR', 0, 'R', 0);
            // Move para a próxima linha


            $this->SetY($this->GetY() + 5);
            $this->SetX(105);
            $this->Cell(60, 5, 'Segurados - Contrib. Individual', 'LR', 0, 'L', 0);
            $deducao_formater = number_format($rpps_salario_deducao, 2, ",", ".");
            $this->Cell(37, 5, $deducao_formater, 'LR', 0, 'R', 0);
            $this->SetY($this->GetY() + 5);
            $this->SetX(105);
            $this->Cell(60, 5, '', 'LR', 0, 'L', 0);
            $this->Cell(37, 5, '', 'LR', 0, 'R', 0);

            $this->SetY($this->GetY() + 5);
            $this->SetX(105);
            $this->Cell(60, 5, ' ', 'LR', 0, 'C', 0);
            $this->Cell(37, 5, '', 'LR', 0, 'R', 0);

            $this->SetY($this->GetY() + 5);
            $this->SetX(105);

            // LINHA EM BRANCO
            $this->Cell(60, 5, '', 'L', 0, 'L', 0);

            $this->Cell(37, 5, '', 'LR', 0, 'R', 0);
            // Exibe os totais
            //Regime Próprio - RPPS
            $this->SetY($this->GetY() + 5);
            $this->SetX(105);
            $this->Cell(40, 5, 'TOTAL', 'TLB', 0, 'L', 0);
            if (isset($grupoRpps[$codigo]['rpps'])) {
                $this->Cell(20, 5, $grupoRpps[$codigo]['rpps'], 'TRB', 0, 'R', 0);
            } else {
                $this->Cell(20, 5, 0, 'TRB', 0, 'R', 0);
            }
            $patronal_avulso_rgps = $rpps_salario_deducao + $salario_rpps;
            $avulso_formater = number_format($patronal_avulso_rgps, 2, ",", ".");
            $this->Cell(37, 5, $avulso_formater, 'TRB', 0, 'R', 0);

            //Deduções RGPS Demais Encargos RGPS RPPS
            $salario_familia = 0;
            $salario_maternidade = 0;
            $rgps_salario_terceiros = 0;
            $rgps_salario_fat = 0;

            $this->setX(6);
            $this->SetY($this->GetY());
            $this->Cell(60, 5, '', 0, 0, 'C', 0);
            $this->SetY($this->GetY() + 10);
            $this->SetX(6);
            $this->setFont('Arial', 'B', 8);
            $this->Cell(60, 5, $this->convertUTF8Decode('Deduções'), 1, 0, 'C', 0);
            $this->Cell(37, 5, 'RGPS', 1, 0, 'C', 0);

            $this->SetY($this->GetY());
            $this->SetX(105);
            $this->Cell(60, 5, $this->convertUTF8Decode('Demais Encargos'), 1, 0, 'C', 0);
            $this->Cell(37, 5, 'RGPS', 1, 0, 'C', 0);

            $this->SetY($this->getY() + 5);
            $this->SetX(6);
            $this->setFont('Arial', '', 8);
            $this->Cell(60, 5, $this->convertUTF8Decode('Salário Familia'), 'LR', 0, 'L', 0);

            //VERIFICAR
            $salario_familia = isset($gRgps[$codigo]['rgps_salario_familia']) ?
                $gRgps[$codigo]['rgps_salario_familia'] : 0;

            $this->Cell(37, 5, number_format($salario_familia, 2, ",", "."), 'LR', 0, 'R', 0);

            $rgps_salario_terceiros += isset($gRgps[$codigo]['rgps_salario_cfpess']) ?
                (($gRgps[$codigo]['rgps_salario_cfpess'] * $dados['terceiros']) / 100) : 0;
            $this->SetY($this->getY());
            $this->SetX(105);
            $this->Cell(60, 5, 'Terceiros', 'LR', 0, 'L', 0);
            $this->Cell(37, 5, number_format($rgps_salario_terceiros, 2, ",", "."), 'LR', 0, 'R', 0);

            $this->SetY($this->getY() + 5);
            $this->SetX(6);

            $salario_maternidade += isset($gRgps[$codigo]['rgps_sal_maternidade']) ?
                $gRgps[$codigo]['rgps_sal_maternidade'] : 0;


            $this->Cell(60, 5, $this->convertUTF8Decode('Salário Maternidade'), 'LR', 0, 'L', 0);
            $this->Cell(37, 5, number_format($salario_maternidade, 2, ",", "."), 'LR', 0, 'R', 0);

            $rgps_salario_fat += isset($gRgps[$codigo]['rgps_salario_cfpess']) ?
                ($gRgps[$codigo]['rgps_salario_cfpess'] * $dados['fat']) / 100 : 0;
            $this->SetY($this->getY());
            $this->SetX(105);
            $this->Cell(60, 5, 'FAT', 'LR', 0, 'L', 0);
            $this->Cell(37, 5, number_format($rgps_salario_fat, 2, ",", "."), 'LR', 0, 'R', 0);
            //TOTAL DEDUCOES
            $this->SetY($this->getY() + 5);
            $this->SetX(6);
            $this->setFont('Arial', 'B', 8);
            $this->Cell(60, 5, 'TOTAL', 'TLRB', 0, 'L', 0);
            $this->setFont('Arial', '', 8);
            $total_deducoes = $salario_maternidade + $salario_familia;
            $this->Cell(37, 5, number_format($total_deducoes, 2, ",", "."), 'TLRB', 0, 'R', 0);
            //TOTAL DEMAIS ENCARGOS
            $this->SetY($this->getY());
            $this->SetX(105);
            $this->setFont('Arial', 'B', 8);
            $this->Cell(60, 5, 'TOTAL', 'TLRB', 0, 'L', 0);
            $this->setFont('Arial', '', 8);
            $total_fat_terc = ($rgps_salario_fat + $rgps_salario_terceiros);
            $this->Cell(37, 5, number_format($total_fat_terc, 2, ",", "."), 'TLRB', 0, 'R', 0);

            $this->footerPage();
        }
    }

    // Função Genérica
    private function atualizarValor(&$array, $chave, $valor)
    {
        if (isset($array[$chave])) {
            $array[$chave] += $valor;
        } else {
            $array[$chave] = 0;
        }
    }
    // Função para inicializar dados agrupados
    public function inicializarGrupodDadosRgps()
    {
        return [
            'rgps_patronal' => 0,
            'rgps_total_servidores' => 0,
            'rgps_total_liquido' => 0,
            'rgps_salario_fgts' => 0,
            'rgps_sal_deducao_avulso' => 0,
            'rgps_sal_deducao_ind' => 0,
            'rgps_salario_familia' => 0,
            'rgps_sal_maternidade' => 0,
            'rgps_salario_agente_nocivo' => 0,
            'rgps_salario_avulso' => 0,
            'rgps_salario_individual' => 0,
            'rgps_categorias' => 0,
            'rgps' => 0,
            'rpps' => 0,
            'rgps_salario_cfpess' => 0,
        ];
    }

    public function inicializarGrupodDadosRpps()
    {
        return [
            'rpps_patronal' => 0,
            'rpps_total_servidores' => 0,
            'rpps_total_liquido' => 0,
            'rpps_salario_fgts' => 0,
            'rpps_salario_deducao' => 0,
            'rpps_salario_agente_nocivo' => 0,
            'rpps_salario_patronal' => 0,
            'rpps_salario_individual' => 0,
            'rpps_categorias' => 0,
            'rpps' => 0,
            'rgps' => 0,
        ];
    }

     //SUBSTUTUI utf8_decode
    private function convertUTF8Decode($string)
    {
        return mb_convert_encoding($string, 'ISO-8859-1', 'UTF-8');
    }

    /**
     * Imprimi PDF
     *
     * @return PDF
     */
    private function imprimir()
    {
        $nomeArquivo = 'tmp/tabela-previdencia' . time() . '.pdf';

        $this->output('F', $nomeArquivo);

        $retorno =  [
            "name" => $this->convertUTF8Decode("Relatório da Tabela de Previdência PDF"),
            "path" => $nomeArquivo,
            'pathExterno' => ECIDADE_REQUEST_PATH . $nomeArquivo
        ];

        return $retorno;
    }
}
