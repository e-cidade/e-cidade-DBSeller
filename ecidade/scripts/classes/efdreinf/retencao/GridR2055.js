class GridR2055 {
    constructor(retencoes, grid, closingDates) {
        this.closingDates = closingDates;
        this.build(retencoes, grid)
    }

    build(retencoes, grid) {
        let data = [];

        // header grid
        const columns = [
            { field: "status", title: "Status", align: "center" },
            { field: "statusCode", title: "Status", visible: false },
            { field: "nfdata", title: "Data da NF", align: "center" },
            { field: "nfnumero", title: "Número da NF", align: "center" },
            { field: "prestador", title: "Prestador", align: "center" },
            { field: "indaquis", title: "Ind. de Aquisição", align: "center" },
            { field: "vlrbruto", title: "Valor Bruto", align: "center" },
            { field: "vlrsenar", title: "Valor Senar 0,2%", align: "center" },
            { field: "vlrrat", title: "Valor Gilrat 0,1%", align: "center" },
            { field: "vlrcp", title: "Valor CP 1,2%", align: "center" },
            {
                field: "orgunid",
                title: "Órgão Unidade",
                align: "center",
                visible: $F('o40_orgao') || $F('o41_unidade') ? true : false
            },
            { field: "acao", title: "Ação", align: "center" }
        ];

        // body grid
        retencoes.forEach(item => {
            let rowValidate = this.validate(item);
            let row = {};

            if (rowValidate.erros.length > 0) {
                row.statusCode = 0;
                row.status = `
                <a href="#" onclick='js_errosRetencao(this)'
                    data-erros='${JSON.stringify(rowValidate.erros)}'
                    class='${rowValidate.class}' title="Clique para visualizar">
                        <i class="fa fa-exclamation-triangle"></i>
                </a>`;
            } else {
                row.statusCode = 1;
                row.status = "<span class='ok-row' title='Item de acordo para o processamento do R-2055'><i class='fa fa-check-circle'></i>";
            }

            row.nfdata = js_formatar(item.data_nota, 'd');
            row.nfnumero = item.nfnumero;
            row.prestador = item.prestador;
            row.vlrbruto = item.vlrBruto ? js_formatar(item.vlrBruto, 'f') : '-';
            row.vlrsenar = item.e158_vlrsenar ? js_formatar(item.e158_vlrsenar, 'f') : '-';
            row.vlrrat = item.e158_vlrrat ? js_formatar(item.e158_vlrrat, 'f') : '-';
            row.vlrcp = item.e158_vlrcp ? js_formatar(item.e158_vlrcp, 'f') : '-';
            row.indaquis = item.indAqProd === null ? '-' : String(item.indAqProd);
            row.acao = `<button onclick="js_manutencaoRentecao(this, 'r2055')" data-retencao='${JSON.stringify(item)}'>Alterar</button>`;

            data.push(row);
        });

        grid.bootstrapTable('destroy');
        grid.bootstrapTable({
            data: data,
            locale: 'pt-BR',
            search: true,
            searchHighlight: true,
            columns: columns,
            buttons: buttons,
            showButtonText: true
        });
    }

    validate(item) {
        const erros = [];
        const filters = [
            {
                name: 'Valores da retenção (senar, gilrat, cp)',
                value: item.e158_sequencial,
                validate: '^[1-9][0-9]*$'
            },
            {
                name: 'Indicativo de aquisição',
                value: item.indAqProd,
                validate: '^[1-7]$'
            }
        ];

        // validacoes de regex
        filters.forEach(i => {
            let validate = new RegExp(i.validate);
            let value = i.value;

            if (typeof value == 'string') {
                value = i.value.trim();
            }

            if (!validate.test(value)) {
                erros.push(i.name);
            }
        });

        // checar se foi lancado pos fechamento
        if (this.closingDates) {
            const retencaoDate = new Date(item.data_calculo + ' 00:00');
            const retencaoCompetence = item.data_nota.slice(0, 7);

            for (let [closingCompetence, closingDate] of Object.entries(this.closingDates)) {
                if (!closingCompetence || !closingDate) continue;
                if (retencaoCompetence === closingCompetence) {
                    closingDate = new Date(closingDate);
                    if (retencaoDate > closingDate) {
                        erros.push('Retenção lançada depois do fechamento: ' + closingCompetence);
                    }
                }
            }
        }

        let className = '';
        if (erros.length > 0) {
            className = 'bad-row'
        }

        return {
            class: className,
            erros: erros
        }
    }
}
