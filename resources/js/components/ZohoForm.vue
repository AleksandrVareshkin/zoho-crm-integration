<template>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Create Deal and Account in Zoho CRM</h1>
        <form @submit.prevent="submitForm" class="needs-validation position-relative" novalidate>
            <div class="mb-3 position-relative">
                <label class="form-label">Deal Name:</label>
                <input v-model="formData.deal_name" :class="{'is-invalid': errors['deal_name']}" class="form-control" required>
                <div v-if="errors['deal_name']" class="error-message position-absolute">{{ errors['deal_name'][0] }}</div>
            </div>
            <div class="mb-3 position-relative">
                <label class="form-label">Deal Stage:</label>
                <select v-model="formData.deal_stage" :class="{'is-invalid': errors['deal_stage']}" class="form-select" required>
                    <option value="Prospecting">Qualification</option>
                    <option value="Qualification">Needs Analysis</option>
                    <option value="Needs Analysis">Value Proposition</option>
                    <option value="Value Proposition">Identify Decision Makers</option>
                    <option value="Identify Decision Makers">Proposal/Price Quote</option>
                    <option value="Proposal/Price Quote">Negotiation/Review</option>
                    <option value="Negotiation/Review">Closed Won</option>
                    <option value="Closed Won">Closed Lost</option>
                    <option value="Closed Lost">Closed Lost to Competition</option>
                </select>
            </div>
            <div class="mb-3 position-relative">
                <label class="form-label">Account Name:</label>
                <input v-model="formData.account_name" :class="{'is-invalid': errors['account_name']}" class="form-control" required>
                <div v-if="errors['account_name']" class="error-message position-absolute">{{ errors['account_name'][0] }}</div>
            </div>
            <div class="mb-3 position-relative">
                <label class="form-label">Account Website:</label>
                <input v-model="formData.account_website" :class="{'is-invalid': errors['account_website']}" class="form-control" required>
                <div v-if="errors['account_website']" class="error-message position-absolute">{{ errors['account_website'][0] }}</div>
            </div>
            <div class="mb-3 position-relative">
                <label class="form-label">Account Phone:</label>
                <input v-model="formData.account_phone" :class="{'is-invalid': errors['account_phone']}" class="form-control" required>
                <div v-if="errors['account_phone']" class="error-message position-absolute">{{ errors['account_phone'][0] }}</div>
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
                deal_name: '',
                deal_stage: '',
                account_name: '',
                account_website: '',
                account_phone: ''
            },
            message: '',
            errors: {},
            messageType: ''
        }
    },

    methods: {
        async submitForm() {
            this.errors = {};
            this.message = '';
            try {
                const response = await axios.post('http://127.0.0.1:8000/form/submit', { form_data: this.formData });
                if (response.data.status === 'success') {
                    this.message = 'Deal and Account created successfully!';
                    this.messageType = 'alert-success';
                    this.resetForm();
                } else {
                    this.message = 'Error creating records: ' + response.data.message;
                    this.messageType = 'alert-danger';
                }
            } catch (error) {
                if (error.response && error.response.status === 422 && error.response.data.errors) {
                    this.errors = this.formatErrors(error.response.data.errors);
                    this.message = 'Error creating records. Please fix the highlighted errors.';
                    this.messageType = 'alert-danger';
                } else {
                    this.message = 'Error creating records: ' + (error.response ? error.response.data.message : error.message);
                    this.messageType = 'alert-danger';
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
                deal_name: '',
                deal_stage: '',
                account_name: '',
                account_website: '',
                account_phone: ''
            };
        }
    }
}
</script>

<style scoped>
.container {
    max-width: 600px;
}
.error-message {
    color: red;
    font-size: 0.7em;
    top: 100%;
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
.position-absolute {
    position: absolute;
    top: 100%;
    left: 0;
}
.is-invalid {
    border-color: #dc3545;
}
</style>
