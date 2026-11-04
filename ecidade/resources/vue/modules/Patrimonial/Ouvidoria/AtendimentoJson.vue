<template>
    <div class="container">
        <div class="p-fluid grid m-2">
            <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <InputText id="inputTextNumero" type="text" v-model="form.numero"/>
                        <label for="inputTextNumero">Número</label>
                    </span>
            </div>
            <div class="field col-12 md:col-4">
                    <span class="p-float-label">
                        <InputText id="inputTextAno" type="text" v-model="form.ano"/>
                        <label for="inputTextAno">Ano</label>
                    </span>
            </div>
            <div class="field col-12 md:col-2">
                <Button class="p-button" label="Pesquisar" @click="findJson"></Button>
            </div>
            <div class="field col-12 md:col-2">
                <Button class="p-button" label="Salvar Json" v-if="json.secoes" @click="saveJson"></Button>
            </div>
        </div>
        <JsonEditorVue v-model="json"/>
        <ModalLoading :isLoading="loading"/>
        <Toast/>
    </div>
</template>

<script>
import JsonEditorVue from "json-editor-vue";
import ModalLoading from "../../Components/ModalLoading.vue";
import Toast from 'primevue/toast';

export default {
    components: {
        JsonEditorVue,
        ModalLoading,
        Toast
    },
    data() {
        return {
            loading: false,
            form: {
                numero: '',
                ano: ''
            },
            json: {},
            atendimento_id: '',
        }
    },
    methods: {
        clear() {
            this.json = {};
            this.atendimento_id = '';
        },
        async findJson() {
            const {numero, ano} = this.form;
            if (!numero || !ano) {
                this.$toast.add({
                    severity: 'info',
                    summary: 'Atenção',
                    detail: "Prenche número do atendimento e ano!",
                    life: 3000
                });
                return;
            }
            this.loading = true;

            try {
                const resp = await window.axios.get(
                    `v4/api/patrimonial/ouvidoria/atendimento/atendimento-json/numero/${numero}/ano/${ano}`
                );
                this.loading = false;
                const data = resp.data;
                if (data.error && data.message) {
                    this.clear();
                    this.$toast.add({severity: 'warn', summary: 'Atenção', detail: data.message, life: 3000});
                    return;
                }

                if (data.error) {
                    this.clear();
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Atenção',
                        detail: "Ocorreu um erro ao consultar o atendimento",
                        life: 3000
                    });
                    return;
                }

                this.json = JSON.parse(data.data.json);
                this.atendimento_id = data.data.atendimento_id;

            } catch (e) {
                this.clear();
                this.loading = false;
                if (e.response.data.message) {
                    this.$toast.add({
                        severity: 'warn',
                        summary: 'Atenção',
                        detail: e.response.data.message,
                        life: 3000
                    });
                    return;
                }
                this.$toast.add({
                    severity: 'error',
                    summary: 'Erro',
                    detail: 'Ocorreu um erro ao consultar o atendimento',
                    life: 3000
                });
            }

        },
        async saveJson() {
            this.loading = true;
            try {
                const resp = await window.axios.put(
                    `v4/api/patrimonial/ouvidoria/atendimento/atendimento-json/atendimento_id/${this.atendimento_id}`,
                    {json: JSON.stringify(this.json)}
                );
                const data = resp.data;
                if (data.error && data.message) {
                    this.$toast.add({
                        severity: 'warn',
                        summary: 'Erro',
                        detail: data.message,
                        life: 3000
                    });
                    return;
                }

                if (data.error) {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Erro',
                        detail: "Ocorreu um erro ao consultar o atendimento",
                        life: 3000
                    });
                    return;
                }
                this.$toast.add({
                    severity: 'success',
                    summary: 'Sucesso',
                    detail: "Atualizado com sucesso!",
                    life: 3000
                });
                this.clear();
                this.loading = false;
            } catch (e) {
                this.loading = false;
                if (e.response.data.message) {
                    this.$toast.add({
                        severity: 'warn',
                        summary: 'Erro',
                        detail: e.response.data.message,
                        life: 3000
                    });
                    return;
                }

                if (data.error) {
                    this.$toast.add({
                        severity: 'error',
                        summary: 'Erro',
                        detail: "Ocorreu um erro ao consultar o atendimento",
                        life: 3000
                    });
                    return;
                }
            }


        }
    },
    watch: {
        'form.numero'(newValue) {
            if (!newValue) {
                this.clear();
            }
        },
        'form.ano'(newValue) {
            if (!newValue) {
                this.clear();
            }
        }
    }
}
</script>

<style scoped>
</style>
