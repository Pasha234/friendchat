export default async function auth ({store}) {
  await store.dispatch('checkUser')
  if (store.getters.getUser.auth == false) {
    return {
      name: 'login'
    }
  }
}