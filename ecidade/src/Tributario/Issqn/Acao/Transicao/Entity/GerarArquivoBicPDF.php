<?php
namespace ECidade\Tributario\Issqn\Acao\Transicao\Entity;

use Illuminate\Support\Facades\Log;

class GerarArquivoBicPDF
{
    private $inscricao;
    private $pdf;
    private $daoIssQuant;
    private $daoEscrito;
    private $rotulo;
    private $titulo = 9;
    private $texto = 8;
    private $caminho;

    public function __construct($inscricao)
    {
        require_once(modification("fpdf151/pdf.php"));
        $this->inscricao = $inscricao;
        $this->daoEscrito = new \cl_escrito();
        $this->daoIssQuant = new \cl_issquant();
        $this->rotulo = new \rotulocampo();
        $data = date('dmYHis');
        $this->caminho = "tmp/bic_inscricao_{$this->inscricao}_data_{$data}.pdf";
    }

    /**
     * @return string
     */
    public function getCaminho()
    {
        return $this->caminho;
    }

    /**
     * @param string $caminho
     */
    public function setCaminho($caminho)
    {
        $this->caminho = $caminho;
    }


    public function gerar()
    {
        $dadosCadastrais = $this->getDadosCadastrais();
        if (empty($dadosCadastrais)) {
            throw new \Exception(
                "Dados cadastrais não encotradados para inscrição {$this->inscricao}"
            );
        }
        $this->buildCabecalhoPDF($dadosCadastrais);
        $this->buildCorpoPDF($dadosCadastrais);
        $this->pdf->Output($this->caminho, false, true);
    }

    public function buildCorpoPDF(array $dadosCadastrais)
    {
        $total = 0;
        $alt = 4;
        $pri = true;
        $zona = $this->getZona();
        Log::debug("zona=>".json_encode($zona));
        $area = $this->getArea();
        Log::debug("area=>".json_encode($area));
        $socios = $this->getSocios();
        Log::debug("socios=>".json_encode($socios));
        $atividades = $this->getAtividades();
        Log::debug("ativdades=>".json_encode($atividades));
        $aidofs = $this->getAidof();
        Log::debug("aidofs=>".json_encode($aidofs));
        $simples = $this->getSimplesNacional();
        Log::debug("simples=>".json_encode($simples));
        $movimentacoes = $this->getMovimentacaoDoAlvara();
        Log::debug("movimentacoes=>".json_encode($movimentacoes));

        foreach ($dadosCadastrais as $dadoCadastral) {
            $dadoCadastral = (object)$dadoCadastral;
            if (($this->pdf->gety() > $this->pdf->h - 30) || $pri == true) {
                $this->pdf->addpage("");
                $this->pdf->setfillcolor(235);
                $this->buildCadastroCGM($dadoCadastral);
            }

            if (($this->pdf->gety() > $this->pdf->h - 30) || $pri == true) {
                $this->buildCadastroAlvara($dadoCadastral, $zona);
            }
        }

        $dadoCadastral = (object)$dadosCadastrais[0];

        // lado esquedo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Área de Publicidade:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$area->q30_areapublicidade}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);
        if ((isset($dadoCadastral->q02_obs) && $dadoCadastral->q02_obs != "")
            || (isset($dadoCadastral->q02_memo) && $dadoCadastral->q02_memo != "")) {
            $this->pdf->setX(5);
            $this->pdf->SetFont('Arial', 'B', $this->titulo);
            $this->pdf->Cell(200, 4, "Observações", "LRBT", 1, "C", 0);
            $this->pdf->SetFont('Arial', '', '8');
            $this->pdf->MultiCell(190, 4, $dadoCadastral->q02_obs . $dadoCadastral->q02_memo, "", "J", 0, 0);
        }

        $this->buildAtividades($atividades);
        $this->buildSocios($socios);
        $this->buildAidof($aidofs);
        $this->buildSimplesNacional($simples);
        $this->buildMovimentacaoDoAlvara($movimentacoes);
    }

    private function buildCabecalhoPDF(array $dadosCadastrais)
    {
        global $head4;
        global $head5;
        global $head6;
        $head4 = "BIC Alvará";
        $head5 = "Inscrição: {$this->inscricao}";
        $head6 = "CGM: {$dadosCadastrais[0]['z01_numcgm']}";
        $this->pdf = new \PDF();
        $this->pdf->Open();
        $this->pdf->AliasNbPages();
    }

