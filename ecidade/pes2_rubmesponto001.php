<?php
/*
 * E-cidade Software Publico para Gestao Municipal
 * Copyright (C) 2009 DBSeller Servicos de Informatica
 * www.dbseller.com.br
 * e-cidade@dbseller.com.br
 *
 * Este programa e software livre; voce pode redistribui-lo e/ou
 * modifica-lo sob os termos da Licenca Publica Geral GNU, conforme
 * publicada pela Free Software Foundation; tanto a versao 2 da
 * Licenca como (a seu criterio) qualquer versao mais nova.
 *
 * Este programa e distribuido na expectativa de ser util, mas SEM
 * QUALQUER GARANTIA; sem mesmo a garantia implicita de
 * COMERCIALIZACAO ou de ADEQUACAO A QUALQUER PROPOSITO EM
 * PARTICULAR. Consulte a Licenca Publica Geral GNU para obter mais
 * detalhes.
 *
 * Voce deve ter recebido uma copia da Licenca Publica Geral GNU
 * junto com este programa; se nao, escreva para a Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA
 * 02111-1307, USA.
 *
 * Copia da licenca no diretorio licenca/licenca_en.txt
 * licenca/licenca_pt.txt
 */
require_once(modification("libs/db_stdlib.php"));
require_once(modification("libs/db_conecta.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));

$clrotulo = new rotulocampo();
$clrotulo->label('DBtxt23');
$clrotulo->label('DBtxt25');
$clrotulo->label('DBtxt27');
$clrotulo->label('DBtxt28');
$clrotulo->label('rh27_rubric');
$clrotulo->label('rh27_descr');
$clrotulo->label('r44_des');

db_postmemory($_POST);

?>

<html>
<head>
<title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="Expires" CONTENT="0">
<link href="estilos.css" rel="stylesheet" type="text/css">
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
</head>
<body>
<div class="container">
<form name="form1" method="post">
<fieldset>
 <legend>Relatório por código - pelo Ponto</legend>
 <table class="form-container">
   <tr>
     <td title="Digite o Ano / Mes de competência">Ano / Mês :</td>
     <td>
        <?php
          $DBtxt23 = DBPessoal::getAnoFolha();
          db_input('DBtxt23', 4, $IDBtxt23, true, 'text', 2, '')
        ?>
        &nbsp;/&nbsp;
        <?php
          $DBtxt25 = DBPessoal::getMesFolha();
          db_input('DBtxt25', 2, $IDBtxt25, true, 'text', 2, '')?>
     </td>
   </tr>
   <tr> 
     <td title="<?=$Trh27_rubric?>"> 
       <?php
        db_ancora(@$Lrh27_rubric, "js_pesquisarrubric(true);", 1);
        ?>
     </td>
     <td> 
       <?php
        db_input('rh27_rubric', 8, $Irh27_rubric, true, 'text', 1, " onchange='js_pesquisarrubric(false);'");
        db_input('rh27_descr', 30, $Irh27_descr, true, 'text', 3, '');
        ?>
     </td>
   </tr>
   <tr>
     <td>Ponto</td>
     <td>
        <?php
          $x = [
              "f" => "Fixo",
              "s" => "Salário",
              "c" => "Complementar",
              "a" => "Adiantamento",
              "d" => "13o. Salário",
              "r" => "Rescisão"
          ];
          db_select('ponto', $x, true, 1, "onchange='mostraOpcaoRegistros()'");
            ?>
     </td>
   </tr>
   <tr id='tr_apenas_sem_ponto' style="display: none">
     <td>Apenas registros sem ponto:</td>
     <td>
     <?php
          $x = [
              "f" => "NÃO",
              "t" => "SIM"
          ];
          db_select('apenas_sem_ponto', $x, true, 1);
            ?>
     </td>
   </tr>
   <tr>
     <td>Ordem</td>
     <td> 
        <?php
            $x = [
                "a" => "Alfabética",
                "n" => "Numérica",
                "l" => "Lotação",
                "v" => "Valor",
                "q" => "Quantidade",
                "d" => "Digitação"
            ];
            db_select('ordem', $x, true, 4, "");
            ?>
     </td>
   </tr>
   <tr>
     <td>Tipo de Ordem</td>
     <td>
       <?php
         $x = [
             "asc" => "Ascendente",
             "desc" => "Descendente"
         ];
         db_select('tipoordem', $x, true, 4, "");
            ?>
     </td>
   </tr>
   <tr>
     <td>Totalização</td>
     <td>
       <?php
         $x = [
             "a" => "Analítico",
             "s" => "Sintético"
         ];
         db_select('total', $x, true, 4, "");
            ?>
     </td>
   </tr>
   <tr>
     <td>Quebrar por Recurso</td>
     <td>
       <?php
         $xy = [
             "n" => "Não",
             "s" => "Sim"
         ];
         db_select('recurso', $xy, true, 4, "");
            ?>
     </td>
   </tr>
   <tr title="Mostra Local de Trabalho - Este filtro somente é aplicado quando Tipo de Relatório for 'Relatório'">
     <td>Local de Trabalho:</td>
     <td >
      <?php
        $opcoes = ["N"=>"Não","S"=>"Sim"];
        db_select('localtrab', $opcoes, true, 4, "");
        ?>
     </td>
   </tr>
   <tr>
     <td>Formato</td>
     <td>
       <?php
         $opcoes = [
             "pdf" => "PDF",
             "csv" => "CSV"
         ];
         db_select('formato', $opcoes, true, 4, "");
            ?>
     </td>
   </tr>      
  </table>
</fieldset>
<input name="emite2" id="emite2" type="button" value="Processar" onclick="js_emite();">
</form>
</div>
<?php
db_menu();
?>
</body>
</html>
<script>
function mostraOpcaoRegistros() 
{
    $('tr_apenas_sem_ponto').style.display = 'none'; 
    if ($F('ponto') == 'f' || $F('ponto') == 's') {
        $('tr_apenas_sem_ponto').style.display = '';
    } 
}   
mostraOpcaoRegistros();

function js_pesquisatabdesc(mostra)
{
     if(mostra==true){
       db_iframe.jan.location.href = 'func_tabdesc.php?funcao_js=parent.js_mostratabdesc1|0|2';
       db_iframe.mostraMsg();
       db_iframe.show();
       db_iframe.focus();
     }else{
       db_iframe.jan.location.href = 'func_tabdesc.php?pesquisa_chave='+document.form1.codsubrec.value+'&funcao_js=parent.js_mostratabdesc';
     }
}
function js_mostratabdesc(chave,erro)
{
  document.form1.k07_descr.value = chave;
  if(erro==true){
     document.form1.codsubrec.focus();
     document.form1.codsubrec.value = '';
  }
}

function js_mostratabdesc1(chave1,chave2)
{
     document.form1.codsubrec.value = chave1;
     document.form1.k07_descr.value = chave2;
     db_iframe.hide();
}

/**
 * Realiza a busca de rubricas, retornando o código e descrição da rubrica escolhida
 */
 function js_pesquisarrubric(lMostra) 
 {
   if ( lMostra) {
    js_OpenJanelaIframe(
        'CurrentWindow.corpo',
        'db_iframe_rhrubricas',
        'func_rhrubricas.php?funcao_js=parent.js_mostrarubricas1|rh27_rubric|rh27_descr',
        'Pesquisa',
        true
    );
   } else {
       
      if ( $F(rh27_rubric) != '' ) {
          
        quantcaracteres = $F(rh27_rubric).length;
        
        for ( i=quantcaracteres;i<4;i++ ) {
          $(rh27_rubric).setValue("0"+$F(rh27_rubric));
        }
        
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_rhrubricas',
            'func_rhrubricas.php?pesquisa_chave='+$F(rh27_rubric)+'&funcao_js=parent.js_mostrarubricas',
            'Pesquisa',
            false
        );
      } else { 
        $(rh27_descr).setValue(''); 
      }
   }
 }

 /**
 * Trata o retorno da função js_pesquisarrubric()
 */
 function js_mostrarubricas(sChave, lErro) 
 {
   $(rh27_descr).setValue(sChave);
   if ( lErro ) {
       
     $(rh27_rubric).setValue('');
     $(rh27_rubric).focus();
   }
 }

 /**
 * Trata o retorno da função js_pesquisarrubric()
 */
 function js_mostrarubricas1(sChave1,sChave2)
 {
   $(rh27_rubric).setValue(sChave1);
   $(rh27_descr).setValue(sChave2);
   $(db_iframe_rhrubricas).hide();
 }

 /**
  * Realiza a busca de seleções retornando o código e descrição da rubrica escolhida;
  */
  function js_pesquisaSelecao(lMostra) 
  {
    if ( lMostra ) {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_selecao',
            'func_selecao.php?funcao_js=parent.js_geraform_mostraselecao1|r44_selec|r44_descr&instit=<?=db_getsession("DB_instit")?>',
            'Pesquisa',
            true
        );
    } else {
      if ( $F(r44_selec) != "" ) {
        js_OpenJanelaIframe(
            'CurrentWindow.corpo',
            'db_iframe_selecao',
            'func_selecao.php?pesquisa_chave=' + $F(r44_selec) + '&funcao_js=parent.js_geraform_mostraselecao&instit=<?=db_getsession("DB_instit")?>',
            'Pesquisa',
            false
        );
      } else {
        $(r44_des).setValue(""); 
      }
    }
  }

  /**
  * Trata o retorno da função js_pesquisaSelecao().
  */
  function js_geraform_mostraselecao(sDescricao, lErro) 
  {
    if ( lErro ) { 

      $(r44_selec).setValue('');
      $(r44_selec).focus(); 
    }
    
    $(r44_des).setValue(sDescricao); 
  }

  /**
  * Trata o retorno da função js_pesquisaSelecao();
  */
  function js_geraform_mostraselecao1(sChave1, sChave2) 
  {
    $(r44_selec).setValue(sChave1);
    
    if( $(r44_des) ) {
      $(r44_des).setValue(sChave2);
    }
    
    db_iframe_selecao.hide();
  } 

  function js_emite() 
  {
    if ($F('rh27_rubric') == "" && $F("apenas_sem_ponto") == 'f') {
        alert("Informe uma rubrica");
        return false;
    }
      
    jan = window.open('pes2_rubmesponto002.php?xtotal='+$F('total')+
                      '&tipoordem='+$F('tipoordem')+
                      '&ordem='+$F('ordem')+
                      '&ponto='+$F('ponto')+
                      '&rubrica='+$F('rh27_rubric')+
                      '&recurso='+$F('recurso')+
                      '&localtrab='+$F('localtrab')+
                      '&ano='+$F('DBtxt23')+
                      '&mes='+$F('DBtxt25')+
                      '&formato='+$F('formato')+
                      '&apenas_sem_ponto='+$F('apenas_sem_ponto'),
                      '',
                      'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
    jan.moveTo(0,0);
  }

  function abrirDownload(sArquivo) 
  {
    var oDownload = new DBDownload();
    oDownload.addFile(sArquivo, 'Download CSV.');
    oDownload.show();
  } 
</script>

<?php
if (isset($ordem)) {
    echo "<script> js_emite(); </script>";
}

$func_iframe = new janela('db_iframe', '');
$func_iframe->posX = 1;
$func_iframe->posY = 20;
$func_iframe->largura = 780;
$func_iframe->altura = 430;
$func_iframe->titulo = 'Pesquisa';
$func_iframe->iniciarVisivel = false;
$func_iframe->mostrar();
?>