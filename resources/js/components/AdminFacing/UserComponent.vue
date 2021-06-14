<template>
  <v-app style="min-height: 0">
    <v-card elevation="2">
        <v-card-title> USERS </v-card-title>
        <v-card-subtitle> Create, Read, Update, Delete </v-card-subtitle>
        <v-card-text>
          <v-btn class="btn-block" color="success" @click="openDialog">NEW USER</v-btn>
          <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line hide-details></v-text-field>
        </v-card-text>
        <v-card-text>
            <v-data-table :headers="headers" :items="users" :search="search" :items-per-page="5" @click:row="openDialog">
              <template v-slot:[`item.actions`]="{ item }">
                <v-icon small @click="deleteItem(item.id)"> mdi-delete </v-icon>
              </template>
            </v-data-table>
        </v-card-text>
    </v-card>
    <v-dialog v-model="dialog" persistent max-width="50%" transition="dialog-bottom-transition">
      <v-card>
        <v-toolbar dark color="orange">
          <v-btn icon dark @click="dialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
          <v-toolbar-title>{{rowSelected.id ? 'EDIT USER' : 'CREATE USER'}}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-toolbar-items>
            <v-btn dark text @click="dialog = false"> CANCEL </v-btn>
            <v-btn dark text @click="saveItem()"> SAVE </v-btn>
          </v-toolbar-items>
        </v-toolbar>
        <v-card-text class="mt-5">
          <v-container>
            <v-row>
              <v-col cols="12" md="6" class="py-0">
                <v-text-field outlined dense label="Name" v-model="rowSelected.name" :rules="[rules.required]"></v-text-field>
              </v-col>
              <v-col cols="12" md="6" class="py-0">
                <v-text-field outlined dense label="Email" v-model="rowSelected.email" hint="user@email.com" :rules="[rules.required, rules.email]"></v-text-field>
              </v-col>
              <v-col cols="12" md="6" class="py-0">
                <v-text-field outlined dense label="Password" v-model="rowSelected.password" type="password" :rules="[rules.required, rules.passwordLength, rules.passwordSecurity]"></v-text-field>
              </v-col>
            </v-row>
          </v-container>
        </v-card-text>
      </v-card>
    </v-dialog>
  </v-app>
</template>

<script>
    export default {
      mounted() {
          console.log('Component user mounted.');
      },
      created(){
        this.loadData();
      },
      data: () => ({
        users: [],
        search: '',
        headers: [
          { text: 'ID', align: 'center', value: 'id', sortable: false },
          { text: 'NAME', align: 'center', value: 'name' },
          { text: 'EMAIL', align: 'center', value: 'email' },
          { text: '', align: 'center', value: 'actions', sortable: false },
        ],
        dialog: false,
        rowSelected: [],
        rules: {
          required: value => !!value || 'Required.',
          counter: value => value.length <= 20 || 'Max 20 characters',
          email: value => {
              const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
              return pattern.test(value) || 'Invalid e-mail.'
          },
          passwordLength: value => (value && value.length >= 8) || 'Minimum password length of 8 characters',
          passwordSecurity: value => /(?=.*\d)/.test(value) || 'Password must contain at least one number', 
        },
        forgot: false,
      }),
      methods: {
        loadData() {
          axios.get('/api/users').then(res=>{
              this.users = res.data;
              console.log(this.users);
          });
        },
        openDialog(object) {
          this.dialog = true;
          console.log(object);
          this.rowSelected = object;
        },
        saveItem() {
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
          })

          if(this.rowSelected.id) {
            axios.put('/api/users/' + this.rowSelected.id, this.rowSelected).then(res=>{
              console.log(res);
              Toast.fire({
                  icon: 'success',
                  title: 'Successful edit',
              });
              this.dialog = false;
              this.loadData();
            }).catch(function (error) {
              Toast.fire({
                  icon: 'error',
                  title: 'An error has occurred, please try again'
              });
            });
          } else {
            axios.post('/api/users', this.rowSelected).then(res=>{
              console.log(res);
              Toast.fire({
                  icon: 'success',
                  title: 'Added successfully',
              });
              this.dialog = false;
              this.loadData();
            }).catch(function (error) {
              Toast.fire({
                  icon: 'error',
                  title: 'An error has occurred, please try again'
              });
            });
          }
        },
        deleteItem(id) {
          const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
          });

          axios.delete('/api/users/' + id).then(res=>{
            console.log(res);
            Toast.fire({
                icon: 'success',
                title: 'Successful removed',
            });
            this.dialog = false;
            this.loadData();
          }).catch(function (error) {
            Toast.fire({
                icon: 'error',
                title: 'An error has occurred, please try again'
            });
          });
        }
      },
    }
</script>
<style>
  .v-application--wrap {
    min-height: 0;
  }
  .v-label, .v-input--checkbox {
    margin: 0;
  }
</style>