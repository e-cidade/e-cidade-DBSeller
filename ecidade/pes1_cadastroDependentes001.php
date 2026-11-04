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

require_once modification('libs/db_stdlib.php');
require_once modification('libs/db_conecta.php');
require_once modification('libs/db_sessoes.php');
require_once modification('libs/db_usuariosonline.php');
require_once modification('libs/db_app.utils.php');
require_once modification('libs/db_utils.php');
require_once modification('dbforms/db_funcoes.php');

$oGet = db_utils::postMemory($_GET);
$oPost = db_utils::postMemory($_POST);

$oRotulos = new rotulocampo;
$oRotulos->label('rh01_regist');
$oRotulos->label('z01_nome');
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="iso-8859-1">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>DBSeller Informática Ltda - Página Inicial</title>
    <?php
    db_app::load("estilos.css");
    db_app::load("scripts.js");
    db_app::load("strings.js");
    db_app::load("prototype.js");
    db_app::load("windowAux.widget.js");
    db_app::load("messageboard.widget.js");
    db_app::load("datagrid.widget.js");
    ?>
    <style>
        #table-matricula {
            margin: auto;
            padding-top: 2.5%
        }

        #table-matricula td {
            padding-top: 2.5%
        }
    </style>
</head>
<body>
<table id="table-matricula">
    <tbody>
    <tr>
        <td title="<?= @$Trh01_regist ?>">
            <?php
            db_ancora($Lrh01_regist, "js_pesquisaMatricula(true);", 1);
            ?>
        </td>
        <td>
            <?php
            db_input('rh01_regist', 6, $Irh01_regist, true, 'text', 1, "onchange='js_pesquisaMatricula(false);'");
            db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3, '');
            ?>
        </td>
    </tr>
    <tr>
        <td colspan="2" class="text-center">
            <input type="submit" value="Pesquisar" name="pesquisar" onclick="js_processaConsulta();">
        </td>
    </tr>
    </tbody>
</table>
<?php
db_menu();
?>
<script>
    const inputMatricula = document.getElementById('rh01_regist');
    const inputNome = document.getElementById('z01_nome');

    /**
     * Escopo geral do script
     */
    var me = this;

    /**
     * Mostra tela de manutenção de documentos
     */
    function js_abreJanelaManutencao() {
        me.windowDocumentos = new windowAux(
            'windowDocumentos',
            'Manutencão de Dependentes',
            screen.availWidth * 0.75,
            screen.availHeight * 0.75
        );
        me.windowDocumentos.setContent('<div id=\'messageDocumentos\'></div><div id=\'conteudoDocumentos\'></div>');
        me.windowDocumentos.setShutDownFunction(function() {
            if ($('windowDocumentos')) {
                js_fechaJanelaManutencao();
            }
        });

        me.windowDocumentos.show(0, 0, false, 0, 0);

        var sMessage = '<B>Matrícula:</B> ' + inputMatricula.value + '<br>';
        sMessage += '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<B>Servidor:</B>  ' + inputNome.value;

        me.oMessageBoard = new messageBoard(
            'msgboard1',
            'Manutenção de Dependentes',
            sMessage,
            $('messageDocumentos')
        );
        me.oMessageBoard.show();
        $('msgboard1').style.width = '';

        const urlSearchParams = new URLSearchParams;
        urlSearchParams.set('rh31_regist', inputMatricula.value);
        urlSearchParams.set('z01_nome', inputNome.value);
        urlSearchParams.set('vmenu', 'true');

        const oIframeConteudo = document.createElement('iframe');
        oIframeConteudo.src = `pes1_rhdepend001.php?${urlSearchParams.toString()}`;
        oIframeConteudo.id = 'db_iframe_manutencaoDocumentos';
        oIframeConteudo.name = 'db_iframe_manutencaoDocumentos';
        oIframeConteudo.width = `100%`;
        oIframeConteudo.height = `${me.windowDocumentos.getHeight() - $('msgboard1').clientHeight - 35}px`;
        oIframeConteudo.style.border = '0';
        $('conteudoDocumentos').appendChild(oIframeConteudo);

        return false;
    }

    /**
     * Processa formulário com os dados digitados
     */
    function js_processaConsulta() {
        if (inputMatricula.value === '') {
            alert('Informe a matrícula do funcionário.');
            return false;
        }
        else {
            if ($('windowDocumentos')) {
                js_fechaJanelaManutencao();
            }

            return js_abreJanelaManutencao();
        }
    }

    /**
     * Pesquisa dados da matricula conforme variável de visualização
     */
    function js_pesquisaMatricula(lShowWindow) {
        if ($('windowDocumentos')) {
            js_fechaJanelaManutencao();
        }

        if (lShowWindow) {
            js_OpenJanelaIframe(
                '',
                'db_iframe_rhpessoal',
                'func_rhpessoal.php?funcao_js=parent.js_retornaDadosAncora|rh01_regist|z01_nome&instit=<?=(db_getsession("DB_instit"))?>',
                'Pesquisa',
                true
            );
        }
        else {
            if (inputMatricula.value === '') {
                inputNome.value = '';
            }
            else {
                js_OpenJanelaIframe(
                    '',
                    'db_iframe_rhpessoal',
                    'func_rhpessoal.php?pesquisa_chave=' + inputMatricula.value +
                    '&funcao_js=parent.js_retornaDadosDigitacao&instit=<?=(db_getsession("DB_instit"))?>',
                    'Pesquisa',
                    false
                );
            }
        }
    }

    /**
     * Retorna os dados buscados apartir do evento change do campo matricula
     */
    function js_retornaDadosDigitacao(sChave, lErro) {
        if ($('windowDocumentos')) {
            js_fechaJanelaManutencao();
        }

        inputNome.value = sChave;

        if (lErro == true) {
            $('rh01_regist').focus();
            inputMatricula.value = '';
        }
    }

    /**
     * Retorna os dados buscados da OpenJanelaIframe
     */
    function js_retornaDadosAncora(sBusca1, sBusca2) {
        if ($('windowDocumentos')) {
            js_fechaJanelaManutencao();
        }

        inputMatricula.value = sBusca1;
        inputNome.value = sBusca2;

        db_iframe_rhpessoal.hide();
    }

    function js_fechaJanelaManutencao() {
        me.windowDocumentos.destroy();
    }
</script>
</body>
</html>
