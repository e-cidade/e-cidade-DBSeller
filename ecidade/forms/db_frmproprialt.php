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

$mostraApenasFracao = isset($_GET['apenasFracao']);

if (isset($_POST['incluir']) || isset($_POST['alterar'])) {
    $j42_numcgm = '';
    $z01_nome = '';
}

if ((isset($j42_matric) && $j42_matric != "") && (isset($z01_nomematri) || $z01_nomematri == "")) {
    $oDaoIptuBase = new cl_iptubase();
    $sSqlProprietario = $oDaoIptuBase->sql_query($j42_matric);
    $rsProprietario = db_query($sSqlProprietario);
    $proprietariosEncontrados = db_utils::getCollectionByRecord($rsProprietario);

    if (count($proprietariosEncontrados) > 0) {
        $z01_nomematri = $proprietariosEncontrados[0]->z01_nome;
    }
}
?>

<script>
    function js_trocaid(numcgm) {
        <?php if (isset($j42_matric) && $j42_matric != "") { ?>
            location.href = "cad1_proprialt.php?j42_matric=" + document.form1.j42_matric.value + "&j42_numcgm=" + numcgm + "<?=$mostraApenasFracao ? '&apenasFracao=true' : ''?>";
        <?php } else { ?>
            location.href = "cad1_proprialt.php?j42_matric=" + document.form1.j42_matric.value + "&j42_numcgm=" + numcgm  + "<?=$mostraApenasFracao ? '&apenasFracao=true' : ''?>";;
        <?php } ?>
    }

    function js_excluiid(numcgm) {
        location.href = "cad1_proprialt.php?excluir=true&&j42_matric=" + document.form1.j42_matric.value + "&numcgm=" + numcgm  + "<?=$mostraApenasFracao ? '&apenasFracao=true' : ''?>";
    }

    function js_verizero(tipo) {
        const j42_numcgm = new Number(document.form1.j42_numcgm.value);
        const z01_nome = document.form1.z01_nome.value;

        try{
            if (isNaN(j42_numcgm) || j42_numcgm == "0" || z01_nome == "Código () não Encontrado") {
                document.form1.j42_numcgm.focus();
                throw new Error("Verifique o campo com o numero da Matrícula!");
            }
        } catch(error){
            alert(error.message);
            return false;
        }

        return true;
    }

    function js_excluiProprietario(matric, numcgm) {
        if (!confirm('Deseja excluir o proprietário ' + numcgm + '?')) {
            return false;
        } else {
            js_excluiid(numcgm);
        }
    }
</script>

<?php
    if(!$mostraApenasFracao){
?>

<fieldset>

    <legend><b><?=$mostraApenasFracao ? 'Fração por proprietário:' : 'Outros proprietários:' ?></b></legend>

    <table border="0" width="790">
        <tr>
            <td nowrap title="<?= @$Tj42_matric ?>"><?= @$Lj42_matric ?></td>
            <td>
                <?php
                db_input('j42_matric', 10, $Ij42_matric, true, 'text', 3, " onchange='js_pesquisaj42_matric(false);'");
                db_input('z01_nome', 78, $Ij01_numcgm, true, 'text', 3, '', 'z01_nomematri');
                ?>
            </td>
        </tr>
        <tr>
            <td nowrap title="<?= @$Tj42_numcgm ?>">
                <?php
                db_ancora($Lj42_numcgm, ' js_pesquisaj42_numcgm(true); ', (isset($_POST['incluir']) || !$j42_numcgm) ? "1" : "3");
                ?>
            </td>
            <td>
                <?php
                db_input('j42_numcgm', 10, $Ij42_numcgm, true, 'text', (isset($_POST['incluir']) || !$j42_numcgm) ? "1" : "3", "onchange='js_pesquisaj42_numcgm(false)'");
                db_input('z01_nome', 78, $Iz01_nome, true, 'text', 3, "");
                ?>
            </td>
        </tr>

        <!--Tipo de proprietário-->
        <tr>
            <td nowrap title="<?php echo $Tj163_descricao ?>"><?php echo $Lj163_descricao ?></td>
            <td id="listaProprietario"></td>
        </tr>

        <?php
        $j01_tipoimovel = isset($j01_tipoimovel) ? $j01_tipoimovel : $tipoImovel;
        if ($j01_tipoimovel == "2") {
            if (empty($j166_sequencial)) {
                $j166_sequencial = "";
            }
        ?>
            <tr>
                <td><strong>Percentual de Posse (%):</strong></td>
                <td>
                    <input type="hidden" name="j166_sequencial" id="j166_sequencial" value="<?= $j166_sequencial ?>">
                    <?php
                    db_input('j166_percentual', 10, $Ij166_percentual, true, 'text', 1, "", "", "", "", 5);
                    ?>
                </td>
            </tr>
        <?php } ?>
        <tr>
            <td><strong>Fra&ccedil;&atilde;o por propriet&aacute;rio:</strong></td>
            <td>
                <?php db_input('j42_fracaoproprietario', 9, $Ij42_fracaoproprietario, true, 'text', 1, "onchange='js_validaPorcentagem()'", "", "", "", null); ?>%
            </td>
        </tr>
        <tr>
            <td><strong>&Aacute;rea do lote por propriet&aacute;rio:</strong></td>
            <td>
                <?php db_input('j42_arealoteproprietario', 9, $Ij42_arealoteproprietario, true, 'text', 1); ?>m²
            </td>
        </tr>
        <tr>
        <tr>
            <td><input name="cgmpropri" type="hidden" value="<?= @$cgmpropri ?>"></td>
        </tr>

    </table>

</fieldset>

<br />

<input name="incluir" type="submit" id="incluir" value="Incluir" onclick="return js_verizero('inclusao')">

<input name="alterar" type="button" id="alterar" value="Alterar" onclick="return alteraProprietario()">

<?php
    }
