<template>
    <v-app>
         <!-- class="fixed-top" -->
        <v-app-bar>
            <v-toolbar-title>eCommerce Shop</v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn-toggle v-model="text" tile color="deep accent-3" group>
                <v-btn value="home"> HOME </v-btn>

                <v-btn value="products"> PRODUCTS </v-btn>

                <v-btn value="about"> ABOUT US </v-btn>

                <v-btn value="contact"> CONTACT </v-btn>

                <v-btn value="login" @click="goToSite('/login')"> LOG IN </v-btn>
            </v-btn-toggle>

            <v-btn icon @click="goToSite('/checkout')">
                <v-icon>mdi-cart </v-icon>
            </v-btn>
        </v-app-bar>

        <!-- Sizes your content based upon application components -->
        <v-main>
            <v-carousel hide-delimiters cycle>
                <v-carousel-item v-for="(item,i) in items" :key="i" :src="item.src"></v-carousel-item>
            </v-carousel>
            <!-- Provides the application the proper gutter -->
            <v-container fluid>
                <div class="text-center">
                    <h3>ALL PRODUCTS</h3>
                </div>
                <v-divider></v-divider>
                <v-card flat tile>
                    <v-row>
                        <v-spacer></v-spacer>
                        <v-col v-for="card in cards" :key="card" cols="12" sm="6" md="4">
                            <v-card :loading="loading" class="mx-auto my-12" max-width="374">
                                <template slot="progress">
                                    <v-progress-linear color="deep-purple" height="10" indeterminate></v-progress-linear>
                                </template>

                                <v-img height="250" src="https://cdn.vuetifyjs.com/images/cards/cooking.png"></v-img>

                                <v-card-title>Cafe Badilico</v-card-title>

                                <v-card-text>
                                    <v-row align="center" class="mx-0">
                                        <v-rating :value="4.5" color="amber" dense half-increments readonly size="14"></v-rating>

                                        <div class="grey--text ms-4"> 4.5 (413) </div>
                                    </v-row>

                                    <div class="my-4 text-subtitle-1"> $ • Italian, Cafe </div>

                                    <div>Small plates, salads & sandwiches - an intimate setting with 12 indoor seats plus patio seating.</div>
                                </v-card-text>

                                <v-divider class="mx-4"></v-divider>

                                <v-card-actions>
                                    <v-btn color="deep lighten-2" text @click="reserve"> READ MORE </v-btn>
                                    <v-btn color="cyan" elevation="2" text @click="reserve"> ADD TO CART </v-btn>
                                </v-card-actions>
                            </v-card>
                        </v-col>
                    </v-row>
                </v-card>
            </v-container>
        </v-main>

        <v-footer dark padless>
            <v-card flat tile class="lighten-1 white--text text-center">
                <v-card-text>
                    <v-btn v-for="icon in icons" :key="icon" class="mx-4 white--text" icon>
                        <v-icon size="24px"> {{ icon }} </v-icon>
                    </v-btn>
                </v-card-text>

                <v-card-text class="white--text pt-0">
                    Phasellus feugiat arcu sapien, et iaculis ipsum elementum sit amet. Mauris cursus commodo interdum. Praesent ut risus eget metus luctus accumsan id ultrices nunc. Sed at orci sed massa consectetur dignissim a sit amet dui. Duis commodo vitae velit et faucibus. Morbi vehicula lacinia malesuada. Nulla placerat augue vel ipsum ultrices, cursus iaculis dui sollicitudin. Vestibulum eu ipsum vel diam elementum tempor vel ut orci. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
                </v-card-text>

                <v-divider></v-divider>

                <v-card-text class="white--text">
                    {{ new Date().getFullYear() }} — <strong>Vuetify</strong>
                </v-card-text>
            </v-card>
        </v-footer>
    </v-app>
</template>

<script>
    export default {
        mounted() {
            console.log('Component landing mounted.')
        },
        data: () => ({
            cards: ['Good', 'Best', 'Finest'],
            icons: [
                'mdi-facebook',
                'mdi-twitter',
                'mdi-linkedin',
                'mdi-instagram',
            ],
            loading: false,
            items: [
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/squirrel.jpg',
                },
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/sky.jpg',
                },
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/bird.jpg',
                },
                {
                    src: 'https://cdn.vuetifyjs.com/images/carousel/planet.jpg',
                },
            ],
            text: 'home',
        }),
        methods: {
            reserve () {
                this.loading = true

                setTimeout(() => (this.loading = false), 2000)
            },
            goToSite(route) {
                window.location.href = route;
            },
        },
    }
</script>