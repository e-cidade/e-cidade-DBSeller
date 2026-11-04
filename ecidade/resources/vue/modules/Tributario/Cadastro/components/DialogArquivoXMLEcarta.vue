<template>
    <BlockUI v-model="loading">
        <Dialog
            header="Consulta de Arquivo XML Ecarta"
            :maximizable="true"
            :modal="true"
            :style="{ width: '1000px', minHeight: '300px' }"
            v-model:visible="display"
        >
            <section style="width: 100%; height: 100%; min-height: 300px" class="m-auto">
                <DataTable
                    :rows="10"
                    :loading="loading"
                    :value="emissoes_xml"
                >
                    <Column header="Cod. Requisição" field="tr15_codigo" />
                    <Column header="Cod. Requisição Geral PDF Ecarta" field="emissao_geral_ecarta.tr13_codigo" />
                    <Column header="Data da Geracao" field="tr15_data_geracao" />
                    <Column header="Arquivo Complementaris" field="tr15_arquivo_complementar">
                        <template #body="slotProps">
                            <i :class="{
                                'pi pi-check':slotProps.data.tr15_arquivo_complementar,
                                'pi pi-times':!slotProps.data.tr15_arquivo_complementar
                            }"></i>
                        </template>
                    </Column>
                    <Column header="Status" field="tr15_status" />
                    <Column field="" header="">
                        <template #body="slotProps">
                            <Button
                                title="Detalhes da Geracao"
                                class="p-button-rounded p-button-info p-button-outlined p-button-sm mr-2"
                            >
                                <i class="pi pi-eye"></i>
                            </Button>
                        </template>
                    </Column>
                    <template #empty>
                        Nenhuma remessa de Arquivo XML E-carta foi encontrada
                    </template>
                </DataTable>
            </section>
        </Dialog>
    </BlockUI>
</template>

<script>
export default {
    name: 'DialogArquivoXMLEcartta',
    data() {
        return {
            emissoes_xml: [],
            display: false,
            loading: false
        }
    },
    methods: {
        openModal() {
            this.display = true

            this.getListEmissaoArquivoXML();
        },
        getListEmissaoArquivoXML() {
            this.loading      = true
            this.emissoes_xml = [];

            window.axios.get('v4/api/tributario/cadastro/arquivo_xml_correios')
                .then((res) => {
                    const data = res.data.data

                    this.emissoes_xml = data;
                })
                .finally(() => this.loading = false)
        }
    }
}
</script>