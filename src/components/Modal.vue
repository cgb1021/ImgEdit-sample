<template>
  <div class="modal" tabindex="-1" role="dialog" :style="{'display': isShow ? 'block' : 'none'}">
    <div class="modal-dialog" role="document">
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
  props: ['isShow']
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style lang="scss" scoped>

</style>
