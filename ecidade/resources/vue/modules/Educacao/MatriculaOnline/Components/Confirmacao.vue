<script setup>
import { ref } from "vue"
import { Inscricao } from "../Models/Inscricao.js";
const inscricao = new Inscricao()

const dados = ref(inscricao.toTable(['tipoResponsavel', 'fase', 'service']))
dados.value = dados.value.filter(item => item.valor !== false)
</script>
<template>
    <div class="border-2 border-dashed surface-border border-round surface-ground flex-auto flex justify-content-center align-items-center font-medium">
        <div class="p-fluid grid mt-3" style="max-width: 700px;">
            <Card>
                <template #title></template>
                <template #content>
                    <Divider />
                    <ScrollPanel style="height: 100vh">
                        <p class="m-0" v-for="dado in dados">
                            <strong>{{ dado.campo }}</strong><br />
                            <div v-html="dado.valor"></div>
                            <Divider />
                        </p>
                </ScrollPanel>
                </template>
            </Card>
        </div>
    </div>
</template>
<style scoped>
</style>