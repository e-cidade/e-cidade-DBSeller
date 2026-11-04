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
use \ECidade\Financeiro\Orcamento\Repository\RecursoRepository;

//MODULO: empenho
$clempempenho->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("z01_nome");
$clrotulo->label("nome");
$clrotulo->label("e60_codemp");
$clrotulo->label("pc50_descr");
$clrotulo->label("e60_codcom");
$clrotulo->label("e63_codhist");
$clrotulo->label("e44_tipo");
$clrotulo->label("c58_descr");
$clrotulo->label("e60_tipol");

$outrosDados = null;
if (!empty($outros_dados)) {
    $outrosDados = json_decode($outros_dados);
}

$tiposCompraJustificaveis = ['7','8', '9', '13', '14'];
$dispensaElementosAtoJuridico = [];
$e56_codele = "";
if(isRioDeJaneiro()){

    $daoOrcElemento = new cl_orcelemento;
    $anoUsu = db_getsession("DB_anousu");
    $where = "
      o56_anousu = $anoUsu and (
        substr(o56_elemento,3,1) in ('1','2','5','6') OR
        substr(o56_elemento,6,2) in (
          '01','04','03','06','07','08','10','11','12','13','14','15','16','17','18',
          '19','20','21','22','23','24','25','26','27','28','29','41','42','43','45','46','47','48','49','53','54',
          '55','56','57','58','59','65','70','71','72','73','74','75','76','77','81','91','93','94','95','96','97','98'
        )
      )
    ";
    $sqlElementosDispensados = $daoOrcElemento->sql_query_file(null,null,'o56_codele',null,$where);
    $rsElementosDispensados = db_query($sqlElementosDispensados);
    for($i = 0;$i < pg_num_rows($rsElementosDispensados); $i++){
      $dispensaElementosAtoJuridico[] = db_utils::fieldsMemory($rsElementosDispensados,$i)->o56_codele;
    }
}
?>
<form name="form1" method="post" action="">
    <table border="0">
        <tr>
            <td nowrap title="<?= @$Te60_codemp ?>">
                <?= @$Le60_codemp ?>
            </td>
            <td>
                <?php
                db_input('e60_numemp', 10, '', true, 'hidden', 3);
                db_input('e60_codemp', 10, $Ie60_codemp, true, 'text', 3);
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= @$Te60_numcgm ?>">
                <?= $Le60_numcgm ?>
            </td>
            <td>
                <?php
                db_input('e60_numcgm', 10, $Ie60_numcgm, true, 'text', 3);
                db_input('z01_nome', 40, $Iz01_nome, true, 'text', 3, '');
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= @$Te60_codcom ?>">
                <?= $Le60_codcom ?>
            </td>
            <td>
                <?php

                $dao = new cl_pctipocompra();
                $campos = "pc50_codcom as e60_codcom, pc50_descr, l44_obrigalicitacao";
                $sql = $dao->sql_query(null, $campos, "pc50_descr", "pc50_ativo is true");
                $result = db_query($sql);
                $tiposCompra = db_utils::getCollectionByRecord($result);
                $aTipoCompra = [];
                foreach ($tiposCompra as $item) {
                    $aTipoCompra[$item->e60_codcom] = "{$item->e60_codcom} - {$item->pc50_descr}";
                }

                db_select('e60_codcom', $aTipoCompra, true, 1, "onchange='pesquisarTipoLicitacao()'");
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= @$Te60_tipol ?>">
                <?= @$Le60_tipol ?>
            </td>
            <td>
                <input type='hidden' id='tipoAtribuidoAnterior' value='<?=$e60_tipol?>' >
                <?php

                db_select('e60_tipol', [], true, 1);
                ?>
            </td>
        </tr>
        <tr>
            <td><strong>Número da Licitação:</strong></td>
            <td><?php
                $bloqueia = '';
                if (isset($l44_obrigalicitacao) && $l44_obrigalicitacao == 'f') {
                    $bloqueia = "class='readonly' readonly";
                }

                $numeroLicitacao = '';
                $anoLicitacao = '';
                if (!empty($e60_numerol)) {
                    $dadosLicitacao = explode('/', $e60_numerol);
                    $numeroLicitacao = $dadosLicitacao[0];
                    $anoLicitacao = !empty($dadosLicitacao[1]) ? $dadosLicitacao[1] : '';
                }
                ?>

                <input type="text" id="numeroLicitacao" name="numeroLicitacao" <?= $bloqueia ?> style="width: 150px"
                       oninput="js_ValidaCampos(this, 1, 'Número da Licitação', 'f', 'f', event)" maxlength="20"
                       value="<?=$numeroLicitacao?>">
                &nbsp;&nbsp;/&nbsp;&nbsp;
                <input type="text" id="anoLicitacao" name="anoLicitacao" <?= $bloqueia ?> style="width: 50px"
                       oninput="js_ValidaCampos(this, 1, 'Ano da Licitação', 'f', 'f', event)" maxlength="4"
                       value="<?=$anoLicitacao?>">
            </td>
        </tr>

        <tr>
            <td><label class="bold" for="licitacaoCompartilhada">Licitação Compartilhada:</label></td>
            <td>
                <?php
                $licitacao_compartilhada = 'X';
                if (!empty($outrosDados) && !empty($outrosDados->licitacao_compartilhada)) {
                    $licitacao_compartilhada = $outrosDados->licitacao_compartilhada;
                }

                $tipos = [
                    'X' => 'Não se Aplica',
                    'N' => 'Não',
                    'S' => 'Sim',
                ];

                db_select('licitacao_compartilhada', $tipos, false, 1);
                ?>

            </td>
        </tr>
        <tr style="display: none" id="linhaCNPJ">
            <td><label class="bold" for="cnpjGerenciador">CNPJ do órgão gerenciador:</label></td>
            <td>
                <?php
                $valor = '';
                if (!empty($outrosDados) && !empty($outrosDados->cnpj_gerenciador)) {
                    $valor = $outrosDados->cnpj_gerenciador;
                }

                ?>
                <input type="text" maxlength="14" id="cnpjGerenciador" name="cnpj_gerenciador" value="<?=$valor?>">
            </td>
        </tr>

        <tr>
            <td nowrap title="<?= @$Te60_codtipo ?>">
                <?= $Le60_codtipo ?>
            </td>
            <td>
                <?php
                $result = $clemptipo->sql_record($clemptipo->sql_query_file(null, "e41_codtipo,e41_descr"));
                db_selectrecord("e60_codtipo", $result, true, $db_opcao);

                ?>
            </td>
        </tr>

        <?php if (isParaiba() && substr($o56_elemento, 0, 7) === '3449051'): ?>
        <tr>
            <td nowrap title="Código do Geo Obra">
                <label for="geo_obra" class="bold">GEO Obras:</label>
            </td>
            <td>
                <?php
                $geo_obra = '';
                if (!empty($outros_dados)) {
                    $outrosDados = json_decode($outros_dados);
                    if (!empty($outrosDados->geo_obra)) {
                        $geo_obra = $outrosDados->geo_obra;
                    }
                }
                ?>
                <input type="text" class="field-size5" id="geo_obra" name="geo_obra" value="<?=$geo_obra?>"
                       oninput="js_ValidaCampos(this, 1, 'GEO Obras', 'f', 'f', event);" >
            </td>
        </tr>
        <?php endif; ?>

        <tr>
            <td nowrap title="<?= @$Te63_codhist ?>">
                <?= $Le63_codhist ?>
            </td>
            <td>
                <?php
                $result = $clemphist->sql_record($clemphist->sql_query_file(null, "e40_codhist,e40_descr"));
                db_selectrecord("e63_codhist", $result, true, 1, "", "", "", "Nenhum");
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= @$Te44_tipo ?>">
                <?= $Le44_tipo ?>
            </td>
            <td>
                <?php

                $sql = $clempprestatip->sql_query_file(null, "e44_tipo as tipo,e44_descr,e44_obriga", "e44_obriga ");
                $result = $clempprestatip->sql_record($sql);
                $numrows = $clempprestatip->numrows;

                $arr = array();
                for ($i = 0; $i < $numrows; $i++) {
                    db_fieldsmemory($result, $i);
                    if ($e44_obriga == 0 && empty($e44_tipo)) {
                        $e44_tipo = $tipo;
                    }
                    $arr[$tipo] = $e44_descr;
                }

                if ( isset($e60_numemp) && !empty($e60_numemp) ) {

                    $e44_tipo = 1;
                    $oDaoemppresta = new cl_emppresta;
                    $sqlTipo = $oDaoemppresta->sql_query_file(null, "e45_tipo", "e45_data", "e45_numemp = $e60_numemp");
                    $rs = $oDaoemppresta->sql_record($sqlTipo);

                    if ($oDaoemppresta->numrows > 0) {
                      $e44_tipo = db_utils::fieldsMemory($rs, 0)->e45_tipo;
                    }
                }
                db_select("e44_tipo", $arr, true, 3);

                ?>
            </td>
        </tr>

        <?php
        if (isset($e60_numemp)) {

            $sql = "select pagordem.* from pagordem inner join pagordemdesconto on e34_codord = e50_codord
	  													where e50_numemp = $e60_numemp";
            //die($sql);
            $result = $clpagordem->sql_record($sql);
            $ldesconto = false;
            if ($clpagordem->numrows > 0) {
                $ldesconto = true;
            }
        }
        if (isset($e60_vlrliq) && $e60_vlrliq == 0 && !$ldesconto && $e60_anousu >= db_getsession("DB_anousu")) {
            ?>
            <tr>
                <td nowrap title="Desdobramentos">
                    <b><?= "Desdobramento:" ?></b>
                </td>
                <td>
                    <?php
                        $result = $clempempaut->sql_record($clempempaut->sql_query(null, "e61_autori", "", "e61_numemp = $e60_numemp"));
                    if ($clempempaut->numrows > 0) {
                        $oResult = db_utils::fieldsMemory($result, 0);
                        $e54_autori = $oResult->e61_autori;
                        $anoUsu = db_getsession("DB_anousu");
                        $sWhere = "e56_autori = " . $e54_autori . " and e56_anousu = " . $anoUsu;
                        $result = $clempautidot->sql_record($clempautidot->sql_query_dotacao(null, "e56_coddot", null, $sWhere));

                        if ($clempautidot->numrows > 0) {
                            $oResult = db_utils::fieldsMemory($result, 0);
                            $result = $clorcdotacao->sql_record($clorcdotacao->sql_query($anoUsu, $oResult->e56_coddot, "o56_elemento,o56_codele"));
                            if ($clorcdotacao->numrows > 0) {
                                $oResult = db_utils::fieldsMemory($result, 0);
                                $oResult->estrutural = criaContaMae($oResult->o56_elemento . "00");
                                $sWhere = "o56_elemento like '$oResult->estrutural%' and o56_codele <> $oResult->o56_codele and o56_anousu = $anoUsu";
                                $sSql = "select distinct o56_codele,o56_elemento,o56_descr
											  from empempitem
											        inner join pcmater on pcmater.pc01_codmater    = empempitem.e62_item
											        inner join pcmaterele on pcmater.pc01_codmater = pcmaterele.pc07_codmater
											        left join orcelemento on orcelemento.o56_codele = pcmaterele.pc07_codele
											                              and orcelemento.o56_anousu = $anoUsu
											    where o56_elemento like '$oResult->estrutural%'
											    and e62_numemp = $e60_numemp and o56_anousu = $anoUsu";
                                $result = $clorcelemento->sql_record($sSql);

                                $oResult = db_utils::getCollectionByRecord($result);

                                $numrows = $clorcelemento->numrows;
                                $aEle = array();

                                foreach ($oResult as $oRow) {
                                    $aEle[$oRow->o56_codele] = $oRow->o56_descr;
                                }
                                //die($clempautitem->sql_query_autoriza (null,null,"e55_codele",null,"e55_autori = $e54_autori"));
                                $result = $clempelemento->sql_record($clempelemento->sql_query_file($e60_numemp, null, "e64_codele"));
                                if ($clempelemento->numrows > 0) {
                                    $oResult = db_utils::fieldsMemory($result, 0);
                                }

                                $e56_codele = $oResult->e64_codele;

                                $e64_codele = $e56_codele;
                                db_input('e64_codele', 10, 0, true, 'hidden', 3);
                                db_select("e56_codele", $aEle, true, 1);
                            }
                        }
                    } else {
                        $aEle = array();
                        $e56_codele = "";
                        db_select("e56_codele", $aEle, true, 1);
                    }
                    ?>
                </td>
            </tr>
            <?php
        } else {
            if (isset($e60_vlrliq) && $e60_vlrliq != 0) {
                $mensagem = "Você não pode alterar o desdobramento deste empenho porque este já possui valor liquidado. Se realmente for necessária a alteração, anule todas as liquidações";
            } else if (isset($ldesconto) && $ldesconto) {
                $mensagem = "Este empenho teve uma operação de desconto e isto inviabiliza a substituição do desdobramento.";
            }

        }
        ?>

        <tr id="trFinalidadeFundeb">
            <td><b>Finalidade:</b></td>
            <td>
                <?php
                $oDaoFinalidadeFundeb = db_utils::getDao('finalidadepagamentofundeb');
                $sSqlFinalidadeFundeb = $oDaoFinalidadeFundeb->sql_query_file(null, "e151_codigo, e151_descricao", "e151_codigo");
                $rsBuscaFinalidadeFundeb = $oDaoFinalidadeFundeb->sql_record($sSqlFinalidadeFundeb);
                db_selectrecord('e151_codigo', $rsBuscaFinalidadeFundeb, true, 1);
                ?>
            </td>
        </tr>

        <tr>
            <td class="bold">Complemento:</td>
            <td>
                <?php
                $complementosDisponiveis = [];
                $dataLimite = date('Y-m-d', db_getsession('DB_datausu'));
                if (!empty($e60_numemp)) {
                    $empenho = EmpenhoFinanceiroRepository::getEmpenhoFinanceiroPorNumero($e60_numemp);
                    $recurso = $empenho->getDotacao()->getDadosRecurso();
                    $fonte = $recurso->getFonteRecurso($empenho->getAno());

                    $complementosEncontrados = RecursoRepository::getComplementosByGestao(
                        $fonte->gestao,
                        $recurso->getRecurso(),
                        $fonte->exercicio,
                        $dataLimite
                    );
                    $complementosDisponiveis = [];
                    foreach ($complementosEncontrados as $complemento) {
                        $complementosDisponiveis[$complemento->codigo] = $complemento->descricao;
                    }
                    $registro = \ECidade\Financeiro\Orcamento\Recurso\Origem::getEmpenho($e60_numemp, db_getsession('DB_anousu'));
                    if (!empty($registro)) {
                        $complemento = $registro->o206_complementorecurso;
                    }
                }
                db_select('complemento', $complementosDisponiveis, true, 1);
                ?>
            </td>
        </tr>

        <?php if(isRioDeJaneiro() && !empty($o56_codele) && !in_array($o56_codele,$dispensaElementosAtoJuridico)) { ?>
            <tr>
              <td class = 'bold'>Ato jurídico:</td>
              <td>
                <?php
                  $daoEmpTipoAtoJuridoco = new cl_tipoatojuridico;
                  $sqlAtoJuridico = $daoEmpTipoAtoJuridoco->sql_query_file(null, "*",  "e165_sequencial", "e165_ativo = true");
                  $rsAtoJuridico = db_query($sqlAtoJuridico);
//                  $tiposAtoJuridico = [];
//                  $tiposAtoJuridico = ['0 - Ausência de Ato Jurdico'];

                  for($i=0;$i<pg_num_rows($rsAtoJuridico);$i++){
                    $tipoAtoJuridico =  db_utils::fieldsMemory($rsAtoJuridico,$i);
                    if($tipoAtoJuridico->e165_ativo){
                      $codigoAtoJuridico = $tipoAtoJuridico->e165_sequencial;
                      $descricaoAtoJuridico = $tipoAtoJuridico->e165_sequencial.' - ';
                      $descricaoAtoJuridico .= $tipoAtoJuridico->e165_descricao;
                      $tiposAtoJuridico[$codigoAtoJuridico] = $descricaoAtoJuridico;
                    }
                  }

                  db_select('tipoatojuridico', $tiposAtoJuridico, true, 1,"onchange='js_abreFechaBlocoJustificativa()'");
                ?>
              </td>
            </tr>

            <tr id="justificativaatojuridico_bloco" style = "display:none">
              <td class = 'bold'>Justificativa Ausência de Ato Jurídico: </td>
              <td>
                <?php
                  $daoJustificativaAtoJuridico = new \cl_justificativaatojuridico;
                  $sqlJustificativaAtoJuridico = $daoJustificativaAtoJuridico->sql_query_file(null, "*", 'e173_sequencial');
                  $rsJustificativaAtoJuridico =  db_query($sqlJustificativaAtoJuridico);
                  $justificativasAtoJuridico = [];
                  for($i=0;$i<pg_num_rows($rsJustificativaAtoJuridico);$i++){
                    $justificativaAtoJuridico =  db_utils::fieldsMemory($rsJustificativaAtoJuridico,$i);
                    if($justificativaAtoJuridico->e173_ativo){
                      $codigoJustificativa = $justificativaAtoJuridico->e173_sequencial;
                      $descricaoJustificativa = $justificativaAtoJuridico->e173_sequencial.' - ';
                      $descricaoJustificativa .= $justificativaAtoJuridico->e173_descricao;
                      $justificativasAtoJuridico[$codigoJustificativa] = $descricaoJustificativa;
                    }
                  }

                  db_select('justificativaatojuridico', $justificativasAtoJuridico, true, 1);

                ?>
              </td>
            </tr>

            <tr id="justificativainstrumentoprevio_bloco" style = "display:none">
              <td class = 'bold'>Justificativa Ausência de Instrumento Prévio: </td>
              <td>
                <?php
                  $daoJustificativaInstrumentoPrevio = new \cl_justificativainstrumentoprevio;
                  $sqlJustificativaInstrumentoPrevio = $daoJustificativaInstrumentoPrevio->sql_query_file(null, "*", 'e174_sequencial');
                  $rsJustificativaInstrumentoPrevio =  db_query($sqlJustificativaInstrumentoPrevio);
                  $justificativasInstrumentoPrevio = [];
                  for($i=0;$i<pg_num_rows($rsJustificativaInstrumentoPrevio);$i++){
                    $justificativaInstrumentoPrevio =  db_utils::fieldsMemory($rsJustificativaInstrumentoPrevio,$i);
                    if($justificativaInstrumentoPrevio->e174_ativo){
                      $codigoJustificativa = $justificativaInstrumentoPrevio->e174_sequencial;
                      $descricaoJustificativa = $justificativaInstrumentoPrevio->e174_sequencial.' - ';
                      $descricaoJustificativa .= $justificativaInstrumentoPrevio->e174_descricao;
                      $justificativasInstrumentoPrevio[$codigoJustificativa] = $descricaoJustificativa;
                    }
                  }
                  db_select('justificativainstrumentoprevio', $justificativasInstrumentoPrevio, true, 1);
                ?>
              </td>
            </tr>

          <?php }?>

        <!-- indicativo de aquisição de produção rural -->
        <tr id="trAqProd" style="display: none;">
            <td><b>Tipo de Aquisicão: </b></td>
            <td>
                <select name="indAqProd" id="indAqProd"></select>
            </td>
        </tr>

        <tr>
            <td nowrap title="<?= @$Te60_destin ?>">
                <?= @$Le60_destin ?>
            </td>
            <td>
                <?php
                db_input('e60_destin', 40, $Ie60_destin, true, 'text', $db_opcao, "")
                ?>
            </td>
        </tr>

        <tr>
            <td nowrap title="<?= @$Te60_resumo ?>" colspan="2">
                <fieldset>
                    <legend><b><?= @$Le60_resumo ?></b></legend>
                    <?php
                    db_textarea('e60_resumo', 8, 90, $Ie60_resumo, true, 'text', $db_opcao, "")
                    ?>
                </fieldset>
            </td>
        </tr>
        <?php
        $anousu = db_getsession("DB_anousu");

        if ($anousu > 2007) {
            ?>
            <tr>
                <td nowrap title="<?= @$Te60_concarpeculiar ?>"><?php
                    db_ancora(@$Le60_concarpeculiar, "js_pesquisae60_concarpeculiar(true);", $db_opcao);
                    ?></td>
                <td>
                    <?php
                    db_input("e60_concarpeculiar", 10, $Ie60_concarpeculiar, true, "text", $db_opcao, "onChange='js_pesquisae60_concarpeculiar(false);'");
                    db_input("c58_descr", 50, 0, true, "text", 3);
                    ?>
                </td>
            </tr>
            <?php
        } else {
            $e60_concarpeculiar = 0;
            db_input("e60_concarpeculiar", 10, 0, true, "hidden", 3, "");

        }
        if (isset($e60_numemp) && isset($e30_notaliquidacao) && $e30_notaliquidacao != '') {
            $rsNotaLiquidacao = $oDaoEmpenhoNl->sql_record(
                $oDaoEmpenhoNl->sql_query_file(null, "e68_numemp", "", "e68_numemp = {$e60_numemp}"));
            if ($oDaoEmpenhoNl->numrows == 0) {
                ?>
                <tr>
                    <td nowrap title="Nota de liquidação">
                        <b>Nota de liquidação:</b>
                    </td>
                    <td>
                        <?php
                        $aNota = array("s" => "Sim", "n" => "NÃO");
                        db_select("e68_numemp", $aNota, true, 1);
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
        <!--[Extensao OrdenadorDespesa] inclusao_ordenador-->

    </table>

    <input name="alterar" type="submit" id="db_opcao" value="Alterar" <?= ($db_botao == false ? "disabled" : "") ?>
           onclick='return js_valida()';>

    <input type="button" id="btnLancarCotasMensais" value="Manutenção de Cotas Mensais"
           onclick="manutencaoCotasMensais()"/>

    <input name="pesquisar" type="button" id="pesquisar" value="Pesquisar empenhos" onclick="js_pesquisa();">
</form>

<script>
    const UF = '<?=getEstadoInstituicao();?>'
    var isPB = UF === 'PB';
    var isRJ = UF === 'RJ';
    var isJustificavel = '<?php echo !in_array($o56_codele,$dispensaElementosAtoJuridico) ? 'true': 'false';?>';

    let linhaCNPJ = document.getElementById('linhaCNPJ');
    let inputLicitacaoCompartilhada = document.getElementById('licitacao_compartilhada');
    let inputCnpjGerenciador = document.getElementById('cnpjGerenciador');
    new DBInputCNPJ(inputCnpjGerenciador);

    inputLicitacaoCompartilhada.addEventListener('change', () => {
        linhaCNPJ.style.display = 'none';
        if (inputLicitacaoCompartilhada.value === 'S') {
            linhaCNPJ.style.display = 'table-row';
        }
    });
    inputLicitacaoCompartilhada.dispatchEvent(new Event('change'));

    function manutencaoCotasMensais() {

        oViewCotasMensais = new ViewCotasMensais('oViewCotasMensais', $F('e60_numemp'));
        oViewCotasMensais.setReadOnly(false);
        oViewCotasMensais.abrirJanela();
    }


    function js_pesquisae60_concarpeculiar(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_concarpeculiar', 'func_concarpeculiar.php?funcao_js=parent.js_mostraconcarpeculiar1|c58_sequencial|c58_descr', 'Pesquisa', true);
        } else {
            if (document.form1.e60_concarpeculiar.value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_concarpeculiar', 'func_concarpeculiar.php?pesquisa_chave=' + document.form1.e60_concarpeculiar.value + '&funcao_js=parent.js_mostraconcarpeculiar', 'Pesquisa', false);
            } else {
                document.form1.c58_descr.value = '';
            }
        }
    }

    function js_mostraconcarpeculiar(chave, erro) {
        document.form1.c58_descr.value = chave;
        if (erro == true) {
            document.form1.e60_concarpeculiar.focus();
            document.form1.e60_concarpeculiar.value = '';
        }
    }

    function js_mostraconcarpeculiar1(chave1, chave2) {
        document.form1.e60_concarpeculiar.value = chave1;
        document.form1.c58_descr.value = chave2;
        db_iframe_concarpeculiar.hide();
    }

    function js_pesquisa() {
        js_OpenJanelaIframe('', 'db_iframe_empempenho', 'func_empempenho.php?funcao_js=parent.js_preenchepesquisa|e60_numemp', 'Pesquisa', true);
    }

    function js_preenchepesquisa(chave) {
        db_iframe_empempenho.hide();
        <?php
        echo " location.href = '" . basename($GLOBALS["HTTP_SERVER_VARS"]["PHP_SELF"]) . "?chavepesquisa='+chave";
        ?>
    }


    /**
     * Ajustes no layout
     */
    $("e60_codtipo").style.width = "15%";
    $("e63_codhist").style.width = "15%";
    $("e60_codtipodescr").style.width = "84%";
    $("e63_codhistdescr").style.width = "84%";
    $("e44_tipo").style.width = "100%";
    if ($("e56_codele")) {
        $("e56_codele").style.width = "100%";
    }
    $("e60_destin").style.width = "100%";
    $("e60_resumo").style.width = "100%";


    function js_verificaFinalidadeEmpenho() {

        js_divCarregando("Aguarde, verificando recurso da dotação...", "msgBox");
        var oParam = new Object();
        oParam.exec = "getFinalidadePagamentoFundebEmpenho";
        oParam.iSequencialEmpenho = $F('e60_numemp');

        new Ajax.Request('emp4_empenhofinanceiro004.RPC.php',
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParam),
                onComplete: function (oAjax) {

                    js_removeObj("msgBox");
                    var oRetorno = JSON.parse(oAjax.responseText);

                    $('trFinalidadeFundeb').style.display = '';
                    $("e151_codigo").style.width = "15%";
                    $("e151_codigodescr").style.width = "84%";

                    if (oRetorno.oFinalidadePagamentoFundeb) {

                        $('e151_codigo').value = oRetorno.oFinalidadePagamentoFundeb.e151_codigo;
                        js_ProcCod_e151_codigo('e151_codigo', 'e151_codigodescr');
                    }
                }
            });
    }

    js_verificaFinalidadeEmpenho();


    function js_valida() {
        if (!validaNumeroLicitacao()) {
            return false
        }

        if (!validaTipoServico()) {
            return false;
        }

        let geoObra = document.getElementById('geo_obra');
        if (isPB && geoObra != null && geoObra.value === '') {
            alert(`Para empenhos de Obras, você deve informar o código do GEO Obras.`);
            return false
        }

        let cnpj = inputCnpjGerenciador.getValue().replace(/[^0-9]/gi, "");
        if (inputLicitacaoCompartilhada.value === 'S' && empty(cnpj)) {
            alert(`Quando Licitação Compartilhada for "SIM", você deve informar o CNPJ do órgão gerenciador`);
            return false;
        }

        return true;
    }

    function validaNumeroLicitacao() {
        let numeroLicitacao = document.getElementById('numeroLicitacao');
        let anoLicitacao = document.getElementById('anoLicitacao');

        if ((!numeroLicitacao.hasAttribute('readonly') && numeroLicitacao.value == '') ||
            (!anoLicitacao.hasAttribute('readonly') && anoLicitacao.value == '')) {
            alert('Você deve informar o número e ano da licitação.');
            return false;
        }

        if (!anoLicitacao.hasAttribute('readonly') && anoLicitacao.value.length < 4) {
            alert('O ano deve possuir 4 digitos.');
            return false;
        }

        return true;
    }

    /* =============================
    * Validacoes dos indicativo de
    * aquisicão de producao rural
    * (EFD-REINF)
    * =============================
    */
    const indAqProd  = document.querySelector("#indAqProd");
    const trAqProd   = document.querySelector('#trAqProd');

    // entrypoint
    iniciarCamposReinf();

    /**
    * Ajax requests
    */
    async function iniciarCamposReinf() {
        let produtorrural = await checkProdutorrual();

        if (produtorrural && produtorrural == 't') {
            getTipoaAquisicaoProducaoRuralLabels();
        }
    }

    async function checkProdutorrual() {
        const RPC = 'emp4_tipoaquisicaoproducaorural.RPC.php';
        let cgm = $F('e60_numcgm');
        let params = JSON.stringify({exec: 'produtorrural', cgm: cgm});
        let formData = new FormData;
        let response = '';

        try {
            formData.append('json', params);
            response = await fetch(RPC, {method: 'post', body: formData});
            data = await response.json();

            if (!data.erro) {
               return data.produtorrural;
            }
            return false;
        } catch (error) {
            console.error(error);
        }
    }

    async function getTipoaAquisicaoProducaoRuralLabels() {
        const RPC    = 'emp4_tipoaquisicaoproducaorural.RPC.php';
        let cgm      = $F('e60_numcgm');
        let params   = JSON.stringify({exec: 'getLabels', cgm: cgm});
        let formData = new FormData();
        let response = '';
        let labels   = [];
        let option   = '';

        js_divCarregando('Buscando dados do indicativo de aquisição...', 'aqProdLoad');

        try {
            formData.append('json', params);
            response = await fetch(RPC, {method: 'post', body: formData});
            data = await response.json();

            if (!data.erro && data.labels) {
                labels = data.labels;
                trAqProd.style.display = 'table-row';

                // option não se aplica
                option = document.createElement('option');
                option.value = '';
                option.innerHTML = '0 - NÃO SE APLICA';
                indAqProd.appendChild(option);

                labels.forEach(item => {
                    option = document.createElement('option');
                    option.value = item.value;
                    option.innerHTML = item.value + ' - ' + item.name;
                    indAqProd.appendChild(option);
                });
            }
            await getTipoaAquisicaoProducaoRural();
        } catch (error) {
            console.error(error);
        }
        js_removeObj('aqProdLoad');
    }

    async function getTipoaAquisicaoProducaoRural() {
        const RPC = 'emp4_tipoaquisicaoproducaorural.RPC.php';
        let e60_numemp = $F('e60_numemp');
        let params     = JSON.stringify({exec: 'getByEmpenho', numemp: e60_numemp});
        let formData   = new FormData();
        let response   = '';

        try {
            formData.append('json', params);
            response = await fetch(RPC, {method: 'post', body: formData});
            data = await response.json();

            if (!data.erro && data.tipoaquisicaoproducaorural) {
                indAqProd.value = data.tipoaquisicaoproducaorural.e159_tipo;
            }
        } catch (error) {
            console.error(error);
        }
    }

    function pesquisarTipoLicitacao() {

        if(isRJ && isJustificavel == 'true') {
            $('justificativainstrumentoprevio_bloco').style.display = 'none';
            let tiposCompraJustificaveis = <?php echo json_encode($tiposCompraJustificaveis) ?>;
            if(tiposCompraJustificaveis.includes($F('e60_codcom'))){
              $('justificativainstrumentoprevio_bloco').style.display = 'table-row';
            }
        }

        $('numeroLicitacao').className = ''
        $('anoLicitacao').className = '';
        $('numeroLicitacao').removeAttribute('readonly');
        $('anoLicitacao').removeAttribute('readonly');

        var oTipoLicitacao = $('e60_tipol');

        new AjaxRequest(
            'lic4_geraAutorizacoes.RPC.php',
            { exec : 'getTipoLicitacao', iTipoCompra : $F('e60_codcom') },
            function (oRetorno, lErro) {

                if (!oRetorno.obrigaLicitacao) {
                    $('numeroLicitacao').value = '';
                    $('anoLicitacao').value = '';
                    $('numeroLicitacao').className += 'readonly'
                    $('anoLicitacao').className += 'readonly';
                    $('numeroLicitacao').setAttribute('readonly', 'readonly');
                    $('anoLicitacao').setAttribute('readonly', 'readonly');
                }

                if (oRetorno.aTiposLicitacao.length == 0) {
                    return;
                }

                oTipoLicitacao.options.length = 0;
                oRetorno.aTiposLicitacao.each(
                    function (oTipo) {
                        oTipoLicitacao.add(new Option(`${oTipo.l03_tipo} - ${oTipo.l03_descr}`, oTipo.l03_tipo));
                        oTipoLicitacao.value = $F('tipoAtribuidoAnterior');
                    }
                );
            }
        ).setMessage('Aguarde, carregando tipo de licitação...').execute();
    }
    pesquisarTipoLicitacao();

    function js_abreFechaBlocoJustificativa(){

        $('justificativaatojuridico_bloco').style.display = 'none';
        if($F('tipoatojuridico') == "0") {
          $('justificativaatojuridico_bloco').style.display = 'table-row';
        }
    }

    iniciarJustificativasSigfis();
    function iniciarJustificativasSigfis(){
        if(isRJ && isJustificavel == 'true'){

            let tipoAtoJuridico = '<?=!empty($tipoatojuridico) ? $tipoatojuridico : ''?>';
            let tipoCompra = '<?=!empty($e60_codcom) ? $e60_codcom : ''?>';
            let tiposCompraJustificaveis = <?php echo json_encode($tiposCompraJustificaveis) ?>;

            if(tipoAtoJuridico == '99'){
                $('justificativaatojuridico_bloco').style.display = 'table-row';
            }
            if(tiposCompraJustificaveis.includes(tipoCompra)){
                $('justificativainstrumentoprevio_bloco').style.display = 'table-row';
            }
        }
    }
</script>
