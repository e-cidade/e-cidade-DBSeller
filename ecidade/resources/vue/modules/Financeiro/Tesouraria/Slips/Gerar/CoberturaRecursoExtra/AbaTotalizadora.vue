<script setup>
import {ref, watch} from "vue";
import {useToast} from "primevue/usetoast";
import {useConfirm} from "primevue/useconfirm";
import ConfirmPopup from 'primevue/confirmpopup';
import Dialog from 'primevue/dialog';
import {formatCurrency} from "../../../../../../utils/Strings";
import ModalLoading from "../../../../../Components/ModalLoading.vue";

const props = defineProps(['dataInicial', 'dataFinal', 'contas', 'dadosEmpenho', 'dadosPlanilha']);
const toast = useToast();
const confirm = useConfirm();

const dataInicial = props.dataInicial;
const dataFinal = props.dataFinal;
const dadosEmpenho = props.dadosEmpenho;
const dadosPlanilha = props.dadosPlanilha;

const loading = ref(false);
const mensagemLoad = ref(null);
// grid
const expandedRows = ref([]);
const itensSelecionados = ref([]);
const showTabelaAgrupados = ref(false);
const showTabelaOrigem = ref(false);
// dados na grid por origem
const dadosPorOrigem = ref([]);
// dados na grid agrupado
const dadosAgrupados = ref([]);
const visibleDialog = ref(false);
const mensagemDialog = ref('');
const headerDialog = ref('');
const slipsGerados = ref([]);

// combobox
const agrupar = ref(0);
const options = ref([
    {code: 0, label: 'Selecione uma opção'},
    {code: 1, label: 'Debito/Credito'},
    {code: 2, label: 'Por Origem'},
]);

const rotaGerarSlips = 'v4/api/financeiro/tesouraria/slip/cobertura-recurso-extra/gerar';

const confirmaAcao = (event) => {
    return new Promise((accept, reject) => {
        confirm.require({
            target: event.currentTarget,
            message: 'Confirma geração dos slips das apropriações selecionadas?',
            icon: 'pi pi-exclamation-triangle',
            accept,
            reject
        });
    }).then(() => true).catch(() => false);
}

/**
 * Objeto simplificado para padronizar a geração dos slips.
 * origem: Empenho/ Planilha
 * identificador: no caso de Empenho é a OP e na planilha o código da Planilha
 * id: chave identificadora para identificar o registro.
 *  - No empenho: empagemovslips.k107_sequencial
 *    Na planilha: placaixarec.k81_seqpla
 * @param dados
 * @returns {*[]}
 */
const montaComposicao = (dados) => {
    const composicao = []
    for (const dado of dados) {
        const c = {
            origem: dado.origem,
            identificador: dado.codigo, //
            id: null
        }
        if (dado.origem === 'Empenho') {
            c.id = dado.empagemovslips_id;
        }
        if (dado.origem === 'Planilha') {
            c.id = dado.lancamento;
        }

        composicao.push(c);
    }

    return composicao;
};

/**
 * Retorna um array simplificado com os dados necessários para geração do slip
 * @param apropriar
 * @returns {*[]}
 */
const getComposicaoAproriacao = (apropriar) => {
    if (apropriar.apropriacoes === undefined) {
        return montaComposicao([apropriar])
    }

    return montaComposicao(apropriar.apropriacoes);
}

