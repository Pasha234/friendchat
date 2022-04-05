<template>
  <div class="container-sm" v-show="!loading">
    <div class="col-sm-5 mx-auto">
      <ul class="list-group my-3" v-if="users.length > 0">
        <li class="list-group-item">
          <span>Chat List</span>
        </li>
        <li class="list-group-item" v-for="user in users" :key="user.id">
          <img class="rounded-circle image" style="width: 40px; height: 40px;" :src="user.avatar ? user.avatar : '/img/usernotfound.jpg'" :alt="user.nickname + ' avatar'">
          <router-link :to="'/chat?u=' + user.id" class="user-select-none mx-3">{{ user.nickname }}</router-link>
        </li>
      </ul>
      <div v-else class="pt-4"><span class="">It seems you don't have any chats. Find users using search field above 😀</span></div>
    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex';

export default {
  data() {
    return {
      users: [],
      loading: true,
    }
  },
  created() {
    this.getChats()
  },
  methods: {
    getChats() {
      this.loading = true
      axios.get('/api/user/chats')
        .then(response => {
          if (response.data) {
            this.users = response.data.data.map(v => {
              return v.to.id == this.getUser.id ? v.from : v.to
            })
          }
          this.loading = false
        })
        .catch(error => {
          console.log(error);
          this.loading = false
        })
    }
  },
  computed: {
    ...mapGetters(['getUser']),
  }
};
</script>
