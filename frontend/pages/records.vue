<template>
  <v-layout column justify-center align-center>
    <v-flex xs12 sm8 md6>
  
      <ShowRecords :project="false"/>

    </v-flex>

  </v-layout>
</template>
<script>

//const qs = require('querystring');
// import api from '../../utils/api'

import ShowRecords from '../components/ShowRecords'

export default {
  components: {
    ShowRecords
  },
  methods: {
    
  },
  data: function (){ 
    return {
    } 
  },
  mounted() {

  },
}

</script>
