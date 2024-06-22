<!-- resources/js/components/ZohoForm.vue -->

<template>
    <form @submit.prevent="submitForm">
        <input type="text" v-model="deal_name" placeholder="Deal Name">
        <input type="text" v-model="deal_stage" placeholder="Deal Stage">
        <input type="text" v-model="account_name" placeholder="Account Name">
        <input type="text" v-model="account_website" placeholder="Account Website">
        <input type="text" v-model="account_phone" placeholder="Account Phone">
        <button type="submit">Submit</button>
    </form>
</template>

<script>
export default {
    data() {
        return {
            deal_name: '',
            deal_stage: '',
            account_name: '',
            account_website: '',
            account_phone: ''
        };
    },
    methods: {
        submitForm() {
            axios.post('/form/submit', {
                deal_name: this.deal_name,
                deal_stage: this.deal_stage,
                account_name: this.account_name,
                account_website: this.account_website,
                account_phone: this.account_phone
            })
                .then(response => {
                    console.log(response.data);
                    // Handle success response here
                })
                .catch(error => {
                    console.error(error.response.data);
                    // Handle error response here
                });
        }
    }
}
</script>
