import Vue from "vue";
import Vuex from "vuex";
import axios from 'axios';
import router from "../router";

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    user: {},
    eaos: [],
    snackbar: {
      timeout: 3000,
      text: "",
      isActive: false,
    },
  },
  mutations: {
    setUser(state, payload) {
      state.user = payload;
    },
    openSnackbar({ snackbar }, payload) {
      snackbar.text = payload;
      return (snackbar.isActive = true);
    },
    closeSnackbar({ snackbar }) {
      return (snackbar.isActive = false);
    },
    setEaos(state, payload) {
      state.eaos = payload
    }
  },
  actions: {
    async register({ commit }, payload) {
      try {
        return await axios.post('auth/register', payload).then(res => {

          let result = res.data;

          console.log(result)
          commit('openSnackbar', result.message)

          return result;
        })
      } catch (error) {
        console.log(error)
      }
    },
    async loginUser({ commit }, payload) {
      try {
        return axios.post('auth/login', payload).then(res => {
          let result = res.data;
          console.log(result)
          if (result?.status) {
            localStorage.setItem('arnold-token', result.token)
            axios.defaults.headers.common["Authorization"] = `Bearer ${result.token}`;
            commit('setUser', result.data);
            router.push({ name: 'experiments.all' })
          }

          commit('openSnackbar', result.message);

          return result;
        });
      } catch (error) {
        commit('openSnackbar', error);

        console.log(error)
      }
    },
    async logoutUser({ commit }) {
      try {
        return axios.post('auth/logout').then(res => {
          let result = res.data;

          if (result?.status) {
            localStorage.removeItem('arnold-token');
            commit('setUser', {});
          }

          router.push({ name: 'main.auth' })
          commit('openSnackbar', result.message);

          console.log(res.data);
          return result;
        }).catch(e => {
          console.log(e)
        })
      } catch (error) {
        console.log(error)
      }
    },
    async getUser({ commit }) {
      try {
        await axios.post('auth/me').then(res => {
          let result = res.data
          console.log(result)

          if (result?.status) {
            commit('setUser', result.data);
          }
        })
      } catch (error) {
        console.log(error)
      }
    },
    async getExperiments() {
      try {
        return axios.get('experiments').then(res => {
          let result = res.data


          return result;
        })
      } catch (error) {
        console.log(error)
      }
    },
    async createExperiment(context, payload) {
      try {
        return axios.post('experiments', payload).then(res => {
          let result = res.data
          return result;
        })
      } catch (error) {
        console.log(error)
      }
    },
    async getEaos({ commit }) {
      try {
        return axios.get('eaos').then(res => {
          let result = res.data
          commit('setEaos', result.data);
          return result;
        })
      } catch (error) {
        console.log(error)
      }
    },
    async getStudents() {
      try {
        return axios.get('students').then(res => {
          let result = res.data

          return result;
        })
      } catch (error) {
        console.log(error)
      }
    },
    async getAdmins() {
      try {
        return axios.get('admins').then(res => {
          let result = res.data

          return result;
        })
      } catch (error) {
        console.log(error)
      }
    },
  },
  modules: {}
});
