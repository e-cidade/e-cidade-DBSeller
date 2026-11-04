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

//MODULO: educa??o
require_once(modification("libs/db_stdlibwebseller.php"));
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_aluno_classe.php"));
require_once(modification("classes/db_serie_classe.php"));
require_once(modification("classes/db_alunocurso_classe.php"));
require_once(modification("classes/db_alunopossib_classe.php"));
require_once(modification("classes/db_cursoescola_classe.php"));
require_once(modification("libs/db_jsplibwebseller.php"));
db_postmemory($_POST);

$claluno          = new cl_aluno;
$clserie          = new cl_serie;
$clalunocurso     = new cl_alunocurso;
$clalunopossib    = new cl_alunopossib;
$clcursoescola    = new cl_cursoescola;
$clrotulo         = new rotulocampo;
$clsecparametros  = new cl_sec_parametros;
$clrotulo->label("ed47_i_codigo");
$clrotulo->label("ed60_i_codigo");
$clrotulo->label("ed47_v_nome");
$clrotulo->label("ed47_v_pai");
$clrotulo->label("ed47_v_mae");
$clrotulo->label("ed56_c_situacao");
$clrotulo->label("ed223_i_serie");
$clrotulo->label("ed31_i_curso");
$clrotulo->label("ed56_i_escola");
$clrotulo->label("ed47_v_cpf");
$clrotulo->label("ed47_c_codigoinep");
$clrotulo->label("ed47_c_nis");
$clrotulo->label("ed47_certidaomatricula");

$codescola    = empty($codescola) ? 0 : $codescola;
$codcurso     = empty($codcurso)  ? 0 : $codcurso;


// Busca campo em Secretaria > Procedimentos > Par?metros > Par?metros Globais > Habilita Consulta Aluno Por Escola.
$sqlSecParametros = $clsecparametros->sql_query("", "ed290_habilitaconsultaalunoporescola");
$resultSecParametros = $clsecparametros->sql_record($sqlSecParametros);
$sHabilitaConsultaAlunoPorEscola = pg_fetch_result($resultSecParametros, 0, "ed290_habilitaconsultaalunoporescola");

// Se Habilita Consulta Aluno Por Escola for verdadeiro ent?o o campo Escola: em Escola > Consultas > Alunos trar?
// apenas a Escola selecionada no departamento, caso contr?rio trar? todas as Escolas do mesmo jeito se fosse acessado
// pelo m?dulo Secretaria.
$escolaFixa = '';
if ($sHabilitaConsultaAlunoPorEscola == 't') {
    if (db_getsession("DB_modulo") == 1100747) {
        $codescola    = db_getsession("DB_coddepto");
        $escolaFixa = " ed18_i_codigo = ".db_getsession("DB_coddepto");
        if ((db_getsession("DB_coddepto") == 10)){
            $escolaFixa ="";
        }
    }
}

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/webseller.js"></script>
</head>
<script>
nextfield = "campo1"; // nome do primeiro campo
netscape  = "";
ver       = navigator.appVersion;
len       = ver.length;
for (iln = 0; iln < len; iln++)
  if (ver.charAt(iln) == "(")
    break;
netscape = (ver.charAt(iln+1).toUpperCase() != "C");
function keyDown(DnEvents) {

 k = (netscape) ? DnEvents.which : window.event.keyCode;
 if (k == 13) { // pressiona tecla enter
   if (nextfield == 'done') {
     return true; // envia quando termina os campos
   } else {
     document.getElementById(nextfield).focus();
     return false;
   }
  }
}
document.onkeydown = keyDown;
if(netscape)
 document.captureEvents(Event.KEYDOWN|Event.KEYUP);

function js_redireciona(chave) {

  js_OpenJanelaIframe('','db_iframe_aluno','edu3_alunos001.php?chavepesquisa='+chave,
		              'Consulta de Alunos',true,20,0,screen.availWidth+5,1500
		             );

}
</script>
<body class="body-default">
<form name="form1" action="">
<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
 <tr>
  <td width="360" height="18">&nbsp;</td>
  <td width="263">&nbsp;</td>
  <td width="25">&nbsp;</td>
  <td width="140">&nbsp;</td>
 </tr>
