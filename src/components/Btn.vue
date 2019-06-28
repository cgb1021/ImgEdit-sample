<template>
  <div class="btn">
    {{text}}
  </div>
</template>

<script>
export default {
  name: 'btn',
  props: ['text']
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
.btn {
  cursor: pointer;
  border: 1px solid #ccc;
  display: inline-block;
  font-size: 14px;
  padding: 0 0.75em;
  line-height: 1.5;
  border-radius: 3px;
  user-select: none;
  &:hover {
    background: #f1f1f1;
  }
}
</style>
