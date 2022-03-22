<template>
  <div class="search-container w-auto d-flex flex-row">
      <input type="text" class="form-control" v-model.trim="searchString" @keypress.enter="search">
    <div class="search__button align-self-center mx-2" role="button" @click="search">
      <img src="/img/search.png" alt="search">
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      searchString: ''
    }
  },
  methods: {
    search() {
      if (this.searchString !== '') {
        if (this.$router.currentRoute._value.name === 'search') {
          this.$router.push({
            name: 'search',
            query: {
              s: this.searchString
            }
          })
        //   this.loading = true
        //   axios.get(window.location.origin + `/api/users/search?s=${this.$route.query.s}`).then(response => {
        //     if (response.data.data) {
        //       this.foundUsers = response.data.data
        //     }
        //     this.loading = false
        //   })
        }
        this.$router.push({
          name: 'search',
          query: {
            s: this.searchString
          }
        })
        this.searchString = ''
        document.activeElement.blur();
      }
    }
  }
}
</script>