</table>
<?php MsgAviso(db_getsession("DB_coddepto"),"escola");?>
<br><div class="container">
<fieldset style="width:95%"><legend><b>Consulta de Alunos</b></legend>
<table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#CCCCCC">
 <tr>
  <td valign="top">
   <table width="100%" border="0" cellspacing="0">
    <tr>
     <td nowrap title="<?=$Ted47_i_codigo?>">
      <?=$Led47_i_codigo?>
     </td>
     <td nowrap>
      <?php db_input("ed47_i_codigo",10,$Ied47_i_codigo,true,"text",1,"onFocus=\"nextfield='pesquisar'\"");?>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=$Ted47_v_nome?>">
      <?=$Led47_v_nome?>
     </td>
     <td nowrap>
         <input type="text" id="inputNovoNome" name="ed47_v_nome" onKeyPress="return lettersOnly(event)" style="width: 494px;"/>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=$Ted47_v_mae?>">
      <?=$Led47_v_mae?>
     </td>
     <td nowrap>
         <input type="text" id="inputNovoNomeMae" name="ed47_v_mae" onKeyPress="return lettersOnly(event)" style="width: 494px;"/>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=$Ted47_v_pai?>">
      <?=$Led47_v_pai?>
     </td>
     <td nowrap>
         <input type="text" id="inputNovoNomePai" name="ed47_v_pai" onKeyPress="return lettersOnly(event)" style="width: 494px;"/>
     </td>
    </tr>
   </table>
  </td>
  <td valign="top">
   <table border="0" cellspacing="0">
    <tr>
     <td nowrap title="<?=$Ted56_i_escola?>">
      <?=$Led56_i_escola?>
     </td>
     <td>
      <?php
      $result_escola = $clalunocurso->sql_record($clalunocurso->sql_query("",
                                                                          "DISTINCT ed18_i_codigo,ed18_c_nome",
                                                                          " ed18_c_nome",
                                                                          "$escolaFixa"
                                                                         )
                                                );
      if ($clalunocurso->numrows==0) {

        $x = array(''=>'NENHUM REGISTRO');
        db_select('ed56_i_escola',$x,true,1,"style='width:300px;'");

      } else {

        ?>
        <select name="ed56_i_escola" id="ed56_i_escola" onchange="js_escola(this.value);" style="width:300px;">
            <?php if($escolaFixa == "") { ?>
            <option value=""></option>
         <?php
            }
         for ($x=0;$x<$clalunocurso->numrows;$x++) {
           db_fieldsmemory($result_escola,$x);
         ?>
           <option value="<?=$ed18_i_codigo?>" <?=$codescola==$ed18_i_codigo?"selected":""?>><?=$ed18_c_nome?></option>
          <?php
         }
        ?>
       </select>
       <?php
      }
      ?>
     </td>
    </tr>
    <tr>
     <td>
      <?=$Led56_c_situacao?>
     </td>
     <td>
      <?php

      $disabled        = $codescola!=0?"":"disabled";
      $result_situacao = $clalunocurso->sql_record($clalunocurso->sql_query("",
                                                                            "DISTINCT ed56_c_situacao as sit",
                                                                            "ed56_c_situacao",
                                                                            " ed56_i_escola = $codescola"
                                                                           )
                                                   );
      if ($clalunocurso->numrows == 0) {
        $x = array(''=>'');
        db_select('ed56_c_situacao',$x,true,1," $disabled style='width:300px;'");
      } else {
       ?>
       <select name="ed56_c_situacao" id="ed56_c_situacao" <?=$disabled?> style='width:300px;'>
        <option value=""></option>
        <?php
        for ($x=0;$x<$clalunocurso->numrows;$x++) {
          db_fieldsmemory($result_situacao,$x);
         ?>
         <option value="<?=$sit?>" <?=@$sit==@$ed56_c_situacao?"selected":""?>><?=$sit?></option>
         <?php
        }
        ?>
       </select>
       <?php
      }
      ?>
     </td>
    </tr>
    <tr>
     <td nowrap title="<?=$Ted31_i_curso?>">
      <?=$Led31_i_curso?>
     </td>
     <td>
      <?php
      $result_curso = $clcursoescola->sql_record($clcursoescola->sql_query("",
                                                                           "DISTINCT ed29_i_codigo,ed29_c_descr",
                                                                           " ed29_c_descr",
                                                                           " ed71_i_escola = $codescola"
                                                                          )
                                                );
      if ($clcursoescola->numrows == 0) {
        $x = array(''=>'');
        db_select('ed31_i_curso',$x,true,1," $disabled style='width:300px;'");
      } else {
       ?>
       <select name="ed31_i_curso" id="ed31_i_curso" onchange="js_curso(this.value,document.form1.ed56_i_escola.value);" style="width:300px;" <?=$disabled?>>
        <option value=""></option>
        <?php
        for ($x=0;$x<$clcursoescola->numrows;$x++) {
          db_fieldsmemory($result_curso,$x);
         ?>
         <option value="<?=$ed29_i_codigo?>" <?=$codcurso==$ed29_i_codigo?"selected":""?>><?=$ed29_c_descr?></option>
         <?php
        }
        ?>
       </select>
       <?php
      }
      ?>
     </td>
    </tr>
    <tr>
     <td nowrap>
      <?=$Led223_i_serie?>
     </td>
     <td>
      <?php

      $disabled1    = $codcurso != 0?"":"disabled";
      $campos       = " DISTINCT ed11_i_codigo,ed11_c_descr,ed11_i_sequencia ";
      $result_serie = $clalunopossib->sql_record($clalunopossib->sql_query("",
                                                                           $campos,
                                                                           "",
                                                                           " ed31_i_curso = $codcurso
                                                                             AND ed56_i_escola = $codescola"
                                                                          )
                                                );
      if ($clalunopossib->numrows == 0) {
        $x = array(''=>'');
        db_select('ed223_i_serie',$x,true,1," $disabled1 style='width:300px;'");
      }else{
       ?>
       <select name="ed223_i_serie" id="ed223_i_serie" <?=$disabled1?> style='width:300px;'>
        <option value=""></option>
        <?php
        for ($x=0; $x<$clalunopossib->numrows; $x++) {
          db_fieldsmemory($result_serie,$x);
         ?>
         <option value="<?=$ed11_i_codigo?>" <?=@$ed223_i_serie==$ed11_i_codigo?"selected":""?>><?=$ed11_c_descr?></option>
         <?php
        }
        ?>
       </select>
       <?php
      }
      ?>
     </td>
    </tr>
   </table>
  </td>
 </tr>
    <tr>
        <td colspan="2" style="padding-top:15px;">
                <fieldset  class="separator">
                    <legend>Documentos:</legend>
                    <table width="100%">
                        <tr>
                            <td nowrap align="right" title="CPF">
                                <b>CPF:</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ed47_v_cpf",42,1,true,"text",1,"onFocus=\"nextfield='pesquisar'\"");?>
                            </td>
                            <td nowrap align="right" title="Cód. INEP">
                                <b>Código INEP:</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ed47_c_codigoinep",42,1,true,"text",1,"onFocus=\"nextfield='pesquisar'\"");?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap align="right" title="Cód. NIS">
                                <b>NIS:</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ed47_c_nis",42,1,true,"text",1,"onFocus=\"nextfield='pesquisar'\"");?>
                            </td>
                            <td nowrap align="right" title="Certidão Matricula">
                                <b>Certidão de Nascimento (Nova):</b>
                            </td>
                            <td nowrap>
                                <?php db_input("ed47_certidaomatricula",42,1,true,"text",1,"onFocus=\"nextfield='pesquisar'\"");?>
                            </td>
                        </tr>
                    </table>
                </fieldset>
            </center>
        </td>
    </tr>
 <tr>
  <td colspan="2" align="center">
   <br>
   <input name="pesquisar" id="pesquisar" type="button" value="Pesquisar" onclick="js_pesquisar();"
          onFocus="nextfield='done'">
   <input name="limpar" type="button" value="Limpar" onclick="location.href='edu3_alunos000.php'">
  </td>
 </tr>
