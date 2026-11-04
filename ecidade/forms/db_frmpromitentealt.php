<?php
/**
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

 $daoIptubase = new cl_iptubase();
?>
<script>
function js_verizero(){
  j41_numcgm = new Number(document.form1.j41_numcgm.value);
  z01_nome = document.form1.z01_nome.value;
  if(isNaN(j41_numcgm) || j41_numcgm=="0" || z01_nome=="Código () não Encontrado" ){
    alert("Verifique o campo com o numero da Matrícula!");
    document.form1.j41_numcgm.focus();
    return false;
  }
  return true;
}
function js_trocaid(valor){
  <?php
  if(isset($j41_matric) && $j41_matric!=""){
  ?>
    location.href="cad1_promitentealt.php?j41_matric=<?php echo $j41_matric?>&j41_numcgm="+valor;
  <?php
  }else{
  ?>
    location.href="cad1_promitentealt.php?j41_matric="+document.form1.j41_matric.value+"&j41_numcgm="+valor;
  <?php
  }
  ?>
}

/**
 * Gambiarra: A matricula nao estava sendo recarregada com a pagina, o que impossibilita de pegar o valor dela por php,
 * por isso foi criado essa funcao, para o javascript dar reload na pagina quando tiver o valor.
 */
async function js_recarregaPaginaComMatricula(){
  const matriculaComValor = <?=(isset($j41_matric) && trim($j41_matric) != '') ? $j41_matric : "''" ?>

  if(matriculaComValor){
    return;
  }

  while(true){
    await new Promise((resolve) => {
      const inputMatricula = document.getElementById('j41_matric');

      if(inputMatricula){
        const valorMatricula = inputMatricula.value;
        
        if(valorMatricula && valorMatricula !== ''){ 
          location.href="cad1_promitentealt.php?j41_matric=" + valorMatricula;
          return;
        }
      }

      setTimeout(() => {
        resolve('');
      }, 500)
    })
  }
}

js_recarregaPaginaComMatricula();
</script>

<fieldset>
  <legend><b>Promitentes do imóvel</b></legend>

