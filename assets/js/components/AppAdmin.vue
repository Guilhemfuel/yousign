<template>
    <div id="app" class="container h-100 mx-4">
        <div class="py-4">
            <div class="d-flex justify-content-end">
                <b-button variant="danger" href="/logout">Logout</b-button>
            </div>
            <div class="text-center d-flex align-items-center flex-column" v-if="loader">
                <b-spinner
                        variant="dark"
                        key="dark"
                ></b-spinner>
            </div>
            <list-users :users="users" :callMakeToast="makeToast" v-else></list-users>
        </div>
    </div>
</template>

<script>
  import ListUsers from './ListUsers.vue'

  export default {
    name: 'AppAdmin',
    components: {
      ListUsers
    },
    data () {
      return {
        users: [],
        loader: false
      }
    },
    methods: {
      getUsers () {
        this.loader= true
        axios.get('/admin/users')
          .then((response) => {
            // handle success
            console.log(response.data);
            this.users = response.data
          })
          .catch((error) => {
            console.log(error);
          })
          .finally(() => {
            this.loader = false
          });
      },
      makeToast(variant = null, message) {
        this.$bvToast.toast(message, {
          title: 'Notification',
          variant: variant,
          toaster: 'b-toaster-top-right',
          solid: true
        })
      },
    },
    created () {
      this.getUsers()
    }
  }
</script>

<style scoped>
</style>