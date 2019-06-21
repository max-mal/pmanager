<template>
  <v-layout column justify-center align-center>
    <v-flex xs12 sm8 md6>
      <v-card>
        <v-card-title class="headline">Проекты</v-card-title>
        <v-card-text>
           <v-data-table
              :headers="headers"
              :items="projects"
              class="elevation-1"
            >
              <template v-slot:items="props">
                <td>{{ props.item.id }}</td>
                <td class="text-xs-right">{{ props.item.name }}</td>
                <td class="text-xs-right">{{ props.item.description }}</td>
                <td class="text-xs-right">
                  
                  <nuxt-link :to="`/projects/${props.item.id}`">
                  <v-icon
                    small
                  >
                    assignment
                  </v-icon>
                  </nuxt-link>
                  <v-icon
                    small
                    @click="deleteItem(props.item.id)"
                  >
                    delete
                  </v-icon>

                </td>
              </template>
            </v-data-table>

          
         
        </v-card-text>
      </v-card>
      <v-card>
        <v-card-title class="headline">Создать проект</v-card-title>
        <v-card-text>
           <v-form ref="form" lazy-validation v-on:submit="create">
            <p v-if="form_message">{{ form_message }}</p>
            <v-text-field v-model="project_name" label="Название" required name="name"></v-text-field>
            <v-btn type="submit">
              Создать
            </v-btn>
            
          </v-form>
        </v-card-text>

      </v-card>
    </v-flex>
  </v-layout>
</template>
<script>

//const qs = require('querystring');
import api from '../../utils/api'

export default {
  components: {

  },
  methods: {
    create: function(event){
      let that = this
      event.preventDefault()

      api.createProject({
        name: that.project_name
      }).then((result) => {
        if (result !== true) {
          that.form_message = result
        }
        that.getProjects()
      })
      .catch(function (error) {
        console.log(error);
      });
    },
    getProjects: function(){
      let that = this

      api.getProjects().then((response)=>{
        if (response.data.success !== false) {
          if (!response.data.length) {
            that.projects = []
          } else {
            that.projects = response.data    
          }
          
        }    
      })

    },
    deleteItem: function(id){
      let that = this
      api.deleteProject(id).then(()=>{
        that.getProjects()
      })
    }
  },
  data: function (){ 
    return {
      project_name: null,
      form_message: "",
      projects: false,
      headers: [
          {
            text: 'ID',
            align: 'left',
            sortable: true,
            value: 'id'
          },
          { text: 'Название', value: 'name', align: 'left', },
          { text: 'Описание', value: 'description' },
          { text: 'Действия', value: 'actions' },
        ],
      projects: [
      ]
    } 
  },
  mounted() {
    // api.checkAuth(this.$auth)
    this.getProjects()    

  },
}

</script>
