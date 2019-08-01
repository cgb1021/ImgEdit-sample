<template>
  <div class="btn btn-secondary" :class="{'btn-secondary': !primary, 'btn-primary': primary}">
    {{text}}
  </div>
</template>

<script>
export default {
  name: 'btn',
  props: ['text', 'primary']
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
.btn {
  cursor: pointer;
}
</style>
