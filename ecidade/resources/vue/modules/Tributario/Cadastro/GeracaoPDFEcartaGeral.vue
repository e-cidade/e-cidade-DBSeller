<template>
  <blockUI class="h-full" :blocked="loading" :fullscreen="true">
    <section class="container h-full">
      <Panel>
        <template #header>
          <b>Dados Gerais</b>
        </template>
        <div>
          <div class="grid">
            <div class="col-fixed pt-3" style="width: 150px">
              <label> Código de remessa: </label>
            </div>
            <div class="col">
              <InputText v-model="form.codigo" disabled class="p-inputtext-sm" />
            </div>
          </div>
          <div class="grid">
            <div class="col-fixed pt-3" style="width: 150px">
              <label>
                <a>
                  Remessa pix:
                </a>
              </label>
            </div>
            <div class="col-fixed" style="width: 200px">
              <Dropdown placeholder="Selecione uma Remessa PIX" :disabled="blocks.selectRemessaPix" class="p-inputtext-sm" :options="options.remessa"
                v-model="form.remessa" optionValue="tr10_codigo_emissao" style="width: 184.6px"
                optionLabel="tr10_codigo_emissao" />
            </div>
          </div>
          <div class="grid">
            <div class="col-fixed pt-3" style="width: 150px">
              <label>
                <a>
                  Modelo:
                </a>
              </label>
            </div>
            <div class="col-fixed" style="width: 200px">
              <Dropdown placeholder="Selecione um Modelo de Emissao" :disabled="blocks.selectModeloPix" class="p-inputtext-sm" :options="options.modelo"
                v-model="form.modelo" optionValue="tr12_sequencial" style="width: 184.6px" optionLabel="tr12_nome" />
            </div>
          </div>
        </div>
      </Panel>
      <div class="border-1 border-500 p-4 mt-4">
        <Button @click="emitir" class="p-button-sm mr-2">
          Emitir
        </Button>
        <Button class="p-button-sm p-button-info" @click="this.$refs['dialog_consulta_emissao_ecarta'].openModal()" >
          Consultar
        </Button>
      </div>
      <Toast />
      <DialogConsultaEmissaoEcartaGeral ref="dialog_consulta_emissao_ecarta" />
    </section>
  </blockUI>
</template>

<script>
import Toast from "primevue/toast"
import DialogConsultaEmissaoEcartaGeral from "./components/DialogConsultaEmissaoEcartaGeral.vue";
import DialogTipoDebito from "../Arrecadacao/components/DialogTipoDebito.vue"

export default {
  name: "GeracaoPDFEcartaGeral",
  components: { Toast, DialogTipoDebito, DialogConsultaEmissaoEcartaGeral },
  created() {
    this.loadingModeloEmissao()
    this.loadingRequisicaoPix()
    this.newCodigo()
  },
  data() {
    return {
      loading: false,
      form: {
        codigo: null,
        remessa: null,
        modelo: null,
        optionTipoGeracao: 3
      },
      options: {
        remessa: [],
        modelo: []
      },
      blocks: {
        selectRemessaPix: true,
        selectModeloPix: true,
        inputTipoDebito: false
      }
    }
  },
  methods: {
    newCodigo() { 
      this.form.codigo = window.uuid()
    },
    async loadingRequisicaoPix() {
      this.blocks.selectRemessaPix = true
      this.options.remessa = []

      window.axios.get('v4/api/tributario/arrecadacao/request-api-pix/concluidos')
        .then((res) => {
          const data = res.data.data

          this.options.remessa = data
        })
        .catch((error) => {
          this.options.remessa = []

          this.$toast.add({
            severity: "error",
            summary: "Aviso",
            detail: error?.response.data.message ?? "",
            life: 3000,
          })
        })
        .finally(() => this.blocks.selectRemessaPix = false)

    },
    async loadingModeloEmissao() {
      this.blocks.selectModeloPix = true
      this.options.modelo = []

      window.axios.get('v4/api/tributario/cadastro/emissao_ecarta/modelos')
        .then((res) => {
          const data = res.data.data

          this.options.modelo = data
        })
        .catch((error) => {
          this.options.modelo = []

          this.$toast.add({
            severity: "error",
            summary: "Aviso",
            detail: error?.response.data.message ?? "",
            life: 3000,
          })
        })
        .finally(() => this.blocks.selectModeloPix = false)
    },
    emitir() {
      this.loading = true

      window.axios.post('v4/api/tributario/cadastro/emissao_ecarta/geral', this.form)
        .then(() => alert('Emissao Geral de PDFs Ecarta criada com sucesso'))
        .catch((error) => {
          const data = error.response.data;

          alert(data.message);
        })
        .finally(() => {
          this.newCodigo()
          this.loading = false
        })
    }
  }
};
</script>
