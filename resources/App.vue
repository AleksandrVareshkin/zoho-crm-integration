<template>
    <ZohoForm></ZohoForm>
</template>

<script>

import ZohoForm from './js/components/ZohoForm.vue';
import AuthForm from "./js/components/AuthForm.vue";
export default {
    name: 'App',

    components: {
        ZohoForm,
        AuthForm
    },

}
</script>

<style>
    body {
        margin: 0;
        padding: 0;
        background-color: ghostwhite;
    }
</style>
