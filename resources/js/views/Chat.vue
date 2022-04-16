<template>
  <div class="container-xl d-flex my-auto" style="height: 85%;">
    <div class="container-xl border d-flex flex-column mx-4 bg-light">
      <div class="header d-flex flex-row align-items-center p-3 border-bottom border-2">
        <img class="rounded-circle mx-3" style="width: 50px; height: 50px;" :src="anotherUser.avatar ? anotherUser.avatar : '/img/usernotfound.jpg'" :alt="anotherUser.nickname + ' avatar'">
        <h5 class="mx-3"><router-link v-if="userLink" :to="userLink">{{ anotherUser.nickname }}</router-link><span v-else>{{ anotherUser.nickname }}</span></h5>
      </div>
      <div class="c-content d-flex flex-column-reverse p-3">
        <div class="d-flex flex-row my-1" style="width: 100%;" v-for="message in messages" :key="message.id" v-show="!loading">
          <Message :owner="message.user.id == getUser.id" :body="message.body" :date="message.created_at"/>
        </div>
        <div v-show="!messages.length && !loading" class="text-center fs-3">
          <span>There is no messages yet</span>
        </div>
        <div class="d-flex flex-row justify-content-center" v-if="loading">
          <div class="spinner-grow" role="status">
            <span class="visually-hidden">Loading...</span>
          </div>
        </div>
      </div>
      <div class="d-flex flex-row align-items-center p-3 mt-auto">
        <input class="form-control" type="text" v-model="messageBody" @keypress.enter="sendMessage" :disabled="!anotherUser.id">
        <button class="btn btn-primary ms-3" @click="sendMessage" :disabled="!anotherUser.id">Send</button>
      </div>
    </div>
  </div>
</template>

<script>
import Message from '@/js/components/Message';
import {mapGetters} from 'vuex';

export default {
  data() {
    return {
      messages: [],
      chat: {
        id: null,
        name: null,
        to: null,
        from: null,
      },
      loading: true,
      anotherUser: {
        id: null,
        nickname: null,
      },
      messageBody: '',
      userLink: null,
    }
  },
  components: {
    Message
  },
  computed: {
    ...mapGetters(['getUser']),
  },
  mounted() {
    this.getAnotherUser()
    this.getMessages()
  },
  unmounted() {
    this.stopListening()
  },
  created() {
    if (this.$route.query.u == this.getUser.id) {
      this.$router.push({
        name: 'home',
      })
    }
  },
  methods: {
    getAnotherUser() {
      axios.get('api/users/' + this.$route.query.u)
        .then(response => {
          this.anotherUser = response.data.data
          this.userLink = '/user/' + this.$route.query.u
          this.startListening()
        })
        .catch(error => {
          console.log(error);
          this.anotherUser.nickname = 'Not Found'
        })
    },
    getMessages() {
      this.loading = true
      axios.get('api/chat/to/' + this.getUser.id + '/from/' + this.$route.query.u + '/messages')
        .then(response => {
          this.chat = response.data.chat
          this.messages = response.data.messages
          this.loading = false
        })
        .catch(error => {
          console.log(error);
          this.loading = false
        })
    },
    sendMessage() {
      if (this.messageBody.length) {
        let payload = {
          from: this.getUser.id,
          body: this.messageBody,
          chat_id: this.chat.id ? this.chat.id : null,
          to: this.chat.id ? null : this.$route.query.u,
        }
        axios.post('api/messages/send', payload)
          .then(response => {
            if (this.chat.id == null) {
              this.chat = response.data.chat
            }
          })
          .catch()
        this.messageBody = ''
      }
    },
    startListening() {
      Echo.private('chatToUser.from.' + this.getUser.id + '.to.' + this.anotherUser.id)
        .listen('NewMessage', (data) => {
          this.messages.unshift(data.message)
        })
    },
    stopListening() {
      Echo.leave('chatToUser.from.' + this.getUser.id + '.to.' + this.anotherUser.id)
    }
  },
}
</script>

<style>

</style>