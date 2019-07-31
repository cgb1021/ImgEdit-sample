<template>
  <div class="img-edit">
    <h1>ImgEdit sample</h1>
    <Modal :is-show="isShow" @close="close">
      <div id="draw_range">
        <canvas id="canvas"></canvas>
        <div class="info">{{state.width}}px X {{state.height}}px @{{state.scale}}</div>
        <div class="info">（高x宽）<input type="text" v-model="width" placeholder="width">x<input type="text" v-model="height" placeholder="height"><Btn text="调整" @click.native="resize"/></div>
        <div class="info">（width,height,x,y）<input type="text" :value="range" @change="change($event, 'range')" placeholder="width,height,x,y"><Btn text="裁剪" @click.native="cut"/></div>
        <div class="info tool"><Btn text="逆时针90度" @click.native="rotate(-.5)"/><Btn text="顺时针90度" @click.native="rotate(.5)"/><Btn text="放大" @click.native="scale(state.scale + .1)"/><Btn text="缩小" @click.native="scale(state.scale - .1)"/><Btn text="平铺" @click.native="scale(1)"/><Btn text="居中" @click.native="align('center')"/></div>
        <div class="info tool"><Btn text="清理" @click.native="clean"/><Btn text="重置" @click.native="reset"/><Btn text="预览" @click.native="preview"/><Btn text="保存" @click.native="save"/></div>
      </div>
    </Modal>
    <form action="">
      <label>选择本地图片 <input type="file" name="" multiple accept="image/*" id="file_input"><Btn text="选择"/></label>
      <label>输入在线图片 <input type="text" v-model="url"><Btn text="加载" @click.native="fetch"/></label>
    </form>
    <div class="filelist">
      <ul>
        <li v-for="(file, index) in fileList" :key="index" :class="{'current': fileListIndex === index}">{{file.name}} | {{getSize(file.size)}} | {{file.md5}} | {{file.result}} <Btn text="编辑" @click.native="open(index)"/> <Btn text="上传" @click.native="upload(index)"/> <Btn text="移除" @click.native="remove(index)"/></li>
      </ul>
    </div>
  </div>
</template>

