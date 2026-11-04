<template>
  <section class="container">
    <Panel header="Documentos">
      <br>
      <div class="p-fluid grid">
        <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <InputText v-model="sequencialContrato" disabled/>
                        <label for="">Sequencial Contratacao</label>
                    </span>
        </div>
        <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <InputText v-model="anoContrato" disabled/>
                        <label for="">Ano Contratacao</label>
                    </span>
        </div>
        <div class="field col-12 md:col-4">
          <DialogPesquisaContratos @sequencialPNCP="preencheCampos"/>
        </div>
      </div>
      <DocumentosContrato :sequencialContrato="sequencialContrato" :anoContrato="anoContrato"/>
    </Panel>
  </section>
</template>

<script setup>
import {ref} from "vue";
import DialogPesquisaContratos from "@modules/Patrimonial/PNCP/components/DialogPesquisaContratos.vue";
import DocumentosContrato from "@modules/Patrimonial/PNCP/components/DocumentosContrato.vue";

const sequencialContrato = ref();
const anoContrato = ref();


const preencheCampos = (data) => {
  sequencialContrato.value = data.numero;
  anoContrato.value = data.ano;
}

</script>

