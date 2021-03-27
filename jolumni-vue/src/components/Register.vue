<template>
  <div style="padding : 2px 5px">
    <h1>Register</h1>
    <hr>
    <div class="alert alert-danger" role="alert" v-if="errors.length != 0">
      <!-- <p class="alert-text" :key="err.message" v-for="err in errors" >{{ err }}</p> -->
      <ul>
        <li :key="err.message" v-for="err in errors">
          {{err}}
          <!-- <p>{{ err }}</p> -->
        </li>
      </ul>
    </div>
    <form @submit="register" action="http://localhost:8000/api/register" method="post">
      <div class="form-row">
        <div class="form-group col-md-6">
          <label for="first_name">First Name</label>
          <input type="text" class="form-control" id="first_name" v-model="first_name" placeholder="First Name">
        </div>
        <div class="form-group col-md-6">
          <label for="last_name">Last Name</label>
          <input type="text" class="form-control" id="last_name" v-model="last_name" placeholder="Last Name">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group col-md-4">
          <label for="email">Email</label>
          <input type="email" class="form-control" id="email" v-model="email" placeholder="Email">
        </div>
        <div class="form-group col-md-4">
          <label for="password">Password</label>
          <input type="password" class="form-control" id="password" v-model="password" placeholder="Password">
        </div>
        <div class="form-group col-md-4">
          <label for="jurusan">Jurusan</label>
          <select class="form-control" v-model="jurusan" name="jurusan" id="jurusan">
            <option value="TKJ" selected >Teknologi Komputer & Jaringan</option>
            <option value="AKL">Akuntansi</option>
            <option value="BDP">Pemasaran</option>
          </select>
        </div>
      </div>
      <div class="form-group text-center">
        <input class="form-control btn btn-primary" value="Register" type="submit">
      </div>
    </form>
  </div>
</template>

<script>
var axios = require('axios');

export default {
  name: 'Register',
  data(){
    return{
      first_name: '',
      last_name: '',
      email: '',
      password: '',
      jurusan: 'TKJ',
      errors: []
    }
  },
  props: {
    // loggedIn: Boolean
  },
  methods: {
    async register(e){
      e.preventDefault();
      this.errors = [];

      var err = [];
      await axios.post('http://localhost:8000/api/register', {
        nama_depan: this.first_name,
        nama_belakang: this.last_name,
        email: this.email,
        password: this.password,
        jurusan: this.jurusan
      })
      .then(function (rs) {
        if (rs.data.message == 'berhasil') {
          err = [];
        }
      })
      .catch(function (e){
        if(e.response.data.errors.length > 0){
          err = e.response.data.errors;
        }
      });

      if (err.length > 0) {
        this.errors = err
      }else{
        this.$emit('register');
      }
    }
  }
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
ul{
  margin: 0;
  padding-left: 10px;
}
</style>
