<template>
  <BlockUI :blocked="loading" :fullscreen="true" style="height: 100%">
    <section class="container">
      <Panel :toggleable="true">
        <template #header>
          <b>Dados da Requisição</b>
        </template>
        <div class="grid">
          <div class="col-fixed pt-3" style="width: 180px">
            <label for="codigo_requisicao">
              Código da Requisição:
            </label>
          </div>
          <div class="col">
            <InputText
              class="p-inputtext-sm"
              v-model="form.codigoRequisicao"
              id="codigo_requisicao"
              :disabled="true"
            />
          </div>
        </div>
        <div class="grid mb-3">
          <div class="col-fixed pt-3" style="width: 180px">
            <label for="tipo_debito">
              <a href="javascript:void(0)" @click="openTipoDebito">
                Tipo de Débito:
              </a>
              <span class="text-red-500">*</span>
            </label>
          </div>
          <div class="col">
            <InputText
              class="p-inputtext-sm"
              v-model="form.tipoDebito.k00_descr"
              @change="verifyTipoDebito"
              id="tipo_debito"
              :disabled="tipoDebitoD"
            />
          </div>
        </div>
      </Panel>
      <BlockUI :blocked="blockBancos">
        <Panel class="mt-4" :toggleable="true">
          <template #header>
            <b>Dados da API</b>
          </template>
          <div class="grid">
            <div class="col-fixed pt-3" style="width: 110px">
              <label for="username1"> Banco / API: </label>
            </div>
            <div class="col-fixed" style="width: 210px">
              <Dropdown
                style="min-width: 216px"
                :options="filterBancos()"
                optionLabel="dadosBanco.db90_descr"
                :filter="true"
                placeholder="Selecione um banco"
                @change="addBanco"
              />
            </div>
            <div class="col px-0 sm:pl-4">
              <DataTable :value="form.bancos" responsiveLayout="scroll">
                <Column field="dadosBanco.db90_descr" header="Banco"></Column>
                <Column header="Ação" class="text-center" style="width: 60px">
                  <template #body="slotProps">
                    <Button
                      icon="pi pi-trash"
                      class="p-button-rounded p-button-danger p-button-sm"
                      @click="(event) => removeBanco(event, slotProps)"
                    >
                    </Button>
                    <ConfirmPopup group="removerBanco"></ConfirmPopup>
                  </template>
                </Column>
                <template #empty> Nenhum banco selecionado </template>
              </DataTable>
            </div>
          </div>
        </Panel>
      </BlockUI>
      <div class="border-1 border-500 p-4 mt-4">
        <div class="grid">
          <div class="col">
            <BlockUI :blocked="blockCotaUnica">
              <DataTable :value="cotas" responsiveLayout="scroll">
                <Column style="width: 44px">
                  <template #body="slotProps">
                    <RadioButton
                      name="cota"
                      :value="slotProps.data"
                      v-model="form.cotaUnicaSelect"
                      :disabled="!form.contaUnica"
                    />
                  </template>
                </Column>
                <Column>
                  <template #body="slotProps">
                    {{ slotProps.data.k00_percdes }}% 
                    vence em {{ (new Date(slotProps.data.k00_dtvenc)).toLocaleDateString() }}
                  </template>
                </Column>
                <template #empty> Nenhuma cota única foi encontrada </template>
              </DataTable>
            </BlockUI>
          </div>
          <div class="col-fixed" v-show="false" style="width: 270px">
            <label>Considera registros de cota única:</label><br />
            <InputSwitch
              @change="clearCotaUnica"
              class="mt-2"
              v-model="form.contaUnica"
            />
          </div>
          <div class="col-4" v-show="false">
            <label>Data do vencimento:</label><br />
            <Calendar
              :disabled="form.contaUnica"
              class="mt-2 p-inputtext-sm"
              v-model="form.dataVencimento"
              dateFormat="dd/mm/yy"
            />
          </div>
        </div>
        <div class="mt-4">
          <Button 
            :disabled="enviar"
            class="p-button-sm mr-2"
            @click="enviarForm"
          >
            Enviar
          </Button>
          <Button
            @click="openRequisicaoPix"
            class="p-button-sm p-button-info mr-2"
          >
            Consultar
          </Button>
          <Button 
            @click="clearForm"
            class="p-button-sm p-button-danger"
          >
            Limpar
          </Button>
          <ConfirmPopup group="limparForm"></ConfirmPopup>
        </div>
      </div>
      <DialogTipoDebito ref="dialog_tipo_debito" @selectRow="selectTipoDebito" />
      <DialogGrupoDebito
        ref="dialog_grupo_debito"
        @selectRow="selectGrupoDebito"
      />
      <DialogConsultaRequisicaoPix ref="dialog_consulta_requisicao_pix" />
      <Toast />
    </section>
  </BlockUI>
