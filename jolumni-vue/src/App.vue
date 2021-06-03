<template>
<div>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Karla:wght@500&display=swap" rel="stylesheet"> 

  <div v-if="!loading">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <a class="navbar-brand">Jolumni</a>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
          <li class="nav-item" :class="activeH">
            <a style="cursor: pointer" class="nav-link" @click="changeScreen('Home')">Home</a>
          </li>
          <li class="nav-item" :class="activeL">
            <a style="cursor: pointer" class="nav-link" @click="changeScreen('Lowongan Kerja')">Lowongan Kerja</a>
          </li>
          <li class="nav-item" :class="activeK">
            <a style="cursor: pointer" class="nav-link" @click="changeScreen('Kegiatan')">Kegiatan</a>
          </li>
        </ul>
        <div class="col-2" v-if="!loggedIn">
          <div class="justify-content-around d-flex">
            <button @click="changeScreen('Login')" class="btn btn-primary">Login</button>
            <button @click="changeScreen('Register')" class="btn btn-primary">Register</button>
          </div>
        </div>
        <div class="navbar-nav col-2 text-right" v-else-if="loggedIn">
          <a style="cursor: pointer;width: 100%;color:white" class="nav-link" @click="changeToProfile(user.user_id)">Welcome, {{ user.nama_depan }}!</a>
          <!-- <button @click="logout" class="btn btn-primary">Logout</button> -->
        </div>
      </div>
    </nav>
    <div style="min-height:511px" class="container">
      <div class="row justify-content-around">
        <div class="col-9 bg-lr" :class="activeLR">
          <div v-if="activeScreen == 'Login'">
            <Login @loginUser="loginUser($event)" :loggedIn="loggedIn"/>
          </div>
          <div v-else-if="activeScreen == 'Register'">
            <Register @register="register" />
          </div>
          <div v-else-if="activeScreen == 'EditProfile'">
            <EditProfile :loggedIn="loggedIn" :token="token" :user="user" @goProfile="editedProfile" @back="changeToProfile(user.user_id)" @deleteAccount="deleteAccount"/>
          </div>
        </div>
        <div class="col-12 bg-lr" :class="activeHP">
          <div v-if="activeScreen == 'Home'">
            <Home :loggedIn="loggedIn" :user="user" :token="token" @toKegiatan="changeScreen('Kegiatan')" @toLowongan="changeScreen('Lowongan Kerja')" @toProfile="changeToProfile" @goLogin="changeScreen('Login')"/>
          </div>
          <div v-else-if="activeScreen == 'Lowongan Kerja'">
            <Lowongan />
          </div>
          <div v-else-if="activeScreen == 'Kegiatan'">
            <Kegiatan :loggedIn="loggedIn" :user="user" :token="token" @toProfile="changeToProfile" @goLogin="changeScreen('Login')"/>
          </div>
        </div>
        <div class="col-12" v-if="activeScreen == 'Profile'">
          <Profile :loggedIn="loggedIn" :profile_id="profile_id" :user_id="user.user_id" :token="token" @edit="changeScreen('EditProfile')" @goLogin="changeScreen('Login')" @logout="logout"/>
        </div>
      </div>
    </div>
    <footer class="footer py-4 bg-dark navbar-dark">
      <div class="container text-center">
        <p style="margin: 0;font-weight: 500">Copyright &copy; 2021, made by Cleimentinus Venti</p>
      </div>
    </footer>
  </div>
  <div v-else class="d-flex justify-content-center align-items-center" style="width: 100%; min-height: 500px">
    <div class="loader loader-default"></div>
  </div>
</div>
</template>

<script>
import Login from './components/Login.vue'
import Register from './components/Register.vue'
import Home from './components/Home.vue'
import Lowongan from './components/Lowongan.vue'
import Kegiatan from './components/Kegiatan.vue'
import Profile from './components/Profile.vue'
import EditProfile from './components/EditProfile.vue'

var axios = require('axios');

