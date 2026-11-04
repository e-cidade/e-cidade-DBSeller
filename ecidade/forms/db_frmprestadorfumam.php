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

$clprestador->rotulo->label();
$clrotulo = new rotulocampo;

$clrotulo->label("fm06_codigo");
$clrotulo->label("fm06_depart");
$clrotulo->label("fm06_numcgm");
$clrotulo->label("z01_cgccpf");

if ($db_opcao == 1) {
  $sNameBotaoProcessar = "incluir";
} else if ($db_opcao == 2 || $db_opcao == 22) {
  $sNameBotaoProcessar = "alterar";
} else {
  $sNameBotaoProcessar = "excluir";
}
?>
<form name="form1" method="post">
  <fieldset>
    <legend><b>Prestador</b></legend>
		<table border="0">
		  <tr>
		    <td nowrap title="<?php echo $Tfm06_codigo?>">
		      <?php echo "$Lfm06_codigo";?>
		    </td>
		    <td>
					<?
					  db_input('fm06_codigo',10,$Ifm06_codigo,true,'text',3,"");
					?>
		    </td>
		  </tr>
		  <tr>
		    <td nowrap title="<?php echo $Tfm06_depart?>">
		      <?php
		        db_ancora($Lfm06_depart,"js_pesquisa_departamento(true);",$db_opcao);
		      ?>
		    </td>
		    <td>
					<?php
						db_input('fm06_depart',$Mfm06_depart,$Ifm06_depart,true,'text',$db_opcao," onchange='js_pesquisa_departamento(false);'");
						db_input('depart_descricao',50,'depart_descricao',true,'text',3,'');
 	        ?>
		    </td>
		  </tr>
		  <tr>
		    <td nowrap title="<?php echo $Tfm06_numcgm;?>">
		      <?php
		        db_ancora($Lfm06_numcgm,"js_pesquisa_numcgm(true);",$db_opcao);
		      ?>
		    </td>
		    <td>
					<?php
						db_input('fm06_numcgm',$Mfm06_numcgm, $Ifm06_numcgm,true,'text',$db_opcao," onchange='js_pesquisa_numcgm(false);'");
						db_input('z01_nome',50,'z01_nome',true,'text',3,'');
 	        ?>
		    </td>
		  </tr>          
		  <tr>
		    <td nowrap title="<?php echo 'nomefantasia';?>">
		      <label><b>Nome Fantasia: </b></label>
		    </td>
		    <td nowrap> 
					<?php
					  db_input('z01_nomefanta',100,'z01_nomefanta',true,'text',3,"");
					?>
		    </td>
		  </tr>
      <tr>
        <td nowrap title="<?php echo $Tz01_cgccpf;?>" >
            <label id="lcpf" for="lcpf"><b>CNPJ/CPF: </b></label>
        </td>
        <td nowrap>
            <?php
              if (isset($z01_cgccpf) && !empty($z01_cgccpf)) {
                 if (strlen($z01_cgccpf) > 11) {
                    $z01_cgccpf = db_formatar($z01_cgccpf, 'cnpj');
                  } else {
                    $z01_cgccpf = db_formatar($z01_cgccpf, 'cpf');
                 }
              }
              db_input ('z01_cgccpf', 15, 'z01_cgccpf', true, 'text', 3 );
            ?>                       
        </td>
      </tr>                
      <tr>
        <td nowrap title="Número" >
            <label id="lz01_numero" for="lz01_numero"><b>Número: </b></label>
        </td>
        <td nowrap>
            <?php
              db_input ('z01_numero', 15, 'z01_numero', true, 'text', 3 ); 
            ?>                       
            <label id="lcomplemento" for="'complemento'"><b>Complemento: </b></label>
            <?php
              db_input ('z01_compl', 20, 'z01_compl', true, 'text', 3 );
            ?>
        </td>
      </tr>
      <tr>
        <td title="<?php echo 'Endereço';?>">
            <label><b>Endereço: </b></label>
        </td>
        <td>
            <?php
              db_input('z01_ender',100,'z01_ender',true,'text',3,"");
            ?>
        </td>
      </tr>        
      <tr>
        <td title="Bairro">
            <label><b>Bairro: </b></label>
        </td>
        <td>
            <?php
              db_input('z01_bairro',60,'z01_bairro',true,'text',3,"");
            ?>
        </td>
      </tr>  
      <tr>
        <td title="Município">
            <label><b>Município: </b></label>
        </td>
        <td nowrap>
            <?php
              db_input('z01_munic',60,'z01_munic',true,'text',3,"");
            ?>
        </td>
      </tr>                    
      <tr>
        <td nowrap title="Telefone">
            <label id="ltelefone" for="ltelefone"><b>Telefone: </b></label>
        </td>
        
        <td nowrap>
            <?php
              $iInstit = db_getsession('DB_instit');
              db_input ('iInstit', 10, 'iInstit', true, 'hidden', 3 );
              db_input ('z01_telcel', 15, 'z01_telcel', true, 'text', 3 );
            ?>                       
        </td>
      </tr>                                                   
	  </table>
  </fieldset>
  <center>
    <input name="<?php echo $sNameBotaoProcessar; ?>" type="submit" id="db_opcao" value="<?php echo ucfirst($sNameBotaoProcessar); ?>" <?php echo (!$db_botao ? "disabled" : ""); ?> >
    <?php if ($db_opcao != 1) {?>
      <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisar();" >
    <?php } else { ?>
      <input name="limpar" type="button" id="limpar" value="Limpar Campos" onclick="js_limpar();" >
    <?php }?>
  </center>
