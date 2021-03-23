<template>
  <v-app>
    <v-main>
      <router-view></router-view>
      <v-snackbar v-model="snackbar.isActive" :timeout="snackbar.timeout">
        {{ snackbar.text }}
        <v-btn color="blue" text @click="closeSnackbar">Close</v-btn>
      </v-snackbar>
      <!-- <ExperimentLayout /> -->
    </v-main>
  </v-app>
</template>

<script>
// import ExperimentLayout from "./layouts/ExperimentLayout";

export default {
  name: "App",

  components: {
    // ExperimentLayout
  },

  data: () => ({}),
  computed: {
    dashboardItems() {
      return "";
    },
    snackbar() {
      return this.$store.state.snackbar;
    }
  },
  methods: {
    closeSnackbar() {
      return this.$store.commit("close-snackbar");
    }
  }
};
</script>
