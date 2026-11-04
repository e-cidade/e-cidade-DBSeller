<script setup>
import { ref, onMounted } from "vue";
import ModalLoading from "../../../Components/ModalLoading.vue";
const props = defineProps(['visible', 'noticia'])
const emits = defineEmits(['update:visible'])

const imagem = ref(props.noticia.imagem)
console.log(props.noticia)
const loading = ref(false)
const visivel = ref(props.visible)
const exibeCard = ref(false)

onMounted(async () => {
    loading.value = true
    loading.value = false
    exibeCard.value = true
})
</script>

<template>
    <ModalLoading :isLoading="loading"/>
    <Dialog header="Visualizador Noticias" :visible="visivel" @update:visible="value => emits('update:visible', value)" :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '80vw'}">
        <Card style="width: 25em; margin: 0 auto" class="mt-5" v-if="exibeCard">
            <template #header>
                <Image :src="imagem" alt="Image" width="360" />
            </template>
            <template #title>
                <div v-html="props.noticia.titulo"></div>
            </template>
            <template #content>
                <p class="m-0" v-html="props.noticia.texto">
                </p>
            </template>
        </Card>
    </Dialog>
</template>

<style scoped>
span {
    cursor: pointer
}
</style>
