<template>
  <v-container fill-height>
    <h1>All Students</h1>

    <v-row class="mt-16">
      <v-col>
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-btn color="success" class="mr-5" @click="dialog = true">Create Student</v-btn>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search students"
              single-line
              hide-details
            ></v-text-field>
          </v-card-title>
          <v-data-table :loading="isFetching" :headers="headers" :items="students" :search="search"></v-data-table>
        </v-card>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-dialog v-model="dialog" persistent max-width="600px">
        <v-card>
          <v-card-title>
            <span class="headline">Create Student</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-text-field v-model="name" label="Name*" required></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="phoneNumber" label="Phone Number*" required></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="email" label="Email*" required></v-text-field>
                </v-col>
                <v-col cols="12">
                  <v-text-field v-model="password" label="Password*" type="password" required></v-text-field>
                </v-col>
              </v-row>
            </v-container>
            <small>*indicates required field</small>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="dialog = false">Close</v-btn>
            <v-btn color="blue darken-1" text @click="register">Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
  </v-container>
</template>


<script>
export default {
  data() {
    return {
      search: "",
      isFetching: false,
      isCreating: false,
      isLoading: false,
      name: "",
      email: "",
      phoneNumber: "",
      password: "",
      dialog: false,
      students: [],
      headers: [
        {
          text: "Name",
          align: "start",
          filterable: false,
          value: "name"
        },
        // { text: "Calories", value: "calories" },
        { text: "Email", value: "email" },
        { text: "Phone Number", value: "phone_number" }
      ]
    };
  },
  methods: {
    async getStudents() {
      try {
        this.isFetching = true;
        let res = await this.$store.dispatch("getStudents");

        if (res.status) {
          this.students = res.data;
        }
        this.isFetching = false;
      } catch (error) {
        console.log(error);
        this.isFetching = false;
      }
    },
    async register() {
      if (!this.email || !this.password || !this.name || !this.phoneNumber) {
        // console.log("ere");

        this.$store.commit("openSnackbar", "Please enter all fields");
        return;
      }

      let body = {
        email: this.email,
        password: this.password,
        name: this.name,
        phone_number: this.phoneNumber,
        user_type: "student"
      };

      await this.$store.dispatch("register", body).then(() => {
        this.getStudents();
        this.email = "";
        this.password = "";
        this.name = "";
        this.phoneNumber = "";
        this.dialog = false;
      });

      this.isLoading = false;
    }
  },
  mounted() {
    this.getStudents();
  }
};
</script>