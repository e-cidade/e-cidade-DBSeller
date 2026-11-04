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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_app.utils.php"));
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <?php
    db_app::load("
    scripts.js,
    prototype.js,
    datagrid.widget.js,
    strings.js,
    ");
    db_app::load("estilos.css, grid.style.css");
    ?>
    <style>
        td {
            white-space: nowrap;
        }

        fieldset table td:first-child {
            width: 110px;
            white-space: nowrap;
        }
    </style>
</head>
<body>
<div style="margin:auto;width:500px">
    <fieldset>
        <legend>Forma de Reclamação</legend>
        <form id="form">
            <input type="hidden" name="action"/>
            <input type="hidden" name="p42_sequencial"/>
        <div style="margin: 2px; width: 100%">
            <b>Descrição :</b>
            <input name="p42_descricao" style="width: 100%"/> <br>
        </div>
        <div style="width: 100%;float:left">
<!--            <div style="float:left;margin: 2px">-->
<!--                <b>Data Inicio :</b><br>-->
<!--                <input type="date" name="p42_dtinicio"/>-->
<!--            </div>-->
            <div style="float:left;margin: 2px">
                <b>Data Fim :</b><br>
                <input type="date" name="p42_dtfim"/>
            </div>
        </div>
        <div style="float:right">
            <input type="submit" value="Cancelar" id="btnLimpar"/>
            <input type="submit" value="salvar" id="btnSalvar"/>
        </div>
        </form>
    </fieldset>
</div>
<div style="max-width:700px;max-height:200px;margin: auto;overflow: auto;margin-top: 20px;">
    <table border="1" style="width: 100%;text-align: left;">
        <thead style="text-align: center">
        <tr>
            <th>Código</th>
            <th>Descrição</th>
<!--            <th>Data Inicio</th>-->
            <th>Data Fim</th>
            <th>Ações <input type="submit" value="Recarregar" onclick="getFormasReclamacao()"></th>
        </tr>
        </thead>
        <tbody id="table-body" style="background: #ffff">
        </tbody>
    </table>
</div>
<script>
    const FILE_RPC = 'ouv4_formareclamacao.RPC.php';
    const formulario = document.querySelector("#form");
    const TableBody = document.querySelector("#table-body");
    var dadosFormaReclamacao = [];


    formulario.btnSalvar.addEventListener("click",async function(event){
         event.preventDefault();
        js_divCarregando('Enviando dados...',"loading");
         try
         {
            const resp = await fetch(FILE_RPC,{
                method:"POST",
                body: new FormData(form),
            });
            const data = await resp.json();
             if (!data.success) {
                 throw new Error(data.message);
             }
             getFormasReclamacao();
             limparFormulario();
             alert(data.message);
         }catch (e){
             alert(e.message ? e.message : "Ocorreu um erro!");
         }
        js_removeObj('loading');

    });

    formulario.btnLimpar.addEventListener("click",function(event){
        event.preventDefault();
        limparFormulario();
    });

    function editar(p42_sequencial){
       const formaReclamacao =  dadosFormaReclamacao.find(value => p42_sequencial == value.p42_sequencial);
      if(formaReclamacao){
          formulario.action.value = "forma-reclamacao-update";
          formulario.p42_sequencial.value =  formaReclamacao.p42_sequencial
          formulario.p42_descricao.value =  formaReclamacao.p42_descricao
          //formulario.p42_dtinicio.value =  formaReclamacao.p42_dtinicio
          formulario.p42_dtfim.value =   formaReclamacao.p42_dtfim
          formulario.btnSalvar.value = "Alterar";
          formulario.btnLimpar.style.display = "";
      }else{
          alert("Forma Reclamação não encontrada!");
      }

    }

    async function getFormasReclamacao() {
        js_divCarregando('Buscando dados...',"loading");
        try {

            const form = new FormData();
            form.append('action', 'forma-reclamacao');
            const resp = await fetch(FILE_RPC, {
                method: 'POST',
                body: form,
            });
            const data = await resp.json();

            if (!data.success) {
                throw new Error(data.message);
            }
            dadosFormaReclamacao = data.data;
            buildBodyTable(data.data);

        } catch (e) {
            alert(e.message ? e.message : "Ocorreu um erro!");
        }
        js_removeObj('loading');
    }

    function buildBodyTable(data) {
        var bodyTable = [];
        data.forEach(function (element) {

            let dataFim = ' - ';
            let dataInicio = ' - ';

            if(element.p42_dtfim){
                 dataFim =  new Date(element.p42_dtfim);
                 dataFim = dataFim.toLocaleDateString('pt-BR', { timeZone: 'UTC' });
            }

            if(element.p42_dtinicio){
                dataInicio =  new Date(element.p42_dtinicio);
                dataInicio = dataInicio.toLocaleDateString('pt-BR',{ timeZone: 'UTC' });
            }

            bodyTable.push(`
                   <tr>
                       <td>${element.p42_sequencial}</td>
                       <td>${element.p42_descricao}</td>
                       <!--<td style="text-align: center">${dataInicio}</td>-->
                       <td style="text-align: center">${dataFim}</td>
                       <td style="text-align: center">
                          <input type="submit" value="Alterar" onclick="editar(${element.p42_sequencial})">
                          <input type="submit" value="Excluir" onclick="deleteFormaReclamacao(${element.p42_sequencial})">
                       </td>
                    </tr>
              `);

        });

        TableBody.innerHTML = bodyTable.join("");
    }

    function limparFormulario(){
        formulario.action.value = "forma-reclamacao-save";
        formulario.reset();
        formulario.p42_sequencial.value = '';
        formulario.btnSalvar.value = "Salvar";
        formulario.btnLimpar.style.display = "none";
    }

    function main(){
        formulario.action.value = "forma-reclamacao-save";
        formulario.btnLimpar.style.display = "none";
        getFormasReclamacao();
    }

    async function deleteFormaReclamacao(p42_sequencial){
        event.preventDefault();
        if(!confirm("Deseja excluir essa forma de reclamação?")){
            return;
        }
        js_divCarregando('Enviando dados...',"loading");
        try
        {
            const form  = new FormData();
            form.set('p42_sequencial',p42_sequencial);
            form.set('action','forma-reclamacao-delete');
            const resp = await fetch(FILE_RPC,{
                method:"POST",
                body: form,
            });
            const data = await resp.json();
            if (!data.success) {
                throw new Error(data.message);
            }
            getFormasReclamacao();
            limparFormulario();
            alert(data.message);
        }catch (e){
            alert(e.message ? e.message : "Ocorreu um erro!");
        }
        js_removeObj('loading');
    }

    main();

</script>
</body>
</html>
