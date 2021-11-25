<template>
<div>
    <div class="jumbotron">
        <h2>Register Expense</h2>
        
        
        <div class="form-group" :class="{ 'error': $v.expense.transfer.$error }">
            <label for="inputType">Type of Movement</label>
            <br>
            <select class="browser-default custom-select" v-model="$v.expense.transfer.$model">
                <option value=0>Payment to External Entity</option>
                <option value=1>Transfer</option>
            </select>
            <div class="alert alert-warning" v-if="!$v.expense.transfer.required">Field is required</div>
        </div>

        <div class="form-group" :class="{ 'error': $v.expense.value.$error }">
            <label for="inputValue">Value</label>
            <input
                type="number"
                class="form-control"
                v-model="$v.expense.value.$model"
                name="value"
                id="inputValue"
                placeholder="Value"
            >
            <div class="alert alert-warning" v-if="!$v.expense.value.required">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.expense.value.numeric">Value must only contain numbers</div>
            <div class="alert alert-warning" v-if="!$v.expense.value.between">Value be between 0,01 and 5000,00</div>
        </div>
        <!-- TYPE  v-for="movement in movements"  :key="movement.id" -->

        <div class="form-group" :class="{ 'error': $v.expense.category.$error }">
            <label for="inputType">Category</label>
            <br>
            <select class="browser-default custom-select" v-model="$v.expense.category.$model">
                <option v-for="category in categories"  :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>


            <div class="alert alert-warning" v-if="!$v.expense.category.required">Field is required</div>
        </div>

        <div class="form-group" :class="{ 'error': $v.expense.description.$error }">
            <label for="inputDescription">Description</label>
            <input
                type="text"
                class="form-control"
                v-model="$v.expense.description.$model"
                name="description"
                id="inputDescription"
                placeholder="Description"
            >
            <div class="alert alert-warning" v-if="!$v.expense.description.required">Field is required</div>
        </div>


        <!--  ############  IF PAYMENT TO EXTERNAL ENTITY ############  -->
        <!--  ############  IF PAYMENT TO EXTERNAL ENTITY ############  -->

        <div class="form-group" v-if="expense.transfer == 0" :class="{ 'error': $v.expense.type.$error }">
            <label for="inputType">Type of Payment</label>
            <br>
            <select class="browser-default custom-select" v-model="$v.expense.type.$model">
                <option value='bt'>Bank Transfer</option>
                <option value='mb'>MB Payment</option>
            </select>
            <div class="alert alert-warning" v-if="!$v.expense.type.requiredIf">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.expense.type.isTypeValid">Type of Payment must be Bank Transfer or MB Payment</div>
        </div>

        <div class="form-group" v-if="expense.transfer == 0 && expense.type=='bt'" :class="{ 'error': $v.expense.iban.$error }">
            <label for="inputIBAN">IBAN</label>
            <input
                type="text"
                class="form-control"
                v-model="$v.expense.iban.$model"
                name="iban"
                id="inputIBAN"
                placeholder="IBAN"
            >
            <div class="alert alert-warning" v-if="!$v.expense.iban.requiredIf">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.expense.iban.isIBANvalid">Format must be 2 letters followed by 23 numbers</div>
        </div>

        <div class="form-group" v-if="expense.transfer == 0 && expense.type=='mb'" :class="{ 'error': $v.expense.mb_entity_code.$error }">
            <label for="inputValue">MB Entity Code</label>
            <input
                type="number"
                class="form-control"
                v-model="$v.expense.mb_entity_code.$model"
                name="entityCode"
                id="inputEntityCode"
                placeholder="MB Entity Code"
            >
            <div class="alert alert-warning" v-if="!$v.expense.mb_entity_code.requiredIf">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.expense.mb_entity_code.numeric">Value must only contain numbers</div>
            <div class="alert alert-warning" v-if="!$v.expense.mb_entity_code.isMBEntityValid">Size must be 5 digits</div>
            <!--<div class="alert alert-warning" v-if="!$v.expense.mb_entity_code.minLenght">Size must be 5 digits</div>-->
        </div>

        <div class="form-group" v-if="this.expense.transfer == 0 && expense.type=='mb'" :class="{ 'error': $v.expense.mb_payment_reference.$error }">
            <label for="inputValue">MB Payment Reference</label>
            <input
                type="number"
                class="form-control"
                v-model="$v.expense.mb_payment_reference.$model"
                name="entityPaymentReference"
                id="inputPaymentReference"
                placeholder="MB Payment Reference"
            >
            <div class="alert alert-warning" v-if="!$v.expense.mb_payment_reference.requiredIf">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.expense.mb_payment_reference.numeric">Value must only contain numbers</div>
            <div class="alert alert-warning" v-if="!$v.expense.mb_payment_reference.isMBPaymentValid">Size must be 9 digits</div>
            <!--<div class="alert alert-warning" v-if="!$v.expense.mb_payment_reference.minLenght">Size must be 9 digits</div>-->
        </div>

        <!-- ############    IF TRANSFER ############   -->
        <!-- ############    IF TRANSFER ############   -->

        <div class="form-group" v-if="expense.transfer == 1" :class="{ 'error': $v.expense.destination_email.$error }">
            <label for="inputEmail">Destination e-mail</label>
            <input
                type="email"
                class="form-control"
                v-model="$v.expense.destination_email.$model"
                name="email"
                id="inputEmail"
                placeholder="Destination e-mail"
            >
            <div class="alert alert-warning" v-if="!$v.expense.destination_email.requiredIf">Field is required</div>
            <div class="alert alert-warning" v-if="!$v.expense.destination_email.email">E-mail format invalid</div>
        </div>

        <div class="form-group" v-if="expense.transfer == 1" :class="{ 'error': $v.expense.source_description.$error }">
            <label for="inputSourceDescription">Source Description</label>
            <input
                type="text"
                class="form-control"
                v-model="$v.expense.source_description.$model"
                name="description"
                id="inputSourceDescription"
                placeholder="Source Description"
            >
            <div class="alert alert-warning" v-if="!$v.expense.source_description.requiredIf">Field is required</div>
        </div>
        
        <div class="alert alert-danger" v-if="hasError">Empty or invalid fields!</div>
        <div class="alert alert-danger" v-if="!valid_Expense">Balance not enough for this expense!</div>
        
        <div class="form-group">
            <a class="btn btn-success" v-on:click.prevent="createExpense()">Save</a>
            <a class="btn btn-light" v-on:click.prevent="cancelCreation()">Close</a>
        </div>
        
    </div>
