<template>
    <div class="d-flex justify-content-center">
        <b-form inline @submit="onSubmit">
            <label class="sr-only" for="inline-form-input-username">Username</label>
            <b-input
                    id="inline-form-input-username"
                    class="mb-2 mr-sm-2 mb-sm-0"
                    placeholder="Username"
                    v-model="form.username"
                    required
            ></b-input>

            <label class="sr-only" for="inline-form-input-username">Email</label>
            <b-input-group prepend="@" class="mb-2 mr-sm-2 mb-sm-0">
                <b-input id="inline-form-input-email" type="email" placeholder="Email" v-model="form.email"
                         required></b-input>
            </b-input-group>

            <b-button type="submit" variant="primary" :disabled="loading">
                <span v-if="loading">
                    <b-spinner small type="grow"></b-spinner>
                Loading...
                </span>
                <span v-else>
                    Add
                </span>
            </b-button>
        </b-form>
    </div>
</template>

<script>
  export default {
    data() {
      return {
        form: {
          username: '',
          email: ''
        },
        loading: false
      }
    },
    props: {
      callMakeToast: '',
      addUser: ''
    },
    methods: {
      onSubmit(evt) {
        evt.preventDefault()
        this.loading = true
        let user = JSON.stringify(this.form)
        axios.post('/api/register', user)
          .then(response => {
            this.callMakeToast('primary', 'User added !')
            this.addUser(response.data.user)
            this.form.username = ''
            this.form.email = ''
          })
          .catch((error) => {
            this.callMakeToast('danger', error.response.data.message)
          })
          .finally(() => {
            this.loading = false
          })
      }
    }
  }
</script>

<style scoped>
</style>
