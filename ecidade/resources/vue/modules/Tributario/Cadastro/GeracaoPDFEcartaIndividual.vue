<template>
  <BlockUI class="h-full" :blocked="loading" :fullscreen="true">
    <section class="container">
      <Panel :toggleable="true">
        <template #header>
          <b>Dados Gerais</b>
        </template>
        <div>
          <div class="grid">
            <div class="col-fixed pt-3" style="width: 150px">
              <label>
                <a href="javascript:void(0)" @click="openTipoDebito">
                  Tipo do débito:
                </a>
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
          <div class="grid">
            <div class="col-fixed pt-3" style="width: 150px">
              <label> Tipo de geração: </label>
            </div>
            <div class="col-fixed" style="width: 200px">
              <Dropdown
                class="p-inputtext-sm"
                :options="optionsTipoGeracao"
                v-model="form.optionTipoGeracao"
                optionValue="value"
                style="width: 184.6px"
                optionLabel="title"
              />
            </div>
          </div>
          <div class="grid">
            <div class="col-fixed pt-3" style="width: 150px">
              <label>
                <a href="javascript:void(0)" @click="openMatricula">
                  Matricula:
                </a>
              </label>
            </div>
            <div class="col">
              <InputText
                :disabled="matriculaD"
                class="p-inputtext-sm"
                id="matricula"
                @change="verifyMatricula"
                v-model="form.matricula.descr"
              />
            </div>
          </div>
          <div v-show="form.optionTipoGeracao == 2" class="grid">
            <div class="col-fixed pt-3" style="width: 150px">
              <label>
                <a href="javascript:void(0)">
                  Emissao E-carta:
                </a>
              </label>
            </div>
            <div class="col">
              <InputText
                :disabled="emissaoD"
                class="p-inputtext-sm"
                id="matricula"
                v-model="form.emissao"
              />
            </div>
          </div>
          <div class="grid">
            <div class="col-fixed pt-3" style="width: 150px">
              <label>
                <label>
                  Modelo:
                </label>
              </label>
            </div>
            <div class="col-fixed" style="width: 200px">
              <BlockUI :blocked="modeloEmissaoD">
                <Dropdown
                  class="p-inputtext-sm"
                  :options="optionsModelo"
                  v-model="form.optionModel"
                  optionValue="tr12_sequencial"
                  style="width: 184.6px"
                  optionLabel="tr12_nome"
                  :disabled="modeloEmissaoD"
                />
              </BlockUI>
            </div>
          </div>
        </div>
      </Panel>
      <Panel class="mt-4" v-show="form.optionTipoGeracao == 1" :toggleable="true">
        <template #header>
          <b>Dados da Cota Única</b>
        </template>
        <div>
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
                      vence em {{ dataPTBR(slotProps.data.k00_dtvenc) }}
                    </template>
                  </Column>
                  <template #empty> Nenhuma cota única foi encontrada </template>
                </DataTable>
              </BlockUI>
            </div>
            <div v-show="false" class="col-fixed" style="width: 270px">
              <label>Considera registros de cota única:</label><br />
              <InputSwitch
                @change="clearCotaUnica"
                class="mt-2"
                v-model="form.contaUnica"
              />
            </div>
            <div v-show="false" class="col-4">
              <label>Data do vencimento:</label><br />
              <Calendar
                :disabled="form.contaUnica"
                class="mt-2 p-inputtext-sm"
                v-model="form.dataVencimento"
                dateFormat="dd/mm/yy"
              />
            </div>
          </div>
        </div>
      </Panel>
      <div class="border-1 border-500 p-4 mt-4">
        <Button
          class="p-button-sm mr-2"
          :disabled="!verifyForm()"
          @click="emitir"
        >
          Emitir
        </Button>
        <Button
          class="p-button-sm"
          :disabled="!verificarMatricula()"
          @click="consultar"
        >
          Consultar Dados Matricula
        </Button>
      </div>
      <DialogTipoDebito ref="dialog_tipo_debito" @selectRow="selectTipoDebito" />
      <DialogMatricula ref="dialog_matricula" @selectRow="selectMatricula" />
      <Dialog
        header="Consulta Dados por Matricula"
        :maximizable="true"
        :modal="true"
        :style="{ width: '1000px', minHeight: '300px' }"
        v-model:visible="display"
      >
        <div class="grid">
          <div class="col-6">
            <h3>Endereco de Origem</h3>
            <div>
              <div><b>Matricula Imovel: </b>{{ this.dadosMatricula.dadosEnderecoOrigem?.matriculaImovel ?? '' }}</div>
              <div><b>Proprietario: </b>{{ this.dadosMatricula.dadosEnderecoOrigem?.proprietario ?? '' }}</div>
              <div><b>Distrito: </b>{{ this.dadosMatricula.dadosEnderecoOrigem?.distrito ?? '' }}</div>
              <div><b>CEP:</b> {{ this.dadosMatricula.dadosEnderecoOrigem?.cep ?? '' }}</div>
              <div><b>Zona:</b> {{ this.dadosMatricula.dadosEnderecoOrigem?.zona ?? '' }}</div>
              <div><b>Lote:</b> {{ this.dadosMatricula.dadosEnderecoOrigem?.lote ?? '' }}</div>
              <div><b>Quadra: </b>{{ this.dadosMatricula.dadosEnderecoOrigem?.quadra ?? '' }}</div>
              <div><b>Logradouro: </b>{{ this.dadosMatricula.dadosEnderecoOrigem?.logradouro ?? '' }}</div>
              <div><b>Numero: </b>{{ this.dadosMatricula.dadosEnderecoOrigem?.numero ?? '' }}</div>
              <div><b>Complemento: </b>{{ this.dadosMatricula.dadosEnderecoOrigem?.complemento ?? '' }}</div>
            </div>
          </div>
          <div class="col-6">
            <h3>Endereco de Entrega</h3>
            <div>
              <div><b>Destinatario: </b>{{ this.dadosMatricula.dadosEnderecoEntrega?.destinatario ?? '' }}</div>
              <div><b>Municipio: </b>{{ this.dadosMatricula.dadosEnderecoEntrega?.municipio ?? '' }}</div>

              <div><b>Bairro: </b>{{ this.dadosMatricula.dadosEnderecoEntrega?.bairro ?? '' }}</div>
              <div><b>CEP:</b> {{ this.dadosMatricula.dadosEnderecoEntrega?.cep ?? '' }}</div>
              <div><b>Endereco: </b>{{ this.dadosMatricula.dadosEnderecoEntrega.endereco ?? '' }}</div>
              <div><b>Numero: </b>{{ this.dadosMatricula.dadosEnderecoEntrega?.numero ?? '' }}</div>
              <div><b>Complemento: </b>{{ this.dadosMatricula.dadosEnderecoEntrega?.complemento ?? '' }}</div>
            </div>
          </div>
          <div class="col-6">
            <h3>Caracteristicas do Imovel</h3>
            <div>
              <div><b>Area Comum: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.area_comum ?? '' }}</div>
              <div><b>Area Garagem: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.area_garagem ?? '' }}</div>
              <div><b>Area Jirau: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.area_jirau ?? '' }}</div>
              <div><b>Area Lote Vila: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.area_lote_vila ?? '' }}</div>
              <div><b>Area Privativa: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.area_privativa ?? '' }}</div>
              <div><b>Area Terreno: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.area_terreno ?? '' }}</div>
              <div><b>Area Tributavel da Unidade: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.area_tributavel_da_unidade ?? '' }}</div>
              <div><b>Caracteristica Imovel: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.caracteristica_imovel ?? '' }}</div>
              <div><b>Metro Linear Testada: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.metro_linear_testada ?? '' }}</div>
              <div><b>Testada Terreno: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.testada_terreno ?? '' }}</div>
              <div><b>Tipo Imovel: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.tipo_imovel ?? '' }}</div>
              <div><b>Total Construido Lote: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.total_construido_lote ?? '' }}</div>
              <div><b>Unidades no Lote: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.unidades_no_lote ?? '' }}</div>
              <div><b>Utilizacao Imovel: </b>{{ this.dadosMatricula.infoAdicional?.caracteristicas?.utilizacao_imovel ?? '' }}</div>
            </div>
          </div>
          <div class="col-6">
            <h3>Calculo</h3>
            <div>
              <div><b>Aliquota: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.aliquota ?? '' }}</div>
              <div><b>Incentivo Cultural: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.incentivo_cultural ?? '' }}</div>
              <div><b>IPTU Bom Pagador: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.iptu_bom_pagador ?? '' }}</div>
              <div><b>IPTU DECAD: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.iptu_decad ?? '' }}</div>
              <div><b>IPTU DEVIDO: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.iptu_devido ?? '' }}</div>
              <div><b>TCIL: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.tcil_imovel ?? '' }}</div>
              <div><b>Total a Pagar: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.total_a_pagar ?? '' }}</div>
              <div><b>Valor IPTU: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.valor_iptu ?? '' }}</div>
              <div><b>Valor Nit Nota: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.valor_nit_nota ?? '' }}</div>
              <div><b>Valor Venal: </b>{{ this.dadosMatricula.infoAdicional?.calculo?.valor_venal ?? '' }}</div>
            </div>
          </div>
        </div>
      </Dialog>
      <Toast />
    </section>
  </BlockUI>
