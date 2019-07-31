<template>
    <div>
        <b-modal
                id="toggle-btn"
                ref="modal"
                title="Edit User"
                v-model="modalShow"
                @show="resetModal"
                @hidden="resetModal"
                @ok="handleOk"
        >
            <form ref="form" @submit.stop.prevent="handleSubmit">
                <b-form-group
                        :state="usernameState"
                        label="Username"
                        label-for="username"
                        invalid-feedback="Username is required"
                >
                    <b-form-input
                            id="username"
                            v-model="form.username"
                            :state="usernameState"
                            required
                    ></b-form-input>
                </b-form-group>
                <b-form-group
                        :state="emailState"
                        label="Email"
                        label-for="email"
                        invalid-feedback="Email is required"
                >
                    <b-form-input
                            id="email"
                            type="email"
                            v-model="form.email"
                            :state="emailState"
                            required
                    ></b-form-input>
                </b-form-group>
            </form>
        </b-modal>
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
        usernameState: null,
        emailState: null,
        submittedNames: [],
        modalShow: false,
        user: false,
        username: '',
        email: ''
      }
    },
    props: {
      callMakeToast: ''
    },
    methods: {
      toggleModal(data) {
        this.$refs['modal'].toggle('#toggle-btn')
        console.log(data.item.id)
        this.form.username = data.item.username
        this.form.email = data.item.email
        this.user = data
      },
      checkFormValidity() {
        const valid = this.$refs.form.checkValidity()
        this.usernameState = valid ? 'valid' : 'invalid'
        this.emailState = valid ? 'valid' : 'invalid'
        return valid
      },
      resetModal() {
        this.form.username = ''
        this.form.email = ''
        this.usernameState = null
        this.emailState = null
      },
      handleOk(bvModalEvt) {
        // Prevent modal from closing
        bvModalEvt.preventDefault()
        // Trigger submit handler
        this.handleSubmit()
      },
      handleSubmit() {
        // Exit when the form isn't valid
        if (!this.checkFormValidity()) {
          return
        }

        let userform = this.form

        this.username = userform.username
        this.email = userform.email

        axios.put('/admin/user/edit/' + this.user.item.id, JSON.stringify(userform))
          .then(response => {

            this.user.item.username = this.username
            this.user.item.email = this.email

            this.submittedNames.push(this.username)
            this.submittedNames.push(this.email)

            // Hide the modal manually
            this.$nextTick(() => {
              this.$refs.modal.hide()
            })
          })
          .catch(error => {
            this.callMakeToast('danger', error.response.data.message)
          })
      }
    }
  }
</script>