    private function buildCadastroCGM($dadoCadastral)
    {
        $this->pdf->setX(5);
        $this->pdf->SetFont('Arial', 'B', $this->titulo);
        $this->pdf->Cell(200, 4, "Dados Cadastrais do CGM", "LRBT", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);

        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Nome:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->z01_nome}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado direito da tela
        if (strlen(trim($dadoCadastral->z01_cgccpf)) == 14) {
            $cpfcnpj = db_formatar($dadoCadastral->z01_cgccpf, "cnpj");
        } elseif (strlen(trim($dadoCadastral->z01_cgccpf)) == 11) {
            $cpfcnpj = db_formatar($dadoCadastral->z01_cgccpf, "cpf");
        } else {
            $cpfcnpj = $dadoCadastral->z01_cgccpf;
        }

        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "CNPJ/CPF:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "$cpfcnpj", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Endereço:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->z01_ender}, Nº {$dadoCadastral->z01_numero}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado direito da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Complemento:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "$dadoCadastral->z01_compl", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Bairro:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->z01_bairro}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado direito da tela
        $telefone = '';
        if (strlen(trim($dadoCadastral->z01_telef)) > 0) {
            $telefone = $dadoCadastral->z01_telef;
        }
        if (strlen(trim($dadoCadastral->z01_telcel)) > 0) {
            $telefone = $telefone . ' / ' . $dadoCadastral->z01_telcel;
        }

        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Fone:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "$telefone", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Cidade:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->z01_munic}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado direito da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "E-mail:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->z01_email}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Cep:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, db_formatar($dadoCadastral->z01_cep, "cep"), "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado direito da tela
        $this->pdf->setX(120);
        $this->pdf->Cell(60, 6, "", "", 1, "L", 0);
    }

    private function buildCadastroAlvara($dadoCadastral, $zona)
    {

        $this->pdf->setfillcolor(235);
        //lado esquerdo da tela
        $this->pdf->setX(5);
        $this->pdf->SetFont('Arial', 'B', $this->titulo);
        $this->pdf->Cell(200, 4, "Dados Cadastrais do Alvará", "LRBT", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);

        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Inscrição Municipal:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$this->inscricao}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado direito da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Inscrição Estadual:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->z01_incest}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Nome:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(120, 4, "{$dadoCadastral->z01_nome}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Nome Completo:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(120, 4, "{$dadoCadastral->z01_nomecomple}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Nome Fantasia:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->z01_nomefanta}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Registro na junta:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->q02_regjuc}", "", 0, "L", 0);
        //
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado direito da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Protocolo da Junta:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->q02_protocolojuntacomercial}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);
        //aqui

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Data da Junta:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, db_formatar($dadoCadastral->q02_dtjunta, "d"), "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        $complemento = $dadoCadastral->q02_numero;
        if (strlen(trim($dadoCadastral->q02_compl)) > 0) {
            $complemento .= ' / ' . $dadoCadastral->q02_compl;
        }

        //lado direito da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Data do cadastro:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, db_formatar($dadoCadastral->q02_dtcada, "d"), "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Data de início:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, db_formatar($dadoCadastral->q02_dtinic, "d"), "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Data de Baixa:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, db_formatar($dadoCadastral->q02_dtbaix, "d"), "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);


        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Logradouro:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(
            60,
            4,
            "{$dadoCadastral->j14_codigo} - {$dadoCadastral->j88_descricao} {$dadoCadastral->j14_nome}",
            "",
            0,
            "L",
            0
        );
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);
        //lado direito da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Número / Compl.:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$complemento}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Bairro:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->j13_descr}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado direito da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Cep:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, db_formatar($dadoCadastral->q02_cep, "cep"), "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        $this->pdf->MultiCell(30, 5, "", "", 0, "L", 1);

        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Forma de Localização:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->q167_descricao}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        $this->buildArea($dadoCadastral);

        $this->rotulo->label("q177_setorfiscal");
        global $LSq177_setorfiscal;
        //lado esquerdo da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "{$LSq177_setorfiscal}:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->j90_descr}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado direito da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Controle - Protocolo:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(
            60,
            4,
            "{$dadoCadastral->q14_proces} - {$dadoCadastral->p58_numero}/{$dadoCadastral->p58_ano} ",
            "",
            0,
            "L",
            0
        );
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);


        $zonaComDescricao = null;
        if (!empty($zona)) {
            $zonaComDescricao = "{$zona->q35_zona}-{$zona->j50_descr}";
        }
        //lado esquerdo da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Zona Fiscal:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, $zonaComDescricao, "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado direito da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Referência Anterior:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->q02_inscmu}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        // lado esquedo da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Tipo de Alvará:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->q98_descricao}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        // lado esquedo da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Porte:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->q40_descr}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        $escritorioComCgm = null;
        $escritorio = $this->getEscritorio();
        if (!empty($escritorio)) {
            $escritorioComCgm = $escritorio->cgm_esc . " - " . $escritorio->nome_esc;
        }

        //lado direito da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Contador:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "$escritorioComCgm", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);
    }

    private function buildAtividades($atividades = array())
    {
        $this->pdf->setX(5);
        $this->pdf->SetFont('Arial', 'B', 9);
        $this->pdf->Cell(200, 4, "Atividades", "LRBT", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);

        if (empty($atividades)) {
            $this->pdf->cell(190, 4, "NÃO POSSUI ATIVIDADE", 0, 1, "C", 0);
        } else {
            $this->pdf->setX(10);
            $this->pdf->SetFont('Arial', '', $this->titulo);
            $this->pdf->cell(15, 4, "Cod.", 0, 0, "C", 1);
            $this->pdf->cell(25, 4, "Atividade Interna", 0, 0, "C", 1);
            $this->pdf->cell(89, 4, "Atividade", 0, 0, "C", 1);
            $this->pdf->cell(6, 4, "Tipo", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Data Inicio", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Data Fim", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Data Baixa", 0, 1, "C", 1);

            foreach ($atividades as $atividade) {
                $atividade = (object)$atividade;
                $y = $this->pdf->y;
                $this->pdf->setX(10);
                $this->pdf->SetFont('Arial', '', $this->texto);
                $this->pdf->cell(15, 4, "{$atividade->q07_ativ}", 0, 0, "C", 0);
                $this->pdf->cell(25, 4, "{$atividade->q07_val_ativ_int}", 0, 0, "C", 0);
                $this->pdf->multicell(89, 4, "{$atividade->q03_descr}", 0, "L", 0);
                $ym = $this->pdf->y;
                $this->pdf->setY($y);
                $this->pdf->setX(140);
                $this->pdf->cell(6, 4, $atividade->q88_tipo, 0, 0, "L", 0);
                $this->pdf->cell(20, 4, db_formatar($atividade->q07_datain, "d"), 0, 0, "C", 0);
                $this->pdf->cell(20, 4, db_formatar($atividade->q07_datafi, "d"), 0, 0, "C", 0);
                $this->pdf->cell(20, 4, db_formatar($atividade->q07_databx, "d"), 0, 1, "C", 0);
                if (isset($atividade->q11_obs) && $atividade->q11_obs != "") {
                    $this->pdf->multicell(190, 4, "Observações da baixa - {$atividade->q11_obs}  ", 0, "L", "L", 0);
                    $ym = $this->pdf->y;
                }
                $this->pdf->setY($ym);
            }
        }
    }

    private function buildSocios($socios = array())
    {
        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->SetFont('Arial', 'B', $this->titulo);
        $this->pdf->Cell(200, 4, "Sócios / Responsável", "LRBT", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);

        if (empty($socios)) {
            $this->pdf->cell(190, 4, "NÃO POSSUI SOCIOS", 0, 1, "C", 0);
        } else {
            $this->pdf->setX(10);
            $this->pdf->SetFont('Arial', '', $this->titulo);
            $this->pdf->cell(10, 4, "CGM", 0, 0, "C", 1);
            $this->pdf->cell(65, 4, "Nome", 0, 0, "C", 1);
            $this->pdf->cell(65, 4, "Endereço", 0, 0, "C", 1);
            $this->pdf->cell(30, 4, "Município", 0, 0, "C", 1);
            $this->pdf->cell(24, 4, "Valor do Capital", 0, 1, "C", 1);

            foreach ($socios as $socio) {
                $socio = (object) $socio;
                $socio->q95_perc = db_formatar($socio->q95_perc, 'f');
                $this->pdf->setX(10);
                $this->pdf->SetFont('Arial', '', $this->texto);
                $this->pdf->cell(10, 4, "{$socio->z01_numcgm}", 0, 0, "C", 0);
                $this->pdf->cell(65, 4, "{$socio->z01_nome}", 0, 0, "L", 0);
                $this->pdf->cell(65, 4, "{$socio->z01_ender}", 0, 0, "L", 0);
                $this->pdf->cell(30, 4, "{$socio->z01_munic}", 0, 0, "C", 0);
                $this->pdf->cell(24, 4, "{$socio->q95_perc}", 0, 1, "C", 0);
            }
        }
    }

    private function buildArea($dadoCadastral)
    {
        $area = $this->getArea();
        //lado esquerdo da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Área:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$area->q30_area}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado direito da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Empregados:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$area->q30_quant}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);

        //lado esquerdo da tela
        $this->pdf->setX(120);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(30, 4, "Matrícula:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$dadoCadastral->q05_matric}", "", 1, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 1, "L", 0);

        //lado direito da tela
        $this->pdf->setX(10);
        $this->pdf->SetFont('Arial', '', $this->titulo);
        $this->pdf->Cell(34, 4, "Tempo Funcionamento:", "", 0, "L", 1);
        $this->pdf->SetFont('Arial', '', $this->texto);
        $this->pdf->Cell(60, 4, "{$area->q30_tempofuncionamento}", "", 0, "L", 0);
        $this->pdf->Cell(30, 1, "", "", 0, "R", 0);
        $this->pdf->Cell(60, 1, "", "", 0, "L", 0);
    }

    private function buildAidof($aidofs)
    {

        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->SetFont('Arial', 'B', $this->titulo);
        $this->pdf->Cell(200, 4, "Aidof", "LRBT", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);

        if (empty($aidofs)) {
            $this->pdf->cell(190, 4, "NÃO POSSUI AIDOF", 0, 1, "C", 0);
        } else {
            $this->pdf->setX(10);
            $this->pdf->SetFont('Arial', '', $this->titulo);
            $this->pdf->cell(10, 4, "Código", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Processo", 0, 0, "C", 1);
            $this->pdf->cell(30, 4, "Data Lançamento", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Nota Inicial", 0, 0, "C", 1);
            $this->pdf->cell(30, 4, "Quant. Solicitada", 0, 0, "C", 1);
            $this->pdf->cell(30, 4, "Quant. Liberada", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Nota Final", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Gráfica", 0, 0, "C", 1);
            $this->pdf->cell(10, 4, "Cancel.", 0, 1, "C", 1);

            foreach ($aidofs as $aidof) {
                $aidof = (object) $aidof;
                $this->pdf->setX(10);
                $this->pdf->SetFont('Arial', '', $this->texto);
                $p = 0;
                if ($aidof->y08_cancel == "t") {
                    $cancel = "Sim";
                } else {
                    $cancel = "Não";
                }
                $this->pdf->cell(10, 4, $aidof->y08_codigo, 0, 0, "C", $p);
                $this->pdf->cell(20, 4, $aidof->y02_codproc, 0, 0, "C", $p);
                $this->pdf->cell(30, 4, db_formatar($aidof->y08_dtlanc, "d"), 0, 0, "C", $p);
                $this->pdf->cell(20, 4, $aidof->y08_notain, 0, 0, "C", $p);
                $this->pdf->cell(30, 4, $aidof->y08_quantsol, 0, 0, "C", $p);
                $this->pdf->cell(30, 4, $aidof->y08_quantlib, 0, 0, "C", $p);
                $this->pdf->cell(20, 4, $aidof->y08_notafi, 0, 0, "C", $p);
                $this->pdf->cell(20, 4, $aidof->y08_numcgm, 0, 0, "C", $p);
                $this->pdf->cell(10, 4, $cancel, 0, 1, "C", $p);
            }
        }
    }

    private function buildSimplesNacional($simples = array())
    {

        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->SetFont('Arial', 'B', $this->titulo);
        $this->pdf->Cell(200, 4, "Optante Simples", "LRBT", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);

        if (empty($simples)) {
            $this->pdf->cell(190, 4, "Sem lançamentos", 0, 1, "C", 0);
        } else {
            $this->pdf->setX(10);
            $this->pdf->SetFont('Arial', '', $this->titulo);
            $this->pdf->cell(10, 4, "Código", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Data Inicial", 0, 0, "C", 1);
            $this->pdf->cell(30, 4, "Categoria", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Data da baixa", 0, 0, "C", 1);
            $this->pdf->cell(40, 4, "Motivo da baixa", 0, 0, "C", 1);
            $this->pdf->cell(70, 4, "Observações", 0, 1, "C", 1);

            foreach ($simples as $simplesValue) {
                $simplesValue = (object) $simplesValue;
                $this->pdf->setX(10);
                $this->pdf->SetFont('Arial', '', $this->texto);
                $p = 0;
                $this->pdf->cell(10, 4, $simplesValue->q38_sequencial, 0, 0, "C", $p);
                $this->pdf->cell(20, 4, db_formatar($simplesValue->q38_dtinicial, 'd'), 0, 0, "C", $p);
                $this->pdf->cell(30, 4, $simplesValue->q38_categoria, 0, 0, "C", $p);
                $this->pdf->cell(20, 4, db_formatar($simplesValue->q39_dtbaixa, 'd'), 0, 0, "C", $p);
                $this->pdf->cell(40, 4, $simplesValue->q42_descr, 0, 0, "C", $p);
                $this->pdf->cell(70, 4, $simplesValue->q39_obs, 0, 1, "C", $p);
            }
        }
    }

    private function buildMovimentacaoDoAlvara($movimentacoes)
    {

        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->SetFont('Arial', 'B', $this->titulo);
        $this->pdf->Cell(200, 4, "Movimentações Alvará", "LRBT", 1, "C", 0);
        $this->pdf->setX(5);
        $this->pdf->Cell(200, 2, "", "", 1, "C", 0);

        if (empty($movimentacoes)) {
            $this->pdf->cell(190, 4, "NÃO POSSUI MOVIMENTAÇÕES", 0, 1, "C", 0);
        } else {
            $this->pdf->setX(10);
            $this->pdf->SetFont('Arial', '', $this->titulo);
            $this->pdf->cell(30, 4, "Movimentação", 0, 0, "L", 1);
            $this->pdf->cell(16, 4, "Data", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Sitiação", 0, 0, "C", 1);
            $this->pdf->cell(20, 4, "Validade", 0, 0, "C", 1);
            $this->pdf->cell(30, 4, "Processo", 0, 0, "C", 1);
            $this->pdf->cell(30, 4, "Login", 0, 0, "C", 1);
            $this->pdf->cell(49, 4, "Observação", 0, 1, "L", 1);

            foreach ($movimentacoes as $movimentacao) {
                $movimentacao = (object) $movimentacao;
                $this->pdf->setX(10);
                $this->pdf->SetFont('Arial', '', $this->texto);
                $this->pdf->cell(30, 4, "{$movimentacao->dl_movimentacao}", 0, 0, "L", 0);
                $this->pdf->cell(16, 4, db_formatar($movimentacao->dl_data, "d"), 0, 0, "C", 0);
                $this->pdf->cell(20, 4, "{$movimentacao->dl_situacao}", 0, 0, "C", 0);
                $this->pdf->cell(20, 4, "{$movimentacao->dl_validade}", 0, 0, "C", 0);
                $this->pdf->cell(30, 4, "{$movimentacao->dl_processo}", 0, 0, "C", 0);
                $this->pdf->cell(30, 4, "{$movimentacao->dl_login}", 0, 0, "C", 0);
                $this->pdf->MultiCell(49, 4, "$movimentacao->q120_obs", 0, 1, "L", 0);
            }
        }
    }


    /**
     * @return array|false
     */
    private function getDadosCadastrais()
    {

        $sql = "select issbase.*,
                 cg.z01_nome,
                 cg.z01_nomecomple,
                 cg.z01_numero,
                 cg.z01_email,
                 cg.z01_telef,
                 cg.z01_cep,
                 cg.z01_telcel,
                 cg.z01_ident,
                 cg.z01_bairro,
                 cg.z01_munic,
                 cg.z01_compl,
                 cg.z01_numcgm,
                 cg.z01_ender,
                 cg.z01_incest,
                 c.z01_nome as escritorio,
                 cg.z01_nomefanta,
                 j14_nome,
                 j13_descr ,
                 q02_inscmu,
                 q02_numero,
                 q02_compl,
                 q02_obs,
                 q05_matric,
                 q14_proces,
                 cg.z01_cgccpf,
                 j88_descricao,
                 ruas.j14_codigo,
                 p58_numero,
                 p58_ano,
                 q40_descr,
                 q98_descricao,
                 q167_descricao,
                 j90_descr
          from issbase
                 inner join cgm cg on cg.z01_numcgm = q02_numcgm
                 left outer join issruas on issbase.q02_inscr = issruas.q02_inscr
                 left outer join ruas on ruas.j14_codigo = issruas.j14_codigo
                 left outer join issbairro on issbase.q02_inscr = q13_inscr
                 left outer join bairro on j13_codi = q13_bairro
                 left outer join escrito on issbase.q02_inscr = q10_inscr
                 left outer join cgm c on c.z01_numcgm = q10_numcgm
                 left outer join issmatric on issbase.q02_inscr = q05_inscr
                 left outer join issprocesso on issbase.q02_inscr = q14_inscr
                 left outer join ruastipo on j88_codigo = ruas.j14_tipo
                 left outer join protprocesso on p58_codproc = q14_proces
                 inner join issbaseporte on issbaseporte.q45_inscr = issbase.q02_inscr
                 inner join issporte on issporte.q40_codporte = issbaseporte.q45_codporte
                 inner join issalvara on issalvara.q123_inscr = issbase.q02_inscr
                 inner join isstipoalvara on isstipoalvara.q98_sequencial = issalvara.q123_isstipoalvara
                 left join issqn.formalocalvara on issbase.q02_formalocalvara = formalocalvara.q167_sequencial
                 left join isssetorfiscal on isssetorfiscal.q177_issbase = issbase.q02_inscr
                 left join setorfiscal on setorfiscal.j90_codigo = isssetorfiscal.q177_setorfiscal
          where issbase.q02_inscr = {$this->inscricao}
          order by issalvara.q123_sequencial desc limit 1 ";

        $rs = db_query($sql);
        if (!$rs) {
            return false;
        }
        return pg_fetch_all($rs);
    }

    /**
     * @return array|false
     */
    private function getAtividades()
    {
        $sql = "select
               q07_ativ,
               q07_val_ativ_int,
               q03_descr,
               q07_datain,
               q07_datafi,
               q07_databx,
               q07_quant,
               tabativbaixa.*,
			case when q88_inscr is null then 'S'::char(1) else 'P'::char(1) end as q88_tipo,
               q11_processo,
			case when q11_oficio = 'true' then 'NORMAL'
				when q11_oficio = 'false' then 'OFICIO'
			     else ''
               end as q11_oficio
        from tabativ
                     inner join ativid on q07_ativ = q03_ativ
                     left join ativprinc on ativprinc.q88_inscr = tabativ.q07_inscr
                            and ativprinc.q88_seq = tabativ.q07_seq
                     left join tabativbaixa on tabativ.q07_inscr = tabativbaixa.q11_inscr
                            and tabativ.q07_seq = tabativbaixa.q11_seq
        where q07_inscr = {$this->inscricao}
        order by case when q88_inscr is null then 2 else 1 end, q07_datain, q07_datafi
        ";

        $rs = db_query($sql);
        if (!$rs) {
            return false;
        }
        return pg_fetch_all($rs);
    }

    /**
     * @return false|object
     */
    private function getArea()
    {
        $rs = $this->daoIssQuant->sql_record(
            $this->daoIssQuant->sql_query_file(
                null,
                $this->inscricao,
                "q30_area,q30_quant,q30_tempofuncionamento,q30_areapublicidade",
                null,
                "q30_inscr = {$this->inscricao} and q30_anousu = " . db_getsession('DB_anousu')
            )
        );

        if (!$rs) {
            return false;
        }
        return pg_fetch_object($rs);
    }

    /**
     * @return false|object
     */
    private function getSocios()
    {

        $sql = "select
                    cgmsocio.z01_numcgm,
                    cgmsocio.z01_nome,
                    cgmsocio.z01_ender,
                    cgmsocio.z01_munic,
                    q95_perc
            from issbase
            inner join socios on q95_cgmpri = q02_numcgm
            inner join cgm cgmsocio on cgmsocio.z01_numcgm = q95_numcgm
            inner join cgm cgmempresa on cgmempresa.z01_numcgm = q02_numcgm
            where q95_tipo in (1,2,3)
              and q02_inscr = {$this->inscricao}";

        $rs = db_query($sql);
        if (!$rs) {
            return false;
        }
        return pg_fetch_all($rs);
    }

    private function getAidof()
    {
        $sql = "select * from aidof left join aidofproc on y02_aidof = y08_codigo where y08_inscr = {$this->inscricao}";
        $rs = db_query($sql);
        if (!$rs) {
            return false;
        }
        return pg_fetch_all($rs);
    }

    private function getMovimentacaoDoAlvara()
    {
        $sql = "select
                    q120_sequencial,
                    q121_descr as dl_Movimentacao,
                    q120_dtmov as dl_data,
                    case
                    when q123_situacao = 1
                    then  'Ativo'
                    else 'Inativo'
                    end as dl_situacao,
                    q120_validadealvara ||' Dias' as dl_validade,
                    q124_codproc as dl_processo,
                    login as dl_Login,
                    q120_obs
               from issmovalvara
                   inner join isstipomovalvara on isstipomovalvara.q121_sequencial = issmovalvara.q120_isstipomovalvara
                   inner join issalvara on issalvara.q123_sequencial = issmovalvara.q120_issalvara
                   inner join issbase on issbase.q02_inscr = issalvara.q123_inscr
                   inner join isstipoalvara on isstipoalvara.q98_sequencial = issalvara.q123_isstipoalvara
                inner join db_usuarios on id_usuario = q120_usuario
                 left join issmovalvaraprocesso on q124_issmovalvara = q120_sequencial
                   where q123_inscr = {$this->inscricao}
                  order by q120_sequencial desc";

        $rs = db_query($sql);
        if (!$rs) {
            return false;
        }
        return pg_fetch_all($rs);
    }

    private function getZona()
    {
        $sql = "select * from isszona inner join zonas on j50_zona = q35_zona where q35_inscr = {$this->inscricao}";
        $rs = db_query($sql);
        if (!$rs) {
            return false;
        }
        return pg_fetch_object($rs);
    }

    private function getEscritorio()
    {
        $rs = $this->daoEscrito->sql_record(
            $this->daoEscrito->sql_query(
                null,
                "q10_numcgm as cgm_esc,a.z01_nome as nome_esc",
                "q10_sequencial DESC",
                "q10_inscr = {$this->inscricao} AND q10_dtfim IS NULL"
            )
        );

        if (!$rs) {
            return false;
        }
        return pg_fetch_object($rs);
    }

    private function getSimplesNacional()
    {
        $sql = "SELECT
               isscadsimples.q38_sequencial,
               isscadsimples.q38_dtinicial,
               CASE
                 WHEN isscadsimples.q38_categoria = 1 THEN 'Micro Empresa'
                 WHEN isscadsimples.q38_categoria = 2 THEN 'Empresa de pequeno porte'
                 WHEN isscadsimples.q38_categoria = 3 THEN 'MEI'
                 WHEN isscadsimples.q38_categoria = 4 THEN 'EIRELI'
                 WHEN isscadsimples.q38_categoria = 5 THEN 'Soc. Profissionais'
               END AS q38_categoria,
               isscadsimplesbaixa.q39_dtbaixa,
               isscadsimplesbaixa.q39_issmotivobaixa,
               isscadsimplesbaixa.q39_obs,
               issmotivobaixa.q42_descr
          FROM isscadsimples
               LEFT JOIN isscadsimplesbaixa ON isscadsimples.q38_sequencial  = isscadsimplesbaixa.q39_isscadsimples
               LEFT JOIN issmotivobaixa     ON issmotivobaixa.q42_sequencial = isscadsimplesbaixa.q39_issmotivobaixa
         WHERE isscadsimples.q38_inscr = {$this->inscricao}";

        $rs = db_query($sql);
        if (!$rs) {
            return false;
        }
        return pg_fetch_all($rs);
    }
}
