<?php
/*
 *     E-cidade Software Publico para Gestao Municipal                
 *  Copyright (C) 2014  DBSeller Servicos de Informatica             
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

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);

// if (!isset($db_opcao)) {
//     $db_opcao = 1;
// }

$cltipoproprietario = new cl_tipoproprietario;
$cltipoproprietario->rotulo->label();
$cltipoproprietario->rotulo->tlabel();

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/AjaxRequest.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style type="text/css">
td {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 12px;
}
input {
  font-family: Arial, Helvetica, sans-serif;
  font-size: 12px;
  height: 17px;
  border: 1px solid #999999;
}
.cores:nth-child(even) {
    background: #FFF;
}
.cores:nth-child(odd) {
    background: #efefef;
}
table.form-container tr td {
    font-weight: normal !important;
}
</style>
</head>

<body onLoad="js_trocacordeselect()">
<div class="container">
    <form name="form1" method="post" onSubmit="return js_verifica_campos_digitados();" action="">
        <fieldset>
            <legend><b>Tipo de Proprietario</b></legend>

            <table border="0" width="300">
                <?= db_input('j163_tipoproprietario',20,$Ij163_tipoproprietario,true,"hidden",$db_opcao,"") ?>
                <tr>
                    <td nowrap title="<?php echo $Tj163_descricao?>"><?= $Lj163_descricao?></td>
                    <td>
                        <?php
                        db_input('j163_descricao',20,$Ij163_descricao,true,"text",$db_opcao,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo $Tj163_abreviatura?>"><?= $Lj163_abreviatura?></td>
                    <td>
                        <?php
                        db_input('j163_abreviatura',20,$Ij163_abreviatura,true,"text",$db_opcao,"");
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo $Tj163_pesfisjur?>"><?= $Lj163_pesfisjur?></td>
                    <td>
                        <select name="j163_pesfisjur" id="j163_pesfisjur" style="width:124px">
                            <option value="0">Física e Jurídica</option>
                            <option value="1">Física</option>
                            <option value="2">Jurídica</option>
                        </select>
                    </td>
                </tr>
                <?php
                // db_input('j163_tipopromitente',2,$Ij163_tipopromitente,true,"hidden",$db_opcao,""); 
                ?>
            </table>

        </fieldset>
        
        <fieldset>
            <legend>Tipos de Proprietários adcionados</legend>
                <table class="form-container" border="1">
                    <thead>
                    <tr style="background-color: #e1e1e1">
                        <th class="text-center">Tipo de Proprietário</th>
                        <th class="text-center">Abreviatura</th>
                        <th class="text-center">Pessoa</th>
                        <th class="text-center">Alterar</th>
                    </tr>
                    </thead>
                    <tbody id="listaProprietario">
                        <!--Função JS Lista popula os campos daqui-->
                    </tbody>
                </table>
        </fieldset>
        
        <fieldset id="fieldsetTipoPromitente">
            <legend>Vincular Promitentes</legend>
                <table id="tableTipoPromitente" class="form-container" border="1">
                    <thead>
                    <tr style="background-color: #e1e1e1">
                        <th class="text-center"><a onclick="js_marcaTodos()">M</a></th>
                        <th class="text-center">Tipo de Promitente</th>
                    </tr>
                    </thead>
                    <tbody id="listaPromitente">
                        <!--Função JS Lista popula os campos daqui-->
                    </tbody>
                </table>
        </fieldset>

        <input name="incluir" type="button" id="incluir" value="Incluir" onclick="js_salvar()">
        <input name="alterar" type="button" id="alterar" value="Alterar" onclick="js_salvar()">
        <input name="excluir" type="button" id="excluir" value="Excluir" onclick="js_excluir()" >

    </form>
</div>
</body>
</html>

<script>
//CHAMADAS DE MÉTODOS
    js_listaTipoProprietario();
    js_listaTipoPromitente();

//CRUD
    function js_excluir() {
		obj = document.form1;

		var oParam = new Object();
		oParam.executa = "excluir";
		oParam.j163_tipoproprietario = obj.j163_tipoproprietario.value;

		new AjaxRequest("cad1_tipoproprietario.RPC.php", oParam, js_getExcluir).execute();
    }
    
    function js_getExcluir(oRetorno) {

        alert(oRetorno.mensagem);

        if (oRetorno.erro) {
            return;
        }

        js_limpa();

        js_listaTipoProprietario();

        js_listaTipoPromitente();
    }

    function js_salvar() {
		obj = document.form1;

		if (obj.j163_descricao.value === '') {
			alert('Campo descrição deve ser preenchido.');
			return;
		};

		var oParam = new Object();
		oParam.executa = "salvar";
		oParam.j163_tipoproprietario = obj.j163_tipoproprietario.value;
		oParam.j163_descricao = obj.j163_descricao.value;
		oParam.j163_abreviatura = obj.j163_abreviatura.value;
		oParam.j163_pesfisjur = obj.j163_pesfisjur.value;

        var checkbox = document.querySelectorAll('input[tipo=ok]:checked');
        var check = "";
        checkbox.forEach(function(el){

            check += el.value + ",";
        });

        oParam.j165_tipopromitente = check;

		new AjaxRequest("cad1_tipoproprietario.RPC.php", oParam, js_getSalvar).execute();
	}

    function js_getSalvar(oRetorno) {

		alert(oRetorno.mensagem);

		if (oRetorno.erro) {
			return;
		}

        js_limpa();

        js_listaTipoProprietario();

        js_listaTipoPromitente();
	}
//MÉTODOS DO FORM
    function js_limpa() {
		obj = document.form1;
        obj.j163_tipoproprietario.value = "";
        obj.j163_descricao.value = "";
        obj.j163_abreviatura.value = "";
        obj.j163_pesfisjur.value = "";
        obj.j165_tipopromitente.value = "";
    }

    function js_preencheCampos(sequencial, descricao, abreviatura, pessoa, sigla)
    {
		obj = document.form1;
        obj.incluir.disabled = true;
        obj.alterar.disabled = false;
        obj.excluir.disabled = false;

        obj.j163_tipoproprietario.value = sequencial;
        obj.j163_descricao.value = descricao;
        obj.j163_abreviatura.value = abreviatura;
        obj.j163_pesfisjur.value = pessoa;
        obj.j165_tipopromitente.value = sigla;

        js_listaVinculo();
    }

    //Atribui inicio da variavel para marcar o checkbox
    var marcado = false;
    function js_marcaTodos()
    {
        // var checkbox = document.getElementsByName('j165_tipopromitente');
        var checkbox = document.querySelectorAll('input[tipo=ok]');
        var check = "";
        checkbox.forEach(function(el){
            if (marcado == false) {
                el.checked = true;
            } else {
                el.checked = false;
            }
        });

        if (marcado == false) {
            marcado = true;
        } else {
            marcado = false;
        }
    }    

//LISTAS
    function js_listaTipoProprietario()
    {
        var oParam = new Object();
		oParam.executa = "lista";

		new AjaxRequest("cad1_tipoproprietario.RPC.php", oParam, js_getTipoProprietario).execute();    
    }

    function js_getTipoProprietario(oRetorno) 
    {
        if (oRetorno.mensagem != "") {
            alert(oRetorno.mensagem);
        }

        if (oRetorno.erro) {
            return;
        }

        obj = document.form1;
        obj.incluir.disabled = false;
        obj.alterar.disabled = true;
        obj.excluir.disabled = true;
        const lista = document.getElementById("listaProprietario");
        lista.innerHTML = "";

        for (var index = 0; index < oRetorno.lista.length; index++) {
            const tr = document.createElement("tr");
            tr.setAttribute("class","cores");

            const tdDesc = document.createElement("td");
            const desc = document.createTextNode(oRetorno.lista[index].j163_descricao);
            tdDesc.setAttribute("class", "text-center");
            tdDesc.appendChild(desc);
            tr.appendChild(tdDesc);

            const tdAbrev = document.createElement("td");
            const nodeAbrev = document.createTextNode(oRetorno.lista[index].j163_abreviatura);
            tdAbrev.appendChild(nodeAbrev);
            tdAbrev.setAttribute("class", "text-center");
            tr.appendChild(tdAbrev);

            var retornoPessoa = oRetorno.lista[index].j163_pesfisjur;

            if (retornoPessoa === '0') {
                retornoPessoa = "Física e Jurídica";
            } 
            if (retornoPessoa === '1') {
                retornoPessoa = "Física";
            }
            if (retornoPessoa === '2') {
                retornoPessoa = "Jurídica";
            }
            const tdPessoa = document.createElement("td");
            const nodePessoa = document.createTextNode(retornoPessoa);
            tdPessoa.appendChild(nodePessoa);
            tdPessoa.setAttribute("class", "text-center");
            tr.appendChild(tdPessoa);

            const tdAlt = document.createElement("td");
            const href = document.createElement("a");
            href.setAttribute('href', "javascript:void(0);");
            const sequencialProprietario = oRetorno.lista[index].j163_tipoproprietario;
            const descricao = oRetorno.lista[index].j163_descricao;
            const abreviatura = oRetorno.lista[index].j163_abreviatura;
            const pessoa = oRetorno.lista[index].j163_pesfisjur;
            const sequencialPromitente = oRetorno.lista[index].j165_tipopromitente;
            const js_preenchecampos = "js_preencheCampos('"+sequencialProprietario+"','"+descricao+"','"+abreviatura+"','"+pessoa+"','"+sequencialPromitente+"')";
            href.setAttribute("onclick", js_preenchecampos);
            href.text = "A";
            tdAlt.appendChild(href);
            tdAlt.setAttribute("class", "text-center");
            tr.appendChild(tdAlt);

            lista.appendChild(tr);
        }
    }

    function js_listaTipoPromitente()
    {
        var oParam = new Object();
		oParam.executa = "lista";

		new AjaxRequest("cad1_tipopromitente.RPC.php", oParam, js_getTipoPromitente).execute();    
    }

    function js_getTipoPromitente(oRetorno) 
    {
        if (oRetorno.mensagem != "") {
            alert(oRetorno.mensagem);
        }

        if (oRetorno.erro) {
            return;
        }

        obj = document.form1;
        obj.incluir.disabled = false;
        obj.alterar.disabled = true;
        obj.excluir.disabled = true;
        const lista = document.getElementById("listaPromitente");
        lista.innerHTML = "";

        for (var index = 0; index < oRetorno.lista.length; index++) {
            const tr = document.createElement("tr");
            tr.setAttribute("class","cores");

            const tdCheck = document.createElement("td");
            const check = document.createElement("input");
            check.setAttribute("type", "checkbox");
            check.setAttribute("value", oRetorno.lista[index].j164_tipopromitente);
            check.setAttribute("id", "j165_tipopromitente_"+oRetorno.lista[index].j164_tipopromitente);
            check.setAttribute("name", "j165_tipopromitente");
            tdCheck.setAttribute("class", "text-center");
            tdCheck.setAttribute("width", "10px");
            check.setAttribute("tipo", "ok");

            tdCheck.appendChild(check);
            tr.appendChild(tdCheck);

            const tdDesc = document.createElement("td");
            const desc = document.createTextNode(oRetorno.lista[index].j164_descricao);
            tdDesc.setAttribute("class", "text-center");

            tdDesc.appendChild(desc);
            tr.appendChild(tdDesc);
            lista.appendChild(tr);
        }
    }

    function js_listaVinculo()
    {
        var oParam = new Object();
		oParam.executa = "buscarVinculo";
		oParam.j163_tipoproprietario = document.form1.j163_tipoproprietario.value;

		new AjaxRequest("cad1_tipoproprietario.RPC.php", oParam, js_getVinculo).execute();    
    }

    function js_getVinculo(oRetorno) 
    {
        if (oRetorno.mensagem != "") {
            alert(oRetorno.mensagem);
        }

        if (oRetorno.erro) {
            return;
        }

        const lista = document.getElementById("listaPromitente");
        
        var checkbox = document.querySelectorAll('input[tipo=ok]:checked');
        var check = "";
        checkbox.forEach(function(el){

            el.checked = false;
        });

        for (var index = 0; index < oRetorno.lista.length; index++) {
            var promit = document.getElementById("j165_tipopromitente_"+oRetorno.lista[index].j165_tipopromitente);
            promit.checked = true;
        }

    }
</script>