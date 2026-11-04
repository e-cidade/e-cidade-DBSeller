<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2009  DBselller Servicos de Informatica             
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

//MODULO: educação
$cldisciplina->rotulo->label();
$clcaddisciplina->rotulo->label();
$query1 = $clcaddisciplina->sql_record($clcaddisciplina->sql_query("", "*", "ed232_c_descr", ""));
?>
<form name="form1" method="post" action="">
    <center>
        <input name="ed12_i_ensino" type="hidden" value="<?= @$ed12_i_ensino ?>">
        <input name="incluir" type="button" value="Salvar" onclick="js_enviar();">
        <input name="restaurar" type="button" value="Restaurar"
               onclick="location.href='edu1_disciplina001.php?ed12_i_ensino=<?= $ed12_i_ensino ?>'">
        <br><br>
        <table border="1" cellspacing="0" width="80%">
            <tr class="cabec1">
                <td align="center" title="<?= @$Ted232_i_codigo ?>">
                    <input type="checkbox" name="todos" onclick="js_todos();">
                </td>
                <td align="center" title="Matriz Curricular:" width="120">
                    <input type="checkbox" name="matriz_curricular_todos" onclick="js_matriz_curricular_todos();">                
                    <b onclick="js_matriz_curricular_todos();">Matriz Curricular</b>
                </td>                
                <td align="center" title="<?= @$Ted232_i_codigo ?>">
                    <?= $Led232_i_codigo ?>
                </td>
                <td title="<?= @$Ted232_c_descr ?>">
                    <?= $Led232_c_descr ?>
                </td>
                <td title="<?= @$Ted232_c_descr ?>">
                    <?= $Led232_c_abrev ?>
                </td>
            </tr>
            <?php
            $cor1 = "#DBDBDB";
            $cor2 = "#f3f3f3";
            $cor = "";
            for ($x = 0; $x < $clcaddisciplina->numrows; $x++) {
                db_fieldsmemory($query1, $x);
                $query2 = $cldisciplina->sql_record($cldisciplina->sql_query("", "*", "",
                    " ed12_i_ensino = $ed12_i_ensino AND ed12_i_caddisciplina = $ed232_i_codigo"));
                if ($cor == $cor2) {
                    $cor = $cor1;
                } else {
                    $cor = $cor2;
                }
                if ($cldisciplina->numrows > 0) {
                    db_fieldsmemory($query2, 0);
                    $checked = "checked";
                    $classe = "aluno1";
                    
                    if ($ed12_matrizcurricular == "t") {
                        $matriz_curricular_checked = "checked";
                    } else {
                        $matriz_curricular_checked = "";
                    }
                    
                } else {
                    $checked = "";
                    $ed12_i_codigo = "";
                    $classe = "aluno";
                    $matriz_curricular_checked = "";
                }
                ?>
                <tr bgcolor="<?= $cor ?>">
                    <td align="center" title="<?= @$Ted232_i_codigo ?>">
                        <input type="checkbox" name="codigo" <?= $checked ?> value="<?= $ed232_i_codigo ?>" onclick='js_marca_matrizcurricular(this)'>
                        <input name="ed12_i_caddisciplina" type="hidden" value="<?= $ed232_i_codigo ?>" size="10">
                    </td>
                    <td align="center" title="Matriz Curricular">
                        <input type="checkbox" name="matriz_curricular" <?= $matriz_curricular_checked ?> value="<?= $ed232_i_codigo ?>" onclick='js_marca_disciplina(this)'>
                    </td>
                    <td class="<?= $classe ?>" align="center" title="<?= @$Ted232_i_codigo ?>">
                        <?= $ed232_i_codigo ?>
                    </td>
                    <td class="<?= $classe ?>" title="<?= @$Ted232_c_descr ?>">
                        &nbsp;&nbsp;<?= $checked != "" ? " -> " : "" ?><?= $ed232_c_descr ?>
                    </td>
                    <td class="<?= $classe ?>" title="<?= @$Ted232_c_descr ?>">
                        &nbsp;&nbsp;<?= $ed232_c_abrev ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </table>
</form>
</center>

<script>

    function js_enviar() {
        var nodeList = document.querySelectorAll('input[type="checkbox"][name="codigo"]');

        const parametros = {
            exec: 'vinclularDisciplinas',
            ensino : document.form1.ed12_i_ensino.value,
            disciplinas_incluir : [],
            disciplinas_remover : [],
            matriz_curricular_incluir : [],
            matriz_curricular_remover : []
        };
        
        nodeList.forEach(function (elemento) {
            if (elemento.checked) {
                parametros.disciplinas_incluir.push(elemento.value);
            } else {
                parametros.disciplinas_remover.push(elemento.value);
            }
        });

        var nodeList = document.querySelectorAll('input[type="checkbox"][name="matriz_curricular"]');
        nodeList.forEach(function (elemento) {
            if (elemento.checked) {
                parametros.matriz_curricular_incluir.push(elemento.value);
            } else {
                parametros.matriz_curricular_remover.push(elemento.value);
            }
        });        

        new AjaxRequest('edu4_configuracaoensino.RPC.php', parametros, function (response, erro) {
            alert(response.mensagem);
            location.href = 'edu1_disciplina001.php?ed12_i_ensino=' + parametros.ensino;
        }).setMessage('Salvando disciplinas ao ensino').execute();

    }

    function js_todos() {
        if (document.form1.todos.checked == true) {
            tam = document.form1.codigo.length;
            for (i = 0; i < tam; i++) {
                document.form1.codigo[i].checked = true;
            }
        } else {
            tam = document.form1.codigo.length;
            for (i = 0; i < tam; i++) {
                document.form1.codigo[i].checked = false;
                document.form1.matriz_curricular[i].checked = false;
            }
            document.form1.matriz_curricular_todos.checked = false;
        }
    }

    function js_matriz_curricular_todos() {
        
    	tam = document.form1.matriz_curricular.length;
        if (document.form1.matriz_curricular_todos.checked == true) {
            for (i = 0; i < tam; i++) {
                document.form1.matriz_curricular[i].checked = true;
                document.form1.codigo[i].checked = true;
                document.form1.todos.checked = true;
            }
        } else {
            
            for (i = 0; i < tam; i++) {
                document.form1.matriz_curricular[i].checked = false;
            }
            
        }


    }

    function js_marca_disciplina(element) {

    	tam = document.form1.codigo.length;
        for (i = 0; i < tam; i++) {
          if (document.form1.codigo[i].value == element.value && element.checked) {
            document.form1.codigo[i].checked = true;  
          }
        }

        if (element.checked == false) {
          document.form1.matriz_curricular_todos.checked = false;    
        }
        	
    }

    function js_marca_matrizcurricular(element) {

    	tam = document.form1.codigo.length;
        for (i = 0; i < tam; i++) {
          if (document.form1.codigo[i].value == element.value && !element.checked) {
            document.form1.matriz_curricular[i].checked = false;  
          }
        }

        if (element.checked == false) {
            document.form1.matriz_curricular_todos.checked = false;    
            document.form1.todos.checked = false;
        }        
        	
    }    
</script>
