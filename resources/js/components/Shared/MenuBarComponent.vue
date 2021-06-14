<template>
    <v-app-bar class="sticky-top">
        <v-toolbar-title>e-commerce shop</v-toolbar-title>

        <v-spacer></v-spacer>

        <!-- Buttom for open cart. 
        The card contain all the product added to cart with the next infomation: picture, quantity, unit price and total. -->
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
                            <v-btn color="orange white--text" href="/checkout" :disabled="cartNumber==0">
                                <v-icon left>mdi-check</v-icon> CHECK OUT
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
                <v-data-table :headers="headers" :items="cartObjectJSON" hide-default-footer class="elevation-1 hidden-sm-and-down">
                    <template v-slot:[`item.picture`]="{ item }">
                        <v-list-item-avatar>
                            <v-img :src="item.picture" :alt="item.picture"></v-img>
                        </v-list-item-avatar>
                    </template>
                    <template v-slot:[`item.actions`]="{ item }">
                        <!-- <v-icon @click="addCart(item.id)"> mdi-plus </v-icon> -->
                        <v-icon @click="deleteItemCart(item.id)"> mdi-close </v-icon>
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

        <!-- Menu
        Display all the options in the toolbar, the design is responsive. -->
        <v-toolbar-items class="hidden-sm-and-down">
            <v-btn text v-for="item in menuItems" :key="item.title" :href="item.path"> {{item.title}} </v-btn>
        </v-toolbar-items>

        <v-menu v-if="$vuetify.breakpoint.xsOnly || $vuetify.breakpoint.smOnly">
            <template v-slot:activator="{ on }">
                <v-btn icon v-on="on">
                    <v-icon>mdi-menu</v-icon>
                </v-btn>
            </template>

            <v-list>
                <v-list-item v-for="(item, i) in menuItems" exact :key="i" :href="item.path"> {{item.title}} </v-list-item>
            </v-list>
      </v-menu>
    </v-app-bar>
</template>

<script>
    export default {
        props: ["data"],
        // Call method for load cart content after DOM has been mounted
        mounted() {
            this.getCartContent();
        },
        // The data content variables to cart and menu options
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
            menuItems: [
                { path: "/", name: "product", title: "HOME" },
                { path: "/categories", name: "us", title: "CATEGORIES" },
                { path: "/contact", name: "resources", title: "CONTACT" },
                { path: "/login", name: "login", title: "LOGIN" }
            ]
        }),
        methods: {
            // Method for add a item for the user cart
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
            // Method for remove a item for the user cart
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
            // Method for consult user cart
            getCartContent() {
                axios.get('/api/productsCart').then(res=>{
                    this.cartObject = res.data;
                    this.cartNumber = Object.keys(this.cartObject).length;
                    this.cartObjectJSON = Object.values(this.cartObject);
                });
            },
            // Method for comunicate with a other components
            callParent() {
                this.$emit('onChange');
            }
        }
    }
</script>