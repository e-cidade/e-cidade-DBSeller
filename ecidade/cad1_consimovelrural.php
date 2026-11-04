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
include(modification("dbforms/db_funcoes.php"));
include(modification("classes/db_iptubase_classe.php"));
db_postmemory($HTTP_SERVER_VARS);
db_postmemory($HTTP_POST_VARS);
$cliptubase = new cl_iptubase;
$cliptubase->rotulo->label();
$cliptubase->rotulo->tlabel();
?>
<html>
<head>
  <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
  <meta http-equiv="Expires" CONTENT="0">
  <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
  <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body class="body-default">
<div class="container">
  <form name="form1" method="post">
    <fieldset>
      <legend>Matricula do imóvel</legend>
      <table class="form-container">
        <tr>
          <td>
              <?php
              db_ancora($Lj01_matric, ' js_matri(true); ', 1);
              ?>
          </td>

          <td>
              <?php
              db_input('j01_matric', 10, 0, true, 'text', 1, "onchange='js_matri(false)'");
              db_input('z01_nome', 50, 0, true, 'text', 3, "");
              ?>
          </td>
        </tr>
      </table>
    </fieldset>
    <input name="entrar" type="button" id="entrar" value="Entrar">
  </form>
</div>

<?php
db_menu();
?>
</body>
</html>
<script>
  function js_matri(mostra) {
    const matri = document.form1.j01_matric.value;
    const url = 'func_iptubase.php?tipoImovel=2';
    let params = '&funcao_js=parent.js_mostra|0|2';

    if (!mostra) {
      params = `&pesquisa_chave=${matri}&funcao_js=parent.js_mostra1`;
    }

    js_OpenJanelaIframe('', 'db_iframe', url + params, 'Pesquisa', mostra);
  }

  function js_mostra(chave1, chave2) {
    document.form1.j01_matric.value = chave1;
    document.form1.z01_nome.value = chave2;
    db_iframe.hide();
  }

  function js_mostra1(chave, erro) {
    document.form1.z01_nome.value = chave;
    if(erro == true) {
      document.form1.j01_matric.focus();
      document.form1.j01_matric.value = '';
    }
  }

  $('entrar').addEventListener('click', () => {

    const matricula = $('j01_matric');

    if (empty(matricula.value)) {

      alert('Matrícula não informada.');
      return;
    }

    js_OpenJanelaIframe(
      '',
      'db_iframe_pesquisa',
      `cad1_consimovelrural002.php?matricula=${matricula.value}`,
      'Consulta Imóvel Rural',
      true
    );
  });
</script>
