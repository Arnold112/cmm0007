import Vue from "vue";
import VueRouter from "vue-router";
// import Home from "../views/Home.vue";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: 'main.auth',
    component: () =>
      import(/* webpackChunkName: "about" */ "../views/Auth.vue"),

  },
  {
    path: "/app",
    component: () => import(/* webpackChunkName: "about" */ '../layouts/UserLayout.vue'),
    children: [
      {
        path: "experiments",
        name: "experiments.all",
        component: () =>
          import(/* webpackChunkName: "about" */ "../views/AllExperiments.vue"),

      },
      {
        path: 'eaos',
        name: "eaos.all",
        component: () =>
          import(/* webpackChunkName: "about" */ "../views/AllEAOs.vue"),
      },
      {
        path: 'students',
        name: "students.all",
        component: () =>
          import(/* webpackChunkName: "about" */ "../views/AllStudents.vue"),
      },
      {
        path: 'admins',
        name: "admins.all",
        component: () =>
          import(/* webpackChunkName: "about" */ "../views/AllAdmins.vue"),
      }
    ]
  }
];

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes
});

export default router;
