<?php 
/*
*     E-cidade Software Publico para Gestao Municipal                
*  Copyright (C) 2009 DBSeller Servicos de Informatica             
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

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clrotulo = new rotulocampo;

if(isset($opcao) && $opcao == "alterar"){
    $usuario = $_POST['id_usuario'];
    echo "<script>parent.iframe_fiscais.location.href='fis1_fis_tipoandamusu002.php?chavepesquisa=$usuario&parametrosandamento=$parametrosandamento&db_opcao=2'</script>";
}

if(isset($opcao) && $opcao == "excluir"){
    $usuario = $_POST['id_usuario'];
    echo "<script>parent.iframe_fiscais.location.href='fis1_fis_tipoandamusu003.php?chavepesquisa=$usuario&parametrosandamento=$parametrosandamento&db_opcao=3'</script>";
}

?>
<form name="form1" method="post" action="">
<?php 
  db_input('parametrosandamento',5,$parametrosandamento,true,'hidden',3,'');
  db_input('sequencial',5,$sequencial,true,'hidden',3,'');
?>
    <center>
        <table border="0">
            <br />
            <tr>
                <td nowrap title="Código do Usuário">
                    <?php 
                        db_ancora('Código do Usuário',"js_pesquisay40_id_usuario(true);",$db_opcao);
                    ?>
                </td>
                <td> 
                    <?php 
                        db_input('id_usuario',5,$id_usuario,true,'text',$db_opcao," onchange='js_pesquisay40_id_usuario(false);'")
                    ?>
                    <?php 
                        db_input('nome',20,$nome,true,'text',3,'')
                    ?>
                </td>
            </tr>
            <tr>
                <td align="center" colspan="3">
                    <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
                </td>
            </tr>  
            <tr>
                <td align="top" colspan="3">
                    <?php 
                        $db_opcao = 1;
                        $chavepri= array("id_usuario"=>@$id_usuario,"parametrosandamento"=>@$parametrosandamento);
                        $cliframe_alterar_excluir->chavepri=$chavepri;
                        $cliframe_alterar_excluir->opcoes=1;
                        $cliframe_alterar_excluir->campos="id_usuario,nome";
                        $cliframe_alterar_excluir->sql=$cldbusuariosestendida->sqlQueryUsuariosParametrosAndamento("distinct(db_usuarios.id_usuario) as id_usuario, db_usuarios.nome","fis_parametrosandamento.sequencial = $parametrosandamento");
                        
                        $cliframe_alterar_excluir->legenda="Fiscais";
                        $cliframe_alterar_excluir->msg_vazio ="<font size='1'>Nenhum Usuário Cadastrado!</font>";
                        $cliframe_alterar_excluir->textocabec ="darkblue";
                        $cliframe_alterar_excluir->textocorpo ="black";
                        $cliframe_alterar_excluir->fundocabec ="#aacccc";
                        $cliframe_alterar_excluir->fundocorpo ="#ccddcc";
                        $cliframe_alterar_excluir->iframe_height ="170";
                        $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);    
                    ?>
                </td>
            </tr>  
        </table>
    </center>
</form>
<script>
    function js_pesquisay40_id_usuario(mostra){
        if(mostra==true){
           js_OpenJanelaIframe('','db_iframe_fiscais','func_fis_cadfiscaisalt.php?funcao_js=parent.js_mostradb_usuarios1|id_usuario|nome','Pesquisa',true);
        }else{
            js_OpenJanelaIframe('','db_iframe_fiscais','func_fis_cadfiscaisalt.php?pesquisa_chave='+document.form1.id_usuario.value+'&funcao_js=parent.js_mostradb_usuarios','Pesquisa',false);
        }
    }

    function js_mostradb_usuarios(chave,erro){
        document.form1.nome.value = chave; 
        if(erro==true){ 
           document.form1.id_usuario.focus(); 
            document.form1.id_usuario.value = '';
        }
    }

    function js_mostradb_usuarios1(chave1,chave2){
        document.form1.id_usuario.value = chave1;
        document.form1.nome.value = chave2;
        db_iframe_fiscais.hide();
    }

    function js_pesquisa(){
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_fandamusu','func_fis_fandamusu.php?funcao_js=parent.js_preenchepesquisa|y40_codandam|1','Pesquisa',true);
    }

    function js_preenchepesquisa(chave,chave1){
        db_iframe_fandamusu.hide();
        <?php 
            if($db_opcao!=1){
                echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave;";
            }
        ?>
    }
</script>
<?php 
    echo "<script>parent.document.formaba.fiscais.focus()</script>";
?>
