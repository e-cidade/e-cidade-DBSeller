class GridR4010 {

    constructor (retencoes, grid, closingDates) {
        this.closingDates = closingDates;
        this.build(retencoes, grid);
    }

    build(retencoes, grid) {
        let data = [];

        // header grid
        const columns = [
            { field: "status", title: "Status", align: "center" },
            { field: "statusCode", title: "Status", visible: false },
            { field: "benef", title: "Beneficiário", align: "center" },
            { field: "fato_gerador", title: "Data do Pagamento", align: "center" },
            { field: "empenho", title: "Empenho", align: "center" },
            { field: "ordem_pagamento", title: "OP", align: "center"},
            { field: "nota_fiscal", title: "Nota Fiscal", align: "center"},
            { field: "subcontratacao", title: "Possui Subcontratações", align: "center"},
            { field: "tipo_retencao", title: "Retenção", align: "center" },
            { field: "valor_bruto", title: "Valor Bruto", align: "center" },
            { field: "valor_base", title: "Valor Tributável", align: "center" },
            { field: "deducao", title: "Dedução", align: "center" },
            { field: "valor_retencao", title: "Valor Retencao", align: "center" },
            { field: "codnatureza_rendimento", title: "Natureza Rendimento", align: "center" },
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
                row.status = "<span class='ok-row' title='Item de acordo para o processamento do R-4010'><i class='fa fa-check-circle'></i>";
            }

            row.fato_gerador = js_formatar(item.fato_gerador, 'd');
            row.benef = item.benef;
            row.empenho = item.empenho;
            row.valor_bruto = js_formatar(Number(item.valor_base) + Number(item.deducao), 'f');
            row.valor_base = js_formatar(item.valor_base, 'f');
            row.valor_retencao = js_formatar(item.valor_retencao, 'f');
            row.ordem_pagamento = item.ordem_pagamento;
            row.nota_fiscal = item.nota_fiscal;
            row.subcontratacao = item.subcontratados ? 'Sim' : 'Não';
            row.tipo_retencao = item.tipo_retencao;
            row.codnatureza_rendimento = item.codnatureza_rendimento;
            row.orgunid = item.orgao_unidade;
            row.deducao = item.deducao;
            row.acao = `<button onclick="js_manutencaoRentecao(this, 'r4010')" data-retencao='${JSON.stringify(item)}'>Alterar</button>`;

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
                name: 'Natureza de rendimento',
                value: item.codnatureza_rendimento,
                validate: '^[0-9]+$'
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
            const retencaoCompetence = item.fato_gerador.slice(0, 7);

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
