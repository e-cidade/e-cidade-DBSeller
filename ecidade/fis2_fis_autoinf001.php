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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("classes/db_fis_auto_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

db_postmemory($HTTP_POST_VARS);

$cldbauto = new cl_fis_auto;
$cldbauto->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("y100_sequencial");
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
                <legend>Auto de Infração</legend>
                <table>
                    <tr>
                        <td nowrap title="<?php echo $Ty50_codauto?>">
                            <?php db_ancora('Código: ', "js_codauto(true);", 1);?></td>
                        <td>
                            <?php
                                db_input(
                                    'y50_codauto',
                                    6,
                                    $Iy50_codauto,
                                    true,
                                    'text',
                                    1,
                                    " onchange='js_codauto(false);'"
                                );
                                db_input('y50_nome', 35, $Iy50_nome, true, 'text', 3, '');
                                ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?php echo $Ty100_sequencial?>">
                            <?php db_ancora('Processo fiscal: ', "js_codprocfiscal(true);", 1);?></td>
                        <td>
                            <?php
                                db_input(
                                    'y100_sequencial',
                                    6,
                                    $Iy50_codauto,
                                    true,
                                    'text',
                                    1,
                                    " onblur='js_codprocfiscal(false);'"
                                );
                                db_input('z01_nome', 35, $Iy50_nome, true, 'text', 3, '');
                                ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input name="consultar" type="button" value="Processar" onclick="js_enviaDados();" />
        </form>
    </div>
<?php
db_menu();
?>
</body>
</html>
<script type="text/javascript">

    function js_enviaDados(){
        if(document.form1.y50_codauto.value === "" && document.form1.y100_sequencial.value === ""){
            alert("Preencha um dos campos do formulário!");
            if (document.form1.y50_codauto.value === '') {
                document.form1.y50_codauto.focus();
            }
            if (document.form1.y100_sequencial.value === "") {
                document.form1.y100_sequencial.focus();
            }
        }else{
            if(document.form1.y50_codauto.value !== "") {
                jan = window.open('fis2_fis_autoinf002.php?codauto='+document.form1.y50_codauto.value,
                    '','width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+',scrollbars=1,location=0 ');
                jan.moveTo(0,0);
                imprimelevantamento();
            }else{
                alert("Favor Preencher o campo Código");
            }
        }
    }

    function js_mostracodauto1(chave1,chave2){
        document.form1.y50_codauto.value = chave1;
        document.form1.y50_nome.value    = chave2;
        db_iframe_auto.hide();
    }

    function js_mostracodauto(chave,erro){
        document.form1.y50_nome.value = chave;
        if(erro){
            document.form1.y50_codauto.focus();
            document.form1.y50_codauto.value = '';
        }
    }

    function js_codauto(mostra){
        if(mostra){
            js_OpenJanelaIframe('CurrentWindow.corpo',
                'db_iframe_auto',
                'func_fis_autoalt.php?funcao_js=parent.js_mostracodauto1|dl_auto|z01_nome',
                'Pesquisa',
                true);
        }else{
            y50_codauto = document.form1.y50_codauto.value;
            if(y50_codauto !== ""){
                js_OpenJanelaIframe('CurrentWindow.corpo',
                    'db_iframe_auto',
                    'func_fis_autoalt.php?pesquisa_chave='+y50_codauto+'&funcao_js=parent.js_mostracodauto',
                    'Pesquisa',
                    false);
            }else{
                document.form1.y50_nome.value='';
            }
        }
    }

    function js_codprocfiscal(mostra){
        if (mostra) {
            js_OpenJanelaIframe('CurrentWindow.corpo',
                'db_iframe_auto',
                'func_fis_auto_infracao.php?funcao_js=parent.js_mostracodprocfiscal1|'+
                'dl_Auto|z01_nome|dl_Processo_Fiscal',
                'Pesquisa',
                true)
        } else {
            y100_sequencial = document.form1.y100_sequencial.value;
            if (y100_sequencial !== '') {
                js_OpenJanelaIframe('CurrentWindow.corpo',
                    'db_iframe_auto',
                    'func_fis_auto_infracao_rel.php?procfiscal='+y100_sequencial+
                    '&funcao_js=parent.js_mostracodprocfiscal1|dl_Auto|z01_nome|dl_Processo_Fiscal',
                    'Pesquisa',
                    true);
            }
        }
    }

    function js_mostracodprocfiscal1(chave1,chave2,chave3){
        document.form1.y50_codauto.value = chave1;
        document.form1.z01_nome.value    = chave2;
        document.form1.y100_sequencial.value = chave3;
        db_iframe_auto.hide();
    }

    function js_mostracodprocfiscal(chave,erro){
        document.form1.z01_nome.value = chave;
        if(erro){
            document.form1.y100_sequencial.focus();
            document.form1.y100_sequencial.value = '';
        }
    }

</script>

<script language="JavaScript" type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript">

    function imprimelevantamento(){

        var codigo = $('#y50_codauto').val();

        $.ajax({
            type     : 'post',
            url      : 'codLevantamento.php',
            data     : { codigo : codigo },
            dataType : 'json',
            success  : function(d){
                if (d.erro === true) {
                    return false;
                }

                if (!d.codigoLevantamento.length) {
                    return false;
                }

                imprimeLevantamento = confirm("Deseja imprimir o levantamento?");
                if(imprimeLevantamento){
                    for ( var i = 0; i < d.codigoLevantamento.length; i++ ) {
                        janela = window.open('fis2_fis_levantamento002.php?codlev='+d.codigoLevantamento[i],
                            '',
                            'width='+(screen.availWidth-5)+',height='+(screen.availHeight-40)+
                            ',scrollbars=1,location=0 ');
                        janela.moveTo(0,0);
                    }
                }
            }
        });
    }
</script>
