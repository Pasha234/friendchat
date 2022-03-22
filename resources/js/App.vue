<template>
  <NavBar />

  <router-view></router-view>
</template>
<script>
import NavBar from '@/js/components/NavBar';

export default {
  components: {
    NavBar
  }
};
</script>
