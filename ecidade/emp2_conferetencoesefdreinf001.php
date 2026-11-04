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
require_once(modification("libs/db_utils.php"));
require_once(modification("libs/db_sessoes.php"));
require_once(modification("libs/db_usuariosonline.php"));
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("dbforms/db_funcoes.php"));

$db_opcao = 1;
?>
<html>
<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script type="text/javascript" src="scripts/strings.js"></script>
    <script type="text/javascript" src="scripts/prototype.js"></script>
    <script type="text/javascript" src="scripts/classes/http/http.js"></script>
    <script type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/widgets/DBDownload.widget.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>
<body bgcolor=#CCCCCC>
<div class="container" style="width: 640px;">
    <form name='form1' id="form1">
        <fieldset>
            <legend>Relatório de Retenções da EFD-Reinf</legend>
            <table class="form-container">
                <tr>
                    <td>
                        <label for="evento">Evento: </label>
                    </td>
                    <td>
                        <select name="evento" id="evento" style = "width:120px">
                            <option value="r2010">R-2010</option>
                            <option value="r2055">R-2055</option>
                            <option value="r4010">R-4010</option>
                            <option value="r4020">R-4020</option>
                            <option value="todos1">TODOS (R-2010 e R-2055)</option>
                            <option value="todos2">TODOS (R-4010 e R-4020)</option>
                        </select>
                    </td>
               </tr>
               <tr>
                    <td>
                        <b>Data Inicial:</b>
                    </td>
                    <td>
                        <?php
                          db_inputdata("datainicial", null, null, null, true, "text", 1);
                        ?>
                        <b>Data Final:</b>
                        <?php
                          db_inputdata("datafinal", null, null, null, true, "text", 1);
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>Agrupa por:</td>
                    <td>
                        <?php
                          $aQuebras = [
                            1 => "Nenhuma",
                            3 => "Credor",
                            5 => "Unidade Orçamentária",
                            6 => "Unidade Orçamentária e Credor"
                          ];
                          db_select("group", $aQuebras, true, 1, "style='width:20em'");
                        ?>
                    </td>
                </tr>
            </table>
            <br>
            <fieldset>
                <legend>
                    <strong>Filtrar Credores</strong>
                </legend>
                <table class="form-container">
                    <tr>
                        <td>
                            <?php
                              db_ancora('Credor:',"js_pesquisaCredor(true)",1);
                            ?>
                        </td>
                        <td>
                            <?php
                              db_input ( "cgm_credor", 10, "", true, "text", 1, " onchange='js_pesquisaCredor(false);' " );
                              db_input ( "nome_credor", 40, "", true, "text", 3 );
                            ?>

                       </td>
                    </tr>
                    <tr>
                      <td class="bold">
                        <label for="orgao_numero"><?php db_ancora('Órgão:', 'buscarOrgao(true)', $db_opcao, null, 'orgao_numero_ancora'); ?></label>
                      </td>
                      <td>
                        <?php
                        $Sorgao_numero = 'Órgão';
                        db_input('orgao_numero', 14, 1, true, 'text', $db_opcao, 'onChange="buscarOrgao(false)"');
                        db_input('orgao_descricao', 44, 0, true, 'text', 3);
                        ?>
                      </td>
                    </tr>
                    <tr>
                      <td class="bold">
                        <label for="unidade_numero>">
                          <?php db_ancora('Unidade:', 'buscarUnidade(true)', $db_opcao, null, 'unidade_numero_ancora'); ?>
                        </label>
                      </td>
                      <td>
                        <?php
                        $Sunidade_numero = 'Unidade';
                        db_input('unidade_numero', 14, 1, true, 'text', $db_opcao, 'onChange="buscarUnidade(false)"');
                        db_input('unidade_descricao', 44, 0, true, 'text', 3);
                        db_input('instituicao_unidade', 10, 0, true, 'hidden');
                        ?>
                      </td>
                    </tr>
                </table>
            </fieldset>
            <table>
                <tr>

                  <td>
                    <label><b>Formato:</b></label>
                    <?php
                      $formatos = ["p" => "PDF", "c" => "CSV"];
                      db_select("formato", $formatos, true, 1);
                    ?>
                  </td>
                </tr>
            </table>
        </fieldset>
        <input type='button' value='Emitir' onclick='js_emitir()'>
    </form>
