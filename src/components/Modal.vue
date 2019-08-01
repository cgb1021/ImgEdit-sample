<template>
  <div class="modal fade" tabindex="-1" role="dialog" :style="{'display': isShow ? 'block' : 'none', 'opacity': opacity}">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header" v-if="title">
          <h5 class="modal-title">{{title}}</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="$emit('close', 0)">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <slot></slot>
        </div>
        <div class="modal-footer" v-if="buttons.length">
          <button type="button" class="btn" data-dismiss="modal" :class="{'btn-secondary': index < buttons.length - 1, 'btn-primary': index === buttons.length - 1}" v-for="(text, index) in buttons" :key="index" @click="$emit('close', index + 1)">{{text}}</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'modal',
  props: ['title', 'isShow', 'buttons'],
  data () {
    return {
      opacity: 0
    }
  },
  watch: {
    isShow (val) {
      if (val) {
        window.setTimeout(() => {
          this.opacity = 1
        }, 0)
      } else {
        this.opacity = 0
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>
</style>
