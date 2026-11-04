<?php
?>
<div style="width: 630px;" id="divFiltroRecursos">
    <fieldset class="separator" id="fieldsetRecursos" >
        <legend>Filtra Recursos</legend>
        <table class="form-container" >
            <tr title="Pesquise usando o código de gestão.">
                <td>
                    <?php
                    db_ancora("<b>Fonte de Recurso:</b>", "js_pesquisaRecurso(true)", 1);
                    ?>
                </td>
                <td>
                    <?php
                    db_input("iCodigoRecurso", 10, null, false, "hidden", 3);
                    db_input("gestao", 10, null, false, "text", 1, "onchange='js_pesquisaRecurso(false);'");
                    db_input("sDescricaoRecurso", 50, null, true, "text", 3);
                    ?>
                </td>
            </tr>
        </table>
        <br>
        <div id="ctnLancadorRecursos" style="width: 625px;"></div>
    </fieldset>

</div>
<script type="text/javascript" src="scripts/widgets/DBLookUp.widget.js"></script>
<script type="text/javascript" src="scripts/datagrid.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/Collection.widget.js"></script>
<script type="text/javascript" src="scripts/widgets/DatagridCollection.widget.js"></script>

<script>
    const collectionRecurso = new Collection().setId('codigo');
    var gridRecursos = new DatagridCollection(collectionRecurso).configure({
        order: false,
        height: 200
    });


    // document.addEventListener('DOMContentLoaded', function() {
    //     document.getElementById('fieldsetRecursos').style.display = ''
    // }, false);

    function js_pesquisaRecurso(lMostraWindow) {

        if (!lMostraWindow && $('gestao').value == '') {
            $("sDescricaoRecurso").value = '';
            $('complementoRecurso').value = '';
            return
        }

        let param = 'gestao='+ $('gestao').value;
        pesquisaRecurso(param);
    }

    const pesquisaRecurso = (parametroAdicional) => {
        let sUrl = 'func_novosRecursos.php?funcao_js=parent.js_preencheRecurso|o15_codigo|o15_recurso|gestao|codigo_siconfi|descricao|o15_complemento';
        if (parametroAdicional) {
            sUrl += `&${parametroAdicional}`;
        }
        js_OpenJanelaIframe('', 'db_iframe_recurso', sUrl, 'Pesquisa Fonte de Recurso', true);
    };

    function js_preencheRecurso(id, recurso, gestao, siconfi, descricao, complemento) {
        preencheCollection(id, recurso, gestao, siconfi, descricao, complemento);
        db_iframe_recurso.hide();
    }

    gridRecursos.addColumn('recurso', {label: "Subrecurso", width: '12%', align: 'center'});
    gridRecursos.addColumn('siconfi', {label: "SICONFI", width: '12%', align: 'center'});
    gridRecursos.addColumn('gestao', {label: "Gestão", width: '12%', align: 'center'});
    gridRecursos.addColumn('complemento', {label: "Compl.", width: '10%', align: 'center'});
    gridRecursos.addColumn('descricao', {label: "Recurso", width: '45%'});
    gridRecursos.addAction('Remover', 'Remover', (event, linha) => {
        collectionRecurso.remove(linha.codigo);
        gridRecursos.reload();
    }, true, 'fa-trash');
    gridRecursos.show($('ctnLancadorRecursos'));

    const preencheCollection = (id, recurso, gestao, siconfi, descricao, complemento) => {
        collectionRecurso.add({
            "codigo" : id,
            "recurso" : recurso,
            "siconfi" : siconfi,
            "gestao" : gestao,
            "descricao" : descricao,
            "complemento" : complemento
        });

        gridRecursos.reload();
        $('iCodigoRecurso').value = '';
        $('gestao').value = '';
        $('sDescricaoRecurso').value = '';
    };

    /**
     * Carrega os recursos considerados depreciados
     * @type {string}
     */
    const rota = 'financeiro/orcamento/cadastro/recursos/depreciados'

    const buscaRecursos = (exercicio) => {
        gridRecursos.clear();
        HttpClient.get(`${PHPSession.requestApi}/${rota}/${exercicio}`).then(response => {
            if (response.error) {
                alert(response.message);
                return;
            }

            for (let recurso of response.data) {
                preencheCollection(
                    recurso.orctiporec_id,
                    recurso.recurso.o15_recurso,
                    recurso.gestao,
                    recurso.codigo_siconfi,
                    recurso.descricao,
                    recurso.recurso.o15_complemento);
            }
        });
    }

</script>
