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
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
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
            <legend><b><?= " Vincular exames ao grupo $descricao_grupo" ?></b></legend>
            <div style="width: 90%">
                <div style="padding-top: 20px; text-align: left">
                    <table>
                        <tr>
                            <td><input type="hidden" name="codigo" id="codigo-grupolaboratorio" value="<?=$codigo_labgrupoexame?>"></td>
                        </tr>
                        <tr>
                            <td><input type="hidden" id="codigo-laboratorio" value="<?=$codigo_laboratorio?>"></td>
                        </tr>
                        <tr>
                            <td><input type="hidden" id="descricao-laboratorio" value="<?=$descricao_laboratorio?>"></td>
                        </tr>
                        <tr>
                            <td>
                                <div style="margin-top: 5px">
                                    <label id="ancora-setor" style="font-weight: bold; font-size: 12px" for="codigo-setor">Setor:&nbsp;</label>
                                </div>
                            </td>
                            <td>
                                <div style="margin-top: 5px">
                                    <input type="text" name="codigo-setor" id="la23_i_codigo">
                                    <input type="text" name="descricao-setor" id="la23_c_descr" style="background-color:#DEB887;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div style="margin-top: 5px">
                                    <label id="ancora-exame" style="font-weight: bold; font-size: 12px" for="descricao-exame">Exame:&nbsp;</label>
                                </div>
                            </td>
                            <td>
                                <div style="margin-top: 5px">
                                    <input type="text" name="codigo-exame" id="la08_i_codigo">
                                    <input type="text" name="descricao-exame" id="la08_c_descr" style="background-color:#DEB887;">
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="text-align: center; margin-top: 5px; text-decoration: none">
                                    <button type="button" id="btn-adicionar" style="margin-bottom: 10px;">
                                    <i class="fas fa-plus"></i> Adicionar
                                    </button>
                                    <button id="btn-voltar" type="button" style="margin-bottom: 5px">
                                        <a style="text-decoration: none; color: black">
                                            <i class="fas fa-undo"></i> Voltar
                                        </a>
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
        const inputCodigoSetor = document.getElementById('la23_i_codigo');
        const inputDescricaoSetor = document.getElementById('la23_c_descr');
        const inputCodigoExame = document.getElementById('la08_i_codigo');
        const inputDescricaoExame = document.getElementById('la08_c_descr');
        const inputCodigoLaboratorio = document.getElementById('codigo-laboratorio');
        const inputCodigoGrupoLaboratorio = document.getElementById('codigo-grupolaboratorio');
        const inputDescricaoLaboratorio = document.getElementById('descricao-laboratorio');

        const ancoraSetor = document.getElementById('ancora-setor');
        const ancoraExame = document.getElementById('ancora-exame');

        const btnAdicionar = document.getElementById('btn-adicionar');
        const btnVoltar = document.getElementById('btn-voltar');

        inputCodigoSetor.addEventListener('change', (events) =>{
            if(inputCodigoSetor.value.length == 0){                
                lookupExame.setParametrosAdicionais(['la24_i_setor=']);
            }
        });

        inputCodigoExame.addEventListener('change', (events) =>{
            if(inputCodigoExame.value.length == 0){                
                lookupSetor.setParametrosAdicionais(['exames=']);
            }
        });

        var table = jQuery('#data-table');

        const lookupExame = new DBLookUp(ancoraExame, inputCodigoExame, inputDescricaoExame, {                        
            'sArquivo': 'func_lab_exame.php',
            'sQueryString':'&la02_i_codigo='+inputCodigoLaboratorio.value,
            'sLabel': 'Pesquisar Exame',
            'sObjetoLookUp': "db_iframe_lab_exame",
            'fCallBack': (codigoSelecionado, descricao, codigo) => {                                
                if(!codigo){
                    codigo = codigoSelecionado;
                }
                var pai = table[0];
                if(pai.rows[1].children[1]){
                    for(var i = 0; i < pai.rows.length; i++){
                        var rowValue = pai.rows[i].children[2].firstChild.nodeValue;
                        if(rowValue == codigo){
                            inputCodigoExame.value = "";
                            inputDescricaoExame.value = "";
                            alert("Exame já inserido.");
                            return false;
                        }
                    }
                } 
                lookupSetor.setParametrosAdicionais(['exames='+codigo]);               
            }
        });

        const lookupSetor = new DBLookUp(ancoraSetor, inputCodigoSetor, inputDescricaoSetor, {
            'sArquivo': 'func_lab_setor.php',
            'sQueryString':'&laboratorio='+inputCodigoLaboratorio.value,
            'sLabel': 'Pesquisar Setor',
            'sObjetoLookUp': "db_iframe_lab_setor",
            'fCallBack': (codigoSelecionado, descricao, codigo) => {            
                if(!codigo){
                    codigo = codigoSelecionado;
                }
                var pai = table[0];
                if(pai.rows[1].children[1]){
                    for(var i = 1; i < pai.rows.length; i++){
                        var rowValue = pai.rows[i].children[1].firstChild.nodeValue;
                        if(rowValue != codigo){
                            inputCodigoSetor.value = "";
                            inputDescricaoSetor.value = "";
                            inputCodigoExame.value = "";
                            inputDescricaoExame.value = "";
                            inputCodigoSetor.focus();
                            alert("Setor diferente do setor dos exames cadastrados.");
                            return false;
                        }
                    }
                }
                lookupExame.setParametrosAdicionais(['la24_i_setor='+codigo]);
            }
        });

        window.operateEvents = {
            'click .excluir': function (e, value, row, index) {
                if(confirm("Você tem certeza que deseja excluir o vinculo do exame: "+ row.codigo + ' - '+row.descricao+" ao grupo?")){
                    const formData = new FormData();
                    formData.append('acao', 'excluirVinculoExameGrupo');
                    formData.append('codigo', row.codigo);
                    HttpClient.post('lab1_lab_grupo002.RPC.php', {body: formData}).then((response) => {
                        if (response.erro) {
                            alert(response.mensagem);
                            return;
                        }
                        buscarRegistrosVinculadosGrupos();
                    });
                    alert("Vinculo de exame ao grupo excluido com sucesso.");
                }
            }
        }

        const formatterAcoes = (value, row) => {
            return '<a class="excluir" href="javascript:void(0)"><i title="Excluir Exame" class="fas fa-trash-alt" style="font-size:15px; margin-left:10px"></i></a>';
        }

        table.bootstrapTable({
            locale: 'pt-BR',
            columns: [
                {
                    title: 'Código',
                    field: 'codigo',
                    align: 'center',
                    valign: 'middle'
                },
                {
                    title: 'Código Setor',
                    field: 'codigosetor',
                    align: 'center',
                    valign: 'middle'
                },
                {
                    title: 'Código Exame',
                    field: 'codigoexame',
                    align: 'center',
                    valign: 'middle'
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

        const buscarRegistrosVinculadosGrupos = (value, row) => {
            const formData = new FormData();
            formData.append('acao', 'buscarExamesGrupo');
            formData.append('grupoLaboratorio', inputCodigoGrupoLaboratorio.value);
            formData.append('laboratorio', inputCodigoLaboratorio.value);
            HttpClient.post('lab1_lab_grupo002.RPC.php', {body: formData}).then((response) => {
                if (response.erro) {
                    alert(response.mensagem);
                    return;
                }
                table.bootstrapTable('load', response.grupoExames);
            });
        }

        const adicionarExamesAoGrupo = () => {
            if(!inputCodigoSetor.value){
                alert("Por favor preencher o setor para ser adicionado.");
                return false;
            }
            if(!inputCodigoExame.value){
                alert("Por favor preencher o exame para ser adicionado.");
                return false;
            }
            const formData = new FormData();
            formData.append('acao', 'adicionarExamesAoGrupo');
            formData.append('laboratorio', inputCodigoLaboratorio.value);
            formData.append('setor', inputCodigoSetor.value);
            formData.append('grupoLaboratorio', inputCodigoGrupoLaboratorio.value);
            formData.append('exame', inputCodigoExame.value);
            HttpClient.post('lab1_lab_grupo002.RPC.php', {body: formData}).then((response) => {
                if (response.erro) {
                    alert(response.mensagem);
                    inputCodigoExame.value = "";
                    inputDescricaoExame.value = "";
                    inputCodigoSetor.value = "";
                    inputDescricaoSetor.value = "";
                    inputCodigoSetor.focus()
                    return;
                }
                inputCodigoExame.value = "";
                inputDescricaoExame.value = "";
                inputCodigoExame.focus();
                buscarRegistrosVinculadosGrupos();
                table.bootstrapTable('load', response.grupoExames);
            });

        };

        btnAdicionar.addEventListener('click', adicionarExamesAoGrupo);

        btnVoltar.addEventListener('click', function(){
            btnVoltar.children[0].setAttribute('href', 'lab1_lab_grupo001.php?codigo_laboratorio='+inputCodigoLaboratorio.value+'&descricao_laboratorio='+inputDescricaoLaboratorio.value);
        });

        //Passar a cor do header para branco, pois por algum motivo modificou
        table[0].childNodes[1].setAttribute('style', 'color:white');

        buscarRegistrosVinculadosGrupos();

    });
</script>
</body>
</html>

