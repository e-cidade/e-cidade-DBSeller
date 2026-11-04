<script setup>
import {computed, onMounted, ref} from "vue";

const props = defineProps(['modelValue', 'tipo', 'anexo']);
const emit = defineEmits(['update:modelValue']);

const modelValue = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const relatorioSelecionado = ref(null);
const periodoSelecionado = ref(null);

const versoes = ref([]);
const periodos = ref([]);

const retorno = ref();

const rota = 'v4/api/financeiro/contabilidade/relatorio-legal/versoes/anexo'

function changeVersao(e) {
    let versao = versoes.value.find((versao) => {
        return versao.codigo === e.value;
    });

    modelValue.value.relatorio = versao;
    periodoSelecionado.value = modelValue.value.periodo = null;
    modelValue.value.abasDisabilitada = true;
    periodos.value = versao.periodos;
}

function changePeriodo(e) {
    const periodo = periodos.value.find((periodo) => {
        return periodo.codigo === e.value;
    })

    modelValue.value.periodo = periodo;
    modelValue.value.abasDisabilitada = false;
}

/**
 *
 * @returns {Promise<void>}
 */
async function buscaVersoesAnexo() {
    await window.axios.get(`${rota}/${props.anexo}/${props.tipo}`).then(response => {
        for (const dado of response.data.data) {
            dado.descricao = `${dado.codigo} - ${dado.descricao}`
            versoes.value.push(dado);
        }
    });
}

onMounted(() => {
    buscaVersoesAnexo()
});

</script>

<template>
    <div class="field col-12">
        <div class="p-float-label w-full">
            <Dropdown v-model="relatorioSelecionado" inputId="dd-versoes-relatorio-legal"
                      :options="versoes"
                      optionLabel="descricao" optionValue="codigo" @change="changeVersao"
                      class="w-full "/>
            <label for="dd-versoes-relatorio-legal">Versão</label>
        </div>
    </div>
    <div class="field col-12">
        <div class="p-float-label w-full">
            <Dropdown v-model="periodoSelecionado" inputId="dd-periodos"
                      :options="periodos"
                      optionLabel="descricao" optionValue="codigo"
                      class="w-full" @change="changePeriodo"/>
            <label for="dd-periodos">Período</label>
        </div>
    </div>
</template>

<style scoped>

</style>
