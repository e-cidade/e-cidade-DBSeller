<template>
  <div class="secao">
    <div class="titulo-secao">
      <h2>{{ title }}
        <a @click="toogle">
          <i class="fas fa-eye" style="color:white" v-if="open"></i>
          <i class="fas fa-eye-slash" style="color:white" v-if="!open"></i>
        </a>
      </h2>
    </div>
    <div v-if="open">
      <slot></slot>
    </div>
  </div>
</template>

<script>
export default {
  name: "SecaoComponent",
  props: ["title"],
  data() {
    return {
      open: true
    }
  },
  methods: {
    toogle() {
      this.open = !this.open;
    }
  }
}
</script>

<style scoped>
.secao {
  display: flex;
  border: solid 1px #ccc;
  margin: 8px;
  flex-direction: column;
}

.titulo-secao {
  display: flex;
  justify-content: center;
  height: 50px;
  width: 100%;
  border: 1px solid #ccc;
  background: #0b77b7;
  color: white;
  flex-wrap: wrap;
}
</style>