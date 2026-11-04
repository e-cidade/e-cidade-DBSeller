<script setup>
import { ref } from 'vue';
import DropdownInstituicao from '@modules/Configuracao/Instituicao/Components/DropdownInstituicao.vue';
import DialogConsultaCgm from '@modules/Patrimonial/Protocolo/Components/DialogConsultaCgm.vue';
import { useToast } from 'primevue/usetoast';

// Services
const toast = useToast();

// Props
const props = defineProps(['dados']);

// Emits
const emit = defineEmits(['saved']);

// Data
const codigo = ref(props.dados?.codigo ?? null);
const instit = ref(props.dados?.instit ?? null);
const cgmordenadordespesa = ref(props.dados?.cgmordenadordespesa ?? null);
const responsavelfolha = ref(props.dados?.responsavelfolha ?? true);
const cgmnomeordenador = ref(props.dados?.cgmnomeordenador ?? null)
const codigofolha = ref(props.dados?.codigofolha ?? null);
const isLoadingSendForm = ref(false);
const dialogConsultaCgm = ref(null);

// Methods
const sendForm = async () => {
    if (!validateForm()) {
        return false;
    }

    isLoadingSendForm.value = true;

    try {
        const url = 'v4/api/financeiro/contabilidade/tce/rj/sigfis/unidadegestora';
        const formData = {
            sequencial: props.dados?.sequencial,
            codigo: codigo.value,
            instit: instit.value,
            responsavelfolha: responsavelfolha.value,
            codigofolha: codigofolha.value,
            cgmordenadordespesa: cgmordenadordespesa.value
        }

        const req = await axios.post(url, formData);
        const { data: resp } = req;

        if (resp.error) {
            throw new Error(resp.message);
        }

        emit('saved', true);
        toast.add({ severity: 'success', summary: 'Sucesso', detail: 'Dados salvos com sucesso' });
    } catch (error) {
        toast.add({ severity: 'error', summary: 'Erro', detail: 'Erro ao salvar dados da unidade gestora' });
        console.error(error);
    } finally {
        isLoadingSendForm.value = false;
    }
}

const selectInstit = (data) => {
    instit.value = data.codigo;
}

const validateForm = () => {
    if (!codigo.value) {
        alert('Código deve ser informado');
        return false;
    }

    if (!instit.value) {
        alert('Instituição deve ser informada');
        return false;
    }

    if (!cgmordenadordespesa.value) {
        alert('Ordenador de despesa deve ser informado');
        return false;
    }

    if (responsavelfolha.value == false && !codigofolha.value) {
        alert('Codigo da folha deve ser informado');
        return false;
    }

    return true;
}

const selecionaCgm = (result) => {
    const { numcgm, nome } = result;
    cgmordenadordespesa.value = numcgm;
    cgmnomeordenador.value = numcgm + ' - ' + nome;
}

</script>

<template>
    <div class="grid gap-3">
        <!-- instit -->
        <div class="col-12 flex flex-column gap-2">
            <label for="instit">Instituição: </label>
            <DropdownInstituicao @change="selectInstit" :instit="instit"/>
        </div>

        <!-- codigo -->
        <div class="col-12 flex flex-column gap-2">
            <label for="codigo">Codigo: </label>
            <InputNumber id="codigo" v-model="codigo" :useGrouping="false" />
        </div>

        <!-- responsavel folha ? -->
        <div class="col-12 flex gap-2">
            <Checkbox v-model="responsavelfolha" inputId="responsavelfolha" binary />
            <label for="responsavelfolha" class="ml-2">Responsável pela folha</label>
        </div>

        <!-- codigofolha -->
        <div class="col-12 flex flex-column gap-2" v-if="!responsavelfolha">
            <label for="codigofolha">Codigo UG folha: </label>
            <InputNumber id="codigofolha" v-model="codigofolha" :useGrouping="false" />
        </div>

        <!-- CGM ordenador -->
        <div class="col-12 flex flex-column gap-2">
            <a href="javascript:;" @click="dialogConsultaCgm.toggleDialog()">Ordenador de Despesa:</a>
            <InputText v-model="cgmnomeordenador" readonly/>
            <DialogConsultaCgm ref="dialogConsultaCgm" @selectRow="selecionaCgm" />
        </div>

        <div class="col-12">
            <Button :label="isLoadingSendForm ? 'Salvando' : 'Salvar'" :loading="isLoadingSendForm" @click="sendForm" />
        </div>
    </div>
</template>
