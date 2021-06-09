<template>
    <v-app>
        <menu-bar-component @onChange="getCartContent" ref="childRef" :data="moduleData"></menu-bar-component>

        <!-- Sizes your content based upon application components -->
        <v-main id="home">
            <v-container fluid id="products">
                <v-row class="mt-5">
                    <v-col cols="12" md="4">
                        <v-card class="pa-2" tile>
                            <v-hover v-slot="{ hover }">
                                <v-card class="mx-auto" color="grey lighten-4" max-width="600">
                                    <v-img :aspect-ratio="16/9" :src="'data:image/png;base64,' + product.picture">
                                        <v-expand-transition>
                                            <div v-if="hover" class="d-flex transition-fast-in-fast-out  darken-2 v-card--reveal text-h2 white--text" style="height: 30%;">
                                                <v-btn depressed color="success" elevation="2" @click="addCart(product.id)">       
                                                    <v-icon left> mdi-cart </v-icon> ADD TO CART 
                                                </v-btn>
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
                                <v-btn color="orange white--text" href="/checkout" :disabled="cartNumber==0"> 
                                    <v-icon left> mdi-check </v-icon> CHECK OUT 
                                </v-btn>
                                <v-btn color="success" depressed elevation="2" @click="addCart(product.id)">       
                                    <v-icon left> mdi-cart </v-icon> ADD TO CART 
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-main>

        <footer-component></footer-component>

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
            this.getCartContent();
        },
        data: () => ({
            moduleData: 'product',
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
            getCartContent() {
                axios.get('/api/productsCart').then(res=>{
                    this.cartObject = res.data;
                    this.cartNumber = Object.keys(this.cartObject).length;
                    this.cartObjectJSON = Object.values(this.cartObject);
                });
                this.callChild();
            },
            callChild() {
                this.$refs.childRef.getCartContent();
            }
        },
    }
</script>

<style scoped>
    .v-card--reveal {
        align-items: center;
        bottom: 0;
        justify-content: center;
        opacity: 1;
        position: absolute;
        width: 100%;
        background-color: rgb(250, 250, 250, 0.3);
    }
</style>