<template>
    <div>
        MDRRRRR :
        <input class="Button" type="submit" value="✓" />
        <timer :timer="time"></timer>
    </div>
</template>

<script>
  import Timer from './Timer.vue'

  export default {
    components: {
      Timer
    },
    el: '#user-edit-app',
    name: 'Submit',
    data () {
      return {
        time: 40
      }
    },
    beforeMount: function() {
      this.time = this.$el.attributes['data-name'].value;
    }
  }
</script>

<style scoped>
    .Button {
        color: #dd9d22;
        background-color: #383838;
        padding: 10px;
        border: none;
        font-size: 14px;
        font-weight: bold;
        vertical-align: bottom;
        cursor: pointer;
    }

    .Button:hover {
        color: white;
    }
</style>
