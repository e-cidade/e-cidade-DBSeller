<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBSeller Servicos de Informatica
 *                            www.dbseller.com.br
 *                         e-cidade@dbseller.com.br
 *
 *  Este programa e software livre; voce pode redistribui-lo e/ou
 *  modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 *  publicada pela Free Software Foundation; tanto a versao 2 da
 *  Licenca como (a seu criterio) qualquer versao mais nova.
 *
 *  Este programa e distribuido na expectativa de ser util, mas SEM
 *  QUALQUER GARANTIA; sem mesmo a garantia implicita de
 *  COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 *  PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 *  detalhes.
 *
 *  Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 *  junto com este programa; se nao, escreva para a Free Software
 *  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 *  02111-1307, USA.
 *
 *  Copia da licenca no diretorio licenca/licenca_en.txt
 *                                licenca/licenca_pt.txt
 */

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("fpdf151/PDFDocument.php"));


try {
    $oParam = JSON::create()->parse(str_replace("\\", "", $_POST["json"]));
    $object = JSON::create()->parse(str_replace("\\", "", $_POST["object"]));
    if (empty($oParam) && empty($object)) {
        throw new BusinessException("Dados não encontrados!");
    }
    if ($object->autorizadas == 1) {
        $autorizadas = 'SIM';
    } elseif ($object->autorizadas == 0) {
        $autorizadas = 'TODAS';
    } else {
        $autorizadas = 'NÃO';
    }
    $oPdf = new PDFDocument();
    $oPdf->addHeaderDescription('');
    $oPdf->addHeaderDescription('Autorização de Férias');
    $oPdf->addHeaderDescription('');
    if ($object->iMatricula) {
        $oPdf->addHeaderDescription('Matricula: '.$object->iMatricula);
    }
    if ($object->sDataInicio) {
        $oPdf->addHeaderDescription('Data inicial: '.$object->sDataInicio);
    }
    if ($object->sDataFinal) {
        $oPdf->addHeaderDescription('Data final: '.$object->sDataFinal);
    }
    if ($object->AnoFolha && $object->MesFolha) {
        $oPdf->addHeaderDescription('Ano / Mês: '.$object->AnoFolha.'/'.$object->MesFolha);
    }
        
    $oPdf->addHeaderDescription('Autorizadas: '.$autorizadas);
    $oPdf->Open();
    $oPdf->setBold(true);
    $oPdf->AddPage();
    pdfHeader($oPdf);
    $oPdf->SetFontSize(12);
    $oPdf->Cell(200, 12, "Autorização de Férias", 0, 1, 'C', 0);
    $oPdf->SetFontSize(8);
    $oPdf->setBold(true);
    $oPdf->Cell(15, 4, "Matricula", 'BRLT', 0, 'L', 0);
    $oPdf->Cell(62, 4, "Servidor", 'BRLT', 0, 'L', 0);
    $oPdf->Cell(18, 4, "Data Início", 'BRLT', 0, 'L', 0);
    $oPdf->Cell(20, 4, "Data Término", 'BRLT', 0, 'L', 0);
    $oPdf->Cell(20, 4, "Dias de Gozo", 'BRLT', 0, 'L', 0);
    $oPdf->Cell(19, 4, "Dias Abono", 'BRLT', 0, 'L', 0);
    $oPdf->Cell(20, 4, "Dias Pecúnia", 'BRLT', 0, 'L', 0);
    $oPdf->Cell(17, 4, "Autorizada", 'BRLT', 1, 'L', 0);
    $oPdf->setBold(false);

    foreach ($oParam as $key => $value) {
        $oPdf->Cell(15, 4, $value[2], 'BRLT', 0, 'L', 0);
        $oPdf->Cell(62, 4, mb_strimwidth($value[3], 0, 35, "..."), 'BRLT', 0, 'L', 0);
        $oPdf->Cell(18, 4, $value[4], 'BRLT', 0, 'L', 0);
        $oPdf->Cell(20, 4, $value[5], 'BRLT', 0, 'L', 0);
        $oPdf->Cell(20, 4, $value[6], 'BRLT', 0, 'L', 0);
        $oPdf->Cell(19, 4, $value[7], 'BRLT', 0, 'L', 0);
        $oPdf->Cell(20, 4, $value[8], 'BRLT', 0, 'L', 0);
        $oPdf->Cell(17, 4, $value[9], 'BRLT', 1, 'L', 0);
    }
    $oPdf->showPDF();
} catch (Exception $exception) {
    return db_redireciona("db_erros.php?fechar=true&db_erro={$exception->getMessage()}");
}

function pdfHeader($oPdf)
{

    $oInstituicao = new Instituicao(db_getsession("DB_instit"));
    $iColuna = $oPdf->GetLeftMargin();

    $oPdf->setXY($iColuna, 1);
    $oPdf->image("imagens/files/{$oInstituicao->getImagemLogo()}", $iColuna, 3, 20);

    $oPdf->setBold(true);
    $oPdf->setItalic(true);
    $oPdf->setUnderline(false);
    $oPdf->setFontSize(9);

    if (strlen($oInstituicao->getDescricao()) > 42) {
        $oPdf->setFontSize(8);
    }

    $iColunaTexto = $iColuna + 23;
    $oPdf->text($iColunaTexto, 9, $oInstituicao->getDescricao());

    $oPdf->setBold(false);
    $oPdf->setFontSize(8);

    $sComplento = substr(trim($oInstituicao->getComplemento()), 0, 20);

    if (!empty($sComplento)) {
        $sComplento = ", " . substr(trim($oInstituicao->getComplemento()), 0, 20);
    }

    $oPdf->text($iColunaTexto, 14, trim($oInstituicao->getLogradouro()) .
     ", " . trim($oInstituicao->getNumero()) . $sComplento);
    $oPdf->text($iColunaTexto, 18, trim($oInstituicao->getMunicipio()) . " - " . trim($oInstituicao->getUF()));
    $oPdf->text($iColunaTexto, 22, trim($oInstituicao->getTelefone()) .
    "   -    CNPJ : " . db_formatar($oInstituicao->getCNPJ(), "cnpj"));
    $oPdf->text($iColunaTexto, 26, trim($oInstituicao->getEmail()));
    $oPdf->text($iColunaTexto, 30, $oInstituicao->getSite());

    $iColunaFinal = $oPdf->getAvailWidth() + $iColuna;

    $oPdf->setFillColor(0);
    $oPdf->setLeftMargin($iColuna);
    $oPdf->setY(35);
}
