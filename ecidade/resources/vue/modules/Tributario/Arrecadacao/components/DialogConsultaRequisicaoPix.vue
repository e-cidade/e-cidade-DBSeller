<template>
  <BlockUI v-model="loading">
    <Dialog
      header="Consulta de Requisição PIX - Remessas"
      :maximizable="true"
      :modal="true"
      :style="{ width: '1000px', minHeight: '300px' }"
      v-model:visible="display"
      @hide="removerLoading"
    >
      <section style="width: 100%; height: 100%; min-height: 300px" class="m-auto">
        <DataTable :value="remessa" :rows="10" :loading="loading">
          <Column header="Cod. Requisição" name="codigo">
            <template #body="slotProps">
              {{ slotProps.data.tr10_codigo_emissao }}
            </template>
          </Column>
          <Column header="Data do vencimento" name="data">
            <template #body="slotProps">
              {{ (new Date(slotProps.data.tr10_data_vencimento)).toLocaleDateString() }}
            </template>
          </Column>
          <Column header="Cota Única" name="cotaUnica">
            <template #body="slotProps">
              {{ (slotProps.data.tr10_cotaunica) ? 'SIM' : 'NÃO' }}
            </template>
          </Column>
          <Column header="Situação" name="situacao">
            <template #body="slotProps">
              {{ situacao(slotProps.data) }}
            </template>
          </Column>
          <Column header="Ações" name="acoes">
            <template #body="slotProps">
              <Button 
                v-if="!slotProps.data.tr10_status && (
                  slotProps.data.tr10_concluidos_jobs > 0 ||
                  slotProps.data.tr10_failed_jobs > 0
                )"
                title="Interromper"
                :disabled="blockInterromper"
                @click="interromper(slotProps.data.tr10_sequencial)"
                class="p-button-rounded p-button-warning p-button-outlined p-button-sm mr-2"
              >
                <i class="pi pi-info"></i>
              </Button>
              <Button 
                v-if="slotProps.data.tr10_status && slotProps.data.tr10_failed_jobs > 0"
                :disabled="blockReprocessar"
                @click="tryAgainJob()"
                title="Reprocessar Falhas"
                class="p-button-rounded p-button-info p-button-outlined p-button-sm mr-2"
              >
                <i class="pi pi-sync"></i>
              </Button>
              <!--<Button 
                v-if="slotProps.data.tr10_status && slotProps.data.tr10_failed_jobs > 0"
                title="Visualizar Falhas"
                class="p-button-rounded p-button-danger p-button-outlined p-button-sm"
              >
                <i class="pi pi-eye"></i>
              </Button>-->
            </template>
          </Column>
          <Column header="Processamento" name="processamento">
            <template #body="slotProps">
              <ProgressBar
                :value="slotProps.data.status"
                class="p-progressbar-determinate"
                style="height: 1em"
              >
                {{ slotProps.data.status }}%
              </ProgressBar>
            </template>
          </Column>
          <template #empty>
            Nenhuma remessa de requisição PIX foi encontrada
          </template>
        </DataTable>
      </section>
      <template #footer>
        <Button
          class="p-button-sm p-button-outlined"
          @click="display = false"
        >
          Fechar
        </Button>
      </template>
    </Dialog>
    <ConfirmDialog />
  </BlockUI>
</template>

<script>
import ConfirmDialog from 'primevue/confirmdialog';

export default {
  name: "DialogConsultaRequisicaoPix",
  components: {
    ConfirmDialog
  },
  data() {
    return {
      display: false,
      loading: false,
      refresh: null,
      blockInterromper: false,
      blockReprocessar: false,
      remessa: []
    }
  },
  methods: {
    removerLoading() {
      clearInterval(this.refresh);
    },
    openModal() {
      this.loading = true
      this.display = true

      this.refresh = setInterval(
        () => this.loadingEmissao(),
        30000
      )

      this.loadingEmissao()
    },
    loadingEmissao() {
      window.axios.post('v4/api/tributario/arrecadacao/request-api-pix/listar')
        .then((res) => {
          this.remessa = res.data.data
        })
        .catch(() => {
          this.remessa = []
        })
        .finally(() => this.loading = false)
    },
    situacao(data) {
      let result = ''

      if (data.tr10_status) { 
        if (data.tr10_failed_jobs != 0) {
          result = 'Processo Finalizado com Falhas'
        } else if (data.tr10_num_jobs == 0) {
          result = 'Processo Interrompido'
        } else {
          result = 'Processo Finalizado com Êxito'
        }
      } else {
        if (data.tr10_concluidos_jobs == 0 && data.tr10_failed_jobs == 0) {
          result = 'Carregando dados da emissão'
        } else {
          result = 'Em Processo'
        }
      }

      return result
    },
    interromper(tr10_sequencial) {
      this.$confirm.require({
        message: 'Tem certeza de que deseja continuar?',
        header: 'Confirmação',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
          this.blockInterromper = true

          window.axios.post('v4/api/tributario/arrecadacao/request-api-pix/interromper/' + tr10_sequencial)
            .then((res) => {
              alert('Emissão interrompida com sucesso')
            })
            .catch(() => {
              alert('Ocorreu um erro na tentativa de interromper a fila, tente novamente')
            })
            .finally(() => {
              this.blockInterromper = false
            }) 
        }
      })
    },
    tryAgainJob() {
      this.$confirm.require({
        message: 'Tem certeza de que deseja continuar?',
        header: 'Confirmação',
        icon: 'pi pi-exclamation-triangle',
        accept: () => {
          this.blockReprocessar = true

          window.axios.get('v4/api/tributario/arrecadacao/request-api-pix/try-again-job')
            .then((res) => {
              alert('Operação concluida com sucesso')
            })
            .catch(() => {
              alert('Ocorreu um erro na tentativa de reprocessar a falha')
            })
            .finally(() => this.blockReprocessar = false)
        }
      })
    }
  }
}
</script>