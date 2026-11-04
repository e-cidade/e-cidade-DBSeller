<script setup>

import { computed, onMounted, ref } from "vue";

const props = defineProps({
    modelValue: { type: Object, required: true }
});
const emit = defineEmits(['update:modelValue']);

const variavel = computed({
    get() { return props.modelValue },
    set(value) { emit('update:modelValue', value) }
});

const isLoading = ref(false);
const selectOptions = ref([]);

async function buildSelect() {
    isLoading.value = true;
    try {
        const data = { sql: variavel.value.sql, bindings: [] };
        const response = await axios.post('v4/api/configuracao/gerador/executar-sql', data);

        for (const option of response.data.data) {
            const properties = Object.keys(option);

            selectOptions.value.push({
                value: option[properties[0]],
                label: option[properties[1]]
            });
        }
    } catch (e) {}
    isLoading.value = false;
}

function updateDate(newValue) {
    variavel.value.valor = newValue.toISOString().substring(0, 10);
}

onMounted(() => {
    if (variavel.value.tipo === 'select') {
        buildSelect();
    }
});

</script>

<template>
    <span v-if="variavel.tipo !== 'bool'" class="p-float-label">
        <InputNumber v-if="variavel.tipo === 'int4'"
                     :id="variavel.nome"
                     v-model="variavel.valor"
                     :useGrouping="false"
                     class="w-full"
                     locale="pt-BR"></InputNumber>
        <InputNumber v-else-if="variavel.tipo === 'float8'"
                     :id="variavel.nome"
                     v-model="variavel.valor"
                     :minFractionDigits="2"
                     class="w-full"
                     locale="pt-BR"></InputNumber>
        <Calendar v-else-if="variavel.tipo === 'date'"
                  :id="variavel.nome"
                  v-model="variavel.input"
                  class="w-full"
                  dateFormat="dd/mm/yy"
                  showIcon
                  @update:modelValue="updateDate"></Calendar>
        <Dropdown v-else-if="variavel.tipo === 'select'"
                  :id="variavel.nome"
                  :loading="isLoading"
                  v-model="variavel.valor"
                  :options="selectOptions"
                  class="w-full"
                  optionLabel="label"
                  optionValue="value"></Dropdown>
        <InputText v-else
                   :id="variavel.nome"
                   v-model="variavel.valor"
                   class="w-full"
                   type="text"></InputText>
        <label :for="variavel.nome">{{ variavel.label ? variavel.label : variavel.nome }}</label>
    </span>
    <div v-else>
        <Checkbox :inputId="variavel.nome" :binary="true" v-model="variavel.valor"></Checkbox>
        <label class="label-checkbox" :for="variavel.nome">{{ variavel.label !== '' ? variavel.label : variavel.nome }}</label>
    </div>
</template>

<style scoped>

.label-checkbox {
    margin-left: 1px;
    font-size: 12px;
    color: #605e5c;
}

</style>
