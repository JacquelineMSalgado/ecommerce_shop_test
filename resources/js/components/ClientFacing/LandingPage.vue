<template>
    <v-app>
        <menu-bar-component @onChange="getCartContent" ref="childRef1" :data="moduleData"></menu-bar-component>

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
                            <v-card :loading="loading == item.id" class="mx-auto my-12" max-width="374">
                                <template slot="progress">
                                    <v-progress-linear color="primary" height="10" indeterminate></v-progress-linear>
                                </template>

                                <v-img height="250" :src="'data:image/png;base64,' + item.picture"></v-img>

                                <v-card-title>{{ item.name }}</v-card-title>

                                <v-card-text>
                                    <v-row align="center" class="mx-0">
                                        <v-rating :value="4.5" color="amber" dense half-increments readonly size="14"></v-rating>

                                        <div class="grey--text ms-4"> 4.5 (413) </div>
                                    </v-row>

                                    <div class="my-4 text-subtitle-1"> ${{item.price}} USD • {{item.slug}} </div>

                                    <!-- <div>{{item.description}}</div> -->
                                </v-card-text>

                                <v-divider class="mx-4"></v-divider>

                                <v-card-actions class="justify-center">
                                    <v-btn color="deep lighten-2" @click="goToSite('/product/' + item.slug)"> READ MORE </v-btn>
                                    <v-btn v-if="!cartObject.hasOwnProperty(item.id)" depressed color="primary" elevation="2" @click="addCart(item.id)">       
                                        <v-icon left> mdi-cart </v-icon> ADD TO CART 
                                    </v-btn>
                                    <template v-else>
                                        <v-btn  depressed color="primary" elevation="2" @click="addCart(item.id)">       
                                            <v-icon left> mdi-cart </v-icon> + 1
                                        </v-btn>
                                        <v-btn depressed text @click="deleteItemCart(item.id)">       
                                            <v-icon left> mdi-delete </v-icon>
                                        </v-btn>
                                    </template>
                                </v-card-actions>
                            </v-card>
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
            goToSite(route) {
                window.location.href = route;
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