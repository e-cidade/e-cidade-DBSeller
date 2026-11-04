<?php

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
require_once(modification('libs/db_conecta.php'));
require_once(modification("libs/db_sessoes.php"));

if ($_SERVER['REQUEST_METHOD'] === "POST") {
    require_once(modification("forms/pdf_lovrot.php"));

    $folha     = filter_input(INPUT_POST, 'tamanho', FILTER_DEFAULT);
    $dados     = filter_input(INPUT_POST, 'table', FILTER_DEFAULT);
    $formato   = filter_input(INPUT_POST, 'formato', FILTER_DEFAULT);
    $headerPDF = filter_input(INPUT_POST, 'header_pdf');

    if (!empty($headerPDF)) {
        $headerPDF = base64_decode($headerPDF);
        $headerPDF = json_decode($headerPDF);
    }

    $dados = base64_decode($dados);
    $dados = "<body>" . $dados . "</body>";
    $pdf   = new \GerarPDFLovrot(null, $folha, $formato);

    $pdf->setSession($_SESSION)
        ->setHeaderAdd($headerPDF)
        ->setHeaderInstituicao()
        ->montedPageHTML($dados)
        ->output();
}
