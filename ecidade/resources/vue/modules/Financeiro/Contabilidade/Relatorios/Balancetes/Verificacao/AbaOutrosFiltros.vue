<script setup>

import {computed, ref} from "vue";

const props = defineProps(['modelValue', 'exercicio']);
const emit = defineEmits(['update:modelValue'])


const filtro = computed({
    get() {
        return props.modelValue
    },
    set(value) {
        emit('update:modelValue', value)
    }
});

const opcoesTipo = ref([
    {name: 'Analítico', code: 'A'},
    {name: 'Sintético', code: 'S'}
]);

const opcoesContaBancaria = ref([
    {name: 'Exibir Conta Bancária', code: 'S'},
    {name: 'Não Exibir Conta Bancária', code: 'N'},
])

const opcoesConsolidar = ref([
    {name: 'Por Reduzido', code: 'R'},
    {name: 'Consolidado por Estrutural', code: 'E'}
])

const subtituloOpcoes = ref([
    {name: 'Todos', code: ''},
    {name: '1 - Consolidação', code: '1'},
    {name: '2 - Intra OFSS', code: '2'},
    {name: '3 - Inter OFSS - União', code: '3'},
    {name: '4 - Inter OFSS - Estado', code: '4'},
    {name: '5 - Inter OFSS - Município', code: '5'},
    {name: '6 - Outros', code: '6'},
    {name: '7 - Outros', code: '7'},
    {name: '8 - Outros', code: '8'},
    {name: '9 - Outros', code: '9'},
]);
</script>

<template>
    <section class="flex flex-column w-full gap-2">
        <section class="flex justify-content-center">

            <Panel header="Outros Filtros" class="w-full md:w-11 lg:w-10 xl:w-7">

                <div class="formgrid grid mt-4 gap-2 md:gap-0">
                    <div class="field col-12 md:col-6">
                        <div class="p-float-label">
                            <Dropdown v-model="filtro.subtitulo" :options="subtituloOpcoes" class="w-full md:w-10rem lg:w-27rem"
                                      optionLabel="name" optionValue="code" placeholder="Subtítulo" />
                            <label for="dd-consolidar">Subtítulo</label>
                        </div>
                    </div>

                    <div class="field col-12 md:col-6">
                        <SelectButton :modelValue="filtro.tipo" :options="opcoesTipo" aria-labelledby="basic"
                                      option-label="name" option-value="code"
                                      @update:modelValue="value => filtro.tipo = value ?? filtro.tipo"/>
                    </div>

                    <div class="field col-12 md:col-6">
                        <SelectButton :modelValue="filtro.contaBancaria" :options="opcoesContaBancaria"
                                      option-label="name" option-value="code" aria-labelledby="basic"
                                      @update:modelValue="value => filtro.contaBancaria = value ?? filtro.contaBancaria"/>
                    </div>

                    <div class="field col-12 md:col-6">
                        <SelectButton :modelValue="filtro.consolidarPor" :options="opcoesConsolidar"
                                      option-label="name" option-value="code" aria-labelledby="basic"
                                      @update:modelValue="value => filtro.consolidarPor = value ?? filtro.consolidarPor"/>
                    </div>
                </div>
            </Panel>
        </section>
    </section>
</template>

<style scoped>

</style>
