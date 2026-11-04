<?php

namespace App\Domain\Educacao\TransporteEscolar\Services;

use AlunoRepository;
use ECidade\Pdf\Pdf;
use InstituicaoRepository;

class EmissorCarteiraTransportePdf extends Pdf
{
    protected $dados;
    /**
     * @var int
     */
    private $incremento;

    public function __construct(array $dados)
    {
        parent::__construct('P');
        $this->dados = $dados;
        $this->addTitulo('EMISSÃO DE CARTEIRA DE TRANSPORTE', 1);
        $this->addTitulo('', 2);
        $this->addTitulo($this->dados['escola'], 3);
        $this->incremento = 0;
    }

    private function initPdf()
    {
        $this->mostrarRodape();
        $this->mostrarTotalDePaginas();
        $this->setMargins(10, 8, 8);
        $this->setAutoPageBreak(false, 10);
        $this->aliasNbPages();
        $this->setFont('Arial', 'B', 9);
        $this->exibeHeader(true, 1);
        $this->setExibeBrasao(true);
    }

    public function emitir()
    {
        $this->initPdf();

        foreach ($this->dados['alunos'] as $aluno) {
            $this->verificaAlturaRestante();
            $this->imprimeCarteira($aluno);
            $this->incremento += 80;
        }

        $fileName = 'tmp/carteira-transporte' . time() . '.pdf';
        $this->output('F', $fileName);

        return [
            "name" => "Carteira Transporte",
            "path" => $fileName,
            'pathExterno' => ECIDADE_REQUEST_PATH . $fileName
        ];
    }

    private function imprimeCarteira($dadosAluno)
    {
        $this->setFillColor(200);
        $this->setMargins(10, 8, 8);
        $this->imprimeFormato();
        $this->imprimeRetangulosInternos();
        $this->imprimeFieldsets();
        $this->imprimeLabelFieldset();
        $this->imprimeInstituicao();
        $this->imprimeFotoPerfil($dadosAluno);
        $this->imprimeLabelDadosAluno();
        $this->imprimeLabelDadosRota();
        $this->imprimeLabelDadosComplementares();
        $this->imprimeDadosAluno($dadosAluno);
        $this->imprimeDadosRota($dadosAluno);
        $this->imprimeDadosComplementares($dadosAluno);
        $this->imprimeFooter();
    }

    private function imprimeFormato()
    {
        // Retângulo de corte
        $this->setDrawColor(200, 200, 200);
        $this->rect(8, 50.5 + $this->incremento, 194, 69);
        // Retângulo maior
        $this->setDrawColor(0, 119, 182);
        $this->roundedRect(10, 52.5 + $this->incremento, 190, 65, 3);
        // Retângulo maior inferior
        $imagePath = realpath(
            __DIR__ . '/../../../../../imagens/educacao/transporte-escolar/carteira/background-carteira-transporte.png'
        );
        $this->image($imagePath, 11, 53.5 + $this->incremento, 188, 63);
        // Retângulo que divide a carteira ao meio
        $this->setDrawColor(255, 255, 255);
        $this->setFillColor(255, 255, 255);
        $this->rect(104.5, 53.5 + $this->incremento, 1, 63, 'DF');
    }

    private function imprimeRetangulosInternos()
    {
        $this->setDrawColor(0, 119, 182);
        $this->setFillColor(255, 255, 255);
        // Imprime retângulo da foto
        $this->roundedRect(12.2, 63.5 + $this->incremento, 21, 26, 3, 'DF');
        // Retângulo dados pessoais
        $this->roundedRect(35.2, 63.5 + $this->incremento, 67, 26, 3, 'DF');
        // Retângulo dados da rota
        $this->roundedRect(12.2, 94 + $this->incremento, 90, 21.5, 3, 'DF');
        // Retângulo dados complementares
        $this->roundedRect(108, 56 + $this->incremento, 89.5, 35.6, 3, 'DF');
        // Retângulo atenção
        $this->roundedRect(108, 95 + $this->incremento, 89.5, 11.6, 3, 'DF');
        // Retângulo assinatura
        $this->roundedRect(108, 109 + $this->incremento, 89.5, 6.6, 3, 'DF');
    }

