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
      },
      token: null,
    }
  },
  mutations: {
    changeAuth (state, value) {
      state.user.auth = value
    },
    setUser (state, user={}, token=null) {
      state.user.id = user.id ?? null;
      state.user.nickname = user.nickname ?? '';
      state.user.email = user.email ?? '';
      state.user.auth = user.auth ?? false
      state.token = token
    },
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
        if (response.data.user) {
          ctx.commit('setUser', {
            id: response.data.user.id,
            nickname: response.data.user.name,
            email: response.data.user.email,
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