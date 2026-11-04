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

//MODULO: educação
use App\Domain\Configuracao\Helpers\StorageHelper;

include(modification("dbforms/db_classesgenericas.php"));
$cliframe_alterar_excluir = new cl_iframe_alterar_excluir;
$clformacao->rotulo->label();
$clrotulo = new rotulocampo;
$clrotulo->label("ed20_i_codigo");
$clrotulo->label("ed29_i_codigo");
$clrotulo->label("ed21_i_codigo");
$clrotulo->label("ed20_c_posgraduacao");
$clrotulo->label("ed20_c_outroscursos");
$db_botao1 = false;
if (isset($opcao) && $opcao == "alterar") {
    $db_opcao = 2;
    $db_botao1 = true;
    if (trim($ed27_c_situacao) == "CONCLUÍDO") {
        $ed27_c_situacao = "CON";
    } elseif (trim($ed27_c_situacao) == "INTERROMPIDO") {
        $ed27_c_situacao = "INT";
    } else {
        $ed27_c_situacao = "CUR";
    }

    if (trim($ed27_i_licenciatura) == "SIM") {
        $ed27_i_licenciatura = "1";
    } else {
        $ed27_i_licenciatura = "0";
    }

} elseif (isset($opcao) && $opcao == "excluir" || isset($db_opcao) && $db_opcao == 3) {
    $db_botao1 = true;
    $db_opcao = 3;
    if (trim($ed27_c_situacao) == "CONCLUÍDO") {
        $ed27_c_situacao = "CON";
    } elseif (trim($ed27_c_situacao) == "INTERROMPIDO") {
        $ed27_c_situacao = "INT";
    } else {
        $ed27_c_situacao = "CUR";
    }

} else {
    if (isset($alterar)) {
        $db_opcao = 2;
        $db_botao1 = true;
    } else {
        $db_opcao = 1;
    }
}

$daoCensoDisciplina = new cl_censodisciplina();
$sqlCensoDisciplina = $daoCensoDisciplina->sql_query_formacao(!empty($ed27_i_codigo) ? $ed27_i_codigo : null);
$rsCensoDisciplina = db_query($sqlCensoDisciplina);

if (isset($ed27_i_cursoformacao)) {
    $daoCursoFormacao = new cl_cursoformacao();
    $sqlCursoFormacao = $daoCursoFormacao->sql_query_file($ed27_i_cursoformacao, 'ed94_i_grauacademico');
    $rsCursoFormacao = db_query($sqlCursoFormacao);

    if (!$rsCursoFormacao) {
        db_msgbox('Não foi possível buscar o grau de formação.');
        return;
    }

    $grauAcademico = null;
    if (pg_num_rows($rsCursoFormacao) > 0) {
        $grauAcademico = db_utils::fieldsMemory($rsCursoFormacao, 0)->ed94_i_grauacademico;
    }
}

if (!$rsCensoDisciplina) {
    db_msgbox('Não foi possível buscar as formações disponíveis.');
    return;
}
$formacoes = array(null => (object)array('codigo' => null, 'descricao' => 'Selecionar', 'selecionada' => false));
while ($formacao = pg_fetch_object($rsCensoDisciplina)) {
    $formacoes[$formacao->ed265_i_codigo] = (object)array(
        'descricao' => substr($formacao->ed265_c_descr, 0, 60),
        'selecionada' => empty($formacao->ed145_sequencial) ? false : true
    );
}

$arquivo = "";
$arquivoFormacao = "";

if (isset($ed27_i_codigo) && !empty($ed27_i_codigo)) {

    $daoFormacao = new cl_formacao();
    $sqlFormacao = $daoFormacao->sql_query_file($ed27_i_codigo, 'ed27_i_docformacao_estorage');
    $rsFormacao = db_query($sqlFormacao);

    if (!$rsFormacao) {
        db_msgbox('Não foi possível buscar a formação.');
        return;
    }

    if (pg_num_rows($rsFormacao) > 0) {
        $idFormacao = db_utils::fieldsMemory($rsFormacao, 0)->ed27_i_docformacao_estorage;
        $arquivo = !empty($idFormacao) ? StorageHelper::downloadArquivo($idFormacao): "" ;
        $arquivoFormacao = basename($arquivo);
    }

}

