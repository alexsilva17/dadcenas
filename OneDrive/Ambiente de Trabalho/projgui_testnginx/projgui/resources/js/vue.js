/*jshint esversion: 6 */

/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

import VueRouter from 'vue-router';
Vue.use(VueRouter);

import Vuelidate from 'vuelidate';

Vue.use(Vuelidate);

/* import VueSocketIO from "vue-socket.io";
Vue.use(
    new VueSocketIO({
        debug: false,
        connection: "http://127.0.0.1:8080"
    })
); */

import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/index.css';
 
Vue.use(VueToast);

const welcome = Vue.component('welcome', require('./components/welcomePage.vue').default);
const login = Vue.component('login', require('./components/userLogin.vue').default);
const movements = Vue.component('movements', require('./components/movements/movement.vue').default);
const register = Vue.component('login', require('./components/register.vue').default);
const create = Vue.component('login', require('./components/create.vue').default);
const profile = Vue.component('login',require('./components/profile.vue').default);
const statistics = Vue.component('login',require('./components/statisticsWallet.vue').default);
const users = Vue.component('users', require('./components/userList.vue').default);

const routes = [
  { path: '/', redirect: '/welcome' },
  { path: '/welcome', component: welcome },
  { path: '/login', component: login },
  { path: '/profile', component: profile, meta:{requiresAuth: true} },
  { path: '/movements', component: movements , meta:{requiresAuth: true}},
  { path: '/statistics', component: statistics , meta:{requiresAuth: true}},
  { path: '/register', component: register },
  { path: '/create', component: create, meta:{requiresAdmin: true} },
  { path: '/users', component: users,meta:{requiresAdmin: true} },
];


const router = new VueRouter({
  routes:routes 
});

router.beforeEach((to, from, next) => {
  if (to.matched.some(record => record.meta.requiresAdmin)) {
    if (app.$root.access_token == null){
      next('/login')
   }else {
    console.log(app.$root.currentUser.type )
    if(app.$root.currentUser.type == 'a'){
      console.log(app.$root.currentUser)
      console.log(app.$root.currentUser.type)
     next()
      }else{
    next('/welcome')}   }
  }else{
  if (to.matched.some(record => record.meta.requiresAuth)) {
  if (app.$root.access_token == null){
     next('/login')
  }else{ 
    next()
  }
}else{next()}
  }
})

const app = new Vue({
  router,
  
  data:{
    access_token:  null,
    currentUser: null,
    
    
  },
  computed:{
    loggedIn(){
        return this.$root.$data.access_token !=null ;
    }
  }
}).$mount('#app');