<template>
  <v-layout column justify-center align-center>
    <v-flex xs12 sm8 md6>
      <v-card v-if="project">
        <v-card-title class="headline">{{ project.name }} 
                  <v-icon
                    small
                    @click="dialog = true"
                  >
                    edit
                  </v-icon></v-card-title>
        <v-card-text>
            <pre>{{ project.description }}</pre>

        </v-card-text>
        <ProjectMembers :project="project"/>
        <ShowRecords :project="project"/>
      </v-card>
    </v-flex>

    <v-dialog v-model="dialog" persistent max-width="290" v-if="project">
      <v-card>
        <v-card-title class="headline">Изменение проекта</v-card-title>
        <v-card-text>
          {{ message }}
          <v-form ref="form" lazy-validation v-on:submit="editProject">
            <v-text-field v-model="form.name" label="Название" required name="name" value="project.name"></v-text-field>
            <v-textarea
              solo
              name="description"
              label="Описание"
              v-model="form.description"
              value="project.description"
            ></v-textarea>

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
import api from '../../utils/api'

import ShowRecords from '../../components/ShowRecords'
import ProjectMembers from '../../components/ProjectMembers'

export default {
  components: {
    ShowRecords,
    ProjectMembers,
  },
  methods: {
    getProject: function(){
      let that = this
      api.getProject(this.$route.params.id).then((response)=>{
        if (response.data.success !== false) {
          that.project = response.data
          that.form = {... response.data, project_id: response.data.id}
        }
      })

      api.getRecords({
        master_password: that.$store.state.masterPassword
      }).then(function(response){
        if (response.data.success !== false) {
          that.records = response.data
        }
      })
    },
    editProject: function(event){
      let that = this
      event.preventDefault()
      api.updateProject(this.form).then((response)=>{
        if (response.data.success) {
          that.dialog = false
          that.getProject()
        } else {
          that.message = response.data.message
        }
      })
      
    }
  },
  data: function (){ 
    return {
      dialog: false,
      project: null,
      message: null,
      form: null,
      records: [],
      headers: [
          {
            text: 'ID',
            align: 'left',
            sortable: true,
            value: 'id'
          },
          // { text: 'Название', value: 'name', align: 'left', },
          // { text: 'Описание', value: 'description' },
          // { text: 'Действия', value: 'actions' },
        ],
    } 
  },
  mounted() {

    this.getProject()

  },
}

</script>
