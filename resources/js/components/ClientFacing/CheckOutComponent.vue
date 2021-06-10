<template>
    <v-app>
        <menu-bar-component @onChange="getCartContent" ref="childRef" :data="moduleData"></menu-bar-component>
        <v-main id="home">
            <v-container fluid id="products">
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
            </v-container>
        </v-main>
        <footer-component></footer-component>
    </v-app>
</template>

<script>
    export default {
        mounted() {
            console.log('Component check out mounted.');
            this.getCartContent();
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
        }),
        methods: {
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
        }
    }
</script>