<template>
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
</template>

<script>
    export default {
        mounted() {
            console.log('Component footer mounted.');
        },
        data: () => ({
            icons: [
                'mdi-facebook',
                'mdi-twitter',
                'mdi-linkedin',
                'mdi-instagram',
            ]
        })
    }
</script>