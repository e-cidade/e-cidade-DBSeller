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
require_once(modification("libs/db_conecta.php"));
$oGet = db_utils::postMemory($_GET);
$oPost = db_utils::postMemory($_POST);
$opcaoA = 3;
$opcaoB = 3;
$lDesabled = true;
$sMsg = "";

$arquivos = array();
if (isset($oPost->arquivos)) {
    $arquivos[] = $oPost->arquivos;
    $arquivos = $arquivos[0];
}

$empenhoEletronico = 0;
if(isset($oPost->eletronico)){
    if($oPost->eletronico){
        $empenhoEletronico = 1;
    }else{
        $empenhoEletronico = 0;
    }
}

if (isset($oGet->chavepesquisa)) {
    $opcaoA = 1;
    $opcaoB = 1;
    $lDesabled = false;

    if (isset($e70_vlrliq) && $e70_vlrliq > 0) {
        $opcaoA = 3;
        $sMsg = "** Não será permitida alteração de data quando a nota já foi liquidada e/ou paga a NF **";
    }
}

if ($lDisabled2) {
    $opcaoA = 3;
    $opcaoB = 3;
}

$Tm51_codordem = isset($Tm51_codordem) ? $Tm51_codordem : null;
$Lm51_numcgm = isset($Lm51_numcgm) ? $Lm51_numcgm : null;
$Tdescrdepto = isset($Tdescrdepto) ? $Tdescrdepto : null;
$Lm51_depto = isset($Lm51_depto) ? $Lm51_depto : null;
$Te11_cfop = isset($Te11_cfop) ? $Te11_cfop : null;
$Tm51_valortotal = isset($Tm51_valortotal) ? $Tm51_valortotal : null;
$Ie11_basecalculoicms = isset($Ie11_basecalculoicms) ? $Ie11_basecalculoicms : null;
$Ie11_basecalculosubstitutotrib = isset($Ie11_basecalculosubstitutotrib) ? $Ie11_basecalculosubstitutotrib : null;

$oPreferenciaUsuario = db_getsession("DB_preferencias_usuario", false, true);
$visualizarEmOutraJanela = $oPreferenciaUsuario->isVisulizarEmOutraJanela();
?>
<script type="text/javascript" src="scripts/scripts.js"></script>
<script type="text/javascript" src="scripts/prototype.js"></script>
<script type="text/javascript" src="scripts/classes/http/http.js"></script>
<script rel="script" type="text/javascript" src="scripts/widgets/DBLancador.widget.js"></script>
<link href="estilos.css" rel="stylesheet" type="text/css">
<link href="estilos/grid.style.css" rel="stylesheet" type="text/css">
<style>
    div.files {
        display: inline-block;
        background: #fff;
        padding: 5px;
        border-radius: 5px;
        border: solid #0a0a0a 1px;
        cursor: pointer;
    }

    div#arquivos-selecionados {
        width: 400px;
        height: auto;
        margin-bottom: 20px;
    }