const gerarSlips = async (event) => {
    if (itensSelecionados.value.length === 0) {
        toast.add({
            severity: 'warn',
            detail: 'Selecione ao menos um registro na tabela',
            summary: 'Aviso'
        });
        return
    }

    if (!await confirmaAcao(event)) {
        return
    }

    loading.value = true;
    mensagemLoad.value = 'Gerando Slips, aguarde.';

    const dados = [];

    for (const apropriar of itensSelecionados.value) {
        const composisao = getComposicaoAproriacao(apropriar);

        let dado = {
            creditar: apropriar.creditar,
            debitar: apropriar.debitar,
            valor: apropriar.valor,
            composisao: composisao,
        };

        dados.push(dado);
    }

    const obj = {
        dados: dados,
        dataInicial: dataInicial.value.toLocaleDateString('pt-BR', {timeZone: 'America/Sao_Paulo'}),
        dataFinal: null
    };
    if (dataFinal.value !== undefined) {
        obj.dataFinal = dataFinal.value.toLocaleDateString('pt-BR', {timeZone: 'America/Sao_Paulo'});
    }

    await window.axios.post(rotaGerarSlips, obj).then(response => {
        let msg = response.data.message;

        visibleDialog.value = true
        mensagemDialog.value = msg;
        slipsGerados.value = response.data.data;
        headerDialog.value = "Sucesso"
        removerSelecionados();
    }).catch(response => {
        toast.add({severity: 'error', detail: response.data.message, summary: 'Erro'});
    }).finally(() => loading.value = false);
};

const imprimirPdf = () => {
    const urlMatch = window.location.href.match(/[^\r\n]+(w\/[0-9]+)+/g);
    const locationUrl = urlMatch !== null ? urlMatch[0] : '';
    let lista = slipsGerados.value.join(',')
    window.open(`${locationUrl}/cai1_slip003.php?numslip=${lista}`,'', 'location=0');
};

const removerOrigem = dado => {
    if (dado.origem === 'Empenho') {
        let index = dadosEmpenho.value.findIndex(valor => {
            return dado.empagemovslips_id === valor.empagemovslips_id
        });
        dadosEmpenho.value.splice(index, 1);
    }

    if (dado.origem === 'Planilha') {
        let index = dadosPlanilha.value.findIndex(valor => {
            return dado.lancamento === valor.lancamento
        });
        dadosPlanilha.value.splice(index, 1);
    }
}

const removerSelecionados = () => {
    for (const apropriar of itensSelecionados.value) {
        if (apropriar.apropriacoes === undefined) {
            removerOrigem(apropriar);
        } else {
            for (let dado of apropriar.apropriacoes) {
                removerOrigem(dado);
            }
        }
    }
    agruparDados();
};

const criaPrimeiroNivel = (creditar, creditar_descricao, debitar, debitar_descric, recurso) => {
    return {
        creditar: creditar,
        creditar_descricao: creditar_descricao,
        debitar: debitar,
        debitar_descricao: debitar_descric,
        recurso: recurso,
        apropriacoes: [],
        valor: 0
    }
}

const agruparRecurso = (id, siconfi, subrecurso, complemento) => {
    return {
        recurso: id,
        siconfi: siconfi,
        subrecurso: subrecurso,
        complemento: complemento
    }
}
const montarDadosAgrupado = () => {
    if (dadosEmpenho.value.length > 0) {
        for (const apropriacao of dadosEmpenho.value) {
            if (apropriacao.contaSelecionada === null) {
                continue;
            }
            const recurso = agruparRecurso(apropriacao.recurso, apropriacao.siconfi, apropriacao.subrecurso, apropriacao.complemento);
            let nivel = dadosAgrupados.value.filter(agrupado => {
                return (agrupado.creditar == apropriacao.creditar &&
                    agrupado.debitar == apropriacao.debitar &&
                    agrupado.recurso.recurso == apropriacao.recurso)
            }).shift();

            if (!nivel) {
                nivel = criaPrimeiroNivel(
                    apropriacao.creditar,
                    apropriacao.creditar_descricao,
                    apropriacao.debitar,
                    apropriacao.debitar_descricao,
                    recurso
                );

                dadosAgrupados.value.push(nivel);
            }

            apropriacao.origem = 'Empenho';
            apropriacao.codigo = apropriacao.op;
            nivel.apropriacoes.push(apropriacao);
            nivel.valor += Number(apropriacao.valor);
        }
    }

    if (dadosPlanilha.value.length > 0) {
        for (const apropriacao of dadosPlanilha.value) {
            const recurso = agruparRecurso(apropriacao.recurso, apropriacao.siconfi, apropriacao.subrecurso, apropriacao.complemento);
            let nivel = dadosAgrupados.value.filter(agrupado => {
                return (agrupado.creditar == apropriacao.creditar &&
                    agrupado.debitar == apropriacao.debitar &&
                    agrupado.recurso.recurso == apropriacao.recurso)
            }).shift();

            if (!nivel) {
                nivel = criaPrimeiroNivel(
                    apropriacao.creditar,
                    apropriacao.creditar_descricao,
                    apropriacao.debitar,
                    apropriacao.debitar_descricao,
                    recurso
                );

                dadosAgrupados.value.push(nivel);
            }

            apropriacao.origem = 'Planilha';
            apropriacao.codigo = apropriacao.planilha;
            nivel.apropriacoes.push(apropriacao);
            nivel.valor += Number(apropriacao.valor);
        }
    }
}

