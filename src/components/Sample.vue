<template>
  <div class="img-edit">
    <h1>ImgEdit sample</h1>
    <canvas id="canvas"></canvas>
    <form action="">
      <input type="file" name="" id="file_input">
    </form>
  </div>
</template>

<script>
import SparkMD5 from 'spark-md5'
import ImgEdit from 'imgedit'
let edit
export default {
  name: 'Sample',
  data () {
    return {
    }
  },
  mounted () {
    /* edit = new ImgEdit({
      canvas: document.getElementById('canvas'),
      width: 800,
      height: 600
    }) */
    // edit = new ImgEdit(document.getElementById('canvas'))
    // edit = new ImgEdit('#canvas')
    edit = new ImgEdit({
      canvas: '#canvas',
      width: 800,
      height: 600,
      input: '#file_input',
      listenHook: (e) => {
        const file = e.target.files[0]
        if (!/\.(?:png|jpg|jpeg|gif|bmp)$/i.test(file.name)) return false
        const blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice
        const fileReader = new FileReader()
        const spark = new SparkMD5.ArrayBuffer()
        const chunkSize = 2097152
        const chunks = Math.ceil(file.size / chunkSize)
        let currentChunk = 0
        let md5 = ''
        fileReader.onload = function (e) {
          spark.append(e.target.result)
          currentChunk++
          if (currentChunk < chunks) {
            // 继续加载
            loadNext()
          } else {
            // 数据块加载结束
            md5 = spark.end()
            console.log(md5)
          }
        }
        function loadNext () {
          const start = currentChunk * chunkSize
          const end = start + chunkSize >= file.size ? file.size : start + chunkSize
          fileReader.readAsArrayBuffer(blobSlice.call(file, start, end))
        }
        loadNext()
      }
    })
    edit.draw('https://t12.baidu.com/it/u=54104471,2172971201&fm=76')
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
#canvas {
  border: 1px solid black;
}
</style>
