<template>
    <v-app>
         <!-- class="fixed-top" -->
        <v-app-bar class="sticky-top">
            <v-toolbar-title>eCommerce Shop</v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn-toggle v-model="text" tile color="deep accent-3" group>
                <v-btn value="home" :href="'#' + text"> HOME </v-btn>

                <v-btn value="products" :href="'#' + text"> PRODUCTS </v-btn>

                <v-btn value="about" :href="'#' + text"> ABOUT US </v-btn>

                <v-btn value="contact" :href="'#' + text"> CONTACT </v-btn>

                <v-btn icon @click="goToSite('/checkout')">
                    <v-badge color="green" v-if="cartNumber > 0" :content="cartNumber"><v-icon>mdi-cart </v-icon></v-badge>
                    <v-icon v-if="cartNumber == 0"> mdi-cart </v-icon>
                </v-btn>

                <v-btn value="login" @click="goToSite('/login')"> LOG IN </v-btn>
            </v-btn-toggle>
        </v-app-bar>

        <!-- Sizes your content based upon application components -->
        <v-main id="home">
            <v-carousel hide-delimiters cycle>
                <v-carousel-item v-for="(item,i) in products" :key="i" :src="'data:image/png;base64,' + item.picture"></v-carousel-item>
            </v-carousel>
            <!-- Provides the application the proper gutter -->
            <v-container fluid id="products">
                <v-divider></v-divider>
                <v-card flat tile>
                    <v-row>
                        <v-spacer></v-spacer>
                        <v-col v-for="(item, index) in products" :key="index" cols="12" sm="6" md="4">
                            <v-card :loading="loading" class="mx-auto my-12" max-width="374">
                                <template slot="progress">
                                    <v-progress-linear color="deep-purple" height="10" indeterminate></v-progress-linear>
                                </template>

                                <v-img height="250" :src="'data:image/png;base64,' + item.picture"></v-img>

                                <v-card-title>{{ item.name }}</v-card-title>

                                <v-card-text>
                                    <v-row align="center" class="mx-0">
                                        <v-rating :value="4.5" color="amber" dense half-increments readonly size="14"></v-rating>

                                        <div class="grey--text ms-4"> 4.5 (413) </div>
                                    </v-row>

                                    <div class="my-4 text-subtitle-1"> ${{item.price}} USD • {{item.slug}} </div>

                                    <div>{{item.description}}</div>
                                </v-card-text>

                                <v-divider class="mx-4"></v-divider>

                                <v-card-actions>
                                    <v-btn color="deep lighten-2" text> READ MORE </v-btn>
                                    <v-btn color="cyan" elevation="2" text @click="addCart(item.id)"> ADD TO CART </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card>
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
        mounted() {
            console.log('Component landing mounted.');
        },
        data: () => ({
            products: [],
            cartNumber: 0,
            cartObject: [],
            icons: [
                'mdi-facebook',
                'mdi-twitter',
                'mdi-linkedin',
                'mdi-instagram',
            ],
            loading: false,
            text: 'home',
            snackbar: false,
            textSnackBar: '',
            timeout: 5000,
        }),
        created(){
            axios.get('/products').then(res=>{
                this.products = res.data;
            });
            this.getCartContent();
        },
        methods: {
            addCart($id) {
                this.loading = true
                axios.get('/add-to-cart/' + $id).then(res=>{
                    if(res.status == 200) {
                        this.loading = false;
                        this.snackbar = true;
                        this.textSnackBar = 'You added the product correctly. Check to your cart.'
                    }
                });
                this.getCartContent();
            },
            goToSite(route) {
                window.location.href = route;
            },
            getCartContent() {
                axios.get('/get-cart').then(res=>{
                    this.cartObject = res.data;
                    this.cartNumber = Object.keys(this.cartObject).length;
                });
            }
        },
    }
</script>