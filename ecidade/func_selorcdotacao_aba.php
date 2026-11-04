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

require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("libs/db_liborcamento.php"));
require_once(modification("dbforms/db_funcoes.php"));

$cldb_config = new cl_db_config;

parse_str($_SERVER["QUERY_STRING"]);
db_postmemory($_SERVER);

$clorcdotacao   = new cl_orcdotacao;
$clorcelemento = new cl_orcelemento;
$clorctiporec  = new cl_orctiporec;
$clempresto  = new cl_empresto();

$anosFiltro = [];
$anouso = db_getsession("DB_anousu");
if (isset($restos) && $restos == 'true') {
    $sqlRAP = $clempresto->sql_query_empenho(
        null,
        null,
        "distinct(empempenho.e60_anousu)",
        '1',
        'e91_anousu = ' . $anouso
    );
    $resultRAP = db_query($sqlRAP);

    while ($ano = pg_fetch_array($resultRAP)) {
        $anosFiltro[] = $ano['e60_anousu'];
    }
}
$anosFiltro[] = $anouso;

// recebe a variavel db_input_retorno com o nome do objeto que será atualizado com os dados selecionados
// se for prefeitura, traz todas as informações, caso contrario traz informações da instituição
if (isset($instit) && trim(@$instit)=="") {
     $usa_instit = false;
     $rr = $cldb_config->sql_record($cldb_config->sql_query_file(db_getsession("DB_instit"), "prefeitura"));
    if ($cldb_config->numrows > 0) {
         db_fieldsmemory($rr, 0);
        if ($prefeitura=='t') {
             $usa_instit = false;
        } else {
            $usa_instit = true;
        }
    }

     $array_instit = [];
     $sep = '';
     $instit = '';
    if ($usa_instit == true) {
         $instit= db_getsession("DB_instit");
        // recebeu instituição
         $array_instit =explode(',', $instit);
    } else {
       // todas instituições
        $rr = $cldb_config->sql_record($cldb_config->sql_query_file(null, "codigo"));
        if ($cldb_config->numrows>0) {
            for ($linha=0; $linha<$cldb_config->numrows; $linha++) {
                 db_fieldsmemory($rr, $linha);
                 $instit .= $sep.$codigo;
                 $sep = ',';
            }
        }
    }
}

$instit = str_replace("-", ",", @$instit);
$instit = (!isset($instit)||trim($instit)=='')?'NULL':$instit;
$array_instit =explode(',', $instit);
$sel_orgaos = " o58_instit in ($instit) ";

$porgao   = "";
$punidade = "";
$pfuncao  = "";
$psubfuncao = "";
$pprograma   = "";
$pprojativ   = "";
$pelemento   = "";
$precurso    = "";
$plocalizadorgastos = "";

$ploaespecificacao = "";
// caso seja informado dotação na tela, o sistema pesquisa a dotação
if (isset($codigo_dot) && $codigo_dot!="") {
    $res = db_dotacaosaldo(8, 2, 3, true, "o58_coddot=$codigo_dot", $anouso, $anouso .'-01-01', $anouso .'-01-01');
    if (pg_num_rows($res)>0) {
        db_fieldsmemory($res, 0);
        $porgao   = $o58_orgao;
        $punidade = $o58_unidade;
        $pfuncao  = $o58_funcao;
        $psubfuncao = $o58_subfuncao;
        $pprograma  = $o58_programa;
        $pprojativ  = $o58_projativ;
        $pelemento  = $o58_elemento;
        $precurso   = $gestao;
        $plocalizadorgastos = $o58_localizadorgastos;
    }
}

// seleciona os orgaos que o usuario tem permissao
$clpermusuario_dotacao =  new cl_permusuario_dotacao(
    db_getsession('DB_anousu'),
    db_getsession('DB_id_usuario'),
    null,
    null,
    null,
    '',
    $instit
);

$orgaos_liberados = [];

if ($clpermusuario_dotacao->sql!="") {
    $result = db_query($clpermusuario_dotacao->orgaos);
    if (pg_num_rows($result)>0) {
        for ($x=0; $x < pg_num_rows($result); $x++) {
              db_fieldsmemory($result, $x);
              $orgaos_liberados[$o40_orgao]=$o40_orgao;
        }
    }
}

?> <html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/classes/DBViewFiltroRecursos.classe.js">
    </script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
    <style>
    table.tableOpcoesFiltros {
        margin-left: auto;
        margin-right: auto;
        background-color: #EEE;
        border: 1px solid threedshadow;
        padding: 15;
    }

    .divFiltro {
        position: absolute;
        visibility: hidden;
        width: 100%;
    }

    table.tableRegistros {
        margin-left: auto;
        margin-right: auto;
        border: 1px solid threedshadow;
        background-color: #EEE;
        border-spacing: 0;
        min-width: 850px;
    }

    table.tableRegistros tr:hover {
        background: #D7E4EA !important;
    }

    table.tableRegistros th {
        background-color: #CCC;
        height: 30px;
    }

    table.tableRegistro td {
        height: 30px;
    }

    .tdCheck {
        width: 20px;
        text-align: center;
        border-bottom: 1px solid threedshadow;
    }

    .tdId {
        width: 35px;
        text-align: center;
        border-bottom: 1px solid threedshadow;
    }

    .tdId2 {
        width: 140px;
        text-align: center;
        border-bottom: 1px solid threedshadow;
    }

    .tdDescricao {
        min-width: 250px;
        text-align: "left";
        font-weight: bold;
        border-bottom: 1px solid threedshadow;
    }

    .tdCheckAgrupador {
        width: 20px;
        text-align: center;
        border-left: 1px solid threedshadow;
        border-bottom: 1px solid threedshadow;
        background-color: #FFF;
    }

    .tdDescricaoAgrupador {
        min-width: 250px;
        text-align: "left";
        font-weight: bold;
        border-bottom: 1px solid threedshadow;
        background-color: #FFF;
    }
    </style>
