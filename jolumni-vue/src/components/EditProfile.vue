<template>
  <div style="padding : 2px 5px">
    <div>
      <h1>Edit Profile</h1>
      <div class="d-flex justify-content-between">
        <button class="btn btn-outline-secondary" @click="$emit('back')"> Back </button>
        <button class="btn btn-danger px-4" @click="deleteAcc"> Delete Account </button>
      </div>
      <hr>
      <div class="alert alert-danger" role="alert" v-if="errors.length != 0">
        <ul>
          <li :key="err.message" v-for="err in errors">
            {{ err }}
          </li>
        </ul>
      </div>
      <form @submit="editProfile" action="http://localhost:8000/api/register" method="post">
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="first-name">Nama Depan</label>
            <input type="text" class="form-control px-3" id="first-name" v-model="newUser.nama_depan" placeholder="Nama Depan">
          </div>
          <div class="form-group col-md-6">
            <label for="last-name">Nama Belakang</label>
            <input type="text" class="form-control px-3" id="last-name" v-model="newUser.nama_belakang" placeholder="Nama Belakang">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="email">Email</label>
            <input type="email" class="form-control px-3" id="email" v-model="newUser.email" placeholder="Email">
          </div>
          <div class="form-group col-md-4">
            <label for="tgl-lahir">Tanggal Lahir</label>
            <input type="date" class="form-control" id="tgl-lahir" v-model="newUser.tgl_lahir">
          </div>
          <div class="form-group col-md-4">
            <label for="no-telp">No Telp</label>
            <input type="text" class="form-control px-3" id="no-telp" minlength="11" maxlength="14" v-model="newUser.no_telp" placeholder="No Telp">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-4">
            <label for="tahun-masuk">Tahun Masuk</label>
            <input type="number" class="form-control px-3" :min="minTahun" :max="maxTahunMasuk" id="tahun-masuk" v-model="newUser.tahun_masuk" placeholder="Tahun Masuk">
          </div>
          <div class="form-group col-md-4">
            <label for="tahun-selesai">Tahun Selesai</label>
            <input type="number" class="form-control px-3" :min="minTahun + 2" :max="maxTahunSelesai" id="tahun-selesai" v-model="newUser.tahun_selesai" placeholder="Tahun Selesai">
          </div>
          <div class="form-group col-md-4">
            <label for="jurusan">Jurusan</label>
            <select class="form-control" v-model="newUser.jurusan" name="jurusan" id="jurusan">
              <option value="TKJ" selected >Teknologi Komputer & Jaringan</option>
              <option value="AKL">Akuntansi</option>
              <option value="BDP">Pemasaran</option>
            </select>
          </div>
        </div>
        <div class="form-row">
          <div class="form-group col-md-6">
            <label for="alamat">Alamat</label>
            <textarea class="form-control" id="alamat" placeholder="Alamat" v-model="newUser.alamat"></textarea>
          </div>
          <div class="form-group col-md-6">
            <label for="pekerjaan">Pekerjaan</label>
            <textarea class="form-control" id="pekerjaan" placeholder="Pekerjaan" v-model="newUser.pekerjaan"></textarea>
          </div>
        </div>
        <div class="form-row mb-3">
          <div class="col-sm-6 d-flex justify-content-center align-items-center">
            <!-- <p class="text-center" v-if="!url">Preview will show here (After you add an image)</p> -->
            <div :style="url == '' ? 'background-image:url(http://localhost:8000/storage/'+ newUser.gambar +')' : 'background-image:url('+ url +')'" class="user_gambar"></div>
          </div>
          <div class="form-group col-md-6">
            <label>Profile Picture</label>
            <div class="input-group mb-3">
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="inputGroupFile01" @change="onFileChange">
                <label class="custom-file-label" for="inputGroupFile01">{{ !file.name ? newUser.gambar == '' ? 'Profile Picture' :   newUser.gambar.substring(5) : file.name }}</label>
              </div>
            </div>
            <label>Input Password to Confirm Edit / Delete Account</label>
            <input @change="sureDelete2 = true" type="password" class="form-control" v-model="confPassword" placeholder="Password">
          </div>
        </div>
        <div class="form-group text-center">
          <input class="form-control btn btn-primary" value="Edit Profile" type="submit" v-if="!loading">
          <button class="form-control btn btn-primary disabled" disabled v-else>Loading...</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