?>
<form name="form1" method="post" action="">
    <table border="0">
        <tr>
            <td valign="top">
                <fieldset style="height:100%">
                    <legend><b>Cursos Superiores</b></legend>
                    <table border="0">
                        <tr>
                            <td nowrap>
                            </td>
                            <td>
                                <?php db_input('ed27_i_codigo', 15, @$Ied27_i_codigo, true, 'hidden', 3, "") ?>
                                <?php db_input('ed27_i_rechumano', 15, @$Ied27_i_rechumano, true, 'hidden', 3, "") ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$ed20_i_tiposervidor == '1' ? 'Matrícula' : 'CGM' ?>">
                                <b><?= @$ed20_i_tiposervidor == '1' ? 'Matrícula:' : 'CGM:' ?></b>
                            </td>
                            <td>
                                <?php db_input('identificacao', 10, @$identificacao, true, 'text', 3, "") ?>
                                <?php db_input('z01_nome', 50, @$Iz01_nome, true, 'text', 3, '') ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Ted27_i_cursoformacao ?>">
                                <?php db_ancora(@$Led27_i_cursoformacao, "js_pesquisaed27_i_cursoformacao(true);", $db_opcao); ?>
                            </td>
                            <td>
                                <?php db_input('ed27_i_cursoformacao', 10, $Ied27_i_cursoformacao, true, 'hidden', 3, "") ?>
                                <?php db_input('ed94_c_codigocenso', 10, @$Ied94_c_codigocenso, true, 'text', 3, "") ?>
                                <?php db_input('ed94_c_descr', 35, @$Ied94_c_descr, true, 'text', 3, '') ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Ted27_i_censoinstsuperior ?>">
                                <?php db_ancora(@$Led27_i_censoinstsuperior, "js_pesquisaed27_i_censoinstsuperior(true);", $db_opcao); ?>
                            </td>
                            <td>
                                <?php db_input('ed27_i_censoinstsuperior', 10, $Ied27_i_censoinstsuperior, true, 'text', $db_opcao, " onchange='js_pesquisaed27_i_censoinstsuperior(false);'") ?>
                                <?php db_input('ed257_c_nome', 35, @$Ied257_c_nome, true, 'text', 3, '') ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Ted27_c_situacao ?>">
                                <?= @$Led27_c_situacao ?>
                            </td>
                            <td>
                                <?php
                                $x = array('CON' => 'CONCLUÍDO', 'CUR' => 'EM ANDAMENTO', 'INT' => 'INTERROMPIDO');
                                db_select('ed27_c_situacao', $x, true, $db_opcao, "onchange = 'js_verificacao(this.value);'");
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Ted27_i_formacaopedag ?>">
                                <?= @$Led27_i_formacaopedag ?>
                            </td>
                            <td>
                                <?php
                                if (!empty($ed27_i_formacaopedag) && $ed27_i_formacaopedag === 'SIM') {
                                    $ed27_i_formacaopedag = 1;
                                } else {
                                    $ed27_i_formacaopedag = 0;
                                }
                                $x = array('0' => 'NÃO', '1' => 'SIM');
                                db_select('ed27_i_formacaopedag', $x, true, $db_opcao, "");
                                ?>
                            </td>
                        </tr>
                        <tr data-id="formacoes">
                            <td><b>Formação/1</b></td>
                            <td>
                                <select name="formacao1">
                                    <?php
                                    $selecionou = false;
                                    foreach ($formacoes as $index => $formacao) {
                                        $selected = "";
                                        if ($formacao->selecionada && !$selecionou) {
                                            $selected = 'selected';
                                            $selecionou = true;
                                            $formacao->selecionada = false;
                                        }
                                        echo "<option value='{$index}' {$selected}>{$formacao->descricao}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr data-id="formacoes">
                            <td><b>Formação/2</b></td>
                            <td>
                                <select name="formacao2">
                                    <?php
                                    $selecionou = false;
                                    foreach ($formacoes as $index => $formacao) {
                                        $selected = "";
                                        if ($formacao->selecionada && !$selecionou) {
                                            $selected = 'selected';
                                            $selecionou = true;
                                            $formacao->selecionada = false;
                                        }
                                        echo "<option value='{$index}' {$selected}>{$formacao->descricao}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr data-id="formacoes">
                            <td><b>Formação/3</b></td>
                            <td>
                                <select name="formacao3">
                                    <?php
                                    $selecionou = false;
                                    foreach ($formacoes as $index => $formacao) {
                                        $selected = "";
                                        if ($formacao->selecionada && !$selecionou) {
                                            $selected = 'selected';
                                            $selecionou = true;
                                            $formacao->selecionada = false;
                                        }
                                        echo "<option value='{$index}' {$selected}>{$formacao->descricao}</option>";
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td nowrap title="<?= @$Ted27_i_licenciatura ?>">
                                <?= @$Led27_i_licenciatura ?>
                            </td>
                            <td>
                                <?php
                                $xx = array("0" => "NÃO", "1" => "SIM");
                                db_select("ed27_i_licenciatura", $xx, true, $db_opcao, "");
                                ?>
                                <?= @$Led27_i_anoinicio ?>
                                <?php db_input('ed27_i_anoinicio', 4, $Ied27_i_anoinicio, true, 'text', $db_opcao, "") ?>
                                <?= @$Led27_i_anoconclusao ?>
                                <?php db_input('ed27_i_anoconclusao', 4, $Ied27_i_anoconclusao, true, 'text', $db_opcao, "") ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>Anexo da Formação:</b>
                            </td>
                            <td>
                                <div style="display: flex; align-items: center; width:100%;">
                                    <iframe name="frame_imagemFormacao" id="frame_imagemFormacao" src="edu4_alunodocumentoformacao.php" width="56" height="40" frameborder="1" scrolling="no"></iframe>
                                <script>
                                    frame_imagemFormacao.location.href="edu4_alunodocumentoformacao.php?imagem_gerada=<?php echo $arquivoFormacao?>";
                                </script>
                                <div style="display: flex;margin-top: -5px;flex-direction: column;width:100%;">
                                    <iframe name="frame_formacao" id="frame_formacao" src="edu1_frameformacao.php" width="100%" height="31" frameborder="0" scrolling="no" style="margin-bottom: 0px;margin-top:2px;"></iframe>
                                    <input type="button" value="Excluir Imagem"
                                        onclick="location.href='edu1_formacao001.php?excluirformacao&ed27_i_rechumano=<?php echo $ed27_i_rechumano; ?>&ed27_i_codigo=<?php echo (isset($ed27_i_codigo)?$ed27_i_codigo:"");?>'"
                                        style="font-size: 9px;padding: 0px;margin-left: 3px;width:82px;">
                                </div>
                                <input name="oid_arquivoFormacao" type="hidden" id="oid_arquivoFormacao" value="<?php echo $arquivo?>" size="30">
                            </div>
                        </tr>
                    </table>
                </fieldset>
            </td>
        </tr>
        <tr>
            <td colspan="2" align="center"><br>
                <input
                        name="<?= ($db_opcao == 1 ? "incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "alterar" : "excluir")) ?>"
                        type="submit" id="db_opcao"
                        value="<?= ($db_opcao == 1 ? "Incluir" : ($db_opcao == 2 || $db_opcao == 22 ? "Alterar" : "Excluir")) ?>" <?= ($db_botao == false ? "disabled" : "") ?> <?= ($ed20_i_escolaridade != 6 ? "disabled" : "") ?>
                        onclick=" return js_validacao();">
                <input name='btnPosGraduacao_' value="Pós Graduação" type="button" id='btnPosGraduacao'>
                <input name='btnOutroDados' value="Outros Cursos" type="button" id='btnOutrosDados'>
                <input name="cancelar" type="submit" value="Cancelar" <?= ($db_botao1 == false ? "disabled" : "") ?> >
            </td>
        </tr>
    </table>
    <table width="100%">
      <tr>
        <td>
          <?php
            $campos = "ed27_i_codigo,
                       ed27_i_rechumano,
                       ed27_i_cursoformacao,
                       ed94_c_descr,
                       ed94_c_codigocenso,
                       ed27_i_censoinstsuperior,
			  	       		  ed27_i_anoinicio,
                       ed27_i_anoconclusao,
                             case
                        when ed27_i_formacaopedag = '0'
                         then 'NÃO'
                        when ed27_i_formacaopedag = '1'
                         then 'SIM'
                       end as ed27_i_formacaopedag,
                       case
                        when ed27_c_situacao = 'CON'
                         then 'CONCLUÍDO'
                        when ed27_c_situacao = 'CUR'
                         then 'CURSANDO' else
                         'INTERROMPIDO'
                       end as ed27_c_situacao,
                       case
                        when ed27_i_licenciatura = 0
                         then 'NÃO'
                         else 'SIM'
                       end as ed27_i_licenciatura,
                       ed257_c_nome,
                       case
                         when ed27_i_docformacao_estorage is not null
                           then 'Sim'
                         else 'Não'
                       end as possui_anexo_formacao";

            $campos_iframe  = "ed27_i_codigo,ed94_c_codigocenso,ed94_c_descr,ed257_c_nome,ed27_i_licenciatura,";
            $campos_iframe .= "ed27_i_anoinicio,ed27_i_anoconclusao,ed27_c_situacao,ed27_i_formacaopedag,";
            $campos_iframe .= "possui_anexo_formacao";

            $chavepri = array();
            $chavepri["ed27_i_codigo"] = (!empty($ed27_i_codigo) ? $ed27_i_codigo: '');
            $chavepri["ed27_i_rechumano"] = (!empty($ed27_i_rechumano) ? $ed27_i_rechumano: '');
            $chavepri["ed27_i_cursoformacao"] = (!empty($ed27_i_cursoformacao) ? $ed27_i_cursoformacao: '');
            $chavepri["ed94_c_descr"] = (!empty($ed94_c_descr) ? $ed94_c_descr: '');
            $chavepri["ed94_c_codigocenso"] = (!empty($ed94_c_codigocenso) ? $ed94_c_codigocenso: '');
            $chavepri["ed27_i_censoinstsuperior"] = (!empty($ed27_i_censoinstsuperior) ? $ed27_i_censoinstsuperior: '');
            $chavepri["ed257_c_nome"] = (!empty($ed257_c_nome) ? $ed257_c_nome: '');
            $chavepri["ed27_c_situacao"] = (!empty($ed27_c_situacao) ? $ed27_c_situacao: '');
            $chavepri["ed27_i_anoinicio"] = (!empty($ed27_i_anoinicio) ? $ed27_i_anoinicio: '');
            $chavepri["ed27_i_anoconclusao"] = (!empty($ed27_i_anoconclusao) ? $ed27_i_anoconclusao: '');
            $chavepri["ed27_i_licenciatura"] = (!empty($ed27_i_licenciatura) ? $ed27_i_licenciatura: '');
            $chavepri["ed27_i_formacaopedag"] = (!empty($ed27_i_formacaopedag) ? $ed27_i_formacaopedag: '');
            $cliframe_alterar_excluir->chavepri = $chavepri;
            $cliframe_alterar_excluir->sql = $clformacao->sql_query("", $campos, "", " ed27_i_rechumano = $ed27_i_rechumano");
            $cliframe_alterar_excluir->campos = $campos_iframe;
            $cliframe_alterar_excluir->legenda = "Registros";
            $cliframe_alterar_excluir->msg_vazio = "Não foi encontrado nenhum registro.";
            $cliframe_alterar_excluir->textocabec = "#DEB887";
            $cliframe_alterar_excluir->textocorpo = "#444444";
            $cliframe_alterar_excluir->fundocabec = "#444444";
            $cliframe_alterar_excluir->fundocorpo = "#eaeaea";
            $cliframe_alterar_excluir->iframe_height = "200";
            $cliframe_alterar_excluir->iframe_width = "100%";
            $cliframe_alterar_excluir->tamfontecabec = 9;
            $cliframe_alterar_excluir->tamfontecorpo = 9;
            $cliframe_alterar_excluir->formulario = false;
            $cliframe_alterar_excluir->iframe_alterar_excluir($db_opcao);

            if (isset($ed27_i_rechumano) && !empty($ed27_i_rechumano)) {
                $daoRecHumano = new cl_rechumano;
                $sqlCgm = $daoRecHumano->sql_query_rechumano_cgm(
                    $ed27_i_rechumano,
                    "case when cgmrh.z01_nome is not null then  cgmrh.z01_nome else cgmcgm.z01_nome end as nome,".
                    "case when cgmrh.z01_numcgm is not null then cgmrh.z01_numcgm else cgmcgm.z01_numcgm end as cgm"
                    );
                $result = db_query($sqlCgm);
                if (!$result) {
                    db_msgbox("Erro ao buscar CGM");
                }
                else {
                    db_fieldsmemory($result,0);
                }
            }

          ?>
        </td>
      </tr>
    </table>
</form>

<script type="text/javascript" src="assets/jquery/jquery-3.5.1.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/popper.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-4.5.3/js/bootstrap.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/locale/bootstrap-table-pt-BR.min.js"></script>
<script type="text/javascript" src="assets/bootstrap-table/bootstrap-table-export.min.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/session.js"></script>
<script language="JavaScript" type="text/javascript" src="scripts/classes/http/http.js"></script>

<script language="JavaScript" type="text/javascript"
    src="scripts/classes/dbViewFormacaoProfissional.classe.js"></script>

<script>
    $.noConflict();
    const GRAU_LICENCIATURA = '3';

    const formacaoPedagogica = document.getElementById('ed27_i_formacaopedag');
    const containerFormacoes = document.querySelectorAll('[data-id=formacoes]');
    var grauAcademico = '<?php echo empty($grauAcademico) ? 'null' : $grauAcademico?>';

    console.log('Grau Academico '+ grauAcademico);
    console.log('formacaoPedagogica '+ formacaoPedagogica.value);

    const verificaGrauAcademico = () => {
        if (grauAcademico !== 'null' && grauAcademico !== GRAU_LICENCIATURA && formacaoPedagogica.value === '1') {
            containerFormacoes.forEach(element => {
                element.style.display = 'table-row';
            });
        } else {
            containerFormacoes.forEach(element => {
                element.style.display = 'none';
                element.querySelector('select').value = "";
            });
        }
    };

    formacaoPedagogica.addEventListener('change', () => verificaGrauAcademico());
    verificaGrauAcademico();

    function js_pesquisaed27_i_cursoformacao(mostra) {
        js_OpenJanelaIframe('', 'db_iframe_cursoformacao', 'func_cursoformacao.php?funcao_js=parent.js_mostracursoedu1|ed94_i_codigo|ed94_c_codigocenso|ed94_c_descr|db_ed94_i_grauacademico', 'Pesquisa de Cursos de Formação Superior', true);
    }

    function js_mostracursoedu1(chave1, chave2, chave3, chave4) {
        document.form1.ed27_i_cursoformacao.value = chave1;
        document.form1.ed94_c_codigocenso.value = chave2;
        document.form1.ed94_c_descr.value = chave3;
        db_iframe_cursoformacao.hide();
        grauAcademico = chave4;
        verificaGrauAcademico();
    }

    function js_pesquisaed27_i_censoinstsuperior(mostra) {
        if (document.form1.ed27_i_cursoformacao.value == "") {
            alert("Informe o Curso!");
            document.form1.ed27_i_censoinstsuperior.value = "";
            document.form1.ed257_c_nome.value = "";
        } else {
            if (mostra == true) {
                js_OpenJanelaIframe('', 'db_iframe_censoinstsuperior', 'func_censoinstsuperior.php?cursoformacao=' + document.form1.ed27_i_cursoformacao.value + '&funcao_js=parent.js_mostracensoinstsuperior1|ed257_i_codigo|ed257_c_nome', 'Pesquisa de Instituições de Ensino Superior', true);
            } else {
                if (document.form1.ed27_i_censoinstsuperior.value != '') {
                    js_OpenJanelaIframe('', 'db_iframe_censoinstsuperior', 'func_censoinstsuperior.php?cursoformacao=' + document.form1.ed27_i_cursoformacao.value + '&pesquisa_chave=' + document.form1.ed27_i_censoinstsuperior.value + '&funcao_js=parent.js_mostracensoinstsuperior', 'Pesquisa', false);
                } else {
                    document.form1.ed257_c_nome.value = '';
                }
            }
        }
    }

    function js_mostracensoinstsuperior(chave, erro) {
        document.form1.ed257_c_nome.value = chave;
        if (erro == true) {
            document.form1.ed27_i_censoinstsuperior.focus();
            document.form1.ed27_i_censoinstsuperior.value = '';
        }
    }

    function js_mostracensoinstsuperior1(chave1, chave2) {
        document.form1.ed27_i_censoinstsuperior.value = chave1;
        document.form1.ed257_c_nome.value = chave2;
        db_iframe_censoinstsuperior.hide();
    }

    function js_verificacao(valor) {
        if (valor == 'CUR') {
            alert("Informe o ano de inicio");
            document.form1.ed27_i_formacaopedag.disabled = true;
            document.form1.ed27_i_anoconclusao.value = '';
            document.form1.ed27_i_anoconclusao.disabled = true;
        } else {
            document.form1.ed27_i_anoconclusao.disabled = false;
            document.form1.ed27_i_formacaopedag.disabled = false;
        }

        if (valor == 'CON') {
            alert("Informe o ano de conclusao");
            document.form1.ed27_i_anoinicio.disabled = true;
            document.form1.ed27_i_anoinicio.value = '';
            document.form1.ed27_i_formacaopedag.disabled = false;
        } else {
            document.form1.ed27_i_formacaopedag.disabled = true;
            document.form1.ed27_i_anoinicio.disabled = false;
        }
    }

    function js_validacao() {
        if (document.form1.ed27_c_situacao.value == 'CUR') {
            if (document.form1.ed27_i_anoinicio.value == '') {
                alert("Informe o ano de início");
                document.form1.ed27_i_anoinicio.focus();
                document.form1.ed27_i_anoconclusao.value = '';
                return false;
            }

            if (document.form1.ed27_i_anoconclusao.value != '') {
                alert("Ano de conclusão não deve ser informado");
                document.form1.ed27_i_anoconclusao.value = '';
                document.form1.ed27_i_anoinicio.focus();
                return false;
            }
        } else {
            document.form1.ed27_i_anoconclusao.disabled = false;
        }

        if (document.form1.ed27_c_situacao.value == 'CON') {
            if (document.form1.ed27_i_anoconclusao.value == '') {
                alert("Informe o ano de conclusao");
                document.form1.ed27_i_anoinicio.value = '';
                document.form1.ed27_i_anoconclusao.focus();
                document.form1.ed27_i_formacaopedag.disabled = false;
                return false;
            }
            if (document.form1.ed27_i_anoinicio.value != '') {
                alert("Ano de início não deve ser informado");
                document.form1.ed27_i_anoinicio.value = '';
                document.form1.ed27_i_anoinicio.focus();
                return false;
            }
        } else {
            document.form1.ed27_i_formacaopedag.disabled = true;
            document.form1.ed27_i_anoinicio.disabled = false;
        }

        return true;
    }

    $('btnOutrosDados').observe("click", function () {

        var oParametro = new Object();
        oParametro.exec = "getAvaliacaoRecursoHumano";
        oParametro.iRecursoHumano = $F('ed27_i_rechumano');
        js_divCarregando('Aguarde, carregando dados da Avaliação', 'msgBox');
        var oAjax = new Ajax.Request('edu4_dadoscensoescola.RPC.php',
            {
                method: 'post',
                parameters: 'json=' + Object.toJSON(oParametro),
                onComplete: js_montarAvaliacao
            });


    });

    $('btnPosGraduacao').observe("click", function () {
        js_montarFormacao(<?= @$cgm ?>, "<?= @$nome ?>");
    });

    function js_montarAvaliacao(oResponse) {

        js_removeObj('msgBox');
        var oRetorno = JSON.parse(oResponse.responseText);
        if (oRetorno.status == 1) {

            var iCodigoAvaliacao = '3000002';
            oAvaliacaoEscola = new dbViewAvaliacao(iCodigoAvaliacao, oRetorno.iCodigoAvaliacao);
            oAvaliacaoEscola.show();
            $('btnSalvarPerguntas' + iCodigoAvaliacao).style.display = 'none';
            $('btnSalvarAvaliacao' + iCodigoAvaliacao).value = 'Salvar';
        } else {
            alert('Dados da avaliação não disponíveis.');
        }
    }

     function js_montarFormacao(cgm, nome) {
        js_removeObj('msgBox');
        var iCodigoAvaliacao = '3000003';
        oFormacaoProfissional = new dbViewFormacaoProfissional(iCodigoAvaliacao, cgm, nome);
     }
</script>
