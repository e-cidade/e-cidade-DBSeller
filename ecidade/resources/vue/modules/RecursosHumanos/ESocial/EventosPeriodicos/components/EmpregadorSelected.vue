<script setup>
defineProps(['label', 'instituicao', 'isLoading'])
</script>
<template>
    <template v-if="isLoading">
        <label class="col-12"></label>
        <Skeleton width="10rem" height="2rem" class="w-full mb-3"></Skeleton>
    </template>
    <template v-else>
        <label for=""><b>{{ label }}</b></label>
        <select class="selected-customizer w-full mb-3">
            <option value="" disabled>-- Selecione uma opção</option>
            <option v-for="c in instituicao" :key="c.codigo" :value="c.codigo">{{ c.nomeinst }}</option>
        </select>
    </template>
</template>
<style scoped>
.selected-customizer {
  padding: 0.5rem 0.5rem;
  border: 1px solid #605e5c;
  background-color: white;
  appearance: none;
  border-radius: 2px;
}
</style>