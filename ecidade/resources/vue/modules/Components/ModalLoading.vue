<template>
    <transition name="modal">
        <div v-if="isLoading" class="modal-mask">
            <div class="modal-wrapper">
                <div class="modal-container">
                    <ProgressSpinner strokeWidth="4" />
                </div>
                <h2 style="color:white; white-space: nowrap;"><slot>{{message}}</slot></h2>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: 'ModalLoading',
    props: {
        isLoading: Boolean,
        message: {
            type: String,
            default:'Carregando...'
        }
    }
}
</script>

<style scoped>

.modal-mask {
    position: fixed;
    z-index: 9998;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: table;
    transition: opacity 0.3s ease;
}

.modal-wrapper {
    display: table-cell;
    vertical-align: middle;
    text-align: center;
}

.modal-container {
    width: 100px;
    margin: 0px auto;
    transition: all 0.3s ease;
}
</style>
