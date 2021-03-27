<template>
  <div style="padding : 2px 5px">
    <h1>Login</h1>
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
    <form @submit="login" action="http://localhost:8000/api/login" method="post">
      <div class="form-group row">
        <label for="email" class="col-sm-2 col-form-label">Email</label>
        <div class="col-sm-10">
          <input type="text" class="form-control" id="email" v-model="email" name="email " placeholder="email@example.com">
        </div>
      </div>
      <div class="form-group row">
        <label for="password" class="col-sm-2 col-form-label">Password</label>
        <div class="col-sm-10">
          <input type="password" class="form-control" id="password" v-model="pass" name="password" placeholder="Password">
        </div>
      </div>
      <div class="form-group text-center">
        <input class="form-control btn btn-primary" value="Login" type="submit">
      </div>
    </form>
  </div>
</template>

<script>
var axios = require('axios');

export default {
  name: 'Login',
  data(){
    return{
      email: '',
      pass: '',
      errors: []
    }
  },
  props: {
    loggedIn: Boolean
  },
  methods: {
    async login(e){
      e.preventDefault();
      this.errors = [];

      if (this.loggedIn) {
        this.$emit('logout');
      }

      var err = [];
      var res = [];
      await axios.post('http://localhost:8000/api/login', {
        email: this.email,
        password: this.pass
      })
      .then(function (rs) {
        if (rs.data.message) {
          err = rs.data.message
        }else{
          res = rs.data
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
        this.$emit('loginUser', res);
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
