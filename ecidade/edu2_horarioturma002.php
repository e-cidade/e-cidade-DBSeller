<?php
/*
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

use ECidade\Pdf\Pdf;

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("classes/db_regenciahorario_classe.php"));
require_once(modification("classes/db_periodoescola_classe.php"));
require_once(modification("classes/db_escola_classe.php"));
require_once(modification("classes/db_turma_classe.php"));
require_once(modification("classes/db_turmaturnoadicional_classe.php"));
require_once(modification("classes/db_diasemana_classe.php"));

$clregenciahorario = new cl_regenciahorario;
$cldiasemana = new cl_diasemana;
$clperiodoescola = new cl_periodoescola;
$clescola = new cl_escola;
$clturma = new cl_turma;
$clturmaturnoadicional = new cl_turmaturnoadicional;

isset($escola) ?: $escola = db_getsession("DB_coddepto");

$sCampos  = "distinct                                   \n";
$sCampos .= "ed57_i_codigo, ed52_c_descr,ed57_c_descr,ed11_c_descr,ed57_i_turno, ed220_i_codigo \n";

$sSql   = $clturma->sql_query_turmaserie("", $sCampos, "ed57_c_descr", " ed220_i_codigo in ($turma)");

$resultTurmas = $clturma->sql_record($sSql);

if ($clturma->numrows == 0) {
    ?>
    <table width='100%'>
        <tr>
            <td align='center'>
                <font color='#FF0000' face='arial'>
                    <b>Nenhuma registro encontrado.<br>
                        <input type='button' value='Fechar' onclick='window.close()'></b>
                </font>
            </td>
        </tr>
    </table>
    <?php
    exit;
}
$pdf = new Pdf();
$pdf->AliasNbPages();
$linhas = $clturma->numrows;

if ($professor != "") {
    $pdf->addTitulo("Professor: $professor");
} else {
    $pdf->addTitulo("Professor: TODOS");
}

$pdf->addTitulo("RELATÓRIO DE HORÁRIO DE TURMAS");

for ($y = 0; $y < $linhas; $y++) {
    
    db_fieldsmemory($resultTurmas, $y);
    
    $pdf->editTitleByKey(3,"Turma: $ed57_c_descr");
    $pdf->editTitleByKey(4,"Etapa: $ed11_c_descr");
    $pdf->editTitleByKey(5,"Calendário: $ed52_c_descr");
    
    $pdf->init(false);
    $pdf->exibeHeader(true, 1);
    $pdf->addpage('P');

    $result_add = $clturmaturnoadicional->sql_record($clturmaturnoadicional->sql_query("", "ed246_i_turno", "", " ed246_i_turma in ($ed57_i_codigo)"));

    if ($clturmaturnoadicional->numrows > 0) {
        db_fieldsmemory($result_add, 0);
        $cod_turnos = "$ed57_i_turno,$ed246_i_turno";
    } else {
        $cod_turnos = "$ed57_i_turno";
    }

    $turno = "";
    $sql = $clperiodoescola->sql_query("", "*", "ed15_i_sequencia,ed08_i_sequencia", " ed17_i_escola = $escola AND ed17_i_turno in ($cod_turnos)");
    $result1 = $clperiodoescola->sql_record($sql) or die(pg_errormessage());
    $contp = 0;
    $contd = 0;

    for ($z = 0; $z < $clperiodoescola->numrows; $z++) {
        db_fieldsmemory($result1, $z);
        $result = $cldiasemana->sql_record($cldiasemana->sql_query_rh("", "*", "ed32_i_codigo", " ed04_c_letivo = 'S' AND ed04_i_escola = $escola"));
        $pdf->setfillcolor(215);
        $contp++;

        if ($turno != $ed15_c_nome) {
            $pdf->setfont('arial', 'B', 9);
            $pdf->cell(195, 5, $ed15_i_codigo == $ed57_i_turno ? "TURNO PRINCIPAL" : "TURNO ADICIONAL", 1, 1, "C", 1);
            $pdf->cell(35, 5, trim(pg_result($result1, $z, "ed15_c_nome")), 1, 0, "C", 1);

            if ($cldiasemana->numrows == 0) {
                $pdf->cell(195, 5, "Informe os dias lelivos desta escola", 1, 1, "C", 1);
            }

            $qb = 0;

            for ($x = 0; $x < $cldiasemana->numrows; $x++) {
                $contd++;
                db_fieldsmemory($result, $x);

                if ($x + 1 == $cldiasemana->numrows) {
                    $qb = 1;
                }
                $pdf->cell(32, 5, $ed32_c_descr, 1, $qb, "C", 1);
            }
        }

        $nomeSocLegenda = false;
        $turno = $ed15_c_nome;
        $pdf->setfillcolor(215);
        $pdf->setfont('arial', '', 7.5);
        $pdf->cell(35, 20, $ed08_c_descr . " - " . $ed17_h_inicio . " / " . $ed17_h_fim, 1, 0, "C", 1);
        $pdf->setfillcolor(255);
        $pdf->setfont('arial', '', 7.5);
        $qb = 2;

        for ($x = 0; $x < $cldiasemana->numrows; $x++) {
            if ($x + 1 == $cldiasemana->numrows) {
                $qb = 1;
            }

            $quadro = "Q" . $z . $x;
            db_fieldsmemory($result, $x);
            $ed20_i_codigo = '';

        $sql2 = "SELECT ed20_i_codigo,case when ed20_i_tiposervidor = 1 then cgmrh.z01_nome else cgmcgm.z01_nome end as z01_nome,
                case when ed20_i_tiposervidor = 1 then cgmsocial.z04_nomesocial else cgmsoc.z04_nomesocial end as z04_nomesocial,
                ed232_c_descr
            FROM regenciahorario
                inner join regencia on regencia.ed59_i_codigo = regenciahorario.ed58_i_regencia
                inner join rechumano on rechumano.ed20_i_codigo = regenciahorario.ed58_i_rechumano
                inner join disciplina on disciplina.ed12_i_codigo = regencia.ed59_i_disciplina
                inner join caddisciplina on  ed232_i_codigo = ed12_i_caddisciplina
                inner join turma on turma.ed57_i_codigo = regencia.ed59_i_turma
                inner join turmaserieregimemat on ed220_i_turma = ed57_i_codigo
                inner join serieregimemat on ed223_i_codigo = ed220_i_serieregimemat
                inner join serie on ed11_i_codigo = ed223_i_serie
                left join rechumanopessoal  on  rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
                left join rhpessoal  on  rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
                left join cgm as cgmrh on  cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
                left join cgmfisico as cgmsocial on cgmsocial.z04_numcgm = rhpessoal.rh01_numcgm
                left join rechumanocgm  on  rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
                left join cgm as cgmcgm on  cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm
                left join cgmfisico as cgmsoc on cgmsoc.z04_numcgm = rechumanocgm.ed285_i_cgm
                WHERE ed58_i_diasemana = $ed32_i_codigo
                and ed58_ativo is true
                AND ed58_i_periodo = $ed17_i_codigo
                AND ed220_i_codigo in ($ed220_i_codigo)
                AND ed57_i_escola = $escola
                AND ed223_i_serie = ed59_i_serie
    UNION

        SELECT ed20_i_codigo,
               case when ed20_i_tiposervidor = 1 then cgmrh.z01_nome else cgmcgm.z01_nome end as z01_nome,
               case when ed20_i_tiposervidor = 1 then cgmsocial.z04_nomesocial else cgmsoc.z04_nomesocial end as z04_nomesocial,
               ed232_c_descr
            FROM regenciahorariodiscsemreg
                 inner join regencia on regencia.ed59_i_codigo = regenciahorariodiscsemreg.ed175_regencia
                 left join rechumano on rechumano.ed20_i_codigo = regenciahorariodiscsemreg.ed175_rechumano
                 inner join disciplina on disciplina.ed12_i_codigo = regencia.ed59_i_disciplina
                 inner join caddisciplina on ed232_i_codigo = ed12_i_caddisciplina
                 inner join turma on turma.ed57_i_codigo = regencia.ed59_i_turma
                 inner join turmaserieregimemat on ed220_i_turma = ed57_i_codigo
                 inner join serieregimemat on ed223_i_codigo = ed220_i_serieregimemat
                 inner join serie on ed11_i_codigo = ed223_i_serie
                 left join rechumanopessoal on rechumanopessoal.ed284_i_rechumano = rechumano.ed20_i_codigo
                 left join rhpessoal on rhpessoal.rh01_regist = rechumanopessoal.ed284_i_rhpessoal
                 left join cgm as cgmrh on cgmrh.z01_numcgm = rhpessoal.rh01_numcgm
                 left join cgmfisico as cgmsocial on cgmsocial.z04_numcgm = rhpessoal.rh01_numcgm
                 left join rechumanocgm on rechumanocgm.ed285_i_rechumano = rechumano.ed20_i_codigo
                 left join cgm as cgmcgm on cgmcgm.z01_numcgm = rechumanocgm.ed285_i_cgm
                 left join cgmfisico as cgmsoc on cgmsoc.z04_numcgm = rechumanocgm.ed285_i_cgm
        WHERE ed175_diasemana = $ed32_i_codigo
          and ed175_ativo is true
          AND ed175_periodo = $ed17_i_codigo
          AND ed220_i_codigo in ($ed220_i_codigo)
          AND ed57_i_escola = $escola
          AND ed223_i_serie = ed59_i_serie
             ";
        $result2 = db_query($sql2);
        $linhas2 = pg_num_rows($result2);

            if ($linhas2 > 0) {
                db_fieldsmemory($result2, 0);
            $regente       = $z01_nome;
            $regenteSocial = $z04_nomesocial;

                if ($regente == null) {
                    $regente = "SEM PROFESSOR";
                }
                $disci = $ed232_c_descr;
                $cor = "red";
            } else {
                $regente = "";
                $disci = "";
                $cor = "green";
            }
            $posy = $pdf->getY();
            $posx = $pdf->getX();

            if ($professor == "" || $professor == $ed20_i_codigo || $linhas2 == 0) {
                $pdf->setfont('arial', 'B', 7.5);
                $iYinicial = $pdf->getY();
                $iXinicial = $pdf->getX();

                /**
                 * calculo para saber o número de linhas que a disciplina irá ocupar, se maior que 2 linhas, diminui fonte
                 */
                if ($pdf->NbLines(32, $disci) > 2) {
                    $pdf->setfont('arial', 'B', 7);
                }

                $pdf->MultiCell(32, 4, $disci, 0, 'C');

                $pdf->setX($iXinicial);
                $pdf->setfont('arial', '', 7.5);

                /**
                 * calculo para centralizar o nome do professor
                 */
                if (($pdf->GetY() - $iYinicial) < 10) {
                    $pdf->SetY($iYinicial + 7);
                }
                $pdf->setX($iXinicial);
                $pdf->setfont('arial', '', 6.5);
            if (!empty($regenteSocial) && $regenteSocial !== "" && !empty($regente)) {
                $nomeSocLegenda = true;
                $pdf->multiCell(32, 4, mb_strimwidth($regente, 0, 25) . " / *" . mb_strimwidth($regenteSocial, 0, 25), 0, $qb, "C", 1);
            } else {
                $pdf->multiCell(32, 4, mb_strimwidth($regente, 0, 50), 0, $qb, "C", 1);
            }

                $pdf->Rect($iXinicial, $iYinicial, 32, 20);
                $pdf->SetY($iYinicial + 20);
            } else {
                $pdf->cell(32, 20, "", 1, $qb, "C", 1);
            }

            if ($qb != 1) {
                $pdf->setY($posy);
                $pdf->setX($posx + 32);
            }

            $regente = "";
            $ed20_i_codigo = "";
        }
    }

    if (!empty($nomeSocLegenda)) {
    $pdf->setY(185);
    $pdf->setX(20);
    $pdf->setfont('arial', 'B', 7.5);
    $pdf->cell(10, 10, "Legenda : * Nome Social", 0, 1, "C", 1);
    }

}
$pdf->Output();
?>
