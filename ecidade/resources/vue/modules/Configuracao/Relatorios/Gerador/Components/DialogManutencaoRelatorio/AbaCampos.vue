<script setup>

import { computed, onMounted, ref, watch } from "vue";

const props = defineProps(['modelValue']);
const emit = defineEmits(['update:modelValue'])

const spansAlias = ref({});
const teleportedStyle = ref([]);
const currentItemInTeleport = ref(null);
let initialAliasWidth = 0;

const campos = computed({
    get() { return props.modelValue },
    set(value) { emit('update:modelValue', value) }
});

const alinhamentos = ref([
    { value: 'c', label: 'Centro' },
    { value: 'l', label: 'Esquerda' },
    { value: 'r', label: 'Direita' }
]);

const mascaras = ref([
    { value: 't', label: 'Texto' },
    { value: 'm', label: 'Moeda' },
    { value: 'd', label: 'Data' }
]);

const totalizadores = ref([
    { value: 'n', label: 'Não' },
    { value: 's', label: 'Soma' },
    { value: 'q', label: 'Quantidade' }
]);

function resetStyles() {
    for (const prop in teleportedStyle.value) {
      teleportedStyle.value[prop] = null;
    }
}

/**
 * recoloca o input no picklist quando rolar o mouse, para evitar que o input fique "flutuando".
 */
function handleScroll() {
    resetStyles();

    if (currentItemInTeleport.value) {
        currentItemInTeleport.value.teleport = false;
    }
}

/**
 * Devido bug no componente PickList, foi feito uma lógica para retirar o input do lugar quando o usuário focasse nele
 * @todo retirar essa lógica quando o bug for resolvido
 * https://github.com/primefaces/primevue/issues/4318
 */
function teleport(item, event) {
    resetStyles();

    if (event === 'blur' && item.teleport) {
        item.teleport = false;
        currentItemInTeleport.value = null;
        return;
    }

    if (currentItemInTeleport.value !== null) {
        currentItemInTeleport.value.teleport = false
    }

    const el = spansAlias.value[item.nome];
    setTimeout(el => {
        el.childNodes[1].focus();
    }, 200, el);

    initialAliasWidth = el.offsetWidth;

    const rect = el.getBoundingClientRect();

    teleportedStyle.value[item.nome] = {
      position: 'absolute',
      top: `${rect.top}px`,
      left: `${rect.left}px`,
      width: `${initialAliasWidth}px`,
      zIndex: 999
    };

    item.teleport = true;
    currentItemInTeleport.value = item;
}

/**
 * Ajuste técnico para fazer o PickList imprimir as duas listas de forma diferente
 * @todo Refatorar quando houver alternativa melhor. Até a versão 3.32.0 do PrimeVue, não existia alternativa.
 */
function verificaPosicaoCampo() {
    if (!campos.value.length) {
        return;
    }

    const [disponiveis, selecionados] = campos.value;

    for (const disponivel of disponiveis) {
        disponivel.isTarget = false;
    }

    for (const selecionado of selecionados) {
        selecionado.isTarget = true;
    }
}

watch(campos, verificaPosicaoCampo);
onMounted(() => {
    verificaPosicaoCampo();

    /**
     * recoloca o input no PickList quando houver alterações no tamanho da tela, para evitar que o input
     * fique "flutuando".
     */
    window.addEventListener('resize', () => {
       if (currentItemInTeleport.value === null) {
           return;
       }

       resetStyles();
       currentItemInTeleport.value.teleport = false;
       currentItemInTeleport.value = null;
    });
});

</script>

