<?php
/*
 *     E-cidade Software Publico para Gestao Municipal
 *  Copyright (C) 2013  DBselller Servicos de Informatica
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
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("classes/db_bancoagencia_classe.php"));

$clbancoagencia = new cl_bancoagencia;
$clbancoagencia->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("db90_descr");

$matrizLimite = array(
    1 => "Desconsiderar",
    2 => "Considerar"
);
?>
    <html>
    <head>
        <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>

        <?PHP
        $db_opcao = 1;
        db_app::load("scripts.js");
        db_app::load("prototype.js");
        db_app::load("strings.js");
        db_app::load("estilos.css");
        db_app::load("AjaxRequest.js");
        ?>


    </head>

    <body bgcolor="#CCCCCC" style="margin-top: 30px;">

    <center>
        <fieldset style="width: 600px;margin-top: 20px;">
            <legend>
                <strong>Relatório Gerencial Conciliação</strong>
            </legend>

            <table style="width: 600px;">

                <tr>
                    <td nowrap title="<?=@$Tdb89_db_bancos?>">
                        <?php db_ancora(@$Ldb89_db_bancos,"js_pesquisadb89_db_bancos(true);",$db_opcao);?>
                    </td>
                    <td>
                        <?php
                        db_input('db89_db_bancos',10,$Idb89_db_bancos,true,'text',$db_opcao," onchange='js_pesquisadb89_db_bancos(false);'");
                        db_input('db90_descr',40,$Idb90_descr,true,'text',3,'');
                        ?>
                    </td>
                </tr>

                </tr>
                <tr>
                    <td nowrap title="Data Movimento">
                        <strong>Data do Movimento:</strong>
                    </td>
                    <td>
                        <?php
                        db_inputdata('datamov','','','',true,'text',2,"")
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap title='Data Limite'>
                        <strong>Data Limite:</strong>
                    </td>
                    <td>
                        <?php
                          db_select('limite', $matrizLimite ,false, $db_opcao )
                        ?>
                    </td>
                </tr>






            </table>
        </fieldset>
        <input name="lProcessar" id="lProcessar" onclick="js_emiteRelatorio();" type="button" style="margin-top: 10px;" value="Processar" />
    </center>
    </body>
    </html>

    <script type="text/javascript">

        function js_emiteRelatorio() {

            var iBanco = $F("db89_db_bancos");
            var iAno = $F("datamov");
            var sUrl   = "cai2_controleconciliacaobancaria002.php";
            var sQuery = '?iBanco=' + iBanco + '&iData='+iAno;
            if(limite.value == 2){
                sQuery += '&iLimite=sim';
            }
            oJanela = window.open(sUrl + sQuery, '', 'width='+(screen.availWidth-5)+', height='+(screen.availHeight-40)+', scrollbars=1, location=0 ');
            oJanela.moveTo(0,0);
        }


        function js_pesquisadb89_db_bancos(mostra){

            if(mostra == true){
                js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_bancos','func_db_bancos.php?funcao_js=parent.js_mostradb_bancos1|db90_codban|db90_descr','Pesquisa',true);
            }else{
                if( $F("db89_db_bancos") != ''){
                    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_db_bancos','func_db_bancos.php?pesquisa_chave='+$F("db89_db_bancos") + '&funcao_js=parent.js_mostradb_bancos','Pesquisa',false);
                }else{
                    $("db90_descr").value = '';
                }
            }
        }
        function js_mostradb_bancos(chave, erro){

            $("db90_descr").value = chave;
            if(erro == true){

                $("db89_db_bancos").focus();
                $("db89_db_bancos").value = '';
            }
        }
        function js_mostradb_bancos1(chave1,chave2){

            $("db89_db_bancos").value = chave1;
            $("db90_descr").value = chave2;
            db_iframe_db_bancos.hide();
        }
    </script>
<?php db_menu(db_getsession("DB_id_usuario"),db_getsession("DB_modulo"),db_getsession("DB_anousu"),db_getsession("DB_instit"));?>
