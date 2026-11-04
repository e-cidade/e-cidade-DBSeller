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

use ECidade\RecursosHumanos\ESocial\Model\Formulario\Tipo;
use ECidade\RecursosHumanos\ESocial\Service\ProcessamentoExternoService;

require(modification("libs/db_stdlib.php"));
require(modification("libs/db_conecta.php"));
include(modification("libs/db_sessoes.php"));
include(modification("libs/db_usuariosonline.php"));
include(modification("dbforms/db_funcoes.php"));
include(modification("dbforms/db_classesgenericas.php"));
$clcriaabas     = new cl_criaabas;
$db_opcao = 1;
$inst = db_getsession('DB_instit');
$tipos = Tipo::getTitulos();
$empregadores =  ProcessamentoExternoService::empregador($inst);
?>
<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<style>
.btn {
    border: none;
    padding: 8px;
    text-decoration: none;
    cursor: pointer;
    transition: background .3s;
    color: #FFF;
    margin: 0;
    font-size: 15px;
    font-weight: 500;
}

/*SUCCESS*/
.btn-success {
    background-color: rgb(75, 200, 138);
    outline: none;
}
.btn-success:hover {
    background-color: rgb(116, 158, 137);  
}
.btn-success:active {
    background-color: rgb(27, 66, 50);
}
/*INFO*/
.btn-info {
    background-color: rgb(61, 157, 246);
    outline: none;
}
.btn-info:hover {
    background-color: rgb(116, 134, 158);
}
.btn-info:active {
    color:rgb(61, 157, 246);
    background-color: rgb(31, 62, 88);
}
.btn-principal{
    position: fixed; 
    right: 5px;
    padding: 4px !important;
}
/*Texto de informação cabeçalho*/
.texto-info{
    padding-left: 16px;
    font-size: larger;
}
/*Conteúdo do form*/
.form-conteudo{
    background-color: #e1dede;
    margin-bottom: 0px;
    padding:16px 16px 16px 16px;
    border: 1px solid #4a789c;
}
/**FIELD */
/*LABELS*/
.label_form {
    width: 80px;
    display: inline-block;
}
/*INPUTS*/
.input_form {
    margin-left:65px;
    font-size:15px; 
    width: 60%;
    padding:15px; 
    border-radius:5px; 
    margin-bottom:5px; 
}
/**SELECT*/
.select-form {
  width: 60%;
  padding:8px; 
  margin-left: 145px;
  font-weight: 500 !important;
}
/**SPINNER */
.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
/*********************/
/*MODAL*/
 /* The Modal (background) */
 .modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 10000; /* Sit on top */
  padding-top: 100px; /* Location of the box */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content */
.modal-content {
  position: relative;
  background-color: #fefefe;
  margin: auto;
  padding: 0;
  border: 1px solid #888;
  width: 80%;
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2),0 6px 20px 0 rgba(0,0,0,0.19) !important;
  -webkit-animation-name: animatetop;
  -webkit-animation-duration: 0.4s;
  animation-name: animatetop;
  animation-duration: 0.4s;
  max-height: calc(100vh - 210px);
  overflow-y: auto;
}

/* Add Animation */
@-webkit-keyframes animatetop {
  from {top:-300px; opacity:0} 
  to {top:0; opacity:1}
}

@keyframes animatetop {
  from {top:-300px; opacity:0}
  to {top:0; opacity:1}
}

/* The Close Button */
.close {
  color: white;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: #000;
  text-decoration: none;
  cursor: pointer;
}

.modal-header {
  padding: 2px 16px;
  background-color: #4a789c;
  color: white;
}

