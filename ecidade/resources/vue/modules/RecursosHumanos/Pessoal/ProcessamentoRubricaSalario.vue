
<template>
    <ModalLoading :is-loading="loading" />
    <section class="container">
        <TabPanel>
            <Panel header="Processamento Dados do Ponto">
                <div class="flex align-items-center justify-content-center mx-auto" style="width: 50%;">
                    <div class="p-1 m-auto">
                        <div class="text-center">
                            <div class="text-center" ref="containerEnviar">
                                <div class="text-center" ref="containeCompetencia">
                                    Competência:
                                    <div class="flex align-items-center justify-content-center">
                                        <InputNumber v-model="anoCalculo" placeholder="Digite o Ano" inputId="minmax"
                                            :useGrouping="false" :min="2000" :max="2099" />
                                    </div>
                                    <div>
                                        <InputNumber v-model="mesCalculo" placeholder="Digite o Mês" inputId="minmax"
                                            :useGrouping="false" :min="0" :max="12" />
                                    </div>
                                </div>
                                <div class="text-center mt-3" ref="containerSelecaoRubrica">
                                    Rubrica:<br>
                                    <div class="p-inputgroup" style="width: 350px;">
                                        <span class="p-inputgroup-addon" @click="openDialogRubrica">
                                            <i class="pi pi-search"></i>
                                        </span>
                                        <AutoComplete v-model="rubrica" placeholder="Inclusão do Código da Rubrica"
                                            optionLabel="rh27_descr" optionValue="rh27_rubric" :suggestions="rubricas"
                                            forceSelection @complete="pesquisarRubricasDaInstituicao($event)" />
                                    </div>
                                    <div class="text-center mt-3">
                                        <Button @click="processarPonto()" icon="pi pi-check" label="Processar Fundeb"
                                            class="p-button-sm mt-3">
                                        </Button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Panel>
        </TabPanel>

        <DialogRubricaServidor ref="dialog_rubrica" @select="selectRubrica" />
        <Toast />
    </section>
</template>
<script>

import ConfirmPopup from "primevue/confirmpopup";
import Toast from "primevue/toast";
import ModalLoading from "../../Components/ModalLoading";
import DialogRubricaServidor from "./Components/DialogRubricaServidor";

export default {
    name: 'Processamento',
    components: {
        ConfirmPopup,
        Toast,
        DialogRubricaServidor,
        ModalLoading
    },
    data() {
        return {
            anoCalculo: null,
            mesCalculo: null,
            rubricaCalculo: null,
            rubrica: null,
            rubricas: [],
            loading : false
        }
    },
    methods: {
        openDialogRubrica() {
            this.$refs.dialog_rubrica.openDialog();
        },

        selectRubrica(rubrica) {
            this.rubrica = rubrica
        },

        async pesquisarRubricasDaInstituicao({ query } = { query: '' }) {
            try {
                const resp = await window.axios.get(
                    `v4/api/recursos-humanos/pessoal/rhrubricas/list?nome=${query}`
                );
                this.rubricas = resp.data.data;
            } catch (e) {
                this.rubricas = [];
            }
        },

        async processarPonto() {
            try {

                this.loading = true;
                const data = new FormData();
                data.append('ano', this.anoCalculo);
                data.append('mes', this.mesCalculo);
                data.append('rubrica', this.rubrica.rh27_rubric);
                const resp = await window.axios.post(
                    `v4/api/recursos-humanos/pessoal/fundeb/rhprocessamentofundeb/salvar`, data
                );

                if (resp.status === 200) {
                    alert('Processamento dos valores da Gratificação Fundeb realizada com Sucesso!');
                }
                this.loading = false;
            } catch (error) {
                alert('Não há servidores para o Processamento dos valores da Gratificação Fundeb');
                this.anoCalculoIncluido = [];
                this.mesCalculoIncluido = [];
                this.rubricaCalculoIncluido = [];
                this.loading = false;
            }
        }

    }, beforeMount() {

    }
}
</script>