</table>
</fieldset>
</form>
<table width="100%" id="listadados">
 <tr>
  <td valign="top" align="center" >
   <?php
   if(isset($pesquisar)){
    ?><fieldset style="width:95%"><legend><b>Registros</b></legend>
    <?php
    $sql  = " SELECT * ";
    $sql .= "     FROM ( ";
    $sql .= "        SELECT distinct on (aluno.ed47_i_codigo) aluno.ed47_i_codigo, ";
    $sql .= "               aluno.ed47_v_nome, ";
    $sql .= "               alunocurso.ed56_c_situacao, ";
    $sql .= "               serie.ed11_c_descr as dl_serie, ";
    $sql .= "               case when alunocurso.ed56_i_codigo is not null ";
    $sql .= "                then ";
    $sql .= "                 case when alunocurso.ed56_c_situacao = 'TRANSFERIDO REDE' ";
    $sql .= "                  then ";
    $sql .= "                  (select ed18_c_nome ";
    $sql .= "                    from transfescolarede ";
    $sql .= "                     inner join matricula on ed60_i_codigo = ed103_i_matricula ";
    $sql .= "                     inner join turma on ed57_i_codigo = ed60_i_turma ";
    $sql .= "                     inner join escola on ed18_i_codigo = ed57_i_escola ";
    $sql .= "                     where ed60_i_aluno = ed56_i_aluno ";
    $sql .= "                    and ed57_i_base = ed56_i_base ";
    $sql .= "                    and ed57_i_calendario = ed56_i_calendario ";
    $sql .= "                    order by ed103_d_data desc limit 1) ";
    $sql .= "                   else ";
    $sql .= "                  escola.ed18_c_nome ";
    $sql .= "                 end ";
    $sql .= "                 else null ";
    $sql .= "               end as dl_escola, ";
    $sql .= "               calendario.ed52_c_descr as dl_calendario ";
    $sql .= "       FROM aluno ";
    $sql .= "         left join alunocurso on alunocurso.ed56_i_aluno = aluno.ed47_i_codigo ";
    $sql .= "         left join escola on escola.ed18_i_codigo = alunocurso.ed56_i_escola ";
    $sql .= "         left join calendario on  calendario.ed52_i_codigo = alunocurso.ed56_i_calendario ";
    $sql .= "         left join base on  base.ed31_i_codigo = alunocurso.ed56_i_base ";
    $sql .= "         left join cursoedu on  cursoedu.ed29_i_codigo = base.ed31_i_curso ";
    $sql .= "         left join alunopossib on  alunopossib.ed79_i_alunocurso = alunocurso.ed56_i_codigo ";
    $sql .= "         left join serie on  serie.ed11_i_codigo = alunopossib.ed79_i_serie ";
    if (isset($ed47_i_codigo)) {
      $repassa = array("ed47_i_codigo"=>$ed47_i_codigo);
    }
     $sql .= " WHERE ed47_i_codigo > 0 ";
    if (isset($ed47_i_codigo) && (trim($ed47_i_codigo) !="" )) {
      $sql .= " AND ed47_i_codigo = $ed47_i_codigo  ";
    }
    if (isset($ed60_i_codigo) && (trim($ed60_i_codigo) !="" )) {
      $sql .= " AND ed60_i_codigo = $ed60_i_codigo ";
    }

    if (isset($ed47_v_nome) && (trim($ed47_v_nome) !="" )) {
        $ed47_v_nome = pg_escape_string($ed47_v_nome);
       $sql .= " AND ed47_v_nome like '{$ed47_v_nome}%'";
    }
    if (isset($ed47_v_pai) && (trim($ed47_v_pai) !="" )) {
        $ed47_v_pai = pg_escape_string($ed47_v_pai);
      $sql .= " AND ed47_v_pai like '{$ed47_v_pai}%'";
    }
    if (isset($ed47_v_mae) && (trim($ed47_v_mae) !="" )) {
        $ed47_v_mae = pg_escape_string($ed47_v_mae);
      $sql .= " AND ed47_v_mae like '{$ed47_v_mae}%'";
    }
    if (isset($ed56_i_escola) && (trim($ed56_i_escola) !="" )) {
      $sql .= " AND ed56_i_escola = $ed56_i_escola ";
    }
    if (isset($ed56_c_situacao) && (trim($ed56_c_situacao) !="" )) {
      $sql .= " AND trim(ed56_c_situacao) = '$ed56_c_situacao' ";
    }
    if (isset($ed31_i_curso) && (trim($ed31_i_curso) !="" )) {
      $sql .= " AND ed31_i_curso = $ed31_i_curso ";
    }
    if (isset($ed223_i_serie) && (trim($ed223_i_serie) !="" )) {
      $sql .= " AND ed79_i_serie = $ed223_i_serie  ";
    }
    if (isset($ed47_v_cpf) && (trim($ed47_v_cpf) != "")) {
           $sql .= " AND ed47_v_cpf = '$ed47_v_cpf' ";
    }
    if (isset($ed47_c_codigoinep) && (trim($ed47_c_codigoinep) != "")) {
           $sql .= " AND ed47_c_codigoinep = '$ed47_c_codigoinep' ";
    }
    if (isset($ed47_c_nis) && (trim($ed47_c_nis) != "")) {
           $sql .= " AND ed47_c_nis = '$ed47_c_nis' ";
    }
    if (isset($ed47_certidaomatricula) && (trim($ed47_certidaomatricula) != "")) {
           $sql .= " AND ed47_certidaomatricula = '$ed47_certidaomatricula' ";
    }
    $sql .= "  ) as x ORDER BY ed47_v_nome";  // <- To ascii ADD

    db_lovrot(@$sql,12,"()","","js_redireciona|ed47_i_codigo","","NoMe",$repassa);
    ?></fieldset>
    <?php
   }
   ?>
  </td>
 </tr>
