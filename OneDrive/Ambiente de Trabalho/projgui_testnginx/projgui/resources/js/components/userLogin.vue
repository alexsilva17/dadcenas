<template>
  <div>
    <navbar></navbar>
    <div class="jumbotron">
      <h2>Login</h2>
      <div class="form-group">
        <label for="inputEmail">Email</label>
        <input
          type="email"
          class="form-control"
          v-model="user.email"
          name="email"
          id="inputEmail"
          placeholder="Email address"
        />
      </div>
      <div class="form-group">
        <label for="inputPassword">Password</label>
        <input
          type="password"
          class="form-control"
          v-model="user.password"
          name="password"
          id="inputPassword"
          placeholder="Password"
        />
      </div>
      <div class="form-group">
        <a class="btn btn-primary" v-on:click.prevent="login">Login</a>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript">
import Navbar from "./navbar.vue";
export default {
  data: function() {
    return {
      user: {
        email: "",
        password: ""
      }
    };
  },
  methods: {
    login() {
      axios.post("api/login", this.user).then(response => {
        console.log(response.data);
        //sessionStorage.setItem('token', response.data.access_token);
        this.$root.$data.access_token = response.data.access_token;
        this.$root.$data.currentUser = this.user;
        
        //console.log(this.$root.$data.currentUser);
        //console.log(this.$root.$data.access_token);
        
        axios.get("api/user", {
          headers: {
            Accept: "application/json",
            Authorization: "Bearer " + this.$root.$data.access_token
          }
        }).then(response => {
            this.$root.$data.currentUser = response.data
            this.$socket.emit('user_enter', response.data);
        });

        this.$router.push("/welcome");
      });
    }
  },

  components: {
    navbar: Navbar
  }
};
</script>