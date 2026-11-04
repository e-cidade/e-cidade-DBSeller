EmitirBICView = function (windowAux, codigoInscricao) {
    instance = this;
    instance.codigoInscricao = codigoInscricao;
    instance.windowAux = windowAux;
    instance.windowAux.setShutDownFunction(() => {
        instance.windowAux.destroy();
    });

    instance.createContent = () => {
        const content = `
            <div class='container'>
                <fieldset>
                    <legend>Filtros</legend>
                    <table class='table-container'>
                        <tr>
                            <td>
                                <label><b>Atividades: </b></label>
                            </td>
                            <td>
                                <select id='bicAtividades'>
                                    <option value='1' slected>Todas</option>
                                    <option value='2'>Somente Ativas</option>
                                    <option value='3'>Somente Baixadas</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </fieldset>
                <br/>
                <button onclick="instance.emit()">
                    <i class="fa fa-print"></i>
                    Imprimir
                </button>
            </div>
        `;

        return content;
    }

    instance.emit = () => {
        instance.atividades = document.querySelector('#bicAtividades').value;
        const url = `iss2_imprimebiciss.php?inscr=${instance.codigoInscricao}&atividades=${instance.atividades}`;
        window.open(url, '', 'location=0,HEIGHT=600,WIDTH=600');
    }

    instance.show = () => {
        instance.windowAux.setContent(instance.createContent());
        instance.windowAux.show();
    }
}