</template>

<script>
import Toast from "primevue/toast"
import DialogTipoDebito from "../Arrecadacao/components/DialogTipoDebito.vue"
import DialogMatricula from "./components/DialogMatricula.vue"

export default {
  name: "GeracaoPDFEcartaIndividual",
  components: {
    DialogTipoDebito,
    DialogMatricula,
    Toast
},
  data() {
    return {
      display: false,
      loading: false,
      tipoDebitoD: false,
      emissaoD: false,
      matriculaD: false,
      modeloEmissaoD: false,
      blockCotaUnica: false,
      dadosMatricula: null,
      form: {
        emissao: '',
        optionTipoGeracao: 1,
        optionModel: 1,
        contaUnica: true,
        dataVencimento: '',
        cotaUnicaSelect: {
          k00_percdes: 0,
          k00_dtvenc: ''
        },
        tipoDebito: {
          k00_tipo: "",
          k00_descr: ""
        },
        matricula: {
          j01_matric: '',
          descr: ''
        }
      },
      optionsTipoGeracao: [
        { value: 1, title: 'Emissão' },
        { value: 2, title: 'Reemissão' }
      ],
      optionsModelo: [],
      cotas: []
    }
  },
  created() {
    this.loadingModeloEmissao();
  },
  methods: {
    verificarMatricula() {
      if (!this.form.matricula.j01_matric || !this.form.matricula.descr) {
        return false
      }
      
      return true
    },
    consultar() {
      this.loading = true
      this.dadosMatricula =  null

      window.axios.get(
          'v4/api/tributario/cadastro/emissao_ecarta/dados_matricula/' +
          this.form.matricula.j01_matric
        )
        .then((res) => {
          this.display = true;
          this.dadosMatricula = res.data.data

          console.log(this.dadosMatricula.dadosEnderecoOrigem.logradouro)
        })
        .catch((error) => alert(error.response.data.message))
        .finally(() => this.loading = false)
    },
    dataPTBR(data) {
      data = data.replace(/\D/g, '');
      return data.replace(/(\d{4})?(\d{2})?(\d{2})/g, '$3/$2/$1')
    },
    selectTipoDebito(select) {
      this.form.tipoDebito.k00_tipo = select.k00_tipo;
      this.form.tipoDebito.k00_descr = select.k00_descr;

      this.loadingCotaUnica(this.form.tipoDebito.k00_tipo);
    },
    selectMatricula(select) {
      this.form.matricula.j01_matric = select.j01_matric

      this.form.matricula.descr = select.j01_matric + ' - ' + select.cgm.z01_nomecomple
    },
    openTipoDebito() {
      this.$refs["dialog_tipo_debito"].openModal();
    },
    openMatricula() {
      this.$refs['dialog_matricula'].openModal();
    },
    verifyForm() {  
      if (!this.form.matricula.j01_matric || !this.form.matricula.descr) {
        return false
      } else if(!this.form.tipoDebito || !this.form.tipoDebito.k00_descr) {
        return false
      }

      if (this.form.optionTipoGeracao == 1) {
        if (this.form.contaUnica && this.form.cotaUnicaSelect == null) {
          return false
        } else if (!this.form.contaUnica && this.form.dataVencimento == '') {
          return false
        }
      } else if (this.form.optionTipoGeracao == 2) {
        return false
      }

      return true
    },
    async verifyTipoDebito() {
      this.tipoDebitoD = true

      let num = this.form.tipoDebito.k00_descr

      if (!isNaN(num)) {
        await this.$refs["dialog_tipo_debito"]
          .verifyTipoDebito(num)
          .then((res) => {
            const tipoDebito = res.data.data

            this.form.tipoDebito.k00_tipo  = tipoDebito.k00_tipo
            this.form.tipoDebito.k00_descr = tipoDebito.k00_descr

            this.loadingCotaUnica(this.form.tipoDebito.k00_tipo);
          })
          .catch((error) => {
            this.form.tipoDebito.k00_descr = ""
            this.form.tipoDebito.k00_tipo  = ""

            this.$toast.add({
              severity: "error",
              summary: "Aviso",
              detail: error?.response?.data?.message ?? "",
              life: 3000,
            })
          })
      }

      this.tipoDebitoD = false;
    },
    async verifyMatricula() {
      this.matriculaD = true

      let num = this.form.matricula.descr

      if (!isNaN(num)) {
        await this.$refs['dialog_matricula']
        .verifyMatricula(num)
        .then((res) => this.selectMatricula(res))
        .catch((error) => {
          this.$toast.add({
            severity: "error",
            summary: "Aviso",
            detail: error ?? "",
            life: 3000,
          })
        })
      }

      this.matriculaD = false
    },
    async loadingModeloEmissao() {
      this.modeloEmissaoD = true;

      window.axios.get('v4/api/tributario/cadastro/emissao_ecarta/modelos')
        .then((res) => {
          const data = res.data.data

          this.optionsModelo = data
        })
        .catch((error) => {
          this.optionsModelo = []

          this.$toast.add({
            severity: "error",
            summary: "Aviso",
            detail: error?.response.data.message ?? "",
            life: 3000,
          })
        })
        .finally(() => this.modeloEmissaoD = false);
    },
    async emitir() {
      this.loading = true
    
      await window.axios.post(
        'v4/api/tributario/cadastro/emissao_ecarta/emitir_individual',
        this.form
      )
        .then((res) => {
          alert(res.data.message)

          if (this.form.optionTipoGeracao == 1) {
            window.open(
              window.ECIDADE_PATH + '/w/1/' + res.data.data[0],
              '',
              'height=800, width=600'
            );
          }

        })
        .catch((error) => alert(error.response.data.message))
        .finally(() => this.loading = false)
    },
    clearCotaUnica() {
      this.form.cotaUnicaSelect = null
      this.form.dataVencimento  = ''
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
    }
  }
};
</script>