<template>
    <section class="flex justify-content-center">
        <div id="target"></div>
        <PickList v-model="campos" listStyle="height: 400px" class="w-11" :pt="{ targetList: { onScroll: handleScroll }}">
            <template #sourceheader> Disponível </template>
            <template #targetheader> Selecionado </template>
            <template #item="slotProps">
                <div :class="['flex', 'p-2', 'align-items-center', 'gap-3', 'w-full', { 'mt-3': slotProps.item.isTarget }]">
                    <!-- lista dos disponiveis -->
                    <div v-if="!slotProps.item.isTarget" class="flex-1 flex flex-column gap-2 w-full">
                        <span class="font-bold w-full">{{ slotProps.item.nome }}</span>
                        <div class="flex align-items-center gap-2 w-full">
                            <i class="pi pi-tag text-sm"></i>
                            <span>{{ slotProps.item.alias }}</span>
                        </div>
                    </div>
                    <!-- lista dos selecionados -->
                    <template v-if="slotProps.item.isTarget">
                        <span class="float-label-copy w-3">
                            <label class="font-bold w-full">{{ slotProps.item.nome }}</label>
                            <Teleport :disabled="!slotProps.item.teleport" to="#target">
                                <span class="p-input-icon-left" :style="teleportedStyle[slotProps.item.nome]" :ref="el => spansAlias[slotProps.item.nome] = el">
                                    <i class="pi pi-tag text-sm"></i>
                                    <InputText v-model="slotProps.item.alias"
                                               class="w-full"
                                               inputId="alias"
                                               @blur="teleport(slotProps.item, 'blur')"
                                               @focus="teleport(slotProps.item, 'focus')"></InputText>
                                </span>
                            </Teleport>
                            <span v-if="slotProps.item.teleport" class="p-input-icon-left">
                                <i class="pi pi-tag text-sm"></i>
                                <InputText class="w-full"></InputText>
                            </span>
                        </span>
                        <div class="w-1">
                            <span class="p-float-label">
                                <InputText inputId="largura" :modelValue="slotProps.item.largura" class="w-full" @update:modelValue="value => slotProps.item.largura = value | 0"></InputText>
                                <label for="largura">Largura</label>
                            </span>
                        </div>
                        <div class="w-2">
                            <span class="p-float-label">
                                <Dropdown inputId="alinhamento"
                                          v-model="slotProps.item.alinhamento"
                                          :options="alinhamentos"
                                          optionValue="value"
                                          optionLabel="label"
                                          class="w-full"></Dropdown>
                                <label for="alinhamento">Alinhamento</label>
                            </span>
                        </div>
                        <div class="w-3">
                            <span class="p-float-label">
                                <Dropdown inputId="alinhamentoCabecalho"
                                          v-model="slotProps.item.alinhamentoCabecalho"
                                          :options="alinhamentos"
                                          optionValue="value"
                                          optionLabel="label"
                                          class="w-full"></Dropdown>
                                <label for="alinhamentoCabecalho">Alinhamento do Cab.</label>
                            </span>
                        </div>
                        <div class="w-2">
                            <span class="p-float-label">
                                <Dropdown inputId="mascara"
                                          v-model="slotProps.item.mascara"
                                          :options="mascaras"
                                          optionValue="value"
                                          optionLabel="label"
                                          class="w-full"></Dropdown>
                                <label for="mascara">Formatar</label>
                            </span>
                        </div>
                        <div class="w-1">
                            <span class="p-float-label">
                                <Dropdown inputId="totalizar"
                                          v-model="slotProps.item.totalizar"
                                          :options="totalizadores"
                                          optionValue="value"
                                          optionLabel="label"
                                          class="w-full"></Dropdown>
                                <label for="totalizar">Totalizar</label>
                            </span>
                        </div>
                        <div class="w-1">
                            <Checkbox inputId="quebra" :binary="true" v-model="slotProps.item.quebra"></Checkbox>
                            <label class="label-checkbox" for="quebra">Quebra</label>
                        </div>
                    </template>
                </div>
            </template>
        </PickList>
    </section>
</template>

<style scoped>

:deep(.p-picklist-source-wrapper) {
    flex: 1 1 25%;
}

:deep(.p-picklist-target-wrapper) {
    flex: 1 1 75%;
}

.float-label-copy {
    display: block;
    position: relative;
}

.float-label-copy label {
    top: -.75rem;
    position: absolute;
    pointer-events: none;
    margin-top: -.5rem;
    line-height: 1;
}
.float-label-copy > label {
    left: 0.5rem;
}

.label-checkbox {
    margin-left: 1px;
    font-size: 12px;
    color: #605e5c;
}

</style>
