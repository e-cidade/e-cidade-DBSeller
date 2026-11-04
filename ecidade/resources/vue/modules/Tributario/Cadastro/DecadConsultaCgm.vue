<template>
    <ModalLoading :isLoading="loading" />
    <section class="container">
        <Panel>
            <template #header>
                <b>Pesquisar</b>
            </template>
            <div class="flex align-items-center justify-content-center m-2" style="width: 100%;">
                <div class="p-1 m-auto">

                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 250px">
                            <label>Selecione o exercicio: </label>
                        </div>
                        <div class="col">
                            <Dropdown style="width: 207px" :disabled="isActive" v-model="ano" :options="anos"
                                optionLabel="name" optionValue="code" placeholder="" />
                        </div>
                    </div>
                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 250px">
                            <label>Data Inicial: </label>
                        </div>
                        <div class="col">
                            <Calendar style="width: 207px" class="p-inputtext-sm" :disabled="isActive"
                                v-model="datainicial" :showIcon="true" />
                        </div>
                    </div>
                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 250px">
                            <label>Data Final: </label>
                        </div>
                        <div class="col">
                            <Calendar style="width: 207px" class="p-inputtext-sm" :disabled="isActive"
                                v-model="datafinal" :showIcon="true" />
                        </div>
                    </div>
                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 250px">
                            <label>Status: </label>
                        </div>
                        <div class="col">
                            <Dropdown style="width: 207px" :disabled="isActive" v-model="status" :options="statusarray"
                                optionLabel="name" optionValue="code" placeholder="" />
                        </div>
                    </div>
                    <div class="grid">
                        <div class="col-fixed pt-3" style="width: 250px">
                            <label for="matricula">Matricula: </label>
                        </div>
                        <div class="col">
                            <InputNumber :disabled="isActive" style="width: 205px" inputId="matricula" v-model="matricula" mode="decimal"
                                :useGrouping="false" />
                        </div>
                    </div>
                    <div class="grid mt-5">
                        <div class="col flex align-items-center justify-content-center">
                            <Button label="Secondary" @click="consultar" class="p-button-sm  m-3">Pesquisar</Button>
                            <Button label="Secondary" @click="limpar"
                                class="p-button-sm p-button-danger  m-3">Limpar</Button>
                        </div>
                    </div>
                </div>
            </div>
        </Panel>
        <div class="mt-5">
            <DataTable v-show="cgm.length != 0" :value="cgm" ref="table" :paginator="true" class="p-datatable-customers"
                :rows="10" :rowsPerPageOptions="[10, 25, 50]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} entries"
                responsiveLayout="scroll">
                <template #header>
                    <div style="text-align: left">
                        <Button icon="pi pi-external-link" label="CSV" @click="exportCSV($event)" />
                        <Button class="ml-2" icon="pi pi-external-link" label="Excel" @click="exportCSVFile()" />
                        <Button class="ml-2" icon="pi pi-external-link" label="PDF" @click="downloapdf()" />
                    </div>
                </template>
                <template #empty>
                    Nenhum Registro
                </template>
                <Column field="cgm" header="CGM"></Column>
                <Column field="cpf" header="CPF"></Column>
                <Column field="nome" header="Nome"></Column>
                <Column field="data" header="Data do envio"></Column>
                <Column field="matricula" header="Matricula"></Column>
                <Column field="perc" header="Percentual"></Column>
                <Column field="iptu" header="IPTU(R$)"></Column>
                <Column field="desconto" header="Desconto(R$)"></Column>
                <Column field="status" header="Status"></Column>
            </DataTable>
        </div>
    </section>
    <Dialog header="Aviso" v-model:visible="display" :breakpoints="{ '960px': '75vw', '640px': '90vw' }"
        :style="{ width: '50vw' }">
        {{ msg }}
    </Dialog>
</template>

