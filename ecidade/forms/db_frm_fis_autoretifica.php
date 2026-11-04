<br>
<form name="form1" method="post" action="">
<center>

  <table border="0">

    <tr>
      <td nowrap title="<?=@$Ty50_codauto?>" width="120">
         <strong>Auto de Infração:</strong>
      </td>
      <td width="auto"> 
        <?php 
          db_input('y50_codauto',10,$Iy50_codauto,true,'text',3,"");
        ?>
      </td>
    </tr>

    <tr>
      <td nowrap title="<?=@$Ly50_numbloco?>" width="120">
          <strong>Numero do Bloco:</strong>
      </td>
      <td width="auto"> 
        <?php 
          db_input('y50_numbloco',10,$Iy50_numbloco,true,'text',3,"");
        ?>
      </td>
    </tr>

    <tr>
      <td nowrap title="Processo">
        <?php 
          db_ancora('Processo',"js_pesquisay114_processo(true);",$db_opcao);
        ?>
      </td>
      <td> 
        <?php 
          db_input('y114_processo',15,'',true,'text',$db_opcao," onchange='js_pesquisay114_processo(false);'",'','','','15');
        ?>
        <?php  
          db_input('p58_requer',40,@$Ip58_requer,true,'text',3,'');
        ?>
      <td>
    </tr>

    <tr>
      <td nowrap colspan="2">
        <?php 
          echo "<strong>".@$dados."</strong>";
          db_input('y27_descr',30,$Iy27_descr,true,'text',3,"");
        ?>
      </td>
    </tr>

    <?php 
      $textoRelato       = '';

      $paSql  = "select * from fiscalizacao.fis_paragrafoauto where pl10_auto = $y50_codauto";
      $paRs   = db_query($paSql);
      $paRows = pg_num_rows($paRs);
      for ($i=0; $i < $paRows; $i++) { 
        db_fieldsmemory($paRs, $i);
        if($pl10_paragrafo == 1){
          $textoRelato = $pl10_texto;
          $codRelato   = $pl10_codigo;
        }
      }  
    ?>

    <tr <?=(($textoRelato=='')?'style="display:none"':'')?>>
        <td class="tdParagrafo">
          <strong>RELATO:</strong>
        </td>
        <td class="tdParagrafoTexto">
            <textarea name="paragrafoTexto" style="background-color:#DEB887; width:380px" rows="8" readonly="readonly"><?=$textoRelato?></textarea>
        </td>
    </tr>

    <tr>
      <td nowrap colspan="2" height="20">
        
      </td>
    </tr>

    <tr>
      <td nowrap colspan="2"> 
        <input name="retificar" type="submit" id="db_opcao" value="Retificar" <?php //=($db_botao==false?"disabled":"")?> >
        <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar" onclick="js_pesquisa();" >
      </td>
    </tr>

  </table>

</center>
</form>

<script>
function js_pesquisa(){
  js_OpenJanelaIframe('','db_iframe_auto','func_fis_autoalt.php?db_opcao=<?=$db_opcao?>&funcao_js=parent.js_preenchepesquisa|dl_auto&baixa=1&fisauto=1&retifica=1','Pesquisa',true);
}
function js_preenchepesquisa(chave){
  db_iframe_auto.hide();
  <?php 
  if($db_opcao!=1){
    echo " location.href = '".basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"])."?chavepesquisa='+chave";
  }
  ?>
}
</script>

<script>
function js_validaprocesso(validarprocesso,gerachaves) {

  var processo        = document.form1.y114_processo.value;
  var validarProcesso = validarprocesso;
  var gerarChaves     = gerachaves;
  
  if (validarProcesso == true) {
    if (processo == '') {
      alert("Processo não informado!");
      return false;
    }
  }
  
  if (gerarChaves == true) {
    js_gera_chaves();
  }
}
function js_inscr(mostra){
  var inscr=document.form1.y50_codauto.value;
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_inscr','func_fis_autoalt.php?funcao_js=parent.js_mostrainscr|dl_auto|z01_nome&baixa=1&fisauto=1&retifica=1','Pesquisa',true);
  }else{
    if(inscr!=""){
      js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_inscr','func_fis_autoalt.php?pesquisa_chave='+inscr+'&funcao_js=parent.js_mostrainscr1&baixa=1&fisauto=1&retifica=1','Pesquisa',false);
    }else{
      document.form1.z01_nome.value="";
      document.form1.submit();  
    }
  }
}
function js_mostrainscr(chave1,chave2){
  document.form1.y50_codauto.value = chave1;
  document.form1.z01_nome.value = chave2;
  db_iframe_inscr.hide();
  document.form1.y114_processo.value = '';
  document.form1.p58_requer.value = '';
  document.form1.submit(); 
}
function js_mostrainscr1(chave,erro){
  document.form1.z01_nome.value = chave; 
  if(erro==true){ 
    document.form1.y50_codauto.focus(); 
    document.form1.y50_codauto.value = ''; 
    document.form1.submit();
  }else{
    document.form1.y114_processo.value = '';
    document.form1.p58_requer.value = '';
    document.form1.submit();
  }
}
function js_pesquisay114_processo(mostra){
  if(mostra==true){
    js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_processo','func_fis_processoadministrativo.php?funcao_js=parent.js_mostraprocesso1|p58_numero|p58_requer','Pesquisa',true);
  }else{
     if(document.form1.y114_processo.value != ''){ 
        js_OpenJanelaIframe('CurrentWindow.corpo','db_iframe_processo','func_fis_processoadministrativo.php?pesquisa_chave='+document.form1.y114_processo.value+'&funcao_js=parent.js_mostraprocesso','Pesquisa',false);
     }else{
       document.form1.y114_processo.value = ''; 
     }
  }
}
function js_mostraprocesso(chave,chave1,erro){
  document.form1.p58_requer.value = chave1; 
  if(erro==true){ 
    document.form1.y114_processo.focus(); 
    document.form1.y114_processo.value = ''; 
  }
}
function js_mostraprocesso1(chave1,chave2){
  document.form1.y114_processo.value = chave1;
  document.form1.p58_requer.value = chave2;
  db_iframe_processo.hide();
}
</script>
