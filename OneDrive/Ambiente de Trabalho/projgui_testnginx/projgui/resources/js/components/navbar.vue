<template>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: teal;">
  <div class="navbar-brand" href="#">
      <router-link class="navbar-brand" to="/welcome">Virtual Wallet</router-link>
  </div>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav">
    <ul class="navbar-nav mr-auto">
      <li v-if="!this.$root.loggedIn" class="nav-item">
        <router-link class="nav-link" to="/login">Login</router-link>
      </li>
      <li class="nav-item">
        <router-link class="nav-link" v-if="!this.$root.loggedIn" to="/register">Register</router-link>
      </li>
       <li class="nav-item">
        <router-link class="nav-link" v-if="this.$root.loggedIn" to="/statistics">Statistics</router-link>
      </li>
      <li class="nav-item">
        <router-link v-if="this.$root.loggedIn && this.$root.$data.currentUser.type == 'a'" class="nav-link" to="/create">Create</router-link>
      </li>
      <li v-if="this.$root.loggedIn" class="nav-item">
        <router-link class="nav-link" to="/profile">Profile</router-link>
      </li>
       <li  v-if="this.$root.loggedIn && this.$root.$data.currentUser.type != 'a'" class="nav-item">
        <router-link class="nav-link" to="/movements">Movements</router-link>
      </li>
      <li  v-if="this.$root.loggedIn && this.$root.$data.currentUser.type == 'a'" class="nav-item">
        <router-link class="nav-link" to="/users">Users</router-link>
      </li>
    </ul>
    <div class="right">
      <button class="btn btn-sm btn-danger" v-if="this.$root.loggedIn" v-on:click="this.logout">Logout</button>
    </div>
  </div>
</nav>
</template>
<script type="text/javascript">

export default {
    data:function () {
        return{
          auth: {
            headers: {
              'Accept': 'application/json',
              'Authorization': 'Bearer ' + this.$root.$data.access_token
            }
          },
          balance:""
        }
    },
    methods: {
            logout() {
                axios.post('api/logout', null, {
                  headers: {
              'Accept': 'application/json',
              'Authorization': 'Bearer ' + this.$root.$data.access_token
            }
                }).then(response => {
                        this.$root.$data.access_token = null;
                        this.$root.$data.currentUser = undefined;
                        this.$router.push('/welcome');
                    }
                )
            }
    },
}
</script>