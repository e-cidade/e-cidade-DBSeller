class GridR2010 {

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
            { field: "nf", title: "NF", align: "center" },
            { field: "nfdata", title: "Data da NF", align: "center" },
            { field: "prestador", title: "Prestador de Serviço", align: "center" },
            { field: "nfservico", title: "Tipo de Serviço da NF", align: "center" },
            { field: "nfvalor", title: "Valor da NF", align: "center" },
            { field: "nfvalorbase", title: "Valor base", align: "center" },
            { field: "nfvalorbruto", title: "Valor Total", align: "center" },
            { field: "aliquota", title: "Alíquota", align: "center" },
            { field: "retencao", title: "Valor Retido", align: "center" },
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
                row.status = "<span class='ok-row' title='Item de acordo para o processamento do R-2010'><i class='fa fa-check-circle'></i>";
            }

            row.nf = item.numero_nota;
            row.nfdata = js_formatar(item.data_emissao, 'd');
            row.prestador = item.nome_prestador;
            row.nfservico = item.referencia_tipo_servico_desc === null ? '-' : item.referencia_tipo_servico_desc;
            row.nfvalor = js_formatar(item.valor_nota_liq, 'f');
            row.nfvalorbase = js_formatar(item.valor_base_retido, 'f');
            row.nfvalorbruto = js_formatar((Number(item.valor_nota_liq) + Number(item.notas_nao_retidas)).toFixed(2), 'f');
            row.aliquota = item.aliquota + '%';
            row.retencao = js_formatar(item.valor_retencao, 'f');
            row.orgunid = item.orgao_unidade;
            row.acao = `<button onclick="js_manutencaoRentecao(this, 'r2010')" data-retencao='${JSON.stringify(item)}'>Alterar</button>`;

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
                name: 'Número da Nota Fiscal',
                value: item.numero_nota,
                validate: '^[0-9]+$'
            },
            {
                name: 'Indicativo de serviço de Obra',
                value: item.indicativo_obra_tipo,
                validate: '^[0-2]$'
            },
            {
                name: 'Tipo de serviço da nota fiscal',
                value: item.referencia_tipo_servico,
                validate: '^[0-9]+$'
            },
            {
                name: 'Valores das retenções adicionais',
                value: item.receitasadicionais_sequencial,
                validate: '^[1-9][0-9]*$'
            },
            {
                name: 'Número de série nota fiscal',
                value: item.serie_nota,
                validate: '^[A-Za-z0-9]{0,5}$'
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


        // validacoes personalizadas
        let retencao = Number(item.valor_retencao);
        let aliquota = (item.indicativo_cprb === true) ? 0.035 : 0.11;
        let base = (item.indicativo_valor_base === true)
            ? Number(item.valor_base_retido)
            : Number(item.valor_nota_liq) + Number(item.notas_nao_retidas)

        // o valor da retencao lancada nao pode ser maior que equacao de calculo
        // o efdreinf trunca as casas decimais e adiciona 1 centavo para a margem
        if (retencao > ((aliquota * base) + 0.01).toFixed(2)) {
            let percent = (100 * aliquota).toFixed(2);
            let msg = `
                    Retenção de ${js_formatar(retencao, 'f')}
                    maior que ${percent}%
                    de ${js_formatar(base, 'f')}
                `;

            erros.push(msg);
        }

        // checar se foi lancado pos fechamento
        if (this.closingDates) {
            const retencaoDate = new Date(item.data_calculo + ' 00:00');
            const retencaoCompetence = item.data_emissao.slice(0, 7);

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