const montarDadosPorOrigem = () => {
    for (const apropriacao of dadosEmpenho.value) {
        if (apropriacao.contaSelecionada === null) {
            continue;
        }
        apropriacao.origem = 'Empenho';
        apropriacao.codigo = apropriacao.op;
        dadosPorOrigem.value.push(apropriacao)
    }
    for (const apropriacao of dadosPlanilha.value) {
        apropriacao.origem = 'Planilha';
        apropriacao.codigo = apropriacao.planilha;
        dadosPorOrigem.value.push(apropriacao)
    }
}

const agruparDados = () => {
    itensSelecionados.value.length = 0;
    dadosAgrupados.value.length = 0;
    dadosPorOrigem.value.length = 0;
    loading.value = true;

    switch (agrupar.value) {
        case 1:
            montarDadosAgrupado();
            showTabelaAgrupados.value = true;
            showTabelaOrigem.value = false;
            break;

        case 2:
            montarDadosPorOrigem();
            showTabelaAgrupados.value = false;
            showTabelaOrigem.value = true;
            break;
        default:
            showTabelaAgrupados.value = false;
            showTabelaOrigem.value = false;
    }

    loading.value = false;
};

const getSeverity = (origem) => {
    switch (origem.toLowerCase()) {
        case 'empenho':
            return 'success';
        case 'planilha':
            return 'warning';
        default:
            return null;
    }
};

watch([dadosEmpenho, dadosPlanilha], () => {
    if (dadosEmpenho.value === null || dadosPlanilha.value === null) {
        return;
    }
    agruparDados();
});

</script>

