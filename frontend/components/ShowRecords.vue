<template>
  <v-layout column justify-center align-center>
    <v-flex xs12 sm8 md6>
      <v-card>
        <v-card-title class="headline">Записи</v-card-title>
        <v-card-text>
          <p>{{ message }}</p>
            <v-text-field v-model="site_url" label="Url" required @change="getRecords"></v-text-field>
            <v-data-table
              :headers="headers"
              :items="records"
              class="elevation-1"
            >
              <template v-slot:items="props">
                <td>{{ props.item.id }}</td>
                <td>{{ props.item.data.label }}</td>
                <td>{{ props.item.data.username }}</td>
                <td>{{ props.item.data.email }}</td>
                <td>{{ props.item.data.url }}</td>
                <td><v-icon
                    small
                    @click="form = props.item, dialog=true"
                  >
                    assignment
                  </v-icon>
                  </nuxt-link>
                  <v-icon
                    small
                    @click="deleteItem(props.item.id)"
                  >
                    delete
                  </v-icon></td>
              </template>
            </v-data-table>

        </v-card-text>
      </v-card>
      <AddRecord :project="$attrs.project" :reload="getRecords"/>
    </v-flex>

    <v-dialog v-model="dialog" persistent max-width="290">
      <v-card>
        <v-card-title class="headline">Изменение записи</v-card-title>
        <v-card-text>
          {{ form_message }}
          
          <v-form ref="form" lazy-validation v-on:submit="updateRecord">
            <v-text-field v-model="form.data.label" label="Название" required></v-text-field>
            <v-text-field v-model="form.data.username" label="Логин" ></v-text-field>
            <v-text-field v-model="form.data.email" label="E-mail"></v-text-field>
            <v-text-field
            v-model="form.data.password"
            :append-icon="form_show ? 'visibility' : 'visibility_off'"
            :type="form_show ? 'text' : 'password'"
            name="password"
            label="Пароль"
            counter
            required
            @click:append="form_show = !form_show"
          ></v-text-field>
          <v-text-field v-model="form.data.note" label="Примечание"></v-text-field>
          <v-text-field v-model="form.data.url" label="URL"></v-text-field>

            <v-btn type="submit">
              Изменить
            </v-btn>
            
          </v-form>

        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn color="green darken-1" flat @click="dialog = false">Закрыть</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </v-layout>



</template>
<script>

//const qs = require('querystring');
import api from '../utils/api'
import AddRecord from './AddRecord'

export default {
  components: {
    AddRecord
  },
  methods: {

    getRecords(){
      let that = this
      api.getRecords({
        master_password: that.$store.state.masterPassword,
        project_id: that.$attrs.project? that.$attrs.project.id: null,
        url: that.site_url? that.site_url: null,
      }).then(function(response){
        if (response.data.success !== false) {
          that.records = response.data
        }
      })
    },
    updateRecord(event){
      let that = this

      event.preventDefault()

      api.call('post', 'UpdateRecord', {...that.form.data, id: that.form.id}).then((response)=>{
        that.form_message = response.data.message
      })

      if (window.environment == 'extension') {
        chrome.runtime.sendMessage({action: "ClearCache"});
      }
    },
    deleteItem(id){
      let that = this

      api.call('post', 'DeleteRecord', {
        record_id: id,
      }).then((response)=>{
        that.getRecords()
      })
      if (chrome.management) {
        chrome.runtime.sendMessage({action: "ClearCache"});
      }
    }
      
  },
  data: function (){ 
    return {
      records: [],
      headers: [
          {
            text: 'ID',
            align: 'left',
            sortable: true,
            value: 'id'
          },
          { text: 'Название', value: 'label', align: 'left', },
          { text: 'Логин', value: 'name', align: 'left', },
          { text: 'E-mail', value: 'email' },
          { text: 'URL', value: 'url' },
          { text: 'Действия', value: 'actions' },
        ],
      message: "",
      dialog: false,
      form: {
        id: null,
        data: {
          password: null,
        username: null,
        note: null,
        url: null,
        email: null,
        label: null,
        }
        
      },
      form_show: false,
      form_message: null,
      site_url: null,
    } 
  },
  mounted() {
    let that = this
    if (chrome.management) {
      chrome.tabs.query({active: true}, function (tabs) {
         let url = (tabs[0].url.replace('https://', '').replace('http://', '').split('/')[0])
         that.site_url = url
         that.form.url = url
         that.getRecords()
      });
    } else {
      this.getRecords()  
    }
    
  },
}

</script>
