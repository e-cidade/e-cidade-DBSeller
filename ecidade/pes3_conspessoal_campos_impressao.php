<?php
/**
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
require_once(modification("dbforms/db_funcoes.php"));

db_postmemory($_POST);
parse_str($_SERVER["QUERY_STRING"]);

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
</head>
<body>
<form>
 <input type="hidden" name="regist" id="regist" value="<?=$regist?>">
 <input type="hidden" name="mes" id="mes" value="<?=$mes?>">
 <input type="hidden" name="ano" id="ano" value="<?=$ano?>">
 <div class="container">
 <table>
   <tr>
     <td>
       <fieldset>
         <legend><b>Campos Ficha Cadastral</b></legend>
         <table>
           <tr>
             <td>
                <table>
                  <tr>
                    <td align="center" colspan="2" class="table_header"><b>Dados Cadastrais</b></td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraRegist" id="mostraRegist" checked>Matrícula do Servidor</td>
                    <td><input type="checkbox" name="mostraEndereco" id="mostraEndereco" checked>Endereço</td>
                  </tr>  
                  <tr>
                    <td><input type="checkbox" name="mostraCgm" id="mostraCgm" checked>Numcgm</td>
                    <td><input type="checkbox" name="mostraBairro" id="mostraBairro" checked>Bairro</td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraNome" id="mostraNome" checked>Nome</td>
                    <td><input type="checkbox" name="mostraCep" id="mostraCep" checked>CEP</td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraNascimento" id="mostraNascimento" checked>Nascimento</td>
                    <td><input type="checkbox" name="mostraMunicipio" id="mostraMunicipio" checked>Município</td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraRegime" id="mostraRegime" checked>Regime</td>
                    <td><input type="checkbox" name="mostraLotacao" id="mostraLotacao" checked>Lotação</td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraVinculo" id="mostraVinculo" checked>Tipo de Vínculo</td>
                    <td><input type="checkbox" name="mostraPrevidencia" id="mostraPrevidencia" checked>Tab.Prev.</td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraCBO" id="mostraCBO" checked>CBO</td>
                    <td><input type="checkbox" name="mostraAdmissao" id="mostraAdmissao" checked>Admissão</td>                    
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraVinculoRais" id="mostraVinculoRais" checked>Vínculo</td>
                    <td><input type="checkbox" name="mostraCargo" id="mostraCargo" checked>Cargo</td>
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraPadrao" id="mostraPadrao" checked>Padrão</td>
                    <td><input type="checkbox" name="mostraFuncao" id="mostraFuncao" checked>Função</td>
                  </tr>  
                  <tr>
                    <td><input type="checkbox" name="mostraPadraoSecundario" id="mostraPadraoSecundario" checked>Padrão Secundário</td>
                    <td><input type="checkbox" name="mostraHrsMensais" id="mostraHrsMensais" checked>Nr hrs mensais</td>                    
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraHrsSemanais" id="mostraHrsSemanais" checked>Horas Semanais</td>
                    <td><input type="checkbox" name="mostraBanco" id="mostraBanco" checked>Banco</td>                    
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraConta" id="mostraConta" checked>Conta Corrente</td>
                    <td><input type="checkbox" name="mostraTipoContrato" id="mostraTipoContrato" checked>Tipo de Contrato</td>                    
                  </tr>
                  <tr>
                    <td><input type="checkbox" name="mostraTerminoContrato" id="mostraTerminoContrato" checked>Término Contrato Temporário</td>
                    <td><input type="checkbox" name="mostraPai" id="mostraPai" checked>Pai</td>                    
                  </tr> 
                  <tr>
                    <td><input type="checkbox" name="mostraMae" id="mostraMae" checked>Mãe</td>
                    <td><input type="checkbox" name="mostraTipoAposentadoria" id="mostraTipoAposentadoria" checked>Tipo Aposentadoria</td>                    
                  </tr>
                </table>
             </td>
             <td valign="top">
               <table>
                 <tr>
                  <td align="center" colspan="2" class="table_header"><b>Outros Dados</b></td>
                  <tr>                                       
                    <td><input type="checkbox" name="mostraEstadoCivil"   id="mostraEstadoCivil"   checked>Estado Civil</td>
                    <td><input type="checkbox" name="mostraNacionalidade" id="mostraNacionalidade" checked>Nacionalidade</td>
                  </tr>                                                  
                  <tr>                                                   
                    <td><input type="checkbox" name="mostraTipoSalario"       id="mostraTipoSalario"       checked>Tipo de Salário</td>
                    <td><input type="checkbox" name="mostraNumeroCartaoPonto" id="mostraNumeroCartaoPonto" checked>Cartão Ponto</td>
                  </tr>
                  <tr>                                                                                                     
                    <td><input type="checkbox" name="mostraTipoFolha"     id="mostraTipoFolha"     checked>Tipo de Folha</td>
                    <td><input type="checkbox" name="mostraGrauInstrucao" id="mostraGrauInstrucao" checked>Instrução</td>
                  </tr>                                                  
                  <tr>                                                   
                    <td><input type="checkbox" name="mostraPortadorMolestia" id="mostraPortadorMolestia" checked>Portador de Moléstia</td>
                    <td><input type="checkbox" name="mostraDificienteFisico" id="mostraDificienteFisico" checked>Deficiente Físico</td>
                  </tr>                                                  
                  <tr>                                                   
                    <td><input type="checkbox" name="mostraNaturalidade" id="mostraNaturalidade" checked>Naturalidade</td>
                    <td><input type="checkbox" name="mostraSexo"         id="mostraSexo"         checked>Sexo</td>
                  </tr>                                                  
                  <tr>                                                   
                    <td><input type="checkbox" name="mostraDataOpcaoFGTS" id="mostraDataOpcaoFGTS" checked>Opção do FGTS</td>
                    <td><input type="checkbox" name="mostraContaFGTS"     id="mostraContaFGTS"     checked>Conta  do FGTS</td>
                  </tr>                                                  
                  <tr>                                                   
                    <td><input type="checkbox" name="mostraProgressao" id="mostraProgressao" checked>Data Anterior</td>
                    <td><input type="checkbox" name="mostraTelefone"   id="mostraTelefone"   checked>Telefone</td>
                  </tr>                                                  
                  <tr>
                    <td><input type="checkbox" name="mostraCelular"   id="mostraTelefone"   checked>Celular</td>                                                   
                    <td><input type="checkbox" name="mostraEmail" id="mostraEmail" checked>Email<td>
                  </tr>
               </table>
             </td>
             <td valign="top">
               <table>
                 <tr>
                   <td align="center" colspan="2" class="table_header"><b>Documentos</b></td>
                 </tr>
                 <tr>                                       
                   <td><input type="checkbox" name="mostraTituloEleitoral" id="mostraTituloEleitoral" checked>Título/Zona/Seção</td>
                   <td><input type="checkbox" name="mostraCNH" id="mostraCNH" checked>Habilitação</td>
                 </tr>
                 <tr>                                                                                                     
                   <td><input type="checkbox" name="mostraDataProgressao" id="mostraDataProgressao" checked>Data Anterior</td>
                   <td><input type="checkbox" name="mostraDataBaseTrienio" id="mostraDataBaseTrienio" checked>Data Triênio</td>
                 </tr>                                                  
                 <tr>                                                   
                   <td><input type="checkbox" name="mostraCTPS" id="mostaCTPS" checked>CTPS</td>
                   <td><input type="checkbox" name="mostraPis" id="mostraPis" checked>PIS/PASEP</td>
                 </tr>
                 <tr>                                                                                                     
                   <td><input type="checkbox" name="mostraReservista" id="mostraReservista" checked>Reservista/Categoria</td>
                   <td><input type="checkbox" name="mostraCNPJCPF"    id="mostraCNPJCPF"   checked>CNPJ/CPF</td>
                 </tr>
                 <tr>                                                                                                     
                   <td><input type="checkbox" name=mostraRG id="mostraRG" checked>Identidade</td>
                 </tr>                                                  
               </table>
             </td>
             <td valign="top">
               <table>
                 <tr><td><input type="checkbox" name="mostraRescisao" id="mostraRescisao" checked><b>Mostrar informação da Rescisão</b></td></tr>
                 <tr><td><input type="checkbox" name="mostraLocaisTrabalho" id="mostraLocaisTrabalho" checked><b>Mostrar informação de Locais de trabalho</b></td></tr>
                 <tr><td><input type="checkbox" name="mostraFerias" id="mostraFerias" checked><b>Mostrar informação de Férias</b></td></tr>
                 <tr><td><input type="checkbox" name="mostraDependentes" id="mostraDependentes" checked><b>Mostrar informação de Dependentes</b></td></tr>
                 <tr><td><input type="checkbox" name="mostraObservacoes" id="mostraObservacoes" checked><b>Mostrar Observações</b></td></tr>
               </table>
             </td>
           </tr>
         </table>
       </fieldset> 
     </td>
   </tr>
 </table>
 <input name="emite" id="emite" type="button" value="Imprimir" onclick="Imprime();" > 
</div> 
</form>   
</body>
</html>

<script>
function Imprime() {

   var sCampos = '';
   
   aInputs = document.getElementsByTagName("INPUT");
   for (iInd = 0; iInd < aInputs.length; iInd++) {
	   
	  var Item = aInputs[iInd];
	  if (Item.type == "checkbox" && Item.checked) {
		  sCampos += '&'+Item.name+'=true';
	  }
   }
	  	
   var sUrl  = 'pes3_conspessoal_impressao.php';
       sUrl += '?regist='+$F("regist");
       sUrl += '&mes='+$F("mes");
       sUrl += '&ano='+$F("ano");
       sUrl += sCampos;
       
   window.open(sUrl, '', 'location=0');
   
}
</script>
