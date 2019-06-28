<template>
  <div class="img-edit">
    <h1>ImgEdit sample</h1>
    <div id="draw_range">
      <canvas id="canvas"></canvas>
      <form action="">
        <input type="file" name="" multiple accept="image/*" id="file_input">
      </form>
      <div class="info">width: {{file.width}} height: {{file.width}}</div>
      <div class="tools"><Btn text="逆时针90度" @click.native="rotate(-.5)"/><Btn text="顺时针90度" @click.native="rotate(.5)"/><Btn text="裁剪" @click.native="cut()"/><Btn text="缩放" @click.native="resize()"/><Btn text="清理" @click.native="clean()"/><Btn text="预览" @click.native="preview"/></div>
    </div>
  </div>
</template>

<script>
import ImgEdit from 'imgedit'
import message from 'jmessage'
import SparkMD5 from 'spark-md5'
import Btn from './Btn'
let edit
function getImgBlob (url) {
  return new Promise((resolve) => {
    const xhr = new XMLHttpRequest()
    xhr.responseType = 'blob'
    xhr.onload = () => {
      const file = xhr.response
      let name = ''
      const m = url.match(/[\w.-]+\.(?:jpe?g|png)$/)
      if (m) name = m[0]
      else {
        name = SparkMD5.hash(url)
        switch (file.type.slice('/')[1]) {
          case 'png': name = `${name}.png`
            break
          default: name = `${name}.jpg`
            break
        }
      }
      file.name = name
      resolve(file)
    }
    xhr.open('GET', url)
    // xhr.overrideMimeType('text/plain; charset=x-user-defined')
    xhr.send(null)
  })
}
export default {
  name: 'sample',
  components: { Btn },
  data () {
    return {
      file: {
        name: '',
        size: 0,
        width: 0,
        height: 0,
        md5: ''
      }
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
        height: 600
        /* input: '#file_input',
        inputListener: (e) => {
          for (const f of e.target.files) {
            this.add(f)
          }
        } */
      })
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
        /* for (const f of e.dataTransfer.files) {
          this.add(f)
        } */
        this.add(e.dataTransfer.files[0])
        drawRange.classList.remove('active')
      })
      document.getElementById('file_input').addEventListener('change', e => {
        /* for (const f of e.target.files) {
          this.add(f)
        } */
        this.add(e.target.files[0])
      })
      this.add('https://t12.baidu.com/it/u=54104471,2172971201&fm=76')
      edit.onChange((state, type) => {
        console.log(type, state)
      })
    })
    message.config({
      noClose: true,
      dragMode: 0
    })
  },
  methods: {
    async add (file) {
      if (typeof file === 'string' && /^(?:https?:)?\/\//.test(file)) {
        file = await getImgBlob(file)
      }
      if (!/\.(?:png|jpg|jpeg|gif|bmp)$/i.test(file.name)) return false
      const blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice
      const fileReader = new FileReader()
      const spark = new SparkMD5.ArrayBuffer()
      const chunkSize = 2097152
      const chunks = Math.ceil(file.size / chunkSize)
      let currentChunk = 0
      let md5 = ''
      fileReader.onload = async (e) => {
        spark.append(e.target.result)
        currentChunk++
        if (currentChunk < chunks) {
          // 继续加载
          loadNext()
        } else {
          // 数据块加载结束
          md5 = spark.end()
          await edit.open(file)
          edit.draw()
          /* this.files.push({
            name: file.name,
            size: file.size,
            width: edit.width(),
            height: edit.height(),
            md5
          }) */
          this.file.name = file.name
          this.file.size = file.size
          this.file.width = edit.width()
          this.file.height = edit.height()
          this.file.md5 = md5
        }
      }
      function loadNext () {
        const start = currentChunk * chunkSize
        const end = start + chunkSize >= file.size ? file.size : start + chunkSize
        fileReader.readAsArrayBuffer(blobSlice.call(file, start, end))
      }
      loadNext()
    },
    rotate (deg) {
      edit.rotate(deg)
    },
    cut () {
      edit.cut()
    },
    resize () {
      edit.resize()
    },
    clean () {
      edit.clean()
    },
    preview () {
      const img = new Image()
      img.src = edit.toDataURL()
      const box = message.pop().append(img)
      window.setTimeout(() => {
        box.center()
      }, 0)
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss">
@import 'jmessage/style.css';
#canvas {
  border: 1px solid black;
}
#draw_range {
  &.active {
    border: 1px dotted #eee;
  }
}
.jmessage .message-box.pop-box {
  width:800px;
  .message-box__head {
    font-size: 0;
  }
  .message-box__foot {
    display: none;
  }
}
</style>
