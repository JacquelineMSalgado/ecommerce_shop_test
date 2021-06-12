<template>
    <v-app>
        <menu-bar-component @onChange="getCartContent" ref="childRef" :data="moduleData"></menu-bar-component>
        <v-main id="home">
            <v-container fluid id="products">
                <!-- Stack the columns on mobile by making one full-width and the other half-width -->
                <v-row>
                    <v-col cols="12" md="8">
                        <v-card class="pa-2" tile>
                            <v-card-text class="text-center black--text font-weight-black"> ITEMS IN YOUR CART </v-card-text>
                            <v-divider class="ma-2"></v-divider>
                            <v-data-table :headers="headers" :items="cartObjectJSON" hide-default-footer class="elevation-1 mb-5">
                                <template v-slot:[`item.picture`]="{ item }">
                                    <v-list-item>
                                        <img :src="(item.picture.includes('.png') ? '/storage/images/' : 'data:image/png;base64,') + item.picture" :alt="item.picture" width="80vw">
                                    </v-list-item>
                                </template>
                                <template v-slot:[`item.actions`]="{ item }">
                                    <v-icon @click="addCart(item.id)"> mdi-plus </v-icon>
                                    <v-icon @click="deleteItemCart(item.id)"> mdi-delete </v-icon>
                                </template>
                                <template v-slot:[`item.price`]="{ item }">
                                    $ {{ item.price }} USD
                                </template>
                                <template v-slot:[`item.total`]="{ item }">
                                    $ {{ item.total }} USD
                                </template>
                                <template v-slot:no-data>
                                    You don't have products added
                                </template>
                            </v-data-table>
                            <v-btn block color="orange white--text" :disabled="enablePayment" href="/"> CONTINUE SHOPPING </v-btn>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="4">
                        <v-card class="pa-2" tile>
                            <v-card-text class="text-center black--text font-weight-black"> ORDER SUMMARY </v-card-text>
                            <v-divider class="ma-2"></v-divider>
                            <v-row>
                                <v-col cols="6" md="6">
                                    <v-card-text class="pb-0 pt-0"> TOTAL QUANTITY:</v-card-text>
                                </v-col>
                                <v-col cols="6" md="6" class="text-right">
                                    <v-card-text class="pb-0 pt-0">$ {{this.order.total}} USD</v-card-text>
                                </v-col>
                            </v-row>
                            <v-row class="mb-3">
                                <v-col cols="6" md="6">
                                    <v-card-text class="pb-0 pt-0"> SHIPPING: </v-card-text>
                                </v-col>
                                <v-col cols="6" md="6" class="text-right">
                                    <v-card-text class="pb-0 pt-0">$ {{this.order.shipping}} USD </v-card-text>
                                </v-col>
                                <v-col cols="6" md="6">
                                    <v-card-text class="pb-0 pt-0 error--text font-weight-bold"> TOTAL: </v-card-text>
                                </v-col>
                                <v-col cols="6" md="6" class="text-right">
                                    <v-card-text class="pb-0 pt-0 error--text font-weight-bold">$ {{this.order.total + this.order.shipping}} USD </v-card-text>
                                </v-col>
                            </v-row>
                            <v-col cols="12" md="12">
                                <v-btn block :color="enablePayment ? '' : 'success'" @click="enablePayment = !enablePayment" :disabled="this.order.quantity==0"> <v-icon left>mdi-credit-card-check</v-icon> {{ enablePayment ? 'HIDE' : 'ENABLE' }} SECURE PAYMENT </v-btn>
                            </v-col>
                            <v-card class="pa-2" color="grey lighten-4 rounded" v-if="freeShipping">
                                <v-card-text class="p-0 text-center" v-if="freeShipping"> <v-icon>mdi-truck</v-icon> You need $ {{25 - this.order.total}} USD to get to the free shipping. </v-card-text>
                            </v-card>
                        </v-card>
                    </v-col>
                </v-row>
                <v-form ref="form" v-model="valid" lazy-validation>
                    <v-row v-if="enablePayment">
                        <v-col cols="12" md="6">
                            <v-card class="pa-2" tile>
                                <v-card-text class="text-center black--text font-weight-black"> INVOICING INFORMATION </v-card-text>
                                <v-divider class="ma-2"></v-divider>
                                <v-card-text>
                                    <v-row class="pb-0 pt-0">
                                        <v-col cols="12" md="6" class="py-0">
                                            <v-text-field outlined dense label="First Name" v-model="order.first_name" :rules="[rules.required]"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6" class="py-0">
                                            <v-text-field outlined dense label="Last Name" v-model="order.last_name" :rules="[rules.required]"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="7" class="py-0">
                                            <v-text-field outlined dense label="Email" v-model="order.email" hint="user@email.com" :rules="[rules.required, rules.email]"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="5" class="py-0">
                                            <v-text-field outlined dense label="Phone" v-model="order.phone" :rules="[rules.required]" @keypress="isNumber($event)" @input="acceptNumber" counter maxlength="14"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="9" class="py-0">
                                            <v-text-field outlined dense label="Address" v-model="order.address" :rules="[rules.required]"></v-text-field>
                                        </v-col>
                                        <v-col cols="6" md="3" class="py-0">
                                            <v-text-field outlined dense label="ZIP Code" v-model="order.zip_code" :rules="[rules.required]" @keypress="isNumber($event)" counter maxlength="5"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6" class="py-0">
                                            <v-autocomplete @change="getCities()" outlined dense label="State" v-model="order.state" :rules="[rules.required]" :items="states" clearable></v-autocomplete>
                                        </v-col>
                                        <v-col cols="12" md="6" class="py-0">
                                            <v-autocomplete outlined dense label="City" v-model="order.city" :rules="[rules.required]" :items="cities" :disabled="cities.length == 0" clearable></v-autocomplete>
                                        </v-col>
                                    </v-row>
                                </v-card-text>
                            </v-card>
                        </v-col>
                        <v-col cols="12" md="6">
                            <v-card class="pa-2" tile>
                                <v-card-text class="text-center black--text font-weight-black"> PAYMENT INFORMATION </v-card-text>
                                <v-divider class="ma-2"></v-divider>
                                <v-img src="https://www.fisioterapiaduque.com/wp-content/uploads/2020/11/pagos.png" height="7vh"></v-img>
                                <v-card-text>
                                    <v-row class="mt-2">
                                        <v-col cols="12" md="8" class="py-0">
                                            <v-text-field outlined dense label="Card number" v-model="order.card_number" :rules="[rules.required]" @keypress="isNumber($event)" @input="numberCardMask" maxlength="19" append-icon="mdi-credit-card-settings-outline"></v-text-field>
                                        </v-col>
                                        <v-col cols="4" md="4" class="py-0">
                                            <v-text-field outlined dense label="CVV" v-model="order.cvv" :rules="[rules.required]" @keypress="isNumber($event)" counter maxlength="4" minlength="3"></v-text-field>
                                        </v-col>
                                        <v-col cols="12" md="6" class="py-0">
                                            <v-select dense :items="months" item-text="month" label="EXPIRATION MONTH" v-model="order.exp_month" :rules="[rules.required]" outlined clearable></v-select>
                                        </v-col>
                                        <v-col cols="12" md="6" class="py-0">
                                            <v-select dense :items="years" label="EXPIRATION YEAR" v-model="order.exp_year" :rules="[rules.required]" outlined clearable></v-select>
                                        </v-col>
                                    </v-row>
                                    <v-btn :disabled="!valid" @click="validate" block color="orange white--text">ORDER AND PAYMENT</v-btn>
                                </v-card-text>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-form>
            </v-container>
        </v-main>
        <footer-component></footer-component>
    </v-app>
