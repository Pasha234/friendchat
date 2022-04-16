<template>
  <div class="container-sm" v-show="!loading">
    <div class="col-sm-5 mx-auto">
      <ul class="list-group my-3" v-if="chats.length > 0">
        <li class="list-group-item">
          <span>Chat List</span>
        </li>
        <li class="list-group-item d-flex flex-row align-items-center" v-for="chat in chats" :key="chat.id">
          <img class="rounded-circle image" style="width: 40px; height: 40px;" :src="chat.user.avatar ? chat.user.avatar : '/img/usernotfound.jpg'" :alt="chat.user.nickname + ' avatar'">
          <router-link :to="'/chat?u=' + chat.user.id" class="user-select-none mx-3">{{ chat.user.nickname }}</router-link>
          <span v-if="chat.new_messages > 0" class="badge bg-secondary ms-auto">{{ chat.new_messages }}</span>
        </li>
      </ul>
      <div v-else class="pt-4"><span>It seems you don't have any chats. Find users using search field above 😀</span></div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  data() {
    return {
      chats: [],
      loading: true,
    }
  },
  created() {
    this.getChats()
  },
  unmounted() {
    this.stopListeningForChats()
  },
  methods: {
    getChats() {
      this.loading = true
      axios.get('/api/user/chats')
        .then(response => {
          if (response.data) {
            this.chats = response.data.data.map(v => {
              return {
                user: v.to.id == this.getUser.id ? v.from : v.to,
                name: v.name,
                new_messages: v.new_messages,
                id: v.id
              }
            })
            this.startListeningForChats()
          }
          this.loading = false
        })
        .catch(error => {
          console.log(error);
          this.loading = false
        })
    },
    startListeningForChats() {
      Echo.private('user.' + this.getUser.id + '.chats')
        .listen('NewMessageInChats', (data) => {
          console.log(data);
          this.chats = this.chats.filter(v => v.id != data.chat.id)
          this.chats.unshift({
                user: data.chat.to.id == this.getUser.id ? data.chat.from : data.chat.to,
                name: data.chat.name,
                new_messages: data.chat.new_messages,
                id: data.chat.id
              })
        })
    },
    stopListeningForChats() {
      Echo.leave('user.' + this.getUser.id + '.chats')
    }
  },
  computed: {
    ...mapGetters(['getUser']),
  }
};
</script>