</head>

<body>
    <div class="div-container">
        <form name="form1" method="post">
            <?php
            db_input("db_selinstit", 10, 0, true, "hidden", 3);
            if (isset($desdobramento) && $desdobramento == true) {
                db_input("desdobramento", 10, 0, true, "hidden", 3);
            }
            ?>
            <table class="tableOpcoesFiltros">
                <tbody>
                    <tr>
                        <td>
                            <input type="button"
                                   id="geralmarca"
                                   value="Marcar Geral"
                                   onclick="js_marca_geral();">
                        </td>
                        <td style="text-align:right;">
                            <?php db_ancora("Dotação", "pesquisaDotacao();", 1); ?>
                            <input type='text'
                                   id='codigo_dot'
                                   name='codigo_dot'
                                   value='<?=(isset($codigo_dot)?$codigo_dot:"")?>''
                                   size='8'>
                            <input type='submit' id='pesquisa_dot' name='pesquisa_dot' value='Seleciona'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="tdOpcaoFiltros">
                            <input type="button"
                                   id="orgaos"
                                   value="Orgãos"
                                   onclick="js_marca_orgaos('orgao');return false">

                            <input type="button"
                                   id="unidade"
                                   value="Unidade"
                                   onclick="js_marca_orgaos('unidade');return false">

                            <input type="button"
                                   id="funcao"
                                   value="Função"
                                   onclick="js_marca_orgaos('funcao');return false">

                            <input type="button"
                                   id="subfuncao"
                                   value="Sub-Função"
                                   onclick="js_marca_orgaos('subfuncao');return false">

                            <input type="button"
                                   id="programa"
                                   value="Programa"
                                   onclick="js_marca_orgaos('programa');return false">

                            <input type="button"
                                   id="prjativ"
                                   value="Proj/Atividade"
                                   onclick="js_marca_orgaos('projativ');return false">

                            <input type="button"
                                   id="elemento"
                                   value="Elemento"
                                   onclick="js_marca_orgaos('elemento');return false">
                            <?php
                            if (isset($desdobramento) && $desdobramento==true) {
                                 echo "<input type='button'
                                              id='desdobramento'
                                              value='Desdobramento'
                                              onclick='js_marca_orgaos(\"desdobramento\");return false'>";
                            }
                            ?>
                            <input type="button"
                                   id="recurso"
                                   value="Recurso"
                                   onclick="js_marca_orgaos('recurso');return false">

                            <input type="button"
                                   id="localizadorgastos"
                                   value="Localizador de Gastos"
                                   onclick="js_marca_orgaos('localizadorgastos');return false">
                        </td>
                    </tr>
                </tbody>
            </table>
        </form>

        <div id='divOrgao' class='divFiltro'>
            <table class='tableRegistros'>
                <thead>
                    <tr>
                        <th colspan="3">Orgãos </th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right">
                            <input type="button"
                                   id="marcarOrgaos"
                                   value="Marcar Todos"
                                   onclick="js_marca_todos(this);">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <form name="formorgao" action="post">
                    <?php
                    $result = $clorcdotacao->sql_record(
                        $clorcdotacao->sql_query(
                            null,
                            null,
                            " distinct on(o58_orgao) o58_orgao,orcorgao.o40_descr",
                            "o58_orgao, o58_anousu DESC",
                            " $sel_orgaos "
                        )
                    );
                    for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                        db_fieldsmemory($result, $i);
                             $checked = ($porgao==$o58_orgao?"checked":"");

                        $disabled = "disabled";
                        $permissao = "<font color=blue>(Sem Permissão)</font>";
                        if (isset($orgaos_liberados[$o58_orgao]) || (isset($restos) && $restos == 'true')) {
                            $disabled = "";
                            $permissao = "";
                        }
                         $linhaHtml = "<tr>
                                        <td class ='tdCheck'>
                                          <input type='checkbox'
                                                 name='orgao_{$o58_orgao}'
                                                 value='{$o58_orgao}'
                                                 onclick='marcarLinha(this)'
                                                 {$checked}
                                                 {$disabled}>
                                        </td>
                                        <td class ='tdId'>
                                        {$o58_orgao}</td>
                                        <td class ='tdDescricao'>
                                          {$o40_descr}
                                          {$permissao}
                                        </td>
                                      </tr>";
                        echo $linhaHtml;
                    }
                    ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id="divUnidade" class='divFiltro'>
            <table class='tableRegistros'>
                <thead>
                    <tr>
                        <th colspan="5">Unidades</th>
                    </tr>
                    <tr>
                        <th colspan="5" style="text-align: right">
                            <input type="button"
                                   id="marcarUnidades"
                                   value="Marcar Todos"
                                   onclick="js_marca_todos(this);">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <form name="formunidade" action="post">
                    <?php
                      $sql = $clorcdotacao->sql_query(
                          null,
                          null,
                          " distinct on (orcdotacao.o58_orgao,orcdotacao.o58_unidade)
                          orcdotacao.o58_orgao,
                          orcorgao.o40_descr,
                          orcdotacao.o58_unidade,
                          orcunidade.o41_descr",
                          "orcdotacao.o58_orgao,orcdotacao.o58_unidade,orcdotacao.o58_anousu DESC",
                          " orcdotacao.o58_anousu in (" . implode(', ', $anosFiltro) . ") and $sel_orgaos "
                      );
                      $result = $clorcdotacao->sql_record($sql);
                      for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                          db_fieldsmemory($result, $i);

                          $checked = ($punidade==$o58_unidade?"checked":"");

                          $linhaHtml = "<tr>
                                            <td class='tdCheck'>
                                                <input type='checkbox'
                                                       name='unidade_{$o58_orgao}_{$o58_unidade}'
                                                       value='{$o58_orgao}_{$o58_unidade}'
                                                       {$checked}
                                                       onclick='marcarLinha(this)'>
                                            </td>
                                            <td class='tdId'>{$o58_orgao}</td>
                                            <td class='tdDescricao'>{$o40_descr}</td>
                                            <td class='tdId'>{$o58_unidade}</td>
                                            <td class='tdDescricao'>{$o41_descr}</td>
                                        <tr>";
                          echo $linhaHtml;
                      }
                        ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id="divFuncao" class='divFiltro'>
            <table class="tableRegistros">
                <thead>
                <tr>
                    <th colspan="3">Função</th>
                </tr>
                <tr>
                    <th colspan="3" style="text-align: right">
                        <input type="button"
                               id="marcarFuncoes"
                               value="Marcar Todos"
                               onclick="js_marca_todos(this);">
                    </th>
                </tr>
                </thead>
                <tbody>
                    <form name="formfuncao" action="post">
                    <?php
                        $sQuery  = " o58_anousu in (" . implode(', ', $anosFiltro) . ") and $sel_orgaos ";
                        $result = $clorcdotacao->sql_record(
                            $clorcdotacao->sql_query(
                                null,
                                null,
                                " distinct o58_funcao,orcfuncao.o52_descr",
                                "o58_funcao",
                                $sQuery
                            )
                        );

                        for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                            db_fieldsmemory($result, $i);

                            $checked = ($pfuncao==$o58_funcao?"checked":"");

                            $linhaHtml = "<tr>
                                              <td class='tdCheck'>
                                                  <input type='checkbox'
                                                         name='funcao_{$o58_funcao}'
                                                         value='{$o58_funcao}'
                                                         {$checked}
                                                         onclick='marcarLinha(this)'>
                                              </td>
                                              <td class='tdId'> {$o58_funcao} </td>
                                          <td class='tdDescricao'>{$o52_descr}</td>
                                          <tr>";
                            echo $linhaHtml;
                        }
                        ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id='divSubFuncao' class='divFiltro'>
            <table class="tableRegistros">
                <thead>
                    <tr>
                        <th colspan="3">Sub-Função</th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right">
                            <input type="button"
                                   id="marcarSubFuncoes"
                                   value="Marcar Todos"
                                   onclick="js_marca_todos(this);">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <form name="formsubfuncao" action="post">
                    <?php
                        $sql = $clorcdotacao->sql_query(
                            null,
                            null,
                            " distinct o58_subfuncao,orcsubfuncao.o53_descr",
                            "o58_subfuncao",
                            " o58_anousu in(" . implode(', ', $anosFiltro) . ") and $sel_orgaos "
                        );
                        $result = $clorcdotacao->sql_record($sql);
                        for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                            db_fieldsmemory($result, $i);

                            $checked = ($psubfuncao==$o58_subfuncao?"checked":"");

                            $linhaHtml = "<tr>
                                              <td class='tdCheck'>
                                                <input type='checkbox'
                                                       name='subfuncao_{$o58_subfuncao}'
                                                       value='{$o58_subfuncao}'
                                                      {$checked}
                                                      onclick='marcarLinha(this)'>
                                              </td>
                                              <td class='tdId'>{$o58_subfuncao}</td>
                                              <td class='tdDescricao'>{$o53_descr}</td>
                                          <tr>";
                            echo $linhaHtml;
                        }
                        ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id='divPrograma' class='divFiltro'>
            <table class="tableRegistros">
                <thaed>
                    <tr>
                        <th colspan="3">Programa</th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right">
                            <input type="button"
                                   id="marcarProgramas"
                                   value="Marcar Todos"
                                   onclick="js_marca_todos(this);">
                       </th>
                    </tr>
                </thead>
                <tbody>
                    <form name="formprograma" action="post">
                    <?php
                    $sql = $clorcdotacao->sql_query(
                        null,
                        null,
                        " distinct on(o58_programa) o58_programa,orcprograma.o54_descr",
                        "o58_programa, o58_anousu DESC",
                        " o58_anousu in(" . implode(', ', $anosFiltro) . ") and $sel_orgaos "
                    );
                    $result = $clorcdotacao->sql_record($sql);
                    for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                        db_fieldsmemory($result, $i);
                        $checked = ($pprograma==$o58_programa?"checked":"");

                        $linhaHtml = "<tr>
                                        <td class='tdCheck'>
                                            <input type='checkbox'
                                                   name='programa_{$o58_programa}'
                                                   value='{$o58_programa}'
                                                   {$checked}
                                                   onclick='marcarLinha(this)'>
                                        </td>
                                        <td class='tdId'> {$o58_programa}</td>
                                        <td class='tdDescricao'>{$o54_descr}</td>
                                      <tr>";
                        echo $linhaHtml;
                    }
                    ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id='divProjAtiv' class='divFiltro'>
            <table class="tableRegistros">
                <thead>
                    <tr>
                        <th colspan="3">Projeto/Atividade</th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right">
                            <input type="button"
                                   id="marcarProjAtiv"
                                   value="Marcar Todos"
                                   onclick="js_marca_todos(this);">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <form name="formprojativ" action="post">
                    <?php
                    $sql = $clorcdotacao->sql_query(
                        null,
                        null,
                        " distinct on(o58_projativ) o58_projativ,orcprojativ.o55_descr",
                        "o58_projativ,o58_anousu DESC",
                        " o58_anousu in(" . implode(', ', $anosFiltro) . ") and $sel_orgaos "
                    );
                    $result = $clorcdotacao->sql_record($sql);
                    for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                        db_fieldsmemory($result, $i);

                        $checked = ($pprojativ==$o58_projativ?"checked":"");
                        $linhaHtml = "<tr>
                                         <td class='tdCheck'>
                                             <input type='checkbox'
                                                    name='projativ_{$o58_projativ}'
                                                    value='{$o58_projativ}'
                                                    {$checked}
                                                    onclick='marcarLinha(this)'>
                                         </td>
                                         <td class='tdId'> {$o58_projativ} </td>
                                         <td class='tdDescricao'>{$o55_descr}</td>
                                      <tr>";

                        echo $linhaHtml;
                    }
                    ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id='divElemento' class='divFiltro'>
            <table class="tableRegistros">
                <tr>
                    <th colspan="5">Elemento</th>
                </tr>
                <tr>
                    <th colspan="5" style="text-align: right">
                        <input type="button"
                               id="marcarElementos"
                               value="Marcar Todos"
                               onclick="js_marca_todos(this);">
                    </th>
                </tr>
                <tbody>
                    <form name="formele" action="post">
                    <?php
                        $ind = 0;
                        $sql = $clorcdotacao->sql_query(
                            null,
                            null,
                            " distinct on(o56_elemento) o56_elemento, o56_codele,orcelemento.o56_descr",
                            "o56_elemento, o58_anousu DESC",
                            " o58_anousu in(" . implode(', ', $anosFiltro) . ") and $sel_orgaos "
                        );
                        $result = $clorcdotacao->sql_record($sql);
                        $tipodesp = array() ;
                        $tipod = "";

                        for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                            db_fieldsmemory($result, $i);

                            $ele = substr($o56_elemento, 0, 3);
                            $tipodd = " o56_elemento = '$ele'";
                            if (array_search($ele, $tipodesp)==0) {
                                $srec = $clorcelemento->sql_record(
                                    $clorcelemento->sql_query_file(
                                        null,
                                        null,
                                        'o56_descr',
                                        null,
                                        " o56_anousu = {$anouso}
                                          and  o56_elemento = '{$ele}0000000000'
                                        order by o56_elemento limit 1 "
                                    )
                                );

                                if ($clorcelemento->numrows!=0) {
                                    db_fieldsmemory($srec, 0);
                                    $tipodesp["$ele"] = substr($o56_descr, 0, 20);
                                }
                            }
                            $tipod = " and ";
                        }

                        for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                            db_fieldsmemory($result, $i);

                            $checked = ($pelemento==$o56_elemento?"checked":"");

                            $linhaHtml = "<tr>
                                            <td class='tdCheck'>
                                               <input type='checkbox'
                                                      name='{$o56_elemento}_elemento_{$o56_codele}'
                                                      value='{$o56_codele}'
                                                      {$checked}
                                                      onclick='marcarLinha(this)'>
                                            </td>
                                            <td class='tdId2'>({$o56_codele}) {$o56_elemento} </td>
                                            <td class='tdDescricao'>{$o56_descr}</td>";

                            if ($ind<sizeof($tipodesp)) {
                                $linhaHtml .= "<td class='tdCheckAgrupador'>
                                                <input type='checkbox'
                                                       name='tipoDespesas_".substr(key($tipodesp), 0, 3)."'
                                                       onclick='js_despesas(\"".substr(key($tipodesp), 0, 3)."\",this.checked);'
                                                       value='0' >
                                               </td>
                                               <td class='tdDescricaoAgrupador'> {$tipodesp[key($tipodesp)]}</td>";
                                $ind ++;
                                next($tipodesp);
                            }
                            $linhaHtml .= "<tr>";
                            echo $linhaHtml;
                        }
                        ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id='divDesdobramento' class='divFiltro'>
            <table class="tableRegistros">
                <thead>
                    <tr>
                        <th colspan="3">Desdobramentos</th>
                    </tr>
                    <tr colspan="3" style="text-align: right">
                        <th>
                            <input type="button"
                                   id="marcarDesdobramentos"
                                   value="Marcar Todos"
                                   onclick="js_marca_todos(this);">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <form name="formdes" action="post">
                    <?php
                        $result = $clorcelemento->sql_record(
                            $clorcelemento->sql_query_exercicio(
                                $anouso,
                                $array_instit,
                                null,
                                ' distinct on (o56_codele) o56_codele, o56_elemento, o56_descr',
                                "o56_codele,o56_elemento,o56_anousu DESC",
                                "o56_anousu in(" . implode(', ', $anosFiltro) . ")"
                            )
                        );

                        for ($i = 0; $i < $clorcelemento->numrows; $i++) {
                            db_fieldsmemory($result, $i);

                            $linhaHtml = "<tr>
                                            <td class='tdCheck'>
                                                  <input type='checkbox'
                                                         title='{$o56_descr}'
                                                         name='desdobramento_{$o56_codele}'
                                                         value='{$o56_codele}'
                                                         onchange='setDesdobramento(event)'
                                                         onclick='marcarLinha(this)'>
                                            </td>
                                            <td class='tdId2'>{$o56_elemento}</td>
                                            <td class='tdDescricao'>{$o56_descr}</td>
                                          <tr>";
                            echo $linhaHtml;
                        }
                        ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id='divRecurso' class="divFiltro">
            <table class="tableRegistros">
                <thead>
                    <tr>
                        <th colspan="6">Recursos</th>
                        <th colspan="2"
                            rowspan="3"
                            style='vertical-align: middle; border-left: 1px solid ThreeDLightShadow;'>
                            Classificação
                        </th>
                    </tr>
                    <tr>
                        <th colspan="6" style="text-align: right">
                            <input type="button"
                                   id="marcarRecursos"
                                   value="Marcar Todos"
                                   onclick="js_marca_todos(this);">
                        </th>
                    </tr>
                    <tr>
                        <th></th>
                        <th>Recurso</th>
                        <th>Gestão</th>
                        <th>Siconfi</th>
                        <th>Nome</th>
                        <th>Complemento</th>
                    </tr>
                </thead>
                <tbody>
                    <form name="formrecurso" action="post">
                    <?php
                        $dataUsuario = date('Y-m-d', db_getsession('DB_datausu'));

                        $campos = "
                        distinct
                        orctiporec.o15_codigo ,
                        orctiporec.o15_complemento,
                        orctiporec.o15_recurso,
                        fonterecurso.gestao,
                        fonterecurso.codigo_siconfi,
                        fonterecurso.descricao,
                        complementofonterecurso.o200_descricao,
                        fonterecurso.classificacaofr_id,
                        o15_datalimite,
                        case when o15_datalimite < '{$dataUsuario}' then true else false end as inativo
                        ";

                        $sSql = $clorctiporec->sqlNovaFonteRecurso(
                            null,
                            $campos,
                            " o15_datalimite desc, o15_recurso, o15_complemento, gestao ",
                            " exercicio = {$anouso} "
                        );
                        $result = db_query($sSql);
                        $linhas = pg_num_rows($result);

                        $sqlClassificacao = "SELECT * FROM classificacaofr where id > 1";
                        $rsClassificacao = db_query($sqlClassificacao);
                        $classificacoes = db_utils::getCollectionByRecord($rsClassificacao);

                        for ($i = 0; $i < $linhas; $i++) {
                            $recurso = db_utils::fieldsMemory($result, $i);
                            $complemento = "$recurso->o15_complemento - $recurso->o200_descricao";

                            $checked = ($precurso==$recurso->gestao)?"checked":"";
                            $inativo = ($recurso->inativo == "t"?" <font color='red'>(INATIVO)</font> ":"");
                            ?>
                            <tr>
                                <td>
                                    <input type='checkbox'
                                           class="recursos"
                                           name='recurso_<?=$recurso->o15_recurso?>'
                                           classificacao="<?=$recurso->classificacaofr_id?>"
                                           value='<?=$recurso->o15_codigo?>'
                                           <?=$checked?>
                                           onclick="marcarLinha(this)">
                                </td>
                                <td class="tdId"> <?=$recurso->o15_recurso?> </td>
                                <td class="tdId"> <?=$recurso->gestao?> </td>
                                <td class="tdId"> <?=$recurso->codigo_siconfi?> </td>
                                <td class='tdDescricao'> <?=$recurso->descricao.$inativo?>
                                </td>
                                <td class='tdDescricao'><?=$complemento?></td>
                                    <?php
                                    if (!empty($classificacoes[$i])) {
                                        ?>
                                <td class="tdCheckAgrupador">
                                    <input type='checkbox'
                                           name='classificacao_<?=$classificacoes[$i]->id?>'
                                           value='<?=$classificacoes[$i]->id?>'
                                           onclick="js_despesasRecursos(<?=$classificacoes[$i]->id?>, this.checked);">
                                </td>
                                <td class='tdDescricaoAgrupador'><?=$classificacoes[$i]->descricao?></td>
                                        <?php
                                    } else {
                                        ?>
                                <td></td>
                                <td></td>
                                        <?php
                                    }
                                    ?>
                            </tr>
                            <?php
                        }
                        ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id='divLocalizadorGastos' class='divFiltro'>
            <table class="tableRegistros">
                <thead>
                    <tr>
                        <th colspan="3">Localizador de Gastos</th>
                    </tr>
                    <tr>
                        <th colspan="3" style="text-align: right">
                            <input type="button"
                                   id="marcarLocalizadorGastos"
                                   value="Marcar Todos"
                                   onclick="js_marca_todos(this);">
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <form name="formlocalizadorgastos" action="post">

                    <?php
                    $sQuery  = " orcdotacao.o58_anousu = ".db_getsession("DB_anousu")." and $sel_orgaos";
                    $result  = $clorcdotacao->sql_record(
                        $clorcdotacao->sql_query(
                            null,
                            null,
                            " distinct orcdotacao.o58_localizadorgastos,ppasubtitulolocalizadorgasto.o11_descricao",
                            "orcdotacao.o58_localizadorgastos",
                            $sQuery
                        )
                    );
                    for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                        db_fieldsmemory($result, $i);

                        $checked = ($plocalizadorgastos==$o58_localizadorgastos?"checked":"");

                        $linhaHtml = "<tr>
                                        <td class='tdCheck'>
                                            <input type='checkbox'
                                                   name='localizadorgastos_{$o58_localizadorgastos}'
                                                   value='{$o58_localizadorgastos}'
                                                   {$checked}
                                                   onclick='marcarLinha(this)'>
                                         </td>
                                         <td class='tdId'>{$o58_localizadorgastos}</td>
                                         <td class='tdDescricao'>{$o11_descricao}</td>
                                      <tr>";
                        echo $linhaHtml;
                    }
                    ?>
                    </form>
                </tbody>
            </table>
        </div>

        <div id='divInstituicao' class='divFiltro'>
            <table class="tableRegistros">
                <thead>
                    <tr>
                        <th colspan="3">Instituições</th>
                    </tr>
                </thead>
                <tbody>
                    <form name="forminstit" action="post">
                    <?php
                    $sql = "select codigo,
                                   nomeinst,
                                   prefeitura
                              from db_config
                             order by codigo";
                    $result = $clorcdotacao->sql_record($sql);
                    $todas=false;

                    for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                        db_fieldsmemory($result, $i);
                        if ($prefeitura=='t' && $codigo == db_getsession("DB_instit")) {
                            $todas = true;
                        }
                    }

                    for ($i=0; $i<$clorcdotacao->numrows; $i++) {
                        db_fieldsmemory($result, $i);

                        if ($todas == true || ( $todas == false and $codigo == db_getsession("DB_instit") )) {
                            $checked = "";
                            $style = "";
                            if (in_array($codigo, $array_instit)) {
                                $checked = "checked";
                                $style = "style=\"background:#D7E4EA\"";
                            }

                            $linhaHtml = "<tr {$style} >
                                              <td class='tdCheck'>
                                                <input type='checkbox'
                                                       name='instit_{$codigo}'
                                                       value='{$codigo}'
                                                       {$checked}
                                                       onchange='js_atualiza_instit();'>
                                              </td>
                                              <td class='tdId'>{$codigo}</td>
                                              <td class='tdDescricao'>{$nomeinst}</td>
                                          <tr>";
                            echo $linhaHtml;
                        }
                    }
                    ?>
                    </form>
                </tbody>
            </table>
        </div>

    </div>
    <script type="text/javascript">
    var desdobramentos = [];

    function setDesdobramento(event) {
        var target = event.target;
        var valor = target.value;
        var index = desdobramentos.indexOf(valor);
        if (target.checked && index < 0) {
            desdobramentos.push(parseInt(valor));
        } else {
            desdobramentos.splice(index, 1);
        }
    }

    function pesquisaDotacao() {
        js_OpenJanelaIframe(
            'this',
            'db_iframe_orcdotacao',
            'func_permorcdotacao.php?funcao_js=parent.retornoPesquisaDotacao|o58_coddot',
            'Pesquisa',
            true,
            0
        );
    }

    function retornoPesquisaDotacao(dotacao) {
        $('codigo_dot').value = dotacao;
        $('pesquisa_dot').click();
        db_iframe_orcdotacao.hide();
    }

    /**
     * Atualiza a pagina com base na intituição
     * @param  {Array} aInsituicao
     * @param  {boolean} lDesdobrar
     */
    function atualizarPaginaParametros(aInstituicoes, lDesdobramento, lRestosAPagar) {
        var sInstituicoes = aInstituicoes.join(' -');
        if (sInstituicoes != document.form1.db_selinstit.value) {
            var query = "instit=" + sInstituicoes + "&db_selinstit=" + sInstituicoes;
            if (lDesdobramento) {
                query += "&desdobramento=true";
                document.form1.desdobramento.value = "true";
            }
            if (lRestosAPagar) {
                query += "&restos=true";
            }
            document.form1.db_selinstit.value = sInstituicoes;
            location.href = "func_selorcdotacao_aba.php?" + query;
        }
    }

    function js_atualizar_instit() {
        var obj = parent.iframe_g1.document.form1.db_selinstit;
        if (obj.value !=
            document.form1.db_selinstit.value) {
            var query = "instit=" + obj.value + "&db_selinstit=" +
                obj.value;
            if (parent.iframe_g1.document.form1.desdobramento) {
                query += "&desdobramento=true";
                document.form1.desdobramento.value = "true";
            }
            const urlParams = new
            URLSearchParams(window.location.search);
            const restos = urlParams.get('restos');
            query
                += `&restos=${restos}`;
            document.form1.db_selinstit.value = obj.value;
            location.href = 'func_selorcdotacao_aba.php?' + query;
        }
    }

    function js_atualiza_instit() {
        var ob = document.forminstit.elements;
        var lista = '';
        var virgula = '';
        for (var i = 0; i < ob.length; i++) {
            if (ob[i].type == 'checkbox') {
                if (ob[i].checked == true) {
                    subname = ob[i].name.substr(7, ob[i].name.length);
                    lista = subname + virgula + lista;
                    virgula = ',';
                }
            }
        }
        location.href = 'func_selorcdotacao_aba.php?instit=' + lista;
    }

    function js_marca_todos(elemento) {

        if (elemento.value == "Marcar Todos") {
            elemento.value = "Desmarcar Todos";
            tipoacao = true;
        } else {
            tipoacao = false;
            elemento.value = "Marcar Todos";
        }

        if (document.getElementById('divOrgao').style.visibility == 'visible') {
            objetos = document.formorgao.elements;
            camada = 'orgao';
        }
        if (document.getElementById('divUnidade').style.visibility == 'visible') {
            objetos = document.formunidade.elements;
            camada = 'unidade';
        }
        if (document.getElementById('divFuncao').style.visibility == 'visible') {
            objetos = document.formfuncao.elements;
            camada = 'funcao';
        }
        if (document.getElementById('divSubFuncao').style.visibility == 'visible') {
            objetos = document.formsubfuncao.elements;
            camada = 'subfuncao';
        }
        if (document.getElementById('divPrograma').style.visibility == 'visible') {
            objetos = document.formprograma.elements;
            camada = 'programa';
        }
        if (document.getElementById('divProjAtiv').style.visibility == 'visible') {
            objetos = document.formprojativ.elements;
            camada = 'projativ';
        }
        if (document.getElementById('divElemento').style.visibility == 'visible') {
            objetos = document.formele.elements;
            camada = 'elemento';
        }
        if (document.getElementById('divDesdobramento').style.visibility == 'visible') {
            objetos = document.formdes.elements;
            camada = 'desdobramento';
        }
        if (document.getElementById('divRecurso').style.visibility == 'visible') {
            objetos = document.formrecurso.elements;
            camada = 'recurso';
        }
        if (document.getElementById('divLocalizadorGastos').style.visibility == 'visible') {
            objetos = document.formlocalizadorgastos.elements;
            camada = 'localizadorgastos';
        }
        js_troca(objetos, tipoacao);
    }

    function js_marca_geral() {
        var value = 'Desmarcar Todos';

        if ($F('geralmarca') == "Marcar Geral") {
            tipoacao = true;
            $('geralmarca').value = 'Desmarcar Geral';
        } else {
            tipoacao = false;
            $('geralmarca').value = 'Marcar Geral';
            value = 'Marcar Todos';
        }

        $('marcarOrgaos').value = value;
        $('marcarUnidades').value = value;
        $('marcarFuncoes').value = value;
        $('marcarSubFuncoes').value = value;
        $('marcarProgramas').value = value;
        $('marcarProjAtiv').value = value;
        $('marcarElementos').value = value;
        $('marcarDesdobramentos').value = value;
        $('marcarRecursos').value = value;
        $('marcarLocalizadorGastos').value = value;

        js_troca(document.formorgao.elements, tipoacao);
        js_troca(document.formunidade.elements, tipoacao);
        js_troca(document.formfuncao.elements, tipoacao);
        js_troca(document.formsubfuncao.elements, tipoacao);
        js_troca(document.formprograma.elements, tipoacao);
        js_troca(document.formprojativ.elements, tipoacao);
        js_troca(document.formele.elements, tipoacao);
        js_troca(document.formdes.elements, tipoacao);
        js_troca(document.formrecurso.elements, tipoacao);
        js_troca(document.formlocalizadorgastos.elements, tipoacao);
    }

    function js_troca(objetos, tipoacao) {
        for (var i = 0; i < objetos.length; i++) {
            if (objetos[i].disabled == false) {
                objetos[i].checked = tipoacao;
                marcarLinha(objetos[i]);
            }
        }
    }

    function js_marca_orgaos(tipo) {
        if (tipo == 'orgao') {
            document.getElementById('divOrgao').style.visibility =
                document.getElementById('divOrgao').style.visibility = 'visible';
        } else {
            document.getElementById('divOrgao').style.visibility =
                document.getElementById('divOrgao').style.visibility = 'hidden';
        }
        if (tipo == 'unidade') {
            document.getElementById('divUnidade').style.visibility =
                document.getElementById('divUnidade').style.visibility = 'visible';
        } else {
            document.getElementById('divUnidade').style.visibility =
                document.getElementById('divUnidade').style.visibility = 'hidden';
        }
        if (tipo == 'funcao') {
            document.getElementById('divFuncao').style.visibility =
                document.getElementById('divFuncao').style.visibility = 'visible';
        } else {
            document.getElementById('divFuncao').style.visibility =
                document.getElementById('divFuncao').style.visibility = 'hidden';
        }
        if (tipo == 'subfuncao') {
            document.getElementById('divSubFuncao').style.visibility =
                document.getElementById('divSubFuncao').style.visibility = 'visible';
        } else {
            document.getElementById('divSubFuncao').style.visibility =
                document.getElementById('divSubFuncao').style.visibility = 'hidden';
        }
        if (tipo == 'programa') {
            document.getElementById('divPrograma').style.visibility =
                document.getElementById('divPrograma').style.visibility = 'visible';
        } else {
            document.getElementById('divPrograma').style.visibility =
                document.getElementById('divPrograma').style.visibility = 'hidden';
        }
        if (tipo == 'projativ') {
            document.getElementById('divProjAtiv').style.visibility =
                document.getElementById('divProjAtiv').style.visibility = 'visible';
        } else {
            document.getElementById('divProjAtiv').style.visibility =
                document.getElementById('divProjAtiv').style.visibility = 'hidden';
        }
        if (tipo == 'elemento') {
            document.getElementById('divElemento').style.visibility =
                document.getElementById('divElemento').style.visibility = 'visible';
        } else {
            document.getElementById('divElemento').style.visibility =
                document.getElementById('divElemento').style.visibility = 'hidden';
        }
        if (tipo == 'desdobramento') {
            document.getElementById('divDesdobramento').style.visibility =
                document.getElementById('divDesdobramento').style.visibility = 'visible';
        } else {
            document.getElementById('divDesdobramento').style.visibility =
                document.getElementById('divDesdobramento').style.visibility = 'hidden';
        }
        if (tipo == 'recurso') {
            document.getElementById('divRecurso').style.visibility =
                document.getElementById('divRecurso').style.visibility = 'visible';
        } else {
            document.getElementById('divRecurso').style.visibility =
                document.getElementById('divRecurso').style.visibility = 'hidden';
        }
        if (tipo == 'instit') {
            document.getElementById('divInstituicao').style.visibility =
                document.getElementById('divInstituicao').style.visibility = 'visible';
        } else {
            document.getElementById('divInstituicao').style.visibility =
                document.getElementById('divInstituicao').style.visibility = 'hidden';
        }
        if (tipo == "localizadorgastos") {
            document.getElementById('divLocalizadorGastos').style.visibility =
                document.getElementById('divLocalizadorGastos').style.visibility = 'visible';
        } else {
            document.getElementById('divLocalizadorGastos').style.visibility =
                document.getElementById('divLocalizadorGastos').style.visibility = 'hidden';
        }
    }

    function js_despesas(desp, valor) {
        let elementodesp = document.formele.elements;
        for (var i = 0; i < elementodesp.length; i++) {
            if (elementodesp[i].name.substr(0, 3) == desp) {
                if (valor) {
                    elementodesp[i].checked = true;
                    elementodesp[i].parentNode.parentNode.style.background = '#D7E4EA';
                } else {
                    elementodesp[i].checked = false;
                    elementodesp[i].parentNode.parentNode.style.background = '#EEE';
                }
            }
        }
    }

    function js_despesasRecursos(valor, checked) {
        let elementos = document.formrecurso.elements;
        for (let elemento of elementos) {
            if (elemento.getAttribute('classificacao') == valor) {
                elemento.checked = false;
                elemento.parentNode.parentNode.style.background = '#EEE';
                if (checked) {
                    elemento.checked = true;
                    elemento.parentNode.parentNode.style.background = '#D7E4EA';
                }
            }
        }
    }

    function js_atualiza_variavel_retorno(div_camada, objeto) {
        if (objeto.checked == true) {
            document.form1.db_input_retorno.value = document.form1.db_input_retorno.value
                                                    + div_camada + '_' + objeto.value + '-';
        } else {
            var retira = document.form1.db_input_retorno.value.split('-');
            document.form1.db_input_retorno.value = null;
            for (var i = 0; i < retira.length; i++) {
                if (retira[i] != div_camada + '_' + objeto.value) {
                    if (retira[i] != '') {
                        document.form1.db_input_retorno.value = document.form1.db_input_retorno.value + retira[i] + '-';
                    }
                }
            }
        }
    }

    function js_retorno(div_camada) {
        var obj = div_cama.elements;
        for (var i = 0; i < sizeof(obj); i++) {
            if (obj[i].checked == true) {
                document.form1.db_input_retorno.value = document.form1.db_input_retorno.value
                + div_camada + '_' + obj[i].value + '-'
            }
        }
    }

    function js_atualiza_variavel_retorno(objeto) {
        var camada = [
                'instit', 'orgao', 'unidade', 'funcao', 'subfuncao', 'programa', 'projativ', 'ele', 'des',
                'recurso', 'localizadorgastos'
            ];
        var selecionados = '';
        for (var i = 0; i < camada.length; i++) {
            if (camada[i] == 'recurso' && filtroPorFonteUniao) {
                if (filtroRecursos.getListaRecursos() != '') {
                    var recursos = filtroRecursos.getListaRecursos().replace(',', '-recurso_');
                    selecionados = selecionados + camada[i] + '_' + recursos;
                }
                continue;
            }
            if (camada[i] == 'recurso') {
                let elementos = document.formrecurso.elements;
                for (let elemento of elementos) {
                    if (elemento.checked) {
                      selecionados = selecionados + camada[i] + '_' + elemento.value + '-';
                    }
                }
            } else {
                var qcamada = eval('document.form' + camada[i] + '.elements');
                for (var ii = 0; ii < qcamada.length; ii++) {
                    if (qcamada[ii].checked == true) {
                        selecionados = selecionados + camada[i] + '_' + qcamada[ii].value + '-';
                    }
                }
            }
        }
        if (selecionados != '') {
            return selecionados;
        } else {
            return 'geral';
        }
    }

    function marcarLinha(item) {
        if (item.checked == true) {
            item.parentNode.parentNode.style.background = '#D7E4EA';
        } else {
            item.parentNode.parentNode.style.background = '#EEEEEE';
        }
    }

    var filtroPorFonteUniao = false;
    if (filtroPorFonteUniao) {
        filtroRecursos = new DBViewFiltroRecursos(false);
        $('divRecurso').innerHTML = filtroRecursos.getConteudoFiltro();
    }(function() {
        var query = frameElement.getAttribute('name').replace('IF', ''),
            input = document.querySelector('input[value="Fechar"]');
            input.onclick = parent[query] ?
            parent[query].hide.bind(parent[query]) : input.onclick;
    })();
    </script>
</body>

</html>
