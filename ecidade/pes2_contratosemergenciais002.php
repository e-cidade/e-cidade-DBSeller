<?php

use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));

parse_str($_SERVER['QUERY_STRING']);


if (!isset($dataInicio) || empty($dataInicio)) {
    $dataInicio = "1979-01-01";
}

if (!isset($dataFim) || empty($dataFim)) {
    $dataFim = "2999-12-31";
}

$where = "    exists (select 1
                        from rhcontratoemergencialrenovacao as datainicio
                        where rh164_datafim >= '{$dataInicio}'
                          and datainicio.rh164_contratoemergencial = rh163_sequencial)
          and exists (select 1
                        from rhcontratoemergencialrenovacao as datafim
                       where rh164_datainicio <= '{$dataFim}'
                         and datafim.rh164_contratoemergencial = rh163_sequencial)";

if ($regist != 0) {
    $where = " and rh163_matricula = {$regist}";
}

$sql = "select rh163_matricula, 
               z01_nome,
               rh164_descricao,
               rh164_datainicio,
               rh164_datafim, 
               rh05_recis 
          from rhcontratoemergencial
               inner join rhcontratoemergencialrenovacao on rh163_sequencial = rh164_contratoemergencial
               inner join rhpessoal on rh163_matricula = rh01_regist
               inner join rhpessoalmov on rh02_regist = rh01_regist 
                                      and rh02_anousu = ".DBPessoal::getAnoFolha()." 
                                      and rh02_mesusu = ".DBPessoal::getMesFolha()."
                                      and rh02_instit = rh01_instit
                left join rhpesrescisao on rh05_seqpes = rh02_seqpes                         
               inner join cgm on rh01_numcgm = z01_numcgm
         where {$where}
         order by rh163_matricula";

$rsDados = db_query($sql);
if (pg_num_rows($rsDados) == 0) {
    db_redireciona("db_erros.php?fechar=true&db_erro='Nenhum Registro Encontrado'");
}

$head1 = "Contratos Emergenciais";
$head2 = "Ordem: Servidor, data início";

$hashMatricula = null;

if ($formaEmissao == "pdf") {
    $pdf = new PDF();
    $pdf->addTitulo($head1);
    $pdf->addTitulo($head2);
    $pdf->init(false);
    $pdf->AliasNbPages();
} else {
    $arquivoCsv = "tmp/contratosEmergencias.csv";
    $handle = fopen($arquivoCsv, 'w+');
    fwrite($handle, "{$head1}\n");
    fwrite($handle, "{$head2}\n");
    fwrite($handle, "Matricula;Nome;Descrição;Data início;Data término;Rescisão\n");
}

for ($i = 0; $i < pg_num_rows($rsDados); $i++) {
    $dados = db_utils::fieldsMemory($rsDados, $i, true);

    if ($formaEmissao == "pdf") {
        if ($i == 0) {
            $pdf->addPage();
        }

        if ($hashMatricula != $dados->rh163_matricula) {
            $pdf->ln(4);

            $pdf->setFont('arial', '');
            $pdf->cell(15, 4, $dados->rh163_matricula, 0, 0, "L");
            $pdf->cell(170, 4, $dados->z01_nome, 0, 1, "L");

            $pdf->setFont('arial', 'b');
            $pdf->cell(100, 4, "Descrição", 0, 0, "L", 1);
            $pdf->cell(25, 4, "Data início", 0, 0, "L", 1);
            $pdf->cell(25, 4, "Data término", 0, 0, "L", 1);
            $pdf->cell(25, 4, "Rescisão", 0, 1, "L", 1);
            $pdf->setFont('arial', '');
        }

        $pdf->setFont('arial', '');
            
        $pdf->cell(100, 4, $dados->rh164_descricao, 0, 0, "L", 0);
        $pdf->cell(25, 4, $dados->rh164_datainicio, 0, 0, "L", 0);
        $pdf->cell(25, 4, $dados->rh164_datafim, 0, 0, "L", 0);
        $pdf->cell(25, 4, $dados->rh05_recis, 0, 1, "L", 0);

        $hashMatricula = $dados->rh163_matricula;
    } else {
        fwrite(
            $handle,
            $dados->rh163_matricula
            .";".$dados->z01_nome
            .";".$dados->rh164_descricao
            .";".$dados->rh164_datainicio
            .";".$dados->rh164_datafim
            .";".$dados->rh05_recis."\n"
        );
    }
}

if ($formaEmissao == "pdf") {
    $pdf->output();
} else {
    fclose($handle);
    echo "<script>
    window.opener.downloadArquivo('{$arquivoCsv}');
    window.close();
    </script>";
}
