<template>
  <div>
    <button
      v-on:click="showColumns = !showColumns"
      type="button"
      class="btn btn-info"
    >Show All Columns</button>
    <table class="table table-striped">
      <thead>
        <tr>
          <th>
            <label for="inputNif">Id</label>
            <input
              type="text"
              class="form-control"
              v-model="$parent.auth.params.id"
              id="inputNif"
              placeholder="Id"
            />
          </th>
          <th>
            <label for="inputDate">Date Inferior</label>
            <input type="date" class="form-control"
                  v-model="$parent.auth.params.dateInf"
                  id="inputDate"
                  placeholder="Id" >
          </th>
          <th>
            <label for="inputDate">Date Superior</label>
            <input type="date" class="form-control"
                  v-model="$parent.auth.params.dateSup"
                  id="inputDate"
                  placeholder="Id" >
          </th>
          <th>
            <label for="inputType">Category</label>
            <br />
            <select  class="browser-default custom-select" v-model="$parent.auth.params.category">
              <option value="1">groceries</option>
              <option value="2">restaurant</option>
              <option value="3">clothes</option>
              <option value="4">shoes</option>
              <option value="5">school</option>
              <option value="6">services</option>
              <option value="7">electricity</option>
              <option value="8">phone</option>
              <option value="9">fuel</option>
              <option value="10">mortgage_payment</option>
              <option value="11">car_payment</option>
              <option value="12">entertainement</option>
              <option value="13">gadget</option>
              <option value="14">computer</option>
              <option value="15">vacation</option>
              <option value="16">hobby</option>
              <option value="17">loan_repayment</option>
              <option value="18">loan</option>
              <option value="19">other_expense</option>
              <option value="20">salary</option>
              <option value="21">bonus</option>
              <option value="22">royalties</option>
              <option value="23">interests</option>
              <option value="24">gifts</option>
              <option value="25">dividends</option>
              <option value="26">sales</option>
              <option value="27">loan repayment</option>
              <option value="28">loan repayment</option>
              <option value="29">other income</option>
            </select>
          </th>
          <th>
            <label for="inputType">Type Payment</label>
            <br />
            <select class="browser-default custom-select" v-model="$parent.auth.params.typePayment">
              <option value="c">Cash</option>
              <option value="bt">Bank Transfer</option>
              <option value="mb">Mb Payment</option>
            </select>
          </th>
          <th>
            <label for="inputType">Type Expense</label>
            <br />
            <select class="browser-default custom-select" v-model="$parent.auth.params.type">
              <option value="e">e</option>
              <option value="i">i</option>
            </select>
          </th>
        </tr>
        <tr>
          <th>
            <label for="inputDest">Destination E-mail</label>
            <input
              type="text"
              class="form-control"
              v-model="$parent.auth.params.destination"
              id="inputDest"
              placeholder="Destination"
            />
          </th>
        </tr>
      </thead>
    </table>

    <div class="form-group">
      <a class="btn btn-primary" v-on:click="filter">Filter</a>
      <a class="btn btn-secondary" v-on:click="cancelFilter">Cancel</a>
    </div>

    <table class="table table-striped">
      <thead>
        <tr>
          <th>ID</th>
          <th>Type</th>
          <th>Email</th>
          <!-- the source or destination e-mail – only if the movement is part of a
          transfer between 2 virtual wallets-->
          <th>Type of Payment</th>
          <!-- only if movement is not a transfer -->
          <th>Category</th>
          <th>Date</th>
          <th>Start Balance</th>
          <th>End Balance</th>
          <th>Value</th>
          <th>Edit</th>
          <th v-if="showColumns">description</th>
          <th v-if="showColumns">source_description</th>
          <th v-if="showColumns">IBAN</th>
          <th v-if="showColumns">MB Entity Code</th>
          <th v-if="showColumns">MB Payment Ref</th>
          <th v-if="showColumns">Photo</th>
          <!-- (only for a transfer - photo of
          the user associated to the pair movement of the transfer-->
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="movement in movements"
          :key="movement.id"
          :class="{activerow: editingMovement === movement}"
        >
          <td>{{ movement.id }}</td>
          <td>{{ movement.type }}</td>
          <td v-if="movement.transfer == 1">{{ movement.wallet_id }}</td>
          <td v-else></td>
          <td v-if="movement.transfer == 0">{{ movement.type_payment }}</td>
          <td v-else></td>
          <td>{{ movement.category_id }}</td>
          <td>{{ movement.date }}</td>
          <td>{{ movement.start_balance }}</td>
          <td>{{ movement.end_balance }}</td>
          <td v-if="movement.type == 'e'">({{ movement.value }})</td>
          <td v-else>{{ movement.value }}</td>
          <td>
            <a class="btn btn-sm btn-primary" v-on:click.prevent="editMovement(movement)">Edit</a>
          </td>

          <!-------more -->
          <td v-if="showColumns">{{ movement.description }}</td>
          <td v-if="showColumns">{{ movement.source_description }}</td>
          <td v-if="showColumns">{{ movement.iban }}</td>
          <td v-if="showColumns">{{ movement.mb_entity_code }}</td>
          <td v-if="showColumns">{{ movement.mb_payment_reference }}</td>
          <td v-if="showColumns == true && movement.transfer == 1">
            <img :src="'/storage/fotos/'+movement.photo" />
          </td>
        </tr>
      </tbody>
    </table>

    <p>Current page: {{ currentPage }}</p>
    <v-pagination v-model="currentPage" :page-count="maxPages" @input="nextPage"></v-pagination>
  </div>
</template>

<script type="text/javascript">
import vPagination from "vue-plain-pagination";
	export default {
		components: { vPagination },
		data: function(){
			return {
				editingMovement: null,
				movements: [], 
				showColumns: false,
				currentPage: 1,
				maxPages:1000000
			}
		},
		methods: {
      cancelFilter: function() {
       for (const key in this.$parent.auth.params) {
        if (this.$parent.auth.params.hasOwnProperty(key)) {
           this.$parent.auth.params[key] = "";
        }
      }
      this.getMovements()
    },
      filter: function() {
      axios
        .get("api/movements?page=" + this.currentPage, this.$parent.auth)
        .then(response => {
          this.movements = response.data.data;
        });
    },
			getMovements: function(){
				axios.get('api/movements?page='+this.currentPage, this.$parent.auth).then(response => {
					this.movements = response.data.data;
				});
			},
			nextPage: function(){
				axios.get('api/movements?page='+this.currentPage, this.$parent.auth).then(response => {
					this.movements = response.data.data;
				});
			},
			editMovement: function(movement){
	            this.editingMovement = movement;
	            this.$parent.currentMovement = movement;
	        },
		},
		sockets: {
			expense_movement(value) {
				axios.get('api/balance', this.$parent.auth).then(response => {
					this.$parent.balance = response.data.balance;
					//se eu fiz transferencia pa outra wallet não quero ver esta notificação, daí o IF
					if(value != "me"){
						this.$toast.success(value + "€ got transfered to my wallet!", {
							duration: 20000
						});
					}
				});
				this.getMovements();
			},
			income_movement(value) {
				axios.get('api/balance', this.$parent.auth).then(response => {
					this.$parent.balance = response.data.balance;
					this.$toast.success("Received " + value + "€ from an income movement!", {
						duration: 20000
					});
				});
				this.getMovements();
			}
		},
		mounted() {
			axios.get('api/movements/count', this.$parent.auth).then(response => {
				this.maxPages = Math.ceil(response.data.total_movements / 30);
			});
			this.getMovements();
		}
	}
</script>

<style scoped>
p {
  font-size: 2em;
  text-align: center;
}
tr.activerow {
  background: #123456 !important;
  color: #fff !important;
}
img{
    height: 128px;
    width: 128px;
    
}

</style>