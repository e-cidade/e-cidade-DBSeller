<template>
    <div class="form-container" >
        <fieldset>
            <legend>Dados do desdobramento </legend>
            <Dropdown class="p-inputtext-sm md:w-28rem"
                      v-model="desdobramentoSelecionado"
                      :options="desdobramentos"
                      optionLabel="descricao"
                      @change="removerDotacoes"
            />

        </fieldset>
        <fieldset>
            <legend>Adicionar Dotação (Saldo a autorizar: {{formatarValor(valorAutorizar)}}): </legend>
            <div style="margin-top:1em" class="formgroup-inline">
                <div class="field">
                    <PesquisaDotacao
                    :elemento="desdobramentoSelecionado.elemento" @enviaDotacao="recebeDotacao"/>
                    <InputNumber
                        v-model="novaDotacao.codigo"
                        class="p-inputtext-xs"
                        inputId="withoutgrouping"
                        :useGrouping="false"
                        disabled/>
                </div>
                <div>
                    <label for="saldo">Saldo Dotação: </label>
                    <InputNumber v-model="novaDotacao.saldoDotacao" disabled inputId="valor" mode="currency" currency="BRL" class="p-inputtext-sm"/>
                </div>
            </div>
            <div style="margin:0 0 1em 0.28em">
                <label for="valor" style="padding:1em">Valor: </label>
                <InputNumber v-model="valor" inputId="valor" :minFractionDigits="2" :maxFractionDigits="2" class="p-inputtext-sm" style="margin-left:1em"></InputNumber>
            </div>
            <div style="text-align:center">
                <Button size="small" label="Adicionar" @click="adicionarDotacao" />
            </div>
        </fieldset>
        <div style="margin-top:1em">
            <DataTable
                :value="itemAlteracoes.dotacoes"
                editMode="cell"

                @cell-edit-complete="editarCelula"
                tableClass="editable-cells-table"
                :tableStyle="{'width': '47em' }"    >
                <Column field="ac22_coddot" sortable header="Dotação"></Column>
                <Column field="ac22_anousu" sortable header="Exercício"></Column>
                <Column field="ac22_valor" sortable header="Valor">
                    <template #body="{ data, field }">
                        {{ formatarValor(data[field]) }}
                    </template>
                    <template #editor="{ data, field }">
                        <InputNumber v-model="data[field]" :maxFractionDigits="2" mode="currency" currency="BRL" />
                    </template>
                </Column>
                <Column>
                    <template #body="rowData">
                        <Button icon="pi pi-times" style="background-color:red" @click="excluirDotacao(rowData)"/>
                    </template>
                </Column>
            </DataTable>
        </div>
        <div style="text-align:center">
            <Button size="small"
                    label="Salvar"
                    style="margin:2em"
                    @click="salvarAlteracoes"
            />
        </div>
    </div>

</template>
<script>
import {ref,computed} from 'vue';
import DataTable from 'primevue/datatable';
import PesquisaDotacao from './PesquisaDotacao';
import { useToast } from "primevue/usetoast";