<script>
import ImgEdit, { fetchImg/* , resize, cut, rotate */ } from 'imgedit'
import message from 'jmessage'
import SparkMD5 from 'spark-md5'
import Btn from '../components/Btn'
import Modal from '../components/Modal'
let edit
export default {
  name: 'sample',
  components: { Btn, Modal },
  data () {
    return {
      url: '',
      fileList: [],
      fileListIndex: -1,
      isShow: false,
      file: {
        name: '',
        size: 0,
        width: 0,
        height: 0,
        md5: ''
      },
      width: 0,
      height: 0,
      state: {
        width: 0,
        height: 0,
        scale: 0,
        range: {
          x: 0,
          y: 0,
          width: 0,
          height: 0
        },
        type: 0
      }
    }
  },
  mounted () {
    edit = new ImgEdit({
      canvas: '#canvas',
      width: 800,
      height: 600,
      before: (context) => {
        const canvas = context.canvas
        const bgSize = 10
        const xs = Math.ceil(canvas.width / bgSize) // 画canvas背景x轴循环次数
        const ys = Math.ceil(canvas.height / bgSize) // 画canvas背景y轴循环次数
        const color1 = '#ccc'
        const color2 = '#eee' // 画布和图片的比例
        for (let y = 0; y < ys; ++y) {
          let color = y % 2 ? color1 : color2
          for (let x = 0; x < xs; ++x) {
            context.fillStyle = color
            context.fillRect(x * bgSize, y * bgSize, bgSize, bgSize)
            color = color === color1 ? color2 : color1
          }
        }
      }/* ,
      after: () => {
        console.log('after')
      } */
    })
    const drawRange = document.getElementById('draw_range')
    drawRange.addEventListener('transitionend', e => {
      console.log(e)
    })
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
      // this.add(e.dataTransfer.files[0])
      drawRange.classList.remove('active')
    })
    document.getElementById('file_input').addEventListener('change', e => {
      for (const f of e.target.files) {
        this.add(f)
      }
      // this.add(e.target.files[0])
    })
    edit.onChange((state) => {
      console.log('edit.onChange', state)
      Object.assign(this.state, state)
    })
    /* resize('https://t12.baidu.com/it/u=54104471,2172971201&fm=76', 100).then((b64) => {
      const img = new Image()
      img.onload = () => {
        const box = message.pop().append(img)
        window.setTimeout(() => {
          box.center()
        }, 0)
      }
      img.src = b64
    }) */
    /* cut('https://t12.baidu.com/it/u=54104471,2172971201&fm=76', 100, 100, 50, 50).then((b64) => {
      const img = new Image()
      img.onload = () => {
        const box = message.pop().append(img)
        window.setTimeout(() => {
          box.center()
        }, 0)
      }
      img.src = b64
    }) */
    /* rotate('https://t12.baidu.com/it/u=54104471,2172971201&fm=76', 90).then((b64) => {
      const img = new Image()
      img.onload = () => {
        const box = message.pop().append(img)
        window.setTimeout(() => {
          box.center()
        }, 0)
      }
      img.src = b64
    }) */
  },
  methods: {
    fetch () {
      if (/^https?:\/\//.test(this.url)) {
        fetchImg(this.url).then((img) => {
          this.add(img)
        }).catch((e) => {
          console.log('加载图片失败')
        })
      }
    },
    async add (file, index = -1) {
      if (index === -1) {
        if (!/\.(?:png|jpg|jpeg|gif|bmp)$/i.test(file.name)) {
          console.log('非图片')
          return false
        }
        const maxSize = 500 * 1024 // 500kb
        if (file.size > maxSize) {
          console.log(`超出大小: ${this.getSize(file.size - maxSize)}`)
          return false
        }
      }
      const blobSlice = File.prototype.slice || File.prototype.mozSlice || File.prototype.webkitSlice
      const fileReader = new FileReader()
      const spark = new SparkMD5.ArrayBuffer()
      const chunkSize = 2097152
      const chunks = Math.ceil(file.size / chunkSize)
      let currentChunk = 0
      let md5 = ''
      fileReader.onload = (e) => {
        spark.append(e.target.result)
        currentChunk++
        if (currentChunk < chunks) {
          // 继续加载
          loadNext()
        } else {
          // 数据块加载结束
          md5 = spark.end()
          // await edit.open(file)
          // edit.draw()
          /* this.files.push({
            name: file.name,
            size: file.size,
            width: edit.width(),
            height: edit.height(),
            md5
          }) */
          if (index < 0) {
            this.fileList.push({
              name: file.name,
              size: file.size,
              type: file.type,
              result: 'ready',
              md5,
              file
            })
          } else {
            this.fileList[index].size = file.size
            this.fileList[index].md5 = md5
            this.fileList[index].result = 'ready'
            this.fileList[index].file = file
          }
        }
      }
      function loadNext () {
        const start = currentChunk * chunkSize
        const end = start + chunkSize >= file.size ? file.size : start + chunkSize
        fileReader.readAsArrayBuffer(blobSlice.call(file, start, end))
      }
      loadNext()
    },
    open (index) {
      const file = this.fileList[index].file
      this.fileListIndex = index
      edit.open(file)
      this.isShow = true
    },
    close () {
      edit.close()
      this.isShow = false
      this.fileListIndex = -1
    },
    remove (index) {
      this.fileList.splice(index, 1)
    },
    save () {
      if (!edit.img) return
      const file = this.fileList[this.fileListIndex]
      edit.toBlob(file.type || 'image/png').then((blob) => {
        this.add(new File([blob], file.name, {type: file.type, lastModified: Date.now()}), this.fileListIndex)
      })
    },
    rotate (deg) {
      edit.rotate(deg)
    },
    cut () {
      edit.cut()
    },
    resize () {
      edit.resize(this.width, this.height)
    },
    scale (s) {
      edit.scale(s)
    },
    align (p) {
      edit.align(p)
    },
    clean () {
      edit.clean()
    },
    reset () {
      edit.reset()
    },
    change (e, type) {
      if (type === 'range') {
        // 设置选择范围
        const val = e.target.value

        if (/^(?:\d+,){1,3}\d+$/.test(val)) {
          edit.setRange(...val.split(','))
        }
      }
    },
    preview () {
      if (!edit.img) return
      const img = new Image()
      img.onload = () => {
        const box = message.pop().append(img)
        window.setTimeout(() => {
          box.center()
        }, 0)
      }
      img.src = edit.toDataURL()
    },
    upload (index) {
      const fd = new FormData()
      fd.append('image', this.fileList[index].file)
      const xhr = new XMLHttpRequest()
      xhr.onload = (e) => {
        if (e.target.responseText === '1') {
          this.fileList[this.fileListIndex].result = 'success'
        } else if (e.target.responseText === '0') {
          this.fileList[this.fileListIndex].result = 'fail'
        } else {
          this.fileList[this.fileListIndex].result = e.target.responseText
        }
      }
      xhr.open('POST', '//127.0.0.1/server/upload.php')
      xhr.send(fd)
    },
    getSize (size, len = 2) {
      const text = ['B', 'KB', 'MB', 'GB', 'TB']
      let count = 0
      while (size > 1024 && count < text.length - 1) {
        size /= 1024
        ++count
      }
      return `${size.toFixed(len).replace(/\.?0+$/, '')}${text[count]}`
    }
  },
  computed: {
    range () {
      return `${this.state.range.width},${this.state.range.height},${this.state.range.x},${this.state.range.y}`
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss">
@import 'jmessage/style.css';
.img-edit {
  max-width: 900px;
  margin:0 auto;
  input {
    border-top:none;
    border-left:none;
    border-right:none;
    outline: none;
    &[type=file] {
      width:0;
    }
  }
  .info {
    border-bottom:1px dotted #ccc;
    padding:1em 0;
  }
  .btn {
    margin:0 .75em;
  }
}
#canvas {
  border: 1px solid black;
}
#draw_range {
  overflow: hidden;
  transition: .25s height linear;
  &.active {
    border: 1px dotted #eee;
  }
}
.current {
  color:red;
}
.jmessage .message-box.pop-box {
  width:800px;
  .message-box__head {
    font-size: 0;
  }
  .message-box__body {
    text-align: center;
  }
  .message-box__foot {
    display: none;
  }
}
</style>