    private function imprimeFieldsets()
    {
        $this->setDrawColor(0, 119, 182);
        $this->setFillColor(0, 119, 182);
        // Fieldset Dados Pessoais
        $this->roundedRect(65.7, 61 + $this->incremento, 30, 5, 2.5, 'DF');
        // Fieldset Dados da Rota
        $this->roundedRect(65.7, 91.5 + $this->incremento, 30, 5, 2.5, 'DF');
        // Fieldset Validade
        $this->roundedRect(30.7, 113.5 + $this->incremento, 50, 3, 1.5, 'DF');
        // Fieldset Dados complementares
        $this->roundedRect(150.5, 53.5 + $this->incremento, 40, 5, 2.5, 'DF');
        // Fieldset Atenção
        $this->roundedRect(160.5, 92.5 + $this->incremento, 30, 5, 2.5, 'DF');
        // Fieldset Assinatura
        $this->setDrawColor(255, 255, 255);
        $this->setFillColor(255, 255, 255);
        $this->roundedRect(136, 107.5 + $this->incremento, 33, 5, 2.5, 'DF');
    }

    private function imprimeLabelFieldset()
    {
        $this->setXY(66, 61 + $this->incremento);
        $this->setFont('Arial', 'B', 9);
        $this->setTextColor(255, 255, 255);
        // Label Dados Pessoais
        $this->cell(30, 5, 'Dados Pessoais', 0, 0, 'C');
        // Label Dados da Rota
        $this->setXY(66, 91.5 + $this->incremento);
        $this->cell(30, 5, 'Dados da Rota', 0, 0, 'C');
        // Label Dados Complementares
        $this->setXY(151, 53.5 + $this->incremento);
        $this->cell(40, 5, 'Dados Complementares', 0, 0, 'C');
        // Label Atenção
        $this->setXY(161, 92.5 + $this->incremento);
        $this->cell(27.5, 5, 'Atenção', 0, 0, 'C');
        // Label Validade
        $this->setFont('Arial', '', 7);
        $anoAtual = date('Y');
        $this->setXY(33, 113 + $this->incremento);
        $this->cell(45, 4, 'Válida durante o calendário escolar ' . $anoAtual, 0, 0, 'C');
        // Label Assinatura
        $this->setFont('Arial', '', 8);
        $this->setTextColor(2, 62, 138);
        $this->setXY(136, 107 + $this->incremento);
        $this->cell(33, 5, 'Assinatura Diretor(a)', 0, 0, 'C');
    }

    private function imprimeInstituicao()
    {
        $instituicao = InstituicaoRepository::getInstituicaoSessao();

        $this->setXY(12, 54 + $this->incremento);
        $this->setFont('Arial', 'B', 8);
        $this->setTextColor(2, 62, 138);
        $this->cell(90, 3, $instituicao->getDescricao(), 0, 1, 'C');
        $this->setFont('Arial', 'B', 7);
        $this->cell(90, 4, 'SECRETARIA MUNICIPAL DE EDUCAÇÃO', 0, 1, 'C');
    }

    private function imprimeFotoPerfil($dadosAluno)
    {
        // Imprime imagem Foto Usuário
        if (!empty($dadosAluno['fotoperfil'])) {
            $sCaminhoFoto = AlunoRepository::getAlunoByCodigo($dadosAluno['codAluno'])->getFoto();
            $imagePath = realpath(__DIR__ . '/../../../../../' . $sCaminhoFoto);
            $this->image($imagePath, 13.5, 65.5 + $this->incremento, 18, 22);
        } else {
            $imagePath = realpath(__DIR__ . '/../../../../../imagens/educacao/transporte-escolar/carteira/user.png');
            $this->image($imagePath, 15, 68 + $this->incremento, 15, 17);
        }
    }

    private function verificaAlturaRestante()
    {
        $alturaRestante = $this->getY();
        if ($this->incremento === 0 || $alturaRestante + 80 >= $this->getH()) {
            $this->setTextColor(55, 55, 55);
            $this->setDrawColor(55, 55, 55);
            $this->addPage();
            $this->incremento = 0;
        }
    }

