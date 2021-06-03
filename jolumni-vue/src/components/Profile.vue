<template>
  <div style="padding : 2px 5px">
    <div v-if="loadingData" class="d-flex justify-content-center align-items-center" style="height: 511px">
      <div class="loader loader-default"></div>
    </div>
    <div v-else>
      <div class="d-flex flex-row justify-content-center px-5 mb-4">
        <!-- <div class="col-3"> 
          <img :src="'http://localhost:8000/storage/' +  user.gambar" class="user_gambar">
        </div> -->
        <div class="col-3 preview d-flex justify-content-center align-items-center">
          <div :style="'background-image:url(http://localhost:8000/storage/'+ user.gambar +')'" class="user_gambar"></div>
        </div>
        <div class="col-8" style="word-wrap: break-word">
          <div class="d-flex justify-content-between">
            <h2>{{ user.nama_depan }} {{ user.nama_belakang }}</h2>
            <div class="dropdown">
              <button class="btn btn-secondary">
                <Icon :icon="['fas', 'chevron-down']" />
              </button>
              <div v-if="profile_id == user_id" class="dropdown-content">
                <a @click="$emit('edit')">Edit Profile</a>
                <a @click="$emit('logout')">Logout</a>
              </div>
              <div v-else class="dropdown-content">
                <a @click="tes">Report User</a>
                <a @click="tes">Block User</a>
              </div>
            </div>
          </div>
          <div class="row information">
            <div class="col-1 d-flex flex-column align-items-center">
              <Icon size="sm" :icon="['fas', 'envelope']" class="mt-1 mb-4"/>
              <Icon size="sm" :icon="['far', 'calendar-alt']" class="mb-4"/>
              <Icon size="sm" :icon="['fas', 'map-marker-alt']" class="mb-4"/>
              <Icon size="sm" :icon="['fas', 'phone']" class=""/>
            </div>
            <div class="col-5">
              <p class="mb-3">{{ user.email }}</p>
              <p class="mb-3">{{ date }}</p>
              <p class="mb-3">{{ user.alamat }}</p>
              <p class="mb-3">{{ user.no_telp }}</p>
            </div>
            <div class="col-1 d-flex flex-column align-items-center">
              <Icon size="sm" :icon="['fas', 'user-graduate']" class="mt-1 mb-4"/>
              <Icon size="sm" :icon="['fas', 'book']" class="mb-4"/>
              <Icon size="sm" :icon="['fas', 'building']" class="mb-4"/>
            </div>
            <div class="col-5">
              <p class="mb-3">{{ user.tahun }}</p>
              <p class="mb-3">{{ user.jurusan }}</p>
              <p class="mb-3">{{ user.pekerjaan }}</p>
            </div>
          </div>
        </div>
      </div>
      <hr>
      <div>
        <div v-if="$props.profile_id != $props.user_id" class="form-row justify-content-around pt-3 pb-2">
          <button @click="toggle('Kegiatan')" :class="aKb" class="col-5 py-3 btn">Kegiatan</button>
          <button @click="toggle('Lowongan')" :class="aLb" class="col-5 py-3 btn">Lowongan</button>
        </div>
        <div v-else class="form-row justify-content-around pt-3 pb-2">
          <button @click="toggle('Kegiatan')" :class="aKb" class="col-5 py-3 btn">Kegiatan</button>
          <button @click="toggle('Add')" :class="aAb" class="py-3 px-4 btn">
            <Icon :icon="['fas','plus']"/>
          </button>
          <button @click="toggle('Lowongan')" :class="aLb" class="col-5 py-3 btn">Lowongan</button>
        </div>
      </div>
      <br>
      <div v-if="activeOne == 'Kegiatan'">
        <div v-if="loadingPostData" class="d-flex justify-content-center align-items-center" style="height: 511px">
          <div class="loader loader-yellow"></div>
        </div>
        <div v-else>
          <div class="d-flex justify-content-center align-items-center" style="height: 100px" v-if="datas1.length == 0">
            <p style="color: #afafaf">Tidak ada post</p>
          </div>
          <div v-else class="row">
            <div v-for="data of datas1" :key="data.id" class="col-sm-6">
              <div class="card mb-3">
                <div :style="'background-image:url(http://localhost:8000/storage/'+ data.gambar +')'" class="card-img-top"></div>
                <div class="px-1 card-body d-flex">
                  <div class="col-6">
                    <p class="card-text"><b>{{ user.nama_depan }}</b> {{ data.caption }}</p>
                    <small class="text-muted">{{ data.lastUpdate }}</small>
                  </div>
                  <div class="col-6 d-flex flex-column justify-content-between">
                    <div class="d-flex flex-row justify-content-end">
                      <div @click="like(data.id, 'like')" v-if="!data.liked" class="mr-3" style="color:red">
                        <Icon :icon="['far', 'heart']" size="lg" class="like"/>
                      </div>
                      <div @click="like(data.id, 'unlike')" v-else class="mr-3" style="color: red">
                        <Icon :icon="['fas', 'heart']" size="lg" class="like"/>
                      </div>
                      <p>{{ data.likeCount }}</p>
                    </div>
                    <div v-if="profile_id == user_id" class="d-flex justify-content-end">
                      <div class="dropdown">
                        <div style="cursor: pointer">
                          <Icon :icon="['fas', 'ellipsis-h']" size="lg"/>
                        </div>
                        <div class="dropdown-content">
                          <a @click="editPost(data.id)">Edit Post</a>
                          <a @click="deletePost(data.id)">Delete Post</a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="activeOne == 'Add'">
        <ul class="nav nav-tabs justify-content-center" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" v-if="activeTab=='k'" role="tab">Kegiatan</a>
            <a class="nav-link" @click="activeTab='k'" v-else role="tab">Kegiatan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link active" v-if="activeTab=='l'" role="tab">Lowongan</a>
            <a class="nav-link" @click="activeTab='l'" v-else role="tab">Lowongan</a>
          </li>
        </ul>
        <div class="tab-content">
          <div class="tab-pane fade show active" role="tabpanel" v-if="activeTab=='k'">
            <br>
            <div class="alert alert-danger" role="alert" v-if="errKeg.length != 0">
              <ul>
                <li :key="err.message" v-for="err in errKeg">
                  {{err}}
                </li>
              </ul>
            </div>

            <form class="d-flex" @submit="postKegiatan" action="http://localhost:8000/api/create_post" method="post">
              <div class="col-sm-6 preview d-flex justify-content-center align-items-center">
                <p class="text-center" v-if="!url">Preview will show here (After you add an image)</p>
                <div v-else class="card mb-3" style="width:100%;">
                  <div :style="'background-image:url('+url+')'" class="card-img-top"></div>
                  <div class="px-1 card-body d-flex">
                    <div class="col-6">
                      <p class="card-text"><b>{{ user.nama_depan }}</b> {{ caption }}</p>
                      <small class="text-muted">1 secs ago</small>
                    </div>
                    <div class="col-6 d-flex flex-row justify-content-end">
                      <div class="mr-3" style="color: red">
                        <Icon :icon="['fas', 'heart']" size="lg" class="like"/>
                      </div>
                      <p>1</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="input-group mb-3">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="inputGroupFile01" @change="onFileChange">
                    <label class="custom-file-label" for="inputGroupFile01">{{file.name}}</label>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Caption</span>
                  </div>
                  <textarea class="form-control" v-model="caption" rows="7" maxlength="100"></textarea>
                </div>
                <button class="form-control btn btn-primary" v-if="!loading">Post</button>
                <button class="form-control btn btn-primary disabled" disabled v-else>Loading...</button>
              </div>
            </form>
          </div>
          <div class="tab-pane fade show active" role="tabpanel" v-if="activeTab=='l'">
            <br>
            <div class="alert alert-danger" role="alert" v-if="errLow.length != 0">
              <ul>
                <li :key="err.message" v-for="err in errLow">
                  {{err}}
                </li>
              </ul>
            </div>

            <form class="d-flex justify-content-center" @submit="postLowongan" action="http://localhost:8000/api/create_lowongan" method="post">
              <div class="col-10">
                <div class="form-group">
                  <label for="judul" class="form-label">Judul</label>
                  <input class="form-control" id="judul" v-model="newLowongan.judul" placeholder="Judul lowongan">
                </div>
                <div class="form-group">
                  <label for="detail-lowongan" class="form-label">Detail Lowongan</label>
                  <textarea class="form-control" id="detail-lowongan" v-model="newLowongan.detail" placeholder="Detail lowongan pekerjaan"></textarea>
                </div>
                <button class="form-control btn btn-primary" v-if="!loading">Post</button>
                <button class="form-control btn btn-primary disabled" disabled v-else>Loading...</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div v-if="activeOne == 'Edit Post'">
        <div class="tab-content">
          <div class="tab-pane fade show active" role="tabpanel">
            <br>
            <div class="alert alert-danger" role="alert" v-if="errKeg.length != 0">
              <ul>
                <li :key="err.message" v-for="err in errKeg">
                  {{err}}
                </li>
              </ul>
            </div>

            <form class="d-flex" @submit="editKegiatan" action="http://localhost:8000/api/edit_post" method="post">
              <div class="col-sm-6 preview d-flex justify-content-center align-items-center">
                <div class="card mb-3" style="width:100%;">
                  <div :style="'background-image:url('+url+')'" class="card-img-top"></div>
                  <div class="px-1 card-body d-flex">
                    <div class="col-6">
                      <p class="card-text"><b>{{ user.nama_depan }}</b> {{ caption }}</p>
                      <small class="text-muted"></small>
                    </div>
                    <div class="col-6 d-flex flex-row justify-content-end">
                      <div class="mr-3" style="color: red">
                        <Icon :icon="['fas', 'heart']" size="lg" class="like"/>
                      </div>
                      <p>1</p>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="input-group mb-3">
                  <div class="custom-file">
                    <input type="file" class="custom-file-input" id="inputGroupFile01" @change="onFileChange">
                    <label class="custom-file-label" for="inputGroupFile01">{{file.name}}</label>
                  </div>
                </div>
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Caption</span>
                  </div>
                  <textarea class="form-control" v-model="caption" rows="7" maxlength="100"></textarea>
                </div>
                <button class="form-control btn btn-primary" v-if="!loading">Post</button>
                <button class="form-control btn btn-primary disabled" disabled v-else>Loading...</button>
              </div>
            </form>
          </div>
        </div>
      </div>
      <div v-if="activeOne == 'Lowongan'">
        <div v-if="loadingPostData" class="d-flex justify-content-center align-items-center" style="height: 511px">
          <div class="loader loader-yellow"></div>
        </div>
        <div v-else>
          <div class="d-flex justify-content-center align-items-center" style="height: 100px" v-if="datas2.length == 0">
            <p style="color: #afafaf">Tidak ada lowongan</p>
          </div>
          <div class="row" v-else>
            <div v-for="data of datas2" :key="data.id" class="card mb-3">
              <img src="" class="card-img-top" alt="">
              <div class="card-body">
                <h4 class="card-title">{{data.title}}</h4>
                <p class="card-text">data.desc</p>
                <p class="card-text"><small class="text-muted">Last updated 3 mins ago</small></p>
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
  name: 'Profile',
  data(){
    return{
      errKeg: [],
      errLow: [],
      errLike: [],
      datas1: [],
      datas2: [],
      date: '',
      activeOne: 'Kegiatan',
      activeTab: 'k',
      aKb: 'btn-primary',
      aLb: 'btn-outline-primary',
      aAb: 'btn-outline-success',
      caption: '',
      url: '',
      file: '',
      loading: false,
      user: {},
      loadingData: false,
      loadingPostData: false,
      editId: '',
      newLowongan: {
        judul: '',
        detail: ''
      }
    }
  },
  props: {
    loggedIn: Boolean,
    token: String,
    profile_id: String,
    user_id: String
  },
  async mounted(){
    this.loadingData = true
    await this.getUser(this.profile_id);
    this.tglLahir();
    await this.getPosts();
    this.loadingData = false
    // console.log(this.loggedIn)
  },
  methods: {
    async getUser(id){
      var res = {}
      await axios.get(`http://localhost:8000/api/getUser/${id}`)
      .then(rs => {
        res = rs
      })
      .catch(e => console.log(e))
      
      res.data.tahun = `${res.data.tahun_masuk} - ${res.data.tahun_selesai}`

      for (const rs in res.data){
        // console.log(`${rs} : ${res.data[rs]}`)
        if(res.data[rs] == null && (rs != 'tahun_masuk' || rs != 'tahun_selesai')){
          // console.log(rs)
          res.data[rs] = '-'
        }
        if ((rs == 'tahun_masuk' || rs == 'tahun_selesai') && res.data[rs] == null) {
          res.data['tahun'] = '-'
        }
      }

      this.user = res.data;
    },
    async getPosts(){
      var res = []
      this.datas1 = []
      this.loadingPostData = true

      await axios.get(`http://localhost:8000/api/get_post_owner/${this.user.user_id}`)
      .then(function (rs) {
        res = rs
      })
      .catch(function (e) {
        console.log(e)
      })
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
            if (e.user_liked == this.user_id) {
              dt.liked = true
            }
          });
        }else{
          dt.likeCount = 0
        }
      })
      this.loadingPostData = false
      this.datas1 = res.data.reverse()
    },
    tglLahir(){
      if (this.user.tgl_lahir == '-') {
        this.date = '-'
      }else{
        var days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'December'];
        var now = new Date(this.user.tgl_lahir);
        this.date = (days[now.getDay()] + ', ' + now.getDate() + ' ' + months[now.getMonth()] + ' '+ now.getFullYear());
      }
    },
    toggle(btn){
      if (btn == 'Kegiatan') {
        this.getPosts();
        this.activeOne = 'Kegiatan';
        this.aKb = 'btn-primary';
        this.aLb = 'btn-outline-primary';
        this.aAb = 'btn-outline-success';
      }else if (btn == 'Lowongan') {
        this.activeOne = 'Lowongan';
        this.aKb = 'btn-outline-primary';
        this.aLb = 'btn-primary';
        this.aAb = 'btn-outline-success';
      }else if (btn == 'Add') {
        this.activeOne = 'Add';
        this.aKb = 'btn-outline-primary';
        this.aLb = 'btn-outline-primary';
        this.aAb = 'btn-success';
      }
    },
    onFileChange(e){
      this.file = e.target.files[0];
      this.url = URL.createObjectURL(this.file)
    },
    async postKegiatan(e){
      e.preventDefault();

      var formData = new FormData()
      formData.append('caption', this.caption)
      formData.append('file', this.file)

      this.loading = true

      var err = [];
      await axios.post('http://localhost:8000/api/create_post', formData, {
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'multipart/form-data',
        }
      })
      .then(function (rs) {
        if (rs.data.message) {
          err = rs.data.message
        }else{
          alert('Berhasil Post');
        }
      })
      .catch(function (e){
        // console.log(e.response)
        if(e.response.data.errors.length > 0){
          err = e.response.data.errors;
        }
      });

      this.loading = false

      if (err.length > 0) {
        this.errKeg = err
      }else{
        this.toggle('Kegiatan')
        this.caption = ''
        this.file = ''
        this.url = ''
        this.errKeg = []
      }
    },
    async postLowongan(e){
      e.preventDefault();

      var formData = new FormData()
      formData.append('judul', this.newLowongan.judul)
      formData.append('detail_lowongan', this.newLowongan.detail)

      this.loading = true

      var err = [];
      await axios.post('http://localhost:8000/api/create_lowongan', formData, {
        headers: {
          'Authorization': `Bearer ${this.token}`,
          'Content-Type': 'multipart/form-data',
        }
      })
      .then(function (rs) {
        if (rs.data.message) {
          err = rs.data.message
        }else{
          alert('Berhasil Post');
        }
      })
      .catch(function (e){
        // console.log(e.response)
        if(e.response.data.errors.length > 0){
          err = e.response.data.errors;
        }
      });

      this.loading = false

      if (err.length > 0) {
        this.errLow = err
      }else{
        this.toggle('Lowongan')
        this.newLowongan.judul = ''
        this.newLowongan.detail = ''
        this.errLow = []
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
    async deletePost(id){
      if (confirm('Anda yakin ingin menghapus post?')) {
        this.loadingPostData = true

        await axios.post('http://localhost:8000/api/delete_post', {
          'id' : id
        }, {
          headers: {
            'Authorization' : `Bearer ${this.token}`
          }
        })
        .then(rs => {
          alert(rs.data.message)
        })
        .catch(e => {
          console.log(e);
        })
      }

      this.getPosts();
      this.loadingPostData = false
    },
    async editPost(id){
      this.activeOne = 'Edit Post'
      this.editId = id
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

.user_gambar{
  border-radius: 50%;
  box-shadow: 0px 0px 3px #cfcfcf;
  width: 160px;
  height: 160px;
  background-size: cover;
  background-position: center center;
}

.information{
  font-size: 14px
}

.information p{
  margin: 0
}

.information svg{
  color: #dc3545;
  font-size: 15px;
}

.nav-tabs li a{
  cursor: pointer
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: none;
  position: absolute;
  cursor: pointer;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 2px 8px 0px rgba(122,122,122,0.2);
  z-index: 1;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {background-color: #f1f1f1}

.dropdown:hover .dropdown-content {
  display: block;
}

</style>