<table border="0" width="790">
  <tr>
    <td nowrap title="<?php echo $Tj41_matric?>">
      <?php echo $Lj41_matric?>
    </td>
    <td>
        <?php
          db_input('j41_matric',10,$Ij41_matric,true,'text',3,"");
          db_input("z01_nome",78,$Ij01_numcgm,true,"text",3,"","z01_nomematri");
        ?>
    </td>
  </tr>
      <tr>
        <td nowrap title="<?php echo $Tj41_numcgm?>">

          <?php
            db_ancora($Lj41_numcgm,' js_cgm(true); ',$db_opcao==2?"3":"1");
          ?>

        </td>
        <td>

          <?php
            db_input('j41_numcgm',10,$Ij41_numcgm,true,'text',$db_opcao==2?"3":"1","onchange='js_cgm(false)'");
            db_input('z01_nome',78,$Iz01_nome,true,'text',3,"");
          ?>

        </td>
      </tr>
      <tr>
        <td nowrap title="<?php echo $Tj41_promitipo?>">
          <?php echo $Lj41_promitipo?>
        </td>
        <td>

          <?php
            $x = array("C"=>"Com contrato","S"=>"Sem contrato");

            $sSqlTipoPromitentes = $daoIptubase->sql_query_tipos_promitentes($j41_matric);
            $resultTipoPromitentes = db_query($sSqlTipoPromitentes);
            $tipoPromitentes = db_utils::getCollectionByRecord($resultTipoPromitentes);

            $descricaoTipoPromitentes = array_map(function($item){
              return $item->j164_descricao;
            },$tipoPromitentes);

            $abreviaturaTipoPromitentes = array_map(function($item){
              return $item->j164_tipopromitente;
            },$tipoPromitentes);

            $tipoPromitentes = array_combine($abreviaturaTipoPromitentes, $descricaoTipoPromitentes);

            db_select('j164_tipopromitente',$tipoPromitentes,true,$db_opcao,"");
          ?>

      </td>
     </tr>
     <tr>
       <td nowrap title="<?php echo $Tj41_tipopro?>">
         <?php echo $Lj41_tipopro?>
       </td>
       <td>
        <?php
        if ($outros == true) {
          $xs = array("f"=>"Secundário","t"=>"Principal");
        }else{
          $xs = array("t"=>"Principal","f"=>"Secundário");
        }
        db_select('j41_tipopro',$xs,true,$db_op,"");
        ?>
        </td>
      </tr>
      <tr><td><input name="cgmpromi" type="hidden" value="<?php echo $cgmpromi?>"> </td></tr>
      <tr><td><input name="cgmexclusao" id="cgmexclusao" type="hidden" value="<?php echo $cgmexclusao?>"> </td></tr>
  </table>
  </fieldset>

  <br />
  
  <input name="incluir" type="submit" id="incluir" value="Incluir" <?php echo ($db_opcao!=1?"disabled":"")?> onclick="return js_verizero()" >
  <input name="alterar" type="submit" id="alterar" value="Alterar" <?php echo ($db_opcao==1?"disabled":"")?> onclick="return js_verizero()" >
  
  <br />
  
  <fieldset>
    <legend>Promitentes adicionados</legend>
    <table class="form-container" border="1">
        <thead>
        <tr style="background-color: #e1e1e1">
            <th class="text-center">Numcgm</th>
            <th class="text-left">Promitente</th>
            <th class="text-left">Tipo do Promitente</th>
            <th class="text-left">Principal</th>
            <th class="text-center">Ações</th>
        </tr>
        </thead>
        <tbody>
            <?php
              if (!empty($j41_matric) || $j41_matric != null) { 
                $result = $clpromitente->sql_record($clpromitente->sql_query($j41_matric,"","promitente.*#tipopromitente.*#cgm.z01_nome#cgm.z01_numcgm", "j41_tipopro desc"));

                for($i=0; $i < $clpromitente->numrows; $i++){
                  db_fieldsmemory($result,$i);
            ?>
            <tr class="cores">
                <td class="numcgmpromitente text-center field-size2"><?=$j41_numcgm?></td>
                <td class="text-left field-size8"><?=$z01_nome?></td>
                <td class="descricaopromitente text-left"><?=$j164_descricao?></td>
                <td class="text-left"><?=($j41_tipopro == 't') ? 'SIM' : 'NÃO'?></td>
                <td class="text-center">
                    <input id="numcgm" name="numcgm" type="hidden" value="<?=$j41_numcgm?>">
                    <input type="button" href="#" title="Alterar proprietário."
                           onclick="js_trocaid(<?= $j41_numcgm ?>)" value="A"> |
                    <input name="excluir" type="submit" id="excluir" value="E" onclick="return js_confirmaExclusao('<?= $z01_numcgm ?>');" >
            </tr>
            <?php } } ?>
        </tbody>
    </table>
  </fieldset>


<script>
function js_confirmaExclusao(numcgm) {
  document.getElementById('cgmexclusao').value = numcgm;
  return window.confirm('Deseja remover esse promitente?');
}

function js_cgm(mostra) {
	if (mostra == true) {
		js_OpenJanelaIframe('CurrentWindow.corpo.iframe_promitente', 'func_nome', 'func_nome.php?testanome=true&funcao_js=parent.js_mostranumcgm1|z01_numcgm|z01_nome', 'Pesquisa', true);
	} else {
		if (document.form1.j41_numcgm.value != '') {
		js_OpenJanelaIframe('CurrentWindow.corpo.iframe_promitente', 'func_nome', 'func_nome.php?testanome=true&pesquisa_chave=' + document.form1.j41_numcgm.value + '&funcao_js=parent.js_mostranumcgm', 'Pesquisa', false);
		} else {
		document.form1.z01_nome.value = "";
		}
	}
}

function js_mostranumcgm(erro, chave) {
	document.form1.z01_nome.value = chave;
	if (erro == true) {
		document.form1.j41_numcgm.value = '';
		document.form1.j41_numcgm.focus();
	}
}

function js_mostranumcgm1(chave1, chave2) {
	document.form1.j41_numcgm.value = chave1;
	document.form1.z01_nome.value = chave2;
	func_nome.hide();
}

function selecionaTipoPromitente(){
  const inputCgm = document.getElementById('j41_numcgm');
  const numcgmpromitentes = document.querySelectorAll('.numcgmpromitente');
  const selectTipoProprietaro = document.getElementById('j164_tipopromitente');

  numcgmpromitentes.forEach(item => {
      const numCgm = item.innerText
      const tipopromitente = item.parentElement.querySelector('.descricaopromitente').innerText
      if(Number(numCgm) === Number(inputCgm.value)){
          selectTipoProprietaro.childNodes.forEach(option => {
              if(option.innerText === tipopromitente){
                  option.selected = "true";
              }
          })
      }
  })
}

selecionaTipoPromitente();
</script>