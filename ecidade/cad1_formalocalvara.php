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
require_once(modification("classes/db_formalocalvara_classe.php"));

db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);

// if (!isset($db_opcao)) {
//     $db_opcao = 1;
// }

$clformalocalvara = new cl_formalocalvara;
$clformalocalvara->rotulo->label();
$clformalocalvara->rotulo->tlabel();

?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/strings.js"></script>
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
            <legend><b>Forma de Localização Alvará</b></legend>

            <table border="0" width="300">
                <?php db_input('q167_sequencial',20,$Iq167_sequencial,true,"hidden",$db_opcao,""); ?>
                <tr>
                    <td nowrap title="<?php echo $Tq167_descricao?>"><?= $Lq167_descricao?></td>
                    <td>
                        <input type="text" id="q167_descricao" name="q167_descricao" maxlength="50"/>
                    </td>
                </tr>
                <tr>
                    <td nowrap title="<?php echo $Tq167_data_validade?>"><?= $Lq167_data_validade?></td>
                    <td>
                        <?php db_inputdata('q167_data_validade', "", "", "", true, 'text', $db_opcao); ?>
                    </td>
                </tr>
            </table>

        </fieldset>

        <br />

        <input name="incluir" type="button" id="incluir" value="Incluir" onclick="js_salvar()">
        <input name="alterar" type="button" id="alterar" value="Alterar" onclick="js_salvar()">
        <input name="excluir" type="button" id="excluir" value="Excluir" onclick="js_excluir()">
        <input name="novo"    type="button" id="novo"    value="Novo"    onclick="js_novo()">

        <br />
        <br />

        <fieldset>
            <legend>Formas de Localização adcionados</legend>
                <table class="form-container" border="1">
                    <thead>
                    <tr style="background-color: #e1e1e1">
                        <th class="text-left">Descrição</th>
                        <th class="text-left">Data de Validade</th>
                        <th class="text-center">Alterar</th>
                    </tr>
                    </thead>
                    <tbody id="lista">
                        <!--Função JS Lista popula os campos daqui-->
                    </tbody>
                </table>
        </fieldset>
    </form>
</div>
</body>
</html>

<script>
    function js_excluir() {
		obj = document.form1;

		var oParam = new Object();
		oParam.executa = "excluir";
		oParam.q167_sequencial = obj.q167_sequencial.value;

        if (confirm("Deseja excluir o registro '" + obj.q167_descricao.value + "'?")) {
            new AjaxRequest("iss1_issbase015.RPC.php", oParam, js_getExcluir).execute();
        }
    }
    
    function js_getExcluir(oRetorno) {

        alert(oRetorno.mensagem);

        if (oRetorno.erro) {
            return;
        }

        js_limpa();

        js_lista();
    }

    function js_salvar() {
		obj = document.form1;

		if (obj.q167_descricao.value === '') {
			alert('Campo descrição deve ser preenchido.');
			return;
		};

		var oParam = new Object();
		oParam.executa = "salvar";
		oParam.q167_sequencial = obj.q167_sequencial.value;
		oParam.q167_descricao = obj.q167_descricao.value;
		oParam.q167_data_validade = obj.q167_data_validade.value;

		new AjaxRequest("iss1_issbase015.RPC.php", oParam, js_getSalvar).execute();
	}

    function js_getSalvar(oRetorno) {

		alert(oRetorno.mensagem);

		if (oRetorno.erro) {
			return;
		}

        js_limpa();
        
        js_lista();
	}

    function js_limpa() {
		obj = document.form1;
        obj.q167_sequencial.value = "";
        obj.q167_descricao.value = "";
        obj.q167_data_validade.value = "";
    }

    js_lista();
    
    function js_lista()
    {
        var oParam = new Object();
		oParam.executa = "lista";

		new AjaxRequest("iss1_issbase015.RPC.php", oParam, js_getLista).execute();    
    }

    function js_getLista(oRetorno) 
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
        const lista = document.getElementById("lista");
        lista.innerHTML = "";

        for (var index = 0; index < oRetorno.lista.length; index++) {
            const tr = document.createElement("tr");
            const tdDesc = document.createElement("td");
            const nodeDesc = document.createTextNode(oRetorno.lista[index].q167_descricao);
            tdDesc.appendChild(nodeDesc);
            tr.appendChild(tdDesc);
            tr.setAttribute("class","cores");

            
            var data_validade = oRetorno.lista[index].q167_data_validade;
            if (data_validade == null) {
                data_validade = '';
            } else {
                data_validade = js_formatar(data_validade, 'd')
            }

            const tdDataValidade = document.createElement("td");
            const nodeDataValidade = document.createTextNode(data_validade);
            tdDataValidade.appendChild(nodeDataValidade);
            tr.appendChild(tdDataValidade);
            tr.setAttribute("class","cores");

            const tdAlt = document.createElement("td");
            const href = document.createElement("a");
            href.setAttribute('href', "javascript:void(0);");
            const sequencial = oRetorno.lista[index].q167_sequencial;
            const descricao = oRetorno.lista[index].q167_descricao;
            
            const js_preenchecampos = "js_preencheCampos('"+sequencial+"','"+descricao+"', '"+data_validade+"')";
            href.setAttribute("onclick", js_preenchecampos);
            href.text = "A";
            tdAlt.appendChild(href);
            tdAlt.setAttribute("class", "text-center");
            tr.appendChild(tdAlt);

            lista.appendChild(tr);
        }

    }

    function js_preencheCampos(sequencial, descricao, data_validade)
    {
		obj = document.form1;
        obj.incluir.disabled = true;
        obj.alterar.disabled = false;
        obj.excluir.disabled = false;

        obj.q167_sequencial.value = sequencial;
        obj.q167_descricao.value = descricao;
        obj.q167_data_validade.value = data_validade;
    }

    function js_novo()
    {
        js_limpa();
        obj.incluir.disabled = false;
        obj.alterar.disabled = true;
        obj.excluir.disabled = true;
    }

</script>