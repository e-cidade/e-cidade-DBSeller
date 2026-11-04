<script setup>
import { useToast } from 'primevue/usetoast';
import { ref, watch } from 'vue';

// services
const toast = useToast();

// emits
const emit = defineEmits(['selected', 'update:idValue', 'update:descriptionValue']);

// props
const props = defineProps({
    url: {
        type: String,
        required: true
    },
    params: {
        typ: Object,
        default: {}
    },
    paramField: {
        type: String
    },
    idField: {
        type: String,
        required: true
    },
    descriptionField: {
        type: String,
        required: true
    },
    idValue: {
        type: [String, Number]
    },
    descriptionValue: {
        type: String
    },
    Method: {
        type: String,
        default: 'POST'
    }
});

// data
const id = ref(props.idValue);
const description = ref(props.descriptionValue);
const loading = ref(false);
const oldId = ref(null);

// methods
const setIdAndDescription = (dataResponse) => {

    // Se o retorno for um objeto
    if (props.idField in dataResponse && props.descriptionField in dataResponse) {
        id.value = dataResponse[props.idField];
        description.value = dataResponse[props.descriptionField];
        emit('selected', dataResponse);
        return;
    }

    // Se o retorno for um array
    if (Array.isArray(dataResponse) && dataResponse.length > 0) {
        if (props.idField in dataResponse[0] && props.descriptionField in dataResponse[0]) {
            id.value = dataResponse[0][props.idField];
            description.value = dataResponse[0][props.descriptionField];
            emit('selected', dataResponse[0]);
            return;
        }
    }

    // Se o retorno for uma paginacao
    if ('data' in dataResponse && 'per_page' in dataResponse && dataResponse.data.length > 0) {
        if (props.idField in dataResponse.data[0] && props.descriptionField in dataResponse.data[0]) {
            id.value = dataResponse.data[0][props.idField];
            description.value = dataResponse.data[0][props.descriptionField];
            emit('selected', dataResponse.data[0]);
            return;
        }
    }

    id.value = '';
    description.value = 'Nenhum registro encontrado';
}

const fetchData = async () => {
    if (!id.value) {
        description.value = '';
        return
    }

    if (oldId.value == id.value) return;

    try {
        loading.value = true;
        const paramField = props.paramField ?? props.idField;
        let req = {};

        switch (props.Method) {
            case 'GET':
                let params = {[paramField]: id.value, ...props.params};
                req = await axios.get(props.url, params);
                break;
            case 'POST':
                req = await axios.post(props.url, {[paramField]: id.value});
                break;
            default:
                throw new Error('Metodo invalido');
        }

        const { data: resp } = req;
        if (resp.error) {
            throw new Error(resp.message);
        }

        setIdAndDescription(resp.data);
    } catch (error) {
        toast.add({ severity: 'warn', summary: 'Erro', detail: `Erro ao buscar ${props.descriptionField}` });
        console.error(error);
        id.value = '';
        description.value = '';
    } finally {
        loading.value = false;
        oldId.value = id.value;
    }
}

// watch
watch(() => props.idValue, (newValue) => {
    id.value = newValue;
});

watch(() => props.descriptionValue, (newValue) => {
    description.value = newValue;
});

watch(id, (newValue) => {
    emit('update:idValue', newValue);
});

watch(description, (newValue) => {
    emit('update:descriptionValue', newValue);
});
</script>
<template>
    <div class="flex gap-2 align-items-center w-full">
        <InputText v-model="id" @blur="fetchData" class="w-6rem"/>
        <div class="relative w-full ">
            <InputText v-model="description" readonly class="w-full"/>
            <i class="pi pi-spin pi-spinner absolute"
                style="right: 0.5rem; top: 33.33%"
                v-show="loading"
            ></i>
        </div>
    </div>
</template>
