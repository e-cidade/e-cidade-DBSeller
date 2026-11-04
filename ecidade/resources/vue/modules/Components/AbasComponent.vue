<script setup>
import { ref, toRaw } from "vue";

const props = defineProps({
    abas: { required: true },
    activeIndex: { type: Number, default: 0 },
    lazy: { type: Boolean, default: false },
    bases: {}
});

const emit = defineEmits(['tabClick']);

const componentesAbas = ref({});

const getComponenteAba = (nome) => {
    return componentesAbas.value[nome.toLowerCase()];
}

const defineRef = (el, componente) => {
    if (componente.__name === undefined) {
        console.warn(`Não foi possível acessar o nome do componente "${componente.__file}".`);
        return;
    }

    componentesAbas.value[componente.__name.toLowerCase()] = el;
}

const onTabClick = ({ originalEvent, index }) => {
    if (typeof props.abas[index]?.onTabClick === 'function') {
        props.abas[index].onTabClick(originalEvent);
    }

    emit('tabClick', { originalEvent, index });
}

defineExpose({ getComponenteAba });
</script>
<template>
    <TabView class="tabview-custom" :lazy="lazy" :active-index="activeIndex" @tabClick="onTabClick">
        <TabPanel v-for="aba in abas" :disabled="aba.disabled === undefined ? false : aba.disabled">
            <template #header>
                <i :class="aba.iconClass === undefined ? 'pi pi-angle-right mr-2' : aba.iconClass + ' mr-2'"></i>
                <span>{{ aba.nome }}</span>
            </template>
            <component :is="toRaw(aba.componente)" :bases="props.bases"
                       :ref="el => defineRef(el, aba.componente)"
                       v-bind="aba.props === undefined ? {} : aba.props"
                       v-on="aba.eventos === undefined ? {} : aba.eventos"></component>
        </TabPanel>
    </TabView>
</template>
<style scoped>

</style>
