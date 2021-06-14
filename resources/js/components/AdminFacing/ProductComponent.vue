<template>
  <v-app style="min-height: 0">
    <v-card elevation="2">
        <v-card-title> PRODUCTS </v-card-title>
        <v-card-subtitle> Create, Read, Update, Delete </v-card-subtitle>
        <v-card-text>
          <v-btn class="btn-block" color="success" @click="openDialog">NEW PRODUCT</v-btn>
          <v-text-field v-model="search" append-icon="mdi-magnify" label="Search" single-line hide-details></v-text-field>
        </v-card-text>
        <v-card-text>
            <v-data-table :headers="headers" :items="products" :search="search" :items-per-page="5" @click:row="openDialog">
              <template v-slot:[`item.picture`]="{ item }">
                <img :src="item.picture" :alt="item.name" width="100vw">
              </template>
              <template v-slot:[`item.status`]="{ item }">
                <v-chip :color="item.status == 1 ? 'success' : 'orange'" dark> {{ item.status == 1 ? 'Activo' : 'Baja' }} </v-chip>
              </template>
              <template v-slot:[`item.actions`]="{ item }">
                <v-icon small @click="deleteItem(item.id)" v-if="item.status == 1"> mdi-delete </v-icon>
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
          <v-toolbar-title>{{rowSelected.id ? 'EDIT PRODUCT' : 'CREATE PRODUCT'}}</v-toolbar-title>
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
                <v-text-field outlined dense label="Slug" v-model="rowSelected.slug" :rules="[rules.required]" @keydown.space.prevent></v-text-field>
              </v-col>
              <v-col cols="12" md="8" class="py-0">
                <v-textarea outlined dense label="Description" v-model="rowSelected.description" :rules="[rules.required]"></v-textarea>
              </v-col>
              <v-col cols="12" md="4" class="py-0">
                <v-text-field outlined dense label="Price" v-model="rowSelected.price" :rules="[rules.required]" @keypress="isNumber($event)" prefix="$"></v-text-field>
              </v-col>
              <v-col cols="12" md="12" class="py-0">
                <v-img :src="previewImage" class="uploading-image" width="100%" />
                <input type="file" accept="image/*" @change="getImage">
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
          console.log('Component product mounted.');
      },
      created(){
        this.loadData();
      },
      data: () => ({
        products: [],
        search: '',
        headers: [
          { text: 'ID', align: 'center', value: 'id', sortable: false },
          { text: 'PICTURE', align: 'center', value: 'picture' },
          { text: 'NAME', align: 'center', value: 'name' },
          { text: 'PRICE', align: 'center', value: 'price' },
          { text: 'SLUG', align: 'center', value: 'slug' },
          { text: 'STATUS', align: 'center', value: 'status' },
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
        previewImage:null
      }),
      methods: {
        loadData() {
          axios.get('/api/products').then(res=>{
            this.products = res.data;
            console.log(this.products);
          });
        },
        openDialog(object) {
          this.dialog = true;
          console.log(object);
          this.rowSelected = object;
          this.previewImage = this.rowSelected.picture;
        },
        saveItem() {
          this.rowSelected.picture = this.previewImage;
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
            axios.put('/api/products/' + this.rowSelected.id, this.rowSelected).then(res=>{
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
            axios.post('/api/products', this.rowSelected).then(res=>{
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
          this.dialog = false;
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

          Swal.fire({
              title: 'Do you want delete product?',
              showCancelButton: true,
              confirmButtonText: 'Accept',
              confirmButtonColor: 'orange',
              allowOutsideClick: false
          }).then((result) => {
            if (result.isConfirmed) {
              axios.delete('/api/products/' + id).then(res=>{
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
            } else if (result.isDenied) {
              Swal.fire('Canceled order', '', 'info')
            }
          });
        },
        isNumber: function(evt) {
          evt = (evt) ? evt : window.event;
          var charCode = (evt.which) ? evt.which : evt.keyCode;
          if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
              evt.preventDefault();
          } else {
              return true;
          }
        },
        getImage(e){
          const image = e.target.files[0];
          const reader = new FileReader();
          reader.readAsDataURL(image);
          reader.onload = e =>{
              this.previewImage = e.target.result;
              console.log(this.previewImage);
          };
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