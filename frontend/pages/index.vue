<template>
  <v-layout
    column
    justify-center
    align-center
  >
    <v-flex
      xs12
      sm8
      md6
    >
      <div class="text-xs-center">
        Loading...
      </div>

    </v-flex>
  </v-layout>
</template>

<script>

export default {
  mounted(){
    this.$router.push('/records')
  }
}
</script>
