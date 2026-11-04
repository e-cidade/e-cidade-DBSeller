<script setup>
import { computed, ref } from 'vue';
import { useToast } from 'primevue/usetoast';
import DataTableContas from './DataTableContas.vue';
import DataTableEmpenhos from './DataTableEmpenhos.vue';
import MapeamentoService from '../Services/MapeamentoService';
import BadgeSaldo from './BadgeSaldo.vue';

// emits
const emit = defineEmits(['save']);

// service
const toast = useToast();

// data
const isOpenListEmpenhos = ref(false);
const isOpenListContas = ref(false);
const selectedConta = ref(null);
const contaDescr = ref(null);
const filtersEmpenhos = ref({});
const selectedEmpenhos = ref([]);
const empenhosSaldo = ref(0);
const isLoadingSave = ref(false);

// methods
const selectConta = (conta) => {
    isOpenListContas.value = false;
    selectedConta.value = conta;
    contaDescr.value = conta.c60_estrut + ' - ' + conta.c60_descr;

    filtersEmpenhos.value = {
        exercicio: conta.c61_anousu,
        reduzido: conta.c61_reduz,
        estrutural: conta.c60_estrut,
        saldo_conta: conta.saldo,
        saldo_empenhos: conta.saldo_mapeado,
        descricao: conta.c60_descr
    }
}

const OpenListEmpenhos = () => {
    if(!selectedConta.value) {
        alert('Deve selecionar uma conta.')
        return false;
    }

    isOpenListEmpenhos.value = true;
}

const selectEmpenhos = (empenhos) => {
    isOpenListEmpenhos.value = false;
    selectedEmpenhos.value = empenhos;
    empenhosSaldo.value = empenhos.reduce((total, data) => Number(data.saldo) + total, 0).toFixed(2);
}

const save = async () => {
    if(!validateForm()) {
        return false;
    }

    const reduzido = selectedConta.value.c61_reduz;
    const exercicio = selectedConta.value.c61_anousu;
    const empenhos = selectedEmpenhos.value;

    try {
        isLoadingSave.value = true;
        let response = await MapeamentoService.save(reduzido, exercicio, empenhos);
        toast.add({
            severity: 'success',
            summary: 'Sucesso',
            detail: 'Empenhos adicionados com sucesso'
        });

        empenhosSaldo.value = 0;
        contaDescr.value = null;
        selectedConta.value = null;
        selectedEmpenhos.value = [];

        emit('save', true);
    } catch(error) {
        let msg = 'Erro interno';
        if (error.response.data.message) {
            msg = error.response.data.message
        }

        toast.add({
            severity: 'error',
            summary: 'Erro',
            detail: 'Erro ao salvar empenhos: ' + msg
        });
    } finally {
        isLoadingSave.value = false;
    }
}

// computed
const saldoDisponivel = computed(() => {
    if (!selectedConta.value) {
        return 0;
    }

    let saldo = Number(selectedConta.value.saldo);
    let mapeamento = Number(selectedConta.value.saldo_mapeado);

    return (saldo - mapeamento).toFixed(2);
});

// validates
const validateForm = () => {
    if (!selectedConta.value) {
        alert('Conta deve ser informada.');
        return false;
    }

    if (!selectedEmpenhos.value.length) {
        alert('Valor Empenhos deve ser informado.');
        return false;
    }

    if (empenhosSaldo.value > saldoDisponivel.value) {
        alert('Valor dos empenhos é maior que o saldo.');
        return false;
    }

    return true;
}
</script>

<template>
    <!-- dialog emepenho -->
    <Dialog
        v-model:visible="isOpenListEmpenhos"
        maximizable position="top"
        :modal="true"
        header="Adicionar Empenhos"
    >
        <DataTableEmpenhos
            :conta="filtersEmpenhos"
            @select="selectEmpenhos"
        />
    </Dialog>

    <!-- dialog contas -->
    <Dialog v-model:visible="isOpenListContas" position="top" :modal="true" header="Contas">
        <DataTableContas @select="selectConta"/>
    </Dialog>

    <!-- form -->
    <div class="mt-5" style="width: 50rem;">
        <div class="grid gap-2">

            <!-- conta -->
            <div class="col-12 flex flex-column gap-2">
                <label for="conta" class="font-bold">Conta </label>
                <div class="flex">
                    <Button icon="pi pi-search" @click="isOpenListContas = true"/>
                    <InputText id="conta" class="w-full" readonly placeholder="selecione" v-model="contaDescr"/>
                </div>
            </div>

            <!-- empenhos -->
            <div class="col-12 flex flex-column gap-2">
                <label for="empenhos" class="font-bold">Valor Empenhos </label>
                <div class="flex">
                    <Button icon="pi pi-search" @click="OpenListEmpenhos"/>
                    <InputNumber id="empenhos"
                        class="w-full"
                        readonly
                        placeholder="Selecione"
                        v-model="empenhosSaldo"
                        mode="currency"
                        currency="BRL" locale="pt-BR"
                    />
                </div>
            </div>

            <div class="col-12">
                <fieldset class="flex flex-wrap gap-2">
                    <legend>Dados</legend>
                    <BadgeSaldo label="Saldo da Conta" :saldo="selectedConta?.saldo"/>
                    <BadgeSaldo label="Mapeado" :saldo="selectedConta?.saldo_mapeado"/>
                    <BadgeSaldo label="Disponivel" :saldo="saldoDisponivel"/>
                </fieldset>
            </div>

            <div class="col-12">
                <Button
                    :loading="isLoadingSave"
                    severity="success"
                    icon="pi pi-save"
                    :label="isLoadingSave ? 'Salvando' : 'salvar'"
                    @click="save"
                />
            </div>
        </div>
    </div>
</template>
