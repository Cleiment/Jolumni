<template>
<div>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <a class="navbar-brand">Navbar</a>
    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#">Link</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Dropdown
          </a>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="#">Action</a>
            <a class="dropdown-item" href="#">Another action</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#">Something else here</a>
          </div>
        </li>
        <li class="nav-item">
          <a class="nav-link disabled" href="#">Disabled</a>
        </li>
      </ul>
      <div class="col-2" v-if="!loggedIn">
        <div class="justify-content-around d-flex">
          <button class="btn btn-primary">Login</button>
          <button class="btn btn-primary">Register</button>
        </div>
      </div>
      <div class="col-1" v-else-if="loggedIn">
          <button @click="logout" class="btn btn-primary">Logout</button>
        </div>
    </div>
  </nav>
  <div class="container">
    <div v-if="activeScreen == 'Login'">
      <Login @loginUser="loginUser($event)" @logout="logout" :loggedIn="loggedIn"/>
    </div>
    <div v-else-if="activeScreen == 'Register'">
      <Register :msg="'Register ni bang'" />
    </div>
  </div>
</div>
</template>

<script>
import Login from './components/Login.vue'
import Register from './components/Register.vue'

var axios = require('axios');

export default {
  name: 'App',
  data(){
    return{
      activeScreen : 'Login',
      loggedIn: false,
      user: {},
      token: ''
    }
  },
  mounted(){
    this.checkLoggedIn()
  },
  components: {
    Login, Register
  },
  methods:{
    checkLoggedIn(){
      var log = JSON.parse(localStorage.getItem('loggedIn'));
      this.loggedIn = log.log;
      // console.log('status logged in : ' + this.loggedIn);

      if(this.loggedIn){
        this.user = JSON.parse(localStorage.getItem('user_data'));
        this.token = localStorage.getItem('token');
      }
    },
    loginUser(rs){
      localStorage.setItem('user_data', JSON.stringify(rs.user));
      localStorage.setItem('token', rs.token);
      localStorage.setItem('loggedIn', JSON.stringify({ 'log' : true }));
      this.checkLoggedIn();
    },
    async logout(){
      // console.log(this.token);
      await axios.post('http://localhost:8000/api/logout', {}, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(function () {
        localStorage.removeItem('user_data');
        localStorage.removeItem('token');
        localStorage.setItem('loggedIn', JSON.stringify({ 'log' : false }));
      })
      .catch(function (e){
        console.log(e)
      });
      this.checkLoggedIn();
    }
  }
}
</script>

<style>
#app {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}
.navbar-dark{
  color: white;
}
nav{
  margin-bottom: 35px;
}
</style>
