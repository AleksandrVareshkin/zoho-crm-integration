<template>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Client id not found, please input credentials for web application</h1>
        <form @submit.prevent="submitForm" class="needs-validation position-relative" novalidate>
            <div class="mb-3 position-relative">
                <label class="form-label">Client ID:</label>
                <input v-model="formData.client_id" class="form-control" required>
            </div>
            <div class="mb-3 position-relative">
                <label class="form-label">Client Secret:</label>
                <input v-model="formData.client_secret" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <div v-if="message" :class="['alert', messageType, 'mt-4']">{{ message }}</div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            formData: {
                client_id: '',
                client_secret: ''
            },
            message: '',
            errors: {},
            messageType: ''
        }
    },

    name: 'AuthForm',

    methods: {
        async submitForm() {
            this.errors = {};
            this.message = '';
            try {
                const response = await axios.post('/auth-submit', { form_data: this.formData });
                console.log(response.data);
                window.location.href = response.data;
                if (response.data.status === 'success') {
                    this.message = 'Credentials saved successfully!';
                    this.messageType = 'alert-success';
                    this.resetForm();
                }
            } catch (error) {
                if (error.response && error.response.status === 422 && error.response.data.errors) {
                    this.errors = this.formatErrors(error.response.data.errors);
                }
            }
        },
        formatErrors(errors) {
            const formattedErrors = {};
            Object.keys(errors).forEach(key => {
                const newKey = key.replace('form_data.', '');
                formattedErrors[newKey] = errors[key];
            });
            return formattedErrors;
        },
        resetForm() {
            this.formData = {
                client_id: '',
                client_secret: ''
            };
        }
    }
}
</script>

<style scoped>
.container {
    max-width: 600px;
}
.alert {
    padding: 1rem;
}
.needs-validation{
    border: 2px solid #ccc;
    padding: 1rem;
    border-radius: 10px;
}
.position-relative {
    position: relative;
}
</style>
