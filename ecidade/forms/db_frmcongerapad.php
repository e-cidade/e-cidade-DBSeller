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

if ($iTipo == "pad") {

    $periodo = array(
      "1"=> " 1 - Janeiro",
      "2"=> " 2 - Fevereiro",
      "3"=> " 3 - Março",
      "4"=> " 4 - Abril",
      "5"=> " 5 - Maio",
      "6"=> " 6 - Junho",
      "7"=> " 7 - Julho",
      "8"=> " 8 - Agosto",
      "9"=> " 9 - Setembro",
      "10"=>"10 - Outubro",
      "11"=>"11 - Novembro",
      "12"=>"12 - Dezembro" );

} else if ($iTipo == "mgs") {

    $periodo = array(
      "1"=> " 1 - Janeiro          ",
      "2"=> " 2 - Fevereiro        ",
      "3"=> " 3 - Março     (1 trim)",
      "4"=> " 4 - Abril            ",
      "5"=> " 5 - Maio             ",
      "6"=> " 6 - Junho     (2 trim)",
      "7"=> " 7 - Julho            ",
      "8"=> " 8 - Agosto    ",
      "9"=> " 9 - Setembro  (3 trim)       ",
      "10"=>"10 - Outubro   ",
      "11"=>"11 - Novembro         ",
      "12"=>"12 - Dezembro  (4 trim)");
}
?>
<center>
  <form name="form1" method="post" action="">
    <table>
      <tr>
        <td>
          <fieldset>
            <legend><b>Gerar SIAPC/PAD</b></legend>
            <table style='empty-cells: show;'>
              <tr>
                <td colspan="1">
                  <b>Arquivos do:</b>
                    <?php
                    global $periodopad;
                    $periodopad = date("m",db_getsession("DB_datausu"))-1;
                    if (db_getsession("DB_anousu") != date("Y",db_getsession("DB_datausu"))) {
                        $periodopad = 12;
                    } else {

                        if ($periodopad == 0) {
                            $periodopad = 1;
                        }
                    }

                    db_select("periodopad",$periodo,true,2);
                    ?>
                </td>
                  <td colspan="4">
                      &nbsp;<label for="rateio_complemento"><b>Ajuste pelo Complemento da Fonte:</b></label>

                      <select id="rateio_complemento">
                          <option value="1">Sem Ajuste</option>
                          <option value="2">Rateio da Previsão Inicial</option>
                          <option value=3">Remanejamento de Valores</option>
                      </select>
                  </td>
              </tr>
              <tr>
                <td valign="top" rowspan="2">
                  <table border="0" style='border-right: 2px groove white'>
                    <tr>
                      <td colspan='4' style='border-bottom: 2px groove white;text-align: center'>
                        <b>ARQUIVOS PRINCIPAIS</b>
                      </td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="padEmpenho" id="padEmpenho"></td>
                      <td><label for="padEmpenho">Empenhos</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="liquidac" id="liquidac"></td>
                      <td><label for="liquidac">Liquidação</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="pagament" id="pagament"></td>
                      <td nowrap><label for="pagament">Pagamento</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="bal_rec" id="bal_rec"></td>
                      <td nowrap><label for="bal_rec">Balancete de Receita</label></td>
                    </tr>
                    <tr>
                      <td nowrap><input type=checkbox name="receita" id="receita"></td>
                      <td><label for="receita">Receita</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="bal_desp" id="bal_desp"></td>
                      <td nowrap><label for="bal_desp">Balancete de Despesa</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="decreto" id="decreto"></td>
                      <td><label for="decreto">Decretos</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="bal_ver" id="bal_ver"></td>
                      <td><label for="bal_ver">Balancete de Verificação</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="bver_enc" id="bver_enc"></td>
                      <td nowrap><label for="bver_enc">Balancete de Verificação (Com Encerramento)</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="rd_extra" id="rd_extra"></td>
                      <td nowrap><label for="rd_extra">Receitas e Despesas Extra-Orçamentária</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="tce_4111" id="tce_4111"></td>
                      <td nowrap><label for="tce_4111">4111 - Livro Diário Geral</label></td>
                    </tr>
                    <tr>
                      <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="2">&nbsp;</td>
                    </tr>
                    <tr>
                      <td colspan="2">&nbsp;</td>
                    </tr>
                  </table>
                </td>
                <td valign='top' rowspan="2" style='border-right:2px groove white'>
                  <table border="0">
                    <tr>
                      <td colspan='2' style='border-bottom: 2px groove white;text-align: center'>
                        <b>ARQUIVOS AUXILIARES</b>
                      </td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="orgao" id="orgao"></td>
                      <td><label for="orgao">Orgao</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="uniorcam" id="uniorcam"></td>
                      <td><label for="uniorcam">Unidades</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="funcao" id="funcao"></td>
                      <td><label for="funcao">Funções</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="subfunc" id="subfunc"></td>
                      <td><label for="subfunc">Sub-funções</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="programa" id="programa"></td>
                      <td><label for="programa">Programas</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="subprog" id="subprog"></td>
                      <td><label for="subprog">SubProgramas</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="projativ" id="projativ"></td>
                      <td><label for="projativ">Projetos/Atividades</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="rubrica" id="rubrica"></td>
                      <td><label for="rubrica">Rubricas</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="recurso" id="recurso"></td>
                      <td><label for="recurso">Recursos</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="credor" id="credor"></td>
                      <td><label for="credor">Credor</label></td>
                    </tr>
                  </table>
                </td>
                <td valign=top height=50%  >
                  <table border="0">
                    <tr>
                      <td colspan='2' style='border-bottom: 2px groove white;text-align: center'>
                        <b>DO EXERCÍCIO</b>
                      </td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="cta_disp" id="cta_disp"></td>
                      <td><label for="cta_disp">Disponibilidades</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="cta_oper" id="cta_oper"></td>
                      <td><label for="cta_oper">Operações</label></td>
                    </tr>
                  </table>
                </td>
                <td valign=top height=50%>
                  <table border="0" style="border-left: 2px groove white">
                    <tr>
                      <td colspan='2' style='border-bottom: 2px groove white;text-align: center'>
                        <b>DO EXERCICIO ANTERIOR</b>
                      </td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="brec_ant" id="brec_ant"></td>
                      <td><label for="brec_ant">Balancete Receita</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="rec_ant" id="rec_ant"></td>
                      <td><label for="rec_ant">Receita</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="brub_ant" id="brub_ant"></td>
                      <td><label for="brub_ant">Balancete por Rubrica</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="bver_ant" id="bver_ant"></td>
                      <td><label for="bver_ant">Balancete de Verificação</label></td>
                    </tr>
                    <tr>
                      <td><input type=checkbox name="bvmovant" id="bvmovant"></td>
                      <td><label for="bvmovant">BV - Bimestrais</label></td>
                    </tr>
                  </table>
                </td>
              </tr>

<!--
              <tr>
                <td colspan="2" style='border-top: 2px groove white;height:30%'>
                  <iframe name="iframe_processapad" src="con4_processapad.php" scrolling="auto"></iframe>
                </td>
              </tr>
-->
            </table>
          </fieldset>
        </td>
      </tr>
      <tr>
        <td style="text-align: center">
          <input name="todos" type="button" value="Todos" onclick="js_marcatodos();" >
          <input name="limpa" type="button" value="Limpa" onclick="js_limpatodos();" >
          <input name="processar" id="processar" type="button" value="Processar" onclick="js_seleciona('<?=$iTipo;?>');">
        </td>
      </tr>
    </table>


<table width="800">

    <tr>
      <td colspan="2" style='border-top: 2px groove white; width:600px;'>
         <iframe id = "iframe_processapad" name="iframe_processapad" src="con4_processapad.php" scrolling="auto" height="400" width="800"></iframe>
      </td>
    </tr>
</table>





  </form>
</center>
