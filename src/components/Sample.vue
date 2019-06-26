<template>
  <div class="img-edit">
    <h1>ImgEdit sample</h1>
    <div id="draw_range">
    <canvas id="canvas"></canvas>
      <form action="">
        <input type="file" name="" multiple accept="image/*" id="file_input">
      </form>
    </div>
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
    this.$nextTick(() => {
      edit = new ImgEdit({
        canvas: '#canvas',
        width: 800,
        height: 600,
        input: '#file_input',
        inputListener: (e) => {
          for (const f of e.target.files) {
            this.add(f)
          }
        }
      })
      edit.draw('https://t12.baidu.com/it/u=54104471,2172971201&fm=76')
      const drawRange = document.getElementById('draw_range')
      drawRange.addEventListener('dragenter', e => {
        e.preventDefault()
        drawRange.classList.add('active')
      })
      drawRange.addEventListener('dragleave', e => {
        e.preventDefault()
        drawRange.classList.remove('active')
      })
      drawRange.addEventListener('dragover', e => {
        e.preventDefault()
      })
      drawRange.addEventListener('drop', e => {
        e.preventDefault()
        for (const f of e.dataTransfer.files) {
          this.add(f)
        }
        drawRange.classList.remove('active')
      })
    })
  },
  methods: {
    add (file) {
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
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
#canvas {
  border: 1px solid black;
}
#draw_range {
  &.active {
    border: 1px dotted #eee;
  }
}
</style>