</form>
<script language="JavaScript" type="text/javascript" src="scripts/scripts.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/prototype.js"></script>
<script>

function js_limpar() {
      document.getElementById('db_opcao').disabled = false;
      parent.document.formaba.profissionais.disabled = true;
      location.href = 'fum4_prestadores004.php?db_opcao=1';
}

function js_pesquisa_departamento(mostra) {
  if (mostra==true) {
    js_OpenJanelaIframe('','db_iframe_departamento',
                        'func_db_departalt.php?funcao_js=parent.js_mostradepartamento1|coddepto|descrdepto&iInstit='+document.form1.iInstit.value,
                        'Pesquisa',true);
  } else {
     if (document.form1.fm06_depart.value != '') {
        js_OpenJanelaIframe('',
                            'db_iframe_departamento',
                            'func_db_departalt.php?pesquisa_chave='+document.form1.fm06_depart.value+'&funcao_js=parent.js_mostradepartamento',
                            'Pesquisa',false);
     } else {
       document.form1.depart_descricao.value = '';
     }
  }
}

function js_mostradepartamento(chave,erro) {
  document.form1.depart_descricao.value = chave;
  if (erro == true) {
    document.form1.fm06_depart.focus();
    document.form1.fm06_depart.value = '';
  }
}

function js_mostradepartamento1(chave1,chave2) {
  document.form1.fm06_depart.value = chave1;
  document.form1.depart_descricao.value = chave2;
  db_iframe_departamento.hide();
}

function js_pesquisa_numcgm(mostra) {

    if (mostra == true) {
       js_OpenJanelaIframe('', 'db_iframe_nome',
        'func_prestadorfumam.php?funcao_js=parent.js_mostracgm1|z01_numcgm|z01_nome|z01_numero|z01_compl|z01_bairro|z01_telcel|z01_cgccpf|z01_munic|z01_ender|z01_nomefanta&incproc=true',
        'Pesquisa', true);
    } else {
        if (document.form1.fm06_numcgm.value != '') {
           js_OpenJanelaIframe('', 'db_iframe_nome',
            'func_prestadorfumam.php?pesquisa_chave=' + document.form1.fm06_numcgm.value + '&incproc=true&filtro=4&funcao_js=parent.js_mostracgm', 'Pesquisa', false);
        } else {
           document.form1.fm06_numcgm.value = '';
        }
    }
}

function js_mostracgm(erro, chave1, chave2, chave3, chave4, chave5, chave6, chave7, chave8, chave9) {
    document.form1.z01_nome.value = chave1;

    if (erro == true) {
       document.form1.fm06_numcgm.focus();
       document.form1.fm06_numcgm.value = '';
       return;
    }

    let cnpj_cpf = js_formatar(chave2, "cpfcnpj");

    document.form1.z01_cgccpf.value = cnpj_cpf;
    document.form1.z01_numero.value = chave3;
    document.form1.z01_compl.value = chave4;
    document.form1.z01_bairro.value = chave5;
    document.form1.z01_telcel.value = chave6;
    document.form1.z01_munic.value = chave7;
    document.form1.z01_ender.value = chave8;
    document.form1.z01_nomefanta.value = chave9;    
}

function js_mostracgm1(chave1, chave2, chave3, chave4, chave5, chave6, chave7, chave8, chave9, chave10) {

    chave7 = chave7.replace(/[^0-9]/g, '');
    let cnpj_cpf = js_formatar(chave7, "cpfcnpj");

    document.form1.fm06_numcgm.value = chave1;
    document.form1.z01_nome.value = chave2;
    document.form1.z01_numero.value = chave3;
    document.form1.z01_compl.value = chave4;
    document.form1.z01_bairro.value = chave5;
    document.form1.z01_telcel.value = chave6;
    document.form1.z01_cgccpf.value = cnpj_cpf;
    document.form1.z01_munic.value = chave8;
    document.form1.z01_ender.value = chave9;
    document.form1.z01_nomefanta.value = chave10;
    db_iframe_nome.hide();
}

</script>