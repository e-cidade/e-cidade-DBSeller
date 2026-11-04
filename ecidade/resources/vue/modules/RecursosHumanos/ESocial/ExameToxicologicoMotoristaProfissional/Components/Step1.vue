    <template>
    <div>
        <label for="cpf" class="font-bold block mb-2">
            Preencher número do CPF do trabalhador
        </label>
        <InputMask
            id="cpf"
            v-model="localData.cpf"
            mask="999.999.999-99"
            class="col-5"
        />
    </div>
    <div>
        <label for="" class="font-bold block mb-2">
            <Button link label="Matrícula" :loading="loading" @click="openDialogMatricula"/>
            <DialogMatricula ref="funcMatricula" v-on:selectRow="callback"/>
        </label>
        <InputText id="matricula" v-model="localData.matricula" class="col-5"/>
    </div>
</template>

<script setup>
import {computed, ref} from 'vue';
import InputMask from 'primevue/inputmask';
import InputText from 'primevue/inputtext';
import Button from 'primevue/button';
import DialogMatricula from "@modules/RecursosHumanos/Pessoal/Components/DialogMatricula";

const props = defineProps({
    modelValue: {
        type: Object,
        default: () => ({})
    }
});
const emit = defineEmits(['update:modelValue']);

const localData = computed({
    get() {
        return props.modelValue;
    },
    set(value) {
        emit('update:modelValue', value);
    }
});

const funcMatricula = ref();
const loading = ref(false); // Assuming you have a loading state

const openDialogMatricula = () => {
    funcMatricula.value.openModal();
};

const callback = (response) => {
    localData.value.matricula = response.rh01_regist;
};

</script>
