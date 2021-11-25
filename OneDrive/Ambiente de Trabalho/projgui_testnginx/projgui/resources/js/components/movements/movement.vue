<template>
  <div>
    <nav-bar></nav-bar>
    <div class="jumbotron" v-if="this.$root.$data.currentUser.type == 'u'">
      <h1>Virtual Wallet: {{ wallet }}</h1>
      <h1>Current Balance: {{ balance }} €</h1>
    </div>

    <div class="alert alert-success" v-if="showSuccess">
      <button type="button" class="close-btn" v-on:click="showSuccess=false">&times;</button>
      <strong>{{ successMessage }}</strong>
    </div>

    <button
      v-if="this.$root.$data.currentUser.type == 'o'"
      v-on:click="movementIncome = !movementIncome"
      type="button"
      class="btn btn-info"
    >Create Income</button>

    <movement-income v-if="movementIncome && this.$root.$data.currentUser.type == 'o'"></movement-income>

    <button
      v-if="this.$root.$data.currentUser.type == 'u'"
      v-on:click="movementExpense = !movementExpense"
      type="button"
      class="btn btn-info"
    >Register Expense</button>

    <movement-expense v-if="movementExpense && this.$root.$data.currentUser.type == 'u'"></movement-expense>

    <movement-list v-if="this.$root.$data.currentUser.type == 'u'" ref="movementsListRef"></movement-list>

    <movement-edit
      :currentMovement="currentMovement"
      v-if="this.$root.$data.currentUser.type == 'u' && currentMovement"
    ></movement-edit>
  </div>
</template>

<script type="text/javascript">
import MovementList from "./movementList.vue";
import MovementIncome from "./movementIncome.vue";
import MovementExpense from "./movementExpense.vue";
import MovementEdit from "./movementEdit.vue";
import Navbar from "../navbar.vue";
export default {
  data: function() {
    return {
      title: "List Movements",
      showSuccess: false,
      successMessage: "",
      movementIncome: true,
      movementExpense: false,
      balance: "",
      wallet: "",
      auth: {
        params: {
          id: "",
          type: "",
		      dateSup: "",
		      dateInf: "",
          category: "",
          typePayment: "",
          source: "",
          destination: ""
        },
        headers: {
          Accept: "application/json",
          Authorization: "Bearer " + this.$root.$data.access_token
        }
      },
      currentMovement: null
    };
  },
  mounted() {
    if (this.$root.$data.currentUser != undefined) {
      axios.get("api/balance", this.auth).then(response => {
        this.balance = response.data.balance;
        this.wallet = response.data.email;
      });
    } else {
      this.balance = null;
    }
  },
  methods: {
    editMovement: function(movement) {
      this.currentMovement = movement;
      this.showSuccess = false;
    }
  },
  components: {
    "movement-list": MovementList,
    "movement-income": MovementIncome,
    "movement-expense": MovementExpense,
    "movement-edit": MovementEdit,
    "nav-bar": Navbar
  }
};
</script>

<style scoped>
p {
  font-size: 2em;
  text-align: center;
}
</style>