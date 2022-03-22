<template>
  <div class="container">
    <form novalidate :style="{'was-validated': validated}">
      <div class="col-sm-7 mx-auto mt-4" v-show="step === 1">

        <h2>Login</h2>

        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input v-model="email" type="text" name="email" id="email" class="form-control" :class="{'is-invalid': errors.email !== ''}">
          <div class="invalid-feedback" v-show="errors.email !== ''">
            {{ errors.email }}
          </div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <input v-model="password" type="password" name="password" id="password" class="form-control" :class="{'is-invalid': errors.password !== ''}">
          <div class="invalid-feedback" v-show="errors.password !== ''">
            {{ errors.password }}
          </div>
        </div>

        <button type="button" @click="nextStep" class="btn btn-primary">Next step</button>

      </div>
    </form>
  </div>
</template>

<script>
import {mapMutations} from 'vuex'

export default {
  data() {
    return {
      step: 1,
      email: '',
      password: '',
      validated: false,
      errors: {
        email: '',
        password: '',
      }
    }
  },
  methods: {
    ...mapMutations(['setUser', 'changeAuth']),
    nextStep() {
      // this.step++
      this.validated = true
      axios.post(window.location.href, {
        email: this.email,
        password: this.password,
      })
      .then(response => {
        if (response.data.errors) {
          let errors = response.data.errors
          for (let i in this.errors) {
            if (errors.hasOwnProperty(i)) {
              this.errors[i] = errors[i]
            } else {
              this.errors[i] = '';
            }
          }
        } else {
          this.changeAuth(true)
          this.setUser(response.data.user)
          this.$router.push({
            name: 'home',
          })
        }
      })
      .catch(error => {
        if (error.response) {
          let errors = error.response.data.errors
          for (let i in this.errors) {
            if (errors.hasOwnProperty(i)) {
              this.errors[i] = errors[i][0]
            } else {
              this.errors[i] = '';
            }
          }
        }
      })
    }
  }
};
</script>
