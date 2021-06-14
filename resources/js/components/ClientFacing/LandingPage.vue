<template>
    <v-app>
        <menu-bar-component @onChange="getCartContent" ref="childRef1" :data="moduleData"></menu-bar-component>

        <!-- Sizes your content based upon application components -->
        <v-main id="home">
            <v-carousel hide-delimiters cycle>
                <v-carousel-item v-for="(item,i) in products" :key="i" :src="item.picture" :alt="item.name"></v-carousel-item>
            </v-carousel>
            <!-- Provides the application the proper gutter -->
            <v-container fluid id="products">
                <v-divider></v-divider>
                <v-card flat tile>
                    <v-row>
                        <v-spacer></v-spacer>
                        <v-col v-for="(item, index) in products" :key="index" cols="12" sm="6" md="4">
                            <v-hover v-slot="{ hover }" open-delay="200">
                                <v-card :loading="loading == item.id" :elevation="hover ? 16 : 2" :class="'mx-auto my-12 ' + { 'on-hover': hover }" max-width="374">
                                    <template slot="progress">
                                        <v-progress-linear color="primary" height="10" indeterminate></v-progress-linear>
                                    </template>

                                    <v-img height="250" :src="item.picture" :alt="item.name">
                                        <v-expand-transition>
                                            <div v-if="hover" class="d-flex transition-fast-in-fast-out  darken-2 v-card--reveal text-h2 white--text" style="height: 30%;">
                                                <v-card-actions class="justify-center">
                                                    <v-btn color="deep lighten-2" :href="'/api/productSlug/' + item.slug"> READ MORE </v-btn>
                                                    <v-btn depressed color="success" elevation="2" @click="addCart(item.id)">       
                                                        <v-icon left> mdi-cart </v-icon> ADD TO CART 
                                                    </v-btn>
                                                </v-card-actions>
                                            </div>
                                        </v-expand-transition>
                                    </v-img>

                                    <v-card-title>{{ item.name }}</v-card-title>

                                    <v-card-text>
                                        <v-btn v-if="cartObject.hasOwnProperty(item.id)" absolute color="orange" class="white--text" fab x-small right top>
                                            {{cartObject[item.id].quantity}}
                                        </v-btn>
                                        <v-row align="center" class="mx-0">
                                            <v-rating :value="4.5" color="amber" dense half-increments readonly size="14"></v-rating>

                                            <div class="grey--text ms-4"> 4.5 (413) </div>
                                        </v-row>

                                        <div class="my-4 text-subtitle-1"> ${{item.price}} USD • {{item.slug}} </div>
                                    </v-card-text>
                                </v-card>
                            </v-hover>
                        </v-col>
                    </v-row>
                </v-card>
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
        mounted() {
            console.log('Component landing mounted.');
            this.getCartContent();
        },
        data: () => ({
            moduleData: 'home',
            products: [],
            cartNumber: 0,
            cartObject: [],
            cartObjectJSON: [],
            loading: 0,
            snackbar: false,
            textSnackBar: '',
            timeout: 5000,
        }),
        created(){
            axios.get('/api/products').then(res=>{
                this.products = res.data;
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
                this.$refs.childRef1.getCartContent();
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