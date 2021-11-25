<template>
<div>
    <div class="jumbotron">
        <h2>Create Income</h2>
        
        <div class="form-group" :class="{ 'error': $v.income.email.$error }">
            <label for="inputEmail">Email</label>
            <input
                type="text"
                class="form-control"
                v-model="$v.income.email.$model"
                name="email"
                id="inputEmail"
                placeholder="Email"
            >
            <div class="alert alert-warning" v-if="!$v.income.email.required">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.income.email.email">E-mail format invalid</div>
        </div>

        <div class="form-group" :class="{ 'error': $v.income.value.$error }">
            <label for="inputValue">Value</label>
            <input
                type="number"
                class="form-control"
                v-model="$v.income.value.$model"
                name="value"
                id="inputValue"
                placeholder="Value"
            >
            <div class="alert alert-warning" v-if="!$v.income.value.required">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.income.value.numeric">Value must only contain numbers</div>
            <div class="alert alert-warning" v-if="!$v.income.value.between">Value be between 0,01 and 5000,00</div>
        </div>
        <!-- TYPE -->

        <div class="form-group" :class="{ 'error': $v.income.type.$error }">
            <label for="inputType">Payment Type</label>
            <br>
            <select class="browser-default custom-select" v-model="$v.income.type.$model" @change="checkIBAN()">
                <option value='c'>Cash</option>
                <option value='bt'>Bank Transfer</option>
            </select>
            <div class="alert alert-warning" v-if="!$v.income.type.required">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.income.type.isTypeValid">Payment Type must be Cash or Bank Transfer</div>
            <!--<div class="alert alert-warning" v-if="!$v.income.type.sameAs">Type must be Cash or Bank Transfer</div>-->
        </div>

        <div class="form-group" :class="{ 'error': $v.income.source_description.$error }">
            <label for="inputDescription">Source Description</label>
            <input
                type="text"
                class="form-control"
                v-model="$v.income.source_description.$model"
                name="source_description"
                id="inputSourceDescription"
                placeholder="SourceDescription"
            >
            <div class="alert alert-warning" v-if="!$v.income.source_description.required">Field is required</div>
        </div>

        <div class="form-group" v-if="income.type=='bt'">
            <label for="inputIBAN">IBAN</label>
            <input
                type="text"
                class="form-control"
                v-model="income.iban"
                name="iban"
                id="inputIBAN"
                placeholder="IBAN"
            >
            <div class="alert alert-warning" v-if="!$v.income.iban.requiredIf">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.income.iban.isIBANvalid">Format must be 2 letters followed by 23 numbers</div>
        </div>

        <div class="alert alert-danger" v-if="hasError">Empty or invalid fields!</div>

        <div class="form-group">
            <a class="btn btn-success" v-on:click.prevent="createIncome()">Save</a>
            <a class="btn btn-light" v-on:click.prevent="cancelCreation()">Close</a>
        </div>
        
        
        
    </div>
</div>
</template>

<script type="text/javascript">
import { required, numeric, between, email, helpers, requiredIf, sameAs } from 'vuelidate/lib/validators';

 const isIBANvalid= function isIBANvalid(value, income) {
    return (/^[A-Z]{2}[0-9]{23}$/).test(value) || (!income.transfer==1);  
    //colocar na 2ª condição, quando se quer que haja IBAN, e depois negar, assim devolve true quando não é required e fica bom
 }

 const isTypeValid= function isTypeValid() {
    if(this.income.type=='bt' || this.income.type=='c'){
        return true;
    }else{
        return false;
    }
 }

export default {
    data() {
        return {
            hasError: false,
            income: {
                email: "",
                value:"",
                type: "",
                transfer:"",
                source_description:"",
                iban:""
            },
        };
    },
    methods: {
       
        createIncome: function() {
            if (!this.$v.$invalid) {
                this.hasError = false;
                axios.post("api/movements/income", this.income, this.$parent.auth)
                .then(response => {
                    this.$parent.showSuccess = true;
                    this.$parent.successMessage = "Income created with success";
                    this.$parent.movementIncome = false;
                    
                    this.$socket.emit('income_movement', this.income);
                })

                .catch(error => {
                    console.log(error.response);
                });

            }else{
                console.log(this.$v.$invalid);
                this.hasError = true;
            }
        },
        cancelCreation: function() {
            this.$parent.movementIncome = false;
        },
        checkIBAN: function() {
            if(this.income.type=='bt'){
                this.income.transfer = 1;
            }else{
                this.income.transfer = 0;
            }
        }
    },
    validations: {
        income: {
            email: { required, email },
            value: { required, numeric, between: between(0.01, 5000.00) },
            type: { required, isTypeValid },
            source_description: { required },
            iban: { isIBANvalid, requiredIf: requiredIf((income) => {
                if(income.transfer == 1){
                    return true;
                }else{
                    return false;
                }
            })}
        }
   }
};
</script>

<style scoped>
    .error input {
        border-color: red;
        background: #FDD;
    }
    .error input:focus {
        outline-color: #F99;
    }
</style>