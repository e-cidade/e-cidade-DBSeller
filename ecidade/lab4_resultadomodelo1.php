<?php
use ECidade\Saude\Laboratorio\Repository\Parametros;
$oPdf = new ECidade\Pdf\Pdf();
$oPdf->init(false, false, false);
$oPdf->AliasNbPages();
$oPdf->SetTopMargin(1);
$oPdf->SetAutoPageBreak(false, 5);

$calculoPdf = new ECidade\Pdf\Pdf();
$calculoPdf->init(false, false, false);
$calculoPdf->AliasNbPages();
$calculoPdf->SetTopMargin(1);
$calculoPdf->SetAutoPageBreak(false, 5);

$oDadosEstrutura->sNome = "Resultado({$oDadosEstrutura->oSolicitante->iCodigo})" . $oDadosEstrutura->oSolicitante->iCodigo . "_";
$oDadosEstrutura->sNome .= date("d-m-Y", db_getsession("DB_datausu")) . ".pdf";

$parametrosRepository = Parametros::getInstancia();
$parametros = $parametrosRepository->buscar();

$tamanhoFonte = $parametros->getTamanhoFonte();
$espacoLinha = $parametros->getEspacoLinhas();

$oDadosEstrutura->tamanhoFontePadrao = $tamanhoFonte;
$oDadosEstrutura->espacoLinhaPadrao = $espacoLinha;

/**
 * Percorre os setores
 */
foreach ($oDadosEstrutura->aSetor as $iSetor => $oSetor) {
    $oDadosEstrutura->iSetor = $iSetor;
    $oDadosEstrutura->aExames = $oSetor->aExames;

    montaCabecalho($oPdf, $oDadosEstrutura);
    $oPdf->SetY(50);
    $oPdf->Cell(190, $oDadosEstrutura->iAlturaPadrao, $oSetor->sDescricao, 'B', 1, "L");
    $oPdf->SetFont('courier', "B", 10);
    atributosExame($oPdf, $oDadosEstrutura, $oSetor->sDescricao, $calculoPdf);
    rodape($oPdf, $oDadosEstrutura);
}

/**
 * ALTERAÇÕES FEITAS NO CONTEÚDO QUE SERÁ EXIBIDO NO PDF DEVEM ESTAR DE ACORDO COM A FUNÇÃO exameAtributosNovaPagina()
 * PARA QUE A QUEBRA CONTINUE CORRETA.
 *
 * Monta o corpo do relatório com a estrutura dos atributos
 * @param \ECidade\Pdf\Pdf $oPdf
 * @param $oDadosEstrutura
 */