/*FOOTER*/
.modal-footer {
  background-color: #4a789c;
  padding:10px 0px 15px 15px;
  color: white;
}
</style>
</head>
<body bgcolor=#CCCCCC leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
<button id="myBtn" class="btn btn-success btn-principal">Enviar evento Esocial</button>
<!-- The Modal -->
<div id="myModal" class="modal">
  <!-- Modal content -->
  <div class="modal-content">
  <!-- <div class="loader"></div> -->
    <div class="modal-header">
      <span class="close">&times;</span>
      <h2>Processamento e Envio para o eSocial</h2>
    </div>
    <div class="modal-body">
      <div class="texto-info">
        <p>O eSocial é um projeto do governo federal que visa unificar o envio de informações pelo empregador em relações aos seus empregadores. 
          Ao clicar em Processar e enviar, deverá ter certeza que todos os dados foram devidamente cadastrados nas abas: 
            Dados Pessoais, Admissionais, Documentos, Movimentações, Local de Trabalho, Dependentes (se tiver), para o correto e completo 
            envio das informações conforme o layout do evento selecionado abaixo:</b>
          </p>
        </div>
      </div>
      <div class="form-conteudo">
        <form id="envioEvento" method="post">
      <fieldset disabled>
        <legend class="legend-border">Servidor(a)</legend>
        <label class="label_form">Matricula*</label>
        <input type="text" id="matricula" class="input_form">
        <br><br>
        <label class="label_form">Nome*</label>
        <input type="text" id="servidor" class="input_form">
        <input type="hidden" id="instituicao">
        <input type="hidden" id="cgm">
      </fieldset>
      <br><br>
      <fieldset>
        <legend class="legend-border">Evento*</legend>
        <select  id="evento" class="select-form">
          <?php foreach($tipos as $chave => $tipo):?>
            <?php 
              $eventos = ['3'];
             foreach ($eventos as $evento):
              if($evento == $chave):?>
              <option value="<?= $chave;?>"><?= $tipo; ?></option>
            <?php endif;?>
          <?php endforeach;?>
          <?php endforeach;?>
        </select>
      </fieldset>
      <br><br>
      <fieldset>
        <legend class="legend-border">Empregadores*</legend>
        <select class="select-form" id="empregador">
          <?php foreach($empregadores as $empregador):?>
            <option value="<?= $empregador->cgm;?>"><?= $empregador->nome; ?></option>
          <?php endforeach;?>
        </select>
      </fieldset>
    </div>
        <div class="modal-footer">
          <button class="btn btn-success" id="btnSalvar" type="submit"></button>
          <!-- <button class="btn btn-info"  id="myBtn" type="button">Cancelar</button> -->
        </div>
    </form>
      </div>
  </div>
<table width="100%" height="18"  border="0" cellpadding="0" cellspacing="0" bgcolor="#5786B2">
  <tr> 
    <td width="360">&nbsp;</td>
    <td width="263">&nbsp;</td>
    <td width="25">&nbsp;</td>
    <td width="140">&nbsp;</td>
  </tr>
</table>
<table valign="top" marginwidth="0" width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr> 
    <td height="100%" align="left" valign="top" bgcolor="#CCCCCC"> 
     <?php
			 $clcriaabas->identifica = array(
		                                    "rhpessoal"      => "Dados Pessoais",
						     "rhadmissional"  => "Dados admissionais",
		                                    "rhpesdoc"       => "Documentos",
		                                    "rhpessoalmov"   => "Movimentações",
		                                    "rhdepend"       => "Dependentes",
		                                    "rhpeslocaltrab" => "Locais de Trabalho",
			                                "rhpontofixo"    => "Ponto Fixo",
                                        	"rhpontosalario" => "Ponto de Salário",
                                        	"rhcedencia"     => "Cedidos / Disposição"
			                                
		                                  );
		                                   
			 $clcriaabas->sizecampo  = array(
		                                    "rhpessoal"      => "20",
						     "rhadmissional"  => "20",
		                                    "rhpesdoc"       => "15",
		                                    "rhpessoalmov"   => "20",
		                                    "rhdepend"       => "15",
		                                    "rhpeslocaltrab" => "20",
		                                    "rhpontofixo"    => "15",
		                                    "rhpontosalario" => "15",
		                                    "rhcedencia"     => "20"
			                                );
			                                
			 $clcriaabas->src        = array( "rhpessoal" => "pes1_rhpessoal004.php" );
			 
			 $clcriaabas->disabled   = array(
						     "rhadmissional"  => "true",
  						     "rhpesdoc"       => "true",
		                                    "rhpessoalmov"    => "true",
		                                    "rhdepend"        => "true",
		                                    "rhpeslocaltrab"  => "true",
		                                    "rhpontofixo"     => "true",
                                        	"rhpontosalario"  => "true",
                                        	"rhcedencia"      => "true"
		                                  ); 
	   $verifica_permissao = db_permissaomenu(db_getsession('DB_anousu'), 952, 8820);    
       if($verifica_permissao == "true"){
        
         $clcriaabas->identifica["rhsuspensaopag"] ="Suspensão de Pagamentos";
         $clcriaabas->sizecampo["rhsuspensaopag"]  = "20";  
         $clcriaabas->src["rhsuspensaopag"]        = "pes1_rhsuspensaopag001.php";                 
       }
       $clcriaabas->cria_abas();                               

       ?> 
     </td>
   </tr>
