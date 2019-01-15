<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/css/inputmask.min.css" rel="stylesheet"/>

<style>
  [v-cloak], [v-cloak] > * {
    display: none;
  }

</style>

<div id="app">
  <v-app>
  <v-container>
    <h2 v-cloak>Listado de Errores</h2>
    <v-layout>
      <v-flex>
        <v-dialog v-model="dialog" max-width="500px">
          <v-btn v-cloak slot="activator" color="222222" dark class="mb-2">Crear error</v-btn>
          <v-card>
            <v-card-title>
              <span v-cloak class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-layout wrap>
                    <v-flex xs12>
                      <v-text-field v-model="editedItem.word" :rules="[rules.required]" label="Palabra"></v-text-field>
                    </v-flex>
                    <v-flex xs12>
                      <v-select :items="captioners" label="Digitador" v-model="editedItem.captioner_id"></v-select>
                    </v-flex>
                    <v-flex xs12>
                      <v-menu
                        ref="menu"
                        :close-on-content-click="false"
                        v-model="menu"
                        :nudge-right="40"
                        :return-value.sync="editedItem.error_date"
                        lazy
                        transition="scale-transition"
                        offset-y
                        full-width
                        min-width="290px"
                      >
                        <v-text-field
                          slot="activator"
                          v-model="editedItem.error_date"
                          label="Fecha del error"
                          prepend-icon="event"
                          readonly
                        ></v-text-field>
                        <v-date-picker
                        v-model="editedItem.error_date"
                        max="<?=Date('Y-m-d')?>"
                        no-title
                        scrollable
                        >
                          <v-spacer></v-spacer>
                          <v-btn v-cloak flat color="primary" @click="menu = false">Cancelar</v-btn>
                          <v-btn v-cloak flat color="primary" @click="$refs.menu.save(editedItem.error_date)">OK</v-btn>
                        </v-date-picker>
                      </v-menu>
                    </v-flex>
                </v-layout>
              </v-container>
            </v-card-text>

            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn v-cloak color="blue darken-1" flat @click="close">Cancelar</v-btn>
              <v-btn v-cloak color="blue darken-1" flat @click="save" :disabled="!canSubmit" >Guardar</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
        <v-card>
          <v-data-table
            :headers="headers"
            :items="captioners_errors"
            class="elevation-1"
            :loading="loading"
          >
            <template slot="items" slot-scope="props">
              <td class="text-xs-left">{{ props.item.id }}</td>
              <td class="text-xs-left">{{ props.item.word }}</td>
              <td class="text-xs-left">{{ props.item.fullname }}</td>
              <td class="text-xs-left">{{ props.item.error_date }}</td>
              <td class="text-xs-left">{{ props.item.username }}</td>
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
    captioners_errors: [],
    headers: [
      {
        text: 'ID',
        align: 'left',
        sortable: true,
        value: 'id'
      },
      {text: 'Palabra', value: 'word', sortable: true },
      {text: 'Digitador', value: 'fullname', sortable: true},
      {text: 'Fecha', value:"error_date", sortable: true},
      {text: 'Registrado por', value:"username", sortable: true},
      {text: 'Acciones', value: "actions", sortable: false}
    ],
    loading: false,
    editedItem: {
      id: null,
      word: '',
      captioner_id: '',
      error_date: ''
    },
    defaultItem: {
      id: null,
      word: '',
      captioner_id: '',
      error_date: ''
    },
    captioners: [],
    editedIndex: -1,
    dialog: false,
    menu: false,
    rules: {
      required: value => !!value || 'Este campo es requerido.',
      maxLength: value => value.length < 10 || 'STAPH'
    }
  },
  methods: {
    load() {
      this.loading = true;
      axios.get('read')
      .then(response => {
        this.loading = false;
        this.captioners_errors = response.data.captioners_errors
      })
      .catch(error => {
        this.loading = false;
      })
    },
    editItem(item){
      axios.get('',{
        params: {
          id: item.id
        }
      })
      .then(({
        data: {
          captioner_error: {
            0: result
          }
        }
      }) => {
        this.editedItem = Object.assign({},result)
        this.dialog = true;
        this.editedIndex = result.id;
      })
    },
    deleteItem(item){
      Swal({
        title: '¿Estás seguro?',
        text: "¡El error será eliminado para siempre!",
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
                'El error ha sido eliminado.',
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
            'El error no fue eliminado.',
            'success'
          )
        }
      })
    },
    save(){
      let data = new FormData();
      data.append('error_form',JSON.stringify(this.editedItem));
      if(this.editedItem.id == null)
      {
        axios.post('create',data)
        .then(response => {
          swal('Excelente!','Error creado correctamente','success')
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
          swal('Excelente!','Error actualizado correctamente','success')
          .then(val => {
            this.load();
            this.dialog = false;
          })
        })
        .catch(error => {
          this.load();
        })
      }
    },
    close(){
      this.dialog = false;
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      }, 300)
    },
  },
  created() {
    this.load();
    axios.get('../captioners/read')
    .then(response => {
      let captioners = response.data.captioners;

      let formattedCaptioners = [];

      captioners.forEach((item,index) => {
        formattedCaptioners.push({
          text: `${item.name} ${item.lastname.length > 0 ? item.lastname : ''} - ${item.rut}`,
          value: item.id
        })
      })

      this.captioners = formattedCaptioners;
    })
  },
  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'Nuevo error' : 'Editar error'
    },
    canSubmit() {
      return true;
    }
  },
  watch: {
    dialog (val) {
      val || this.close()
    }
  }
});


</script>