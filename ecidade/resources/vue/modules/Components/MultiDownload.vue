<template>
    <Dialog header="Arquivos para Download" :modal="true"  closable v-model:visible="display"
            :breakpoints="{'960px': '75vw', '640px': '90vw'}" :style="{width: '30vw'}" :position="position"
            @hide="closeModal()">
        <div class="card">
            <div class="flex justify-content-start flex-wrap card-container">
                <ul class="list-none">
                    <li v-for="file in files" class="mt-2 text-xl">
                         <a :href="file.url" download>
                             <i :class="file.icon" class="m2" style="font-size: 2rem; color:#4a789c"></i> {{ file.name }}
                         </a>
                    </li>
                </ul>
            </div>
        </div>

    </Dialog>
</template>

<script>
export default {
    name: "MultiDownload",
    props: {
        position: String
    },
    data() {
        return {
            display: false,
            files: []
        }
    },
    methods: {
        openModal() {
            this.display = true;
        },
        closeModal() {
           this.display = false;
           this.files = [];
        },
        addFile(url, name, icon = 'pi pi-file-pdf') {

            let extension = url.split('.').pop();
            switch (extension) {
                case 'pdf':
                    icon = 'pi pi-file-pdf';
                    break;

                case 'doc':
                case 'docx':
                case 'odt':
                    icon = 'pi pi-file-word';
                    break;

                case 'xlsx':
                case 'xls':
                case 'csv':
                case 'ods':
                    icon = 'pi pi-file';
                    break;
            }

            this.files.push({
                url: url,
                name: name,
                icon: icon
            });
        }
    }
}
</script>

<style scoped>
    .list-none {
        margin: 0 auto;
        text-align: center;
        align-items: center;
        justify-content: center;
    }

    a {
        text-decoration: none;
    }
</style>