</template>

<script>
import ConfirmPopup from "primevue/confirmpopup";
import Toast from "primevue/toast";
import DialogTipoDebito from "./components/DialogTipoDebito.vue";
import DialogGrupoDebito from "./components/DialogGrupoDebito.vue";
import DialogConsultaRequisicaoPix from "./components/DialogConsultaRequisicaoPix.vue";

export default {
  name: "RequisicaoAPIPIXGeral",
  components: {
    ConfirmPopup,
    Toast,
    DialogTipoDebito,
    DialogGrupoDebito,
    DialogConsultaRequisicaoPix
  },
  created() {
    this.newIndexUuid()
  },
  data() {
    return {
      display: false,
      tipoDebitoD: false,
      blockBancos: true,
      blockCotaUnica: false,
      loading: false,
      form: {
        contaUnica: true,
        cotaUnicaSelect: null,
        dataVencimento: "",
        codigoRequisicao: "",
        tipoDebito: {
          k00_tipo: "",
          k00_descr: "",
        },
        bancos: []
      },
      bancos: [],
      cotas: [],
    };
  },
  async mounted() {
    await window.axios
      .get("v4/api/configuracao/banco-pix/listar")
      .then((res) => {
        const bancos = res.data.message;

        if (Array.isArray(bancos)) {
          this.bancos = bancos;
        } else {
          for (let index in bancos) {
            this.bancos.push(bancos[index]);
          }
        }
      })
      .finally(() => (this.blockBancos = false));
  },
  methods: {
    newIndexUuid() {
      this.form.codigoRequisicao = window.uuid()
    },
    selectTipoDebito(select) {
      this.form.tipoDebito.k00_tipo = select.k00_tipo;
      this.form.tipoDebito.k00_descr = select.k00_descr;

      this.loadingCotaUnica(this.form.tipoDebito.k00_tipo);
    },
    selectGrupoDebito(select) {
      this.form.grupoDebito.k03_descr = select.k03_descr;
      this.form.grupoDebito.k03_tipo = select.k03_tipo;
    },
    clearCotaUnica() {
      this.form.cotaUnicaSelect = null
      this.form.dataVencimento  = ''
    },
    addBanco(event) {
      this.form.bancos.push(event.value);
    },
    removeBanco(event, slot) {
      this.$confirm.require({
        target: event.currentTarget,
        group: "removerBanco",
        message: "Tem certeza de que deseja continuar?",
        icon: "pi pi-exclamation-triangle",
        accept: () => {
          for (let index in this.form.bancos) {
            if (
              this.form.bancos[index].db90_codban.indexOf(
                slot.data.db90_codban
              ) >= 0
            ) {
              this.form.bancos.splice(index, 1);
            }
          }

          this.$toast.add({
            severity: "info",
            summary: "Aviso",
            detail: "Banco removido com sucesso!",
            life: 3000,
          });
        },
      });
    },
    filterBancos() {
      return this.bancos.filter((banco) => {
        for (let bancoSelect of this.form.bancos) {
          if (bancoSelect === banco) {
            return false;
          }
        }

        return true;
      });
    },
    openTipoDebito() {
      this.$refs["dialog_tipo_debito"].openModal();
    },
    openRequisicaoPix() {
      this.$refs["dialog_consulta_requisicao_pix"].openModal();
    },
    async verifyTipoDebito() {
      this.tipoDebitoD = true;

      let num = this.form.tipoDebito.k00_descr;

      if (!isNaN(num)) {
        await this.$refs["dialog_tipo_debito"]
          .verifyTipoDebito(num)
          .then((res) => {
            const tipoDebito = res.data.data;

            this.form.tipoDebito.k00_tipo = tipoDebito.k00_tipo;
            this.form.tipoDebito.k00_descr = tipoDebito.k00_descr;

            this.loadingCotaUnica(tipoDebito.k00_tipo);
          })
          .catch((error) => {
            this.form.tipoDebito.k00_descr = "";
            this.form.tipoDebito.k00_tipo = "";

            this.$toast.add({
              severity: "error",
              summary: "Aviso",
              detail: error?.response?.data?.message ?? "",
              life: 3000,
            });
          });
      }

      this.tipoDebitoD = false;
    },
    async verifyGrupoDebito() {
      this.grupoDebitoD = true;

      let num = this.form.grupoDebito.k03_descr;

      if (!isNaN(num)) {
        await this.$refs["dialog_grupo_debito"]
          .verifyGrupoDebito(num)
          .then((res) => {
            const grupoDebito = res.data.data;

            this.form.grupoDebito.k03_tipo = grupoDebito.k03_tipo;
            this.form.grupoDebito.k03_descr = grupoDebito.k03_descr;
          })
          .catch((error) => {
            this.form.grupoDebito.k03_descr = "";
            this.form.grupoDebito.k03_tipo = "";

            this.$toast.add({
              severity: "error",
              summary: "Aviso",
              detail: error?.response?.data?.message ?? "",
              life: 3000,
            });
          });
      }

      this.grupoDebitoD = false;
    },
    async loadingCotaUnica(k00_tipo) {
      this.blockCotaUnica = true;

      await window.axios
        .get(`v4/api/tributario/arrecadacao/cota-unica/search/${k00_tipo}`)
        .then((res) => this.cotas = res.data.data)
        .finally(() => (this.blockCotaUnica = false));

      if (this.cotas.length === 0) {
        this.$toast.add({
          severity: "info",
          summary: "Aviso",
          detail: "Nenhuma cota unica foi encontrada para esse tipo de débito",
          life: 3000
        })
      }
    },
    async enviarForm() {
      const data = Object.assign({}, this.form)

      if (
        this.form.dataVencimento && 
        typeof this.form.dataVencimento === 'object'
      ) {
        data.dataVencimento = data.dataVencimento
          .toISOString().slice(0, 10)
      }

      data.contaUnica = (data.contaUnica) ? 1 : 0

      this.loading = true

      await window.axios.post(
        'v4/api/tributario/arrecadacao/request-api-pix/solicitar',
        data
      ).then((res) => {
        this.clear()
        alert(res.data.data)
      })
        .catch((error) => alert(
          error.response.data?.message ?? 'Ocorreu um erro desconhecido'))
        .finally(() => this.loading = false)
    },
    clearForm(event) {
      this.$confirm.require({
        target: event.currentTarget,
        group: "limparForm",
        message: "Tem certeza de que deseja continuar?",
        icon: "pi pi-exclamation-triangle",
        accept: () => {
          this.clear()

          this.$toast.add({
            severity: "info",
            summary: "Aviso",
            detail: "Formulário limpo com sucesso!",
            life: 3000,
          });
        },
      });
    },
    clear() {
      this.form.contaUnica            = true
      this.form.cotaUnicaSelect       = null
      this.form.dataVencimento        = ''
      this.form.codigoRequisicao      = ''
      this.form.tipoDebito.k00_tipo   = ''
      this.form.tipoDebito.k00_descr  = ''
      this.form.bancos                = []
      this.cotas                      = []

      this.newIndexUuid()
    }
  },
  computed: {
    enviar() {
      if (
        this.form.tipoDebito.k00_tipo &&
        this.form.bancos.length > 0
      ) {
        if (this.form.contaUnica && this.form.cotaUnicaSelect !== null) {
          return false
        } else if (!this.form.contaUnica && this.form.dataVencimento) {
          return false
        }
      }

      return true
    }
  }
};
</script>
