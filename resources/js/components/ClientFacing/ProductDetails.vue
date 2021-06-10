<template>
    <v-app>
         <!-- class="fixed-top" -->
        <v-app-bar class="sticky-top">
            <v-toolbar-title>eCommerce Shop</v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn-toggle v-model="text" tile color="deep accent-3" group>
                <v-btn value="home" href="/"> HOME </v-btn>

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
                                    <v-btn color="primary" @click="goToSite('/checkout')">
                                        <v-icon left>mdi-check-outline</v-icon> CHECK OUT
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

        <!-- Sizes your content based upon application components -->
        <v-main id="home">
            <v-container fluid id="products">
                <v-row class="mt-5">
                    <v-col cols="6" md="4">
                        <v-card class="pa-2" tile>
                            <v-hover v-slot="{ hover }">
                                <v-card class="mx-auto" color="grey lighten-4" max-width="600">
                                    <v-img :aspect-ratio="16/9" :src="'data:image/png;base64,' + product.picture">
                                        <v-expand-transition>
                                            <div v-if="hover" class="d-flex transition-fast-in-fast-out orange darken-2 v-card--reveal text-h2 white--text" style="height: 100%;">
                                                $ {{product.price}}
                                            </div>
                                        </v-expand-transition>
                                    </v-img>
                                </v-card>
                            </v-hover>
                        </v-card>
                    </v-col>
                    <v-col cols="12" md="8">
                        <v-card class="pa-2" tile>
                            <v-card-title>{{ product.name }}</v-card-title>
                            <v-card-text>
                                <v-row align="center" class="mx-0">
                                    <v-rating :value="4.5" color="amber" dense half-increments readonly size="14"></v-rating>

                                    <div class="grey--text ms-4"> 4.5 (413) </div>
                                </v-row>

                                <div class="my-4 text-subtitle-1"> ${{product.price}} USD • {{product.slug}} </div>

                                <div>{{product.description}}</div>
                            </v-card-text>

                            <v-divider class="mx-4"></v-divider>

                            <v-card-actions class="justify-center">
                                <v-btn color="primary" @click="goToSite('/checkout')"> 
                                    <v-icon left> mdi-check </v-icon> CHECK OUT 
                                </v-btn>
                                <v-btn v-if="!cartObject.hasOwnProperty(product.id)" depressed elevation="2" @click="addCart(product.id)">       
                                    <v-icon left> mdi-cart </v-icon> ADD TO CART 
                                </v-btn>
                                <template v-else>
                                    <v-btn  depressed elevation="2" @click="addCart(product.id)">       
                                        <v-icon left> mdi-cart </v-icon> + 1
                                    </v-btn>
                                    <v-btn depressed @click="deleteItemCart(product.id)">       
                                        <v-icon left> mdi-delete </v-icon>
                                    </v-btn>
                                </template>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <v-footer dark padless id="about">
            <v-card flat tile class="lighten-1 white--text text-center">
                <v-card-text>
                    <v-btn v-for="icon in icons" :key="icon" class="mx-4 white--text" icon>
                        <v-icon size="24px"> {{ icon }} </v-icon>
                    </v-btn>
                </v-card-text>

                <v-card-text class="white--text pt-0">
                    Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magni in error corrupti pariatur reprehenderit facere aliquid accusamus consequuntur, ex eligendi quam. Quidem cumque quaerat sit ea reiciendis velit beatae ratione.
                </v-card-text>

                <v-divider></v-divider>

                <v-card-text class="white--text">
                    &#174; {{ new Date().getFullYear() }} — <strong>Jacqueline Mireya Salgado Rivas — jacquelinsalgado.2@gmail.com</strong>
                </v-card-text>
            </v-card>
        </v-footer>

        <v-snackbar v-model="snackbar" :timeout="timeout"> {{ textSnackBar }}
            <template v-slot:action="{ attrs }">
                <v-btn color="blue" text v-bind="attrs" @click="snackbar = false"> Close </v-btn>
            </template>
        </v-snackbar>
    </v-app>
</template>

<script>
    export default {
        props: ["product_id"],
        mounted() {
            console.log('Component product details mounted.');
            console.log(this.product_id);
            this.getCartContent();
        },
        data: () => ({
            product: [],
            cartNumber: 0,
            cartObject: [],
            cartObjectJSON: [],
            icons: [
                'mdi-facebook',
                'mdi-twitter',
                'mdi-linkedin',
                'mdi-instagram',
            ],
            loading: 0,
            text: 'home',
            snackbar: false,
            textSnackBar: '',
            timeout: 5000,
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
        created(){
            axios.get('/api/product/' + this.product_id).then(res=>{
                this.product = res.data;
                console.log(this.product);
            });
        },
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
            goToSite(route) {
                window.location.href = route;
            },
            getCartContent() {
                axios.get('/api/productsCart').then(res=>{
                    this.cartObject = res.data;
                    this.cartNumber = Object.keys(this.cartObject).length;
                    this.cartObjectJSON = Object.values(this.cartObject);
                });
            }
        },
    }
</script>

<style>
    .v-card--reveal {
    align-items: center;
    bottom: 0;
    justify-content: center;
    opacity: .5;
    position: absolute;
    width: 100%;
    }
</style>