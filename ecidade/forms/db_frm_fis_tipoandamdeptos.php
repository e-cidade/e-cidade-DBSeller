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

require_once(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;

$clrotulo = new rotulocampo;

if(isset($opcao) && $opcao == "alterar"){
    echo "<script>parent.iframe_deptos.location.href='fis1_fis_tipoandamdeptos002.php?chavepesquisa=$coddepto&parametrosandamento=$parametrosandamento&db_opcao=2'</script>";
}

if(isset($opcao) && $opcao == "excluir"){
    echo "<script>parent.iframe_deptos.location.href='fis1_fis_tipoandamdeptos003.php?chavepesquisa=$coddepto&parametrosandamento=$parametrosandamento&db_opcao=3'</script>";
}

?>
<style type="text/css">
  fieldset {border-radius:7px;padding:20px;}
</style>
<br />
<br />
<form name="form1" method="post" action="">
    <?php db_input('parametrosandamento',5,$parametrosandamento,true,'hidden',3,'');
	  db_input('sequencial',5,$sequencial,true,'hidden',3,'');
	 ?>
    <table border="0">
        <br />
        <tr>
            <td width="150" nowrap title="Código Deptos">
                <?php
                    db_ancora("Código Departamento","js_pesquisa_deptos(true);",$db_opcao);
                ?>
            </td>
            <td>
                <?php
                    db_input('coddepto',10,$coddepto,true,'text',$db_opcao," onchange='js_pesquisa_deptos(false);'")
                ?>
                <?php
                    db_input('descrdepto',40,$descrdepto,true,'text',3,'')
                ?>
            </td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?> >
            </td>
        </tr>
        <tr>
            <td align="top" colspan="2">
                <?php

                    $db_opcao = 1;
                    $chavepri= array("coddepto"=>@$coddepto,"descrdepto"=>@$descrdepto);
                    $cliframe_alterar_excluir->chavepri=$chavepri;
                    $cliframe_alterar_excluir->opcoes=1;
                    $cliframe_alterar_excluir->campos="coddepto, descrdepto";
                    $cliframe_alterar_excluir->sql=$cldbdeptosestendida->sqlQueryDeptosParametrosAndamentos("distinct(db_depart.coddepto), db_depart.descrdepto","db_depart.instit = ".db_getsession('DB_instit')." and fis_deptos_parametrosandamento.parametrosandamento = ".$parametrosandamento, 'db_depart.coddepto');
                    $cliframe_alterar_excluir->legenda="Departamentos";
                    $cliframe_alterar_excluir->msg_vazio ="<font size='1'>Nenhum departamento cadastrado!</font>";
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
</form>
<script>

    function js_pesquisa_deptos(mostra){
        if(mostra==true){
           js_OpenJanelaIframe('','db_iframe_deptos','func_fis_deptos.php?funcao_js=parent.js_mostra_deptos1|coddepto|descrdepto','Pesquisa',true);
        }else{
            js_OpenJanelaIframe('','db_iframe_deptos','func_fis_deptos.php?pesquisa_chave='+document.form1.coddepto.value+'&funcao_js=parent.js_mostra_deptos','Pesquisa',false);
        }
    }

    function js_mostra_deptos(chave,erro){
        document.form1.nome.value = chave;
        if(erro==true){
           document.form1.coddepto.focus();
            document.form1.coddepto.value = '';
        }
    }

    function js_mostra_deptos1(chave1,chave2){
        document.form1.coddepto.value = chave1;
        document.form1.descrdepto.value = chave2;
        db_iframe_deptos.hide();
    }
</script>
<?php
    echo "<script>parent.document.formaba.fiscais.focus()</script>";
?>
