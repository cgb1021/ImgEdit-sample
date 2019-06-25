<template>
  <div class="img-edit">
    <h1>ImgEdit sample</h1>
  </div>
</template>

<script>
import ImgEdit from 'imgedit'
export default {
  name: 'Sample',
  data () {
    return {
    }
  },
  mounted () {
    console.log('sample mounted', ImgEdit)
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
h1, h2 {
  font-weight: normal;
}
ul {
  list-style-type: none;
  padding: 0;
}
li {
  display: inline-block;
  margin: 0 10px;
}
a {
  color: #42b983;
}
</style>
