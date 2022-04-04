<template>
  <div :class="{
    message: true,
    owner: owner,
  }">
    <div class="rounded p-2 bg-primary text-white message-body"><span>{{ body }}</span></div>
    <strong class="date" v-if="date">{{ new Date(date).toLocaleString([], {hour: '2-digit', minute:'2-digit', year:'2-digit', month:'2-digit', day:'2-digit'}) }}</strong>
  </div>
</template>

<script>
export default {
  props: ['body', 'owner', 'date']
}
</script>

<style>

</style>