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
                                        <img :src="'data:image/png;base64,' + item.picture" :alt="item.picture" width="80vw">
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
                                    <v-card-text class="pb-0 pt-0">$ {{total}} USD</v-card-text>
                                </v-col>
                            </v-row>
                            <v-row class="mb-3">
                                <v-col cols="6" md="6">
                                    <v-card-text class="pb-0 pt-0"> SHIPPING: </v-card-text>
                                </v-col>
                                <v-col cols="6" md="6" class="text-right">
                                    <v-card-text class="pb-0 pt-0">$ {{shipping}} USD </v-card-text>
                                </v-col>
                                <v-col cols="6" md="6">
                                    <v-card-text class="pb-0 pt-0 error--text font-weight-bold"> TOTAL: </v-card-text>
                                </v-col>
                                <v-col cols="6" md="6" class="text-right">
                                    <v-card-text class="pb-0 pt-0 error--text font-weight-bold">$ {{total + shipping}} USD </v-card-text>
                                </v-col>
                            </v-row>
                            <v-col cols="12" md="12">
                                <v-btn block :color="enablePayment ? '' : 'success'" @click="enablePayment = !enablePayment" :disabled="cartNumber==0"> <v-icon left>mdi-credit-card-check</v-icon> {{ enablePayment ? 'HIDE' : 'ENABLE' }} SECURE PAYMENT </v-btn>
                            </v-col>
                            <v-card class="pa-2" color="grey lighten-4 rounded" v-if="freeShipping">
                                <v-card-text class="p-0 text-center" v-if="freeShipping"> <v-icon>mdi-truck</v-icon> You need $ {{25 - total}} USD to get to the free shipping. </v-card-text>
                            </v-card>
                        </v-card>
                    </v-col>
                </v-row>
                <v-row v-if="enablePayment">
                    <v-col cols="12" md="6">
                        <v-card class="pa-2" tile>
                            <v-card-text class="text-center black--text font-weight-black"> INVOICING INFORMATION </v-card-text>
                            <v-divider class="ma-2"></v-divider>
                            <v-card-text>
                                <v-row class="pb-0 pt-0">
                                    <v-col cols="12" md="6" class="py-0">
                                        <v-text-field outlined dense label="First Name" :rules="[rules.required]" prepend-inner-icon="mdi-tooltip-account"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" class="py-0">
                                        <v-text-field outlined dense label="Last Name" :rules="[rules.required]"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="7" class="py-0">
                                        <v-text-field outlined dense label="Email" hint="user@email.com" :rules="[rules.required, rules.email]" prepend-inner-icon="mdi-email"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="5" class="py-0">
                                        <v-text-field outlined dense label="Phone" :rules="[rules.required]" @keypress="isNumber($event)" @input="acceptNumber" v-model="value" counter maxlength="14" prepend-inner-icon="mdi-phone"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="9" class="py-0">
                                        <v-text-field outlined dense label="Address" :rules="[rules.required]" prepend-inner-icon="mdi-home-city"></v-text-field>
                                    </v-col>
                                    <v-col cols="6" md="3" class="py-0">
                                        <v-text-field outlined dense label="ZIP Code" :rules="[rules.required]" @keypress="isNumber($event)" counter maxlength="5"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" class="py-0">
                                        <v-autocomplete @change="getCities(state)" outlined dense label="State" :rules="[rules.required]" :items="states" v-model="state" clearable prepend-inner-icon="mdi-map-marker"></v-autocomplete>
                                    </v-col>
                                    <v-col cols="12" md="6" class="py-0">
                                        <v-autocomplete outlined dense label="City" :rules="[rules.required]" :items="cities" v-model="city" :disabled="cities.length == 0" clearable prepend-inner-icon="mdi-city"></v-autocomplete>
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
                                        <v-text-field outlined dense label="Card number" :rules="[rules.required]" @keypress="isNumber($event)" @input="numberCardMask" v-model="numberCard" maxlength="19" append-icon="mdi-credit-card-settings-outline"></v-text-field>
                                    </v-col>
                                    <v-col cols="4" md="4" class="py-0">
                                        <v-text-field outlined dense label="CVV" :rules="[rules.required]" @keypress="isNumber($event)" counter maxlength="4" minlength="3"></v-text-field>
                                    </v-col>
                                    <v-col cols="12" md="6" class="py-0">
                                        <v-select dense :items="months" item-text="month" label="EXPIRATION MONTH" outlined></v-select>
                                    </v-col>
                                    <v-col cols="12" md="6" class="py-0">
                                        <v-select dense :items="years" label="EXPIRATION YEAR" outlined></v-select>
                                    </v-col>
                                </v-row>
                                <v-btn block color="orange white--text">ORDER AND PAYMENT</v-btn>
                            </v-card-text>
                        </v-card>
                    </v-col>
                </v-row>
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
            moduleData: 'checkout',
            cartNumber: 0,
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
            total: 0,
            rules: {
                required: value => !!value || 'Required.',
                counter: value => value.length <= 20 || 'Max 20 characters',
                email: value => {
                    const pattern = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                    return pattern.test(value) || 'Invalid e-mail.'
                },

            },
            value: '',
            numberCard: '',
            state: '',
            city: '',
            states: [],
            cities: [],
            freeShipping: false,
            shipping: 0,
            enablePayment: false,
        }),
        methods: {
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
                this.callParent();
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
                this.callParent();
            },
            getCartContent() {
                axios.get('/api/productsCart').then(res=>{
                    this.cartObject = res.data;
                    this.cartNumber = Object.keys(this.cartObject).length;
                    this.cartObjectJSON = Object.values(this.cartObject);
                });
                axios.get('/api/totalCart').then(res=>{
                    this.total = res.data;
                    this.freeShipping = this.total < 25 ? true : false; 
                    this.shipping = this.total < 25 ? 5 : 0; 
                });
                this.callChild();
            },
            callChild() {
                this.$refs.childRef.getCartContent();
            },
            acceptNumber() {
                var x = this.value.replace(/\D/g, '').match(/(\d{0,3})(\d{0,3})(\d{0,4})/);
                this.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
            },
            numberCardMask() {
                var x = this.numberCard.replace(/\D/g, '').match(/(\d{0,4})(\d{0,4})(\d{0,4})(\d{0,4})/);
                this.numberCard = !x[2] ? x[1] : x[1] + ' ' + x[2] + (x[3] ? ' ' + x[3] : '') + (x[4] ? ' ' + x[4] : '');
            },
            getCities(state) {
                this.cities = datos[state];
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