<script>
import ModalLoading from '../../Components/ModalLoading.vue'
export default {
    name: 'DecadConsultaCgm',
    components: {
        ModalLoading
    },
    data() {
        return {
            loading: false,
            cgm: [],
            datafinal: null,
            datainicial: null,
            ano: null,
            anos: null,
            isActive: false,
            display: false,
            msg: null,
            matricula: null,
            status: 1,
            statusarray: [
                { name: 'Todos', code: 1 },
                { name: 'Processado', code: 2 },
                { name: 'Não processado', code: 3 },
                { name: 'Sem Matricula', code: 4 }
            ],
        }
    }, methods: {
        exportCSV() {
            this.$refs.table.exportCSV();
        },
        limpar() {
            this.isActive = false
            this.datafinal = null
            this.datainicial = null
            const dataAtual = new Date();
            const anoAtual = dataAtual.getFullYear();
            this.ano = anoAtual
            this.cgm = []
            this.matricula = null
            this.status = 1
        },
        consultar() {

            if (this.ano == null) {
                this.msg = "Selecione o Ano"
                this.display = true
                return
            } else if (this.datainicial == null && this.matricula == null) {
                this.msg = "Defina a Data Inicial ou uma Matricula"
                this.display = true
                return
            }

            var data = Object.assign({
                ano: this.ano,
                matricula: this.matricula,
                status: this.status
            }, {})

            if (!this.matricula) {
                if (this.datafinal == null) {
                    const d = new Date();
                    data.datafinal = d.toISOString().split("T")[0];
                } else {
                    data.datafinal = this.datafinal.toISOString().split("T")[0];
                }
                data.datainicial = this.datainicial.toISOString().split("T")[0];
            }

            data.ano = this.ano;

            this.loading = true;
            window.axios
                .post('v4/api/tributario/cadastro/decad/pesquisarcgm', data)
                .then((res) => {
                    this.loading = false;
                    const data = res.data.data
                    if (data.length > 0) {
                        this.cgm = data;
                        this.isActive = true
                    } else {
                        this.msg = 'Nenhum CGM encontrado'
                        this.display = true
                    }
                })
                .catch((error) => {
                    this.loading = false;
                    const message = error.response.data.message
                    this.msg = message
                    this.display = true
                })
        },
        downloapdf() {
            if (this.cgm.length == 0) {
                this.msg = "Nenhum CGM encontrado!"
                this.display = true
                return
            }

            var data = Object.assign({
                cgms: this.cgm,
            }, {})

            if (this.datafinal == null) {
                const d = new Date();
                data.datafinal = d.toISOString().split("T")[0];
            } else {
                data.datafinal = this.datafinal.toISOString().split("T")[0];
            }

            data.ano = this.ano;
            data.datainicial = this.datainicial.toISOString().split("T")[0];

            data.cgms = this.cgm;
            this.loading = true;
            window.axios
                .post('v4/api/tributario/cadastro/decad/relatoriocgm', data)
                .then((res) => {
                    const data = res.data.data
                    window.open(window.CurrentWindow.ECIDADE_REQUEST_PATH + data, '', 'height=800, width=600');
                    this.loading = false;
                })
                .catch((error) => {
                    this.loading = false;
                    const message = error.response.data.message
                    this.msg = message
                    this.display = true
                })
        },
        convertToCSV(objArray) {
            const array = typeof objArray !== 'object' ? JSON.parse(objArray) : objArray;
            let str = '';
            for (let i = 0; i < array.length; i++) { // eslint-disable-line
                let line = '';
                for (const index in array[i]) { // eslint-disable-line
                    if (line !== '') line += ',';
                    if (!array[i][index])
                        line += '';
                    else
                        line += array[i][index];


                }
                str += line + '\r\n'; // eslint-disable-line
            }
            return str;
        }, exportCSVFile() {
            const headers = {
                cgm: 'CGM',
                cpf: 'CPF',
                name: 'Nome',
                data: 'Data de Envio',
                matricula: 'Matricula',
                percentual: 'Percentual',
                iptu: 'IPTU(R$)',
                desconto: 'Desconto(R$)',
                status: 'Status'
            }

            const items = this.cgm
            const filename = 'somefilename.csv'
            const fileTitle = 'somefilename'
            if (headers) {
                items.unshift(headers);
            }
            const jsonObject = JSON.stringify(items);
            const csv = this.convertToCSV(jsonObject);
            this.cgm.splice(0, 1);
            const exportedFilenmae = fileTitle + '.xlsx' || 'export.xlsx'; // eslint-disable-line
            const blob = new Blob([csv], { type: 'text/xlsx;charset=utf-8;' });
            if (navigator.msSaveBlob) { // IE 10+
                navigator.msSaveBlob(blob, exportedFilenmae);
            } else {
                const link = document.createElement('a');
                if (link.download !== undefined) {
                    const url = URL.createObjectURL(blob);
                    link.setAttribute('href', url);
                    link.setAttribute('download', exportedFilenmae);
                    link.style.visibility = 'hidden';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                }
            }
        },
        getAno() {
            window.axios
                .get('v4/api/tributario/cadastro/decad/ano')
                .then((res) => {
                    const data = res.data
                    this.anos = [
                        { name: data - 1, code: data - 1 },
                        { name: data, code: data },
                        { name: data + 1, code: data + 1 }
                    ];
                    this.ano = data
                })
        },
    }, beforeMount() {
        this.getAno()
    },

}
</script>
