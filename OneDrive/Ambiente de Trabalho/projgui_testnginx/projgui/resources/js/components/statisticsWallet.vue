<template>
  <div>
    <navbar></navbar>
    <div class="jumbotron">
      <h1>{{title}}</h1>
    </div>
    <h3 v-if="!this.dados">
      We are getting your data.
      <br />Please Wait.
    </h3>
    <button v-if="this.dados" v-on:click="expensesCategory()">Expenses by Category</button>
    <button v-if="this.dados" v-on:click="incomeCategory()">Income by Category</button>
    <div id="app">
      <canvas id="chart"></canvas>
    </div>
  </div>
</template>

<script>
import planetChartData1 from "../chart-data.js";
import Chart from "chart.js";
import Navbar from "./navbar.vue";
export default {
  data: function() {
    return {
      title: "Statistics",
      planetChartData: planetChartData1,
      movements: [],
      dados: false,
      value: [],
      date: [],
      categories:{
        names:["salary","bonus","royalties","interests","gifts","dividends","sales","loan repayment","loan","other income"],
        values:[],
      },
      categorias: {
        names: [
          "groceries",
          "restaurant",
          "clothes",
          "shoes",
          "school",
          "services",
          "electricity",
          "phone",
          "fuel",
          "mortgage_payment",
          "car_payment",
          "entertainement",
          "gadget",
          "computer",
          "vacation",
          "hobby",
          "loan_repayment",
          "loan",
          "other_expense"
        ],
        values: [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0]
        //groceries:0,
        //restaurant:0,},
        /* clothes:[],
        shoes:[],
        school:[],
        services:[],
        electricity:[],
        phone:[],
        fuel:[],
        mortgage_payment:[],
        car_payment:[],
        entertainement:[],
        gadget:[],
        computer:[],
        vacation:[],
        hobby:[],
        loan_repayment:[],
        loan:[],
        other_expense:[],*/
      },
      auth: {
        headers: {
          Accept: "application/json",
          Authorization: "Bearer " + this.$root.$data.access_token
        }
      }
    };
  },
  methods: {
     expensesCategory() {
      let cont = 0;
      /*this.movements.forEach(movement => {
        if (movement.date.substring(0, 4) < "2015") {
          cont++;
          this.value[cont] = movement.value;
          this.date[cont] = movement.date;
        }
      });
      */
      this.movements.forEach(movement => {
        if ("restaurant" == movement.category_id) {
          this.categorias.values[0] += parseInt(movement.value);
        }
        if ("groceries" == movement.category_id) {
          this.categorias.values[1] += parseInt(movement.value);
        }
        if ("shoes" == movement.category_id ) {
        this.categorias.values[2] += parseInt(movement.value);
       }
       if ("school" == movement.category_id ) {
         this.categorias.values[3] += parseInt(movement.value);
       }
       if ("services" == movement.category_id ) {
          this.categorias.values[4] += parseInt(movement.value);
       }
       if ("electricity" == movement.category_id ) {
          this.categorias.values[5] += parseInt(movement.value);
       }
       if ("phone" == movement.category_id ) {
          this.categorias.values[6] += parseInt(movement.value);
       }
       if ("fuel" == movement.category_id ) {
          this.categorias.values[7] += parseInt(movement.value);
       }
       if ("motgage payment" == movement.category_id ) {
          this.categorias.values[8] += parseInt(movement.value);
       }
       if ("car payment" == movement.category_id ) {
          this.categorias.values[9] += parseInt(movement.value);
       }
       if ("entertainement" == movement.category_id ) {
          this.categorias.values[10] += parseInt(movement.value);
       }
       if ("gadget" == movement.category_id ) {
         this.categorias.values[11] += parseInt(movement.value);
       }
       if ("computer" == movement.category_id ) {
          this.categorias.values[12] += parseInt(movement.value);
       }
       if ("vacation" == movement.category_id ) {
          this.categorias.values[13] += parseInt(movement.value);
       }
       if ("hobby" == movement.category_id ) {
          this.categorias.values[14] += parseInt(movement.value);
       }
       if ("loan repayment" == movement.category_id ) {
          this.categorias.values[15] += parseInt(movement.value);
       }
       if ("loan" == movement.category_id ) {
          this.categorias.values[16] += parseInt(movement.value);
       }
       if ("other expense" == movement.category_id ) {
          this.categorias.values[17] += parseInt(movement.value);
       }
       if ("clothes" == movement.category_id ) {
          this.categorias.values[18] += parseInt(movement.value);
       }
      });
            
      this.graph("chart",this.categorias.names,this.categorias.values);
      console.log(this.categorias)
    },
    incomeCategory() {
      let cont = 0;
      /*this.movements.forEach(movement => {
        if (movement.date.substring(0, 4) < "2015") {
          cont++;
          this.value[cont] = movement.value;
          this.date[cont] = movement.date;
        }
      });
      */
     this.movements.forEach(movement => {
        if ("salary" == movement.category_id) {
          this.categorias.values[0] += parseInt(movement.value);
        }
        if ("bonus" == movement.category_id) {
          this.categorias.values[1] += parseInt(movement.value);
        }
        if ("royalties" == movement.category_id ) {
        this.categorias.values[2] += parseInt(movement.value);
       }
       if ("interests" == movement.category_id ) {
         this.categorias.values[3] += parseInt(movement.value);
       }
       if ("gifts" == movement.category_id ) {
          this.categorias.values[4] += parseInt(movement.value);
       }
       if ("dividends" == movement.category_id ) {
          this.categorias.values[5] += parseInt(movement.value);
       }
       if ("sales" == movement.category_id ) {
          this.categorias.values[6] += parseInt(movement.value);
       }
       if ("loan repayment" == movement.category_id ) {
          this.categorias.values[7] += parseInt(movement.value);
       }
       if ("loan" == movement.category_id ) {
          this.categorias.values[8] += parseInt(movement.value);
       }
       if ("other income" == movement.category_id ) {
          this.categorias.values[9] += parseInt(movement.value);
       }
       
     });
            
      this.graph("chart",this.categories.names,this.categorias.values);
      console.log(this.categorias)
    },
    graph(chartId,names, values) {
      const Data = {
        type: "bar",
        data: {
          labels: names,
          datasets: [
            {
              // one line graph
              label: "expenses",
              data: values,
          
              borderWidth: 3
            }
          ]
        },
        options: {
          responsive: true,
          lineTension: 1,
          scales: {
            yAxes: [
              {
                ticks: {
                  beginAtZero: true,
                  padding: 25
                }
              }
            ]
          }
        }
      };
      const ctx = document.getElementById(chartId);
      const myChart = new Chart(ctx, {
        type: Data.type,
        data: Data.data,
        options: Data.options
      });
    },

    getMovements: function() {
      axios.get("api/movements", this.auth).then(response => {
        this.movements = response.data.data;
        console.log("****MOVIMENTOS***");
        console.log(this.movements);
        this.dados = true;
       /* axios.get('api/movements/categories', this.auth).then(response => {
            this.categories.names = response.data.data;
            console.log("ola")
            console.log(this.categories.names)
        });*/
      });
    }
  },

  components: {
    navbar: Navbar
  },
  created() {
    this.getMovements();
    
  },
   mounted() {
        
    },
};
</script>