export default {
    name:'AlterarDotacaoForm',
    emit:['salvarAlteracoes'],
    components: {
        PesquisaDotacao,
        DataTable
    },
    props: {
        desdobramentos: {
            type: Array,
            required:true
        },
        desdobramentoSelecionado: {
            type: Object,
            required: true
        },
        itemSelecionado:{
            type: Number,
            required: true
        }
    },
    setup(props, { emit }) {
        const desdobramentoSelecionado = ref(props.desdobramentoSelecionado);
        const valor = ref(null);
        const toast = useToast();

        const item = computed ( () => {
            return props.itemSelecionado
        })
        const valorAutorizar = computed ( () => {
            return props.itemSelecionado.valorAutorizar
        })
        const dotacoes = computed( () => {
            return props.itemSelecionado.dotacoes
        });

        const desdobramentos = computed ( () => {
            return props.desdobramentos
        });

        const novaDotacao = ref({
            codigo: null,
            saldoDotacao: null,
        });

        const itemAlteracoes = ref({});
        copiarDotacoes();



        function editarCelula(event)  {
            let { data, newValue, field } = event;
            data[field] = newValue;

        };


        function formatarValor (value) {
            return new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' }).format(value);
        }

        function recebeDotacao (dotacao) {
            novaDotacao.value = dotacao;

        }

        function validaDotacao () {

            if (!novaDotacao.value.codigo) {
                toast.add({ severity: 'error', summary: 'Dotacão vazia', detail: 'Você tem que escolher uma dotação pra adicionar', life: 5000 });
                return false
            }

            if(!valor.value) {
                toast.add({ severity: 'error', summary: 'Valor inválido', detail: 'Você tem que digitar um valor', life: 5000 });
                return false
            }

            for(let i = 0;i <   itemAlteracoes.value.dotacoes.length;i++) {
                if(novaDotacao.value.codigo == itemAlteracoes.value.dotacoes[i].ac22_coddot
                && novaDotacao.value.anoDotacao == itemAlteracoes.value.dotacoes[i].ac22_anousu) {
                    toast.add({ severity: 'error', summary: 'Dotação existente', detail: 'A dotação selecionada já está adicionada', life: 5000 });
                    return false;
                }
            }
            return true
        }



        function adicionarDotacao () {
            if (validaDotacao()) {
                itemAlteracoes.value.dotacoes.push({
                    'ac22_coddot': novaDotacao.value.codigo,
                    'ac22_anousu': novaDotacao.value.anoDotacao,
                    'ac22_valor': valor.value,
                    'ac22_quantidade':item.value.ac20_quantidade,
                    'ac22_acordoitem':item.value.ac20_sequencial
                })
                limparCampos();
            }

        }

        function alterarDesdobramento() {
            item.value.ac20_elemento = desdobramentoSelecionado.value.o56_codele
        }

        function removerDotacoes() {
            itemAlteracoes.value.dotacoes.splice(0,itemAlteracoes.value.dotacoes.length)
            limparCampos();

        }

        function excluirDotacao (rowData) {
            itemAlteracoes.value.dotacoes.splice(rowData.index,1)
        }

        function salvarAlteracoes () {
            if (validarValorDotacoes()) {
                alterarDesdobramento();
                salvarDotacoes();
                emit('salvarAlteracoes')
            }
        }

        function validarValorDotacoes() {
            let valorDotacoes = 0;

            itemAlteracoes.value.dotacoes.forEach(obj => {
                valorDotacoes += parseFloat(obj.ac22_valor)
            })

            if(valorDotacoes > valorAutorizar.value) {
                toast.add({ severity: 'error',
                    summary: 'Valor inconsistente',
                    detail: 'O valor das dotações (' + formatarValor(valorDotacoes) +
                        ') é maior que o saldo a autorizar do item (' +
                        formatarValor(valorAutorizar.value) + ')'
                    , life: 5000 });
                return false;
            } else if(valorDotacoes < valorAutorizar.value) {
                toast.add({ severity: 'error',
                    summary: 'Valor inconsistente',
                    detail: 'O valor das dotações (' + formatarValor(valorDotacoes) +
                        ') é menor que o saldo a autorizar do item (' +
                        formatarValor(valorAutorizar.value) + ')'
                    , life: 5000 });
                return false;
            } else {
                return true
            }
        }

        function copiarDotacoes () {
            itemAlteracoes.value.dotacoes = [];
            dotacoes.value.forEach(obj => {
                itemAlteracoes.value.dotacoes.push(obj);
            })
        }

        function salvarDotacoes () {
            dotacoes.value.splice(0,dotacoes.value.length);
            itemAlteracoes.value.dotacoes.forEach(obj => {
                dotacoes.value.push(obj);
            })
        }

        function limparCampos() {
            novaDotacao.value.codigo = null;
            novaDotacao.value.saldoDotacao = null;
            valor.value = null;

        }


        return {
            valor,
            dotacoes,
            formatarValor,
            editarCelula,
            recebeDotacao,
            adicionarDotacao,
            excluirDotacao,
            desdobramentoSelecionado,
            valorAutorizar,
            salvarAlteracoes,
            removerDotacoes,
            item,
            alterarDesdobramento,
            itemAlteracoes,
            novaDotacao
        }
    }
}
</script>
