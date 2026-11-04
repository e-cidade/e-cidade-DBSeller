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


require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("libs/db_liborcamento.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_orcelemento_classe.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);

$clorcelemento = new cl_orcelemento;

//db_postmemory($HTTP_SERVER_VARS,2);
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script>

    function js_marca() {
        var org = '';
        var virg = '';
        var F = document.form1;
        if (document.form1.marca.value == 'Marca Todos') {
            var dis = true;
            document.form1.marca.value = 'Desmarca Todos';
        } else {
            var dis = false;
            document.form1.marca.value = 'Marca Todos';
        }
        for (i = 0; i < F.elements.length; i++) {
            if (F.elements[i].type == "checkbox") {
                F.elements[i].checked = dis;
                if (F.elements[i].id.substr(0, 6) == 'ultimo') {
                    org = org + virg + F.elements[i].value;
                    virg = '-';
                }
            }
        }

        parent.document.form1.<?=(isset($qvernivel) ? $qvernivel : "vernivel")?>.value = document.form1.nivel.value;
        parent.document.form1.<?=(isset($qorgaos) ? $qorgaos : "orgaos")?>.value = org;
    }

    function js_marcafilho(qpai) {

        if (document.form1.elements[qpai].checked)
            acao = true;
        else
            acao = false;

        qpai = qpai + '_';

        tam = document.form1.elements.length;

        for (i = 0; i < tam; i++) {
            obj = document.form1.elements[i].value;
            if (obj.indexOf(qpai) != -1) {
                document.form1.elements[i].checked = acao;
            }
        }

        var org = '';
        var virg = '';
        var F = document.form1;
        for (i = 0; i < F.elements.length; i++) {
            if (F.elements[i].type == "checkbox" && F.elements[i].checked) {
                if (F.elements[i].id.substr(0, 6) == 'ultimo') {
                    org = org + virg + F.elements[i].value;
                    virg = '-';
                }
            }
        }
        parent.document.form1.<?=(isset($qvernivel) ? $qvernivel : "vernivel")?>.value = document.form1.nivel.value;
        parent.document.form1.<?=(isset($qorgaos) ? $qorgaos : "orgaos")?>.value = org;
    }

    function js_marcapai(qpai) {

        xqpai = qpai + '_';

        tam = document.form1.elements.length;
        entrou = false;

        flag_projativ = false;
        cod_projativ = "";
        codigo = "";

        for (i = 0; i < tam; i++) {
            obj = document.form1.elements[i].value;
            if (document.form1.elements[i].checked) {
                vetor = obj.split("_");
                if (vetor.length == 7) {
                    flag_projativ = true;
                    cod_projativ = vetor.join("_");
                    break;
                }
            }
        }

        if (flag_projativ == false) {
            for (i = 0; i < tam; i++) {
                obj = document.form1.elements[i].value;
                if (obj.indexOf(xqpai) != -1) {
                    if (document.form1.elements[i].checked) {
                        if (codigo == "") {
                            codigo = obj.substr(0, 7);
                            if (codigo.substr(6, 1) == '_') {
                                // ser for divisor pega o proximo caracter
                                codigo = obj.substr(0, 8);
                            }
                        }

                        document.form1.elements[qpai].checked = true;
                        entrou = true;
                    }
                }
            }
            if (codigo != "") {
                for (i = 0; i < tam; i++) {
                    obj = document.form1.elements[i].value;
                    if (codigo == obj.substr(0, 7)) {
                        document.form1.elements[i].checked = true;
                        entrou = true;
                    }
                }
            }

        } else {
            for (i = 0; i < tam; i++) {
                obj = document.form1.elements[i].value;
                if (cod_projativ.indexOf(vetor.length) == obj.indexOf(vetor.length)) {
                    document.form1.elements[i].checked = true;
                    entrou = true;
                }
            }
        }

        if (!entrou) {
            document.form1.elements[qpai].checked = false;
        }

        var org = '';
        var virg = '';
        var F = document.form1;
        for (i = 0; i < F.elements.length; i++) {
            if (F.elements[i].type == "checkbox" && F.elements[i].checked) {
                if (F.elements[i].id.substr(0, 6) == 'ultimo') {
                    org = org + virg + F.elements[i].value;
                    virg = '-';
                }
            }
        }

        parent.document.form1.<?=(isset($qvernivel) ? $qvernivel : "vernivel")?>.value = document.form1.nivel.value;
        parent.document.form1.<?=(isset($qorgaos) ? $qorgaos : "orgaos")?>.value = org;
    }
</script>

</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<form name="form1" action="post">
<table border="1"  align="center" cellspacing="1" bgcolor="#CCCCCC">
 <tr>
    <td colspan="3" valign="middle">
      <input id="marca" type="button" value="Marca Todos" onclick="js_marca();return false" >
    </td>
  </tr>
<?php

$xnumero = substr($nivel, 0, 1);
if ($xnumero == 1) {
    $xtitulo = 'Nivel 1 - Órgao';
} elseif ($xnumero == 2) {
    $xtitulo = 'Nivel 2 - Unidade';
} elseif ($xnumero == 3) {
    $xtitulo = 'Nivel 3 - Função';
} elseif ($xnumero == 4) {
    $xtitulo = 'Nivel 4 - Subfunção';
} elseif ($xnumero == 5) {
    $xtitulo = 'Nivel 5 - Programa';
} elseif ($xnumero == 6) {
    $xtitulo = 'Nivel 6 - Projeto/Atividade';
} elseif ($xnumero == 7) {
    $xtitulo = 'Nivel 7 - Elemento';
} elseif ($xnumero == 8) {
    $xtitulo = 'Nivel 8 - Recurso';
} elseif ($xnumero == 9) {
    $xtitulo = 'Nivel 7 - Grupo de Natureza de Despesa';
}
?>
  <tr>
    <td colspan="3" align="center"><strong><?=$xtitulo?></strong><td>
  <tr>
<?php


$sel_orgaos = " o58_instit in (".str_replace('-', ',', $db_selinstit).") ";

if (substr($nivel, 1, 1) == 'B') {
    $nivela = substr($nivel, 0, 1);
    if ($nivela == 9) {
        $nivelb = 7;
        $anousu = db_getsession("DB_anousu");
        $res_grupo = $clorcelemento->sql_record($clorcelemento->sql_query_exercicio(
            $anousu,explode("-", $db_selinstit),
            null,
            " distinct (o56_codele), o56_elemento",
            "o56_elemento",
            "o56_anousu = $anousu"
        ));
        $virgula = "";
        $elemento = "";
        if ($clorcelemento->numrows > 0) {
            for ($ii = 0; $ii < $clorcelemento->numrows; $ii++) {
                db_fieldsmemory($res_grupo, $ii);
                $elemento .= $virgula . "'" . $o56_codele . "'";
                $virgula = ", ";
            }

            if (strlen(trim($elemento)) > 0) {
                $sel_orgaos .= " and o58_codele in (" . $elemento . ") ";
            }
        }
    } else {
        $nivelb = $nivela;
    }

    /**
     * Vi que tem um padrão no valor dos select(combobox) nos values informados.
     * E sempre é informado 8B quando selecionado RECURSO.
     * Vou manter esse padrão, e acrescentar o 8BC quando necessário filtrar recurso e complemento, ficando assim:
     *
     * 8B = Filtro por recurso
     * 8BC = Filtrar recurso e complemento
     */
    if (!in_array($nivel, ["8B", "8BC"])) {
        $result = db_dotacaosaldo($nivelb, 3, 2, true, $sel_orgaos, db_getsession("DB_anousu"));
    } else {
        $campos = "
         distinct orctiporec.o15_codigo,
         orctiporec.o15_complemento,
         orctiporec.o15_recurso,
         orctiporec.o15_descr,
         complementofonterecurso.o200_descricao
     ";
        $clorctiporec = new cl_orctiporec();

        $sSql = $clorctiporec->sql_query_fonte_recurso(null, $campos, " o15_recurso, o15_codigo ");

        if ($nivel === '8B') {
            $campos = "
             orctiporec.o15_recurso,
             orctiporec.o15_descr,
             array_to_string(array_accum(distinct o15_codigo), ',') as o15_codigo
           ";
            $groupBy = " group by o15_recurso, o15_descr";
            $sSql = $clorctiporec->sql_query_file(
                null,
                $campos,
                " o15_recurso ",
                 " true {$groupBy}"
            );
        }

        $result  = db_query($sSql);
    }

    for ($i = 0; $i < pg_numrows($result); $i++) {
        db_fieldsmemory($result, $i);
        //  db_criatabela($result);exit;
        if ($xnumero == 1) {
            $xnivel = db_formatar($o58_orgao, 'orgao');
            $descr = "{$xnivel} - {$o40_descr}";
            $xtitulo = 'Nivel 1 - Órgao';
        } elseif ($xnumero == 2) {
            $xnivel = db_formatar($o58_orgao, 'orgao') . db_formatar($o58_unidade, 'orgao');
            $descr = "{$xnivel} - {$o41_descr}";
            $xtitulo = 'Nivel 2 - Unidade';
        } elseif ($xnumero == 3) {
            $xnivel = $o58_funcao;
            $descr = "{$xnivel} - {$o52_descr}";
            $xtitulo = 'Nivel 3 - Função';
        } elseif ($xnumero == 4) {
            $xnivel = $o58_subfuncao;
            $descr = "{$xnivel} - {$o53_descr}";
            $xtitulo = 'Nivel 4 - Subfunção';
        } elseif ($xnumero == 5) {
            $xnivel = $o58_programa;
            $descr = "{$xnivel} - {$o54_descr}";
            $xtitulo = 'Nivel 5 - Programa';
        } elseif ($xnumero == 6) {
            $xnivel = $o58_projativ;
            $descr = "{$xnivel} - {$o55_descr}";
            $xtitulo = 'Nivel 6 - Projeto/Atividade';
        } elseif ($xnumero == 7) {
            $xnivel = $o58_elemento;
            $descr = "{$xnivel} - {$o56_descr}";
            $xtitulo = 'Nivel 7 - Elemento';
        } elseif ($xnumero == 8) {
            $xnivel = $o15_codigo;

            if ($nivel == "8B") {
                $descr = "$o15_recurso - $o15_descr ";
            } else {
                $descr = "$o15_recurso - $o15_descr - $o200_descricao";
            }
            $xtitulo = 'Nivel 8 - Recurso';
        } elseif ($xnumero == 9) {
            $xnivel = $o58_elemento;
            $descr = "{$xnivel} - {$o56_descr}";
            $xtitulo = 'Nivel 7 - Grupo de Natureza de Despesa';
        }

        ?>
            <tr>
                <td colspan="3">&nbsp;
                <td>
            <tr>
            <tr>
                <td colspan="3" valign="middle">
                    <input type="checkbox" id="ultimo_<?= $xnivel ?>" value="pai_<?= $xnivel ?>"
                           onclick="js_marcafilho('pai_<?= $xnivel ?>');" name="pai_<?= $xnivel ?>">
                    <strong><?= $descr ?></strong>
                </td>
            </tr>

        <?php
    }
} else {

	if ($nivel >= '1A') {
		$sql1 = "select
		             distinct o41_orgao,
		       	     o40_descr
		          from orcunidade
			       inner join orcorgao on o41_orgao = o40_orgao  and o40_anousu = ".db_getsession("DB_anousu")."
			       /*
			       inner join (
			       select distinct o58_orgao
			              from orcdotacao
			       where ".$sel_orgaos."
			           and o58_anousu = ".db_getsession("DB_anousu").") as x
			           on o40_orgao = o58_orgao
			       */
		         where o41_anousu = ".db_getsession("DB_anousu")."
	                       and o41_instit  in (".str_replace('-', ',', $db_selinstit).") order by o41_orgao";

		$result1 = db_query($sql1);
		for ($i1 = 0; $i1 < pg_numrows($result1); $i1 ++) {
			db_fieldsmemory($result1, $i1);
?>
  <tr>
    <td colspan="3">&nbsp;<td>
  <tr>
  <tr>
    <td colspan="3" valign="middle"><input type="checkbox" value="pai_<?=$o41_orgao?>" onclick="js_marcafilho('pai_<?=$o41_orgao?>');" name="pai_<?=$o41_orgao?>" id="<?=$nivel=='1A'?'ultimo_'.$o41_orgao:'primeiro_'.$o41_orgao?>" >&nbsp;&nbsp;<strong><?=$o41_orgao?></strong>&nbsp;&nbsp;-&nbsp;&nbsp;<strong><?=$o40_descr?></strong></td>
  </tr>
  <?php


			if ($nivel >= '2A') {
				$sql2 = "select 	distinct
				                 	o41_unidade,
				     	 		o41_descr
				           from orcunidade
					        inner join orcorgao on o41_orgao = $o41_orgao
					                           and o41_anousu = ".db_getsession("DB_anousu");
				$result2 = db_query($sql2);
				for ($i2 = 0; $i2 < pg_numrows($result2); $i2 ++) {
					db_fieldsmemory($result2, $i2);
?>
  <tr>
    <td>
        <img src="imagens/alinha.gif" width="15">
      <input type="checkbox"  onclick="js_marcapai('pai_<?=$o41_orgao?>');" value="pai_<?=$o41_orgao?>_<?=$o41_unidade?>" name="pai_<?=$o41_orgao?>_<?=$o41_unidade?>" id="<?=$nivel=='2A'?'ultimo_'.$o41_unidade:'primeiro_'.$o41_unidade?>">&nbsp;&nbsp;<?=$o41_unidade?>&nbsp;&nbsp;-&nbsp;&nbsp;<?=$o41_descr?></td>
  </tr>
    <?php


					if ($nivel >= '3A') {
						$sql3 = "select 	distinct
						         	          	o52_funcao,
						     	 			o52_descr
						                 from orcdotacao
						   	              inner join orcfuncao on o58_funcao = o52_funcao
							         where o58_orgao 	= $o41_orgao
							           and o58_unidade 	= $o41_unidade
							           and o58_anousu = ".db_getsession("DB_anousu");
						$result3 = db_query($sql3);
						for ($i3 = 0; $i3 < pg_numrows($result3); $i3 ++) {
							db_fieldsmemory($result3, $i3);
?>
  <tr>
    <td>
        <img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
      <input type="checkbox"  onclick="js_marcapai('pai_<?=$o41_orgao?>_<?=$o41_unidade?>');" value="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>" name="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>" id="<?=$nivel=='3A'?'ultimo_'.$o52_funcao:'primeiro_'.$o52_funcao?>">&nbsp;&nbsp;<?=$o52_funcao?>&nbsp;&nbsp;-&nbsp;&nbsp;<?=$o52_descr?></td>
  </tr>
  <?php


							if ($nivel >= '4A') {
								$sql4 = "  select	distinct
								                 	o53_subfuncao,
								     	 		o53_descr
								                 from orcdotacao
								   	              inner join orcsubfuncao on o58_subfuncao = o53_subfuncao
									         where o58_orgao 	= $o41_orgao
									           and o58_unidade 	= $o41_unidade
										   and o58_funcao       = $o52_funcao
									           and o58_anousu = ".db_getsession("DB_anousu");
								$result4 = db_query($sql4);
								for ($i4 = 0; $i4 < pg_numrows($result4); $i4 ++) {
									db_fieldsmemory($result4, $i4);
?>
  <tr>
    <td>
        <img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
      <input type="checkbox"  onclick="js_marcapai('pai_<?=$o41_orgao?>');" value="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>" name="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>" id="<?=$nivel=='4A'?'ultimo_'.$o53_subfuncao:'primeiro_'.$o53_subfuncao?>" >&nbsp;&nbsp;<?=$o53_subfuncao?>&nbsp;&nbsp;-&nbsp;&nbsp;<?=$o53_descr?></td>
  </tr>
  <?php


									if ($nivel >= '5A') {
										$sql5 = "  select	distinct
										                 	o54_programa,
										     	 		o54_descr
										                 from orcdotacao
										   	              inner join orcprograma on o58_programa = o54_programa
												                          and o54_anousu = ".db_getsession("DB_anousu")."
											         where o58_orgao 	= $o41_orgao
											           and o58_unidade 	= $o41_unidade
												   and o58_funcao       = $o52_funcao
												   and o58_subfuncao    = $o53_subfuncao
											           and o58_anousu = ".db_getsession("DB_anousu");
										$result5 = db_query($sql5);
										for ($i5 = 0; $i5 < pg_numrows($result5); $i5 ++) {
											db_fieldsmemory($result5, $i5);
?>
  <tr>
    <td>
        <img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
      <input type="checkbox"  onclick="js_marcapai('pai_<?=$o41_orgao?>');" value="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>_<?=$o54_programa?>" name="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>_<?=$o54_programa?>" id="<?=$nivel=='5A'?'ultimo_'.$o54_programa:'primeiro_'.$o54_programa?>" >&nbsp;&nbsp;<?=$o54_programa?>&nbsp;&nbsp;-&nbsp;&nbsp;<?=$o54_descr?></td>
  </tr>
  <?php


											if ($nivel >= '6A') {
												$sql6 = "  select	distinct
												                 	o55_projativ,
												     	 		o55_descr
												                 from orcdotacao
												   	              inner join orcprojativ on o58_projativ = o55_projativ
																and o55_anousu = ".db_getsession("DB_anousu")."
													         where o58_orgao 	= $o41_orgao
													           and o58_unidade 	= $o41_unidade
														   and o58_funcao       = $o52_funcao
														   and o58_subfuncao    = $o53_subfuncao
														   and o58_programa     = $o54_programa
													           and o58_anousu = ".db_getsession("DB_anousu");
												$result6 = db_query($sql6);
												for ($i6 = 0; $i6 < pg_numrows($result6); $i6 ++) {
													db_fieldsmemory($result6, $i6);
?>
  <tr>
    <td>
        <img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
      <input type="checkbox"  onclick="js_marcapai('pai_<?=$o41_orgao?>');" value="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>_<?=$o54_programa?>_<?=$o55_projativ?>" name="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>_<?=$o54_programa?>_<?=$o55_projativ?>" id="<?=$nivel=='6A'?'ultimo_'.$o55_projativ:'primeiro_'.$o55_projativ?>">&nbsp;&nbsp;<?=$o55_projativ?>&nbsp;&nbsp;-&nbsp;&nbsp;<?=$o55_descr?></td>
  </tr>
  <?php


													if ($nivel >= '7A') {
														$sql7 = "  select	distinct
														                        o56_codele,
														                 	o56_elemento,
														     	 		o56_descr
														                 from orcdotacao
														   	              inner join orcelemento on o58_codele = o56_codele and o56_anousu = o58_anousu
															         where o58_orgao 	= $o41_orgao
															           and o58_unidade 	= $o41_unidade
																   and o58_funcao       = $o52_funcao
																   and o58_subfuncao    = $o53_subfuncao
																   and o58_programa     = $o54_programa
																   and o58_projativ     = $o55_projativ
															           and o58_anousu = ".db_getsession("DB_anousu");
														$result7 = db_query($sql7);
														for ($i7 = 0; $i7 < pg_numrows($result7); $i7 ++) {
															db_fieldsmemory($result7, $i7);
?>
  <tr>
    <td><img src="imagens/alinha.gif" width="15">
        <img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
      <input type="checkbox"  onclick="js_marcapai('pai_<?=$o41_orgao?>');" value="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>_<?=$o54_programa?>_<?=$o55_projativ?>_<?=$o56_elemento?>" name="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>_<?=$o54_programa?>_<?=$o55_projativ?>_<?=$o56_elemento?>" id="<?=$nivel=='7A'?'ultimo_'.$o56_elemento:'primeiro_'.$o56_elemento?>">&nbsp;&nbsp;<?=$o56_elemento?>&nbsp;&nbsp;-&nbsp;&nbsp;<?=$o56_descr?></td>
  </tr>
  <?php


															if ($nivel >= '8A') {
																$sql8 = "  select	distinct
																                 	o15_codigo,
																     	 		o15_descr
																                 from orcdotacao
																   	              inner join orctiporec on o58_codigo = o15_codigo
																	         where o58_orgao 	= $o41_orgao
																	           and o58_unidade 	= $o41_unidade
																		   and o58_funcao       = $o52_funcao
																		   and o58_subfuncao    = $o53_subfuncao
																		   and o58_programa     = $o54_programa
																		   and o58_projativ     = $o55_projativ
																		   and o58_codele       = $o56_codele
																		   and o58_anousu = ".db_getsession("DB_anousu");
																$result8 = db_query($sql8);
																for ($i8 = 0; $i8 < pg_numrows($result8); $i8 ++) {
																	db_fieldsmemory($result8, $i8);
?>
  <tr>
    <td><img src="imagens/alinha.gif" width="15">
        <img src="imagens/alinha.gif" width="15">
        <img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
	<img src="imagens/alinha.gif" width="15">
      <input type="checkbox"  onclick="js_marcapai('pai_<?=$o41_orgao?>');" value="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>_<?=$o54_programa?>_<?=$o55_projativ?>_<?=$o56_elemento?>_<?=$o15_codigo?>" name="pai_<?=$o41_orgao?>_<?=$o41_unidade?>_<?=$o52_funcao?>_<?=$o53_subfuncao?>_<?=$o54_programa?>_<?=$o55_projativ?>_<?=$o56_elemento?>_<?=$o15_codigo?>" id="<?=$nivel=='8A'?'ultimo_'.$o15_codigo:'primeiro_'.$o15_codigo?>">&nbsp;&nbsp;<?=$o15_codigo?>&nbsp;&nbsp;-&nbsp;&nbsp;<?=$o15_descr?></td>
  </tr>
<?php


																}
															}

														}
													}

												}
											}

										}
									}

								}
							}

						}
					}

				}
			}

		}
	}
}
?>
<input type="hidden" value="<?=$nivel?>" name="nivel" id="nivel">
</table>
</form>
</body>
</html>
<script type="text/javascript">
(function() {
  var query = frameElement.getAttribute('name').replace('IF', ''), input = document.querySelector('input[value="Fechar"]');
  input.onclick = parent[query] ? parent[query].hide.bind(parent[query]) : input.onclick;
})();
</script>
