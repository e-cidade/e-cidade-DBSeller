<?php

$oPdf = new ECidade\Pdf\Pdf();
$oPdf->init(false, false, false);
$oPdf->AliasNbPages();
$oPdf->SetTopMargin(1);
$oPdf->SetAutoPageBreak(false, 5);
$oPdf->mostrarTotalDePaginas();

$calculoPdf = new ECidade\Pdf\Pdf();
$calculoPdf->init(false, false, false);
$calculoPdf->AliasNbPages();
$calculoPdf->SetTopMargin(1);
$calculoPdf->SetAutoPageBreak(false, 5);
$calculoPdf->mostrarTotalDePaginas();

/**
 * Altura limite para impressão dos exâmes
 */
DBRegistry::add('iAlturaLimiteExames', 270);
DBRegistry::add('requisicao', $oDadosEstrutura->iRequisicao);
DBRegistry::add('solicitante', $oDadosEstrutura->oSolicitante);
DBRegistry::add('data', $oDadosEstrutura->sData);

$nomeSetor = '';

try {
    foreach ($oDadosEstrutura->aSetor as $iSetor => $oSetor) {
        $oDadosEstrutura->iSetor = $iSetor;
        $primeiroExameSetor = true;

        if ($nomeSetor != $oSetor->sDescricao) {
            montaCabecalho($calculoPdf, $oDadosEstrutura->iDepartamento);
            $calculoPdf->Cell(190, $oDadosEstrutura->iAlturaPadrao, $oSetor->sDescricao, 'B', 1, "L");
            $calculoPdf->SetFont('arial', "B", 10);
        }
        $nomeSetor = $oSetor->sDescricao;

        montaCabecalho($oPdf, $oDadosEstrutura->iDepartamento);

        $oPdf->SetFont("arial", "", 8);
        $oPdf->Cell($oDadosEstrutura->iLarguraPadrao, $oDadosEstrutura->iAlturaPadrao, $oSetor->sDescricao, 'B', 1);

        foreach ($oSetor->aExames as $oExame) {
            imprimirAtributosExame($oPdf, $oExame, $oDadosEstrutura, $calculoPdf, $primeiroExameSetor);

            $primeiroExameSetor = false;

            $oPdf->SetFont("arial", '', 7);
            $oPdf->setX(15);
            $oPdf->SetFont("arial", 'B', 7);
            $oPdf->Cell(30, 3.5, "Observações do Resultado:", 0, 1);
            $oPdf->setX(15);
            $oPdf->SetFont("arial", '', 7);
            $oPdf->MultiCell(187, 3.5, $oExame->sObservacao);

            if ($oDadosEstrutura->mostrarConferenciaPorExame === true) {
                $oPdf->SetFont('arial', 'b', 8);
                $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $oExame->mensagemLiberacao, 0, "L");
            }

            if ($oExame->exibeHistoricoResultados === true) {
                if (!empty($exameNovaPagina['historicoNovaFolha'])) {
                    rodape($oPdf, $oDadosEstrutura);
                    montaCabecalho($oPdf, $oDadosEstrutura);
                    $oPdf->SetY(50);
                }
                exibeHistoricoResultados($oPdf, $oExame);
            }

            $oPdf->ln(6);
        }

        rodape($oPdf, $oDadosEstrutura);
    }
} catch (Exception $oErro) {
    $sMessage = urlencode($oErro->getMessage());
    db_redireciona("db_erros.php?fechar=true&db_erro={$sMessage}");
}

/**
 * ALTERAÇÕES FEITAS NO CONTEÚDO QUE SERÁ EXIBIDO NO PDF DEVEM ESTAR DE ACORDO COM A FUNÇÃO exameAtributosNovaPagina()
 * PARA QUE A QUEBRA CONTINUE CORRETA.
 */