export default {
  name: 'App',
  data(){
    return{
      activeScreen : 'Login',
      loggedIn: false,
      activeH: '',
      activeL: '',
      activeK: '',
      activeLR: '',
      activeHP: '',
      token: '',
      user: {},
      profile_id: '',
      loading: false
    }
  },
  async mounted(){
    this.loading = true
    await this.checkLoggedIn();
    this.changeScreen('Home');
    this.loading = false
  },
  components: {
    Login, Register, Home, Lowongan, Kegiatan, Profile, EditProfile
  },
  methods:{
    changeToProfile(id){
      this.activeH = ''
      this.activeL = ''
      this.activeK = ''
      this.activeHP = ''
      this.activeLR = ''
      this.profile_id = id
      this.activeScreen = ''
      setTimeout(() => {
        this.activeScreen = 'Profile'
      }, 1);
      document.title = 'Profile'
    },
    changeScreen(screen){
      this.activeScreen = screen
      document.title = screen
      
      if (this.activeScreen == 'Home') {
        this.activeH = 'active'
        this.activeL = ''
        this.activeK = ''
        this.activeHP = 'hp'
        this.activeLR = ''
      }else if (this.activeScreen == 'Lowongan Kerja') {
        this.activeH = ''
        this.activeL = 'active'
        this.activeK = ''
        this.activeHP = 'hp'
        this.activeLR = ''
      }else if (this.activeScreen == 'Kegiatan') {
        this.activeH = ''
        this.activeL = ''
        this.activeK = 'active'
        this.activeHP = 'hp'
        this.activeLR = ''
      }else if (this.activeScreen == 'Login' || this.activeScreen == 'Register' || this.activeScreen == 'EditProfile'){
        this.activeH = ''
        this.activeL = ''
        this.activeK = ''
        this.activeHP = ''
        this.activeLR = 'lr'
      }else{
        this.activeH = ''
        this.activeL = ''
        this.activeK = ''
        this.activeHP = ''
        this.activeLR = ''
      }
    },
    async checkLoggedIn(){
      var log = JSON.parse(localStorage.getItem('loggedIn'));
      this.loggedIn = log.log;
      // console.log('status logged in : ' + this.loggedIn);

      if(this.loggedIn){
        this.token = localStorage.getItem('token');
        var res = {}
        await axios.post(`http://localhost:8000/api/getUser`, {}, {
          headers: {
            'Authorization': `Bearer ${this.token}`
          }
        })
        .then(rs => {
          res = rs.data
        })
        .catch(e => console.log(e))
        this.user = res
      }
    },
    loginUser(rs){
      // localStorage.setItem('user_data', JSON.stringify(rs.user));
      localStorage.setItem('token', rs.token);
      localStorage.setItem('loggedIn', JSON.stringify({ 'log' : true }));
      this.checkLoggedIn();
      this.changeScreen('Home');
      alert('Login Success!');
    },
    register(){
      alert("You have registered! Now you can login!");
      this.activeScreen = 'Login';
    },
    async logout(){
      if (confirm('Are you sure you want to logout?',)) {
        await axios.post('http://localhost:8000/api/logout', {}, {
          headers: {
            'Authorization': `Bearer ${this.token}`
          }
        })
        .then(function () {
          localStorage.removeItem('token');
          localStorage.setItem('loggedIn', JSON.stringify({ 'log' : false }));
        })
        .catch(function (e){
          console.log(e)
        });
        this.user = {}
        this.token = ''
        this.checkLoggedIn();
        alert('Logout berhasil!');
        this.changeScreen('Login');
      }
    },
    async editedProfile(){
      // localStorage.setItem('user_data', JSON.stringify(res));
      this.checkLoggedIn()
      this.changeScreen('Profile');
    },
    async deleteAccount(pass){
      var res = ''
      await axios.post(`http://localhost:8000/api/delete_account`, {
        'pass': pass
      }, {
        headers: {
          'Authorization': `Bearer ${this.token}`
        }
      })
      .then(rs => {
        if (rs.data.message){
          res = rs.data.message
        }
      })
      .catch(e => {
        console.log(e.response);
      })

      if (res != '') {
        alert(res)
        localStorage.removeItem('user_data');
        localStorage.removeItem('token');
        localStorage.setItem('loggedIn', JSON.stringify({ 'log' : false }));
        this.checkLoggedIn();
        this.changeScreen('Login');
      }
    }
  }
}
</script>

<style>
#app {
  font-family: 'Karla', sans-serif;
  font-size: 13px;
}
.navbar-dark{
  color: white;
}
nav{
  margin-bottom: 45px;
}
.bg-lr{
  border-radius: 5px;
  padding: 10px;
  border: 1px solid #dfdfdf;
  display: none;
}
.hp, .lr{
  display: block;
}
.footer {
  margin-top: 50px;
}
p{
  white-space: pre-wrap;
}
.card-img-top{
  height: 197px;
  background-size: cover;
  background-position: center;
}

.like{
  opacity: 80%;
  transition: 0.3s all ease;
  cursor: pointer;
}

.like:hover{
  opacity: 100%;
}

.loader {
  border-radius: 50%;
  width: 40px;
  height: 40px;
  animation: spin 1.3s linear infinite;
}

.loader-default{
  border: 5px solid #d4d4d4;
  border-top: 5px solid #343a40 ;
  border-bottom: 5px solid #343a40 ;
}

.loader-yellow{
  border: 5px solid #dfdfdf;
  border-top: 5px solid #fda50f ;
  border-bottom: 5px solid #fda50f ;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}
</style>