<template>
    <Message severity="info" :closable="false" :style="{marginTop:0}">
        <div style='font-size: 10pt; '>
            Valores apurados onde poderão ser gerados os documentos "Slip" que registrarão os valores a serem transferidos para as contas de recursos extra orçamentários com a finalidade de dar a devida cobertura financeira a seu posterior pagamento.<br>
            Para gerar os slips, primeiro selecione uma forma de agrupamento dos dados. Depois selecione um ou mais itens na grid que irá aparecer.
        </div>
    </Message>

    <section class="flex flex-column w-full mt-1">
        <section class="flex justify-content-center mb-2">
            <div class="card">
                <div class="flex justify-content-start flex-wrap card-container">
                    <div class="flex align-items-center">
                        <label class="font-bold">Agrupar por:</label>
                    </div>
                    <div class="flex align-items-center pl-2">
                        <Dropdown v-model="agrupar" :options="options" optionValue="code" optionLabel="label"
                                  placeholder="Selecione" class="w-full" @change="agruparDados">
                        </Dropdown>
                    </div>
                </div>
            </div>
        </section>
    </section>
    <section>
        <!-- Tabela com os dados agrupados -->
        <DataTable v-model:expandedRows="expandedRows" v-model:selection="itensSelecionados" :value="dadosAgrupados"
                   tableStyle="min-width: 50rem" v-show="showTabelaAgrupados" scrollable scrollHeight="400px">
            <Column expander style="width: 3rem"/>
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="debitar" header="Débito">
                <template #body="slotProps">
                    {{ `${slotProps.data.debitar} - ${slotProps.data.debitar_descricao}` }}
                </template>
            </Column>
            <Column field="creditar" header="Crédito">
                <template #body="slotProps">
                    {{ `${slotProps.data.creditar} - ${slotProps.data.creditar_descricao}` }}
                </template>
            </Column>
            <Column field="recurso.siconfi" header="Siconfi"></Column>
            <Column field="recurso.subrecurso" header="Subrecurso"></Column>
            <Column field="recurso.complemento" header="Complemento"></Column>
            <Column field="valor" header="Valor">
                <template #body="slotProps">
                    {{ formatCurrency(slotProps.data.valor) }}
                </template>
            </Column>
            <template #expansion="slotProps">
                <div class="pl-5">
                    <DataTable :value="slotProps.data.apropriacoes" id="subtable">
                        <Column field="origem" header="Origem">
                            <template #body="slotProps">
                                <Tag :value="slotProps.data.origem" :severity="getSeverity(slotProps.data.origem)"/>
                            </template>
                        </Column>
                        <Column field="codigo" header="OP/Planilha"></Column>
                        <Column field="credor" header="Credor">
                            <template #body="slotProps">
                                {{ `${slotProps.data.cgm} - ${slotProps.data.nome_credor}` }}
                            </template>
                        </Column>
                        <Column field="tipo_retencao" header="Tipo de Retenção"></Column>
                        <Column field="valor" header="Valor">
                            <template #body="slotProps">
                                {{ formatCurrency(slotProps.data.valor) }}
                            </template>
                        </Column>
                    </DataTable>
                </div>
            </template>
        </DataTable>

        <DataTable v-model:selection="itensSelecionados" :value="dadosPorOrigem" tableStyle="min-width: 50rem"
                   v-show="showTabelaOrigem" scrollable scrollHeight="400px">
            <Column selectionMode="multiple" headerStyle="width: 3rem"></Column>
            <Column field="origem" header="Origem">
                <template #body="slotProps">
                    <Tag :value="slotProps.data.origem" :severity="getSeverity(slotProps.data.origem)"/>
                </template>
            </Column>
            <Column field="codigo" header="OP/Planilha"></Column>
            <Column field="tipo_retencao" header="Tipo de Retenção"></Column>
            <Column field="debitar" header="Débito">
                <template #body="slotProps">
                    {{ `${slotProps.data.debitar} - ${slotProps.data.debitar_descricao}` }}
                </template>
            </Column>
            <Column field="creditar" header="Crédito">
                <template #body="slotProps">
                    {{ `${slotProps.data.creditar} - ${slotProps.data.creditar_descricao}` }}
                </template>
            </Column>
            <Column field="siconfi" header="Siconfi"></Column>
            <Column field="subrecurso" header="Subrecurso"></Column>
            <Column field="complemento" header="Complemento"></Column>
            <Column field="valor" header="Valor">
                <template #body="slotProps">
                    {{ formatCurrency(slotProps.data.valor) }}
                </template>
            </Column>
        </DataTable>

        <div v-if="itensSelecionados.length" class="flex justify-content-center flex-wrap card-container">
            <div class="flex align-items-center pt-2">
                <Button @click="gerarSlips($event)" icon="pi pi-cog" label="Gerar Slips"/>
            </div>
        </div>
    </section>

    <section>
        <!-- Dialog -->
        <Dialog v-model:visible="visibleDialog" modal :header="headerDialog" :style="{ width: '50vw' }">
            <p>
                {{ mensagemDialog}}
            </p>
            <template #footer>
                <Button label="Imprimir Slips" @click="imprimirPdf" icon="pi pi-print" autofocus></Button>
            </template>
        </Dialog>

        <ConfirmPopup></ConfirmPopup>
        <ModalLoading :isLoading="loading" :message="mensagemLoad"/>
    </section>
</template>

<style scoped>

</style>
