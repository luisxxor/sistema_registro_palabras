<style>
  [v-cloak], [v-cloak] > * {
    display: none;
  }

</style>

<div id="app">
  <v-app>
  <v-container>
    <h2 v-cloak>Listado de Usuarios</h2>
    <v-layout>
      <v-flex>
        <v-dialog v-model="dialog" max-width="500px">
          <v-btn v-cloak slot="activator" color="222222" dark class="mb-2">Crear usuario</v-btn>
          <v-card>
            <v-card-title>
              <span v-cloak class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-layout wrap>
                  <v-flex xs12>
                    <v-text-field v-model="editedItem.username" :rules="[rules.required]" label="Nombre de usuario"></v-text-field>
                  </v-flex>
                  <v-flex xs12>
                    <v-text-field :rules="editedItem.id == null ? [rules.required] : []" :hint="editedItem.id == null ? '' : 'Si dejas este campo vacío, la contraseña no cambiará'" :type="see_password ? 'text' : 'password'" v-model="editedItem.password" label="Contraseña" :append-icon="see_password ? 'visibility_off' : 'visibility'" @click:append="()=>{ see_password = !see_password }"></v-text-field>
                  </v-flex>
                  <v-flex xs12>
                    <v-switch v-model="editedItem.is_admin" label="¿Es admin?"></v-switch>
                  </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn v-cloak color="blue darken-1" flat @click="close">Cancelar</v-btn>
              <v-btn v-cloak color="blue darken-1" flat @click="save" :disabled="(editedItem.username.trim().length == 0 ) || (editedItem.id == null && editedItem.password.trim().length == 0) ">Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-card>
          <v-data-table
            :headers="headers"
            :items="users"
            class="elevation-1"
            :loading="loading"
            no-data-text="No hay registros"
            rows-per-page-text="Elementos por página"
          >
            <template slot="items" slot-scope="props">
              <td class="text-xs-left">{{ props.item.id }}</td>
              <td class="text-xs-left">{{ props.item.username }}</td>
              <td class="text-xs-left">{{ props.item.is_admin | humanizeBoolean }}</td>
              <td class="justify-start layout">
                <v-tooltip left>
                  <v-icon
                    slot="activator"
                    small
                    class="mr-2"
                    @click="editItem(props.item)"
                    style="height: 100%; margin: auto 0;"
                  >
                    edit
                  </v-icon>
                  Editar
                </v-tooltip>
                <v-tooltip right>
                  <v-icon
                    slot="activator"
                    small
                    @click="deleteItem(props.item)"
                    style="height: 100%; margin: auto 0;"
                  >
                    delete
                  </v-icon>
                  Eliminar
                </v-tooltip>
              </td>
            </template>
            <template slot="pageText" slot-scope="props">
              Mostrando elementos: {{ props.pageStart }} al {{ props.pageStop }} de {{ props.itemsLength }}
            </template>
          </v-data-table>
        </v-card>
      </v-flex>
    </v-layout>
  </v-container>
  </v-app>
</div>

<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vuetify/dist/vuetify.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script>
new Vue({
  el: '#app',
  data: {
    users: [],
    headers: [
      {
        text: 'ID',
        align: 'left',
        sortable: true,
        value: 'id'
      },
      {text: 'Nombre de Usuario', value: 'username'},
      {text: '¿Es admin?', value: 'is_admin', sortable: false},
      {text: 'Acciones', value:"name", sortable: false}
    ],
    loading: false,
    see_password: false,
    editedItem: {
      id: null,
      username: '',
      password: '',
      is_admin: false
    },
    defaultItem: {
      id: null,
      username: '',
      password: '',
      is_admin: false
    },
    editedIndex: -1,
    dialog: false,
    rules: {
      required: value => !!value || 'Este campo es requerido.',
    }
  },
  filters: {
    humanizeBoolean(val) {
      return parseInt(val) ? 'Si' : 'No';
    }
  },
  methods: {
    load() {
      this.loading = true;
      axios.get('getUsers')
      .then(response => {
        this.loading = false;
        this.users = response.data.users
      })
      .catch(error => {
        this.loading = false;
        console.log(error)
      })
    },
    editItem(item){
      this.dialog = true;
      this.editedIndex = this.users.indexOf(item);
      this.editedItem = Object.assign({},item);
      this.editedItem.is_admin = parseInt(this.editedItem.is_admin);
    },
    deleteItem(item){
      Swal({
        title: '¿Estás seguro?',
        text: "¡El usuario será eliminado para siempre! ¡y con él todos los errores que registró!",
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: '¡Si! ¡eliminar!',
        cancelButtonText: '¡No! ¡cancelar!',
        reverseButtons: true
      }).then((result) => {
        if (result.value) {
          let data = new FormData();
          data.append('id',item.id);
          axios.post('delete',data)
          .then(response => {
            if(response)
            {
              Swal(
                '¡Eliminado!',
                'El usuario ha sido eliminado.',
                'success'
              ).then(response => {
                this.load();
              })
            }
            else
            {
              Swal(
                'Error',
                'Ha ocurrido un error.',
                'warning'
              ).then(response => {
                this.load();
              })
            }
          })
        } else if (
          result.dismiss === Swal.DismissReason.cancel
        ) {
          Swal(
            'Cancelado',
            'El usuario no fue eliminado.',
            'success'
          )
        }
      })
    },
    save(){
      let verifyUsername = new FormData();
      verifyUsername.append('usernameTest',JSON.stringify({id: this.editedItem.id, username: this.editedItem.username}))
      axios.post('usernameIsAvailable',verifyUsername)
      .then(isAvailable =>{
        if(isAvailable.data.response)
        {
          let data = new FormData();
          data.append('user_form',JSON.stringify(this.editedItem));
          if(this.editedItem.id == null)
          {
            axios.post('create',data)
            .then(response => {
              swal('Excelente!','Usuario creado correctamente','success')
              .then(val => {
                this.load();
                this.dialog = false;
              })
            })
            .catch(error => {
              this.load();
            })
          }
          else
          {
            axios.post('update',data)
            .then(response => {
              swal('Excelente!','Usuario actualizado correctamente','success')
              .then(val => {
                this.load();
                this.dialog = false;
              })
            })
            .catch(error => {
              this.load();
            })
          }
        }
        else
        {
          swal('Lo sentimos','ese nombre de usuario ya está en uso','error');
        }
      })
    },
    close(){
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      }, 300)
    }
  },
  created() {
    this.load();
  },
  computed: {
    formTitle () {
        return this.editedIndex === -1 ? 'Nuevo usuario' : 'Editar usuario'
      }
  },
  watch: {
    dialog (val) {
      val || this.close()
    }
  },
});


</script>