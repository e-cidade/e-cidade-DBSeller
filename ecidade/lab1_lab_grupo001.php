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
include(modification("classes/db_lab_labusuario_classe.php"));
include(modification("classes/db_lab_setorexame_classe.php"));
include(modification("dbforms/db_funcoes.php"));
db_postmemory($HTTP_POST_VARS);
$codigo_laboratorio = $codigo_laboratorio == "" ? $la06_i_laboratorio : $codigo_laboratorio; 
$descricao_laboratorio = $descricao_laboratorio == "" ? $la02_c_descr : $descricao_laboratorio; 

?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <!-- <link href="estilos.css" rel="stylesheet" type="text/css"> -->
    <link rel="stylesheet" type="text/css" href="estilos.css">
    <link type="text/css" href="assets/bootstrap-table/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="assets/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <script rel="script" type="text/javascript" src="scripts/scripts.js"></script>
    <script rel="script" type="text/javascript" src="scripts/strings.js"></script>
    <script rel="script" type="text/javascript" src="scripts/prototype.js"></script>
    <script rel="script" type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
    <script rel="script" type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" onLoad="a=1" >
</table>
<center>
<br><br>
<table width="790" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="430" align="left" valign="top" bgcolor="#CCCCCC"> 
    <center>
        <fieldset style='width: 80%;'> 
            <legend><b>Grupo Exame</b></legend>
            <input type="hidden" id="codigo_laboratorio" name="codigo_laboratorio" value="<?= $la06_i_laboratorio ?>">
            <input type="hidden" id="descricao_laboratorio" name="descricao_laboratorio" value="<?= $la02_c_descr ?>">
            <div style="width: 90%">
                <div style="padding-top: 20px; text-align: left">
                    <table>
                        <tr>
                            <td>
                                <div style="margin-top: 5px">
                                    <label style="font-weight: bold; font-size: 12px" for="descricao-laboratorio">Laboratório:&nbsp;</label>
                                </div>
                            </td>
                            <td>
                                <div style="margin-top: 5px">
                                    <input type="text" name="codigo-laboratorio" id="codigo-laboratorio" value="<?= $codigo_laboratorio ?>" style="background-color:#DEB887;"  readonly class="field-size2">
                                    <input type="text" name="descricao-laboratorio" id="descricao-laboratorio" value="<?= $descricao_laboratorio ?>" style="background-color:#DEB887;" class="field-size8">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="margin-top: 5px">
                                    <label id="ancora-grupo" style="font-weight: bold; font-size: 12px" for="codigo-grupo">Grupo:&nbsp;</label>
                                </div>
                            </td>
                            <td>
                                <div style="margin-top: 5px">
                                    <input type="text" name="codigo-grupo" id="la66_codigo" class="field-size2">
                                    <input type="text" name="descricao-grupo" id="la66_descricao" style="background-color:#DEB887;" class="field-size8">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="text-align: center; margin-top: 5px; text-decoration: none">
                                    <button type="button" id="btn-adicionar" style="margin-bottom: 10px;">
                                    <i class="fas fa-plus"></i> Adicionar
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <div style="padding-top: 10px; text-align: left">
                        <table id="data-table"
                        class="table table-responsive-md"
                        data-height="300"
                        data-virtual-scroll="true">
                    </table>
                </div>
            </div>
        </fieldset>
    </center>
	</td>
  </tr>
