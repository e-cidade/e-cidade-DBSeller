<template>
    <Dialog 
        v-model:visible="display"
        header="Detalhes Geração PDF E-carta - Geral"
        :maximizable="true"
        :modal="true"
        :style="{ width: '1300px', minHeight: '300px' }"
    >
        <ConfirmPopup group="interromper"></ConfirmPopup>
        <div class="h-full" style="min-height: 300px" >
            <DataTable :value="emissoes" :loading="loading">
                <Column field="tr13_codigo" header="Codigo da Geracao"  />
                <Column field="tr13_tipo" header="Tipo de Debito"  />
                <Column field="remessa.tr10_codigo_emissao" header="Codigo da Remessa PIX"  />
                <Column field="modelo.tr12_nome" header="Modelo"  />
                <Column field="status_pdf" header="Status geracao do PDF">
                    <template #body="slotProps">
                        <ProgressBar
                            :value="slotProps.data.status_pdf"
                            class="p-progressbar-determinate"
                            style="height: 1em"
                        >
                            {{ slotProps.data.status_pdf }}%
                        </ProgressBar>
                    </template>
                </Column>
                <Column field="status_storage" header="Status envio Storage">
                    <template #body="slotProps">
                        <ProgressBar
                            :value="slotProps.data.status_storage"
                            class="p-progressbar-determinate"
                            style="height: 1em"
                        >
                            {{ slotProps.data.status_storage }}%
                        </ProgressBar>
                    </template>
                </Column>
                <Column field="" header="Ações">
                    <template #body="slotProps">
                        <Button
                            title="Detalhes da Geracao"
                            @click="openDetalhe(slotProps.data)"
                            class="p-button-rounded p-button-info p-button-outlined p-button-sm mr-2"
                        >
                            <i class="pi pi-eye"></i>
                        </Button>
                        <Button
                            v-show="(slotProps.data.tr13_status == 'Em Processo')"
                            @click="(event) => interromper(event, slotProps.data.tr13_codigo)"
                            title="Interromper"
                            class="p-button-rounded p-button-danger p-button-outlined p-button-sm mr-2"
                        >
                            <i class="pi pi-info"></i>
                        </Button>
                    </template>
                </Column>
                <template #empty>
                    Nenhuma remessa de requisição PIX foi encontrada
                </template>
            </DataTable>
        </div>
    </Dialog>
    <Sidebar v-model:visible="displayDetalhe" position="full">
        <div>
            <h3>Codigo da Geracao - {{ this.emissao.tr13_codigo }}</h3>
            <DataTable :value="detalhes.data" :loading="loadingDetalhe">
                <template #header>
                    <form class="grid" onsubmit="return false" @submit="openDetalhe(this.emissao)">
                        <div class="col-fixed" style="width: 240px">
                            <InputNumber
                                mode="decimal"
                                :useGrouping="false"
                                class="w-full"
                                v-model="search.matricula"
                                placeholder="Matricula"
                            />
                        </div>
                        <div class="col-fixed" style="width: 140px">
                            <Dropdown
                                v-model="search.status_pdf"
                                optionLabel="title"
                                optionValue="value"
                                :options="optionsStatus"
                                class="w-full"
                                placeholder="Status PDF"
                            />
                        </div>
                        <div class="col-fixed" style="width: 140px">
                            <Dropdown
                                v-model="search.status_storage"
                                optionLabel="title"
                                optionValue="value"
                                class="w-full"
                                :options="optionsStatus"
                                placeholder="Status Storage"
                            />
                        </div>
                        <div class="col-fixed" style="width: 140px">
                            <Dropdown
                                v-model="search.status"
                                optionLabel="title"
                                optionValue="value"
                                class="w-full"
                                :options="optionsStatusDetalhe"
                                placeholder="Status"
                            />
                        </div>
                        <div class="col text-right">
                            <Button type="submit">
                                Pesquisar
                            </Button>
                        </div>
                    </form>
                </template>
                <Column field="tr14_matric" header="Matricula"  />
                <Column field="tr14_pdf" header="Status PDF">
                    <template #body="slotProps">
                        <i :class="{'pi pi-check':slotProps.data.tr14_pdf, 'pi pi-times':!slotProps.data.tr14_pdf}"></i>
                    </template>
                </Column>
                <Column field="tr14_storage" header="Status Storage">
                    <template #body="slotProps">
                        <i :class="{'pi pi-check':slotProps.data.tr14_storage, 'pi pi-times':!slotProps.data.tr14_storage}"></i>
                    </template>
                </Column>
                <Column field="tr14_obs" header="Observação"  />
                <Column field="tr14_status" header="Status"  />
                <Column field="" header="Ações">
                    <template #body="slotProps">
                        <Button
                            v-if="slotProps.data.tr14_status == 'Falha'"
                            title="Reprocessar Emissao PDF"
                            class="p-button-rounded p-button-danger p-button-outlined p-button-sm mr-2"
                            @click="reProcessar(slotProps.data)"
                        >
                            <i class="pi pi-info"></i>
                        </Button>
                        <Button
                            v-if="slotProps.data.tr14_pdf && slotProps.data.tr14_storage"
                            title="Visualizar PDF"
                            class="p-button-rounded p-button-info p-button-outlined p-button-sm mr-2"
                            @click="downloadFIlePDF(slotProps)"
                        >
                            <i class="pi pi-download"></i>
                        </Button>
                    </template>
                </Column>
                <template #empty>
                    Nenhum Detalhe foi encontrado
                </template>
                <template #footer>
                    <Paginator
                        :rows="detalhes.per_page"
                        :totalRecords="detalhes.total"
                        @page="loadingPage"
                    ></Paginator>
                </template>
            </DataTable>
        </div>
    </Sidebar>
