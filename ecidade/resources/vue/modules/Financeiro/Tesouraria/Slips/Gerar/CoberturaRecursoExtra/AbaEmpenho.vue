<script setup>
import {ref} from "vue";
import {useToast} from "primevue/usetoast";
import {formatCurrency} from "../../../../../../utils/Strings";

const props = defineProps(['dataInicial', 'dataFinal', 'contas', 'empenhos', 'tipo', 'situacao']);
const toast = useToast();

const emits = defineEmits(['proximo'])

const dataInicial = ref(props.dataInicial);
const dataFinal = ref(props.dataFinal);
const dados = ref(props.empenhos);

const rotas = {
    get: 'v4/api/financeiro/tesouraria/slip/cobertura-recurso-extra/preparados/empenho'
}

const buscarDados = async () => {
    try {
        let parans = [
            `tipo=${props.tipo.value}`,
            `situacao=${props.situacao.value}`,
            `dataInicial=${dataInicial.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'})}`
        ];

        if (dataFinal.value !== undefined) {
            parans.push(`dataFinal=${dataFinal.value.toLocaleDateString('en-CA', {timeZone: 'America/Sao_Paulo'})}`);
        }

        const response = await window.axios.get(`${rotas.get}?${parans.join('&')}`);

        if (response.status === 206) {
            toast.add({severity: 'warn', detail: response.data.message, summary: 'Aviso'});
            dados.value = [];
            return;
        }
        const contas = [];
        response.data.data.forEach(conta => {
            conta.uniqid = uniqid();
            conta.contaSelecionada = ref(null);
            conta.setDefault = true; // criado para saber se já definiu o valor default
            conta.debitar = null;
            conta.debitar_descricao = null;
            contas.push(conta)
        });
        dados.value = contas;
    } catch (e) {
        toast.add({severity: 'error', detail: e.response.data.message, summary: 'Erro'});
    }
}

const filtraContas = (data) => {

    return  props.contas.value.filter(conta => {
        let recurso = conta.contaContabil.recurso
        return (recurso.siconfi === data.siconfi && recurso.subrecurso === data.subrecurso)
    }).map(conta => {
        let option = {
            code: conta.conta,
            label: `${conta.conta} - ${conta.nome}`,
            nome: conta.nome,
            extra: conta.contaExtra,
            recurso: conta.contaContabil.recurso
        };

        if (data.setDefault && conta.conta == data.creditar) {
            data.contaSelecionada = option
            data.setDefault = false
            if (data.contaSelecionada.extra !== null) {
                data.debitar = data.contaSelecionada.extra.conta;
                data.debitar_descricao = data.contaSelecionada.extra.nome;
            }
        }
        return option
    })
}

const atualizaGrid = (data) => {
    data.creditar = data.contaSelecionada.code;
    data.creditar_descricao = data.contaSelecionada.nome;

    data.debitar = null;
    data.debitar_descricao = null;

    if (data.contaSelecionada.extra !== null) {
        data.debitar = data.contaSelecionada.extra.conta;
        data.debitar_descricao = data.contaSelecionada.extra.nome;
    }
}

const proximo = () => {
    emits('proximo');
}

defineExpose({buscarDados});

</script>

<template>

    <Message severity="info" :closable="false" :style="{marginTop:0}">
        <div style='font-size: 10pt; '>
            Valores apropriados sob a forma de retenções na execução dos empenhos e restos a pagar.<br>
            Quando o campo <b>Creditar</b> não apresentar contas, não existe conta bancária com recurso compatível ao do empenho.
        </div>
    </Message>

    <Button @click="proximo" label="Próxima Aba" class="mb-1"/>

    <section>
        <DataTable :value="dados" class="w-full" tableStyle="min-width: 50rem;" scrollable scrollHeight="400px">
            <template #empty>
                Sem registros de apropriação de retenção para o período informado.
            </template>
            <Column field="credor" header="Credor">
                <template #body="slotProps">
                    {{ `${slotProps.data.cgm} - ${slotProps.data.nome_credor}` }}
                </template>
            </Column>
            <Column field="debitar" header="Debitar">
                <template #body="slotProps" >
                    <span v-if="slotProps.data.debitar !== null">
                        {{`${slotProps.data.debitar} - ${slotProps.data.debitar_descricao}` }}
                    </span>
                </template>
            </Column>
            <Column field="creditar" header="Creditar">
                <template #body="slotProps">
                    <div class="card flex justify-content-center">
                        <Dropdown v-model="slotProps.data.contaSelecionada" :options="filtraContas(slotProps.data)" filter
                                  optionLabel="label" class="w-full" @change="atualizaGrid(slotProps.data)">
                        </Dropdown>
                    </div>
                </template>
            </Column>

            <Column field="siconfi" header="Siconfi"></Column>
            <Column field="subrecurso" header="Subrecurso"></Column>
            <Column field="complemento" header="Complemento"></Column>
            <Column field="op" header="OP"></Column>
            <Column field="tipo_retencao" header="Tipo de Retenção"></Column>
            <Column field="valor" header="Valor">
                <template #body="slotProps">
                    {{ formatCurrency(slotProps.data.valor) }}
                </template>
            </Column>
        </DataTable>
    </section>
<!--    <ModalLoading :isLoading="loading"/>-->
</template>

<style scoped>

</style>