?>

<?php if ($mostraApenasFracao) { ?>
<fieldset>
    <legend>Proprietário Principal</legend>
    <table class="form-container" border="1">
        <thead>
            <tr style="background-color: #e1e1e1">
                <th class="text-center">Numcgm</th>
                <th class="text-left">Proprietário</th>
                <th class="text-left">Tipo do Proprietário</th>
                <th class="text-left">Fração</th>
                <th class="text-left">Área</th>
            </tr>
        </thead>
        <tbody>
            <?php 
                $camposIptubase     = "j01_numcgm,z01_nome,j01_tipoproprietario,j34_area,ROUND(j01_fracaoproprietario, 4) as j01_fracaoproprietario,j163_descricao";
                $resultProprietario = $cliptubase->sql_record($cliptubase->sql_query($j42_matric, $camposIptubase, ""));
                
                $oDadosProprietarioPrincipal = db_utils::getCollectionByRecord($resultProprietario)[0];

                $percentualFracao = $oDadosProprietarioPrincipal->j01_fracaoproprietario . '%';
                $areaLote = (($oDadosProprietarioPrincipal->j01_fracaoproprietario / 100) * $oDadosProprietarioPrincipal->j34_area) . 'm²';

                if ($oDadosProprietarioPrincipal) {
            ?>
                    <tr class="cores">
                        <td class="numcgmproprietario text-center field-size2"><?= $oDadosProprietarioPrincipal->j01_numcgm ?></td>
                        <td class="text-left field-size8"><?= $oDadosProprietarioPrincipal->z01_nome ?></td>
                        <td class="descricaoproprietario text-left"><?= $oDadosProprietarioPrincipal->j163_descricao ?></td>
                        <td class="descricaoproprietario text-left"><?= $percentualFracao ?></td>
                        <td class="descricaoproprietario text-left"><?=  $areaLote ?></td>
                    </tr>
            <?php } ?>
        </tbody>
    </table>
</fieldset>
<?php } ?>

<?php
$result = db_query($clpropri->sql_query($j42_matric, "", "propri.*#cgm.z01_nome#cgm.z01_cgccpf", null, "j42_matric = $j42_matric"));
$num = count(db_utils::getCollectionByRecord($result));
?>
<fieldset>
    <legend>Outros Proprietários adicionados</legend>
    <table class="form-container" border="1">
        <thead>
            <tr style="background-color: #e1e1e1">
                <th class="text-center">Numcgm</th>
                <th class="text-left">Proprietário</th>
                <th class="text-left">Tipo do Proprietário</th>
                <th class="text-left">Fração</th>
                <th class="text-left">Área</th>
                <th class="text-center">Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < $num; $i++) {
                db_fieldsmemory($result, $i);
                $sql = "select j163_descricao from tipoproprietario where j163_tipoproprietario = $j42_tipoproprietario";
                $getTipoProprietario = db_query($sql);
                db_fieldsmemory($getTipoProprietario, 0);

                $sWhereFracaoProprietario = "j42_matric = $j42_matric AND j42_numcgm = $j42_numcgm";
                $SCamposFracaoProprietario = "ROUND(COALESCE(sum(j42_fracaoproprietario), 0), 4) as fracao, COALESCE(sum(j42_arealoteproprietario), 0) as area";
                $sSqlFracaoProprietario = $clpropri->sql_query_file($j42_matric, null, $SCamposFracaoProprietario, null, $sWhereFracaoProprietario);
                $rsFracaoProprietario = db_query($sSqlFracaoProprietario);

                $fracaoProprietario = db_utils::getCollectionByRecord($rsFracaoProprietario)[0];

                $percentualFracao = $fracaoProprietario->fracao . '%';
                $areaLote = $fracaoProprietario->area . 'm²';

                if ($j42_numcgm) {
            ?>
                    <tr class="cores">
                        <td class="numcgmproprietario text-center field-size2"><?= $j42_numcgm ?></td>
                        <td class="text-left field-size8"><?= $z01_nome ?></td>
                        <td class="descricaoproprietario text-left"><?= $j163_descricao ?></td>
                        <td class="descricaoproprietario text-left"><?= $percentualFracao ?></td>
                        <td class="descricaoproprietario text-left"><?= $areaLote ?></td>
                        <td class="text-center">
                            <input id="numcgm" name="numcgm" type="hidden" value="<?= $j42_numcgm ?>">
                            <input id="cgccpf" name="cgccpf" type="hidden" value="<?= $z01_cgccpf ?>">
                            <input type="button" href="#" title="Alterar proprietário." onclick="js_trocaid(<?= $j42_numcgm ?>)" value="A"> 
                            <?php
                                if(!$mostraApenasFracao){
                            ?>
                            |
                            <input name="excluir" type="button" title="Excluir proprietário." onclick="return js_excluiProprietario(<?= $j42_matric ?>,<?= $j42_numcgm ?>)" value="E">
                            <?php
                                }
                            ?>
                        </td>
                    </tr>
            <?php }
            } ?>
        </tbody>
    </table>
