<script setup>
import {onMounted, ref, computed} from "vue";
import ModalLoading from "@modules/Components/ModalLoading.vue";
import IconField from 'primevue/iconfield';
import InputIcon from 'primevue/inputicon';

const modalLoading = ref(false);
const textLoad = ref('');
const itensMenu = ref(null);
const filtro = ref(null);

onMounted(() => {
    getFavoritos();
});

const getFavoritos = async () =>  {
    try {
        modalLoading.value = true;
        const resp = await window.axios.get(
            `v4/api/configuracao/menu/buscar-favoritos`
        );

        if (resp.status === 200) {
            itensMenu.value = resp.data.data;
        }
        modalLoading.value = false;
    } catch (e) {
        modalLoading.value = false;
        console.error('erro: ' + e);
    }
}

const removeFavoritos = async (data) => {
    try {
        modalLoading.value = true;
        const resp = await window.axios.delete(
            `v4/api/configuracao/menu/deletar-favorito?idMenu=${data.id_item_menu}&idInsituicao=${data.id_insituicao}`
        );
        modalLoading.value = false;
        if (resp.status === 201) {
            await getFavoritos();
        }

    } catch (e) {
        modalLoading.value = false;
        console.error('Erro: ' + e);
    }
}

const menuSelecionado = function (menu) {
    var parametros = {
        action: menu.item.funcao,
        iInstitId: menu.instituicao.codigo, // Instituição Prefeitura
        iAreaId: menu.area, // Area Patrimonial
        iModuloId: menu.modulo, // Módulo Protocolo
        lAtalhoDesktop: false
    }
    Desktop.Window.create(menu.item.descricao, parametros);
}

const filtroData = computed(() => {
    if (!filtro.value) return itensMenu.value;
    return itensMenu.value.filter((favorito) =>
        favorito.item.descricao.toString().toLowerCase().includes(filtro.value.toLowerCase())
    );

});
</script>

<template>
    <ModalLoading
        :is-loading="modalLoading"
        :message="textLoad"
    />
    <div class="corpo">
        <div class="conteudo">
            <div class="alinhado-centro div-pesquisa">
                <IconField iconPosition="left">
                    <InputIcon class="pi pi-search"> </InputIcon>
                    <InputText placeholder="Filtre pelo menu" v-model="filtro"/>
                </IconField>
            </div>
            <div class="alinhado-centro tabela">
                <DataTable
                    class="tabela-menus"
                    showGridlines
                    responsiveLayout="scroll"
                    :scrollable="true"
                    scrollHeight="flex"
                    :value="filtroData"
                    paginator
                    :rows="10"
                >
                    <template #header>
                        <div class="flex flex-wrap align-items-center justify-content-between gap-2">
                            <span class="text-xl text-900 font-bold">Itens de Menu</span>
                            <Button @click="getFavoritos()" icon="pi pi-refresh" rounded raised />
                        </div>
                    </template>
                    <Column field="item.descricao" header="Menu"></Column>
                    <Column field="instituicao.nomeinst" header="Instituição"></Column>
                    <Column header="Ações">
                        <template #body="{ data }">
                            <div class="icones-acoes">
                                <i
                                    @click="removeFavoritos(data)"
                                    class="pi pi-star-fill icones estrelas"
                                    title="Remover Favoritos"
                                />
                                <i
                                    @click="menuSelecionado(data)"
                                    title="Ir até o menu"
                                    class="pi pi-arrow-circle-right icones irmenu"
                                />
                            </div>
                        </template>
                    </Column>
                </DataTable>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .corpo {
        display:flex;
        justify-content:center;
        align-items: center;
        width:100% !important;
        height: 100vh !important;
    }

    .conteudo {
        display:flex;
        flex-direction: column;
        width:100%;
        height: 85%;
        background-color:white;
    }

    .tabela-menus {
        width: 90%;
        margin-top: 10px;
    }

    .icones-acoes {
        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        gap: 10px;
    }

    .icones {
        font-size: 20px;
    }

    .icones {
        cursor:pointer;
    }

    .alinhado-centro {
        display: flex;
        justify-content: center;
    }

    .div-pesquisa {
        margin-top: 3%
    }

    .tabela {
        margin-top: 10px;
    }

    .estrelas {
        color: #c1aa00;
    }

    .irmenu:hover {
        cursor: pointer;
        color: dodgerblue;
    }
</style>
