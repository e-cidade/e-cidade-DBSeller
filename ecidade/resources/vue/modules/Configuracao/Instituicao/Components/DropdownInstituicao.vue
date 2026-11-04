<script setup>
import { onMounted, ref } from 'vue';
import { useToast } from 'primevue/usetoast';

// Services
const toast = useToast();

// Props
const props = defineProps(['instit']);

// Emits
const emit = defineEmits(['change']);

// Data
const selectedInstit = ref(null);
const instituicoes = ref([]);
const isLoading = ref(false);
const filters = ['codigo', 'nomeinst'];

// Methods
const getInstituicoes = async () => {
    const url = 'v4/api/configuracao/instituicao';
    isLoading.value = true;

    try {
        const req = await axios.get(url);
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        instituicoes.value = resp.data;

        if (props.instit) {
            selectedInstit.value = instituicoes.value.find((e) => { return e.codigo == props.instit }) ?? null;
        }
    } catch (error) {
        instituicoes.value = [];
        toast.add({ severity: 'warn', summary: 'Erro', detail: 'Erro ao buscar instituições' });
        log.error(error);
    } finally {
        isLoading.value = false;
    }
}

const select = (event) => { emit('change', selectedInstit.value) }

// Hooks
onMounted(() => {
    getInstituicoes();
})
</script>

<template>
    <Dropdown v-model="selectedInstit" :options="instituicoes" :loading="isLoading" filter :filterFields="filters"
        filterPlaceholder="Filtre por código ou nome" optionLabel="nomeinst" placeholder="Selecione a instituição"
        @change="select">
        <template #value="slotProps">
            <span v-if="slotProps.value">
                {{ slotProps.value.codigo + ' - ' + slotProps.value.nomeinst }}
            </span>
            <span v-else>
                {{ slotProps.placeholder }}
            </span>
        </template>
        <template #option="slotProps">
            {{ slotProps.option.codigo + ' - ' + slotProps.option.nomeinst }}
        </template>
    </Dropdown>
</template>
