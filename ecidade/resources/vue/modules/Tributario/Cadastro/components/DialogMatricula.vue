<template>
    <Dialog
        header="Consulta de Matricula"
        :maximizable="true"
        :modal="true"
        :style="{ width: '1000px', minHeight: '300px' }"
        v-model:visible="display"
        @hide="removerLoading"
    >
        <BlockUI :blocked="loading">
            <section style="width: 100%; height: 100%; min-height: 300px" class="m-auto">
                <div>
                    <span class="p-input-icon-left mb-3">
                        <i class="pi pi-search" />
                        <InputText 
                            type="text" 
                            v-model="matriculaSearch"
                            @change="() => loadingData()"
                            placeholder="Pesquisar Matricula"
                        />
                    </span>
                </div>
                <DataTable
                    :value="matriculas"
                    :rows="10"
                    :loading="loading"
                    @rowSelect="onRowSelect"
                    selectionMode="single"
                    dataKey="j01_matric"
                    v-model::selection="select"
                >
                    <Column header="Matricula" field="j01_matric" />
                    <Column header="CGM" field="j01_numcgm" />
                    <Column header="Proprietario" field="cgm.z01_nomecomple" />
                    <template #filterfooter>
                        <Button type="button" icon="pi pi-refresh" class="p-button-text" />
                    </template>
                </DataTable>
                <div class="mt-2">
                    <Button
                        type="button"
                        icon="pi pi-chevron-left"
                        class="p-button-text"
                        @click="loadingData(prev)"
                        :disabled="!prev"
                    />
                    <Button
                        type="button"
                        icon="pi pi-chevron-right"
                        class="p-button-text"
                        @click="loadingData(next)"
                        :disabled="!next"
                    />
                </div>
            </section>
            <template #footer>
                <Button
                    class="p-button-sm p-button-outlined"
                    @click="display = false"
                >
                    Fechar
                </Button>
            </template>
        </BlockUI>
    </Dialog>
</template>

<script>
    export default {
        name: 'DialogMatriculaX',
        data() {
            return {
                select: null,
                display: false,
                loading: false,
                next: null,
                prev: null,
                matriculaSearch: null,
                matriculas: []
            }
        },
        methods: {
            openModal() {
                this.display = !this.display
            
                if (this.display) {
                    this.loadingData()
                } else {
                    this.matriculas      = []
                    this.matriculaSearch = null
                }
            },
            onRowSelect(event) {
                this.data    = []
                this.display = false

                this.$emit('selectRow', event.data)
            },
            async loadingData(url = 'v4/api/tributario/cadastro/matricula/listar') {
                this.loading = true

                if (this.matriculaSearch != null && this.matriculaSearch.length > 0) {
                    if (url.indexOf('?') < 0) {
                        url += '?'
                    }

                    url += 'search=' + this.matriculaSearch
                }

                await window.axios(url)
                    .then((res) => {
                        const data = res.data.data.data

                        this.next = res.data.data.next_page_url
                        this.prev = res.data.data.prev_page_url

                        this.matriculas = data;
                    })
                    .catch((error) => console.log(error))
                    .finally(() => this.loading = false)
            },
            async verifyMatricula(matricula) {
                this.matriculaSearch = matricula
                this.matriculas      = []

                await this.loadingData()

                if (this.matriculas.length === 1) {
                    return this.matriculas[0];
                } else if (this.matriculas.length > 1) {
                    throw 'Um ou mais resultados foram encontrados'
                }
                
                throw 'Nenhum resultado foi encontrado'
            }
        }
    }
</script>