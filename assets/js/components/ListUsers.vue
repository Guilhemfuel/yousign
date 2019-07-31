<template>
    <div class="overflow-auto">
        <p class="mt-3">Current Page: {{ currentPage }}</p>

        <b-table
                striped hover
                id="my-table"
                :items="items"
                :fields="fields"
                :per-page="perPage"
                :current-page="currentPage"
                ref="table"
                small
        >
            <template slot="username" slot-scope="row">
                <div class="visible" @click="update(row.item.id, $event)">{{ row.value }}</div>
                <span :id="'form-' + row.item.id" class="invisible">
                    <b-input-group class="mt-3">
                        <b-form-input :placeholder="row.value" :value="row.value"></b-form-input>
                        <b-input-group-append>
                            <b-button variant="outline-success">V</b-button>
                        </b-input-group-append>
                    </b-input-group>
                </span>
            </template>

            <template slot="enabled" slot-scope="row">
                <b-button v-if="row.value" size="sm" variant="success" @click="activationUser(row)">Enable</b-button>
                <b-button v-else size="sm" variant="danger" @click="activationUser(row)">Disable</b-button>
            </template>

            <template slot="action" slot-scope="row">
                <b-button size="sm" class="mr-1" @click="openModalEditUser(row)">Edit</b-button>
                <b-button size="sm" class="mr-1" @click="modalDeleteConfirmation(row)">Delete</b-button>
            </template>
        </b-table>

        <div class="d-flex justify-content-center">
            <b-pagination v-model="currentPage" :total-rows="rows" :per-page="perPage" aria-controls="my-table"></b-pagination>
        </div>

        <add-user :addUser="addUser" :callMakeToast="callMakeToast"></add-user>

        <edit-user ref="editUserComponent" :callMakeToast="callMakeToast"></edit-user>
    </div>
</template>

<script>
  import AddUser from './AddUser.vue'
  import EditUser from './EditUser.vue'

  export default {
    props: {
      users: Array,
      callMakeToast: ''
    },
    components: {
      AddUser,
      EditUser
    },
    data() {
      return {
        listUsers: [],
        perPage: 5,
        currentPage: 1,
        fields: {
          id: {
            key: 'id',
            label: 'ID',
          },
          username: {
            label: 'Username',
            sortable: true
          },
          email: {
            label: 'Email',
            sortable: true
          },
          enabled: {
            label: 'Enabled'
          },
          action: {
            label: 'Action'
          }
        },
        items: this.users,

      }
    },
    methods: {
      modalDeleteConfirmation (row) {
        this.$bvModal.msgBoxConfirm('Please confirm that you want to delete', {
          title: 'Please Confirm',
          size: 'sm',
          buttonSize: 'sm',
          okVariant: 'danger',
          okTitle: 'YES',
          cancelTitle: 'NO',
          footerClass: 'p-2',
          hideHeaderClose: false,
          centered: true
        })
          .then(value => {
            if (value) {
              axios.delete('/admin/user/delete/' + row.item.id)
                .then(response => {
                  this.callMakeToast('primary', 'User deleted !')
                  let indice = this.items.findIndex(item => item.id === row.item.id)
                  this.items.splice(indice, 1)
                })
            }
          })
          .catch(error => {
            this.callMakeToast('danger', error.response.data.message)
          })
      },
      openModalEditUser: function(data) {
        this.$refs.editUserComponent.toggleModal(data)
      },
      activationUser(row, obj) {
        axios.put('/admin/user/activate/' + row.item.id)
          .then(response => {
            //Find position of element in table to refresh the button
            let indice = this.items.findIndex(item => item.id === row.item.id)
            this.items[indice]['enabled'] = !this.items[indice]['enabled']
          })
      },
      addUser(data) {
        this.items.push(data)
      },
      update (id, obj) {
        obj.target.classList.add('invisible')
        console.log(obj.target)
      }
    },
    computed: {
      rows() {
        return this.items.length
      }
    },
    watch: {
      //To prevent items to be empty before API call is done
      users: function () {
        this.items = this.users
      }
    }
  }
</script>