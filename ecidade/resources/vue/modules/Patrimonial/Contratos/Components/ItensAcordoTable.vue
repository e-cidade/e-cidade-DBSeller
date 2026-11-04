<template>
    <Dialog header="Alterar Dotação" v-model:visible="alterarDotacaoVisible"  >
        <AlterarDotacaoForm
            @salvarAlteracoes="salvarAlteracoes"
            :desdobramentos="desdobramentos"
            :desdobramentoSelecionado="desdobramentoSelecionado"
            :itemSelecionado="itemMostrar"
        />
    </Dialog>
    <ModalLoading :isLoading="loadingDotacoes" :message="'Carregando Dotações'"/>
    <DataTable
        :value="dadosTabela"
        scrollable
        v-model:selection="itemSelecionado"
        sortMode="single"
        scrollHeight="20em"
        :tableStyle="{'background-color':'#e1dede','height': '100%', 'width': '100%', 'table-layout': 'fixed' }"    >
        <Column selectionMode="multiple" style="width: 5%"  />
        <Column field="ac20_pcmater"  header="Código" style="width: 10%" />
        <Column field="ac20_ordem" sortable header="Ordem"  style="width: 7%" />
        <Column field="pc01_descrmater"  header="Material" style="width: 20%">
            <template #body="{data}">
                <span v-tooltip="{value: data['ac20_resumo'], class:'tooltip-class' }">{{ data['pc01_descrmater'] }}</span>
            </template>
        </Column>
        <Column field="ac20_quantidade"  header="Quantidade" style="width: 10%" />
        <Column field="ac20_valortotal"  header="Valor Total" style="width: 10%" >
            <template #body="{data,field}">
                {{ formatarValor(data[field]) }}
            </template>
        </Column>
        <Column field="valorAutorizar" header="Saldo a Autorizar" style="width: 10%" >
            <template #body="{data,field}">
                {{ formatarValor(data[field]) }}
            </template>
        </Column>
        <Column style="width: 10%">
            <template #body="rowData">
                <Button
                    label="Dotações"
                    size="small"
                    @click="mostrarDotacoes(rowData)"
                    :disabled="bloqueiaBotao(rowData)"
                />
            </template>
        </Column>
    </DataTable>
</template>

<script>
import { ref, computed,watch } from "vue";
import DataTable from "primevue/datatable";
import Button from "primevue/button";
import AlterarDotacaoForm from './AlterarDotacaoForm';
import ModalLoading from '../../../Components/ModalLoading';
import Tooltip from "primevue/tooltip";

export default {
    name: "ItensAcordoTable",
    emits:['selecionarItem'],
    directives: {
        'tooltip': Tooltip
    },
    components: {
        DataTable,
        Button,
        AlterarDotacaoForm,
        ModalLoading
    },
    props: {
        dadosTabela: {
            type: Array,
            required: true,
        },
    },

    setup(props,{emit}) {
        const loadingDotacoes = ref(false);
        const itemSelecionado = ref(null);
        const desdobramentos = ref(null);
        const desdobramentoSelecionado = ref(null);
        const itemMostrar = ref(null);
        const dadosTabela = computed(() => {
            return props.dadosTabela;
        });
        const alterarDotacaoVisible = ref(false);

        async function mostrarDotacoes (event) {
            itemMostrar.value = dadosTabela.value[event.index];
            loadingDotacoes.value = true;
            const dadosDesdobramento = await window.axios.get(
                'v4/api/financeiro/orcamento/get-desdobramento/' + event.data.pc01_codmater
            );
            dadosDesdobramento.data.data.forEach(obj => {
                obj.descricao = obj.o56_codele + ' - ' +  obj.o56_elemento + ' - ' + obj.o56_descr
                obj.elemento = obj.o56_elemento.substr(0,7)
            });

            desdobramentos.value = dadosDesdobramento.data.data

            const desdobramentoDefault = await window.axios.get(
                'v4/api/financeiro/orcamento/get-desdobramento-item/'
                + event.data.ac20_elemento
            )
            desdobramentoSelecionado.value = desdobramentoDefault.data.data[0];
            desdobramentoSelecionado.value.descricao = desdobramentoSelecionado.value.o56_codele + ' - ' +
                desdobramentoSelecionado.value.o56_elemento + ' - ' + desdobramentoSelecionado.value.o56_descr;
            desdobramentoSelecionado.value.elemento = desdobramentoSelecionado.value.o56_elemento.substr(0,7);
            alterarDotacaoVisible.value = true;
            loadingDotacoes.value = false;


        }



        function formatarValor (value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
        }


        function bloqueiaBotao(rowData) {
            return rowData.data.valorAutorizar == 0;
        }

        watch(itemSelecionado, () => {
           emit('selecionarItem',itemSelecionado);
        })

        function salvarAlteracoes (data) {
            alterarDotacaoVisible.value = false;

        }



        return {
            dadosTabela,
            itemSelecionado,
            mostrarDotacoes,
            formatarValor,
            alterarDotacaoVisible,
            bloqueiaBotao,
            desdobramentos,
            salvarAlteracoes,
            desdobramentoSelecionado,
            itemMostrar,
            loadingDotacoes
        };
    },
};
</script>
<style>
.p-tooltip > *, .p-tooltip {
    display: block!important;
    max-width: 50em;
}
.tooltip-class {
    border: 1px solid rgb(255, 221, 0);
    background-color: #FFFFCC;
}
</style>
