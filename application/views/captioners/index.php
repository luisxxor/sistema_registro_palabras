<link href="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/css/inputmask.min.css" rel="stylesheet"/>

<style>
  [v-cloak], [v-cloak] > * {
    display: none;
  }

</style>

<div id="app">
  <v-app>
  <v-container>
    <h2 v-cloak>Listado de Digitadores</h2>
    <v-layout>
      <v-flex>
        <v-dialog v-model="dialog" max-width="500px">
          <v-btn v-cloak slot="activator" color="222222" dark class="mb-2">Crear digitador</v-btn>
          <v-card>
            <v-card-title>
              <span v-cloak class="headline">{{ formTitle }}</span>
            </v-card-title>

            <v-card-text>
              <v-container>
                <v-layout wrap>
                <v-form ref="form" style="display: contents">
                  <v-flex xs12>
                    <v-text-field v-model="editedItem.name" :rules="[rules.required]" label="Nombre"></v-text-field>
                  </v-flex>
                  <v-flex xs12>
                    <v-text-field v-model="editedItem.lastname" label="Apellido"></v-text-field>
                  </v-flex>
                  <v-flex xs12>
                    <v-text-field @input="handleRut" :error-messages="rutErrors" :rules="[rules.required]" maxlength="12" v-model="editedItem.rut" label="RUT"></v-text-field>
                  </v-flex>
                </v-form>
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
            :items="captioners"
            class="elevation-1"
            :loading="loading"
          >
            <template slot="items" slot-scope="props">
              <td class="text-xs-left">{{ props.item.id }}</td>
              <td class="text-xs-left">{{ props.item.name }}</td>
              <td class="text-xs-left">{{ props.item.lastname }}</td>
              <td class="text-xs-left">{{ props.item.rut }}</td>
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
    captioners: [],
    headers: [
      {
        text: 'ID',
        align: 'left',
        sortable: true,
        value: 'id'
      },
      {text: 'Nombres', value: 'name', sortable: true},
      {text: 'Apellidos', value: 'lastname', sortable: true },
      {text: 'RUT', value:"rut", sortable: false},
      {text: 'Acciones', value: "actions", sortable: false}
    ],
    loading: false,
    editedItem: {
      id: null,
      name: '',
      lastname: '',
      rut: ''
    },
    defaultItem: {
      id: null,
      name: '',
      lastname: '',
      rut: ''
    },
    editedIndex: -1,
    dialog: false,
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
        this.captioners = response.data.captioners
      })
      .catch(error => {
        this.loading = false;
        console.log(error)
      })
    },
    editItem(item){
      this.dialog = true;
      this.editedIndex = this.captioners.indexOf(item);
      this.editedItem = Object.assign({},item);
    },
    deleteItem(item){
      Swal({
        title: '¿Estás seguro?',
        text: "¡El digitador será eliminado para siempre! ¡y con él todos los errores relacionados!",
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
                'El digitador ha sido eliminado.',
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
            'El digitador no fue eliminado.',
            'success'
          )
        }
      })
    },
    save(){
      let data = new FormData();
      data.append('captioner_form',JSON.stringify(this.editedItem));
      if(this.$refs.form.validate())
      {
        if(this.editedItem.id == null)
        {
          axios.post('create',data)
          .then(response => {
            swal('Excelente!','Captioner creado correctamente','success')
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
            swal('Excelente!','Captioner actualizado correctamente','success')
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
    },
    close(){
      this.dialog = false;
      this.$refs.form.reset()
      setTimeout(() => {
        this.editedItem = Object.assign({}, this.defaultItem)
        this.editedIndex = -1
      }, 300)
    },
    cleanRut(rut) {
      return rut.replace(/[^0-9kK]+/g,'').toLowerCase();
    },
    formatRut(rut) {
      if (rut.length > 1) {
        rut = rut.slice(0,-1)+'-'+rut.slice(-1);
      if (rut.length > 5) {
        if (rut.length > 8) {
            return rut.slice(0,-8)+'.'+rut.slice(-8,-5) +'.'+rut.slice(-5);
          }
        return rut.slice(0,-5) +'.'+rut.slice(-5);
        }
      }
      return rut
    },
    validateRut(rut) {
      let pattern = /^0*(\d{1,3}(\.?\d{3})*)\-?([\dkK])$/
      if (pattern.test(rut)) {
        let numberRut = this.cleanRut(rut).slice(0, -1);
        let auxArray = [3, 2, 7, 6, 5, 4, 3, 2];
        let sum = 0;

        for (let i = numberRut.length - 1; i >= 0; i--) {
          sum += parseInt(numberRut[i]) * auxArray[i];
        }
        switch (11 - sum % 11) {
          case 11:
            return rut.slice(-1) == 0;
          case 10:
            return rut.slice(-1) == 'k';
          default:
            return rut.slice(-1) == 11 - sum % 11;
        }
      }
      return false;
    },
    handleRut() {
      this.editedItem.rut = this.formatRut(this.cleanRut(this.editedItem.rut));
    }

  },
  created() {
    this.load();
  },
  computed: {
    formTitle () {
      return this.editedIndex === -1 ? 'Nuevo digitador' : 'Editar digitador'
    },
    rutErrors() {
      const errors = [];
      if(this.editedItem.rut.length > 0)
      {
        if(!this.validateRut(this.editedItem.rut) || this.editedItem.rut.length < 8)
        {
          errors.push('Este RUT no es válido')
        }
      }
      
      return errors;
    },
    canSubmit() {
      return this.editedItem.name.length > 0 && this.editedItem.rut.length > 0 && this.rutErrors.length == 0;
    }
  },
  watch: {
    dialog (val) {
      val || this.close()
    }
  },
  filters: {
    rut: val => this.formatRut(this.cleanRut(value))
  }
});


</script>