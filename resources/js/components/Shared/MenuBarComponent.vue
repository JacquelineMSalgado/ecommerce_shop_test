<template>
    <v-app-bar class="sticky-top">
        <v-toolbar-title>eCommerce Shop</v-toolbar-title>

        <v-spacer></v-spacer>

        <v-btn-toggle v-model="text" tile color="deep accent-3" group>
            <v-btn v-if="data == 'home'" value="home" :href="'#' + text"> HOME </v-btn>
            <v-btn v-else value="home" href="/"> HOME </v-btn>

            <v-btn v-if="data == 'home'" value="products" :href="'#' + text"> PRODUCTS </v-btn>

            <v-btn v-if="data == 'home'" value="about" :href="'#' + text"> ABOUT US </v-btn>

            <v-btn v-if="data == 'home'" value="contact" :href="'#' + text"> CONTACT </v-btn>

            <v-menu v-model="menu" :close-on-content-click="false" :nudge-width="200" offset-x>
                <template v-slot:activator="{ on, attrs }">
                    <v-btn icon v-bind="attrs" v-on="on">
                        <v-badge color="green" v-if="cartNumber > 0" :content="cartNumber"><v-icon>mdi-cart </v-icon></v-badge>
                        <v-icon v-if="cartNumber == 0"> mdi-cart </v-icon>
                    </v-btn>
                </template>
                <v-card>
                    <v-list>
                        <v-list-item>
                            <v-list-item-avatar>
                                    <img src="https://cdn.pixabay.com/photo/2017/06/07/18/35/design-2381160__340.png" alt="John">
                            </v-list-item-avatar>

                            <v-list-item-content>
                                <v-list-item-title>Shopping Cart</v-list-item-title>
                                <v-list-item-subtitle>Products</v-list-item-subtitle>
                            </v-list-item-content>

                            <v-list-item-action>
                                <v-btn color="primary" @click="goToSite('/checkout')" :disabled="cartNumber==0">
                                    <v-icon left>mdi-check</v-icon> CHECK OUT
                                </v-btn>
                            </v-list-item-action>
                        </v-list-item>
                    </v-list>
                    <v-data-table :headers="headers" :items="cartObjectJSON" hide-default-footer class="elevation-1">
                        <template v-slot:[`item.picture`]="{ item }">
                            <v-list-item-avatar>
                                <img :src="'data:image/png;base64,' + item.picture" :alt="item.picture">
                            </v-list-item-avatar>
                        </template>
                        <template v-slot:[`item.actions`]="{ item }">
                            <v-icon @click="addCart(item.id)"> mdi-plus </v-icon>
                            <v-icon @click="deleteItemCart(item.id)"> mdi-delete </v-icon>
                        </template>
                        <template v-slot:[`item.price`]="{ item }">
                            $ {{ item.price }}
                        </template>
                        <template v-slot:[`item.total`]="{ item }">
                            $ {{ item.total }}
                        </template>
                        <template v-slot:no-data>
                            You don't have products added
                        </template>
                    </v-data-table>
                </v-card>
            </v-menu>

            <v-btn value="login" @click="goToSite('/login')"> LOG IN </v-btn>
        </v-btn-toggle>
    </v-app-bar>
</template>

<script>
    export default {
        props: ["data"],
        mounted() {
            this.getCartContent();
        },
        data: () => ({
            cartNumber: 0,
            cartObject: [],
            cartObjectJSON: [],
            text: 'home',
            menu: false,
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
            goToSite(route) {
                window.location.href = route;
            },
            getCartContent() {
                axios.get('/api/productsCart').then(res=>{
                    this.cartObject = res.data;
                    this.cartNumber = Object.keys(this.cartObject).length;
                    this.cartObjectJSON = Object.values(this.cartObject);
                });
            },
            callParent() {
                this.$emit('onChange');
            }
        }
    }
</script>