function atributosExame(\ECidade\Pdf\Pdf $oPdf, $oDadosEstrutura, $setor, $calculoPdf)
{
    
    $tamanhoFonte = $oDadosEstrutura->tamanhoFontePadrao;
    $espacoLinha = $oDadosEstrutura->espacoLinhaPadrao;
    
    $oDadosEstrutura->lPrimeiroRegistro = true;
    $primeiroExame = true;
    $nomeSetor = '';
    $mesmoSetor = false;

    /**
     * Array com a posição do X a ser setada, de acordo com o nível do atributo
     */
    $aPosicaoAtributos = array();
    $aPosicaoAtributos[1] = 12;
    $aPosicaoAtributos[2] = 14;
    $aPosicaoAtributos[3] = 16;
    $aPosicaoAtributos[4] = 18;
    $aPosicaoAtributos[5] = 20;

    /**
     * Percorre os atributos a serem impressos
     */
    foreach ($oDadosEstrutura->aExames as $oDadosExame) {        
        if (!$mesmoSetor) {
            montaCabecalho($calculoPdf, $oDadosEstrutura);
            $calculoPdf->SetY(50);
            $calculoPdf->Cell(190, $oDadosEstrutura->iAlturaPadrao, 'BIOQUIMICA', 'B', 1, "L");
            $calculoPdf->SetFont('arial', "B", 10);
            $nomeSetor = $setor;
        }
        $mesmoSetor = $nomeSetor == $setor;

        $exameNovaPagina = exameAtributosNovaPagina(
            $calculoPdf,
            $oDadosExame,
            $oDadosEstrutura,
            $aPosicaoAtributos,
            $primeiroExame,
            $oDadosEstrutura->lPrimeiroRegistro
        );
        $primeiroExame = false;

        foreach ($oDadosExame->aAtributos as $key => $oAtributos) {
            if (!empty($exameNovaPagina['exame']) && $oDadosExame->sNomeExame === $exameNovaPagina['exame'] && $key === $exameNovaPagina['atributo']) {
                rodape($oPdf, $oDadosEstrutura);
                montaCabecalho($oPdf, $oDadosEstrutura);
                $oPdf->SetY(50);
            }

            if (!empty($exameNovaPagina)
                && !empty($exameNovaPagina['novaQuebra'])
                && $oDadosExame->sNomeExame === $exameNovaPagina['novaQuebra']['exame']
                && $key === $exameNovaPagina['novaQuebra']['atributo']
                && $exameNovaPagina['atributo'] !== $exameNovaPagina['novaQuebra']['atributo']) {
                rodape($oPdf, $oDadosEstrutura);
                montaCabecalho($oPdf, $oDadosEstrutura);
                $oPdf->SetY(50);
            }

            $sNegrito = $oAtributos->tipo == 1 ? "b" : "";
            $oDadosEstrutura->iAlturaPadrao = $oAtributos->tipo == 1 ? 5 : 3.5;

            /**
             * Caso seja o primeiro registro da página irá imprimir a seguinte célula
             */
            if ($oDadosEstrutura->lPrimeiroRegistro) {
                $iPosicaoY = $oPdf->GetY();
                $oDadosEstrutura->lPrimeiroRegistro = false;
                $oPdf->SetXY(136, $iPosicaoY);
                $oPdf->SetFont('courier', "B", 10);
                $oPdf->Cell(
                    $oDadosEstrutura->iLarguraPadrao,
                    $oDadosEstrutura->iAlturaPadrao,
                    "Valores de Referência",
                    0,
                    1,
                    "L"
                );

                if ($oAtributos->nivel == 1) {
                    $oPdf->SetY($iPosicaoY);
                }
            }

            $oPdf->SetFont('courier', $sNegrito, 10);
            $oPdf->SetX($aPosicaoAtributos[$oAtributos->nivel]);

            $iAlturaLinhaPadrao = $oDadosEstrutura->iAlturaPadrao;
            $iNumeroLinhasOcupadas = 1;

            $iColunaNome = 70;
            $iColunaVlrPercent = 20;
            $iColunaVlrAbsoluto = 40;
            $iColunaReferencia = 55;

            if ($oPdf->NbLines($iColunaNome, $oAtributos->nome) > $iNumeroLinhasOcupadas) {
                $iNumeroLinhasOcupadas = $oPdf->NbLines($iColunaNome, $oAtributos->nome);
            }
            if ($oPdf->NbLines($iColunaVlrPercent, $oAtributos->valorpercentual) > $iNumeroLinhasOcupadas) {
                $iNumeroLinhasOcupadas = $oPdf->NbLines($iColunaVlrPercent, $oAtributos->valorpercentual);
            }
            if ($oPdf->NbLines($iColunaVlrAbsoluto, $oAtributos->valorabsoluto) > $iNumeroLinhasOcupadas) {
                $iNumeroLinhasOcupadas = $oPdf->NbLines($iColunaVlrAbsoluto, $oAtributos->valorabsoluto);
            }
            if ($oPdf->NbLines($iColunaReferencia, $oAtributos->referencia) > $iNumeroLinhasOcupadas) {
                $iNumeroLinhasOcupadas = $oPdf->NbLines($iColunaReferencia, $oAtributos->referencia);
            }

            $iYInicio = $oPdf->GetY();
            $iXInicio = $oPdf->GetX();

            $iAlturaLinhaUsada = $iAlturaLinhaPadrao;
            $oPdf->SetXY($iXInicio, $iYInicio);

            /**
             * Verifica se o tipo de atributo é diferente de 1 e adiciona : após o nome
             */
            if ($oAtributos->tipo != 1) {
                $oAtributos->nome .= ':';
            }

            $oPdf->Cell(
                $iColunaNome,
                $iAlturaLinhaUsada,
                completaCelula($oPdf, $iColunaNome, $oAtributos->nome),
                0,
                0,
                'L'
            );
            $iXInicio += $iColunaNome;
            $oPdf->SetXY($iXInicio, $iYInicio);

            $sValorPercentual = $oAtributos->valorpercentual;
            if (!empty($oAtributos->valorpercentual)) {
                $sValorPercentual = "{$oAtributos->valorpercentual} %";
            }
            $oPdf->MultiCell($iColunaVlrPercent, $iAlturaLinhaUsada, $sValorPercentual, 0, 'L');

            $iXInicio += $iColunaVlrPercent;
            $oPdf->SetXY($iXInicio, $iYInicio);
            $oPdf->MultiCell($iColunaVlrAbsoluto, $iAlturaLinhaUsada, $oAtributos->valorabsoluto, 0, 'L');
            $iXInicio += $iColunaVlrAbsoluto;
            $oPdf->SetXY($iXInicio, $iYInicio);
            $oPdf->MultiCell($iColunaReferencia, $iAlturaLinhaUsada, $oAtributos->referencia, 0, 'L');

            $oPdf->SetY($iYInicio + ($iAlturaLinhaPadrao * $iNumeroLinhasOcupadas));
            $oPdf->Ln($espacoLinha);                         
        }

        $oPdf->SetFont('courier', '', $tamanhoFonte);
        $oPdf->Ln();

        /**
         * Lista os medicamentos do exame
         */
        if (!empty($oDadosExame->aMedicamentosExame)) {
            $oPdf->SetFont('courier', 'b', $tamanhoFonte);
            $aMedicamentos = array();

            foreach ($oDadosExame->aMedicamentosExame as $oMedicamento) {
                $aMedicamentos[] = $oMedicamento->getNome();
            }

            $sMedicamentos = implode(', ', $aMedicamentos);
            $iLinhasMedicamentos = $oPdf->NbLines($oDadosEstrutura->iLarguraPadrao, $sMedicamentos);
            $iYinicio = $oPdf->GetY();

            $oPdf->SetFont('courier', 'b', $tamanhoFonte);
            $oPdf->Cell($oDadosEstrutura->iLarguraPadrao, 4, "Medicamentos:", 0, 1, "L");
            $oPdf->SetFont('courier', '', $tamanhoFonte);
            $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $sMedicamentos, 0, 'J');
        }

        if (!empty($oDadosExame->sObservacao)) {
            $oPdf->SetFont('courier', 'b', $tamanhoFonte);
            $sObservacao = $oDadosExame->sObservacao;
            $iLinhasObservacao = $oPdf->NbLines($oDadosEstrutura->iLarguraPadrao, $sObservacao);
            $iYinicio = $oPdf->GetY();

            $oPdf->SetFont('courier', 'b', $tamanhoFonte);
            $oPdf->Cell($oDadosEstrutura->iLarguraPadrao, 4, "Observações do Resultado:", 0, 1, "L");
            $oPdf->SetFont('courier', '', $tamanhoFonte);
            $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $sObservacao, 0, 'J');
        }

        $oPdf->Ln(2);

        $iLinhasMaterialColeta = count($oDadosExame->aDadosMaterialColeta);
        $iYinicio = $oPdf->GetY();

        foreach ($oDadosExame->aDadosMaterialColeta as $oMaterialColeta) {
            
            $oPdf->SetFont('courier', 'b', $tamanhoFonte);
            $oPdf->Cell(20, 4, 'Material:', 0, 0, "L");                 
            $oPdf->SetFont('courier', '', $tamanhoFonte);
            $oPdf->Cell(80, 4, $oMaterialColeta->material_coleta, 0, 0, "L");
            $oPdf->SetFont('courier', 'b', $tamanhoFonte);
            $oPdf->Cell(15, 4, 'Método:', 0, 0, "L");
            $oPdf->SetFont('courier', '', $tamanhoFonte);
            $oPdf->Cell(80, 4, $oMaterialColeta->metodo_coleta, 0, 1, "L");
        }

        if (!empty($oDadosExame->sObservacaoExame)) {
            $oPdf->SetFont('courier', 'b', $tamanhoFonte);
            $sObservacaoExame = $oDadosExame->sObservacaoExame;
            $iLinhasObservacaoExame = $oPdf->NbLines($oDadosEstrutura->iLarguraPadrao, $sObservacaoExame);
            $iYinicio = $oPdf->GetY();

            $oPdf->SetFont('courier', 'b', $tamanhoFonte);
            $oPdf->Cell($oDadosEstrutura->iLarguraPadrao, 4, "Observações do Exame:", 0, 1, "L");
            $oPdf->SetFont('courier', '', $tamanhoFonte);
            $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $sObservacaoExame, 0, 'J');
        }

        if ($oDadosEstrutura->mostrarConferenciaPorExame === true) {
            $oPdf->SetFont('courier', 'b', 8);
            $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $oDadosExame->mensagemLiberacao, 0, "L");
        }

        if ($oDadosExame->exibeHistoricoResultados === true) {
            if (!empty($exameNovaPagina['historicoNovaFolha'])) {
                rodape($oPdf, $oDadosEstrutura);
                montaCabecalho($oPdf, $oDadosEstrutura);
                $oPdf->SetY(50);
            }
            exibeHistoricoResultados($oPdf, $oDadosExame);
        }

        $oPdf->Ln();
        $oPdf->Ln();
    }
}