</table>
</center>
</body>
</html>
<script>
document.getElementById("ed47_i_codigo").focus();
const inputNovoNome = document.querySelector('#inputNovoNome');
const inputNovoNomePai = document.querySelector('#inputNovoNomePai');
const inputNovoNomeMae = document.querySelector('#inputNovoNomeMae');

function lettersOnly(evt) {
    if(!/[a-z???????????????????????'% ]/i.test(evt.key)) return false;
}
inputNovoNome.addEventListener('keyup', () => {
    inputNovoNome.value = inputNovoNome.value.toUpperCase()
})
inputNovoNomePai.addEventListener('keyup', () => {
    inputNovoNomePai.value = inputNovoNomePai.value.toUpperCase()
})
inputNovoNomeMae.addEventListener('keyup', () => {
    inputNovoNomeMae.value = inputNovoNomeMae.value.toUpperCase()
})

function js_escola(valor) {

  codigo = document.getElementById("ed47_i_codigo").value;
  nome   = document.getElementById("inputNovoNome").value;
  pai    = document.getElementById("inputNovoNomePai").value;
  mae    = document.getElementById("inputNovoNomeMae").value;
  if (valor == "") {

    location.href = "edu3_alunos000.php?loc&ed47_i_codigo="+codigo+"&ed47_v_nome="+nome+"&ed47_v_pai="+pai+
                     "&ed47_v_mae="+mae;

  } else {

   location.href = "edu3_alunos000.php?loc&codescola="+valor+"&ed47_i_codigo="+codigo+"&ed47_v_nome="+nome+
                    "&ed47_v_pai="+pai+"&ed47_v_mae="+mae;

  }
}

function js_curso(valor,escola) {

  codigo   = document.getElementById("ed47_i_codigo").value;
  nome     = document.getElementById("inputNovoNome").value;
  pai      = document.getElementById("inputNovoNomePai").value;
  mae      = document.getElementById("inputNovoNomeMae").value;
  situacao = document.getElementById("ed56_c_situacao").value;
  if (valor == "") {

    location.href = "edu3_alunos000.php?loc&codescola="+escola+"&ed47_i_codigo="+codigo+"&ed47_v_nome="+nome+
                     "&ed47_v_pai="+pai+"&ed47_v_mae="+mae;

  } else {

    location.href = "edu3_alunos000.php?loc&codcurso="+valor+"&codescola="+escola+"&ed47_i_codigo="+codigo+
                    "&ed47_v_nome="+nome+"&ed47_v_pai="+pai+"&ed56_c_situacao="+situacao+"&ed47_v_mae="+mae;

  }
}

function js_pesquisar() {
  codigo        = document.getElementById("ed47_i_codigo").value;
  nome          = document.getElementById("inputNovoNome").value;
  pai           = document.getElementById("inputNovoNomePai").value;
  mae           = document.getElementById("inputNovoNomeMae").value;
  escola        = document.getElementById("ed56_i_escola").value;
  curso         = document.getElementById("ed31_i_curso").value;
  serie         = document.getElementById("ed223_i_serie").value;
  situacao      = document.getElementById("ed56_c_situacao").value;
  cpf           = document.getElementById("ed47_v_cpf").value;
  inep          = document.getElementById("ed47_c_codigoinep").value;
  nis           = document.getElementById("ed47_c_nis").value;
  certidao      = document.getElementById("ed47_certidaomatricula").value;
  location.href = "edu3_alunos000.php?pesquisar&codcurso="+curso+"&codescola="+escola+"&ed47_i_codigo="+codigo+
                   "&ed47_v_nome="+URLEncode(nome)+"&ed47_v_pai="+pai+"&ed47_v_mae="+mae+"&ed56_i_escola="+escola+
                   "&ed223_i_serie="+serie+"&ed56_c_situacao="+situacao+"&ed31_i_curso="+curso +"&ed47_v_cpf="+cpf+
                   "&ed47_c_codigoinep="+inep+"&ed47_c_nis="+nis+"&ed47_certidaomatricula="+certidao;
}

<?php
if (isset($loc)) {
 ?>document.getElementById("pesquisar").click();
 <?php
}
?>
</script>
<?php db_menu(db_getsession("DB_id_usuario"),
          db_getsession("DB_modulo"),
          db_getsession("DB_anousu"),
          db_getsession("DB_instit")
         );
?>
