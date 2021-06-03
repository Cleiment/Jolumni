<template>
  <div style="padding : 2px 5px">
    <div>
      <div class="d-flex flex-row justify-content-between">
        <h2>Lowongan Kerja</h2>
        <small style="margin-top: 7px;color: darkgrey;cursor: pointer">See All</small>
      </div>
      <hr>
      <div>
        <div v-for="data of datas" :key="data.id" class="card mb-3">
          <img src="" class="card-img-top" alt="">
          <div class="card-body">
            <h5 class="card-title">{{data.title}}</h5>
            <p class="card-text">data.desc</p>
            <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
          </div>
        </div>
      </div>
    </div>
    <br>
    <div>
      <div class="d-flex flex-row justify-content-between">
        <h2>Kegiatan</h2>
        <small style="margin-top: 7px;color: darkgrey;cursor: pointer">See All</small>
      </div>
      <hr>
      <div>
        <div v-for="data of datas" :key="data.id" class="card mb-3">
          <img src="" class="card-img-top" alt="">
          <div class="card-body">
            <h5 class="card-title">{{data.title}}</h5>
            <p class="card-text">data.desc</p>
            <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
          </div>
        </div>
      </div>
    </div>
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
      errors: [],
      datas: [{id:1, title: "coba1", desc: "coba1"},{id: 2, title: "coba2", desc: "coba2"},],
      // datas: []
    }
  },
  props: {
    loggedIn: Boolean
  },
  mounted(){

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