function montaCabecalho($oPdf, $oDadosEstrutura)
{
    $oPdf->AddPage();

    try {
        $oDepartamento = new DBDepartamento($oDadosEstrutura->iDepartamento);
        $oInstituicao = $oDepartamento->getInstituicao()->getDadosPrefeitura();

        if ($oInstituicao->getImagemLogo() != "" && file_exists('imagens/files/' . $oInstituicao->getImagemLogo())) {
            $oPdf->Image('imagens/files/' . $oInstituicao->getImagemLogo(), 7, 7, 20);
        }

        $oPdf->SetFont("arial", "B", 8);
        $oPdf->Text(33, 9, $oDepartamento->getNomeDepartamento());
        $oPdf->Text(33, 14, substr($oInstituicao->getDescricao(), 0, 42));
        $oPdf->SetFont("arial", "", 8);

        $sEndereco = $oInstituicao->getLogradouro();
        $sEndereco .= ", " . $oInstituicao->getNumero();

        if ($oInstituicao->getComplemento() != "") {
            $sEndereco .= ", " . $oInstituicao->getComplemento();
        }

        $oPdf->Text(33, 19, $sEndereco);

        $sMunicipio = $oInstituicao->getMunicipio();
        $sMunicipio .= " - " . $oInstituicao->getUf();
        $oPdf->Text(33, 23, $sMunicipio);

        $sTelefoneCnpj = $oInstituicao->getTelefone();

        if ($oInstituicao->getCNPJ() != "") {
            $sTelefoneCnpj .= " - CNPJ: " . $oInstituicao->getCNPJ();
        }

        $oPdf->Text(33, 27, $sTelefoneCnpj);
        $oPdf->Text(33, 31, substr($oInstituicao->getEmail(), 0, 48));
        $oPdf->Text(33, 35, substr($oInstituicao->getSite(), 0, 50));


        $aSexo = array();
        $aSexo["M"] = "MASCULINO";
        $aSexo["F"] = "FEMININO";

        $iLarguraLabel = 16;
        $iLarguraDescricao = 63;

        $oPdf->SetFillColor(240);
        $oPdf->RoundedRect(120, 6, 83, 39, 2, 'DF', '123');

        $oPdf->SetY(8);

        $oPdf->setX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraDescricao + $iLarguraLabel, 4, "DADOS DO PACIENTE", 0, 1, "C");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Requisição:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->Cell($iLarguraDescricao, 4, $oDadosEstrutura->iRequisicao, 0, 1, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Paciente:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->MultiCell($iLarguraDescricao, 4, $oDadosEstrutura->oSolicitante->sNome, 0, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Idade:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->Cell($iLarguraDescricao, 4, $oDadosEstrutura->oSolicitante->idadeCompleta, 0, 1, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Sexo:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->Cell($iLarguraDescricao, 4, $aSexo[$oDadosEstrutura->oSolicitante->sSexo], 0, 1, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Médico:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->MultiCell($iLarguraDescricao, 4, $oDadosEstrutura->oSolicitante->sMedico, 0, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Convênio:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->Cell($iLarguraDescricao, 4, "SUS", 0, 1, "L");

        $oPdf->Line(10, 45, 200, 45);
    } catch (Exception $oErro) {
        db_msgbox($oErro->getMessage());
        db_redireciona("lab4_emissaoresult001.php");
    }
}

/**
 * Função para verificar se a impressão das informações relacionadas para cada exame, irá caber no espaço
 * disponível. Caso não caiba retorna o exame e a chave do respectivo atributo onde a soma das alturas foi superior
 * ao espaço disponível.
 *
 * @param \ECidade\Pdf\Pdf $oPdf
 * @param $oDadosExame
 * @param $oDadosEstrutura
 * @param $nomeSetor
 * @return array
 */
function exameAtributosNovaPagina(
    $oPdf,
    $oDadosExame,
    $oDadosEstrutura,
    $aPosicaoAtributos,
    $primeiroExame,
    $primeiroRegistro
) {
    $tamanhoFonte = $oDadosEstrutura->tamanhoFontePadrao;
    $proximaPagina = [];
    $alturaLimite = 27;
    $alturaInicioAtributo = 0;
    $espacoSegundaQuebra = 0;
    $alturaLimiteNovaQuebra = 237 - 27;

    $quebrouNoAtributo = false;
    $teveQuebra = false;
    $teveNovaQuebra = false;

    /***
     * Irá armazenar o equivalente ao espaço ocupado pelo reespctivo exame.
     */
    $espacoJaOcupado = 0;

    /***
     * Quando um conteúdo precisa ir para nova página, essa variável armazena o espaço que esse conteúdo ocupa
     * para que seja descontado do espaço disponível no laço seguinte.
     */
    static $espacoJaOcupadoParaValidacao = 0;

    /***
     * valida o campo Exibir Liberação por Exame, caso seja false a altura limite irá mudar. Quanto maior seu valor, menos informações
     * serão impressas na página.
     */
    if (!$oDadosEstrutura->mostrarConferenciaPorExame) {
        $alturaLimite = 40;
        $alturaLimiteNovaQuebra = 237 - 40;

        if ($oDadosExame->exibeHistoricoResultados === true) {
            $alturaLimite = 67;
            $alturaLimiteNovaQuebra = 237 - 67;
        }
    }

    /***
     * Valida se é o primeiro exame de cada setor, referente a requisição escolhida.
     */
    if ($primeiroExame) {
        $espacoJaOcupadoParaValidacao = 0;
    }

    $alturaInicioFolha = 237;

    if (!$primeiroExame) {
        $alturaInicioFolha = $oPdf->getAvailableHeight();
    }

    foreach ($oDadosExame->aAtributos as $key => $oAtributos) {
        $alturaInicioAtributo = $oPdf->getAvailableHeight();

        $sNegrito = $oAtributos->tipo == 1 ? "b" : "";
        $oDadosEstrutura->iAlturaPadrao = $oAtributos->tipo == 1 ? 5 : 3.5;

        /**
         * Caso seja o primeiro registro da página irá imprimir a seguinte célula
         */
        if ($primeiroRegistro) {
            $iPosicaoY = $oPdf->GetY();
            $primeiroRegistro = false;
            $oPdf->SetXY(136, $iPosicaoY);
            $oPdf->SetFont('courier', "B", 10);
            $oPdf->Cell(
                $oDadosEstrutura->iLarguraPadrao,
                $oDadosEstrutura->iAlturaPadrao,
                "Valores de Referência",
                0,
                1,
                "L"
            );

            if ($oAtributos->nivel == 1) {
                $oPdf->SetY($iPosicaoY);
            }
        }

        $oPdf->SetFont('courier', $sNegrito, 10);
        $oPdf->SetX($aPosicaoAtributos[$oAtributos->nivel]);

        $iAlturaLinhaPadrao = $oDadosEstrutura->iAlturaPadrao;
        $iNumeroLinhasOcupadas = 1;

        $iColunaNome = 70;
        $iColunaVlrPercent = 20;
        $iColunaVlrAbsoluto = 40;
        $iColunaReferencia = 55;

        if ($oPdf->NbLines($iColunaNome, $oAtributos->nome) > $iNumeroLinhasOcupadas) {
            $iNumeroLinhasOcupadas = $oPdf->NbLines($iColunaNome, $oAtributos->nome);
        }
        if ($oPdf->NbLines($iColunaVlrPercent, $oAtributos->valorpercentual) > $iNumeroLinhasOcupadas) {
            $iNumeroLinhasOcupadas = $oPdf->NbLines($iColunaVlrPercent, $oAtributos->valorpercentual);
        }
        if ($oPdf->NbLines($iColunaVlrAbsoluto, $oAtributos->valorabsoluto) > $iNumeroLinhasOcupadas) {
            $iNumeroLinhasOcupadas = $oPdf->NbLines($iColunaVlrAbsoluto, $oAtributos->valorabsoluto);
        }
        if ($oPdf->NbLines($iColunaReferencia, $oAtributos->referencia) > $iNumeroLinhasOcupadas) {
            $iNumeroLinhasOcupadas = $oPdf->NbLines($iColunaReferencia, $oAtributos->referencia);
        }

        $iYInicio = $oPdf->GetY();
        $iXInicio = $oPdf->GetX();

        $iAlturaLinhaUsada = $iAlturaLinhaPadrao;
        $oPdf->SetXY($iXInicio, $iYInicio);

        $oPdf->Cell(
            $iColunaNome,
            $iAlturaLinhaUsada,
            completaCelula($oPdf, $iColunaNome, $oAtributos->nome),
            0,
            0,
            'L'
        );
        $iXInicio += $iColunaNome;
        $oPdf->SetXY($iXInicio, $iYInicio);

        $sValorPercentual = $oAtributos->valorpercentual;
        if (!empty($oAtributos->valorpercentual)) {
            $sValorPercentual = "{$oAtributos->valorpercentual} %";
        }
        $oPdf->MultiCell($iColunaVlrPercent, $iAlturaLinhaUsada, $sValorPercentual, 0, 'L');

        $iXInicio += $iColunaVlrPercent;
        $oPdf->SetXY($iXInicio, $iYInicio);
        $oPdf->MultiCell($iColunaVlrAbsoluto, $iAlturaLinhaUsada, $oAtributos->valorabsoluto, 0, 'L');
        $iXInicio += $iColunaVlrAbsoluto;
        $oPdf->SetXY($iXInicio, $iYInicio);
        $oPdf->MultiCell($iColunaReferencia, $iAlturaLinhaUsada, $oAtributos->referencia, 0, 'L');

        $oPdf->SetY($iYInicio + ($iAlturaLinhaPadrao * $iNumeroLinhasOcupadas));

        $alturaDisponivel = $oPdf->getAvailableHeight() - $espacoJaOcupadoParaValidacao;

        /***
         * Caso a altura disponível seja menor ou igual a altura limite de impressão, irá retornar em qual exame e atributo ocupou
         * espaço a mais.
         */
        if ($alturaDisponivel <= $alturaLimite && !$quebrouNoAtributo) {
            $proximaPagina['exame'] = $oDadosExame->sNomeExame;
            $proximaPagina['atributo'] = $key;
            $teveQuebra = true;

            /***
             * Caso não seja o primeiro exame do setor e precise quebrar, deve mandar para nova folha o exame inteiro ao
             * invés de quebrar no respectivo atributo do mesmo
             */
            if (!$primeiroExame) {
                $proximaPagina['atributo'] = 0;
            }

            /***
             * Caso a quebra ocorra em um atributo que não seja o primeiro, irá começar a contar as alturas
             * para descontar no próximo laço, a partir desse atributo
             */
            if ($proximaPagina['atributo'] != 0) {
                $alturaInicioFolha = $alturaInicioAtributo;
            }

            $quebrouNoAtributo = true;
        }

        $espacoJaOcupado = $alturaInicioFolha - ($oPdf->getAvailableHeight());

        /***
         * Verifica se a parte do exame ou exame inteiro que precisa ir para nova folha, por falta de espaço ocupará mais
         * que uma folha de espaço ou não. Caso ocorra uma nova quebra será feita.
         */
        if ($teveQuebra && $espacoJaOcupado >= $alturaLimiteNovaQuebra && !$teveNovaQuebra) {
            $teveNovaQuebra = true;
            $proximaPagina['novaQuebra']['exame'] = $oDadosExame->sNomeExame;
            $proximaPagina['novaQuebra']['atributo'] = $key;
        }
    }

    $oPdf->SetFont('courier', '', $tamanhoFonte);
    $oPdf->Ln();

    /**
     * Lista os medicamentos do exame
     */
    if (!empty($oDadosExame->aMedicamentosExame)) {
        $oPdf->SetFont('courier', 'b', $tamanhoFonte);
        $aMedicamentos = array();

        foreach ($oDadosExame->aMedicamentosExame as $oMedicamento) {
            $aMedicamentos[] = $oMedicamento->getNome();
        }

        $sMedicamentos = implode(', ', $aMedicamentos);
        $iLinhasMedicamentos = $oPdf->NbLines($oDadosEstrutura->iLarguraPadrao, $sMedicamentos);
        $iYinicio = $oPdf->GetY();

        $oPdf->SetFont('courier', 'b', $tamanhoFonte);
        $oPdf->Cell($oDadosEstrutura->iLarguraPadrao, 4, "Medicamentos:", 0, 1, "L");
        $oPdf->SetFont('courier', '', $tamanhoFonte);
        $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $sMedicamentos, 0, 'J');
    }

    if (!empty($oDadosExame->sObservacao)) {
        $oPdf->SetFont('courier', 'b', $tamanhoFonte);
        $sObservacao = $oDadosExame->sObservacao;
        $iLinhasObservacao = $oPdf->NbLines($oDadosEstrutura->iLarguraPadrao, $sObservacao);
        $iYinicio = $oPdf->GetY();

        $oPdf->SetFont('courier', 'b', $tamanhoFonte);
        $oPdf->Cell($oDadosEstrutura->iLarguraPadrao, 4, "Observações do Resultado:", 0, 1, "L");
        $oPdf->SetFont('courier', '', $tamanhoFonte);
        $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $sObservacao, 0, 'J');
    }

    $oPdf->Ln(2);

    $iLinhasMaterialColeta = count($oDadosExame->aDadosMaterialColeta);
    $iYinicio = $oPdf->GetY();

    foreach ($oDadosExame->aDadosMaterialColeta as $oMaterialColeta) {
        $oPdf->SetFont('courier', 'b', $tamanhoFonte);
        $oPdf->Cell(20, 4, 'Material:', 0, 0, "L");
        $oPdf->SetFont('courier', '', $tamanhoFonte);
        $oPdf->Cell(80, 4, $oMaterialColeta->material_coleta, 0, 0, "L");
        $oPdf->SetFont('courier', 'b', $tamanhoFonte);
        $oPdf->Cell(15, 4, 'Método:', 0, 0, "L");
        $oPdf->SetFont('courier', '', $tamanhoFonte);
        $oPdf->Cell(80, 4, $oMaterialColeta->metodo_coleta, 0, 1, "L");
    }

    if (!empty($oDadosExame->sObservacaoExame)) {
        $oPdf->SetFont('courier', 'b', $tamanhoFonte);
        $sObservacaoExame = $oDadosExame->sObservacaoExame;
        $iLinhasObservacaoExame = $oPdf->NbLines($oDadosEstrutura->iLarguraPadrao, $sObservacaoExame);
        $iYinicio = $oPdf->GetY();

        $oPdf->SetFont('courier', 'b', $tamanhoFonte);
        $oPdf->Cell($oDadosEstrutura->iLarguraPadrao, 4, "Observações do Exame:", 0, 1, "L");
        $oPdf->SetFont('courier', '', $tamanhoFonte);
        $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $sObservacaoExame, 0, 'J');
    }

    if ($oDadosEstrutura->mostrarConferenciaPorExame === true) {
        $oPdf->SetFont('courier', 'b', 8);
        $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $oDadosExame->mensagemLiberacao, 0, "L");
    }

    $oPdf->Ln();
    $oPdf->Ln();

    $espacoJaOcupado = $alturaInicioFolha - ($oPdf->getAvailableHeight());

    $alturaDisponivel = $oPdf->getAvailableHeight() - $espacoJaOcupadoParaValidacao;

    /***
     * Caso a altura disponível seja menor que a altura limite, irá retornar em qual exame e atributo ocupou espaço a mais
     */
    if ($alturaDisponivel <= $alturaLimite && !$quebrouNoAtributo) { // talvez validar altura a cada parte do final, medicamento, observação e n só aqui
        $proximaPagina['exame'] = $oDadosExame->sNomeExame;
        $proximaPagina['atributo'] = $key;
        $teveQuebra = true;

        if (!$primeiroExame) {
            $proximaPagina['atributo'] = 0;
        }
    }

    if ($teveQuebra && $espacoJaOcupado >= $alturaLimiteNovaQuebra && !$teveNovaQuebra) {
        $teveNovaQuebra = true;
        $proximaPagina['novaQuebra']['exame'] = $oDadosExame->sNomeExame;
        $proximaPagina['novaQuebra']['atributo'] = $key;
    }

    if ($oDadosExame->exibeHistoricoResultados === true) {
        exibeHistoricoResultados($oPdf, $oDadosExame);
        $espacoJaOcupado = $alturaInicioFolha - ($oPdf->getAvailableHeight());

        if (!$teveQuebra) {
            $alturaDisponivel = $oPdf->getAvailableHeight() - $espacoJaOcupadoParaValidacao;

            if ($alturaDisponivel <= $alturaLimite &&
                !$quebrouNoAtributo &&
                $espacoJaOcupado >= $alturaLimiteNovaQuebra) {
                $proximaPagina['historicoNovaFolha'] = true;
            }
        }
    }

    if (!empty($proximaPagina)) {
        $espacoJaOcupadoParaValidacao = $espacoJaOcupado;

        if ($teveNovaQuebra) {
            $espacoSegundaQuebra = $espacoJaOcupado - $alturaLimiteNovaQuebra;
            $espacoJaOcupadoParaValidacao = $espacoSegundaQuebra;
        }

        if (!empty($proximaPagina['exame'])) {
            rodape($oPdf, $oDadosEstrutura);
            montaCabecalho($oPdf, $oDadosEstrutura);
            $oPdf->SetY(50);
        }
        return $proximaPagina;
    }
}

