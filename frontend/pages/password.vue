<template>
  <v-layout column justify-center align-center>
    <v-flex xs12 sm8 md6>
      <v-card>
        <v-card-title class="headline">Хранилище</v-card-title>
        <v-card-text>
          <p>{{ message }}</p>
          <v-form ref="form" lazy-validation v-on:submit="auth" >
            <v-text-field
            v-if="!$store.state.masterPassword"
            v-model="password"
            :append-icon="show ? 'visibility' : 'visibility_off'"
            :type="show ? 'text' : 'password'"
            name="password"
            label="Пароль"
            counter
            required
            @click:append="show = !show"
          ></v-text-field>

            <v-btn type="submit" v-if="!$store.state.masterPassword">
              Вход
            </v-btn>
            <v-btn type="button" color="red" @click="deleteStorage">
              Удалить хранилище
            </v-btn>
          </v-form>
        </v-card-text>
      </v-card>

      <v-card v-if="$store.state.masterPassword">
        <v-card-title class="headline">Изменить master пароль</v-card-title>
        <v-card-text>
          <p>{{ new_password_message }}</p>
          <v-form ref="form" lazy-validation v-on:submit="changePassword" >
            <v-text-field
            v-model="new_password"
            :append-icon="show ? 'visibility' : 'visibility_off'"
            :type="show ? 'text' : 'password'"
            name="password"
            label="Новый пароль"
            counter
            required
            @click:append="show = !show"
          ></v-text-field>

            <v-btn type="submit">
              Изменить
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
      if (!that.createStorage) {
        api.getRecords({
          master_password: that.password
        }).then((response) => {
          if (response.data.success === false && response.data.status === 0) {
            that.createStorage = true
            
          } else if (response.data.success !== false) {
            // that.$store.commit('setMasterPassword', that.password)
            that.storePassword(that.password)
            that.$router.push('/')
          }
          that.message = response.data.message
        })
      } else {
        api.call('post', 'CreateKeyChain', {
          master_password: that.password
        }).then((response)=>{
          if (response.data.success) {
            that.$router.push('/')
            that.createStorage = false
            // that.$store.commit('setMasterPassword', that.password)
            that.storePassword(that.password)
          } else {
            that.message = response.data.message
          }
        })
      }
      
      
    },
    changePassword(){
      let that = this
      event.preventDefault()

      api.call('post', 'ChangeMasterPassword', {
        master_password: that.$store.state.masterPassword,
        new_password: that.new_password,
      }).then((response)=>{
        that.new_password_message = response.data.message

        if (response.data.success) {
          that.storePassword(new_password)
        }
      })
    },
    deleteStorage: function(event){
      event.preventDefault()
      let that = this

      api.call('get', 'DeleteKeyChain', false).then((response)=>{
        that.message = response.data.message
        that.$store.commit('setMasterPassword', '')
      })

    },
    storePassword(password){
      let that = this
      if (chrome.management) {
        chrome.runtime.sendMessage({action: "password", password});
        console.log('Sending password....')
      }
      that.$store.commit('setMasterPassword', password)
    } 
      
  },
  data: function (){ 
    return {
      password: null,
      show: false,
      message: "",
      createStorage: false,
      new_password: null,
      new_password_message: "",
    } 
  },
  mounted() {
    let that = this
    if (chrome.management && !that.$store.state.masterPassword) {
      chrome.runtime.sendMessage({action: "GetPassword"}, function(response) {
        if (response.password) {
          that.$store.commit('setMasterPassword', response.password)
          that.$router.push('/')
        }
      });
    }
    
    api.call('get', 'GetKeyChain', false).then((response) =>{
      if (response.data.status !== 1 ) {
        that.createStorage = true
        that.message = response.data.message
      }
    })
  },
}

</script>
