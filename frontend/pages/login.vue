<template>
  <v-layout column justify-center align-center>
    <v-flex xs12 sm8 md6>
      <v-card>
        <v-card-title class="headline">Авторизация</v-card-title>
        <v-card-text>
          <p>{{ message }}</p>
          <v-form ref="form" lazy-validation v-on:submit="auth">
            <v-text-field v-model="login" label="Логин" required name="login"></v-text-field>
            <v-text-field
            v-model="password"
            :append-icon="show ? 'visibility' : 'visibility_off'"
            :type="show ? 'text' : 'password'"
            name="password"
            label="Пароль"
            counter
            required
            @click:append="show = !show"
          ></v-text-field>

            <v-btn type="submit">
              Вход
            </v-btn>
            
          </v-form>
        </v-card-text>
      </v-card>
    </v-flex>
  </v-layout>
</template>
<script>

//const qs = require('querystring');
import api from '../utils/api'

export default {
  components: {

  },
  methods: {
    auth: function(event){
      let that = this
      event.preventDefault()

      api.auth(this.login, this.password, this.$auth).then((result) => {
        if (result !== true) {
          that.message = result
        }
      })
      .catch(function (error) {
        console.log(error);
      });
    }
      
  },
  data: function (){ 
    return {
      password: null,
      login: null,
      show: false,
      message: "",
    } 
  },
  mounted() {
    api.checkAuth(this.$auth)
  },
}

</script>
