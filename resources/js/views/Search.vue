<template>
  <div class="container">
    <div class="col-6 mx-auto mt-3 d-flex flex-row justify-content-center" v-if="loading">
      <div class="spinner-grow" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>
    <div class="col-6 mx-auto mt-3" v-else>
      <div v-show="!foundUsers.length" class="mx-auto w-50"><h1 class="display-6">No users found</h1></div>
      <div v-for="user in foundUsers" :key="user.id">
        <router-link :to="'/user/' + user.id">
          <div class="p-3 border bg-light mb-3"><img class="rounded-circle" style="width: 40px; height: 40px;" :src="user.avatar ? user.avatar : '/img/usernotfound.jpg'" :alt="user.nickname + ' avatar'"><span class="mx-3">{{ user.nickname }}</span></div>
        </router-link>
      </div>
      <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination" v-if="paginationNumber <= 1">
          <li class="page-item" v-if="getPage != 1"><router-link class="page-link" :to="createPaginationLink(Number(getPage) - 1)">Previous</router-link></li>
          <li class="page-item" v-for="i in paginationNumber" :key="i"><router-link class="page-link" :to="createPaginationLink(i)">{{ i }}</router-link></li>
          <li class="page-item" v-if="getPage < paginationNumber"><router-link class="page-link" :to="createPaginationLink(Number(getPage) + 1)">Next</router-link></li>
        </ul>
        <ul class="pagination" v-else>
          <li class="page-item" v-if="getPage !== 1"><router-link class="page-link" :to="createPaginationLink(Number(getPage) - 1)">Previous</router-link></li>
          <li class="page-item" v-if="getPage !== 1"><router-link class="page-link" :to="createPaginationLink(1)">1</router-link></li>
          <div v-if="getPage >= 4" class="mx-2">...</div>
          <li class="page-item" v-if="getPage >= 3"><router-link class="page-link" :to="createPaginationLink(getPage - 1)">{{ getPage - 1 }}</router-link></li>
          <li class="page-item"><router-link class="page-link" :to="createPaginationLink(getPage)">{{ getPage }}</router-link></li>
          <li class="page-item" v-if="getPage <= paginationNumber - 2"><router-link class="page-link" :to="createPaginationLink(getPage + 1)">{{ getPage + 1 }}</router-link></li>
          <div v-if="getPage <= paginationNumber - 3" class="mx-2">...</div>
          <li class="page-item" v-if="getPage !== paginationNumber"><router-link class="page-link" :to="createPaginationLink(paginationNumber)">{{ paginationNumber }}</router-link></li>
          <li class="page-item" v-if="getPage < paginationNumber"><router-link class="page-link" :to="createPaginationLink(Number(getPage) + 1)">Next</router-link></li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      loading: true,
      foundUsers: {},
      count: 0,
      href: window.location.href
    }
  },
  methods: {
    getUsers() {
      if (!this.getSearchString) {
        return 
      }
      this.loading = true
      axios.get(window.location.origin + `/api/users/search?s=${this.getSearchString}&p=${this.getPage}`).then(response => {
        if (response.data.data) {
          this.foundUsers = response.data.data
          this.count = response.data.count
        }
        this.loading = false
      })
    },
    createPaginationLink(number) {
      return this.$route.path + '?s=' + this.$route.query.s + '&p=' + number
    }
  },
  mounted() {
    this.getUsers()
  },
  computed: {
    getSearchString() {
      return this.$route.query.s
    },
    getPage() {
      return this.$route.query.p != undefined ? Number(this.$route.query.p) : 1
    },
    paginationNumber() {
      return Math.ceil(this.count / 10)
    }
  },
  watch: {
    getSearchString() {
      this.getUsers()
    },
    getPage() {
      this.getUsers()
    }
  }
}
</script>