</template>

<script>
import ConfirmPopup from "primevue/confirmpopup";

export default {
    name: 'DialogConsultaEmissaoEcartaGeral',
    components: { ConfirmPopup },
    data() {
        return {
            display: false,
            loading: false,
            interval: null,
            loadingDetalhe: false,
            displayDetalhe: false,
            emissao: null,
            emissoes: [],
            optionsStatus: [
                { title: 'Todos Status', value: null },
                { title: "Concluido", value: 1 },
                { title: "Nao concluido", value: 0 }
            ],
            optionsStatusDetalhe: [
                { title: 'Todos Status', value: null },
                { title: "Concluido", value: 'Concluido' },
                { title: "Em Processo", value: 'Em Processo' },
                { title: "Falha", value: 'Falha' }
            ],
            detalhes: {
                data: [],
                total: 0,
                per_page: 0,
                page: 1,
                path: ''
            },
            search: {
                codigo: null,
                matricula: null,
                status_pdf: null,
                status_storage: null,
                status: null
            }
        }
    },
    methods: {
        reProcessar(data) {
            this.loading = true

            window.axios.get('v4/api/tributario/cadastro/emissao_ecarta/reemissao_geral/' + data.tr14_sequencial)
                .then((res) => {
                    alert(res.data.message)
                    this.loadingPage({ event: { page: this.detalhes.page }})
                })
                .catch((error) => alert(error.response.data.message))
                .finally(() => this.loading = false)
        },
        interromper(event, codigo) {
            this.$confirm.require({
                target: event.currentTarget,
                message: 'Tem certeza de que deseja continuar?',
                icon: 'pi pi-exclamation-triangle',
                group: 'interromper',
                accept: () => {
                    this.loading = true

                    window.axios.get('v4/api/tributario/cadastro/emissao_ecarta/interromper/' + codigo)
                        .then((res) => {
                            const data = res.data.data
                            
                            alert(data)
                        })
                        .catch((error) => console.error(error))
                        .finally(() => {
                            this.carregarDados()
                        })
                }
            })
        },
        loadingPage(event) {
            this.requestListDetalheEmissao(this.detalhes.path + `?page=${(event.page + 1)}`)
        },
        carregarDados() {
            window.axios.get('v4/api/tributario/cadastro/emissao_ecarta/lista_remessa_ecarta')
                .then((res) => {
                    const data = res.data.data;

                    this.emissoes = data;
                })
                .catch((error) => console.log(error))
                .finally(() => this.loading = false)
        },
        openModal() {
            this.display = true
            this.loading = true
            this.carregarDados()

            if (this.interval != null) {
                clearInterval(this.interval);
            }

            this.interval = setInterval(() => this.carregarDados(), 30000);

        },
        openDetalhe(emissao, url = 'v4/api/tributario/cadastro/emissao_ecarta/lista_detalhe_emissao_ecarta') {
            this.displayDetalhe = true
            this.emissao = emissao

            this.search.codigo = emissao.tr13_sequencial

            this.requestListDetalheEmissao(url)
        },
        requestListDetalheEmissao(url) {
            this.loadingDetalhe = true

            window.axios.post(url, this.search)
                .then((res) => {
                    const data = res.data.data

                    this.detalhes = data;
                })
                .catch((error) => {
                    console.log(error)
                })
                .finally(() => this.loadingDetalhe = false)
        },
        async downloadFIlePDF({data}) {
            this.loadingDetalhe = true;
            
            await window.axios.get('v4/api/tributario/cadastro/emissao_ecarta/view_pdf/' + data.tr14_sequencial);

            this.loadingDetalhe = false;

            window.open(
              window.ECIDADE_PATH + 'tmp/' + data.tr14_sequencial + '.pdf',
              '',
              'height=800, width=600'
            );
        }
    }
}
</script>