</style>
<form name="form1" method="post" action="">
    <fieldset style="margin-top: 50px; width: 700px;">
        <legend>Alteração de Notas Fiscais</legend>
        <div>
            <table class="form-container">
                <tr>
                    <td nowrap title="<?php $Tm51_codordem ?>">
                        <?php db_ancora("Ordem de Compra:", "js_consultaordemcompra(\$F('m51_codordem'));", 1); ?>
                    </td>
                    <td>
                        <?php
                        db_input('m51_codordem', 10, '', true, 'text');
                        ?>
                    </td>
                    <td nowrap title="<?php $Te69_numero ?>">
                        <b>Nota:</b>
                    </td>
                    <td>
                        <?php db_input('e69_numero', 15, 1, true, 'text', $opcaoB); ?>
                    </td>
                </tr>
                <!--[Extensao ContratosPADRS] campo serie nota -->
                <tr>
                    <td nowrap title="Fornecedor">
                        <?php $Lm51_numcgm ?>
                    </td>
                    <td>
                        <?php
                        db_input('m51_numcgm', 10, $Im51_numcgm, true, 'text');
                        db_input('z01_nome', 30, $Iz01_nome, true, 'text');
                        ?>
                    </td>
                    <td nowrap title="<?php $Te69_dtnota ?>">
                        <?php $Le69_dtnota ?>
                    </td>
                    <td>
                        <?php
                        if (!isset($e69_dtnota_dia)) {
                            $e69_dtnota_dia = null;
                        }
                        if (!isset($e69_dtnota_mes)) {
                            $e69_dtnota_mes = null;
                        }
                        if (!isset($e69_dtnota_ano)) {
                            $e69_dtnota_ano = null;
                        }
                        db_inputdata('e69_dtnota', $e69_dtnota_dia, $e69_dtnota_mes, $e69_dtnota_ano, true, 'text', $opcaoA);
                        ?>
                    </td>
                </tr>
                <tr>
                    </td>
                    <td nowrap title="<?php $Tdescrdepto ?>">
                        <?php $Lm51_depto ?>
                    </td>
                    <td>
                        <?php
                        db_input('m51_depto', 10, $Im51_depto, true, 'text');
                        db_input('descrdepto', 30, $Idescrdepto, true, 'text');
                        ?>
                    </td>
                    <td nowrap title="<?php $Te69_dtrecebe ?>">
                        <?php $Le69_dtrecebe ?>
                    </td>
                    <td>
                        <?php
                        if (!isset($e69_dtrecebe_dia)) {
                            $e69_dtrecebe_dia = null;
                        }
                        if (!isset($e69_dtrecebe_mes)) {
                            $e69_dtrecebe_mes = null;
                        }
                        if (!isset($e69_dtrecebe_ano)) {
                            $e69_dtrecebe_ano = null;
                        }
                        db_inputdata('e69_dtrecebe', $e69_dtrecebe_dia, $e69_dtrecebe_mes, $e69_dtrecebe_ano, true, 'text', $opcaoA);
                        ?>
                    </td>
                </tr>
                <tr>
                    </td>
                    <td nowrap title="<?php $Tm51_valortotal ?>">
                        <?php $Lm51_valortotal ?>
                    </td>
                    <td>
                        <?php
                        db_input('m51_valortotal', 10, $Im51_valortotal, true, 'text');
                        ?>
                    </td>
                    <td nowrap title="<?php $Te70_valor; ?>">
                        <?php $Le70_valor; ?>
                    </td>
                    <td>
                        <?php
                        db_input('e70_valor', 10, $Ie70_valor, true, 'text');
                        ?>
                    </td>
                </tr>

                <tr id="linhaTipoNota" style="display: none">
                    <td><label for="tipoNota" class="bold">Tipo de Nota:</label></td>
                    <td colspan="3">
                        <select id="tipoNota" name="tipo_nota" class="field-size-max">
                        </select>
                    </td>
                </tr>

                <tr id="linhaNumeroChave" style="display: none">
                    <td><label for="numeroChave"  class="bold" >Número da Chave: </label></td>
                    <td colspan="3">
                        <input type="text" id="numeroChave" name="chave_nota" class="field-size-max">
                    </td>
                </tr>
                <tr id="linhaNumeroSerie" style="display: none">
                    <td><label for="numeroSerie"  class="bold" >Número de Série: </label></td>
                    <td colspan="3">
                        <input type="text" id="numeroSerie" name="serie_nota" class="field-size-max">
                    </td>
                </tr>

                <?php if ($iControlaPit == 1) : ?>
                    <tr id='controlepit' style='display: <?php $iControlaPit == 1 ? "" : "none" ?>'>
                        <td><b>Tipo da Entrada: </b></td>
                        <td colspan="4">
                            <?php
                            $oDaoDocumentoFiscais = new cl_tipodocumentosfiscal();
                            $rsDocs = $oDaoDocumentoFiscais->sql_record($oDaoDocumentoFiscais->sql_query(null, "*", "e12_sequencial"));
                            $aItens[0] = "selecione";
                            for ($i = 0; $i < $oDaoDocumentoFiscais->numrows; $i++) {
                                $oItens = db_utils::fieldsMemory($rsDocs, $i);
                                $aItens [$oItens->e12_sequencial] = $oItens->e12_descricao;

                            }
                            db_select('e69_tipodocumentosfiscal', $aItens, true, $db_opcao);
                            ?>
                            <a href='#' onclick='js_abreNotaExtra()' style='display: none'
                               id='dadosnotacomplementar'>Outros Dados</a>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td nowrap title="<?php $Te11_cfop ?>">
                        <?php
                        db_ancora("<b>CPOF</b>", "js_pesquisae11_cfop(true);", $db_opcao);
                        ?>
                    </td>
                    <td nowrap colspan='3'>
                        <?php
                        db_input('e11_cfop', 10, $Ie11_cfop, true, 'text', 3, " onchange='js_pesquisae11_cfop(false);'");
                        db_input('e10_cfop', 10, $Ie10_cfop, true, 'text', $db_opcao, " onchange='js_pesquisae11_cfop(false);'");
                        db_input('e10_descricao', 40, $Ie10_descricao, true, 'text', 3, '')
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap>
                        <b>Série:</b>
                    </td>
                    <td nowrap>
                        <?php
                        db_input('e11_seriefiscal', 10, $Ie11_seriefiscal, true, 'text', $db_opcao, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap>
                        <b>Inscrição Subst.Fiscal:</b>
                    </td>
                    <td nowrap>
                        <?php
                        db_input('e11_inscricaosubstitutofiscal', 10, $Ie11_inscricaosubstitutofiscal, true, 'text', $db_opcao, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap>
                        <b>Base Calculo ICMS:</b>
                    </td>
                    <td nowrap>
                        <?php
                        db_input('e11_basecalculoicms', 10, $Ie11_basecalculoicms, true, 'text', $db_opcao, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap>
                        <b>Valor ICMS:</b>
                    </td>
                    <td nowrap>
                        <?php
                        db_input('e11_valoricms', 10, $Ie11_valoricms, true, 'text', $db_opcao, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap>
                        <b>Base Calculo ICMS Substituto:</b>
                    </td>
                    <td nowrap>
                        <?php
                        db_input('e11_basecalculosubstitutotrib', 10, $Ie11_basecalculosubstitutotrib, true, 'text', $db_opcao, '');
                        ?>
                    </td>
                </tr>
                <tr>
                    <td nowrap>
                        <b>Valor ICMS Substituto:</b>
                    </td>
                    <td nowrap>
                        <?php
                        db_input('e11_valoricmssubstitutotrib', 10, $Ie11_valoricmssubstitutotrib, true, 'text', $db_opcao, '');
                        ?>
                    </td>
                </tr>
                <tr id="componente-arquivos">
                    <td nowrap>
                        <label class="bold" for="arquivo_notafsical">Arquivo Nota Fiscal:</label>
                    </td>
                    <td colspan="3">
                        <input type="file" name="arquivo" id="arquivo" multiple>
                    </td>
                </tr>
            </table>
        </div>
    </fieldset>

    <fieldset id="infoSigfis" class="d-none" style="margin-top: 0.5rem;">
        <legend>Informações SIGFIS - TCE/RJ</legend>
        <!-- tipo documento  -->
        <div id="infoTipodocliquidacao" class="d-none">
            <table>
                <tr>
                    <td>
                        <label for="tipodocliquidacao">
                            <b>Tipo de documento: </b>
                        </label>
                        <select name="tipodocliquidacao" id="tipodocliquidacao" data-tipo="<?= isset($iTipodocliquidacao) ? $iTipodocliquidacao : '' ?>">
                            <option value="" selected disabled>Selecione</option>
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <!-- danfe -->
        <div id="infoDanfe" class="d-none">
            <fieldset>
                <legend>Danfe</legend>
                <table>
                    <tr>
                        <td>
                            <label for="danfeNumero"><b>Número: </b></label>
                            <input name="danfeNumero" id="danfeNumero" type="text" value="<?= isset($danfe) ? $danfe->numero : '' ?>"/>
                        </td>
                        <td>
                            <label for="danfeChave"><b>Chave: </b></label>
                            <input name="danfeChave" id="danfeChave" type="text" value="<?= isset($danfe) ? $danfe->chave : '' ?>"/>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </div>
        <!-- atestadores -->
        <input type="hidden" id="atestadores" name="atestadores">
        <div id="infoAtestadores" class="d-none" data-atestadores='<?= isset($aAtestadores) ? $aAtestadores : '' ?>'>
        </div>
    </fieldset>

    <fieldset id="field-arquivos">
        <div id="arquivos-selecionados"></div>
        <input type="hidden" name="arrayDocumentos" id="arrayDocumentos">
        <input type="hidden" name="arrayDocumentosDelete" id="arrayDocumentosDelete">
    </fieldset>
    <div style="margin-top: 10px;">
        <input name="alterar" id='alterar' type="submit" value="Atualizar" onclick='return validar_campos();'>
        <input name="pesquisar" type="button" value="Pesquisar" onclick="js_pesquisa_empnota(true)">
        <?php db_input('e69_codnota', 10, $Ie69_codnota, true, 'hidden'); ?>
    </div>
</form>
<table >
    <tr>
        <td><?php echo $sMsg; ?></td>
    </tr>
</table>
<script>
    var isEletronico = '<?php echo $empenhoEletronico ?>';

    if(isEletronico == false){
        var elemento = document.getElementById("componente-arquivos");
        elemento.style.display = "none";
        var elemento2 = document.getElementById("field-arquivos");
        elemento2.style.display = "none";
    }

    var UF = '<?= getEstadoInstituicao()?>'
    var isPB = UF === 'PB';
    var isRJ = UF === 'RJ';

    const tiposNota = [];

    const linhaTipoNota = document.getElementById('linhaTipoNota');
    const linhaNumeroChave = document.getElementById('linhaNumeroChave');
    const linhaNumeroSerie = document.getElementById('linhaNumeroSerie');
    const inputTipoNota = document.getElementById('tipoNota');
    const inputNumeroSerie = document.getElementById('numeroSerie');
    const inputNumeroChave = document.getElementById('numeroChave');

    /**
     * Considera os tipos de nota da Paraíba
     * @returns {*}
     */
    const getTipoNota = () => {
        return tiposNota.filter((tipo) => {
            return tipo.id == inputTipoNota.value;
        }).shift();
    };

    (function (){
        let codigoNota = document.getElementById('e69_codnota').value;

        if (isPB) {
            inputTipoNota.addEventListener('change', () => {
                if (inputTipoNota.value == '') {
                    return
                }

                let tipo = getTipoNota();
                linhaNumeroChave.style.display = 'none';
                if (tipo.chave != 0) {
                    linhaNumeroChave.style.display = 'table-row';
                }
                if (tipo.serie != 0) {
                    linhaNumeroSerie.style.display = 'table-row';
                }
            });

            const formData = new FormData();
            formData.append('acao', 'dadosNotaParaiba');
            formData.append('codigoNota', codigoNota);

            HttpClient.post('com4_notasempenho.RPC.php', {body: formData}).then(response => {
                inputNumeroSerie.value = response.data.numeroSerie;
                inputNumeroChave.value = response.data.chave;
                inputTipoNota.options.lenght = 0;

                linhaTipoNota.style.display = 'table-row';
                for (let tipo of response.data.tiposCompativeisEmpenho) {
                    inputTipoNota.add(new Option(tipo.label, tipo.id));
                    tiposNota.push(tipo);
                }
                inputTipoNota.value = response.data.tipoNota;
                inputTipoNota.dispatchEvent(new Event('change'));
            });
        }
    })();

    function js_consultaordemcompra(codordem) {
        if (codordem != "") {
            js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_ordemcompra002', 'com3_ordemdecompra002.php?m51_codordem=' + codordem, 'Consulta Ordem de Compra', true);
        }
    }

    function js_pesquisa_empnota() {
        js_reset();
        js_OpenJanelaIframe('CurrentWindow.corpo', 'db_iframe_empnota',
            'func_empnota.php?funcao_js=parent.js_mostraempnota1|e69_codnota&lNaoTrazerAnuladas=1&lm72_codordem=1',
            'Pesquisa', true);
    }

    function js_mostraempnota1(chave1) {
        location.href = 'mat1_altDadosNotaFiscal001.php?chavepesquisa=' + chave1;
    }


    function js_reset() {

        $('z01_nome').value = '';
        $('m51_numcgm').value = '';
        $('m51_codordem').value = '';
        $('e69_numero').value = '';
        $('e69_dtnota').value = '';
        $('e69_dtrecebe').value = '';
        $('e70_valor').value = '';
        $('m51_valortotal').value = '';
        $('m51_depto').value = '';
        $('descrdepto').value = '';

    }

    function js_alterar_ordemcompra() {
        location.href = "mat1_altDadosNotaFiscal001.php";
    }

    function validar_campos() {
        setArrayDocumentos();

        if ($('e69_tipodocumentosfiscal') && $F('e69_tipodocumentosfiscal') == "") {
            alert('Informe o Tipo da nota');
            return false;
        }

        if ($('e69_tipodocumentosfiscal') && $F('e69_tipodocumentosfiscal') == 50 && $F('e11_cfop') == "") {
            alert('Informe a CFOP!');
            return false;
        }

        if (isPB) {
            let tipo = getTipoNota();

            /**
             * se a no não pode ter numer da chave, temos que limpar a informação ao persistir
             */
            if (tipo.chave == 0) {
                inputNumeroChave.value = '';
            }
        }

        if (isRJ) {
            if (!js_validaTidocliquidacao()) {
                return false;
            }


            if (!js_validaAtestadores()) {
                return false;
            }

            js_setAtestadores();
        }

        /** [Extensao ContratosPADRS] valida serie nota */

        if ($('e69_dtnota').value == "") {
            alert('Campo data nota não informado!');
            return false;
        } else if ($('e69_dtnota').value == "") {
            alert('Campo data do recebimento não informado!');
            return false;
        } else {
            if (!confirm('Deseja fazer a alteração?')) {
                return false;
            }
        }



    }

    <?php
    if (isset($sPesquisa) && $sPesquisa == true) {
        echo "js_pesquisa_empnota();";
    }

    if ($lDesabled) {
        echo "$('alterar').disabled = true;";
    }
    ?>
    function js_abreNotaExtra() {

        if ($F('e69_tipodocumentosfiscal') == 50) {
            $('dadosnotacomplementar').style.display = '';
        } else {
            $('dadosnotacomplementar').style.display = 'none';
        }
    }

    function js_pesquisae11_cfop(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo',
                'db_iframe_cfop',
                'func_cfop.php?funcao_js=parent.js_mostracfop1|e10_sequencial|e10_descricao|e10_cfop',
                'Pesquisa CFOP', true);
        } else {
            if ($('e10_cfop').value != '') {
                js_OpenJanelaIframe('CurrentWindow.corpo',
                    'db_iframe_cfop',
                    'func_cfop.php?pesquisa_chave=' + $('e10_cfop').value + '&funcao_js=parent.js_mostracfop',
                    'Pesquisa CFOP', false);
            } else {
                $('e10_descricao').value = '';
            }
        }
    }

    function js_mostracfop(chave, chave2, erro) {

        $('e10_descricao').value = chave;
        $('e11_cfop').value = chave2;
        if (erro == true) {
            $('e10_cfop').focus();
            $('e10_cfop').value = '';
        }
    }

    function js_mostracfop1(chave1, chave2, chave3) {

        $('e11_cfop').value = chave1;
        $('e10_descricao').value = chave2;
        $('e10_cfop').value = chave3;
        db_iframe_cfop.hide();

    }

    const FILE_RPC = 'pro4_andamento_processo.RPC.php';
    const fileInput = document.getElementById("arquivo");
    const divArquivosSelecionados = document.getElementById("arquivos-selecionados");
    var fileList = [];
    var arquivosStorage = '<?php echo json_encode($arquivos)?>';
    arquivosStorage = JSON.parse(arquivosStorage);
    var elementosAusentes = [];

    if(arquivosStorage != null){
        arquivosStorage.forEach(function (arquivo) {
            var file = {
                id: arquivo["codigo_arquivo"],
                descricao: arquivo["nome_arquivo"],
                c70_codlan: arquivo["codigo_lancamento"]
            };
            if(file.descricao !== null && file.descricao !== ''){
                fileList.push(file);
            }
        });
        renderNamesFiles();
    }

    fileInput.addEventListener('change', function (e) {
        let i = 0;
        var data = new FormData();
        for (i; i < fileInput.files.length; i++) {
            data.append('anexos[]', fileInput.files[i]);
        }

        data.append('acao', 'prepararDocumentos');
        data.append('getExtArq', 0);
        HttpClient.post(FILE_RPC, {body: data}).then(function (response) {
            if (response.erro) {
                alert(response.mensagem);
                return;
            }
            response.documentos.map(function (documento) {
                fileList.push(Object.assign({}, documento));
                renderNamesFiles();
            });
        });
        fileInput.value = "";
    });

    function removeFile(indexFile) {
        fileList = fileList.filter(function (file, index) {
            console.log(index, indexFile, (index != indexFile))
            return index != indexFile;
        });
        renderNamesFiles();
    }

    function renderNamesFiles() {
        let html = [];
        fileList.forEach(function (file, index) {
            if ('id' in file) {
                html.push(`
                    <div class='files' style="background-color: lightgreen;" onclick="visualizarDocumento(${file.id})">${file.descricao} <a onclick="removeFile(${index})">X<a></div>
                `);
            } else {
                html.push(`
                    <div class='files' onclick="visualizarDocumento(${file.id})">${file.descricao} <a onclick="removeFile(${index})">X<a></div>
                `);
            }
        });
        divArquivosSelecionados.innerHTML = html.join("");
    }

    function verificaElementos() {
        var arrayIdsRemover = arquivosStorage.filter(function (arquivoStorage) {
            return !fileList.some(function (file) {
                return file.descricao === arquivoStorage.nome_arquivo;
            });
        });
        return arrayIdsRemover;
    }

    function setArrayDocumentos() {
        var arrayIdsRemover = JSON.stringify(verificaElementos());
        var arrayDocumentosDelete = arrayIdsRemover;
        var arrayString = JSON.stringify(fileList);
        document.getElementById("arrayDocumentos").value = arrayString;
        document.getElementById("arrayDocumentosDelete").value = arrayDocumentosDelete;
    }

    function visualizarDocumento(id){
        if(id != null){
            var visualizarEmOutraJanela = "<?php echo $visualizarEmOutraJanela; ?>";
            if (visualizarEmOutraJanela) {
                window.open(`db_visualizador_documentos.php?ids=${id}`);
            } else {
                js_OpenJanelaIframe('CurrentWindow.corpo', 'db_visualizador_imagens', `db_visualizador_documentos.php?ids=${id}`, 'Visualizador de documentos', true);
            }
        }
    }

    /**
     * Sessao destinada ao SIGFIS TCE/RJ
     **/
    if (isRJ) {

        // Elementos
        const infoSigfisEl          = document.querySelector('#infoSigfis');
        const infoAtestadoresEl     = document.querySelector('#infoAtestadores');
        const infoTipodocliquidacao = document.querySelector('#infoTipodocliquidacao');
        const tipodocliquidacao     = document.querySelector('#tipodocliquidacao');
        const infoDanfe             = document.querySelector('#infoDanfe');

        infoSigfisEl.classList.remove('d-none');

        // Preeche tipo de documento liquidacao
        const getTipodocliquidacao = async () => {
            js_divCarregando('Carregando Tipo de documento...', 'loadingTipodoc');

            try {
                const url = '/v4/api/financeiro/empenho/sigfis/tipodocliquidacao';
                const request = await CurrentWindow.axios(url);
                const {data: response} = request;
                const {data: responseData} = response;

                if (responseData.message == true) {
                    alert('Erro ao buscar tipos de documento de liquidacao.');
                    return false;
                }

                infoTipodocliquidacao.classList.remove('d-none');

                const tipos = responseData.tipos;
                tipos.forEach(item => {
                    const option = document.createElement('option');

                    option.innerHTML = item.e177_descr;
                    option.value = item.e177_sequencial;
                    option.dataset.codigo = item.e177_codigo;
                    option.dataset.decimo = item.e177_decimo;
                    option.dataset.tipodiverso = item.e177_tipodiverso;

                    tipodocliquidacao.appendChild(option);
                });

                if (tipodocliquidacao.dataset.tipo) {
                    tipodocliquidacao.value = tipodocliquidacao.dataset.tipo;
                    tipodocliquidacao.dispatchEvent(new Event('change'));
                }
            } catch (error) {
                alert('Erro ao buscar tipos de documento de liquidacao.');
            } finally {
                js_removeObj('loadingTipodoc');
            }
        }

        getTipodocliquidacao();

        // Lancador Atestadores
        var oDBLancadorAtestadores = new DBLancador('oDBLancadorAtestadores');
            oDBLancadorAtestadores.setNomeInstancia('oDBLancadorAtestadores');
            oDBLancadorAtestadores.setTextoFieldset("Atestadores");
            oDBLancadorAtestadores.setLabelAncora('cgm:');
            oDBLancadorAtestadores.setGridHeight(100);
            oDBLancadorAtestadores.setParametrosPesquisa('func_cgm.php', ['z01_numcgm','z01_nome']);
            oDBLancadorAtestadores.show(infoAtestadoresEl);

        if (infoAtestadoresEl.dataset.atestadores) {
            let atestadores = JSON.parse(infoAtestadoresEl.dataset.atestadores);
            atestadores.forEach(a => {
                oDBLancadorAtestadores.adicionarRegistro(a.z01_numcgm, a.z01_nome);
            });
        }

        // Atestadores
        const showFieldAtestadores = (option) => {
            const tipo = Number(option.dataset.codigo);
            const tipodocaccept = [1, 3];

            if (tipodocaccept.includes(tipo)) {
                infoAtestadoresEl.classList.remove('d-none');
            } else {
                infoAtestadoresEl.classList.add('d-none');
            }
        }

        function js_validaAtestadores() {
            const optionEl = tipodocliquidacao.options[tipodocliquidacao.selectedIndex];
            const tipo = Number(optionEl.dataset.codigo);
            const tipodocaccept = [1, 3];

            if (tipodocaccept.includes(tipo)) {
                if (!oDBLancadorAtestadores.getRegistros().length) {
                    alert('[Atestador] Atestadores devem ser informados.');
                    return false;
                }
            }

            return true;
        }

        function js_setAtestadores() {
            if (oDBLancadorAtestadores.getRegistros().length) {
                const atestadoresInput = document.querySelector('#atestadores');
                const aCgm = [];

                oDBLancadorAtestadores.getRegistros().forEach(e => {
                    aCgm.push(Number(e.sCodigo));
                });

                atestadoresInput.value = JSON.stringify(aCgm);
            }
        }

        function js_validaTidocliquidacao() {
            if (!tipodocliquidacao.value) {
                alert('Tipo documento deve ser informado.');
                return false;
            }

            return true;
        }

        // danfe
        const showFieldDanfe = (option) => {
            const tipo = Number(option.dataset.codigo);
            const tipodocaccept = [1];

            if (tipodocaccept.includes(tipo)) {
                infoDanfe.classList.remove('d-none');
            } else {
                infoDanfe.classList.add('d-none');
            }
        }

        // listeners
        const handleTipodocChange = (e) => {
            const selectEl = e.target;
            const optionEl = selectEl.options[selectEl.selectedIndex];

            if (optionEl) {
                showFieldAtestadores(optionEl);
                showFieldDanfe(optionEl);
            }
        }

        tipodocliquidacao.addEventListener('change', handleTipodocChange);

        // Reseta os campos destinado ao sigfis
        function clearSigfis() {

            // Limpa tipo documento
            tipodocliquidacao.value = '';

            // Limpa Atestadores
            infoAtestadoresEl.classList.add('d-none');
            oDBLancadorAtestadores.clearAll();

            // Limpa danfe
            infoDanfe.classList.add('d-none');
            infoDanfe.querySelectorAll('input').forEach(e => e.value = '');
        }
    }
</script>
