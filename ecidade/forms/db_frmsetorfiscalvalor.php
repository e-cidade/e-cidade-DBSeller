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

//MODULO: cadastro
require_once(modification("dbforms/db_classesgenericas.php"));

$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clsetorfiscalvalor->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("j90_descr");
$clrotulo->label("j31_descr");

if (isset($db_opcaoal)) {
    $db_opcao = 33;
    $db_botao = false;
} elseif (isset($opcao) && $opcao == "alterar") {
    $db_botao = true;
    $db_opcao = 2;
} elseif (isset($opcao) && $opcao == "excluir") {
    $db_opcao = 3;
    $db_botao = true;
} else {
    $db_opcao = 1;
    $db_botao = true;
    if (isset($novo) || isset($alterar) ||   isset($excluir) || (isset($incluir) && !$sqlerro)) {
        $j82_codigo = "";
        $j82_anousu = "";
        $j82_caract = "";
        $j31_descr = "";
        $j82_valorterreno = "";
    }
}
?>
<form name="form1" method="post" action="">
  <fieldset>
    <legend class="bold">Valores</legend>
    <center>
      <table border="0">
        <?php
        db_input('j82_codigo', 10, $Ij82_codigo, true, 'hidden', 3, "")
        ?>
        <tr>
          <td nowrap title="<?=@$Tj82_setorfiscal?>">
            <?php
            db_ancora(@$Lj82_setorfiscal, "js_pesquisaj82_setorfiscal(true);", 3);
            ?>
          </td>
          <td>
            <?php
            db_input(
                'j82_setorfiscal',
                10,
                $Ij82_setorfiscal,
                true,
                'text',
                3,
                " onchange='js_pesquisaj82_setorfiscal(false);'"
            )
            ?>
            <?php
            db_input('j90_descr', 40, $Ij90_descr, true, 'text', 3, '')
            ?>
          </td>
        </tr>
        <tr>
          <td nowrap title="<?php echo @$Tj82_anousu?>">
            <?php echo @$Lj82_anousu?>
          </td>
          <td>
            <?php
            $j82_anousu = db_getsession('DB_anousu');
            db_input('j82_anousu', 4, $Ij82_anousu, true, 'text', $db_opcao, "")
            ?>
          </td>
        </tr>
          <tr>
              <td nowrap title="<?php echo $Tj82_caract?>">
                  <?php db_ancora($Lj82_caract, "js_pesquisacaract(true);", $db_opcao); ?>
              </td>
              <td>
                  <?php
                    db_input(
                        'j82_caract',
                        4,
                        $Ij82_caract,
                        true,
                        'text',
                        $db_opcao,
                        "onChange=js_pesquisacaract(false);"
                    );
                    db_input('j31_descr', 40, $Ij31_descr, true, 'text', 3, "");
                    ?>
              </td>
          </tr>
        <tr>
          <td nowrap title="<?php echo @$Tj82_valorterreno?>">
            <?php echo @$Lj82_valorterreno?>
          </td>
          <td>
            <?php
            db_input('j82_valorterreno', 15, $Ij82_valorterreno, true, 'text', $db_opcao, "")
            ?>
          </td>
        </tr>
      </table>
  </fieldset>
  <p>
    <input name="<?php echo ($db_opcao==1?"incluir":($db_opcao==2||$db_opcao==22?"alterar":"excluir"))?>"
           type="submit" id="db_opcao"
           value="<?php echo ($db_opcao==1?"Incluir":($db_opcao==2||$db_opcao==22?"Alterar":"Excluir"))?>"
                  <?php echo ($db_botao==false?"disabled":"")?>  >
    <input name="novo" type="button" id="cancelar" value="Novo"
           onclick="js_cancelar();" <?php echo ($db_opcao==1||isset($db_opcaoal)?"style='visibility:hidden;'":"")?> >
  </p>
  <table>
    <tr>
      <td valign="top"  align="center">
        <?php
        $chavepri= array("j82_codigo"=>@$j82_codigo);
        $cliframe_alterar_excluir->chavepri = $chavepri;
        $cliframe_alterar_excluir->sql = $clsetorfiscalvalor->sql_query(
            null,
            "*",
            null,
            "j82_setorfiscal=$j82_setorfiscal"
        );
        $sCampos = "j82_codigo,j82_setorfiscal,j82_anousu,j82_caract,j31_descr,j82_valorterreno";
        $cliframe_alterar_excluir->campos = $sCampos;
        $cliframe_alterar_excluir->legenda = "ITENS LANÇADOS";
        $cliframe_alterar_excluir->iframe_height = "160";
        $cliframe_alterar_excluir->iframe_width = "700";
        $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);
        ?>
      </td>
    </tr>
  </table>
  </center>
</form>
<script>
  function js_cancelar(){
    var opcao = document.createElement("input");
    opcao.setAttribute("type","hidden");
    opcao.setAttribute("name","novo");
    opcao.setAttribute("value","true");
    document.form1.appendChild(opcao);
    document.form1.submit();
  }

  function js_pesquisaj82_setorfiscal(mostra) {
    if (mostra) {
      js_OpenJanelaIframe('CurrentWindow.corpo.iframe_setorfiscalvalor',
          'db_iframe_setorfiscal',
          'func_setorfiscal.php?funcao_js=parent.js_mostrasetorfiscal1|j90_codigo|j90_descr',
          'Pesquisa',true,'0','1','775','390');
    } else {
      if (document.form1.j82_setorfiscal.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_setorfiscalvalor',
            'db_iframe_setorfiscal',
            'func_setorfiscal.php?pesquisa_chave='+document.form1.j82_setorfiscal.value+
                                                 '&funcao_js=parent.js_mostrasetorfiscal',
            'Pesquisa',false);
      } else {
        document.form1.j90_descr.value = '';
      }
    }
  }

  function js_mostrasetorfiscal(chave,erro) {
    document.form1.j90_descr.value = chave;
    if (erro) {
      document.form1.j82_setorfiscal.focus();
      document.form1.j82_setorfiscal.value = '';
    }
  }

  function js_mostrasetorfiscal1(chave1,chave2) {
    document.form1.j82_setorfiscal.value = chave1;
    document.form1.j90_descr.value = chave2;
    db_iframe_setorfiscal.hide();
  }

  function js_pesquisacaract(mostra) {
      if (mostra) {
          js_OpenJanelaIframe('',
              'db_iframe_caracter',
              'func_caracter.php?funcao_js=parent.js_mostracaract1|j31_codigo|j31_descr',
              'Pesquisa',true);
      }else{
          js_OpenJanelaIframe('',
              'db_iframe_caracter',
              'func_caracter.php?pesquisa_chave='+document.form1.j82_caract.value+
                                                '&funcao_js=parent.js_mostracaract',
              'Pesquisa',false);
      }
  }

  function js_mostracaract(chave,erro) {
      document.form1.j31_descr.value = chave;
      if (erro) {
          document.form1.j82_caract.focus();
          document.form1.j82_caract.value = '';
      }
  }

  function js_mostracaract1(chave1,chave2) {
      document.form1.j82_caract.value = chave1;
      document.form1.j31_descr.value = chave2;
      db_iframe_caracter.hide();
  }

</script>