function imprimirAtributosExame($oPdf, $oExame, $oDadosEstrutura, $calculoPdf, $primeiroExameSetor)
{
    $exameNovaPagina = exameAtributosNovaPagina($calculoPdf, $oExame, $oDadosEstrutura, $primeiroExameSetor);

    foreach ($oExame->aAtributos as $key => $oAtributo) {
        if (!empty($exameNovaPagina['exame']) && $oExame->sNomeExame === $exameNovaPagina['exame'] && $key === $exameNovaPagina['atributo']) {
            rodape($oPdf, $oDadosEstrutura);
            montaCabecalho($oPdf, $oDadosEstrutura->iDepartamento);
        }

        if (!empty($exameNovaPagina)
            && !empty($exameNovaPagina['novaQuebra'])
            && $oExame->sNomeExame === $exameNovaPagina['novaQuebra']['exame']
            && $key === $exameNovaPagina['novaQuebra']['atributo']
            && $exameNovaPagina['atributo'] !== $exameNovaPagina['novaQuebra']['atributo']) {
            rodape($oPdf, $oDadosEstrutura);
            montaCabecalho($oPdf, $oDadosEstrutura->iDepartamento);
        }

        $oAtributo->iAlturaLinhaComplemento = 4;
        $oPdf->SetFont("arial", "", 8);

        if ($oAtributo->tipo == 1) {
            imprimirAtributoSintetico($oPdf, $oAtributo);
            continue;
        }

        $oPdf->SetFont("arial", "", 8);
        switch ($oAtributo->tiporeferencia) {
            case 1:
            case 3:
                imprimirAtributoAlfaNumerico($oPdf, $oAtributo);
                break;
            case 2:
                imprimirAtributoReferenciaNumerica($oPdf, $oAtributo);
                break;
            default:

                $sMsg = "Revise o cadastro do atributo {$oAtributo->nome} vinculado ao exame {$oExame->sNomeExame}.";
                throw new Exception($sMsg);
                break;
        }

        if (!empty($oAtributo->titulacao)) {
            $oPdf->setX(15);
            $oPdf->MultiCell(187, $oAtributo->iAlturaLinhaComplemento, "Titulação: " . $oAtributo->titulacao, 0);
        }

        if (!empty($oAtributo->valorabsolutoanterior)) {
            $sDataResultado = $oAtributo->dataResultadoAnterior->convertTo(DBDate::DATA_PTBR);
            $oPdf->setX(15);
            $sTexto = "Último Resultado: {$oAtributo->valorabsolutoanterior} {$oAtributo->unidade} ";
            $sTexto .= "realizado no dia {$sDataResultado}";
            $oPdf->MultiCell(187, $oAtributo->iAlturaLinhaComplemento, $sTexto, 0);
        }

        $oPdf->ln();
    }
}

function validaQuebraPagina($oPdf, $oAtributo)
{
    $oAtributo->iAlturaLinhaComplemento;

    $iNumeroLinhasAtributo = 0;
    $iNumeroLinhasComplemento = 0;

    if ($oAtributo->tiporeferencia == 2) {
        $sValor = " {$oAtributo->valorabsoluto} {$oAtributo->unidade}";

        if (!empty($oAtributo->valorpercentual)) {
            $sPercent = str_pad($oAtributo->valorpercentual . " %", 5, " ", STR_PAD_LEFT);
            $sValor = "{$sPercent}      {$sValor}";
        }

        $iNumeroLinhasAtributo += $oPdf->nbLines(96, $sValor);
        // Sendo resultado numérico, sempre temos que levar em consideração o valor de referência que são 3 linhas.
        $iNumeroLinhasComplemento += 3;
    } else {
        $iNumeroLinhasAtributo += $oPdf->nbLines(96, $oAtributo->valorabsoluto);
    }

    // Calcula quantas linhas a titulação (se houver) ocupará no pdf.
    if (!empty($oAtributo->titulacao)) {
        $iNumeroLinhasComplemento += $oPdf->nbLines(187, "Titulação: " . $oAtributo->titulacao);
    }

    // Calcula quantas linhas o resultado anterior (se houver) ocupará no pdf.
    if (!empty($oAtributo->valorabsolutoanterior)) {
        $iNumeroLinhasComplemento += $oPdf->nbLines(
            187,
            "Último Resultado: {$oAtributo->valorabsolutoanterior} {$oAtributo->unidade}"
        );
    }

    $iAlturaComplementos = $iNumeroLinhasComplemento * $oAtributo->iAlturaLinhaComplemento;
    $iAlturaAtributos = $iNumeroLinhasAtributo * 5;
    $iAlturaConteudoAtributos = $iAlturaComplementos + $iAlturaAtributos;

    if (($oPdf->getY() + $iAlturaConteudoAtributos) > DBRegistry::get('iAlturaLimiteExames')) {
        return true;
    }

    return false;
}

