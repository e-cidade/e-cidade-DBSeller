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
require_once (modification("libs/db_stdlib.php"));
require_once (modification("libs/db_conecta.php"));
require_once (modification("libs/db_sessoes.php"));
require_once (modification("libs/db_usuariosonline.php"));
require_once (modification("libs/db_liborcamento.php"));
require_once (modification("libs/db_sql.php"));

// pesquisa a conta mae da receita

$tipo_mesini = 1;
$tipo_mesfim = 1;

db_postmemory($_POST);

$xinstit = split("-", $db_selinstit);
$listaInstituicoes = str_replace('-', ', ', $db_selinstit);
$resultinst = db_query("select codigo,nomeinstabrev from db_config where codigo in (". $listaInstituicoes .") ");
$descr_inst = '';
$xvirg = '';
for ($xins = 0; $xins < pg_num_rows($resultinst); $xins++) {
    db_fieldsmemory($resultinst, $xins);
    $descr_inst .= $xvirg.$nomeinstabrev ;
    $xvirg = ', ';
}
$nivela = substr($nivel, 0, 1);
if ($nivela == 1) {
    $tipo = 'ÓRGÃO';
} elseif ($nivela == 2) {
    $tipo = 'UNIDADE';
} elseif ($nivela == 3) {
    $tipo = 'FUNÇÃO';
} elseif ($nivela == 4) {
    $tipo = 'SUBFUNÇÃO';
} elseif ($nivela == 5) {
    $tipo = 'PROGRAMA';
} elseif ($nivela == 6) {
    $tipo = 'PROJ/ATIV';
} elseif ($nivela == 7) {
    $tipo = 'ELEMENTO';
} elseif ($nivela == 8) {
    $tipo = 'RECURSO';
}

$anousu = db_getsession("DB_anousu");

$clselorcdotacao = new cl_selorcdotacao();
$clselorcdotacao->setDados($orgaos);
// passa os parametros vindos da func_selorcdotacao_abas.php
$sele_work  = $clselorcdotacao->getDados();
$sele_work .= ' and w.o58_instit in ('. $listaInstituicoes .') ';

if (substr($nivel, 1, 1) == 'A') {
    $nivelb = 2;
} else {
    $nivelb = 3;
}

$result = db_dotacaosaldo($nivela, $nivelb, 2, true, $sele_work);

$pdf = new \ECidade\Pdf\Pdf();
$pdf->init(false);
$pdf->addTitulo("TOTAL DO ORCAMENTO");
$pdf->addTitulo("POR ".$tipo);
$pdf->addTitulo("EXERCICIO: ". $anousu);
$pdf->addTitulo("INSTITUIÇÕES : ".$descr_inst);

$pdf->AliasNbPages();
$total = 0;
$pdf->setfillcolor(235);
$pdf->setfont('arial', 'b', 8);
$troca = 1;
$alt = 4;
$total = 0;

$pagina = 1;

$wLinha = 192;
$wValor = 30;
$wCodigo = 25;

$tamanhoPorNivel = [
    '6A' => 40,
    '7A' => 45,
    '8A' => 50,
];
if (array_key_exists($nivel, $tamanhoPorNivel)) {
    $wCodigo = $tamanhoPorNivel[$nivel];
}

$wLinha -= $wValor;
$wLinha -= $wCodigo;

for ($i = 0; $i < pg_num_rows($result); $i++) {
    db_fieldsmemory($result, $i);

    $dado = db_utils::fieldsMemory($result, $i);

    if ($pdf->gety()>$pdf->getH()-30 || $pagina ==1) {
        $pagina = 0;
        $pdf->addpage();
        $pdf->setfont('arial', 'b', 7);

        $pdf->cell($wCodigo, $alt, "CÓDIGO", 1, 0, "L", 0);
        $pdf->cell($wLinha, $alt, "E S P E C I F I C A Ç Ã O", 1, 0, "L", 0);
        $pdf->cell($wValor, $alt, "VALOR ORÇADO", 1, 1, "R", 0);
    }

    $o58_valor = $dado->dot_ini;

    $descricao = '';
    $codigo = '';
    if ($dado->o58_orgao != 0) {
        $descricao = $dado->o40_descr;
        $codigo .= db_formatar($dado->o58_orgao, 's', '0', 2, 'e');
    }
    if ($dado->o58_unidade != 0) {
        $descricao = $dado->o41_descr;
        if (substr($nivel, 1, 1) == 'A') {
            $codigo .= '.';
        }
        $codigo .= db_formatar($dado->o58_unidade, 's', '0', 2, 'e');
    }
    if ($dado->o58_funcao != 0) {
        $descricao = $dado->o52_descr;
        if (substr($nivel, 1, 1) == 'A') {
            $codigo .= '.';
        }
        $codigo .= db_formatar($dado->o58_funcao, 's', '0', 2, 'e');
    }
    if ($dado->o58_subfuncao != 0) {
        $descricao = $dado->o53_descr;
        if (substr($nivel, 1, 1) == 'A') {
            $codigo .= '.';
        }
        $codigo .= db_formatar($dado->o58_subfuncao, 's', '0', 4, 'e');
    }
    if ($dado->o58_programa != 0) {
        $descricao = $dado->o54_descr;
        if (substr($nivel, 1, 1) == 'A') {
            $codigo .= '.';
        }
        $codigo .= db_formatar($dado->o58_programa, 's', '0', 2, 'e');
    }
    if ($dado->o58_projativ != 0) {
        $descricao = $dado->o55_descr;
        if (substr($nivel, 1, 1) == 'A') {
            $codigo .= '.';
        }
        $codigo .= db_formatar($dado->o58_projativ, 's', '0', 4, 'e');
    }
    if ($dado->o58_elemento != 0) {
        $descricao = $dado->o56_descr;
        if (substr($nivel, 1, 1) == 'A') {
            $codigo .= '.';
        }
        $codigo .= $dado->o58_elemento;
    }
    if ($dado->o58_codigo != 0) {
        $descricao = $dado->descricao;
        if (substr($nivel, 1, 1) == 'A') {
            $codigo .= '.';
        }
        $codigo_recurso = $dado->gestao;
        $codigo .= db_formatar($codigo_recurso, 's', '0', 4, 'e');
    }

 //$tipo_nivel = 7;
 // 1 = orgao
 // 2 = unidade
 // 3 = funcao
 // 4 = subfuncao
 // 5 = programa
 // 6 = projeto/atividade
 // 7 = elemento

    $pdf->setfont('arial', '', 6);

    $pdf->cell($wCodigo, $alt, $codigo, 0, 0, "L", 0);
    $pdf->cell($wLinha, $alt, $descricao, 0, 0, "L", 0);
    $pdf->cell($wValor, $alt, db_formatar($o58_valor, 'f'), 0, 1, "R", 0);

    $total += $o58_valor;
}
$pdf->setfont('arial', 'b', 7);
$pdf->ln(3);

$pdf->cell(162, $alt, 'T O T A L', 0, 0, "R", 0);
$pdf->cell(30, $alt, db_formatar($total, 'f'), 0, 1, "R", 0);

$pdf->Output('I');