function exibeHistoricoResultados(ECidade\Pdf\Pdf $pdf, $exame)
{
    $colocouTitulo = false;
    $posicaoPosData = 0;
    $requisicoes = [];

    foreach ($exame->aAtributos as $oAtributos) {
        if (empty($oAtributos->resultadosAnteriores)) {
            continue;
        }

        foreach ($oAtributos->resultadosAnteriores['requisicao'] as $requisicao) {
            if (!array_key_exists($requisicao->getCodigo(), $requisicoes)) {
                $requisicoes[$requisicao->getCodigo()] = $requisicao;
            }
        }
    }

    arsort($requisicoes);

    foreach ($exame->aAtributos as $key => $oAtributos) {
        if ($key === 0) {
            $pdf->Ln(2);
            $pdf->SetFont('arial', '', 10);
        }

        if ($key === 0 && (int)$oAtributos->tipo === 1) {
            $pdf->cell(40, 4, "Histórico de Resultados:", 0, 0, 'R');
            $pdf->SetFont('arial', 'B', 8);
            $pdf->cell(100, 4, $oAtributos->nome, 0, 0, 'L');
            $pdf->Ln();

            if (empty($requisicoes)) {
                $pdf->cell(100, 4, 'Não foram encontrados resultados anteriores para este exame.', 0, 0, 'L');
            }
        }

        if (empty($oAtributos->resultadosAnteriores)) {
            continue;
        }

        if ($key >= 0 && (int)$oAtributos->tipo === 2) { // Iremos pegar os atributos analíticos apenas
            $x1 = $pdf->getX();
            $y1 = $pdf->getY();

            if (!$colocouTitulo) {
                $colocouTitulo = true;

                $pdf->SetFont('arial', '', 8);
                $pdf->SetFillColor(240);
                $pdf->MultiCell(50, 5, 'N° Requisição' . PHP_EOL . 'Data Requisição', 1, 'C', true);

                $pdf->setXY($x1 + 50, $y1);

                foreach ($requisicoes as $requisicao) {
                    $cabecalhoDatas = $requisicao->getCodigo() . PHP_EOL . $requisicao->getData()->__toString();

                    $x2 = $pdf->getX();
                    $y2 = $pdf->getY();

                    $pdf->MultiCell(25, 5, $cabecalhoDatas, 1, 'C', true);
                    $posicaoPosData = $pdf->getY();

                    $pdf->setXY($x2 + 25, $y2);
                }
            }

            $pdf->setY($posicaoPosData);
            $x3 = $pdf->getX();
            $y3 = $pdf->getY();

            $requisicoes = array_values($requisicoes);
            $historicoResultados = $oAtributos->resultadosAnteriores['atributo'];

            foreach ($requisicoes as $keyRequisicao => $requisicao) {
                $temResultadoAnterior = false;
                $posicaoResultado = 0;

                foreach ($oAtributos->resultadosAnteriores['requisicao'] as $posicao => $requisicaoAnterior) {
                    if ($requisicaoAnterior->getCodigo() === $requisicao->getCodigo()) {
                        $temResultadoAnterior = true;
                        $posicaoResultado = $posicao;
                        break;
                    }
                }

                if (!$temResultadoAnterior) {
                    $qtLinhasNome = $pdf->nbLines(50, $oAtributos->nome);
                    $qtLinhasresultado = $pdf->nbLines(25, ' - ');
                    $modificador = 1;
                    $qtLinhasResultadoMaior = false;

                    if ($keyRequisicao === 0) {
                        if ($qtLinhasresultado > $qtLinhasNome) {
                            $modificador = $qtLinhasresultado;
                            $qtLinhasResultadoMaior = true;
                        }

                        $pdf->MultiCell(50, 5 * $modificador, $oAtributos->nome, 1, 'L', true);
                        $posicaoPosData = $pdf->getY();
                        $pdf->setXY($x3 + 50, $y3);
                    }

                    if ($qtLinhasNome > $qtLinhasresultado) {
                        $modificador = $qtLinhasNome;
                    }

                    if ($qtLinhasResultadoMaior) {
                        $modificador = 1;
                    }

                    $x4 = $pdf->getX();
                    $y4 = $pdf->getY();

                    $pdf->MultiCell(25, 5 * $modificador, ' - ', 1, 'C');
                    $pdf->setXY($x4 + 25, $y4);
                    continue;
                }


                $qtLinhasNome = $pdf->nbLines(50, $oAtributos->nome);
                $qtLinhasresultado = $pdf->nbLines(25, $historicoResultados[$posicaoResultado]->getValorAbsoluto());
                $modificador = 1;
                $qtLinhasResultadoMaior = false;

                if ($keyRequisicao === 0) {
                    if ($qtLinhasresultado > $qtLinhasNome) {
                        $modificador = $qtLinhasresultado;
                        $qtLinhasResultadoMaior = true;
                    }

                    $pdf->MultiCell(50, 5 * $modificador, $oAtributos->nome, 1, 'L', true);
                    $posicaoPosData = $pdf->getY();
                    $pdf->setXY($x3 + 50, $y3);
                }

                if ($qtLinhasNome > $qtLinhasresultado) {
                    $modificador = $qtLinhasNome;
                }

                if ($qtLinhasResultadoMaior) {
                    $modificador = 1;
                }

                $x4 = $pdf->getX();
                $y4 = $pdf->getY();

                $valor = $historicoResultados[$posicaoResultado]->getValorAbsoluto() === ''
                    ? ' - '
                    : $historicoResultados[$posicaoResultado]->getValorAbsoluto();

                $pdf->MultiCell(25, 5 * $modificador, $valor, 1, 'C');
                $pdf->setXY($x4 + 25, $y4);
            }
        }
    }
}

$oPdf->Output();