function imprimirAtributoAlfaNumerico($oPdf, $oAtributo)
{
    $sAtributo = identaNomeAtributo($oAtributo);
    $oPdf->Cell(96, 5, completaCelula($oPdf, 96, $sAtributo), 0, 0, '', 0, '');
    $oPdf->MultiCell(96, 5, $oAtributo->valorabsoluto, 0);
}

function imprimirAtributoReferenciaNumerica($oPdf, $oAtributo)
{
    $sAtributo = identaNomeAtributo($oAtributo);
    $oPdf->Cell(96, 5, completaCelula($oPdf, 96, $sAtributo), 0, 0, '', 0, '');

    $sValor = " {$oAtributo->valorabsoluto} {$oAtributo->unidade}";
    if (!empty($oAtributo->valorpercentual)) {
        $sPercent = str_pad($oAtributo->valorpercentual . " %", 5, " ", STR_PAD_LEFT);
        $sValor = "{$sPercent}      {$sValor}";
    }

    $oPdf->MultiCell(96, 5, $sValor, 0);

    $oPdf->SetFont("arial", "B", 8);
    $oPdf->ln(4);
    $oPdf->setX(76);
    $oPdf->Cell(30, 4, "Valor de Referência:", 0, 0);
    $oPdf->SetFont("arial", "", 8);
    $oPdf->Cell(30, 4, $oAtributo->referencia, 0, 1);

    $oPdf->ln();
}

function identaNomeAtributo($oAtributo)
{
    return str_repeat("  ", $oAtributo->nivel) . $oAtributo->nome . ' ';
}

function imprimirAtributoSintetico($oPdf, $oAtributo)
{
    $oPdf->SetFont("arial", "B", 8);
    $oPdf->Cell(192, 5, identaNomeAtributo($oAtributo), 0, 1);
}

/**
 * [montaCabecalho description]
 * @param \ECidade\Pdf\Pdf $oPdf [description]
 * @param stdClass $oDadosEstrutura [description]
 */
