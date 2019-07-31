<template>
    <div id="App" class="container h-100">
        <div class="row h-100 justify-content-center align-items-center">
            <b-container class="bv-example-row">

                <b-card no-body sm="6">
                    <b-tabs pills card>
                        <b-tab title="Register" active>
                            <b-card-text>
                                <register :callMakeToast="makeToast"></register>
                            </b-card-text>
                        </b-tab>
                        <b-tab title="Login" v-bind="{ active: active }">
                            <b-card-text>
                                <login :callMakeToast="makeToast"></login>
                            </b-card-text>
                        </b-tab>
                    </b-tabs>
                </b-card>
            </b-container>

            <activation :callMakeToast="makeToast" :success="success"></activation>

        </div>
    </div>
</template>

<script>
  import Register from './Register.vue'
  import Login from './Login.vue'
  import Activation from './Activation.vue'

  export default {
    name: 'App',
    components: {
      Register,
      Login,
      Activation
    },
    data () {
      return {
        active: false
      }
    },
    methods: {
      makeToast(variant = null, message) {
        this.$bvToast.toast(message, {
          title: 'Notification',
          variant: variant,
          toaster: 'b-toaster-top-right',
          solid: true
        })
      },
      success () {
        this.active = true
      }
    }
  }
</script>

<style scoped>
</style>