    private function imprimeLabelDadosAluno()
    {
        $this->setXY(36, 68 + $this->incremento);
        $this->setTextColor(2, 62, 138);
        $this->setFont('Arial', 'B', 8);
        // Label Nome
        $this->cell(65, 5, 'Nome: ', 0, 1, 'L');
        $this->setX(36);
        // Label CPF
        $this->cell(65, 5, 'CPF: ', 0, 1, 'L');
        $this->setX(36);
        // Label Data Nascimento
        $this->cell(65, 5, 'Data Nascimento: ', 0, 1, 'L');
        $this->setX(36);
        // Label Tipo Sanguíneo
        $this->cell(65, 5, 'Tipo Sanguíneo: ', 0, 1, 'L');
    }

    private function imprimeDadosAluno($dadosAluno)
    {
        $this->setXY(46, 68 + $this->incremento);
        $this->setTextColor(2, 62, 138);
        // Imprime Nome Aluno
        $nomeAluno = 'Nome: ';
        $nomeAluno .= $dadosAluno['nomeAluno'];
        $this->setFont('Arial', '', $this->calculaTamanhoFonte(65, $nomeAluno, 8));
        $this->cell(65, 5, $dadosAluno['nomeAluno'], 0, 1, 'L');
        // Imprime CPF
        $this->setFont('Arial', '', 8);
        $this->setX(44);
        $cpf = !empty($dadosAluno['cpfAluno']) ? $dadosAluno['cpfAluno'] : '###.###.###-##';
        $this->cell(65, 5, $cpf, 0, 1, 'L');
        // Imprime Data Nascimento Aluno
        $this->setX(61);
        $this->cell(65, 5, $dadosAluno['dataNascimento'], 0, 1, 'L');
        // Imprime Tipo Sanguíneo
        $this->setX(60);
        $tipoSanguineo = !empty($dadosAluno['tipoSanguineo']) ? $dadosAluno['tipoSanguineo'] : '-';
        $this->cell(65, 5, $tipoSanguineo, 0, 1, 'L');

        if (!empty($dadosAluno['necessidade'])) {
            if ($dadosAluno['cadeirante'] === 'SIM') {
                $this->imprimeImagemPcD();
            } else {
                $this->imprimeImagemAcessibilidade();
            }
        }
    }

    private function imprimeImagemPcD()
    {
        // Imprime imagem PcD
        $imagePath = realpath(__DIR__ . '/../../../../../imagens/educacao/transporte-escolar/carteira/pcd.png');
        $this->image($imagePath, 85, 75 + $this->incremento, 12, 12);
    }

    private function imprimeImagemAcessibilidade()
    {
        // Imprime imagem Acessibilidade
        $imagePath = realpath(
            __DIR__ .
            '/../../../../../imagens/educacao/transporte-escolar/carteira/simbolo-de-acessibilidade-logo-universal.png'
        );
        $this->image($imagePath, 85, 75 + $this->incremento, 10, 10);
    }

    private function imprimeLabelDadosRota()
    {
        $this->setTextColor(2, 62, 138);
        $this->setFont('Arial', 'B', 8);
        $this->setXY(13, 97 + $this->incremento);
        $this->cell(90, 4, 'Embarque Ida: ', 0, 1, 'L');

        $this->setX(13);
        $this->cell(90, 4, 'Desembarque Ida: ', 0, 1, 'L');

        $this->setX(13);
        $this->cell(90, 4, 'Embarque Volta: ', 0, 1, 'L');

        $this->setX(13);
        $this->cell(90, 4, 'Desembarque Volta: ', 0, 1, 'L');
    }

    private function imprimeDadosRota($dadosAluno)
    {
        // Imprime Ponto de Embarque Ida
        $this->setXY(34, 97 + $this->incremento);
        $this->setTextColor(2, 62, 138);
        $this->setFont('Arial', '', $this->calculaTamanhoFonte(60, $dadosAluno['embarqueIda'], 8));
        $this->cell(60, 4, $dadosAluno['embarqueIda'], 0, 1, 'L');
        // Imprime Ponto de Desembarque Ida
        $this->setX(39);
        $this->setFont('Arial', '', $this->calculaTamanhoFonte(60, $dadosAluno['desembarqueIda'], 8));
        $this->cell(60, 4, $dadosAluno['desembarqueIda'], 0, 1, 'L');
        // Imprime Ponto de Embarque Volta
        $this->setX(37);
        $this->setFont('Arial', '', $this->calculaTamanhoFonte(60, $dadosAluno['embarqueVolta'], 8));
        $this->cell(60, 4, $dadosAluno['embarqueVolta'], 0, 1, 'L');
        // Imprime Ponto de Desembarque Volta
        $this->setX(42);
        $this->setFont('Arial', '', $this->calculaTamanhoFonte(60, $dadosAluno['desembarqueVolta'], 8));
        $this->cell(60, 4, $dadosAluno['desembarqueVolta'], 0, 1, 'L');
    }

