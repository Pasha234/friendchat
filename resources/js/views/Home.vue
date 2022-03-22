<template>
  <div>Home component</div>
</template>

<script>
import Echo from 'laravel-echo'
window.Pusher = require('pusher-js');
export default {
  created() {
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '9341235782',
        wsHost: window.location.hostname,
        wsPort: 6001,
        forceTLS: false,
        disableStats: true,
    });
  }
};
</script>
