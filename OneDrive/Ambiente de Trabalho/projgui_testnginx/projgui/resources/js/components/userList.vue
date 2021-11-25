<template>
	<div>
    <nav-bar></nav-bar>
      <table class="table table-striped">
        <thead>
          <tr>
            <th>
              <label for="inputType">Type User</label>
              <br />
              <select class="browser-default custom-select" v-model="auth.params.type">
                <option value="u">User</option>
                <option value="a">Admin</option>
                <option value="o">Operator</option>
              </select>
            </th>
            <th>
              <label for="inputSource">Email</label>
              <input
                type="text"
                class="form-control"
                v-model="auth.params.email"
                id="inputSource"
                placeholder="Email"
              />
            </th>

              <th>
              <label for="inputName">Name</label>
              <input
                type="text"
                class="form-control"
                v-model="auth.params.name"
                id="inputName"
                placeholder="Name"
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
					<th>Type</th>
					<th>Name</th>
					<th>Email</th>
          <th>Photo</th>
                <!-- para os users -->
                <th>Account Status</th>
                <th>Balance</th>
                <th>Change Status</th>
                <th>Delete</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="user in users"  :key="user.id">
					<td>{{ user.type }}</td>
					<td>{{ user.name }}</td>
          <td>{{ user.email }}</td>
          <td><img :src="'/storage/fotos/'+user.photo"></td>
              <!-- para os users -->
              <td v-if="user.active==1">Active</td>
              <td v-else>Inactive</td>
              <td v-if="user.balance > 0">Has Money</td>
              <td v-else>Empty</td>
                  <!-- ADMIN actions -->
                  <td v-if="user.active==1">
                      <a class="btn btn-sm btn-warning" v-on:click.prevent="deactivateUser(user)">Deactivate</a>
                  </td>
                  <td v-else>
                      <a class="btn btn-sm btn-success" v-on:click.prevent="activateUser(user)">Activate</a>
                  </td>
                  <td v-if="user.type=='o' || (user.type=='a' && user.id != $root.$data.currentUser.id)">
                      <a class="btn btn-sm btn-danger" v-on:click.prevent="deleteUser(user)">Delete</a>
                  </td>
                  <td v-else></td>
				</tr>
			</tbody>
		</table>
		
		<p>Current page: {{ currentPage }}</p>
    	<v-pagination v-model="currentPage" :page-count="maxPages" @input="nextPage"></v-pagination>
	</div>				
</template>

<script type="text/javascript">

    import vPagination from 'vue-plain-pagination';
    import Navbar from './navbar.vue';

	export default {
		components: { vPagination, 'nav-bar': Navbar },
		data: function(){
			return {
				users: [], 
				showColumns: false,
				currentPage: 1,
                maxPages:1000000,
                auth: {
                    params:{
                        id:"",
                        name:"",
                        email:"",
                        active:"",
                        type:"",
                        nif:""
                    },
                    headers: {
                        'Accept': 'application/json',
                        'Authorization': 'Bearer ' + this.$root.$data.access_token
                    }
                }			
			}
		},
		methods: {
    cancelFilter: function() {
       for (const key in this.auth.params) {
        if (this.auth.params.hasOwnProperty(key)) {
           this.auth.params[key] = "";
          
        }
      }
      this.getUsers()
    },
    filter: function() {
      axios
        .get("api/users?page=" + this.currentPage, this.auth)
        .then(response => {
          this.users = response.data.data;
        });
    },
			getUsers: function(){
				axios.get('api/users?page='+this.currentPage,  this.auth).then(response => {
					this.users = response.data.data;
				});
			},
			nextPage: function(){
				axios.get('api/users?page='+this.currentPage, this.auth).then(response => {
					this.users = response.data.data;
				});
      },
      activateUser: function(user){
				axios.patch('api/users/active/yes/'+user.id, user, this.auth).then(response => {
                    alert("User activated!");
                    this.getUsers();
				});
      },
      deactivateUser: function(user){
        if(user.balance > 0){
            alert("Can't deactivate user with balance!");
        }else{
            axios.patch('api/users/active/no/'+user.id, user, this.auth).then(response => {
                alert("User deactivated!");
                this.getUsers();
            });
        }
      },
      deleteUser: function(user){
          if(user.type=='o' || (user.type=='a' && user.id != this.$root.$data.currentUser.id)){
              axios.delete('api/users/delete/'+user.id, this.auth).then(response => {
                  alert("User deleted!");
                  this.getUsers();
              });
          }else{
              alert("Delete not allowed on this type of user!");
          }
			}
		},
		mounted() {
			axios.get('api/users/count', this.auth).then(response => {
				this.maxPages = Math.ceil(response.data.total_users / 30);
				this.getUsers();
			});
    }
	}
</script>

<style scoped>	
p {
	font-size: 2em;
	text-align: center;
}
tr.activerow {
  		background: #123456  !important;
  		color: #fff          !important;
}
img{
    height: 128px;
    width: 128px;
    
}
</style>