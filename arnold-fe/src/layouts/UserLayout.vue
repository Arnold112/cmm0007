<template>
  <div>
    <v-app-bar app color="deep-purple accent-4" dark dense>
      <v-app-bar-nav-icon v-if="!$vuetify.breakpoint.mdAndUp" @click.stop="drawer = !drawer"></v-app-bar-nav-icon>
    </v-app-bar>
    <v-navigation-drawer v-model="drawer" app :permanent="$vuetify.breakpoint.mdAndUp">
      <v-list>
        <v-list-item class="px-2">
          <v-list-item-avatar size="70">
            <v-avatar color="red" size="70">
              <span class="white--text headline">{{user.name[0]}}</span>
            </v-avatar>
          </v-list-item-avatar>
        </v-list-item>

        <v-list-item link>
          <v-list-item-content>
            <v-list-item-title class="title">{{user.name}}</v-list-item-title>
            <v-list-item-subtitle>{{user.email}}</v-list-item-subtitle>
            <v-list-item-subtitle>
              <v-chip class="mt-2">{{user.type}}</v-chip>
            </v-list-item-subtitle>
          </v-list-item-content>
        </v-list-item>
      </v-list>

      <v-divider></v-divider>

      <v-list nav dense>
        <v-list-item link v-for="(item, ix) in items" :key="ix" :to="{name: item.link}">
          <v-list-item-icon>
            <v-icon>{{item.icon}}</v-icon>
          </v-list-item-icon>
          <v-list-item-title>{{item.title}}</v-list-item-title>
        </v-list-item>
      </v-list>

      <template v-slot:append>
        <div class="pa-2 d-flex">
          <v-btn block depressed @click="logoutUser">
            <v-icon class="mr-5">mdi-logout</v-icon>Logout
          </v-btn>
        </div>
      </template>
    </v-navigation-drawer>
    <v-container>
      <router-view />
    </v-container>
  </div>
</template>

<script>
export default {
  data() {
    return {
      drawer: true,
      adminItems: [
        {
          title: "Experiments",
          icon: "mdi-test-tube",
          link: "experiments.all"
        },
        { title: "EAOs", icon: "mdi-account-group", link: "eaos.all" },
        { title: "Students", icon: "mdi-school", link: "students.all" },
        { title: "Admins", icon: "mdi-shield-account", link: "admins.all" }
      ],
      generalItems: [
        {
          title: "Experiments",
          icon: "mdi-test-tube",
          link: "experiments.all"
        }
      ]
    };
  },
  computed: {
    user() {
      return this.$store.state.user;
    },
    items() {
      let res = [];
      if (this.user.type == "eao" || this.user.type == "student") {
        res = this.generalItems;
      } else if (this.user.type == "admin") {
        res = this.adminItems;
      }

      return res;
    }
  },
  methods: {
    logoutUser() {
      this.$store.dispatch("logoutUser");
    }
  }
};
</script>