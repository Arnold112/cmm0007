import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import store from "./store";
import axios from 'axios'
import vuetify from "./plugins/vuetify";

Vue.config.productionTip = false;

axios.defaults.baseURL = process.env.VUE_APP_API_URI;
// console.log(process.env.VUE_APP_API_URI);
let token = localStorage.getItem(`arnold-token`);

if (token) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
  store.dispatch("getUser");
}

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount("#app");