</table>
</center>
<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript">
    $.noConflict()
    jQuery(document).ready(function () {
        const ancoraGrupo = document.getElementById('ancora-grupo');

        const inputCodigoGrupo = document.getElementById('la66_codigo');
        const inputDescricaoGrupo = document.getElementById('la66_descricao');
        const inputCodigoLaboratorio = document.getElementById('codigo-laboratorio');
        const inputDescricaoLaboratorio = document.getElementById('descricao-laboratorio');

        const btnAdicionar = document.getElementById('btn-adicionar');
        var table = jQuery('#data-table');

        const lookupExame = new DBLookUp(ancoraGrupo, inputCodigoGrupo, inputDescricaoGrupo, {
            'sArquivo': 'func_lab_grupo.php',
            'sLabel': 'Pesquisar Grupo',
            'sObjetoLookUp': "db_iframe_lab_grupo",
            'fCallBack': (codigo, descricao) => {
                var pai = table[0];
                if(pai.rows[1].children[1]){
                    for(var i = 0; i < pai.rows.length; i++){
                        var rowValue = pai.rows[i].children[1].firstChild.nodeValue;
                        if(rowValue == codigo){
                            inputCodigoGrupo.value = "";
                            inputDescricaoGrupo.value = "";
                            alert("Grupo já inserido.");
                            return false;
                        }
                        inputDescricaoGrupo.value = descricao;
                    }
                }
            }
        });

        window.operateEvents = {
            'click .excluir': function (e, value, row, index) {
                if(confirm("Você tem certeza que deseja excluir este grupo?")){
                    const formData = new FormData();
                    formData.append('acao', 'excluirGrupoLaboratorio');
                    formData.append('codigo', row.codigolaboratoriogrupo);
                    HttpClient.post('lab1_lab_grupo001.RPC.php', {body: formData}).then((response) => {
                        if (response.erro) {
                            alert(response.mensagem);
                            return;
                        }
                        buscarGruposLaboratorio();
                        alert("Grupo excluido com sucesso.");
                    });
                }
            }
        }

        const formatterAcoes = (value, row) => {
            var url = "lab1_lab_grupo002.php?";
            url += "codigo_labgrupoexame=" + row.codigolaboratoriogrupo + "&";
            url += "descricao_grupo=" + row.descricao + "&";
            url += "codigo_laboratorio=" + inputCodigoLaboratorio.value + "&";
            url += "descricao_laboratorio=" + inputDescricaoLaboratorio.value;
            $html = '<a href="'+url+'"><i title="Vincular Exames ao Grupo." class="fas fa-list" style="font-size:15px"></i></a> ';
            $html += '<a><i title="Excluir Grupo." class="fas fa-trash excluir" style="font-size:15px; margin-left:10px"></i></a>';
            return $html;
        }

        table.bootstrapTable({
            locale: 'pt-BR',
            columns: [
                {
                    title: 'Código',
                    field: 'codigolaboratoriogrupo',
                    align: 'center',
                    valign: 'middle',
                    width: '75px',
                },
                {
                    title: 'Código Grupo',
                    field: 'codigo',
                    align: 'center',
                    valign: 'middle',
                    width: '75px'
                },
                {
                    title: 'Descrição',
                    field: 'descricao',
                    align: 'center',
                    valign: 'middle',
                },
                {
                    title: 'Ações',
                    align: 'center',
                    valign: 'middle',
                    events: window.operateEvents,
                    formatter: formatterAcoes
                }
            ]
        })

        const buscarGruposLaboratorio = (value, row) => {
            const formData = new FormData();
            formData.append('acao', 'buscarGruposLaboratorio');
            formData.append('laboratorio', inputCodigoLaboratorio.value);
            HttpClient.post('lab1_lab_grupo001.RPC.php', {body: formData}).then((response) => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                table.bootstrapTable('load', response.gruposLaboratorio);
            });
        }

        const adicionarGrupoAoLaboratorio = () => {
            if(!inputCodigoLaboratorio.value){
                alert("Por favor preencher o laboratorio para ser adicionado.");
                return false;
            }
            if(!inputCodigoGrupo.value){
                alert("Por favor preencher o grupo para ser adicionado.");
                return false;
            }
            const formData = new FormData();
            formData.append('acao', 'adicionarGrupoLaboratorio');
            formData.append('laboratorio', inputCodigoLaboratorio.value);
            formData.append('grupo', inputCodigoGrupo.value);
            HttpClient.post('lab1_lab_grupo001.RPC.php', {body: formData}).then((response) => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                buscarGruposLaboratorio();
                inputCodigoGrupo.value = "";
                inputDescricaoGrupo.value = "";
                table.bootstrapTable('load', response.gruposLaboratorio);
            });
        };

        btnAdicionar.addEventListener('click', adicionarGrupoAoLaboratorio);

        //Passar a cor do header para branco, pois por algum motivo modificou
        table[0].childNodes[1].setAttribute('style', 'color:white');

        buscarGruposLaboratorio();

    });
</script>
</body>
</html>