</table>
<form name="form1">
</form>
<?php 
	db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
<script>
  const urlRpc = 'esocial_externo.RPC.php'
  const formSub = document.querySelector('#envioEvento')
  const matricula = document.getElementById('matricula')
  const instituicao = document.getElementById('instituicao')
  const cgm = document.getElementById('cgm')
  const empregador = document.getElementById('empregador')
  const servidor = document.getElementById('servidor')
  const btnSalvar = document.getElementById('btnSalvar')
  // Get the modal
  var modal = document.getElementById("myModal");
  // Get the button that opens the modal
  var btn = document.getElementById("myBtn");
  // Get the <span> element that closes the modal
  var span = document.getElementsByClassName("close")[0];
  // When the user clicks the button, open the modal  
  

  btn.onclick = function() {
    matricula.value = JSON.parse(localStorage.getItem('matricula'));
    instituicao.value = JSON.parse(localStorage.getItem('instituicao'));
    servidor.value = JSON.parse(localStorage.getItem('servidor'));
    cgm.value = JSON.parse(localStorage.getItem('cgm'));
    // dbvinculo.value = JSON.parse(localStorage.getItem('vinculo'))
    if (matricula.value == '') {
      alert('É necessário ter uma matrícula cadastrada')
      return
    } else {
      vinculo(matricula.value)
    }
  }

  // When the user clicks on <span> (x), close the modal
  span.onclick = function() {
    modal.style.display = "none";
    servidor.innerHTML = "";
  }
  
    btnSalvar.innerHTML = "Salvar/ Enviar para o eSocial"
    btnSalvar.addEventListener('click',(e)=>{
      e.preventDefault()
      if (matricula.value == '' || instituicao.value == '' || cgm.value == '' || empregador.value == '') {
        alert('E necessario a seleção de um (servidor e empregador).')
        return
      }
      btnSalvar.innerHTML = "Aguarde enviando S-2200..."
      
      const dadosForm = new FormData()
      dadosForm.append('evento', 'S2200')
      dadosForm.append('matricula', matricula.value)
      dadosForm.append('instituicao', instituicao.value)
      dadosForm.append('cgm', cgm.value)
      dadosForm.append('empregador', empregador.value)
      fetch(urlRpc,{
        method: 'POST',
        body:dadosForm
      })
      .then(response=>response.json())
      .then(response=>{  
        if (!response) {
          alert('Não ha alteração para este servidor.')
          btnSalvar.innerHTML = "Salvar/ Enviar para o eSocial"
        } else {
          alert('Enviado com sucesso.')
          modal.style.display = "none";
          btnSalvar.innerHTML = "Salvar/ Enviar para o eSocial"
        }
      })
    })

const vinculo = function(matricula) {
  fetch(urlRpc+'?matricula='+matricula,{
    method: 'GET'
  })
  .then(resp => resp.json())
  .then(resp => {
    if (resp.rh30_vinculoemprego === "t") {
      modal.style.display = "block";
    } else {
      alert('Servidor não possui vínculo')
      return
    }
  })
}

</script>
</body>
</html>
