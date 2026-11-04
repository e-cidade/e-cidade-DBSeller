<?php
/**
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2009  DBseller Servicos de Informatica
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
require_once(modification("classes/db_fis_lancamento_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($HTTP_POST_VARS);

$cldblanc = new cl_fis_lancamento;
$cldblanc->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("nl01_codlanc");
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
        <style type="text/css">
        fieldset {border-radius:7px;padding:20px;}
        </style>
        <br />
        <br />
        <form name="form1" method="post" action="" class="container">
            <fieldset>
                <legend>Notificação de Lançamento</legend>
                <table>
                    <tr>
                        <td nowrap title="<?=@$nl01_codlanc?>"><?php db_ancora('Código: ',"js_codlanc(true);",1);?></td>
                        <td>
                            <?php
                                db_input('nl01_codlanc',6,$Inl01_codlanc,true,'text',1," onchange='js_codlanc(false);'");
                                db_input('nl01_nome',35,$Inl01_nome,true,'text',3,'');
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?=@$y100_sequencial?>"><?php db_ancora('Processo fiscal: ',"js_codprocfiscal(true);",1);?></td>
                        <td>
                            <?php
                                db_input('y100_sequencial',6,$Inl01_codlanc,true,'text',1," onblur='js_codprocfiscal(false);'");
                                db_input('z01_nome',35,$Inl01_nome,true,'text',3,'');
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input name="consultar" type="button" value="Processar" onclick="js_enviaDados();" />
        </form>
    </div>
<?php
db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));
?>
</body>
</html>
<script type="text/javascript">

function js_enviaDados(){
    if(document.form1.nl01_codlanc.value == "" && document.form1.y100_sequencial.value == ""){
        alert("Preencha um dos campos do formulário!");
        if (document.form1.nl01_codlanc.value == '') {
            document.form1.nl01_codlanc.focus();
        }
        if (document.form1.y100_sequencial.value == "") {
            document.form1.y100_sequencial.focus();    
        }
    }else{
	if(document.form1.nl01_codlanc.value != "") {
	        jan = window.open('fis2_fis_notlanc002.php?codlanc='+document.form1.nl01_codlanc.value+'&codprocfiscal='+document.form1.y100_sequencial.value,'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
		 jan.moveTo(0,0);	
	         imprimelevantamento();
	}else{
	   alert("Favor Preencher o campo Código");
	}	
    }
}

function js_mostracodlanc1(chave1,chave2){
    js_limpa_campos()
    document.form1.nl01_codlanc.value = chave1;
    document.form1.nl01_nome.value    = chave2;
    db_iframe_lanc.hide();
}

function js_mostracodlanc(chave,erro){
    document.form1.nl01_nome.value = chave;
    if(erro==true){
        document.form1.nl01_codlanc.focus();
        document.form1.nl01_codlanc.value = '';
    }
}

function js_codlanc(mostra){
    if(mostra==true){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_lanc','func_fis_lancamento_alteracao.php?funcao_js=parent.js_mostracodlanc1|dl_Notificacao_Lancamento|z01_nome','Pesquisa',true);
    }else{
        nl01_codlanc = document.form1.nl01_codlanc.value;
        if(nl01_codlanc!=""){
            js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_lanc','func_fis_lancamento_alteracao.php?pesquisa_chave='+nl01_codlanc+'&funcao_js=parent.js_mostracodlanc','Pesquisa',false);
        }else{
            document.form1.nl01_nome.value='';
        }
    }
}

function js_codprocfiscal(mostra){
    if (mostra == true) {
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_lanc', 'func_fis_lancamento_alteracao.php?chave_procfiscal=&funcao_js=parent.js_mostracodprocfiscal1|dl_Notificacao_Lancamento|dl_Desc_Lancamento|dl_Processo_Fiscal|dl_Desc_Proc_Fiscal','Pesquisa',true)
    } else {
        y100_sequencial = document.form1.y100_sequencial.value;
        if (y100_sequencial != '') {
           js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_lanc','func_fis_lancamento_alteracao.php?chave_procfiscal='+y100_sequencial+'&funcao_js=parent.js_mostracodprocfiscal1|dl_Notificacao_Lancamento|dl_Desc_Lancamento|dl_Processo_Fiscal|dl_Desc_Proc_Fiscal','Pesquisa',true);
        }
    }
}

function js_mostracodprocfiscal1(chave1,chave2,chave3,chave4){
    js_limpa_campos()
    document.form1.nl01_codlanc.value = chave1;
    document.form1.nl01_nome.value    = chave2;
    document.form1.y100_sequencial.value = chave3;
    document.form1.z01_nome.value     = chave4;
    db_iframe_lanc.hide();
}

function js_mostracodprocfiscal(chave,erro){
    document.form1.z01_nome.value = chave;
    if(erro==true){
        document.form1.y100_sequencial.focus();
        document.form1.y100_sequencial.value = '';
    }
}

function js_limpa_campos(){
    document.form1.nl01_codlanc.value    = "";
    document.form1.nl01_nome.value       = "";
    document.form1.y100_sequencial.value = "";
    document.form1.z01_nome.value        = "";
}

</script>  

<script language="JavaScript" type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript">

function imprimelevantamento(){      

  var codigo = $('#nl01_codlanc').val();
 
  $.ajax({
    type     : 'post',
    url      : 'codLevantamentoNotificacao.php',
    data     : { codigo : codigo },
    dataType : 'json',
    success  : function(d){
    if (d.erro == false){
        imprimeLevantamento = confirm("Deseja imprimir o levantamento?");

        if (imprimeLevantamento){
		   for ( var i = 0; i < d.codigoLevantamento.length; i++ ) {
	           	janela = window.open('fis2_fis_levantamento002.php?codlev='+d.codigoLevantamento[i],'','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
		        janela.moveTo(0,0);
		   }
	    }
      }
    }
  });

}
 </script>