function montaCabecalho(\ECidade\Pdf\Pdf $oPdf, $departamento)
{
    $oPdf->AddPage();

    try {
        $iRequisicao = DBRegistry::get('requisicao');
        $oSolicitante = DBRegistry::get('solicitante');

        $oDepartamento = new DBDepartamento($departamento);
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
        $oPdf->Cell($iLarguraDescricao, 4, $iRequisicao, 0, 1, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Paciente:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->MultiCell($iLarguraDescricao, 4, $oSolicitante->sNome, 0, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Idade:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->Cell($iLarguraDescricao, 4, $oSolicitante->idadeCompleta, 0, 1, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Sexo:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->Cell($iLarguraDescricao, 4, $aSexo[$oSolicitante->sSexo], 0, 1, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Médico:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->MultiCell($iLarguraDescricao, 4, $oSolicitante->sMedico, 0, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Convênio:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->Cell($iLarguraDescricao, 4, "SUS", 0, 1, "L");

        $oPdf->SetX(120);
        $oPdf->SetFont("arial", "B", 7);
        $oPdf->Cell($iLarguraLabel, 4, "Data:", 0, 0, "L");
        $oPdf->SetFont("arial", "", 7);
        $oPdf->Cell($iLarguraDescricao, 4, DBRegistry::get('data'), 0, 1, "L");

        $oPdf->Line(10, 45, 200, 45);
        $oPdf->SetY(50);
    } catch (Exception $oErro) {
        db_msgbox($oErro->getMessage());
        db_redireciona("lab4_emissaoresult001.php");
    }
}

function imprimeEndereco($oPdf)
{
    $oPdf->Line(10, 282, 200, 282);
    $oDepartamento = new DBDepartamento($_SESSION['DB_coddepto']);
    $oEnderecoDepartamento = $oDepartamento->getEndereco();

    $sEndereco = "Endereço: " . $oEnderecoDepartamento->sRua;

    if (!empty($oEnderecoDepartamento->iNumero)) {
        $sEndereco .= ", {$oEnderecoDepartamento->iNumero}";
    }

    if (!empty($oEnderecoDepartamento->sComplemento)) {
        $sEndereco .= " - {$oEnderecoDepartamento->sComplemento}";
    }

    if (!empty($oEnderecoDepartamento->sBairro)) {
        $sEndereco .= " - {$oEnderecoDepartamento->sBairro}";
    }

    if ($oDepartamento->getInstituicao()->getMunicipio() != "") {
        $sEndereco .= " - " . $oDepartamento->getInstituicao()->getMunicipio();

        if ($oDepartamento->getInstituicao()->getUf() != "") {
            $sEndereco .= "/" . $oDepartamento->getInstituicao()->getUf();
        }
    }

    $sContato = "";
    if ($oDepartamento->getTelefone() != "") {
        $sContato = "Contato: " . $oDepartamento->getTelefone();
    }

    if ($oDepartamento->getEmailDepartamento() != "") {
        $sContato .= " - " . $oDepartamento->getEmailDepartamento();
    }

    $oPdf->setY(282);
    $oPdf->SetFont("arial", "B", 5);
    $oPdf->Cell(192, 3, $sEndereco, 0, 1, "C");
    $oPdf->Cell(192, 3, $sContato, 0, 1, "C");
}

/**
 * Função para verificar se a impressão das informações relacionadas a cada exame, irão caber no espaço
 * disponível. Caso não caibam retorna o exame e a chave do respectivo atributo no momento onde o conteúdo
 * impresso ocupa mais espaço do que a altura disponível.
 *
 * @param \ECidade\Pdf\Pdf $oPdf
 * @param $oExame
 * @param $oDadosEstrutura
 * @param $primeiroExame
 * @param $existeReferencia
 * @return array|void
 * @throws DBException
 */
function exameAtributosNovaPagina(\ECidade\Pdf\Pdf $oPdf, $oExame, $oDadosEstrutura, $primeiroExame)
{
    $proximaPagina = [];
    $alturaLimite = 27;
    $alturaInicioAtributo = 0;
    $espacoSegundaQuebra = 0;
    $alturaLimiteNovaQuebra = 237 - 27;

    $quebrouNoAtributo = false;
    $teveQuebra = false;
    $teveNovaQuebra = false;

    /***
     * Irá armazenar o equivalente a quantia de espaço usado pelo exame.
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

        if ($oExame->exibeHistoricoResultados === true) {
            $alturaLimite = 60;
            $alturaLimiteNovaQuebra = 237 - 60;
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

    foreach ($oExame->aAtributos as $key => $oAtributo) {
        $alturaInicioAtributo = $oPdf->getAvailableHeight();

        $oAtributo->iAlturaLinhaComplemento = 4;
        $oPdf->SetFont("arial", "", 8);

        if ($oAtributo->tipo == 1) {
            imprimirAtributoSintetico($oPdf, $oAtributo);
            continue;
        }

        $oPdf->SetFont("arial", "", 8);
        switch ($oAtributo->tiporeferencia) {
            case 1:
            case 3:
                imprimirAtributoAlfaNumerico($oPdf, $oAtributo);
                break;
            case 2:
                imprimirAtributoReferenciaNumerica($oPdf, $oAtributo);
                break;
            default:

                $sMsg = "Revise o cadastro do atributo {$oAtributo->nome} vinculado ao exame {$oExame->sNomeExame}.";
                throw new Exception($sMsg);
                break;
        }

        if (!empty($oAtributo->titulacao)) {
            $oPdf->setX(15);
            $oPdf->MultiCell(187, $oAtributo->iAlturaLinhaComplemento, "Titulação: " . $oAtributo->titulacao, 0);
        }

        if (!empty($oAtributo->valorabsolutoanterior)) {
            $sDataResultado = $oAtributo->dataResultadoAnterior->convertTo(DBDate::DATA_PTBR);
            $oPdf->setX(15);
            $sTexto = "Último Resultado: {$oAtributo->valorabsolutoanterior} {$oAtributo->unidade} ";
            $sTexto .= "realizado no dia {$sDataResultado}";
            $oPdf->MultiCell(187, $oAtributo->iAlturaLinhaComplemento, $sTexto, 0);
        }

        $oPdf->ln();

        $alturaDisponivel = $oPdf->getAvailableHeight() - $espacoJaOcupadoParaValidacao;

        /***
         * Caso a altura disponível seja menor ou igual a altura limite de impressão, irá retornar em qual exame e atributo ocupou
         * espaço a mais.
         */
        if ($alturaDisponivel <= $alturaLimite && !$quebrouNoAtributo) {
            $proximaPagina['exame'] = $oExame->sNomeExame;
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
            $proximaPagina['novaQuebra']['exame'] = $oExame->sNomeExame;
            $proximaPagina['novaQuebra']['atributo'] = $key;
        }
    }

    $oPdf->SetFont("arial", '', 7);
    $oPdf->setX(15);
    $oPdf->SetFont("arial", 'B', 7);
    $oPdf->Cell(30, 3.5, "Observações do Resultado:", 0, 1);
    $oPdf->setX(15);
    $oPdf->SetFont("arial", '', 7);
    $oPdf->MultiCell(187, 3.5, $oExame->sObservacao);

    if ($oDadosEstrutura->mostrarConferenciaPorExame === true) {
        $oPdf->SetFont('arial', 'b', 8);
        $oPdf->MultiCell($oDadosEstrutura->iLarguraPadrao, 4, $oExame->mensagemLiberacao, 0, "L");
    }

    $oPdf->ln(6);

    $espacoJaOcupado = $alturaInicioFolha - ($oPdf->getAvailableHeight());

    $alturaDisponivel = $oPdf->getAvailableHeight() - $espacoJaOcupadoParaValidacao;

    if ($alturaDisponivel <= $alturaLimite && !$quebrouNoAtributo) {
        $proximaPagina['exame'] = $oExame->sNomeExame;
        $proximaPagina['atributo'] = $key;
        $teveQuebra = true;

        if (!$primeiroExame) {
            $proximaPagina['atributo'] = 0;
        }
    }

    if ($teveQuebra && $espacoJaOcupado >= $alturaLimiteNovaQuebra && !$teveNovaQuebra) {
        $teveNovaQuebra = true;
        $proximaPagina['novaQuebra']['exame'] = $oExame->sNomeExame;
        $proximaPagina['novaQuebra']['atributo'] = $key;
    }

    if ($oExame->exibeHistoricoResultados === true) {
        exibeHistoricoResultados($oPdf, $oExame);
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
            montaCabecalho($oPdf, $oDadosEstrutura->iDepartamento);
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
