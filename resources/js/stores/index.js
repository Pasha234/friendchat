import axios from 'axios';
import { createStore } from 'vuex';

// Create a new store instance.
const store = createStore({
  state () {
    return {
      user: {
        id: null,
        auth: false,
        nickname: '',
        email: '',
      }
    }
  },
  mutations: {
    changeAuth (state, value) {
      state.user.auth = value
    },
    setUser (state, user={}) {
      state.user.id = user.id ?? null;
      state.user.nickname = user.nickname ?? '';
      state.user.email = user.email ?? '';
      state.user.auth = user.auth ?? false
    }
  },
  actions: {
    async checkUser(ctx) {
      await axios('/checkUser')
      .then(response => {
        ctx.commit('changeAuth', response.data)
      })
    },
    async getUserFromServer(ctx) {
      await axios('/getUser')
      .then(response => {
        if (response.data.name) {
          ctx.commit('setUser', {
            id: response.data.id,
            nickname: response.data.name,
            email: response.data.email,
            auth: true
          })
        }
      })
    }
  },
  getters: {
    getUser(state) {
      return state.user
    }
  }
})

export default store;