    private function calculaTamanhoFonte($largura, $conteudo, $tamanhoFonte)
    {
        $fonteDimensionada = $this->getFonteSizeByWidthCell($largura, $tamanhoFonte, $conteudo);
        return min($fonteDimensionada, $tamanhoFonte);
    }

    private function imprimeLabelDadosComplementares()
    {
        $this->setFont('Arial', 'B', 8);
        $this->setTextColor(2, 62, 138);

        $this->setXY(110, 61 + $this->incremento);
        $this->cell(15, 4, 'Filiação A:', 0, 1, 'L');

        $this->setX(110);
        $this->cell(15, 4, 'Filiação B:', 0, 1, 'L');

        $this->setX(110);
        $this->cell(15, 4, 'Responsável:', 0, 1, 'L');

        $this->setX(110);
        $this->cell(15, 4, 'Contatos:', 0, 1, 'L');

        $this->setX(110);
        $this->cell(15, 4, 'Necessidades:', 0, 1, 'L');
    }

    private function imprimeDadosComplementares($dadosAluno)
    {
        $this->setXY(125, 61 + $this->incremento);
        $this->setFont('Arial', '', $this->calculaTamanhoFonte(95, $dadosAluno['nomeresponsavelum'], 8));
        $this->cell(15, 4, $dadosAluno['nomeresponsavelum'], 0, 1, 'L');

        $this->setX(125);
        $this->setFont('Arial', '', $this->calculaTamanhoFonte(95, $dadosAluno['nomeresponsaveldois'], 8));
        $this->cell(15, 4, $dadosAluno['nomeresponsaveldois'], 0, 1, 'L');

        $this->setX(129);
        $this->setFont('Arial', '', $this->calculaTamanhoFonte(90, $dadosAluno['nomeresponsavel'], 8));
        $this->cell(15, 4, $dadosAluno['nomeresponsavel'], 0, 1, 'L');

        $this->setX(126);
        $this->cell(15, 4, $dadosAluno['contatoUm'].  ' / ' . $dadosAluno['contatoDois'], 0, 1, 'L');

        $this->setX(131);
        if ($dadosAluno['necessidade'] !== null) {
            $escape = 0;
            foreach ($dadosAluno['necessidade'] as $necessidade) {
                if ($escape >= 2) {
                    break;
                }

                $this->setX(131);
                $this->setFont('Arial', '', $this->calculaTamanhoFonte(90, $necessidade['descricaoPai'], 8));
                $this->cell(15, 3.5, $necessidade['descricaoPai'], 0, 1, 'L');

                if (count($necessidade['subNecessidades']) > 0) {
                    $subNecessidadesTxt = '';
                    foreach ($necessidade['subNecessidades'] as $subNecessidades) {
                        $subNecessidadesTxt .= '(' . $subNecessidades['descricaoSub'] . ') ';
                    }
                    $this->setX(131);
                    $this->setFont('Arial', 'B', $this->calculaTamanhoFonte(100, $subNecessidadesTxt, 5));
                    $this->cell(15, 3, $subNecessidadesTxt, 0, 1, 'L');
                }

                $escape++;
            }
        } else {
            $this->cell(15, 4, '-', 0, 1, 'L');
        }
    }

    private function imprimeFooter()
    {
        $msgAtencao = 'Esta carteira de identificação é destinada exclusivamente';
        $msgAtencao.= ' para o uso no Transporte Escolar Municipal. O uso para';
        $msgAtencao.= ' qualquer outro propósito não é permitido.';
        $escola = $this->dados['escola'];

        $this->setTextColor(2, 62, 138);
        $this->setFont('Arial', '', 6);
        $this->setXY(109, 98 + $this->incremento);
        $this->multiCell(88, 4, $msgAtencao, 0, 'J', 0);
    }
}
