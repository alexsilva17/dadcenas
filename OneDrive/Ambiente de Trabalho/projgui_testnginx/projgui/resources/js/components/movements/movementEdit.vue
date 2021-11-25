<template>
<div>
    <div class="jumbotron">
        <h2>Edit Movement</h2>

        <div class="form-group" >
            <label for="inputType">Category</label>
            <br>
            <select class="browser-default custom-select" v-model="currentMovement.category_id">
                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
            </select>
        </div>

        <div class="form-group" >
            <label for="inputDescription">Description</label>
            <input
                type="text"
                class="form-control"
                v-model="currentMovement.description"
                name="description"
                id="inputDescription"
                placeholder="Description"
            >
        </div>
        
        <div class="alert alert-warning" v-if="currentMovement.type == 'e' && hasError">Both fields are required!</div>

        <div class="form-group">
            <a class="btn btn-success" v-on:click.prevent="editExpense()">Save</a>
            <a class="btn btn-light" v-on:click.prevent="cancelEdit()">Close</a>
        </div>
        
    </div>
</div>
</template>

<script type="text/javascript">
import { requiredIf } from 'vuelidate/lib/validators';

export default {
    props: ['currentMovement'],
    data() {
        return {
            hasError: false,
            categories: []
        };
    },
    mounted() {
        axios.get('api/movements/categories', this.$parent.auth).then(response => {
            this.categories = response.data.data;
        });
    },
    methods: {
        editExpense: function() {
                if(this.currentMovement.category_id == null || this.currentMovement.description == ""){
                    this.hasError = true;
                }
                else{
                    this.hasError = false;
                    axios.put('api/movements/'+this.currentMovement.id, this.currentMovement, this.$parent.auth)
                    .then(response => {
                        this.$parent.showSuccess = true;
                        this.$parent.successMessage = "Movement saved";
                        this.$parent.$refs.movementsListRef.editingMovement = null;
                        this.$parent.currentMovement = null;
                    })
                    .catch(error => {
                        console.log(error.response);
                    });
                }
        },
        cancelEdit: function() {
            this.$parent.currentMovement = null;
            this.$parent.showSuccess = false;
            this.$parent.$refs.movementsListRef.editingMovement = null;
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