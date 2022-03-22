<template>
  <div class="container">
    <div class="col-sm-7 mx-auto mt-4">
      <div class="p-3 border bg-light mb-3"><img class="rounded-circle" style="width: 100px; height: 100px;" src="https://lifetimemix.com/wp-content/uploads/2021/06/1800x1200_cat_relaxing_on_patio_other.jpg" alt="cat"><span class="mx-3">{{ user.nickname }}</span></div>
      <button v-if="getUser.id == $route.params.id" class="btn btn-danger" @click="logout">Logout</button>
    </div>
  </div>  
</template>

<script>
import {mapMutations, mapGetters} from 'vuex';

export default {
  data() {
    return {
      user: {}
    }
  },
  methods: {
    ...mapMutations(['setUser', 'changeAuth']),
    getUserFromDb() {
      axios.get(window.location.origin + '/api/users/' + this.$route.params.id)
        .then(response => {
          this.user = response.data.data
        })
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
    }
  },
  computed: {
    ...mapGetters(['getUser'])
  },
  mounted() {
    this.getUserFromDb()
  }
}
</script>