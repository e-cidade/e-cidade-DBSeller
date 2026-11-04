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
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("classes/db_db_config_classe.php"));
require_once(modification("classes/db_db_userinst_classe.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

$cl_db_userinst = new cl_db_userinst;
$cldb_config = new cl_db_config;

$cldb_config->rotulo->label("codigo");
$cldb_config->rotulo->label("nomeinst");

// selecina as instituições que o usuario tem liberadas
$vetor_instit = [];
$resit  = $cl_db_userinst->sql_record(
    $cl_db_userinst->sql_query_file(
        null,
        null,
        "id_instit",
        null,
        "id_usuario=".db_getsession("DB_id_usuario")
    )
);

if ($cl_db_userinst->numrows > 0) {
    for ($x=0; $x<$cl_db_userinst->numrows; $x++) {
        db_fieldsmemory($resit, $x);
        $vetor_instit[] = $id_instit;
    }
}
$result = $cldb_config->sql_record(
    $cldb_config->sql_query_file(
        null,
        "codigo,nomeinst,prefeitura",
        "codigo"
    )
);

?>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="estilos.css" rel="stylesheet" type="text/css">
        <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
        <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
        <style>
            table {
                margin-left: auto;
                margin-right: auto;
                background-color: #EEE;
                border: 1px solid threedshadow;
                border-spacing: 0;
            }
            table tr {
                border-bottom: 1px solid threedshadow;
            }

            table tr:hover {
                background: #D7E4EA;
            }

            table th {
                font-weight: bold;
                text-align: center;
                background-color: #CCC;
                border: 1px solid threedshadow;
            }
            .tdDescricao {
                text-align: "left";
                font-weight: bold;
            }
                        
        </style>
    </head>

<script>
function js_marca(codigo) {
  obj = document.form1;
  parent.document.form1.db_selinstit.value = "";
  separa = "";

  for(i=0;i<obj.length;i++) {
    if (obj.elements[i].name != "marcardesmarcar") {
      obj.elements[i].parentNode.parentNode.style.background = '#EEEEEE';

      if (obj.elements[i].checked) {
        obj.elements[i].parentNode.parentNode.style.background = '#D7E4EA';

        parent.document.form1.db_selinstit.value = parent.document.form1.db_selinstit.value 
                                                   + separa + document.form1.elements[i].value ;

        separa = "-";
      }
    }
  }
}

function js_marcatodos() {
  obj = document.form1;
  parent.document.form1.db_selinstit.value = "";

  for(i = 0; i < obj.length; i++) {
    if(obj.elements[i].name != "marcardesmarcar") {
      if (obj.marcardesmarcar.checked == true) {
        obj.elements[i].checked = true;
        obj.elements[i].parentNode.parentNode.style.background = '#D7E4EA';
      } else {
        obj.elements[i].checked = false;
        obj.elements[i].parentNode.parentNode.style.background = '#EEEEEE';
      }
    }
  }
  js_marca();
}
</script>

<body>
    <form name="form1" method="post" id='form1'>
        <table class="form-container tableRegistros">
           <thead>
              <?php
                if ($result==false || $cldb_config->numrows==0) :
                    ?>
                   <th> Sem Cadastro de Instituição </th>
                    <?php
                else :
                    ?>
                  <th alt="Marcar/Desmarcar todos." title="Marcar/Desmarcar todos.">
                   <input type="checkbox" name="marcardesmarcar" onclick="js_marcatodos();"/>
                  </th>
                  <th>Instituições</th>
            </thead>
            <tbody>
                        <?php
                        for ($i=0; $i<$cldb_config->numrows; $i++) {
                              db_fieldsmemory($result, $i);
        
                              // a linha abaixo faz a impressao somente das instituições autorizadas ao usuario
                            if (array_search($codigo, $vetor_instit)===false) {
                              // instituição listada nao encontrada nas permissoes do usuario
                                continue;
                            }
                            ?>
                        <tr>
                          <td class="tdCheck"> 
                             <input id='cod_<?=$codigo?>'
                                    name='cod_<?=$codigo?>' 
                                    type='checkbox' 
                                    onclick='js_marca("<?=$codigo?>"); <?=($funcao==''?'':$funcao.'();')?>' 
                                    value='<?=$codigo?>'>
                          </td>
                          <td class="tdDescricao"> <?=$nomeinst?> </td>
                        </tr>
                            <?php
                            if ($prefeitura=='t') {
                                echo "<script>
                                         document.form1.cod_{$codigo}.click();
                                      </script>";
                            }
                        }
                endif;
                ?>
            <tbody>
        </table>
    </form>
</body>
</html>