var axios = require('axios');

export default {
  name: 'EditProfile',
  data(){
    return{
      errors: [],
      loading: false,
      newUser: {},
      maxTahunMasuk: 0,
      maxTahunSelesai: 0,
      minTahun: 1988,
      file: '',
      url: '',
      fileNya: 'default',
      confPassword: '',
      sureDelete1: false,
      sureDelete2: false
    }
  },
  props: {
    // loggedIn: Boolean
    user: Object,
    token: String
  },
  created(){
    this.newUser = JSON.parse(JSON.stringify(this.user))
    if (this.newUser.tahun_masuk == null) {
      this.newUser.tahun_masuk = 1988
    }
    if (this.newUser.tahun_selesai == null) {
      this.newUser.tahun_selesai = 1990
    }

    this.maxTahunSelesai = new Date().getMonth() == 5 ? new Date().getFullYear() - 1 : new Date().getFullYear() - 2;
    this.maxTahunMasuk = this.maxTahunSelesai - 2;
  },
  methods: {
    async editProfile(e){
      e.preventDefault();

      if (this.sureDelete1) {
        this.deleteAcc();
      }else{
        this.errors = [];
        this.loading = true

        this.newUser.tgl_lahir == "" ? console.log('Iya') : console.log(`${typeof(this.newUser.tgl_lahir)} : ${this.newUser.tgl_lahir}`)

        var err = [];

        var formData = new FormData();
        formData.append('nama_depan', this.newUser.nama_depan);
        formData.append('nama_belakang', this.newUser.nama_belakang);
        formData.append('email', this.newUser.email);
        (this.newUser.tgl_lahir != null || typeof(this.newUser.tgl_lahir) != "object") && this.newUser.tgl_lahir != '' ? formData.append('tgl_lahir', this.newUser.tgl_lahir) : err = []
        this.newUser.no_telp != null ? formData.append('no_telp', this.newUser.no_telp) : err = []
        this.newUser.tahun_masuk != null ? formData.append('tahun_masuk', this.newUser.tahun_masuk) : err = []
        this.newUser.tahun_selesai != null ? formData.append('tahun_selesai', this.newUser.tahun_selesai) : err = []
        formData.append('jurusan', this.newUser.jurusan);
        this.newUser.alamat != null ? formData.append('alamat', this.newUser.alamat) : formData.append('alamat', '')
        this.newUser.pekerjaan != null ? formData.append('pekerjaan', this.newUser.pekerjaan) : formData.append('pekerjaan', '')
        formData.append('fileNya', this.fileNya);
        this.newUser.confPassword != '' ? formData.append('confPassword', this.confPassword) : err = []
        formData.append('file', this.file);

        err = [];
        await axios.post('http://localhost:8000/api/edit_profile', formData, {
          headers: {
            'Authorization': `Bearer ${this.token}`,
            'Content-Type': 'multipart/form-data'
          }
        })
        .then(function (rs) {
          if (rs.data.message) {
            err = rs.data.message
          }
        })
        .catch(function (e){
          // console.log(e)
          if(e.response.data.errors.length > 0){
            err = e.response.data.errors;
          }
        });

        this.loading = false

        if (err.length > 0) {
          this.errors = err
        }else{
          alert('Berhasil Edit!')
          this.$emit('goProfile')
        }
      }
    },
    onFileChange(e){
      this.file = e.target.files[0];
      this.url = URL.createObjectURL(this.file);
      this.fileNya = 'upload'
    },
    deleteAcc(){
      if (!this.sureDelete1) {
        if (confirm('Are you sure you want to delete this account?')) {
          this.sureDelete1 = true
          alert('Input your password below and then press this button again')
        }else{
          this.sureDelete1 = false
        }
      }else if (this.sureDelete1 && this.sureDelete2){
        if (this.confPassword == '') {
          this.sureDelete2 = false
          alert('Input your password below and then press this button again')
        }
        else {
          if (confirm('You really sure want to delete this account?')){
            this.$emit('deleteAccount', this.confPassword);
          }else {
            this.sureDelete1 = false
            this.sureDelete2 = false
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
.preview{
  height: 210px;
}
.user_gambar{
  border-radius: 50%;
  box-shadow: 0px 0px 3px #cfcfcf;
  width: 160px;
  height: 160px;
  background-size: cover;
  background-position: center center;
}
</style>
