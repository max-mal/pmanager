<template>
      <v-card class="AddRecord--card">
        <v-card-title class="headline">Добавить запись</v-card-title>
        <v-card-text>
          <p>{{ message }}</p>
          <v-form ref="form" lazy-validation v-on:submit="addRecord">
            <v-text-field v-model="label" label="Название" required></v-text-field>
            <v-text-field v-model="username" label="Логин" ></v-text-field>
            <v-text-field v-model="email" label="E-mail"></v-text-field>
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
          <v-btn @click="randomPassword()">
              Cлучайный пароль
            </v-btn>
          <v-text-field v-model="note" label="Примечание"></v-text-field>
          <v-text-field v-model="url" label="URL"></v-text-field>

            <v-btn type="submit">
              Добавить
            </v-btn>
            
          </v-form>
        </v-card-text>
      </v-card>
</template>

<style>
  
  .AddRecord--card {
    margin-top: 20px;
    width: 100%;
  }

</style>
<script>

//const qs = require('querystring');
import api from '../utils/api'

export default {
  components: {

  },
  methods: {
    addRecord: function(event){
      let that = this
      event.preventDefault()

      api.call('post', 'AddRecord', {
        username: that.username,
        password: that.password,
        note: that.note,
        url: that.url,
        email: that.email,
        label: that.label,
        project_id: that.$attrs.project? that.$attrs.project.id: null,
      }).then((response) => {
        if (response.data.message) {
          that.message = response.data.message
        }
        that.$attrs.reload()
      })
      .catch(function (error) {
        console.log(error);
      });
      if (chrome.management) {
        chrome.runtime.sendMessage({action: "ClearCache"});
      }
    },
    randomPassword(length=8){
      let validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
      let array = new Uint8Array(length);
      window.crypto.getRandomValues(array);
      array = array.map(x => validChars.charCodeAt(x % validChars.length));
      let randomState = String.fromCharCode.apply(null, array);
      this.password = randomState
    }
      
  },
  data: function (){ 
    return {
      password: null,
      username: null,
      note: null,
      url: null,
      show: false,
      message: "",
      email: null,
      label: null,
    } 
  },
  mounted() {
    var that = this
    if (chrome.management) {
      chrome.tabs.query({active: true}, function (tabs) {
         let url = (tabs[0].url.replace('https://', '').replace('http://', '').split('/')[0])
         that.url = url
      });
    }
  },
}

</script>