</template>

<script>
    import datos from "../../../json/estados-municipios.json";

    export default {
        mounted() {
            console.log('Component check out mounted.');
            this.getCartContent();
            this.states = Object.keys(datos);
        },
        computed: {
            years() {
                const year = new Date().getFullYear();
                return Array.from({length: (year+10) - year}, (value, index) => year + index);
            },
            months() {
                const months = [
                    {month: 'January', value: '01'}, 
                    {month: 'February', value: '02'}, 
                    {month: 'March', value: '03'}, 
                    {month: 'April', value: '04'}, 
                    {month: 'May', value: '05'}, 
                    {month: 'June', value: '06'}, 
                    {month: 'July', value: '07'}, 
                    {month: 'August', value: '08'}, 
                    {month: 'September', value: '09'}, 
                    {month: 'October', value: '10'}, 
                    {month: 'November', value: '11'}, 
                    {month: 'December', value: '12'}
                ];
                return months;
            }
        },
        data: () => ({
            order: {
                quantity: 0,
                shipping: 0,
                total: 0.0,
                first_name: 'd',
                last_name: 'd',
                email: 'd@uu.com',
                phone: '5',
                address: 'j',
                zip_code: '5',
                state: 'jsj',
                city: 'jd',
                card_number: '5',
                cvv: '5',
                exp_month: '05',
                exp_year: '2021',
                products: []
            },
            moduleData: 'checkout',
            cartObject: [],
            cartObjectJSON: [],
            headers: [
                {
                    text: '',
                    value: 'picture',
                    align: 'center',
                },
                {
                    text: 'NAME',
                    value: 'name',
                    align: 'center',
                },
                {
                    text: 'QUANTITY',
                    value: 'quantity',
                    align: 'center',
                },
                {
                    text: 'UNIT PRICE',
                    value: 'price',
                    align: 'center',
                },
                {
                    text: 'TOTAL',
                    value: 'total',
                    align: 'center',
                },
                { 
                    text: '', 
                    value: 'actions', 
                    sortable: false,
                    align: 'center',
                },
            ],
            rules: {
                required: value => !!value || 'Required.',
                counter: value => value.length <= 20 || 'Max 20 characters',
                email: value => {
                    const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    return pattern.test(value) || 'Invalid e-mail.'
                },

            },
            valid: true,
            states: [],
            cities: [],
            freeShipping: false,
            enablePayment: false,
        }),
        methods: {
            validate () {
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
                if(this.$refs.form.validate()) {
                    Swal.fire({
                        title: 'Do you want to order the products?',
                        showCancelButton: true,
                        confirmButtonText: 'ACCEPT',
                        confirmButtonColor: 'orange',
                        allowOutsideClick: false
                    }).then((result) => {
                        /* Read more about isConfirmed, isDenied below */
                        if (result.isConfirmed) {
                            axios.post('/api/order/store', this.order).then(function (response) {
                                console.log(response);
                                Toast.fire({
                                    icon: 'success',
                                    title: 'Order completed',
                                });
                                axios.get('/api/removeCart');
                                setTimeout( function(){
                                    location.href="/";
                                } , 3000);
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
                }
            },
            addCart($id) {
                this.loading = $id;
                axios.get('/api/addItemCart/' + $id).then(res=>{
                    if(res.status == 200) {
                        this.loading = 0;
                        this.snackbar = true;
                        this.textSnackBar = 'You added the product correctly. Check to your cart.'
                    }
                });
                this.getCartContent();
            },
            deleteItemCart($id) {
                this.loading = $id;
                axios.get('/api/removeItemCart/' + $id).then(res=>{
                    if(res.status == 200) {
                        this.loading = 0;
                        this.snackbar = true;
                        this.textSnackBar = 'You delete the product correctly. Check to your cart.'
                    }
                });
                this.getCartContent();
            },
            getCartContent() {
                axios.get('/api/productsCart').then(res=>{
                    this.cartObject = res.data;
                    this.order.quantity = Object.keys(this.cartObject).length;
                    this.cartObjectJSON = Object.values(this.cartObject);
                    this.order.products = this.cartObjectJSON;
                });
                axios.get('/api/totalCart').then(res=>{
                    this.order.total = res.data;
                    this.freeShipping = this.order.total < 25 ? true : false; 
                    this.order.shipping = this.order.total < 25 ? 5 : 0; 
                });
                this.callChild();
            },
            callChild() {
                this.$refs.childRef.getCartContent();
            },
            acceptNumber() {
                var x = this.order.phone.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                this.order.phone = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
            },
            numberCardMask() {
                var x = this.order.card_number.replace(/\D/g, '').match(/(\d{0,4})(\d{0,4})(\d{0,4})(\d{0,4})/);
                this.order.card_number = !x[2] ? x[1] : x[1] + ' ' + x[2] + (x[3] ? ' ' + x[3] : '') + (x[4] ? ' ' + x[4] : '');
            },
            getCities() {
                this.cities = datos[this.order.state];
            },
            isNumber: function(evt) {
                evt = (evt) ? evt : window.event;
                var charCode = (evt.which) ? evt.which : evt.keyCode;
                if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                    evt.preventDefault();;
                } else {
                    return true;
                }
            }
        }
    }
</script>

<style scoped>
    .text-fiel-form {
        padding-top: 0;
        padding-bottom: 0;
    }
</style>