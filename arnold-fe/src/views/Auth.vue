<template>
  <v-app>
    <v-container fill-height style="background: #BEB4C5" fluid>
      <v-container class="pa-5 mx-auto">
        <h1 class="black--text mb-5">Arnold's Project</h1>
        <v-row align="center" justify="center">
          <v-col>
            <v-card class="pa-5">
              <v-card-title>{{isLogin? 'Login' : 'Sign up'}}</v-card-title>

              <v-card-text>
                <v-row>
                  <v-col cols="12" v-if="!isLogin">
                    <v-text-field v-model="name" label="Name"></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field v-model="email" label="Email"></v-text-field>
                  </v-col>
                  <v-col cols="12" v-if="!isLogin">
                    <v-text-field v-model="phoneNumber" label="Phone Number"></v-text-field>
                  </v-col>
                  <v-col cols="12">
                    <v-text-field v-model="password" label="Password" type="password"></v-text-field>
                  </v-col>
                </v-row>
              </v-card-text>

              <v-card-actions class="mb-5">
                <v-btn
                  :loading="isLoading"
                  block
                  color="primary"
                  depressed
                  @click="handleAction"
                >{{isLogin? 'Login' : 'Sign up'}}</v-btn>
              </v-card-actions>

              <h3 v-if="!isLogin">
                Already a user?
                <span
                  class="blue--text"
                  style="cursor: pointer"
                  @click="isLogin = true"
                >Login</span>
              </h3>
              <h3 v-else>
                New here?
                <span
                  class="blue--text"
                  style="cursor: pointer"
                  @click="isLogin = false"
                >Register</span>
              </h3>
            </v-card>
          </v-col>
        </v-row>
      </v-container>
    </v-container>
  </v-app>
</template>

<script>
export default {
  data() {
    return {
      isLogin: true,
      isLoading: false,
      name: "",
      email: "",
      phoneNumber: "",
      password: ""
    };
  },
  methods: {
    handleAction() {
      if (this.isLogin) {
        this.login();
      } else {
        this.signUp();
      }
    },
    login() {
      if (!this.email || !this.password) {
        // console.log("ere");

        this.$store.commit("openSnackbar", "Please enter all fields");
        return;
      }
      this.isLoading = true;

      let body = {
        email: this.email,
        password: this.password
      };

      this.$store.dispatch("loginUser", body);
      this.isLoading = false;

      console.log("rrr");
    },
    signUp() {
      if (!this.email || !this.password || !this.name || !this.phoneNumber) {
        // console.log("ere");

        this.$store.commit("openSnackbar", "Please enter all fields");
        return;
      }

      this.isLoading = true;

      let body = {
        email: this.email,
        password: this.password,
        name: this.name,
        phone_number: this.phoneNumber
      };

      this.$store.dispatch("register", body);
      this.email = "";
      this.password = "";
      this.name = "";
      this.phoneNumber = "";
      this.isLogin = true;
      this.isLoading = false;
    }
  },
  mounted() {
    if (localStorage.getItem("arnold-token")) {
      this.$router.push({ name: "experiments.all" });
    }
  }
};
</script>

