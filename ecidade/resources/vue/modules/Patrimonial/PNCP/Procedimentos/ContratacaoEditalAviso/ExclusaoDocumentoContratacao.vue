<template>
    <section class="container">
        <Panel header="Documentos">
            <br>
            <div class="p-fluid grid">
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <InputText v-model="sequencialCompra" disabled/>
                        <label for="">Sequencial Contratacao</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <InputText v-model="anoCompra" disabled/>
                        <label for="">Ano Contratação</label>
                    </span>
                </div>
                <div class="field col-12 md:col-4">
                    <DialogPesquisaContratacoes @sequencialPNCP="preencheCampos"/>
                </div>
            </div>
            <DocumentosContratacao :sequencialCompra="sequencialCompra" :anoCompra="anoCompra"/>
        </Panel>
    </section>
</template>

<script setup>
import DocumentosContratacao from "@modules/Patrimonial/PNCP/components/DocumentosContratacao.vue";
import {ref} from "vue";
import DialogPesquisaContratacoes from "@modules/Patrimonial/PNCP/components/DialogPesquisaContratacoes.vue";

const sequencialCompra = ref();
const anoCompra = ref();


const teste = () => {
    console.log('teste')
}

const preencheCampos = (data) => {
    sequencialCompra.value = data.numero;
    anoCompra.value = data.ano;
}

</script>

