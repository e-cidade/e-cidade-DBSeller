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

//MODULO: pessoal
require_once(modification("dbforms/db_classesgenericas.php"));
require_once(modification("libs/db_utils.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clrhpesdoc->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("db12_uf");
$clrotulo->label("db12_codigo");
$clrotulo->label("z01_pai");
$clrotulo->label("z01_mae");
$clrotulo->label("z01_identorgao");
$clrotulo->label("db12_codigo");
$clrotulo->label("z01_ident");
$clrotulo->label("z01_identdtexp");
$clrotulo->label("z01_telef");
$clrotulo->label("z01_telcel");
$clrotulo->label("z01_email");
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
  if (isset($novo) || isset($alterar) ||   isset($excluir) || (isset($incluir) && $sqlerro == false ) ){

    $rh16_titele                     = "";
    $rh16_zonael                     = "";
    $rh16_secaoe                     = "";
    $rh16_reserv                     = "";
    $rh16_catres                     = "";
    $rh16_ctps_n                     = "";
    $rh16_ctps_s                     = "";
    $rh16_ctps_d                     = "";
    $rh16_ctps_uf                    = "";
    $rh16_pis                        = "";
    $rh16_carth_n                    = "";
    $r16_carth_cat                   = "";
    $rh16_carth_val                  = "";
    $rh16_emissao                    = "";
    $rh16_data_emissao_cnh           = "";
    $rh16_orgao_classe               = "";
    $rh16_data_orgao_classe          = "";
    $rh16_orgao_emissor_classe       = "";
    $rh16_orgao_emissor_rne          = "";
    $rh16_data_emissao_rne           = "";
    $rh16_data_entrada_rne           = "";
    $rh16_data_validade_rne          = "";
    $rh16_uf_cnh                     = "";
    $rh16_data_validade_orgao_classe = "";
    $rh16_registro_rne               = "";
  }
}

if ($db_opcao == 1 || $db_opcao == 2 || $db_opcao == 11 || $db_opcao == 22) {

  $oDaoRhPesDoc    = db_utils::getDao("rhpesdoc");
  $sSqlDocServidor = $oDaoRhPesDoc->sql_query_file($rh16_regist);
  $rsDocServidor   = $oDaoRhPesDoc->sql_record($sSqlDocServidor);

  if ($oDaoRhPesDoc->numrows <= 0) {

    $db_opcao = 1;
    $db_botao = true;
  }

  $sSqlNome = "select z01_nome, z01_pai, z01_mae, z01_cgccpf,
	            z01_ident, z01_identorgao, z01_identdtexp,
		        z01_telef, z01_telcel, z01_ender||','||z01_numero||','||z01_compl as z01_ender, z01_email
                from cgm
                inner join rhpessoal on cgm.z01_numcgm = rhpessoal.rh01_numcgm
                where rh01_regist = {$rh16_regist}";

  $rsNome         = $oDaoRhPesDoc->sql_record($sSqlNome);
  $z01_nome       = db_utils::fieldsMemory($rsNome, 0)->z01_nome;
  $z01_pai        = db_utils::fieldsMemory($rsNome, 0)->z01_pai;
  $z01_mae        = db_utils::fieldsMemory($rsNome, 0)->z01_mae;
  $z01_cgccpf     = db_utils::fieldsMemory($rsNome, 0)->z01_cgccpf;
  $z01_ident      = db_utils::fieldsMemory($rsNome, 0)->z01_ident;
  $z01_identorgao = db_utils::fieldsMemory($rsNome, 0)->z01_identorgao;
  $z01_identdtexp = db_utils::fieldsMemory($rsNome, 0)->z01_identdtexp;
  $z01_telef      = db_utils::fieldsMemory($rsNome, 0)->z01_telef;
  $z01_telcel     = db_utils::fieldsMemory($rsNome, 0)->z01_telcel;
  $endPrimario    = db_utils::fieldsMemory($rsNome, 0)->z01_ender;
  $z01_email      = db_utils::fieldsMemory($rsNome, 0)->z01_email;

}

?>
<form name="form1" method="post" action="">

<table border='0'><tr><td><Fieldset><legend><b>DOCUMENTOS </b></legend>
<table border='0'>
  <tr>
    <td nowrap title="<?=@$Trh16_regist?>">
       <?=@$Lrh16_regist;?>
    </td>
    <td nowrap colspan='10'>
    <?php
      db_input('rh16_regist',8,$Irh16_regist,true,'text',3," onchange='js_pesquisarh16_regist(false);'");
      db_input('z01_nome',35,$Iz01_nome,true,'text',3,'');
    ?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh16_pis?>">
       <?=@$Lrh16_pis?>
    </td>
    <td>
			<?php
			db_input('rh16_pis',15,$Irh16_pis,true,'text', 3,"onblur = js_validaPis(this.value);")
			?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh16_titele?>">
       <?=@$Lrh16_titele?>
    </td>
<td>
<?php
db_input('rh16_titele',15,$Irh16_titele,true,'text',$db_opcao,"")
?>
</td>
<td>
       <?=@$Lrh16_zonael?>
			 </td><td>
<?php
db_input('rh16_zonael',4,$Irh16_zonael,true,'text',$db_opcao,"")
?>
</td>
<td>
       <?=@$Lrh16_secaoe?>
			 </td><td>

<?php
db_input('rh16_secaoe',4,$Irh16_secaoe,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh16_reserv?>">
       <?=@$Lrh16_reserv?>
    </td>
    <td>
<?php
db_input('rh16_reserv',15,$Irh16_reserv,true,'text',$db_opcao,"")
?>
 </td>
 <td>

 <?=@$Lrh16_catres?>
 </td><td>
 <?php db_input('rh16_catres',4,$Irh16_catres,true,'text',$db_opcao,"")
?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh16_ctps_n?>">
       <?=@$Lrh16_ctps_n?>
    </td>
    <td>
<?php
db_input('rh16_ctps_n',15,$Irh16_ctps_n,true,'text',$db_opcao,"")
?>
</td>
<td>
     <?=@$Lrh16_ctps_s?>
</td>
<td>
    <?php
db_input('rh16_ctps_s',4,$Irh16_ctps_s,true,'text',$db_opcao,"")
?>
 </td>
 <td>
       <?=@$Lrh16_ctps_d?>
			 </td>
			 <td>
<?php
db_input('rh16_ctps_d',4,$Irh16_ctps_d,true,'text',$db_opcao,"")
?>
  </td>
  </tr>
  <tr>
      <td nowrap title="<?=@$Trh16_ctps_uf?>">
        <?php
        db_ancora(@$Lrh16_ctps_uf,"",3);
        ?>
      </td>
      <td colspan='1'>
        <?php
          $result_uf = $cldb_uf->sql_record($cldb_uf->sql_query_file(null,"db12_codigo as rh16_ctps_uf,db12_uf"));
          db_selectrecord("rh16_ctps_uf",$result_uf,true,$db_opcao,"","","","0-Nenhum...");
        ?>
        <td nowrap title="<?=@$Trh16_emissao?>">
           <?=@$Lrh16_emissao?>
        </td>
      </td>
      <td>
		  	<?php
		  	  $rh16_emissao_val_mes = '';
		  	  $rh16_emissao_val_ano = '';
		  	  $rh16_emissao_val_dia = '';

		  	  if( isset($rh16_emissao) && $rh16_emissao != ""){
				list( $rh16_emissao_val_ano, $rh16_emissao_val_mes, $rh16_emissao_val_dia ) = explode( "-", $rh16_emissao );
		  	  }
		  	  db_inputdata('rh16_emissao',$rh16_emissao_val_dia,$rh16_emissao_val_mes,$rh16_emissao_val_ano,true,'text',$db_opcao,"")
		  	?>
      </td>
 </tr>
  <tr>
    <td nowrap title="<?=@$Trh16_carth_n?>">
       <?=@$Lrh16_carth_n?>
    </td>
    <td>
			<?php
			db_input('rh16_carth_n',15,$Irh16_carth_n,true,'text',$db_opcao,"")
			?>
    </td>

    <td nowrap title="<?=@$Tr16_carth_cat?>">
       <?=@$Lr16_carth_cat?>
    </td>

    <td>
			<?php
			db_input('r16_carth_cat',4,$Ir16_carth_cat,true,'text',$db_opcao,"")
			?>

    </td>
    <td nowrap title="<?=@$Trh16_uf_cnh?>">
       <?php
       db_ancora(@$Lrh16_uf_cnh,"",3);
       ?>
    </td>
    <td colspan='3'>
    <?php
        db_input("rh16_uf_cnh", @$Lrh16_uf_cnh, true, 'text', $db_opcao);
    ?>
    </td>

  </tr>
  <tr>
  <td nowrap title="<?=@$Trh16_carth_val?>">
       <?=@$Lrh16_carth_val?>
    </td>
    <td>
			 <?php
			  db_inputdata('rh16_carth_val',@$rh16_carth_val_dia,@$rh16_carth_val_mes,@$rh16_carth_val_ano,true,'text',$db_opcao,"")
			 ?>
    </td>
    <td nowrap title="<?=@$Trh16_data_emissao_cnh?>">
       <?=@$Lrh16_data_emissao_cnh?>
    </td>
    <td>
		  	<?php
		  	$rh16_data_emissao_cnh_ano = '';
		  	$rh16_data_emissao_cnh_mes = '';
		  	$rh16_data_emissao_cnh_dia = '';

		  	if( isset($rh16_data_emissao_cnh) && $rh16_data_emissao_cnh != ""){
		  		list( $rh16_data_emissao_cnh_ano, $rh16_data_emissao_cnh_mes, $rh16_data_emissao_cnh_dia ) = split( "[-]", $rh16_data_emissao_cnh );
		  	}
		  	db_inputdata('rh16_data_emissao_cnh',$rh16_data_emissao_cnh_dia,$rh16_data_emissao_cnh_mes,$rh16_data_emissao_cnh_ano,true,'text',$db_opcao,"")
		  	?>
     </td>
  </tr>

  <tr>
    <td nowrap title="<?=@$Trh16_orgao_classe?>">
         <?=@$Lrh16_orgao_classe?>
      </td>
      <td>
	  		<?php
	  		db_input('rh16_orgao_classe',15,$Irh16_orgao_classe,true,'text',$db_opcao,"")
	  		?>
    </td>
    <td nowrap title="<?=@$Trh16_data_orgao_classe?>">
         <?=@$Lrh16_data_orgao_classe?>
      </td>
      <td>
	  	  	<?php
	  	  	$rh16_data_orgao_classe_ano = '';
	  	  	$rh16_data_orgao_classe_mes = '';
	  	  	$rh16_data_orgao_classe_dia = '';

	  	  	if( isset($rh16_data_orgao_classe) && $rh16_data_orgao_classe != ""){
	  	  		list( $rh16_data_orgao_classe_ano, $rh16_data_orgao_classe_mes, $rh16_data_orgao_classe_dia ) = split( "[-]", $rh16_data_orgao_classe );
	  	  	}
	  	  	db_inputdata('rh16_data_orgao_classe',$rh16_data_orgao_classe_dia,$rh16_data_orgao_classe_mes,$rh16_data_orgao_classe_ano,true,'text',$db_opcao,"")
	  	  	?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh16_orgao_emissor_classe?>">
         <?=@$Lrh16_orgao_emissor_classe?>
      </td>
      <td>
	  		<?php
	  		db_input('rh16_orgao_emissor_classe',15,$Irh16_orgao_emissor_classe,true,'text',$db_opcao,"")
	  		?>
    </td>
    <td nowrap title="<?=@$Trh16_data_validade_orgao_classe?>">
         <?=@$Lrh16_data_validade_orgao_classe?>
      </td>
      <td>
	  	  	<?php
	  	  	$rh16_data_validade_orgao_classe_ano = '';
	  	  	$rh16_data_validade_orgao_classe_mes = '';
	  	  	$rh16_data_validade_orgao_classe_dia = '';

	  	  	if( isset($rh16_data_validade_orgao_classe) && $rh16_data_validade_orgao_classe != ""){
	  	  		list( $rh16_data_validade_orgao_classe_ano, $rh16_data_validade_orgao_classe_mes, $rh16_data_validade_orgao_classe_dia ) = split( "[-]", $rh16_data_validade_orgao_classe );
	  	  	}
	  	  	db_inputdata('rh16_data_validade_orgao_classe',$rh16_data_validade_orgao_classe_dia,$rh16_data_validade_orgao_classe_mes,$rh16_data_validade_orgao_classe_ano,true,'text',$db_opcao,"")
	  	  	?>
    </td>
  </tr>
  <tr>
  <td nowrap title="<?=@$Trh16_registro_rne?>">
         <?=@$Lrh16_registro_rne?>
      </td>
      <td>
	  		<?php
	  		db_input('rh16_registro_rne',15,$Irh16_registro_rne,true,'text',$db_opcao,"")
	  		?>
    </td>
    <td nowrap title="<?=@$Trh16_orgao_emissor_rne?>">
         <?=@$Lrh16_orgao_emissor_rne?>
      </td>
      <td>
	  		<?php
	  		db_input('rh16_orgao_emissor_rne',15,$Irh16_orgao_emissor_rne,true,'text',$db_opcao,"")
	  		?>
    </td>
  </tr>

  <tr>
  <td nowrap title="<?=@$Trh16_data_emissao_rne?>">
         <?=@$Lrh16_data_emissao_rne?>
      </td>
      <td>
	  	  	<?php
	  	  	$rh16_data_emissao_rne_ano = '';
	  	  	$rh16_data_emissao_rne_mes = '';
	  	  	$rh16_data_emissao_rne_dia = '';

	  	  	if( isset($rh16_data_emissao_rne) && $rh16_data_emissao_rne != ""){
	  	  		list( $rh16_data_emissao_rne_ano, $rh16_data_emissao_rne_mes, $rh16_data_emissao_rne_dia ) = split( "[-]", $rh16_data_emissao_rne );
	  	  	}
	  	  	db_inputdata('rh16_data_emissao_rne',$rh16_data_emissao_rne_dia,$rh16_data_emissao_rne_mes,$rh16_data_emissao_rne_ano,true,'text',$db_opcao,"")
	  	  	?>
    </td>
    <td nowrap title="<?=@$Trh16_data_entrada_rne?>">
         <?=@$Lrh16_data_entrada_rne?>
      </td>
      <td>
	  	  	<?php
	  	  	$rh16_data_entrada_rne_ano = '';
	  	  	$rh16_data_entrada_rne_mes = '';
	  	  	$rh16_data_entrada_rne_dia = '';

	  	  	if( isset($rh16_data_entrada_rne) && $rh16_data_entrada_rne != ""){
	  	  		list( $rh16_data_entrada_rne_ano, $rh16_data_entrada_rne_mes, $rh16_data_entrada_rne_dia ) = split( "[-]", $rh16_data_entrada_rne );
	  	  	}
	  	  	db_inputdata('rh16_data_entrada_rne',$rh16_data_entrada_rne_dia,$rh16_data_entrada_rne_mes,$rh16_data_entrada_rne_ano,true,'text',$db_opcao,"")
	  	  	?>
    </td>
  </tr>
  <tr>
    <td nowrap title="<?=@$Trh16_data_validade_rne?>">
         <?=@$Lrh16_data_validade_rne?>
      </td>
      <td>
	  	  	<?php
	  	  	$rh16_data_validade_rne_ano = '';
	  	  	$rh16_data_validade_rne_mes = '';
	  	  	$rh16_data_validade_rne_dia = '';

	  	  	if( isset($rh16_data_validade_rne) && $rh16_data_validade_rne != ""){
	  	  		list( $rh16_data_validade_rne_ano, $rh16_data_validade_rne_mes, $rh16_data_validade_rne_dia ) = split( "[-]", $rh16_data_validade_rne );
	  	  	}
	  	  	db_inputdata('rh16_data_validade_rne',$rh16_data_validade_rne_dia,$rh16_data_validade_rne_mes,$rh16_data_validade_rne_ano,true,'text',$db_opcao,"")
	  	  	?>
    </td>
  </tr>

	</fieldset></td></tr></table>
  <tr>
    <td align="center">
      <fieldset>
        <legend>DADOS DO CGM</legend>
      <table>
     <tr>
        <td nowrap title=<?=@$Tz01_pai?>>
          <?=@$Lz01_pai?>
        </td>
        <td nowrap title="<?=@$Tz01_pai?>" colspan="3">
         <?php
           db_input ( 'z01_pai', 50, $Iz01_pai, true, 'text', 3 );
         ?>
        </td>
      </tr>
      <tr>
        <td nowrap title=<?=@$Tz01_mae?>>
          <?=@$Lz01_mae?>
        </td>
        <td nowrap title="<?=@$Tz01_mae?>" colspan="3">
          <?php
            db_input ( 'z01_mae', 50, $Iz01_mae, true, 'text', 3 );
          ?>
        </td>
      </tr>
     <tr>
        <td title="CPF"><strong>CPF:</strong></td>
        <td align="left">
          <?php
            db_input ( 'z01_cgccpf', 15, @$Iz01_cgccpf, true, 'text', 3, "onBlur='js_verificaCGCCPF(this);'", '', '', 'text-align:left;', 11 );

          ?>
        </td>
        <td align="right" title="<?=@$Tz01_ident?>">
          <?=@$Lz01_ident?>
        </td>
        <td align="left">
          <?php
            db_input ( 'z01_ident', 15, $Iz01_ident, true, 'text', 3 );
          ?>
        </td>
      </tr>
      <tr>
        <td>
          <?=@$Lz01_identorgao?>
        </td>
        <td align="left">
          <?php
            db_input ( 'z01_identorgao', 15, @$Iz01_identorgao, true, 'text', 3 );
          ?>
        </td>
        <td align="right">
          <?=@$Lz01_identdtexp?>
        </td>
        <td align="left">
          <?php
	  	  	  $z01_identdtexp_dia = '';
	  	  	  $z01_identdtexp_mes = '';
	  	  	  $z01_identdtexp_ano = ''  ;

	  	  	  if( isset($z01_identdtexp) && $z01_identdtexp != ""){
	  	  	  	list( $z01_identdtexp_ano, $z01_identdtexp_mes, $z01_identdtexp_dia ) = split( "[-]", $z01_identdtexp );
	  	  	  }
            db_inputdata ( 'z01_identdtexp', @$z01_identdtexp_dia, @$z01_identdtexp_mes, @$z01_identdtexp_ano, true, 'text', 3 );
	  	  	?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Tz01_telef?>">
          <?=@$Lz01_telef?>
        </td>
        <td nowrap>
          <?php
            db_input ( 'z01_telef', 15, $Iz01_telef, true, 'text', 3 );
          ?>
        </td>
        <td nowrap title="<?=@$Tz01_telcel?>" align="right">
          <?=@$Lz01_telcel?>
        </td>
        <td nowrap align="left">
          <?php
            db_input ( 'z01_telcel', 15, $Iz01_telcel, true, 'text', 3 );
          ?>
        </td>
      </tr>
      <tr>
        <td nowrap title="<?=@$Tz01_email?>">
          <?=@$Lz01_email?>
        </td>
        <td colspan="3">
          <?php
            db_input ( 'z01_email', 50, $Iz01_email, true, 'text', 3 );
          ?>
        </td>
      </tr>
       <tr>
        <td colspan="4">
          <center><fieldset><legend> <strong>Endereço</strong></legend>
            <div align="center">
              <?php
                db_input ( 'endPrimario', 80, '', true, 'text', 3 );
              ?>
            </div>
          </fieldset></center>
        </td>
      </tr>
  </table>
</fieldset>
</td>
</tr>
	</tr>
	<tr>
    <td colspan="6" align="center">
      <input name="<?=($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>" type="submit" id="db_opcao" onclick="js_validaDatas();" value="<?=($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>" <?=($db_botao==false?"disabled":"")?>  >
    </td>
  </tr>
  </table>
</form>
<script>
function js_pesquisarh16_regist(mostra) {

  if (mostra == true) {
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpesdoc','db_iframe_rhpessoal','func_rhpessoal.php?funcao_js=parent.js_mostrarhpessoal1|rh01_regist|rh01_numcgm','Pesquisa',true,'0');
  } else {
     if (document.form1.rh16_regist.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpesdoc','db_iframe_rhpessoal','func_rhpessoal.php?pesquisa_chave='+document.form1.rh16_regist.value+'&funcao_js=parent.js_mostrarhpessoal','Pesquisa',false,'0');
     } else {
       document.form1.rh01_numcgm.value = '';
     }
  }
}

function js_mostrarhpessoal(chave, erro) {

  document.form1.rh01_numcgm.value = chave;
  if (erro == true) {

    document.form1.rh16_regist.focus();
    document.form1.rh16_regist.value = '';
  }
}

function js_mostrarhpessoal1(chave1, chave2) {

  document.form1.rh16_regist.value = chave1;
  document.form1.rh01_numcgm.value = chave2;
  db_iframe_rhpessoal.hide();
}

function js_pesquisarh16_ctps_uf(mostra) {

  if (mostra == true) {
    js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpesdoc','db_iframe_db_uf','func_db_uf.php?funcao_js=parent.js_mostradb_uf1|db12_codigo|db12_uf','Pesquisa',true,'0');
  } else {

     if (document.form1.rh16_ctps_uf.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_rhpesdoc','db_iframe_db_uf','func_db_uf.php?pesquisa_chave='+document.form1.rh16_ctps_uf.value+'&funcao_js=parent.js_mostradb_uf','Pesquisa',false,'0');
     } else {
       document.form1.db12_uf.value = '';
     }
  }
}

function js_mostradb_uf(chave, erro) {

  document.form1.db12_uf.value = chave;
  if (erro == true) {

    document.form1.rh16_ctps_uf.focus();
    document.form1.rh16_ctps_uf.value = '';
  }
}

function js_mostradb_uf1(chave1, chave2) {

  document.form1.rh16_ctps_uf.value = chave1;
  document.form1.db12_uf.value = chave2;
  db_iframe_db_uf.hide();
}

function js_validaPis(pis) {

  if (pis != '') {

    if (!js_ChecaPIS(pis)) {

      alert("Pis inválido. Verifique.");
      document.form1.rh16_pis.focus();
      document.form1.rh16_pis.value = '';
      return(false);
    } else {
      return(true);
    }
  }
}
</script>
