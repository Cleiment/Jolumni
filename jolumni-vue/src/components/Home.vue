<template>
  <div style="padding : 2px 5px;">
    <div v-if="loadingData" class="d-flex justify-content-center align-items-center" style="min-height: 460px">
      <div class="loader loader-default"></div>
    </div>
    <div v-else>
      <div>
        <div class="d-flex flex-row justify-content-between">
          <h2>Lowongan Kerja</h2>
          <small @click="$emit('toLowongan')" style="margin-top: 7px;color: darkgrey;cursor: pointer">See All</small>
        </div>
        <hr>
        <div class="lowongan d-flex flex-row">
          <div class="col-4 list-group">
            <div v-for="data of datas2" :key="data.id" class="list-group-item list-group-item-action" style="cursor: pointer" :class="{'active': data.active, 'list-group-item-secondary': data.active}" @click="chActLow(data.id)">
              <p>{{ data.judul }}</p>
              <small>Owner : <span class="lowner" @click="$emit('toProfile', data.owner)">{{ data.nama_depan }}</span></small>
            </div>
          </div>
          <div class="col-8 py-3 detail-lowongan d-flex flex-column">
            <div class="d-flex flew-row justify-content-between">
              <h5><b>{{ showedLowongan.judul }}</b></h5>
              <button v-if="user.user_id != showedLowongan.owner" class="btn btn-primary">Lamar</button>
            </div>
            <div class="d-flex flex-column">
              <div class="mb-3">
                <small>Owner : <span class="lowner" @click="$emit('toProfile', showedLowongan.owner)">{{ showedLowongan.nama_depan }}</span></small>
                <p>Pelamar : {{ showedLowongan.pc }}</p>
              </div>
              <p>{{ showedLowongan.detail_lowongan }}</p>
            </div>
          </div>
        </div>
        <hr>
      </div>
      <div>
        <div class="d-flex flex-row justify-content-between">
          <h2>Kegiatan</h2>
          <small @click="$emit('toKegiatan')" style="margin-top: 7px;color: darkgrey;cursor: pointer">See All</small>
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
  </div>
</template>

<script>
var axios = require('axios');

export default {
  name: 'Home',
  data(){
    return{
      email: '',
      pass: '',
      errors: [],
      datas1: [],
      datas2: [],
      showedLowongan: {},
      loadingData: false
      // datas: []
    }
  },
  props: {
    loggedIn: Boolean,
    user: Object,
    token: String
  },
  async mounted(){
    await this.getLowongan()
    await this.getKegiatan()
  },
  methods: {
    async getKegiatan(){
      // console.log('err')
      var res = []
      this.loadingData = true
      var err = false

      await axios.get('http://localhost:8000/api/get_post_random')
      .then(function (rs) {
        res = rs
      })
      .catch(function (e) {
        console.log(e)
        err = true
      })

      if (err) {
        setTimeout(() => {
          this.getKegiatan()
        }, 1700);
      }else{
        res.data.forEach(dt => {
          var dateNow = new Date();
          var dateThen = new Date(dt.created_at);
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
        this.loadingData = false
      }
    },
    async getLowongan(){
      var res = []
      this.loadingData = true
      var err = false

      await axios.get('http://localhost:8000/api/get_lowongan_random')
      .then(function (rs) {
        res = rs.data
      })
      .catch(function (e) {
        console.log(e)
        err = true
      })

      if (err) {
        setTimeout(() => {
          this.getLowongan()
        }, 1700);
      }else{
        await res.forEach(rs => {
          rs.active = false
          rs.pc = rs.pelamar.length
        })
        this.datas2 = res
        await this.chActLow(res[0].id)
        this.loadingData = false
      }
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
    },
    chActLow(id){
      var data = {}
      this.datas2.map(function (dt) {
        dt.active = false
        if (dt.id == id) {
          dt.active = true
          data = dt
        }
      })

      this.showedLowongan = data
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

.lowongan{
  height: 320px;
}

.detail-lowongan{
  border: 1px solid #dedede;
  border-radius: 3px;
  /* padding-top: 5px; */
}

.list-group-item p{
  margin: 0;
}

.lowner{
  cursor: pointer;
}

.lowner:hover{
  font-weight: bold;
}

</style>
