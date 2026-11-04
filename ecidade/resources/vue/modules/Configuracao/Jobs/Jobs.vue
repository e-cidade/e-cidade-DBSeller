<script setup>

import { ref, onMounted, reactive, watch } from "vue";
import TabView from 'primevue/tabview';
import TabPanel from 'primevue/tabpanel';
import Paginator from 'primevue/paginator';
import Swal from 'sweetalert2';
import JobsTable from "./Components/JobsTable.vue";
import JobsFailedTable from "./Components/JobsFailedTable.vue";
import JobsFilter from "./Components/JobsFilter.vue";
import JobsModal from "./Components/JobsModal.vue";

const jobs = ref([]);
const jobsFailed = ref([]);
const selectJobs = ref([]);
const activeIndex = ref(0);
const sortOrder = ref(1);
const sortField = ref('');
const visible = ref(false);
const loading = ref(false);

const filter = reactive({
    id: null,
    exception: null,
    entity: null,
    data_inicio: null,
    data_fim: null
});

const dadosModal = reactive({
    titulo: 'Modal',
    conteudo: ''
});

const paginate = reactive({
    rows: 10,
    total: 0,
    to: 0
});

onMounted(() => {
    getJobs();
});

watch(activeIndex, () => {
    selectJobs.value = [];
    clearFilter();
    clearSort();
});

const getJobs = async (options = {}) => {
    loading.value = true;

    let { page, rows } = options;

    const { id, exception, entity, data_inicio, data_fim } = filter;
    const search = { id, exception, entity, data_inicio, data_fim };

    const params = new URLSearchParams({
        page: page ? ++page : 1,
        perPage: rows ? rows : 10,
        search: JSON.stringify(search)
    });

    if (activeIndex.value === 0) {
        await fetchJobs(params);
    }

    if (activeIndex.value === 1) {
        await fetchJobsFailed(params);
    }
}

const fetchJobs = async (params) => {
    try {
        const resp = await axios.get("/v4/api/configuracao/jobs?"
            + params.toString()
            + `&sortField=${sortField.value}&sortOrder=${sortOrder.value}`
        );
        const data = await resp.data;
        jobs.value = data.data.data;
        handlePaginate(data.data);
        loading.value = false;
    } catch (e) {
        Swal.fire("Erro", e.response.data.message, "error");
        loading.value = false;
    }
}

const fetchJobsFailed = async (params) => {
    try {
        const resp = await axios.get("/v4/api/configuracao/jobs/failed?"
            + params.toString()
            + `&sortField=${sortField.value}&sortOrder=${sortOrder.value}`
        );
        const data = await resp.data;
        jobsFailed.value = data.data.data;
        handlePaginate(data.data);
        loading.value = false;
    } catch (e) {
        Swal.fire("Erro", e.response.data.message, "error");
        loading.value = false;
    }
}

const handlePaginate = (data) => {
    paginate.rows = data.per_page;
    paginate.total = data.total;
    paginate.to = data.to;
}

const retry = async () => {
    loading.value = true;

    const failed_ids = [];

    selectJobs.value.forEach(e => {
        failed_ids.push(e.id);
    });

    try {
        await axios.post("/v4/api/configuracao/jobs/failed/retry-many", { failed_ids });
    } catch (e) {
        if (e.response && e.response.status === 400) {
            Swal.fire("Erro", e.response.data.message, "error");
        }
    }

    selectJobs.value = [];

    loading.value = false;

    getJobs();
}

const destroy = async () => {
    loading.value = true;

    const failed_ids = [];

    selectJobs.value.forEach(e => {
        failed_ids.push(e.id);
    });

    try {
        const isFailed = activeIndex.value === 1 ? "failed/" : "";
        await axios.post(`/v4/api/configuracao/jobs/${isFailed}destroy-many`, { failed_ids });
    } catch (e) {
        Swal.fire("Erro", e.response.data.message, "error");
    }

    selectJobs.value = [];

    loading.value = false;

    getJobs();
}

const formatDate = (data) => {
    let date;

    if (Number.isInteger(data)) {
        date = new Date(data * 1000);
    } else if (data.includes("T")) {
        date = new Date(data);
    } else {
        let ISODate = data.replace(" ", "T") + ".000000Z";
        date = new Date(ISODate);
    }

    return date.toLocaleString("pt-BR", {
        day: "2-digit",
        month: "2-digit",
        year: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    }).replace(",", " ");
}

const sort = (event) => {
    sortField.value = event.sortField;
    sortOrder.value = event.sortOrder;

    getJobs();
}

const clearFilter = () => {
    filter.id = '';
    filter.exception = '';
    filter.entity = '';
    filter.data_fim = '';
    filter.data_inicio = '';
    getJobs();
}

const clearSort = () => {
    sortField.value = '';
    sortOrder.value = 1;
}

const showModal = (titulo = '', conteudo = '') => {
    dadosModal.titulo = titulo;
    dadosModal.conteudo = conteudo;
    visible.value = true;
}

const trataHtml = (text) => {
    return text.split("#").join("<br>#");
}

</script>

<template>
    <JobsModal v-model:visible="visible" :dadosModal="dadosModal" :trataHtml="trataHtml" />

    <div class="container-table">
        <JobsFilter
            :filter="filter"
            :activeIndex="activeIndex"
            :selectJobs="selectJobs"
            :retry="retry"
            :destroy="destroy"
            :getJobs="getJobs"
            @onSearch="getJobs"
            @clearFilter="clearFilter"
        />

        <TabView value="0" v-model:activeIndex="activeIndex">
            <TabPanel header="Jobs">
                <JobsTable
                    v-model:selectJobs="selectJobs"
                    :jobs="jobs"
                    :showModal="showModal"
                    :formatDate="formatDate"
                    :loading="loading"
                    @onSort="sort"
                />
            </TabPanel>

            <TabPanel header="Jobs falhados">
                <JobsFailedTable
                    v-model:selectJobs="selectJobs"
                    :jobsFailed="jobsFailed"
                    :showModal="showModal"
                    :formatDate="formatDate"
                    :loading="loading"
                    @onSort="sort"
                />
            </TabPanel>
        </TabView>

        <Paginator
            v-if="paginate.total !== 0"
            :rows="paginate.rows"
            :totalRecords="paginate.total"
            :rowsPerPageOptions="[10, 20, 30, 50, 80, 100]"
            @page="getJobs"
        >
            <template #start>
                {{ paginate.to }} de {{ paginate.total }}
            </template>
        </Paginator>

        <p v-else class="text-center" style="font-size: 1.2rem;">Sua pesquisa não retornou resultados</p>
    </div>
</template>
