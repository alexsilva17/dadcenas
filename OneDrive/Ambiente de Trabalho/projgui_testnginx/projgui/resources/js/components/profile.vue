<template>
  <div>
    <navbar></navbar>
    <div class="jumbotron">
      <h2>Profile</h2>
      <div class="form-group" :class="{ 'error': $v.user.name.$error }">
        <label for="inputName">Name</label>
        <input
          type="text"
          class="form-control"
          v-model="user.name"
          name="name"
          id="inputName"
          placeholder="Name"
        />
        <div class="alert alert-warning" v-if="!$v.user.name.required">Name is Required </div>
        <div class="alert alert-warning" v-if="!$v.user.name.strongName && $v.user.name.required">Name Invalid</div>
      </div>
      <div class="form-group">
        <label for="inputEmail">Email</label>
        <input
          type="email"
          class="form-control"
          disabled
          v-model="user.email"
          name="email"
          id="inputEmail"
          placeholder="Email address"
        />
      </div>
      <div class="form-group" :class="{ 'error': $v.user.password.$error }">
        <label for="inputPasswordAtual">Atual Password</label>
        <input
          type="password"
          class="form-control"
          v-model="user.password"
          name="password"
          id="inputPasswordAtual"
          placeholder="Password"
        />
        <div
          class="alert alert-warning"
          v-if="!$v.user.password.minLength"
        >Password needs to Have more than 3 characteres</div>
      </div>
      
      <div class="form-group" :class="{ 'error': $v.user.passwordNova.$error }">
        <label for="inputPasswordNova">New Password</label>
        <input
          type="password"
          class="form-control"
          v-model="user.passwordNova"
          name="password"
          id="inputPasswordNova"
          placeholder="Password"
        />
        <div
          class="alert alert-warning"
          v-if="!$v.user.passwordNova.minLength"
        >Password needs to Have more than 3 characteres</div>
      </div>
      
      <div class="form-group" :class="{ 'error': $v.user.passwordConfirmacao.$error }">
        <label for="inputPasswordConfirmacao">Confirm Password</label>
        <input
          type="password"
          class="form-control"
          v-model="user.passwordConfirmacao"
          name="password"
          id="inputPasswordConfirmacao"
          placeholder="Password"
        />
        <div
          class="alert alert-warning"
          v-if="!$v.user.passwordConfirmacao.minLength"
        >Password needs to Have more than 3 characteres</div>
        <div
          class="alert alert-warning"
          v-if="!$v.user.passwordConfirmacao.sameAsPassword"
        >New Password and Confirmation need to match</div>
      </div>
      
      <div class="form-group">
        <label for="inputPhoto">Photo</label>
        <br />
        <img :src="'/storage/fotos/'+$root.$data.currentUser.photo" />

        <input
          type="file"
          class="form-control"
          v-on:change="onImageChange"
          name="photo"
          id="inputPhoto"
          placeholder="Photo"
        />
        <div class="alert alert-warning" v-if="erroPhoto">{{erroPhoto}}</div>
      </div>

    <div
      class="alert alert-warning"
      v-if="this.showSuccess == false"
    >Something went wrong verify all the fields!!!</div>
    <div class="alert alert-success" v-if="this.showSuccess == true">User Created!!!</div>

      <div class="form-group">
        <a class="btn btn-primary" v-on:click.prevent="update">Save</a>
         <a class="btn btn-light" v-on:click.prevent="cancelUpdate()">Cancel</a>
      </div>
    </div>
  </div>
</template>

<script type="text/javascript">
import Navbar from "./navbar.vue";
import {
  required,
  numeric,
  between,
  email,
  helpers,
  requiredIf,
  minLength,
  validateIf,
  sameAs
} from "vuelidate/lib/validators";
export default {
  data: function() {
    return {
     showSuccess: null,
      erroPhoto: "",
       auth: {
        headers: {
          Accept: "application/json",
          Authorization: "Bearer " + this.$root.$data.access_token
        }
      },
      user: {
      }
    };
  },
  validations: {
    user: {
      name: {
        required,
        strongName(name) {
          return (
            /^[a-zA-ZÀ-ú\s]+$/.test(name) && name.length >= 3 // checks for a-z
          );
        }
      },
      password: {  minLength: minLength(3) },
      passwordNova: {  minLength: minLength(3) },
      passwordConfirmacao: { sameAsPassword: sameAs('passwordNova'),minLength: minLength(3) },
      nif: {required,
        strongNif(nif) {
          return /^[0-9]{9}$/.test(nif);
        }
      },
      
     
    }
  },
  methods: {
    onImageChange(e) {
      let files = e.target.files || e.dataTransfer.files;

      if (!files.length) {
        this.user.photo = "";
        return;
      }
      console.log(
        files[0].name.substring(files[0].name.length - 3, files[0].name.length)
      );
      if (
        files[0].name.substring(
          files[0].name.length - 3,
          files[0].name.length
        ) == "jpg" ||
        files[0].name.substring(
          files[0].name.length - 3,
          files[0].name.length
        ) == "png"
      ) {
        this.createImage(files[0]);
        this.erroPhoto = "";
      } else {
        this.erroPhoto = "The file must be of type png or jpg";
      }
    },
    createImage(file) {
      this.user.photoName = file.name;
      let reader = new FileReader();
      let vm = this.user;
      reader.onload = e => {
        vm.photo = e.target.result;
      };

      reader.readAsDataURL(file);
      console.log(this.$root.$data.currentUser)
      console.log(this.photo)
    },
    update() {
     if (!this.$v.user.$invalid || !this.erroPhoto) {
       console.log(this.user)
      axios
        .put("api/users/" + this.$root.$data.currentUser.id, this.user, this.auth)
        .then(response => {
           this.showSuccess = true
                 axios.get("api/user", {
          headers: {
            Accept: "application/json",
            Authorization: "Bearer " + this.$root.$data.access_token
          }
        }).then(response => {
            console.log(response.data)
            this.$root.$data.currentUser = response.data
           
        });
	        
          
        })
        .catch(error => {
        this.showSuccess = false});
     }else {
        this.showSuccess = false;
      }
    },
     cancelUpdate: function(){
	        axios.get("api/user", {
          headers: {
            Accept: "application/json",
            Authorization: "Bearer " + this.$root.$data.access_token
          }
        }).then(response => {
            console.log(response.data)
            this.$root.$data.currentUser = response.data
            this.$router.push('/welcome')
        });
	        }
  },
  created() {
    this.user = this.$root.$data.currentUser
    
    
    console.log(this.user);
  },

  components: {
    navbar: Navbar
  }
};
</script>

<style scoped>
img{
    height: 128px;
    width: 128px;
    
}

</style>