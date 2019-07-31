<template>
    <div>
        <b-form @submit="onSubmit" @reset="onReset" v-if="show">
            <b-form-group id="input-group-1" label="Your Name:" label-for="input-1">
                <b-form-input id="input-1" v-model="form.username" required placeholder="Enter name"></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-2" label="Email address:" label-for="input-2" description="We'll never share your email with anyone else. loooool">
                <b-form-input id="input-2" v-model="form.email" type="email" required placeholder="Enter email"></b-form-input>
            </b-form-group>

            <b-form-group id="input-group-3" label="Password:" label-for="input-3">
                <b-form-input id="input-3" v-model="form.password" required type="password" placeholder="Enter password"></b-form-input>
            </b-form-group>

            <b-button type="submit" variant="primary" :disabled="loading">
                <span v-if="loading">
                    <b-spinner small type="grow"></b-spinner>
                Loading...
                </span>
                <span v-else>
                    Submit
                </span>
            </b-button>
            <b-button type="reset" variant="danger">Reset</b-button>
        </b-form>
    </div>
</template>

<script>
  export default {
    name: 'Register',
    data() {
      return {
        form: {
          username: '',
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
        let user = JSON.stringify(this.form)
        this.loading = true
        axios.post('/api/register', user)
          .then((response) => {
            console.log(response)
            this.callMakeToast('primary', 'We sent you an email to activate your account !')
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
        this.form.username = ''
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