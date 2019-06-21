<template>
  <v-layout column justify-center align-center>
    <v-flex xs12 sm8 md6>
      <v-card>
        <v-card-title class="headline">Участники проекта</v-card-title>
        <v-card-text>
          <p>{{ message }}</p>
          
            <v-data-table
              :headers="headers"
              :items="members"
              class="elevation-1"
            >
              <template v-slot:items="props">
                <td>{{ props.item.id }}</td>
                <td>{{ props.item.username }}</td>
                <td>{{ props.item.name }} {{ props.item.surname }}</td>
                <td>
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

      <v-card v-if="users.length">
        <v-card-title class="headline">Добавить участника</v-card-title>
        <v-card-text>
          <p>{{ form.message }}</p>
          <v-form ref="form" lazy-validation v-on:submit="addMember">
           
             <v-select
              v-model="form.user"
              :items="users"
              :hint="`${form.user.name} ${form.user.surname} ${form.user.username}`"
              item-text="username"
              item-value="id"
              label="Выберите пользователя"
               persistent-hint
              return-object
              single-line
            ></v-select>

            <v-btn type="submit">
              Добавить
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
import AddRecord from './AddRecord'

export default {
  components: {
    AddRecord
  },
  methods: {

    getMembers(){
      let that = this
      api.call('post', 'ProjectGetUsers', {
        project_id: that.$attrs.project.id,
      }).then(function(response){
        if (response.data.success !== false) {
          that.members = response.data
        }
      })
    },
    deleteItem(id){
      let that = this

      api.call('post', 'ProjectRemoveMember', {
        user_id: id,
        project_id: that.$attrs.project.id
      }).then((response)=>{
        if (response.data.message) {
          that.message = response.data.message
        }
        that.getMembers()
      })
    },
    addMember(event){
      let that = this

      event.preventDefault()

      api.call('post', 'ProjectAddMember', {
        project_id: that.$attrs.project.id,
        user_id: that.form.user.id
      }).then((response)=>{
        that.form.message = response.data.message

        that.getMembers()
      })
    },
    getUsers(){
      let that = this
      api.call('get', 'ListUsers', false).then((response)=>{
      
          that.users = response.data
      })
    }
      
  },
  data: function (){ 
    return {
      members: [],
      users: [],
      headers: [
          {
            text: 'ID',
            align: 'left',
            sortable: true,
            value: 'id'
          },
          { text: 'Username', value: 'username', align: 'left', },
          { text: 'Пользователь', value: 'name', align: 'left', },
          { text: 'Действия', value: 'actions' },
        ],
      message: "",
      form:{
        message: null,
        user: {
          name: '',
          surname: '',
          username: ''
        }
      }
    } 
  },
  mounted() {
    this.getMembers()
    this.getUsers()
  },
}

</script>
