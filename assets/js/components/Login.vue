<template>
    <div>
        <b-form @submit="onSubmit" @reset="onReset" v-if="show">
            <b-form-group id="input-group-4" label="Email address:" label-for="input-4">
                <b-form-input id="input-4" v-model="form.email" type="email" required placeholder="Enter email"></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-5" label="Password:" label-for="input-5">
                <b-form-input id="input-5" v-model="form.password" required type="password" placeholder="Enter password"></b-form-input>
            </b-form-group>

            <b-button type="submit" variant="primary" :disabled="loading">
                <span v-if="loading">
                    <b-spinner small type="grow"></b-spinner>
                Loading...
                </span>
                <span v-else>
                    Login
                </span>
            </b-button>
        </b-form>
    </div>
</template>

<script>
  export default {
    name: 'Login',
    data() {
      return {
        form: {
          email: '',
          password: '',
        },
        show: true,
        loading: false
      }
    },
    props: {
      callMakeToast: ''
    },
    methods: {
      onSubmit(evt) {
        evt.preventDefault()
        this.loading = true
        let user = JSON.stringify(this.form)
        axios.post('/api/login', user)
          .then((response) => {
            console.log(response)
            this.callMakeToast('primary', 'Login successful !')
            window.location.href = 'admin'
          })
          .catch((error) => {
            console.log(error)
            this.callMakeToast('danger', error.response.data.message)
          })
          .finally(() => {
            this.loading = false
          })
      },
      onReset(evt) {
        evt.preventDefault()
        // Reset our form values
        this.form.email = ''
        this.form.password = ''
        // Trick to reset/clear native browser form validation state
        this.show = false
        this.$nextTick(() => {
          this.show = true
        })
      }
    }
  }
</script>