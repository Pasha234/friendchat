<template>
  <div class="container">
    <div class="col-sm-7 mx-auto mt-4">
      <div class="p-3 border bg-light mb-3"><img class="rounded-circle" style="width: 100px; height: 100px;" :src="getAvatar" alt="avatar"><span class="mx-3">{{ getUser.id != $route.params.id ? user.nickname : getUser.nickname }}</span></div>
      <ul class="list-group my-3" v-if="getUser.id == $route.params.id">
        <li class="list-group-item"><a @click.prevent="showNicknameForm" href="#" class="user-select-none">Change nickname</a></li>
        <li class="list-group-item" v-show="nicknameFormShown">
          <label for="newNickname">Enter new nickname: </label>
          <input id="newNickname" type="text" class="form-control my-2" v-model="nickname" @keypress.enter="changeNickname">
          <button class="btn btn-primary my-2" type="button" @click="changeNickname">Submit</button>
          <div class="alert alert-danger" role="alert" v-show="errors.nickname != ''">{{ errors.nickname }}</div>
        </li>
        <li class="list-group-item"><a @click.prevent="showAvatarForm" href="#" class="user-select-none">Change avatar</a></li>
        <li class="list-group-item" v-show="avatarFormShown">
          <label for="newAvatar">Choose new avatar: </label>
          <input id="newNickname" type="file" class="form-control" ref="newAvatar" @change="setAvatarField" accept="image/png, image/jpeg, image/bmp">
          <button class="btn btn-primary my-2" type="button" @click="changeAvatar">Submit</button>
          <div class="alert alert-danger" role="alert" v-show="errors.avatar != ''">{{ errors.avatar }}</div>
        </li>
      </ul>
      <button v-if="getUser.id == $route.params.id" class="btn btn-danger" @click="logout">Logout</button>
      <router-link v-else :to="'/chat?u=' + user.id" class="btn btn-primary" @click="openChat">Send Message</router-link>
    </div>
  </div>  
</template>

<script>
import {mapMutations, mapGetters} from 'vuex';

export default {
  data() {
    return {
      user: {},
      nickname: "",
      avatar: "",
      nicknameFormShown: false,
      avatarFormShown: false,
      errors: {
        nickname: '',
        avatar: ''
      },
    }
  },
  methods: {
    ...mapMutations(['setUser', 'changeAuth', 'setAvatar']),
    getUserFromDb() {
      axios.get(window.location.origin + '/api/users/' + this.$route.params.id)
        .then(response => {
          this.user = response.data.data
        })
    },
    setAvatarField() {
      this.avatar = this.$refs.newAvatar.files[0]
    },
    logout() {
      axios.get(window.location.origin + '/logout')
        .then(response => {
          if (response.data.success) {
            this.setUser()
            this.changeAuth(false)
            this.$router.push({
              name: 'home'
            })
          }
        })
    },
    showNicknameForm() {
      this.nicknameFormShown = !this.nicknameFormShown
      this.avatarFormShown = false
    },
    showAvatarForm() {
      this.avatarFormShown = !this.avatarFormShown
      this.nicknameFormShown = false
    },
    changeNickname() {
      if (this.nickname == '') {
        this.errors.nickname = 'The nickname field is required.'
        return
      }
      axios.post(window.location.origin + '/changeNickname', {
        nickname: this.nickname
      })
        .then(response => {
          if (response.data.errors) {
            this.errors.nickname = response.data.errors.nickname[0]
          } else if (response.data.success) {
            this.setUser({
              id: this.getUser.id,
              nickname: this.nickname,
              email: this.getUser.email,
              auth: true,
            })
            this.nickname = ''
            this.nicknameFormShown = false
            this.errors.nickname = ''
          }
        })
        .catch(error => {
          if (error.response) {
            let errors = error.response.data.errors
            for (let i in this.errors) {
              if (errors.hasOwnProperty(i)) {
                this.errors[i] = errors[i][0]
              } else {
                this.errors[i] = '';
              }
            }
          }
        })
    },
    changeAvatar() {
      if (this.avatar) {
        if (this.avatar.size > 1000000) {
          this.errors.avatar = "Avatar must be less than 1Mb"
          return
        }
        let formData = new FormData()
        formData.append('avatar', this.avatar)

        axios.post('/api/user/changeAvatar', formData, {
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'multipart/form-data'
          }
        })
          .then(response => {
            if (response.data.errors) {
              this.errors.avatar = response.data.errors.avatar[0]
            } else if (response.data.success) {
              this.setAvatar(response.data.filename)
              this.avatar = ''
              this.$refs.newAvatar.value = null
              this.avatarFormShown = false
              this.errors.avatar = ''
            }
          })
          .catch(error => {
            if (error.response.errors) {
              this.errors.avatar = error.response.data.errors.avatar[0]
            } else {
              this.errors.avatar = "The given data was invalid"
            }
          })
      }
    },
    openChat() {

    }
  },
  computed: {
    ...mapGetters(['getUser']),
    getRoute() {
      return this.$route
    },
    getAvatar() {
      return this.getUser.id != this.$route.params.id ? (this.user.avatar ? this.user.avatar : '/img/usernotfound.jpg') : (this.getUser.avatar ? this.getUser.avatar : '/img/usernotfound.jpg')
    }
  },
  mounted() {
    if (this.getUser.id != this.$route.params.id) {
      this.getUserFromDb()
    }
  },
  watch: {
    getRoute() {
      if (this.getUser.id != this.$route.params.id & this.$route.params.id) {
        this.getUserFromDb()
      }
    }
  }
}
</script>