</fieldset>
<script>
    $('j42_matric').classList.add('field-size2');
    $('z01_nomematri').classList.add('field-size9');
    $('j42_numcgm').classList.add('field-size2');
    $('z01_nome').classList.add('field-size9');
    <?php if ($j01_tipoimovel == 2) { ?>
        $('j166_percentual').classList.add('field-size9');
    <?php } ?>

    const tipoproprietario = parseInt(<?= $tipoproprietario ?>);

    js_listaTipoProprietario();

    function js_listaTipoProprietario() {
        var oParam = new Object();
        oParam.executa = "lista";

        new AjaxRequest("cad1_tipoproprietario.RPC.php", oParam, js_getTipoProprietario).execute();
    }

    function js_getTipoProprietario(oRetorno) {
        if (oRetorno.mensagem != "") {
            alert(oRetorno.mensagem);
        }

        if (oRetorno.erro) {
            return;
        }

        const lista = document.getElementById("listaProprietario");
        lista.innerHTML = "";

        var select = document.createElement("select");
        select.setAttribute("id", "j42_tipoproprietario");
        select.setAttribute("name", "j42_tipoproprietario");

        <?php
            if($mostraApenasFracao){
        ?>
        select.setAttribute("disabled", 'true');
        <?php
            }
        ?>

        lista.appendChild(select);

        for (var index = 0; index < oRetorno.lista.length; index++) {
            var option = document.createElement("option");
            option.setAttribute("id", "j42_tipoproprietario_" + oRetorno.lista[index].j163_tipoproprietario);
            option.setAttribute("value", oRetorno.lista[index].j163_tipoproprietario);
            option.setAttribute("pessoaFisica", Number(oRetorno.lista[index].j163_pesfisjur) === 1);
            option.classList.add('opcaotipoproprietario');
            var t = document.createTextNode(oRetorno.lista[index].j163_descricao);
            option.appendChild(t);
            select.appendChild(option);
        }

        selecionaTipoProprietario();

        if (tipoproprietario != '') {
            var option = document.getElementById("j42_tipoproprietario_" + tipoproprietario);
            option.selected = "true";
        }
    }

    function js_pesquisaj42_numcgm(mostra) {
    if (mostra == true) {
      js_OpenJanelaIframe('CurrentWindow.corpo.iframe_propri', 'func_nome', 'func_nome.php?testanome=true&funcao_js=parent.js_mostranumcgm1|z01_numcgm|z01_nome', 'Pesquisa', true);
    } else {
      if (document.form1.j42_numcgm.value != '') {
        js_OpenJanelaIframe('CurrentWindow.corpo.iframe_propri', 'func_nome', 'func_nome.php?testanome=true&pesquisa_chave=' + document.form1.j42_numcgm.value + '&funcao_js=parent.js_mostranumcgm', 'Pesquisa', false);
      } else {
        document.form1.z01_nome.value = "";
      }
    }
  }

  function js_mostranumcgm(erro, chave) {
    document.form1.z01_nome.value = chave;
    if (erro == true) {
      document.form1.j42_numcgm.value = '';
      document.form1.j42_numcgm.focus();
    }
  }

  function js_mostranumcgm1(chave1, chave2) {
    document.form1.j42_numcgm.value = chave1;
    document.form1.z01_nome.value = chave2;
    func_nome.hide();
  }

    function js_cgm(mostra) {
        if (mostra == true) {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_propri', 'func_nome', 'func_nome.php?funcao_js=parent.js_mostra1|0|1&testanome=true', 'Pesquisa', true, 0);
        } else {
            js_OpenJanelaIframe('CurrentWindow.corpo.iframe_propri', 'func_nome', 'func_nome.php?pesquisa_chave=' + document.form1.j42_numcgm.value + '&funcao_js=parent.js_mostra', 'Pesquisa', false, 0);
        }
    }

    function js_mostra1(chave1, chave2) {
        document.form1.j42_numcgm.value = chave1;
        document.form1.z01_nome.value = chave2;
        func_nome.hide();
    }

    function js_validaPorcentagem() {
        var porcentagem = document.form1.j42_fracaoproprietario.value;

        if (porcentagem > 100) {
            alert('Fração não pode ser maior que 100%');
            document.form1.j42_fracaoproprietario.value = 0;
        }

        js_formataPorcentagem();
    }

    function js_formataPorcentagem() {
        const numeroDeCaracteresAposPonto = 4;
        const campoPorcentagem = document.form1.j42_fracaoproprietario;
        const valorPorcentagem = (campoPorcentagem.value).toString();
        const splitPorcentagem = valorPorcentagem.trim().split('.');

        if (splitPorcentagem[1] && splitPorcentagem[1].length > numeroDeCaracteresAposPonto) {
            splitPorcentagem[1] = splitPorcentagem[1].slice(0, numeroDeCaracteresAposPonto);
            campoPorcentagem.value = splitPorcentagem.join('.');
        }
    }

    function js_mostra(erro, chave) {
        document.form1.z01_nome.value = chave;
        if (erro == true) {
            document.form1.j42_numcgm.focus();
            document.form1.j42_numcgm.value = "";
        }
    }

    function toggleButtons() {
        const cgms = document.querySelectorAll('.numcgmproprietario');
        const inputCgm = document.getElementById('j42_numcgm');
        const botaoIncluir = document.getElementById('incluir')
        const botaoAlterar = document.getElementById('alterar')
        const valorInputCgm = inputCgm.value;
        const cgmProprietarios = []

        cgms.forEach(item => {
            cgmProprietarios.push(Number(item.innerText));
        })

        if (cgmProprietarios.includes(Number(valorInputCgm))) {
            botaoIncluir.disabled = true;
            botaoAlterar.disabled = false;
        } else {
            botaoIncluir.disabled = false;
            botaoAlterar.disabled = true;
        }
    }

    function selecionaTipoProprietario() {
        const inputCgm = document.getElementById('j42_numcgm');
        const numcgmproprietarios = document.querySelectorAll('.numcgmproprietario');
        const selectTipoProprietaro = document.getElementById('j42_tipoproprietario');

        numcgmproprietarios.forEach(item => {
            const numCgm = item.innerText
            const tipoproprietario = item.parentElement.querySelector('.descricaoproprietario').innerText

            if (Number(numCgm) === Number(inputCgm.value)) {
                selectTipoProprietaro.childNodes.forEach(option => {
                    if (option.innerText === tipoproprietario) {
                        option.selected = "true";
                    }
                })
            }
        })
    }

    function filtraTiposDeProprietariosParaPessoaJuridica() {
        const inputCgm = document.getElementById('j42_numcgm');
        const numcgmproprietarios = document.querySelectorAll('.numcgmproprietario');
        let pessoaJuridica = false

        numcgmproprietarios.forEach(item => {
            const numCgm = item.innerText

            if (Number(numCgm) === Number(inputCgm.value)) {
                const cgccpfProprietarioSelecionado = item.parentElement.querySelector('#cgccpf').value

                if (cgccpfProprietarioSelecionado.length === 14) {
                    pessoaJuridica = true;
                }
            }
        })

        return pessoaJuridica;
    }

    function alteraProprietario() {
        if (filtraTiposDeProprietariosParaPessoaJuridica()) {
            const tipoProprietaroSelecionado = document.getElementById('j42_tipoproprietario').value;
            const options = document.querySelectorAll('.opcaotipoproprietario');
            let tipoDeProprietarioParaPessoaFisica = false;

            options.forEach(option => {
                if (Number(option.value) === Number(tipoProprietaroSelecionado)) {
                    if (option.getAttribute('pessoaFisica') == 'true') {
                        tipoDeProprietarioParaPessoaFisica = true
                    }
                }
            })

            if (tipoDeProprietarioParaPessoaFisica) {
                alert('Tipo de proprietário inválido para pessoa jurídica');
                return;
            }
        }

        const botaoAterar = document.getElementById('alterar');
        js_verizero('alteracao');

        botaoAterar.onclick = "return js_verizero('alteracao')";
        botaoAterar.type = "submit";
        botaoAterar.click();
    }

    toggleButtons();
</script>