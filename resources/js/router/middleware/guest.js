export default async function guest ({store}) {
  await store.dispatch('checkUser')
  if (store.getters.getUser.auth == true) {
    return {
      name: 'home'
    }
  }
}