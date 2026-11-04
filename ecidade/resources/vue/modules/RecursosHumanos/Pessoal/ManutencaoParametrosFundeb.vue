
<template>
    <ModalLoading :is-loading="loading" />
    <section class="container">
        <TabView>
            <TabPanel header="Cargos/Funções">
                <Panel>
                    <div class="flex align-items-center justify-content-center m-2" style="width: 100%;">
                        <div class="p-1 m-auto">
                            <div class="grid">
                                <div class="col-fixed pt-3" style="width: 170px;">
                                    <div class="grid">
                                        <div class="col-4">
                                            <div class="col-12">
                                                Diretor:<br>
                                                <div class="p-inputgroup" style="width: 500px;">
                                                    <span class="p-inputgroup-addon" @click="openDialogFuncaoDiretor">
                                                        <i class="pi pi-search"></i>
                                                    </span>
                                                    <AutoComplete v-model="funcaoDiretor"
                                                        placeholder="Inclusão Função de Diretor" optionValue="rh04_codigo"
                                                        optionLabel="rh04_descr" :suggestions="funcoes" forceSelection
                                                        @complete="pesquisarFuncoesDaInstituicao($event)" />
                                                </div>
                                                <div class="text-center" style="width: 1800%">
                                                    <Button @click="incluirDiretor()" icon="pi pi-check" label="Salvar"
                                                        class="p-button-sm mt-3"
                                                        :disabled="!campoPreenchidoDiretor()"></Button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid">
                                <div class="col-fixed pt-3" style="width: 170px;">
                                    <div class="grid">
                                        <div class="col-4">
                                            <div class="col-12">
                                                Dirigente:<br>
                                                <div class="p-inputgroup" style="width: 500px;">
                                                    <span class="p-inputgroup-addon" @click="openDialogFuncaoDirigente">
                                                        <i class="pi pi-search"></i>
                                                    </span>
                                                    <AutoComplete v-model="funcaoDirigente"
                                                        placeholder="Inclusão Função de Dirigente" optionValue="rh04_codigo"
                                                        optionLabel="rh04_descr" :suggestions="funcoes" forceSelection
                                                        @complete="pesquisarFuncoesDaInstituicao($event)" />
                                                </div>
                                                <div class="text-center" style="width: 1800%">
                                                    <Button @click="incluirDirigente()" icon="pi pi-check" label="Salvar"
                                                        class="p-button-sm mt-3"
                                                        :disabled="!campoPreenchidoDirigente()"></Button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid">
                                <div class="col-fixed pt-3" style="width: 170px;">
                                    <div class="grid">
                                        <div class="col-4">
                                            <div class="col-12">
                                                Professor:<br>
                                                <div class="p-inputgroup" style="width: 500px;">
                                                    <span class="p-inputgroup-addon" @click="openDialogCargoProfessor">
                                                        <i class="pi pi-search"></i>
                                                    </span>
                                                    <AutoComplete v-model="cargoProfessor"
                                                        placeholder="Inclusão Cargo de Professor" optionValue="rh37_funcao"
                                                        optionLabel="rh37_descr" :suggestions="cargos" forceSelection
                                                        @complete="pesquisarCargosDaInstituicao($event)" />
                                                </div>
                                                <div class="text-center" style="width: 1800%">
                                                    <Button @click="incluirProfessor()" icon="pi pi-check" label="Salvar"
                                                        class="p-button-sm mt-3"
                                                        :disabled="!campoPreenchidoProfessor()"></Button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="grid">
                                <div class="col-fixed pt-3" style="height: 170px;">
                                    <div class="grid">
                                        <div class="col-4">
                                            <div class="col-12">
                                                Apoio:<br>
                                                <div class="p-inputgroup" style="width: 500px;">
                                                    <span class="p-inputgroup-addon" @click="openDialogCargoApoio">
                                                        <i class="pi pi-search"></i>
                                                    </span>
                                                    <AutoComplete v-model="cargoApoio" placeholder="Inclusão Cargo de Apoio"
                                                        optionValue="rh37_funcao" optionLabel="rh37_descr"
                                                        :suggestions="cargos" forceSelection
                                                        @complete="pesquisarCargosDaInstituicao($event)" />
                                                </div>
                                                <div class="text-center" style="width: 340%">
                                                    <Button @click="incluirApoio()" icon="pi pi-check" label="Salvar"
                                                        class="p-button-sm mt-3"
                                                        :disabled="!campoPreenchidoApoio()"></Button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <DataTable :value="diretorIncluido" :loading="loading" :rowHover="true" selectionMode="single"
                        responsiveLayout="scroll" :rows="10">
                        <Column header="Diretor">
                        </Column>
                        <Column field="rh04_codigo" header="Código da Função">
                        </Column>
                        <Column field="rh04_descr" header="Descrição da Função">
                        </Column>
                        <Column field="button" header="Opções">
                            <template #body="slotProps">
                                <Button @click="removeDiretor($event, slotProps)" icon="pi pi-times"
                                    style="color: white"></Button>
                            </template>
                        </Column>
                        <template #empty>
                            Nenhum registro incluído
                        </template>
                    </DataTable>
                    <ConfirmPopup group="removeDiretor"></ConfirmPopup>

                    <DataTable :value="dirigenteIncluido" :loading="loading" :rowHover="true" selectionMode="single"
                        responsiveLayout="scroll" :rows="20">
                        <Column header="Dirigente">
                        </Column>
                        <Column field="rh04_codigo" header="Código da Função">
                        </Column>
                        <Column field="rh04_descr" header="Descrição da Função">
                        </Column>
                        <Column field="button" header="Opções">
                            <template #body="slotProps">
                                <Button @click="removeDirigente($event, slotProps)" icon="pi pi-times"
                                    style="color: white"></Button>
                            </template>
                        </Column>
                        <template #empty>
                            Nenhum registro incluído
                        </template>
                    </DataTable>
                    <ConfirmPopup group="removeDirigente"></ConfirmPopup>

                    <DataTable :value="professorIncluido" :loading="loading" :rowHover="true" selectionMode="single"
                        responsiveLayout="scroll" :rows="10">
                        <Column header="Professor">
                        </Column>
                        <Column field="rh37_funcao" header="Código do Cargo">
                        </Column>
                        <Column field="rh37_descr" header="Descrição do Cargo">
                        </Column>
                        <Column field="button" header="Opções">
                            <template #body="slotProps">
                                <Button @click="removeProfessor($event, slotProps)" icon="pi pi-times"
                                    style="color: white"></Button>
                            </template>
                        </Column>
                        <template #empty>
                            Nenhum registro incluído
                        </template>
                    </DataTable>
                    <ConfirmPopup group="removeProfessor"></ConfirmPopup>

                    <DataTable :value="apoioIncluido" :loading="loading" :rowHover="true" selectionMode="single"
                        responsiveLayout="scroll" :rows="10">
                        <Column header="Apoio">
                        </Column>
                        <Column field="rh37_funcao" header="Código do Cargo">
                        </Column>
                        <Column field="rh37_descr" header="Descrição do Cargo">
                        </Column>
                        <Column field="button" header="Opções">
                            <template #body="slotProps">
                                <Button @click="removeApoio($event, slotProps)" icon="pi pi-times"
                                    style="color: white"></Button>
                            </template>
                        </Column>
                        <template #empty>
                            Nenhum registro incluído
                        </template>
                    </DataTable>
                    <ConfirmPopup group="removeApoio"></ConfirmPopup>
                </Panel>
            </TabPanel>

            <TabPanel header="Locais de Trabalho" class="grid">
                <Panel>
                    <div class="flex align-items-center justify-content-center m-2" style="width: 100%;">
                        <div class="grid">
                            <div class="col-12" style="height: 170px;">
                                Local de Trabalho:<br>
                                <div class="p-inputgroup" style="width: 500px;">
                                    <span class="p-inputgroup-addon" @click="openDialogLocal">
                                        <i class="pi pi-search"></i>
                                    </span>
                                    <AutoComplete v-model="local" placeholder="Inclusão do Local de Trabalho"
                                        optionValue="rh55_codigo" optionLabel="rh55_descr" :suggestions="locais"
                                        forceSelection @complete="pesquisarLocaisDaInstituicao($event)" />
                                </div>
                                <div class="text-center" style="width: 100%">
                                    <Button @click="incluirLocal()" icon="pi pi-check" label="Salvar"
                                        class="p-button-sm mt-3" :disabled="!campoPreenchidoLocal()"></Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <DataTable :value="localIncluido" :loading="loading" :rowHover="true" selectionMode="single"
                        responsiveLayout="scroll" :rows="10">
                        <Column header="Locais de Trabalho">
                        </Column>
                        <Column field="rh55_codigo" header="Código do Local de Trabalho">
                        </Column>
                        <Column field="rh55_estrut" header="Estrutural do Local de Trabalho">
                        </Column>
                        <Column field="rh55_descr" header="Descrição do Local de Trabalho">
                        </Column>
                        <Column field="button" header="Opções">
                            <template #body="slotProps">
                                <Button @click="removeLocal($event, slotProps)" icon="pi pi-times"
                                    style="color: white"></Button>
                            </template>
                        </Column>
                        <template #empty>
                            Nenhum registro incluído
                        </template>
                    </DataTable>
                    <ConfirmPopup group="removeLocal"></ConfirmPopup>
                </Panel>
            </TabPanel>

            <TabPanel header="Assentamentos" class="grid">
                <Panel>
                    <div class="flex align-items-center justify-content-center m-2" style="width: 100%;">
                        <div class="grid">
                            <div class="col-12" ref="containerSelecaoAssentamento" style="height: 170px;">
                                Assentamento:<br>
                                <div class="p-inputgroup" style="width: 500px;">
                                    <span class="p-inputgroup-addon" @click="openDialogAssentamento">
                                        <i class="pi pi-search"></i>
                                    </span>
                                    <AutoComplete v-model="assentamento" placeholder="Inclusão de Assentamentos"
                                        optionValue="h12_assent" optionLabel="h12_descr" :suggestions="assentamentos"
                                        forceSelection @complete="pesquisarAssentamentosDaInstituicao($event)" />
                                </div>
                                <div class="text-center" style="width: 100%">
                                    <Button @click="incluirAssentamento()" icon="pi pi-check" label="Salvar"
                                        class="p-button-sm mt-3" :disabled="!campoPreenchidoAssentamento()"></Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <DataTable :value="assentamentoIncluido" :loading="loading" @rowSelect="onRowSelect" :rowHover="true"
                        selectionMode="single" responsiveLayout="scroll" :rows="10">
                        <Column header="Assentamentos">
                        </Column>
                        <Column field="h12_assent" header="Código do Assentamento">
                        </Column>

                        <Column field="h12_descr" header="Descrição do Assentamento">
                        </Column>
                        <Column field="button" header="Opções">
                            <template #body="slotProps">
                                <Button @click="removeAssentamento($event, slotProps)" icon="pi pi-times"
                                    style="color: white"></Button>
                            </template>
                        </Column>
                        <template #empty>
                            Nenhum registro incluído
                        </template>
                    </DataTable>
                    <ConfirmPopup group="removeAssentamento"></ConfirmPopup>
                </Panel>
            </TabPanel>
            <TabPanel header="Rubrica Abatimento Fundeb" class="grid">
                <Panel>
                    <div class="flex align-items-center justify-content-center" style="width: 100%;">
                        <div style="width: 350px;">
                            <div class="mt-3" ref="containerSelecaoRubrica" style="height: 150px;">
                                Rubrica:<br>
                                <div class="p-inputgroup" style="width: 350px;">
                                    <span class="p-inputgroup-addon" @click="openDialogRubrica">
                                        <i class="pi pi-search"></i>
                                    </span>
                                    <AutoComplete v-model="rubricaAbatimento" placeholder="Inclusão do Código da Rubrica"
                                        style="width: 170px;" optionLabel="rh27_descr" optionValue="rh27_rubric"
                                        :suggestions="rubricas" forceSelection
                                        @complete="pesquisarRubricasDaInstituicao($event)" />
                                </div>
                                <div class="text-center">
                                    <Button label="Salvar" class="p-button-sm mt-3"
                                        @click="salvarRubricaAbatimento()">Salvar</Button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <DataTable :value="rubricaAbatimentoIncluido" :loading="loading" :rowHover="true" selectionMode="single"
                        responsiveLayout="scroll" :rows="10">
                        <Column header="Rubrica">
                        </Column>
                        <Column field="rh27_rubric" header="Código da Rubrica">
                        </Column>
                        <Column field="rh27_descr" header="Descrição da Rubrica">
                        </Column>
                        <Column field="button" header="Opções">
                            <template #body="slotProps">
                                <Button @click="removeRubricaAbatimento($event, slotProps)" icon="pi pi-times"
                                    style="color: white"></Button>
                            </template>
                        </Column>
                        <template #empty>
                            Nenhum registro incluído
                        </template>
                    </DataTable>
                    <ConfirmPopup group="removeRubricaAbatimento"></ConfirmPopup>
                </Panel>
            </TabPanel>

            <TabPanel header="Valores Referentes ao Fundeb" class="grid">
                <Panel>
                    <div class="flex align-items-center justify-content-center" style="width: 100%;">
                        <div style="width: 207.3px;">
                            <div ref="containerSelecaoValor" style="height: 220px;">
                                <div>
                                    <label for="Ano">
                                        Ano:<br>
                                        <InputNumber v-model="anoCalculo" placeholder="Digite o Ano" style="width: 170px;"
                                            :useGrouping="false" inputId="minmax" :min="2000" :max="2100" />
                                    </label>
                                </div>
                                <div class="mt-3">
                                    <label for="Mês">
                                        Mês:<br>
                                        <InputNumber v-model="mesCalculo" placeholder="Digite o Mês" style="width: 170px;"
                                            :useGrouping="false" inputId="minmax" :min="1" :max="12" />
                                    </label>
                                </div>
                                <div class="mt-3">
                                    <label for="Valor">
                                        Valor Fundeb:<br>
                                        <InputNumber v-model="valorCalculo" placeholder="Digite o Valor Fundeb"
                                            style="width: 170px;" inputId="minmaxfraction" :minFractionDigits="2"
                                            :maxFractionDigits="5" />
                                    </label>
                                </div>
                                <div class="text-center">
                                    <Button label="Salvar" class="p-button-sm mt-3"
                                        @click="salvarValorCalculo()">Salvar</Button>
                                </div>
                            </div>
                        </div>
                    </div>
                </Panel>
            </TabPanel>
        </TabView>
        <DialogFuncao ref="dialog_diretor" @select="selectFuncaoDiretor" />
        <DialogFuncao ref="dialog_dirigente" @select="selectFuncaoDirigente" />
        <DialogCargo ref="dialog_professor" @select="selectCargoProfessor" />
        <DialogCargo ref="dialog_apoio" @select="selectCargoApoio" />
        <DialogLocalDeTrabalho ref="dialog_local" @select="selectLocal" />
        <DialogAssentamento ref="dialog_assentamento" @select="selectAssentamento" />
        <DialogRubricaServidor ref="dialog_rubrica" @select="selectRubrica" />
        <Toast />
    </section>
