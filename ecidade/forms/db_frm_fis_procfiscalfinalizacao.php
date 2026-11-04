<?php
// ini_set('display_errors',1);
// ini_set('display_startup_errors',1);
// error_reporting(-1);
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

$clrotulo = new rotulocampo;
$clprocfiscal->rotulo->label();

?>
<style type="text/css">
        fieldset {border-radius:7px;padding:20px;}
    </style>
    <br />
    <br />
<form name="form1" method="post" action="">
    <fieldset style="width: 600px;">
        <legend><strong>Finalizar / Prorrogar Processo Fiscal</strong></legend>
        <center>
            <table border="0">
                <tr>
                    <td><b>Ação:</b></td>
                    <td>
                        <?php 
                            $matriz = array('1' => 'Prorrogar', '2' => 'Finalizar');
                            db_select('situacao',$matriz, true, $db_opcao,""); 
                        ?>
                    </td>
                </tr>
                <tr>
                    <td><b><?php db_ancora('Processo fiscal:',"jsPesquisaProcessoFiscal(true);",$db_opcao); ?></b></td>
                    <td>
                        <?php db_input('processo_fiscal',10,@$processo_fiscal,true,'text',$db_opcao,"jsPesquisaProcessoFiscal(false);"); ?>
                    </td>
                </tr>
		<?php
			$data_abertura = date('Y-m-d'); 
			//echo $data_abertura;
			db_input('data_abertura',10,$data_abertura,true,'hidden',$db_opcao,""); 
		?>
                <tr style="display: none;" id="tr_data_prorrogacao">
                    <td><b>Data Limite:</b></td>
                    <td><?php db_inputdata('data_prorrogacao',@$data_prorrogacao_dia,@$data_prorrogacao_mes,@$data_prorrogacao_ano,true,'text',$db_opcao,""); ?></td>
                </tr>
                <tr style="display: none;" id="tr_data_finalizacao">
                    <td><b>Data de finalização:</b></td>
                    <td><?php db_inputdata('data_fim',@$data_fim_dia,@$data_fim_mes,@$data_fim_ano,true,'text',$db_opcao,""); ?></td>
                </tr>
                <tr>
                    <td><b>Observação:</b></td>
                    <td><?php db_textarea('observacao',3,52,@$observacao,true,'text',$db_opcao,""); ?></td>
                </tr>
            </table>
        </center>
    </fieldset>
    <br />
    <input name="incluir" type="submit" id="db_opcao" value="Finalizar" onclick="return js_valida();" />
    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="jsPesquisaProcessoFiscal(true);" />
</form>
<script language="JavaScript" type="text/javascript" src="scripts/numbers.js"></script>
<script language="JavaScript" type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript">
$('#situacao').change(function(event) {
    document.form1.processo_fiscal.value = '';
});
var situacao = document.getElementById('situacao');
var trDataProrrogacao = document.getElementById('tr_data_prorrogacao');
var trDataFinalizacao = document.getElementById('tr_data_finalizacao');
var botao = document.getElementById('db_opcao');

if (situacao.value != undefined && situacao.value == 1) {
    trDataProrrogacao.style.display = '';
    botao.value = 'Prorrogar';
}else if (situacao.value != undefined && situacao.value == 2) {
    trDataFinalizacao.style.display = '';
    botao.value = 'Finalizar';
}

situacao.onchange=function(){
    if (this.value == '1') {
        trDataProrrogacao.style.display = '';
        trDataFinalizacao.style.display = 'none';
        botao.value = 'Prorrogar';
    } else if (this.value == '2') {
        trDataFinalizacao.style.display = '';
        trDataProrrogacao.style.display = 'none';
        botao.value = 'Finalizar';
    } else {
        trDataProrrogacao.style.display = 'none';
        trDataFinalizacao.style.display = 'none';
        botao.value = 'Salvar';
    }
};

function jsPesquisaProcessoFiscal(mostra){
    if(mostra==true){
        js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal.php?funcao_js=parent.jsPreencheProcessoFiscal|y100_sequencial&situa='+document.form1.situacao.value,'Pesquisa',true,'15');
  
    }else{
        js_OpenJanelaIframe('','db_iframe_procfiscal','func_fis_procfiscal.php?pesquisa_chave='+document.form1.processo_fiscal.value+'&funcao_js=parent.jsPreencheProcessoFiscal2&situa='+document.form1.situacao.value,'Pesquisa',false);
    }
}

function jsPreencheProcessoFiscal(chave,chave1){
    document.form1.processo_fiscal.value = chave;
    db_iframe_procfiscal.hide();
}

function jsPreencheProcessoFiscal2(chave,erro){
   document.form1.processo_fiscal.value = chave;
    if(erro==true){
        document.form1.processo_fiscal.focus();
        document.form1.processo_fiscal.value = '';
    }
    db_iframe_procfiscal.hide();
}
function js_valida(){

    var sit = document.form1.situacao.value;

    if( sit == 2 ){

        var dtFinaliza    = new Date(document.form1.data_fim_ano.value, document.form1.data_fim_mes.value-1, document.form1.data_fim_dia.value);
        var dHoje = new Date();
          if(dtFinaliza  > dHoje){
            alert("A data de finalização não pode ser superior ao dia de hoje!");
            return false;
          }else{
            return true;
          }
    }else{
        return true;
    }

}
</script>
