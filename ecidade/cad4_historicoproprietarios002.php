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
require_once(modification("classes/db_iptubase_classe.php"));
require_once(modification("classes/db_propri_classe.php"));
require_once(modification("classes/db_historicoproprietarios_classe.php"));
require_once(modification("dbforms/db_funcoes.php"));
require_once(modification("dbforms/db_classesgenericas.php"));

parse_str($HTTP_SERVER_VARS["QUERY_STRING"]);
db_postmemory($HTTP_POST_VARS);

$cliptubase = new cl_iptubase;
$clpropri = new cl_propri;
$clhistoricoproprietarios = new cl_historicoproprietarios();
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clhistoricoproprietarios->rotulo->label();

$clrotulo = new rotulocampo;
$clrotulo->label("j172_percentual");
$clrotulo->label("j163_tipoproprietario");
$clrotulo->label("j164_tipopromitente");

$db_opcao = 22;
$db_botao = false;
$sqlerro  = false;

if (isset($incluir)) {
    
    if (empty($tipoAdquirente)) {
        $erro_msg = "Selecione um tipo de proprietário ou promitente";
        $sqlerro = true;
    }

    if ($_POST['j172_percentual'] > 100) {
        $erro_msg = "Percentual não pode ser maior que 100%";
        $sqlerro = true;
    }

    if ($sqlerro == false) {
        db_inicio_transacao();

        if (empty($j172_tipoproprietario) || $tipoAdquirente == 2) {
            $j172_tipoproprietario = 'null';
        }
        if (empty($j172_tipopromitente) || $tipoAdquirente == 1) {
            $j172_tipopromitente = 'null';
        }

        $clhistoricoproprietarios->j172_sequencial       = $j172_sequencial;
        $clhistoricoproprietarios->j172_data             = $j172_data;
        $clhistoricoproprietarios->j172_protocolo        = $j172_protocolo;
        $clhistoricoproprietarios->j172_tipoproprietario = $j172_tipoproprietario;
        $clhistoricoproprietarios->j172_tipopromitente   = $j172_tipopromitente;
        $clhistoricoproprietarios->j172_adquirente       = $j172_adquirente;
        $clhistoricoproprietarios->j172_observacao       = $j172_observacao;
        $clhistoricoproprietarios->j172_matric           = $j172_matric;
        $clhistoricoproprietarios->j172_numcgm           = $j172_numcgm;
        $clhistoricoproprietarios->j172_percentual       = $j172_percentual;

        $clhistoricoproprietarios->incluir($j172_sequencial);
        $erro_msg = $clhistoricoproprietarios->erro_msg;

        if ($clhistoricoproprietarios->erro_status == 0) {
            $sqlerro = true;
        }

        db_fim_transacao($sqlerro);
    }
} else if (isset($alterar)) {
    if (empty($tipoAdquirente)) {
        $erro_msg = "Selecione um tipo de proprietário ou promitente";
        $sqlerro = true;
    }

    if ($_POST['j172_percentual'] > 100) {
        $erro_msg = "Percentual não pode ser maior que 100%";
        $sqlerro = true;
    }

    if ($sqlerro == false) {
        db_inicio_transacao();

        if (empty($j172_tipoproprietario) || $tipoAdquirente == 2) {
            $j172_tipoproprietario = 'null';
        }
        if (empty($j172_tipopromitente) || $tipoAdquirente == 1) {
            $j172_tipopromitente = 'null';
        }

        $clhistoricoproprietarios->j172_sequencial       = $j172_sequencial;
        $clhistoricoproprietarios->j172_data             = $j172_data;
        $clhistoricoproprietarios->j172_protocolo        = $j172_protocolo;
        $clhistoricoproprietarios->j172_tipoproprietario = $j172_tipoproprietario;
        $clhistoricoproprietarios->j172_tipopromitente   = $j172_tipopromitente;
        $clhistoricoproprietarios->j172_adquirente       = $j172_adquirente;
        $clhistoricoproprietarios->j172_observacao       = $j172_observacao;
        $clhistoricoproprietarios->j172_matric           = $_POST['j172_matric'];
        $clhistoricoproprietarios->j172_numcgm           = $j172_numcgm;
        $clhistoricoproprietarios->j172_percentual       = $j172_percentual;

        $clhistoricoproprietarios->alterar($j172_sequencial);
        $erro_msg = $clhistoricoproprietarios->erro_msg;

        if ($clhistoricoproprietarios->erro_status == 0) {
            $sqlerro = true;
        }

        db_fim_transacao($sqlerro);

    }
} else if (isset($excluir)) {
    if ($sqlerro == false) {
        db_inicio_transacao();
        $clhistoricoproprietarios->excluir($j172_sequencial);
        $erro_msg = $clhistoricoproprietarios->erro_msg;
        if ($clhistoricoproprietarios->erro_status == 0) {
            $sqlerro = true;
        }
        db_fim_transacao($sqlerro);
    }
} else if (isset($opcao)) {    
    $result = db_query($clhistoricoproprietarios->sqlHistoricoProprietarios("j172_sequencial = $j172_sequencial"));
    if ($result != false && pg_num_rows($result) > 0) {
        db_fieldsmemory($result, 0);
    }

    if (!empty($j163_tipoproprietario)) {
        $tipoAdquirente = 1;
    } else if (!empty($j164_tipopromitente)) {
        $tipoAdquirente = 2;
    }

}

