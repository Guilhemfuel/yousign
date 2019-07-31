<template>
    <div>
        <b-modal
                id="modal-prevent-closing"
                ref="modal"
                title="Choose a password"
                @show="resetModal"
                @hidden="resetModal"
                @ok="handleOk"
                v-model="modalShow"
        >
            <form ref="form" @submit.stop.prevent="handleSubmit">
                <b-form-group
                        :state="passwordState"
                        label="Password"
                        label-for="password-input"
                        invalid-feedback="Password is required"
                >
                    <b-form-input
                            id="password-input"
                            type="password"
                            v-model="password"
                            :state="passwordState"
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
        password: '',
        passwordState: null,
        param: false,
        modalShow: false
      }
    },
    props: {
      callMakeToast: '',
      success: ''
    },
    methods: {
      checkFormValidity() {
        const valid = this.$refs.form.checkValidity()
        this.passwordState = valid ? 'valid' : 'invalid'
        return valid
      },
      resetModal() {
        this.password = ''
        this.passwordState = null
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

        axios.put('/api/activate/' + this.param, {'password': this.password})
          .then(response => {

            this.callMakeToast('primary', 'Password set ! You can log now !')
            // Display Login screen instead of Register screen
            this.success()

            // Hide the modal manually
            this.$nextTick(() => {
              this.$refs.modal.hide()
            })
          })
          .catch(error => {
            this.callMakeToast('danger', error.response.data.message)
          })
      }
    },
    mounted () {
      this.param = location.search.split('token=')[1]
      if (this.param) {
        this.modalShow = !this.modalShow
      }

      this.param = location.search.split('token_nopwd=')[1]
      if (this.param) {
        axios.put('/api/activate/' + this.param, {'password': ''})
          .then(response => {

            this.callMakeToast('primary', 'Your account is activate, you can log in now !')
            // Display Login screen instead of Register screen
            this.success()

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