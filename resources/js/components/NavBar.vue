<template>
  <nav class="navbar navbar-expand-lg navbar-light bg-light fixed-top">
    <div class="container-fluid">
      <router-link class="navbar-brand" to="/">FriendChat</router-link>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse text-center" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link class="nav-link" aria-current="page" to="/">Home</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" aria-current="page" to="/about">About</router-link>
          </li>
          <li class="nav-item mx-2 d-flex align-items-center justify-content-center">
            <SearchBar />
          </li>
        </ul>
        <ul v-if="!getUser.auth" class="navbar-nav ml-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link class="nav-link" aria-current="page" to="/login">Log in</router-link>
          </li>
          <li class="nav-item">
            <router-link class="nav-link" aria-current="page" to="/signup">Sign Up</router-link>
          </li>
        </ul>
        <ul v-else class="navbar-nav ml-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <router-link :to="userId" class="nav-link" aria-current="page">{{ getUser.nickname }}</router-link>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</template>

<script>
import SearchBar from '@/js/components/SearchBar';
import {mapGetters} from 'vuex'

export default {
  computed: {
    ...mapGetters(['getUser']),
    userId() {
      return '/user/' + this.getUser.id
    }
  },
  components: {
    SearchBar
  }
}
</script>