</div>
</body>
</html>
<?php
db_menu();
?>
<script type="text/javascript" src="scripts/session.js"></script>
<script>

    function js_pesquisaCredor(lMostra){

        if (lMostra) {
           js_OpenJanelaIframe('',
                'db_iframe_credor',
                'func_nome.php?funcao_js=parent.js_mostraCredor1|z01_nome|z01_numcgm',
                'Pesquisar Credor',
                true
            );
        } else {
          if($('cgm_credor').value != ''){
             js_OpenJanelaIframe('',
                'db_iframe_credor',
                'func_nome.php?pesquisa_chave='+$F('cgm_credor')+
                '&funcao_js=parent.js_mostraCredor',
                'Pesquisa',
                false
            );
          } else {
            $("nome_credor").value = '';
          }
        }
    }

    function js_mostraCredor(erro, chave){

        if(erro == true) {

            $('cgm_credor').focus();
            $("cgm_credor").value = '';
            $('nome_credor').value = chave;
        } else {
            $('nome_credor').value = chave;
            me.sChavePesquisa = $("cgm_credor").value;
        }
    }

    function js_mostraCredor1(chave1, chave2) {

        $('nome_credor').value   = chave1;
        $('cgm_credor').value = chave2;
        db_iframe_credor.hide();
        me.sChavePesquisa = chave2;
    }

    function buscarOrgao(lMostrar) {

        var sQuerySring = 'funcao_js=parent.retornoOrgao|0|2';
        var sArquivo    = 'func_orcorgao.php';
        var sTituloTela = 'Pesquisar Órgão';

        if (!lMostrar) {
            sQuerySring = 'pesquisa_chave=' + $F('orgao_numero') + '&funcao_js=parent.retornoOrgaoChave';
        }

        js_OpenJanelaIframe('', 'db_iframe_orcorgao', sArquivo +'?' +sQuerySring, sTituloTela, lMostrar);

    }

    function retornoOrgaoChave(sDescricao, lErro) {

        $('unidade_numero').value    = '';
        $('unidade_descricao').value = '';

        retornoOrgao($F('orgao_numero'), sDescricao, lErro);
    }

    function retornoOrgao(iCodigo, sDescricao, lErro) {

        //Se o valor selecionado for diferente do atual, limpa a unidade.
        if ($('orgao_numero').value != iCodigo) {

            $('unidade_numero').value    = '';
            $('unidade_descricao').value = '';

        }
        db_iframe_orcorgao.hide();
        retorno('orgao', iCodigo, sDescricao, lErro);
    }


    function buscarUnidade(lMostrar) {

        var iOrgao = $F('orgao_numero');

        if (iOrgao == '') {

        alert("Para selecionar uma unidade, você deve primeiro informar o órgão.");
        return false;
        }

        var sQuerySring = 'orgao=' + iOrgao + '&funcao_js=parent.retornoUnidade|2|4|0|o41_instit';
        var sArquivo    = 'func_orcunidade.php';
        var sTituloTela = 'Pesquisar Unidade';

        if (!lMostrar) {
            sQuerySring = 'pesquisa_chave=' + $F('unidade_numero') + '&orgao=' + iOrgao + '&funcao_js=parent.retornoUnidadeChave';
        }

        js_OpenJanelaIframe('', 'db_iframe_orcunidade', sArquivo +'?' +sQuerySring, sTituloTela, lMostrar);
    }


    function retornoUnidadeChave(sDescricao, lErro, sNomeInstituicao, iInstituicao, iCodigoOrgao, iExercicio) {

        if (lErro) {
            iExercicio   = '';
        }
        retornoUnidade($F('unidade_numero'), sDescricao, iExercicio, iInstituicao, lErro);
    }


    function retornoUnidade(iCodigo, sDescricao, iExercicio, iInstituicaoUnidade, lErro) {


        if (lErro) {
            iExercicio = '';
            iInstituicaoUnidade = '';
        }

        db_iframe_orcunidade.hide();
        retorno('unidade', iCodigo, sDescricao, lErro);

    }

    function retorno(sCampo, iCodigo, sDescricao, lErro) {

        $(sCampo+'_numero').value = iCodigo;
        if (lErro) {
        $(sCampo+'_numero').value = '';
        }
        $(sCampo+'_descricao').value = sDescricao;
    }



    function js_emitir() {

        let rota = 'financeiro/empenho/relatorio/retencoesEfdReinf';

        if ($F('datainicial') == "" || $F('datafinal') == "") {
            alert('A data inicial e a final do pagamento devem ser informadas!');
            return false;
        } else if(js_comparadata($F('datainicial'), $F('datafinal'), '>')){
            alert ("A data final deve ser maior que a data inicial.");
            return false;
        }

        const formData = new FormData();

        formData.append('dataInicial', $F('datainicial'));
        formData.append('dataFinal',$F('datafinal'));
        formData.append('agrupa_por', $F('group'));
        formData.append('orgao', $F("orgao_numero"));
        formData.append('unidade',$F("unidade_numero") );
        formData.append('credor',$F("cgm_credor") );
        formData.append('evento',$F("evento") );
        formData.append('formato_relatorio',$F("formato") );

        PHPSession.appendFormData(formData);
        HttpClient.post(`${PHPSession.requestApi}/${rota}`, {body: formData}).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }
            const download = new DBDownload();
            download.addFile(response.data.path, response.data.name);
            download.show();
        })
    }

</script>
