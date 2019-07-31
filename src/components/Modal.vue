<template>
  <div class="modal fade" tabindex="-1" role="dialog" :style="{'display': isShow ? 'block' : 'none', 'opacity': opacity}">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
      <div class="modal-content">
        <!--div class="modal-header">
          <h5 class="modal-title">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="$emit('close')">
            <span aria-hidden="true">&times;</span>
          </button>
        </div-->
        <div class="modal-body">
          <slot></slot>
        </div>
        <div class="modal-footer">
          <!--button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button-->
          <button type="button" class="btn btn-primary" @click="$emit('close')">关闭</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'modal',
  props: ['isShow'],
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
