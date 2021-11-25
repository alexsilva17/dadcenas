<template>
  <div>
    <navbar></navbar>
    <div class="jumbotron">
      <h2>Register User</h2>
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
        <div class="alert alert-warning" v-if="!$v.user.name.required">Field is required</div>
        <div class="alert alert-warning" v-if="!$v.user.name.strongName && $v.user.name.required">Name Invalid</div>
      </div>

      <div class="form-group" :class="{ 'error': $v.user.password.$error }">
        <label for="inputPassword">Password</label>
        <input
          type="password"
          class="form-control"
          name="password"
          v-model="user.password"
          id="inputPassword"
          placeholder="Password"
        />
        <div class="alert alert-warning" v-if="!$v.user.password.required">Field is required</div>
        <div
          class="alert alert-warning"
          v-if="!$v.user.password.minLength"
        >Password needs to Have more than 3 characteres</div>
      </div>

      <div class="form-group" :class="{ 'error': $v.user.email.$error }">
        <label for="inputEmail">Email</label>
        <input
          type="text"
          class="form-control"
          v-model="$v.user.email.$model"
          name="email"
          id="inputEmail"
          placeholder="Email"
        />
        <div class="alert alert-warning" v-if="!$v.user.email.required">Field is required</div>
        <div class="alert alert-warning" v-if="!$v.user.email.email">E-mail format invalid.</div>
      </div>

       

      <div class="form-group" :class="{ 'error': $v.user.nif.$error }">
        <label for="inputNif">Nif</label>
        <input
          type="text"
          class="form-control"
          v-model="$v.user.nif.$model"
          name="nif"
          id="inputNif"
          placeholder="Nif"
          pattern="\d{0,9}"
          title="The NIF has to have 9 digits "
        />
        <div class="alert alert-warning" v-if="!$v.user.nif.required">NIF is Required </div>
        <div class="alert alert-warning" v-if="!$v.user.nif.strongNif && $v.user.nif.required">NIF Invalid</div>
        
      </div>

      <div class="form-group">
        <label for="inputPhoto">Photo</label>
        <input
          type="file"
          class="form-control"
          v-on:change="onImageChange"
          name="photo"
          id="inputPhoto"
          placeholder="Photo"
        />
         <div class="alert alert-warning" v-if="erroPhoto">{{erroPhoto}} </div>
      </div>
    </div>
    <div
      class="alert alert-warning"
      v-if="this.showSuccess == false"
    >Something went wrong verify all the fields!!!</div>
    <div class="alert alert-success" v-if="this.showSuccess == true">User Created!!!</div>
    <div class="form-group">
      <a class="btn btn-primary" v-on:click.prevent="saveUser()">Save</a>
      <a class="btn btn-light" v-on:click.prevent="cancelCreation()">Cancel</a>
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
  validateIf
} from "vuelidate/lib/validators";
export default {
  data() {
    return {
      showSuccess: null,
      successMessage: "",
      erroPhoto:"",
      auth: {
        headers: {
          Accept: "application/json",
          Authorization: "Bearer " + this.$root.$data.access_token
        }
      },
      user: {
        name: "",
        age: "",
        password: "",
        email: "",
        nif: "",
        type: "u",
        photo: "",
        photoName:""
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
      email: { required, email },
      password: { required, minLength: minLength(3) },
      nif: {required,
        strongNif(nif) {
          return /^[0-9]{9}$/.test(nif);
        }
      },
      
     
    }
  },
  components: {
    navbar: Navbar
  },
  methods: {
    //Recebe o Evento e e mete apenas o
    //file que aparece na posicao 0 da lista de files no  files
    // e envia-o para o create image que o transorma de array de informacao
    //para um url de imagem com a informacao da imagem lá que depois
    //a mete no this.user.photo
    onImageChange(e) {
      
      let files = e.target.files || e.dataTransfer.files;
      if (!files.length){ 
        this.user.photo = ""
        return};
      console.log(files[0].name.substring(files[0].name.length-3, files[0].name.length))
      if (files[0].name.substring(files[0].name.length-3, files[0].name.length) == "jpg" || files[0].name.substring(files[0].name.length-3, files[0].name.length) == "png") {
         this.createImage(files[0]);
         this.erroPhoto = ""
          
      }else{
        this.erroPhoto = "The file must be of type png or jpg"
      }
     
      
     
    },
    createImage(file) {
      this.user.photoName = file.name
      console.log("NOME DO FICHEIRO"+this.user.photoName)
      let reader = new FileReader();
      let vm = this.user;
      reader.onload = e => {
        vm.photo = e.target.result;
      };

      reader.readAsDataURL(file);
    },
    saveUser: function() {
      if (!this.$v.user.$invalid || !this.erroPhoto) {
        console.log(this.user)
        axios
          .post("api/users/regist", this.user, this.auth)
          .then(response => {
            Object.create(this.user, response.data.data);
            this.$emit("user-saved", this.user);
            console.log("Criou com sucesso");
            this.successMessage = [];
            this.showSuccess = true;
          })
          .catch(error => {
            this.showSuccess = false;
          });
      } else {
        this.showSuccess = false;
      }
    },
    cancelCreation: function() {
      this.$router.push('/welcome')
    }
  }
};
</script>

<style scoped>
</style>