</template>
<script>
import ModalLoading from "../../Components/ModalLoading";
import ConfirmPopup from "primevue/confirmpopup";
import Toast from "primevue/toast";
import DialogCargo from "./Components/DialogCargo";
import DialogFuncao from "./Components/DialogFuncao";
import DialogLocalDeTrabalho from "./Components/DialogLocalDeTrabalho";
import DialogAssentamento from "../RH/Components/DialogAssentamento";
import DialogRubricaServidor from "./Components/DialogRubricaServidor";

export default {
    name: 'Fundeb',
    components: {
        ConfirmPopup,
        Toast,
        DialogCargo,
        DialogFuncao,
        DialogLocalDeTrabalho,
        DialogAssentamento,
        DialogRubricaServidor
    },
    data() {
        return {
            value: 0,
            loading: false,
            msg: null,
            selectpesquisa: null,
            valorPesquisa: null,
            locais: [],
            assentamento: [],
            cargos: [],
            funcoes: [],
            funcaoDiretor: {
                rh04_codigo: '',
                rh04_descr: ''
            },
            funcaoDirigente: {
                rh04_codigo: '',
                rh04_descr: ''
            },
            cargoProfessor: {
                rh37_funcao: '',
                rh37_descr: ''
            },
            cargoApoio: {
                rh37_funcao: '',
                rh37_descr: ''
            },
            local: {
                rh55_codigo: '',
                rh55_estrut: '',
                rh55_descr: ''
            },
            assentamentos: {
                h12_codigo: '',
                h12_descr: ''
            },
            rubricas: {
                rh27_rubric: '',
                rh27_descr: ''
            },
            anoCalculo: null,
            mesCalculo: null,
            valorCalculo: null,
            rubricaAbatimento: null,
            diretorIncluido: [],
            dirigenteIncluido: [],
            professorIncluido: [],
            apoioIncluido: [],
            localIncluido: [],
            assentamentoIncluido: [],
            anoCalculoIncluido: [],
            mesCalculoIncluido: [],
            valorCalculoIncluido: [],
            rubricaAbatimentoIncluido: [],
            index: [],
            formPesquisa: {
                valor: null,
                select: null,
            },
        }
    }, methods: {
        campoPreenchidoDiretor() {
            return this.funcaoDiretor.rh04_codigo && this.funcaoDiretor.rh04_descr;
        },

        campoPreenchidoDirigente() {
            return this.funcaoDirigente.rh04_codigo && this.funcaoDirigente.rh04_descr;
        },

        campoPreenchidoProfessor() {
            return this.cargoProfessor.rh37_funcao && this.cargoProfessor.rh37_descr;
        },

        campoPreenchidoApoio() {
            return this.cargoApoio.rh37_funcao && this.cargoApoio.rh37_descr;
        },

        campoPreenchidoLocal() {
            return this.local.rh55_codigo && this.local.rh55_estrut && this.local.rh55_descr
        },

        campoPreenchidoAssentamento() {
            return this.assentamento.h12_codigo && this.assentamento.h12_descr
        },

        campoPreenchidoRubrica() {
            return this.rubricaAbatimento.rh27_rubric && this.rubricaAbatimento.rh27_descr
        },

        openDialogFuncaoDiretor() {
            this.$refs.dialog_diretor.openDialog();
        },

        openDialogFuncaoDirigente() {
            this.$refs.dialog_dirigente.openDialog();
        },

        openDialogCargoProfessor() {
            this.$refs.dialog_professor.openDialog();
        },

        openDialogCargoApoio() {
            this.$refs.dialog_apoio.openDialog();
        },

        openDialogLocal() {
            this.$refs.dialog_local.openDialog();
        },

        openDialogAssentamento() {
            this.$refs.dialog_assentamento.openDialog();
        },

        openDialogRubrica() {
            this.$refs.dialog_rubrica.openDialog();
        },

        selectFuncaoDiretor(funcaoDiretor) {
            this.funcaoDiretor = funcaoDiretor
        },

        selectFuncaoDirigente(funcaoDirigente) {
            this.funcaoDirigente = funcaoDirigente
        },

        selectCargoProfessor(cargoProfessor) {
            this.cargoProfessor = cargoProfessor
        },

        selectCargoApoio(cargoApoio) {
            this.cargoApoio = cargoApoio
        },

        selectLocal(local) {
            this.local = local
        },

        selectAssentamento(assentamento) {
            this.assentamento = assentamento
        },

        selectRubrica(rubricaAbatimento) {
            this.rubricaAbatimento = rubricaAbatimento
        },

        async incluirDiretor() {
            try {

                const data = new FormData();
                data.append('tipo', 1);
                data.append('funcao', this.funcaoDiretor.rh04_codigo)

                const descricaoFuncao = this.funcaoDiretor.rh04_descr

                const funcaoExistenteDiretor = this.diretorIncluido.find(funcaoDiretor =>
                    funcaoDiretor.rh04_codigo === this.funcaoDiretor.rh04_codigo);

                const funcaoExistenteDirigente = this.dirigenteIncluido.find(funcaoDirigente =>
                    funcaoDirigente.rh04_codigo === this.funcaoDiretor.rh04_codigo);

                if (funcaoExistenteDiretor) {
                    alert(`A Função '${descricaoFuncao}' já foi incluída anteriormente para o Tipo Diretor.`);
                    return;
                } else if (funcaoExistenteDirigente) {
                    alert(`A Função '${descricaoFuncao}' já foi incluída anteriormente para o Tipo Dirigente.`);
                    return;
                }

                const resp = await window.axios.post(
                    `v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/salvar`, data
                );

                this.diretorIncluido.push({
                    rh04_codigo: this.funcaoDiretor.rh04_codigo,
                    rh04_descr: this.funcaoDiretor.rh04_descr,
                });

                this.funcaoDiretor = {
                    rh04_codigo: '',
                    rh04_descr: ''
                };

                alert(`Função '${descricaoFuncao}' do Tipo Diretor incluída com Sucesso!`);

            } catch (e) {
                this.diretorIncluido = [];
            }
        },

        async incluirDirigente() {
            try {
                const data = new FormData
                data.append('tipo', 2)
                data.append('funcao', this.funcaoDirigente.rh04_codigo)

                const descricaoFuncao = this.funcaoDirigente.rh04_descr

                const funcaoExistenteDirigente = this.dirigenteIncluido.find(funcaoDirigente =>
                    funcaoDirigente.rh04_codigo === this.funcaoDirigente.rh04_codigo);

                const funcaoExistenteDiretor = this.diretorIncluido.find(funcaoDiretor =>
                    funcaoDiretor.rh04_codigo === this.funcaoDirigente.rh04_codigo);

                if (funcaoExistenteDirigente) {
                    alert(`A Função '${descricaoFuncao}' já foi incluída anteriormente para o Tipo Dirigente.`);
                    return;
                } else if (funcaoExistenteDiretor) {
                    alert(`A Função '${descricaoFuncao}' já foi incluído anteriormente para o Tipo Diretor.`);
                    return;
                }

                const resp = await window.axios.post(
                    `v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/salvar`, data
                );

                this.dirigenteIncluido.push({
                    rh04_codigo: this.funcaoDirigente.rh04_codigo,
                    rh04_descr: this.funcaoDirigente.rh04_descr
                });

                this.funcaoDirigente = {
                    rh04_codigo: '',
                    rh04_descr: ''
                };

                alert(`Função '${descricaoFuncao}' do Tipo Dirigente incluída com Sucesso!`);

            } catch (e) {
                this.dirigenteIncluido = [];
            }
        },

        async incluirProfessor() {
            try {
                const data = new FormData
                data.append('tipo', 3)
                data.append('cargo', this.cargoProfessor.rh37_funcao)

                const descricaoCargo = this.cargoProfessor.rh37_descr

                const cargoExistenteProfessor = this.professorIncluido.find(cargoProfessor =>
                    cargoProfessor.rh37_funcao === this.cargoProfessor.rh37_funcao);

                const cargoExistenteApoio = this.apoioIncluido.find(cargoApoio =>
                    cargoApoio.rh37_funcao === this.cargoProfessor.rh37_funcao);

                if (cargoExistenteProfessor) {
                    alert(`O Cargo '${descricaoCargo}' já foi incluído anteriormente para o Tipo Professor.`);
                    return;
                } else if (cargoExistenteApoio) {
                    alert(`O Cargo '${descricaoCargo}' já foi incluído anteriormente para o Tipo Apoio.`);
                    return;
                }

                const resp = await window.axios.post(
                    `v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/salvar`, data
                );

                this.professorIncluido.push({
                    rh37_funcao: this.cargoProfessor.rh37_funcao,
                    rh37_descr: this.cargoProfessor.rh37_descr
                });

                this.cargoProfessor = {
                    rh37_funcao: '',
                    rh37_descr: ''
                };

                alert(`Cargo '${descricaoCargo}' do Tipo Professor incluído com Sucesso!`);

            } catch (e) {
                this.professorIncluido = [];
            }
        },

        async incluirApoio() {
            try {

                if (!this.cargoApoio.rh37_funcao || !this.cargoApoio.rh37_descr) {
                    alert("Por favor, preencha todos os campos antes de salvar.");
                    return;
                }

                const data = new FormData
                data.append('tipo', 4)
                data.append('cargo', this.cargoApoio.rh37_funcao)

                const descricaoCargo = this.cargoApoio.rh37_descr

                const cargoExistenteApoio = this.apoioIncluido.find(cargoApoio =>
                    cargoApoio.rh37_funcao === this.cargoApoio.rh37_funcao);

                const cargoExistenteProfessor = this.professorIncluido.find(cargoProfessor =>
                    cargoProfessor.rh37_funcao === this.cargoApoio.rh37_funcao);

                if (cargoExistenteApoio) {
                    alert(`O Cargo '${descricaoCargo}' já foi incluído anteriormente para o Tipo Apoio.`);
                    return;
                } else if (cargoExistenteProfessor) {
                    alert(`O Cargo '${descricaoCargo}' já foi incluído anteriormente para o Tipo Professor.`);
                    return;
                }

                const resp = await window.axios.post(
                    `v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/salvar`, data
                );

                this.apoioIncluido.push({
                    rh37_funcao: this.cargoApoio.rh37_funcao,
                    rh37_descr: this.cargoApoio.rh37_descr
                });

                this.cargoApoio = {
                    rh37_funcao: '',
                    rh37_descr: ''
                };

                alert(`Cargo '${descricaoCargo}' do Tipo Apoio incluído com Sucesso!`);

            } catch (e) {
                this.apoioIncluido = [];
            }
        },

        async incluirLocal() {
            try {

                const data = new FormData
                data.append('tipo', 4)
                data.append('local', this.local.rh55_codigo)

                const descricaoLocal = this.local.rh55_descr;

                const localExistente = this.localIncluido.find(local =>
                    local.rh55_codigo === this.local.rh55_codigo);

                if (localExistente) {
                    alert(`O Local de Trabalho '${descricaoLocal}' já foi incluído anteriormente para o Parâmetro Locais de Trabalho.`);
                    return;
                }

                const resp = await window.axios.post(
                    `v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/salvar`, data
                );

                this.localIncluido.push({
                    rh55_codigo: this.local.rh55_codigo,
                    rh55_estrut: this.local.rh55_estrut,
                    rh55_descr: this.local.rh55_descr
                });

                this.local = {
                    rh55_codigo: '',
                    rh55_estrut: '',
                    rh55_descr: ''
                };

                alert(`Local de Trabalho '${descricaoLocal}' incluído com Sucesso!`);

            } catch (e) {
                this.localIncluido = [];
            }
        },

        async incluirAssentamento() {
            try {
                const data = new FormData
                data.append('tipo', 0)
                data.append('assentamento', this.assentamento.h12_codigo)

                const descricaoAssentamento = this.assentamento.h12_descr;

                const assentamentoExistente = this.assentamentoIncluido.find(assentamento =>
                    assentamento.h12_codigo === this.assentamento.h12_codigo);

                if (assentamentoExistente) {
                    alert(`O Assentamento '${descricaoAssentamento}' já foi incluído anteriormente para o Parâmetro Assentamentos.`);
                    return;
                }

                const resp = await window.axios.post(
                    `v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/salvar`, data
                );

                this.assentamentoIncluido.push({
                    h12_codigo: this.assentamento.h12_codigo,
                    h12_assent: this.assentamento.h12_assent,
                    h12_descr: this.assentamento.h12_descr
                });

                this.assentamento = {
                    h12_codigo: '',
                    h12_assent: '',
                    h12_descr: ''
                }

                alert(`Assentamento '${descricaoAssentamento}' incluído com Sucesso!`);

            } catch (e) {
                this.assentamentoIncluido = [];
            }
        },

        removeDiretor(event, parametros) {
            this.$confirm.require({
                target: event.currentTarget,
                group: "removeDiretor",
                message: "Tem certeza de que deseja remover a Função de Diretor?",
                icon: "pi pi-exclamation-triangle",
                accept: async () => {
                    const resp = await window.axios.delete(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/removerFuncao/${parametros.data.rh04_codigo}`).then(response => {
                        alert('Função do Tipo Diretor removida com Sucesso!')
                        this.carregarlistFuncao()
                    }).catch(error => {
                        this.loading = false
                        console.log(error)
                    })
                },
            });
        },

        removeDirigente(event, parametros) {
            this.$confirm.require({
                target: event.currentTarget,
                group: "removeDirigente",
                message: "Tem certeza de que deseja remover a Função de Dirigente?",
                icon: "pi pi-exclamation-triangle",
                accept: async () => {
                    const resp = await window.axios.delete(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/removerFuncao/${parametros.data.rh04_codigo}`).then(response => {
                        alert('Função do Tipo Dirigente removida com Sucesso!')
                        this.carregarlistFuncao()
                    }).catch(error => {
                        this.loading = false
                        console.log(error)
                    })
                },
            });
        },

        removeProfessor(event, parametros) {
            this.$confirm.require({
                target: event.currentTarget,
                group: "removeProfessor",
                message: "Tem certeza de que deseja remover o Cargo de Professor?",
                icon: "pi pi-exclamation-triangle",
                accept: async () => {
                    const resp = await window.axios.delete(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/removerCargo/${parametros.data.rh37_funcao}`).then(response => {
                        alert('Cargo do Tipo Professor removido com Sucesso!')
                        this.carregarlistCargo()
                    }).catch(error => {
                        this.loading = false
                        console.log(error)
                    })
                },
            });
        },

        removeApoio(event, parametros) {
            this.$confirm.require({
                target: event.currentTarget,
                group: "removeApoio",
                message: "Tem certeza de que deseja remover o Cargo de Apoio?",
                icon: "pi pi-exclamation-triangle",
                accept: async () => {
                    const resp = await window.axios.delete(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/removerCargo/${parametros.data.rh37_funcao}`).then(response => {
                        alert('Cargo do Tipo Apoio removido com Sucesso!')
                        this.carregarlistCargo()
                    }).catch(error => {
                        this.loading = false
                        console.log(error)
                    })
                },
            });
        },

        removeLocal(event, parametros) {
            this.$confirm.require({
                target: event.currentTarget,
                group: "removeLocal",
                message: "Tem certeza de que deseja remover o Local de Trabalho?",
                icon: "pi pi-exclamation-triangle",
                accept: async () => {
                    const resp = await window.axios.delete(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/removerLocal/${parametros.data.rh55_codigo}`).then(response => {
                        alert('Local de Trabalho removido com Sucesso!')
                        this.carregarlistLocal()
                    }).catch(error => {
                        this.loading = false
                        console.log(error)
                    })
                },
            });
        },

        removeAssentamento(event, parametros) {
            this.$confirm.require({
                target: event.currentTarget,
                group: "removeAssentamento",
                message: "Tem certeza de que deseja remover o Assentamento?",
                icon: "pi pi-exclamation-triangle",
                accept: async () => {
                    const resp = await window.axios.delete(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/removerAssentamento/${parametros.data.h12_codigo}`).then(response => {
                        alert('Assentamento removido com Sucesso!')
                        this.carregarlistAssentamento()
                    }).catch(error => {
                        this.loading = false
                        console.log(error)
                    })
                },
            });
        },

        removeRubricaAbatimento(event, parametros) {
            this.$confirm.require({
                target: event.currentTarget,
                group: "removeRubricaAbatimento",
                message: "Tem certeza de que deseja remover a Rubrica?",
                icon: "pi pi-exclamation-triangle",
                accept: async () => {
                    const resp = await window.axios.delete(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/removerRubricaAbatimento/${parametros.data.rh27_rubric}`).then(response => {
                        alert('Rubrica de Abatimento removida com Sucesso!')
                        this.carregarlistRubricaAbatimento()
                    }).catch(error => {
                        this.loading = false
                        console.log(error)
                    })
                },
            });
        },

        async salvarValorCalculo() {
            try {
                const data = new FormData();
                data.append('id_ano', this.anoCalculo);
                data.append('id_mes', this.mesCalculo);
                data.append('valor', this.valorCalculo);

                const resp = await window.axios.post(
                    `v4/api/recursos-humanos/pessoal/fundeb/rhcalculofundeb/salvar`, data
                );

                if (resp.status === 200) {
                    alert(`Valor Fundeb incluído com Sucesso na Competência!`);
                    this.anoCalculoIncluido.push({ rh285_ano: this.anoCalculo.rh285_ano });
                    this.mesCalculoIncluido.push({ rh285_mes: this.mesCalculo.rh285_mes });
                    this.valorCalculoIncluido.push({ rh285_valor: this.valorCalculo.rh285_valor });
                }
            } catch (error) {
                alert('Valor Fundeb não incluído corretamente!');
                this.anoCalculoIncluido = [];
                this.mesCalculoIncluido = [];
                this.valorCalculoIncluido = [];
            }
        },

        async salvarRubricaAbatimento() {
            try {
                const data = new FormData();
                data.append('tipo', 0)
                data.append('rubrica', this.rubricaAbatimento.rh27_rubric);

                const descricaoRubricaAbatimento = this.rubricaAbatimento.rh27_descr;

                const rubricaAbatimentoExistente = this.rubricaAbatimentoIncluido.find(rubrica =>
                    rubrica.rh27_rubric === this.rubrica.rh27_rubric);

                if (rubricaAbatimentoExistente) {
                    alert(`A Rubrica '${descricaoRubricaAbatimento}' já foi incluída anteriormente para o Parâmetro Rubrica de Abatimento.`);
                    return;
                }

                const resp = await window.axios.post(
                    `v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/salvar`, data
                );

                this.rubricaAbatimentoIncluido.push({
                    rh27_rubric: this.rubricaAbatimento.rh27_rubric,
                    rh27_descr: this.rubricaAbatimento.rh27_descr,
                });

                this.rubricaAbatimentoIncluido = {
                    rh27_rubric: '',
                    rh27_descr: '',
                }

                alert(`Rubrica '${descricaoRubricaAbatimento}' incluída com Sucesso!`);

            } catch (e) {
                this.rubricaAbatimentoIncluido = [];
            }
        },

        carregarlistFuncao() {
            const data = Object.assign(this.formPesquisa, {})
            data.select = this.selectpesquisa;
            data.valor = this.valorPesquisa
            this.loading = true;
            this.diretorIncluido = []
            this.dirigenteIncluido = []
            window.axios.post(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/showFuncoes`, data)
                .then((res) => {
                    const data = res.data.data;
                    if (data.length == 0) {
                        this.msg = "Nenhum registro encontrado"
                        this.aviso = true
                    } else {
                        data.map((parametros) => {
                            if (parametros.rh284_tipo == 1) {
                                this.diretorIncluido.push({
                                    rh04_codigo: parametros?.rh284_funcao,
                                    rh04_descr: parametros?.descricao_funcao?.rh04_descr ?? null
                                });
                            } else if (parametros.rh284_tipo == 2) {
                                this.dirigenteIncluido.push({
                                    rh04_codigo: parametros?.rh284_funcao,
                                    rh04_descr: parametros?.descricao_funcao?.rh04_descr ?? null
                                });
                            }
                        })
                    }

                    this.loading = false
                })
                .catch((_error) => {
                    this.loading = false
                    this.msg = "Erro ao Consultar"
                    this.aviso = true
                })
        },

        carregarlistCargo() {
            const data = Object.assign(this.formPesquisa, {})
            data.select = this.selectpesquisa;
            data.valor = this.valorPesquisa
            this.loading = true;
            this.professorIncluido = []
            this.apoioIncluido = []
            window.axios.post(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/showCargos`, data)
                .then((res) => {
                    const data = res.data.data;
                    //console.log(data)
                    if (data.length == 0) {
                        this.msg = "Nenhum registro encontrado"
                        this.aviso = true
                    } else {
                        data.map((parametros) => {
                            if (parametros.rh284_tipo == 3) {
                                this.professorIncluido.push({
                                    rh37_funcao: parametros?.rh284_cargo,
                                    rh37_descr: parametros?.descricao_cargo?.rh37_descr ?? null
                                });
                            } else if (parametros.rh284_tipo == 4 && parametros.descricao_cargo !== null) {
                                console.log(parametros);
                                this.apoioIncluido.push({
                                    rh37_funcao: parametros?.rh284_cargo,
                                    rh37_descr: parametros?.descricao_cargo?.rh37_descr ?? null
                                });
                            }
                        })
                    }

                    this.loading = false
                })
                .catch((_error) => {
                    this.loading = false
                    this.msg = "Erro ao Consultar"
                    this.aviso = true
                })
        },

        carregarlistLocal() {
            const data = Object.assign(this.formPesquisa, {})
            data.select = this.selectpesquisa;
            data.valor = this.valorPesquisa;
            this.loading = true;
            this.localIncluido = []
            window.axios.post(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/showLocais`, data)
                .then((res) => {
                    const data = res.data.data;
                    if (data.length == 0) {
                        this.msg = "Nenhum registro encontrado"
                        this.aviso = true
                    } else {
                        data.map((parametros) => {
                            if (parametros.rh284_tipo == 4 && parametros.rh284_local_trabalho != null &&
                                    parametros.descricao_local !== null) {
                                this.localIncluido.push({
                                    rh55_codigo: parametros?.rh284_local_trabalho,
                                    rh55_estrut: parametros?.descricao_local?.rh55_estrut ?? null,
                                    rh55_descr: parametros?.descricao_local?.rh55_descr ?? null
                                });
                            }
                        })
                    }
                    this.loading = false
                })
                .catch((_error) => {
                    console.log(_error);
                    this.loading = false
                    this.msg = "Erro ao Consultar"
                    this.aviso = true
                })
        },

        carregarlistAssentamento() {
            const data = Object.assign(this.formPesquisa, {})
            data.select = this.selectpesquisa;
            data.valor = this.valorPesquisa;
            this.loading = true;
            this.assentamentoIncluido = []
            window.axios.post(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/showAssentamentos`, data)
                .then((res) => {
                    const data = res.data.data;
                    if (data.length == 0) {
                        this.msg = "Nenhum registro encontrado"
                        this.aviso = true
                    } else {
                        data.map((parametros) => {
                            if (parametros.rh284_tipo == 0 && parametros.rh284_local_trabalho == null &&
                                    parametros.descricao_assentamento !== null) {
                                this.assentamentoIncluido.push({
                                    h12_codigo: parametros?.rh284_assentamento,
                                    h12_assent: parametros?.descricao_assentamento?.h12_assent?? null,
                                    h12_descr: parametros?.descricao_assentamento?.h12_descr?? null
                                });
                            }
                        })
                    }
                    this.loading = false
                })
                .catch((_error) => {
                    console.log(_error);
                    this.loading = false
                    this.msg = "Erro ao Consultar"
                    this.aviso = true
                })
        },

        carregarlistRubricaAbatimento() {
            const data = Object.assign(this.formPesquisa, {})
            data.select = this.selectpesquisa;
            data.valor = this.valorPesquisa;
            this.loading = true;
            this.rubricaAbatimentoIncluido = []
            window.axios.post(`v4/api/recursos-humanos/pessoal/fundeb/rhparametrosfundeb/showRubricaAbatimento`, data)
                .then((res) => {
                    const data = res.data.data;
                    if (data.length == 0) {
                        this.msg = "Nenhum registro encontrado"
                        this.aviso = true
                    } else {
                        data.map((parametros) => {
                            if (parametros.rh284_tipo == 0 && parametros.rh284_rubrica_abatimento != 'null' &&
                                parametros.descricao_rubrica_abatimento !== null) {
                                this.rubricaAbatimentoIncluido.push({
                                    rh27_rubric: parametros?.rh284_rubrica_abatimento,
                                    rh27_descr: parametros?.descricao_rubrica_abatimento?.rh27_descr ?? null
                                });
                            }
                        })
                    }
                    this.loading = false
                })
                .catch((_error) => {
                    console.log(_error);
                    this.loading = false
                    this.msg = "Erro ao Consultar"
                    this.aviso = true
                })
        },

        async pesquisarFuncoesDaInstituicao({ query } = { query: '' }) {
            try {
                const resp = await window.axios.get(
                    `v4/api/recursos-humanos/pessoal/rhcargo/list?nome=${query}`
                );
                this.funcoes = resp.data.data.data;
            } catch (e) {
                this.funcoes = [];
            }
        },

        async pesquisarCargosDaInstituicao({ query } = { query: '' }) {
            try {
                const resp = await window.axios.get(
                    `v4/api/recursos-humanos/pessoal/rhfuncao/list?nome=${query}`
                );
                this.cargos = resp.data.data;
            } catch (e) {
                this.cargos = [];
            }
        },

        async pesquisarLocaisDaInstituicao({ query } = { query: '' }) {
            try {
                const resp = await window.axios.get(
                    `v4/api/recursos-humanos/pessoal/rhlocaltrab/list?nome=${query}`
                );
                this.locais = resp.data.data;
            } catch (e) {
                this.locais = [];
            }
        },

        async pesquisarAssentamentosDaInstituicao({ query } = { query: '' }) {
            try {
                const resp = await window.axios.get(
                    `v4/api/recursos-humanos/rh/tipoasse/list?nome=${query}`
                );
                this.assentamentos = resp.data.data;
            } catch (e) {
                this.assentamentos = [];
            }
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

    }, beforeMount() {
        this.carregarlistCargo();
        this.carregarlistFuncao();
        this.carregarlistLocal();
        this.carregarlistAssentamento();
        this.carregarlistRubricaAbatimento();
    }
}
</script>