if (isset($db_opcaoal)) {
    $db_opcao = 33;
    $db_botao = false;
} else if (isset($opcao) && $opcao == "alterar") {
    $db_botao = true;
    $db_opcao = 2;
} else if (isset($opcao) && $opcao == "excluir") {
    $db_opcao = 3;
    $db_botao = true;
} else {
    $db_opcao = 1;
    $db_botao = true;
    if (isset($novo) || isset($alterar) || isset($excluir) || (isset($incluir) && $sqlerro == false)) {
        $j172_sequencial = "";
        $j172_data = "";
        $j172_protocolo = "";
        $j172_tipoproprietario = "";
        $j172_tipopromitente = "";
        $j172_adquirente = "";
        $j172_observacao = "";
        $j172_numcgm = "";
    }
}

?>
<html>

<head>
    <title>DBSeller Inform&aacute;tica Ltda - P&aacute;gina Inicial</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <meta http-equiv="Expires" CONTENT="0">
    <script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
    <script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
    <link href="estilos.css" rel="stylesheet" type="text/css">
</head>

<body class="body-default" onLoad="a=1">
    <div class="container">
        <form name="form1" method="post" action="">
            <?php 
                // Sequence como hidden
                db_input('j172_sequencial',6,$Ij172_sequencial,true,'hidden',3,"");
                db_input('testaentra',6,$Itestaentra,true,'hidden',3,"");
            ?>
            <fieldset>
                <legend>Histórico de Proprietários</legend>
                <table class="form-container">
                    <tr>
                        <td nowrap title="<?= @$Tj172_matric ?>">
                            <?= @$Lj172_matric ?>
                        </td>
                        <td nowrap title="<?= @$Tj172_matric ?>">
                            <?php
                                if (!empty($j01_matric)) {
                                    $j172_matric = $j01_matric;
                                }
                                db_input('j172_matric', 6, $Ij172_matric, true, 'text', 3, "");
                            ?>
                        </td>
                    <tr>
                    <tr>
                        <td nowrap title="<?= @$Tj172_data ?>">
                            <?= @$Lj172_data ?>
                        </td>
                        <td nowrap title="<?= @$Tj172_data ?>">
                            <?php
                            db_inputdata('j172_data', $j172_data_dia, $j172_data_mes, $j172_data_ano, true, 'text', $db_opcao);
                            ?>
                        </td>
                    <tr>
                    <tr>
                        <td nowrap title="<?= @$Tj172_protocolo ?>">
                            <?php
                            echo($Lj172_protocolo);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input('j172_protocolo', 26, $Ij172_protocolo, true, 'text', $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Tipo Adquirente:</td>
                        <td> 
                            <?php
                            $x = array('0'=>'Selecione','1'=>'Proprietario','2'=>'Promitente');
                            db_select('tipoAdquirente',$x,true,$db_opcao,"onchange=js_tipo_adquirente();");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Forma de Aquisição</td>
                        <td>
                            <?php
                            // Tipo Proprietário
                            $rsDescricaoTipoProprietario = db_query("SELECT j163_tipoproprietario,j163_descricao FROM tipoproprietario ORDER BY j163_tipoproprietario;");
                            $arrayTipoProprietario = [];
                            foreach (pg_fetch_all($rsDescricaoTipoProprietario) as $key => $value) {
                                $arrayTipoProprietario[$value['j163_tipoproprietario']] = $value['j163_descricao'];
                            }
                            db_select('j172_tipoproprietario', $arrayTipoProprietario, true, $db_opcao, "style=width:200px;");

                            // Tipo Promitente
                            $rsDescricaoTipoPromitente = db_query("SELECT j164_tipopromitente,j164_descricao FROM tipopromitente ORDER BY j164_tipopromitente;");
                            $arrayTipoPromitente = [];
                            foreach (pg_fetch_all($rsDescricaoTipoPromitente) as $key => $value) {
                                $arrayTipoPromitente[$value['j164_tipopromitente']] = $value['j164_descricao'];
                            }
                            db_select('j172_tipopromitente', $arrayTipoPromitente, true, $db_opcao, "style=width:200px");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?=@$Tj172_numcgm?>">
                            <?php
                            db_ancora(@$Lj172_numcgm,"js_pesquisaj172_numcgm(true);",$db_opcao);
                            ?>
                        </td>
                        <td> 
                            <?php
                            db_input('j172_numcgm',10,$Ij172_numcgm,true,'text',$db_opcao," onchange='js_pesquisaj172_numcgm(false);'");
                            ?>
                        </td>
                        </tr>
                    <tr>
                        <td nowrap title="<?= @$Tj172_adquirente ?>">
                            <?php
                            echo($Lj172_adquirente);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input('j172_adquirente', 40, $Ij172_adquirente, true, 'text', $db_opcao, "");
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Tj172_percentual ?>">
                            <?php
                            echo($Lj172_percentual);
                            ?>
                        </td>
                        <td>
                            <?php
                            db_input('j172_percentual', 05, $Ij172_percentual, true, 'text', $db_opcao, "");
                            ?>%
                        </td>
                    </tr>
                    <tr>
                        <td nowrap title="<?= @$Tj172_observacao ?>">
                            <?= @$Lj172_observacao ?>
                        </td>
                        <td nowrap title="<?= @$Tj172_observacao ?>">
                            <?php
                            db_textarea('j172_observacao', 1, 1, $Ij172_observacao, true, 'text', $db_opcao);
                            ?>
                        </td>
                    </tr>
                </table>
            </fieldset>
            <input name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>" type="submit" id="db_opcao" value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?>>
            <input name="novo" type="button" id="cancelar" value="Novo" onclick="js_cancelar();" <?= ($db_opcao == 1 || isset($db_opcaoal) ? "style='visibility:hidden;'" : "") ?>>
            <br /><br />
            <table>
                <tr>
                    <td valign="top" align="center">
                        <?
                        $chavepri = array("j172_sequencial" => @$j172_sequencial);
                        $cliframe_alterar_excluir->chavepri = $chavepri;
                        $cliframe_alterar_excluir->sql = $clhistoricoproprietarios->sqlHistoricoProprietarios("j172_matric = $j172_matric");
                        $cliframe_alterar_excluir->campos  = "j172_data,j172_protocolo,j163_descricao,j164_descricao,j172_adquirente,j172_percentual,j172_observacao";
                        $cliframe_alterar_excluir->legenda = "ITENS LANÇADOS";
                        $cliframe_alterar_excluir->iframe_height = "160";
                        $cliframe_alterar_excluir->iframe_width = "700";
                        $cliframe_alterar_excluir->iframe_alterar_excluir(1);
                        ?>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <script>
        document.getElementById('tipoAdquirente').style.width='200';
        document.getElementById('j172_tipoproprietario').style.display='none';
        document.getElementById('j172_tipopromitente').style.display='none';
        
        const tipoAdquirente = document.getElementById('tipoAdquirente').value;
        if (tipoAdquirente > 0) {
            js_tipo_adquirente();
        }

        function js_cancelar() {
            var opcao = document.createElement("input");
            opcao.setAttribute("type", "hidden");
            opcao.setAttribute("name", "novo");
            opcao.setAttribute("value", "true");
            document.form1.appendChild(opcao);
            document.form1.submit();
        }
        function js_tipo_adquirente() {
            var tipoAdquirente = document.getElementById('tipoAdquirente').value;

            if (tipoAdquirente == 1) {
                document.getElementById('j172_tipoproprietario').style.display='block';
                document.getElementById('j172_tipopromitente').style.display='none';
            } else if (tipoAdquirente == 2) {
                document.getElementById('j172_tipoproprietario').style.display='none';
                document.getElementById('j172_tipopromitente').style.display='block';
            } else {
                document.getElementById('j172_tipoproprietario').style.display='none';
                document.getElementById('j172_tipopromitente').style.display='none';
            }
        }

        function js_pesquisaj172_numcgm(mostra){
            if(mostra==true){
                var testaentra = document.getElementById('testaentra').value;
                if (testaentra) {
                    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_cgm','func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome&testanome=true',
'Pesquisa',true,'100', '100',  screen.availWidth, '600');
                } else {
                    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_historicoproprietarios','db_iframe_cgm','func_nome.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome&testanome=true',
'Pesquisa',true,'100', '100',  screen.availWidth, '600');
                }
            }else{
                if(document.form1.j172_numcgm.value != ''){ 
                    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_historicoproprietarios','db_iframe_cgm','func_nome.php?pesquisa_chave='+document.form1.j172_numcgm.value+'&funcao_js=parent.js_mostracgm&testanome=1','Pesquisa',false);
                }else{
                    document.form1.j172_adquirente.value = ''; 
                    document.form1.j172_adquirente.readOnly = false;
                    document.form1.j172_adquirente.style.backgroundColor='#fff';
                }
            }
        }
        function js_mostracgm(erro,chave){
            document.form1.j172_adquirente.value = chave; 
            document.form1.j172_adquirente.readOnly = true;
            document.form1.j172_adquirente.style.backgroundColor='#DEB887';
            if(erro==true){ 
                document.form1.j172_numcgm.focus(); 
                document.form1.j172_numcgm.value = ''; 
                document.form1.j172_adquirente.readOnly = false;
                document.form1.j172_adquirente.style.backgroundColor='#fff';
            }
        }
        function js_mostracgm1(chave1,chave2){
            document.form1.j172_numcgm.value = chave1;
            document.form1.j172_adquirente.value = chave2;
            document.form1.j172_adquirente.readOnly = true;
            document.form1.j172_adquirente.style.backgroundColor='#DEB887';
            db_iframe_cgm.hide();
        }
    </script>
</body>

</html>
<?php
if (isset($alterar) || isset($excluir) || isset($incluir)) {
    db_msgbox($erro_msg);
    if ($clhistoricoproprietarios->erro_campo != "") {
        echo "<script> document.form1." . $clhistoricoproprietarios->erro_campo . ".style.backgroundColor='#99A9AE';</script>";
        echo "<script> document.form1." . $clhistoricoproprietarios->erro_campo . ".focus();</script>";
    }
}
?>