<template>
  <div style="padding : 2px 5px">
    <div v-if="loadingData" class="d-flex justify-content-center align-items-center" style="height: 511px">
      <div class="loader loader-default"></div>
    </div>
    <div v-else>
      <div class="d-flex flex-row justify-content-between">
        <h2>Kegiatan</h2>
      </div>
      <hr>
      <div class="row">
        <div v-for="data of datas1" :key="data.id" class="col-sm-6">
          <div class="card mb-3">
            <div :style="'background-image:url(http://localhost:8000/storage/'+ data.gambar +')'" class="card-img-top"></div>
            <div class="px-1 card-body d-flex">
              <div class="col-6">
                <p class="card-text"><b style="cursor:pointer" @click="$emit('toProfile', data.owner)">{{ data.nama_depan }}</b> {{ data.caption }}</p>
                <small class="text-muted">{{ data.lastUpdate }}</small>
              </div>
              <div class="col-6 d-flex flex-row justify-content-end">
                <div @click="like(data.id,'like')" v-if="!data.liked" class="mr-3" style="color:red">
                  <Icon :icon="['far', 'heart']" size="lg" class="like"/>
                </div>
                <div @click="like(data.id,'unlike')" v-else class="mr-3" style="color: red">
                  <Icon :icon="['fas', 'heart']" size="lg" class="like"/>
                </div>
                <p>{{ data.likeCount }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
var axios = require('axios');

export default {
  name: 'Kegiatan',
  data(){
    return{
      errors: [],
      datas1: [],
      loadingData: false
    }
  },
  props: {
    loggedIn: Boolean,
    user: Object,
    token: String
  },
  async mounted(){
    this.loadingData = true
    await this.getKegiatan()
    this.loadingData = false
  },
  methods: {
    async getKegiatan(){
      var res = []
      await axios.get('http://localhost:8000/api/post')
      .then(function (rs) {
        res = rs
      })
      .catch(function (e) {
        console.log(e)
      })

      res.data.forEach(dt => {
        var dateNow = new Date();
        var dateThen = new Date(dt.updated_at);
        var diffInMs = dateNow.getTime() - dateThen.getTime()
        // console.log(dateNow + '  __  ' + dateThen)
        
        var seconds = ((diffInMs % 60000) / 1000).toFixed(0)
        var minutes = Math.floor(diffInMs / 60000)
        var hours = Math.floor(minutes / 60)
        var days = Math.floor(hours / 24)
        var weeks = Math.floor(days / 7)
        var word = seconds + ' secs ago'
        if (weeks > 4) {
          var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'December'];
          word = `${dateThen.getDate()} ${months[dateThen.getMonth()]} ${dateThen.getFullYear()}`
        }else if(weeks >= 1){
          word = weeks + ' weeks ago'
        }else if(days >= 1){
          word = days + ' days ago'
        }else if (hours >= 1) {
          word = hours + ' hrs ago'
        }else if(minutes >= 1){
          word = minutes + ' mins ago'
        }
        dt.lastUpdate = word
        dt.liked = false
        if(dt.like.length > 0){
          dt.likeCount = dt.like.length
          dt.like.forEach(e => {
            if (e.user_liked == this.user.user_id) {
              dt.liked = true
            }
          });
        }else{
          dt.likeCount = 0
        }
      })

      this.datas1 = res.data
      
    },
    async like(post_id, status){
      if (!this.loggedIn) {
        var go = confirm('Login required to like. Would you like to login now?')
        if (go) {
          this.$emit('goLogin');
        }
      }else{
        var err = [];
        var url = '';
        if (status == 'like') {
          url = 'http://localhost:8000/api/like'
        }else{
          url = 'http://localhost:8000/api/unlike'
        }
        await axios.post(url, {
          post_id: post_id
        }, {
          headers: {
            'Authorization' : `Bearer ${this.token}`
          }
        })
        .then(function (rs) {
          if (rs.data.message) {
            err = rs.data.message
          }
        })
        .catch(function (e) {
          console.log(e);
        })
  
        if (err.length > 0){
          this.errLike = err
        }else{
          if (status == 'like') {
            this.datas1.forEach(dt => {
              if (dt.id == post_id) {
                dt.liked = true
                dt.likeCount += 1
              }
            });
          }else{
            this.datas1.forEach(dt => {
              if (dt.id == post_id) {
                dt.liked = false
                dt.likeCount -= 1
              }
            });
          }
        }
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