</div>
</template>

<script type="text/javascript">
import { required, numeric, between, email, helpers, requiredIf, sameAs, minLength, maxLength } from 'vuelidate/lib/validators';

 const isIBANvalid= function isIBANvalid(value, expense) {
    return (/^[A-Z]{2}[0-9]{23}$/).test(value) || (!(expense.type=='bt' && expense.transfer==0));  
    //validação tem de devolver true caso este campo nao seja required, daí esta condição 'OR'
 }

 const isTypeValid= function isTypeValid() {
    if(this.expense.type=='bt' || this.expense.type=='mb'){
        return true;
    }else{
        //validação tem de devolver true caso este campo nao seja required
        if(this.expense.transfer!=0){
            return true;
        }else{
            return false;
        }
    }
 }

 const isMBEntityValid= function isMBEntityValid(value, expense) {
    return (/^$|^\d{5}$/).test(value) || (!(expense.type=='mb' && expense.transfer==0));  
    //validação tem de devolver true caso este campo nao seja required, daí esta condição 'OR'
 }

 const isMBPaymentValid= function isMBPaymentValid(value, expense) {
    return (/^$|^\d{9}$/).test(value) || (!(expense.type=='mb' && expense.transfer==0));  
    //validação tem de devolver true caso este campo nao seja required, daí esta condição 'OR'
 }

export default {
    data() {
        return {
            hasError: false,
            valid_Expense: true,
            expense: {
                transfer:"",
                value:"",
                category:"",
                description:"",
                type:"",
                iban:"",
                mb_entity_code:"",
                mb_payment_reference:"",
                destination_email:"",
                source_description:""
            },
            categories: []
        };
    },
    mounted() {
        axios.get('api/movements/categories', this.$parent.auth).then(response => {
            this.categories = response.data.data;
        });
    },
    methods: {
        createExpense: function() {
            if (!this.$v.$invalid) {
                this.hasError = false;
                
                axios.post("api/movements/expense", this.expense, this.$parent.auth).then(response => {
                    if(response.data == '1'){
                        this.valid_Expense = true;
                        this.$parent.showSuccess = true;
                        this.$parent.successMessage = "Expense registered with success";
                        this.$parent.movementExpense = false;

                        if(this.expense.transfer == 1){
                            this.$socket.emit('expense_movement', this.expense);
                        }
                        
                    }else{
                        this.valid_Expense = false;
                    }
                    
                }).catch(error => {
                    console.log(error.response);
                });
            }else{
                this.hasError = true;
            }
        },
        cancelCreation: function() {
            this.$parent.movementExpense = false;
        }
    },
    validations: {
        expense: { 
            value: { required, numeric, between: between(0.01, 5000.00) },
            transfer: { required },
            category: { required },
            description: { required },
            // ####### IF PAYMENT TO EXTERNAL ENTITY
            type: { isTypeValid, requiredIf: requiredIf((expense) => {
                if(expense.transfer==0){
                    return true;
                }else{
                    return false;
                }
            })},
            iban: { isIBANvalid, requiredIf: requiredIf((expense) => {
                if(expense.type=='bt' && expense.transfer==0){
                    return true;
                }else{
                    return false;
                }
            })},
            mb_entity_code: { numeric, isMBEntityValid, requiredIf: requiredIf((expense) => {
                if(expense.type=='mb' && expense.transfer==0){
                    return true;
                }else{
                    return false;
                }
            })},
            mb_payment_reference: { numeric, isMBPaymentValid, requiredIf: requiredIf((expense) => { 
                if(expense.type=='mb' && expense.transfer==0){
                    return true;
                }else{
                    return false;
                }
            })},
            // ####### IF TRANSFER
            destination_email: { email, requiredIf: requiredIf((expense) => {
                if(expense.transfer==1){
                    return true;
                }else{
                    return false;
                }
            })},
            source_description: { requiredIf: requiredIf((expense) => {
                if(expense.transfer==1){
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