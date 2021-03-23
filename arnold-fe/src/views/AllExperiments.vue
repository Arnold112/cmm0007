<template>
  <v-container fill-height>
    <h1>All Experiments</h1>

    <v-row class="mt-16">
      <v-col>
        <v-card>
          <v-card-title class="d-flex align-center">
            <v-btn color="success" class="mr-5" @click="dialog = true">Create Experiment</v-btn>
            <v-text-field
              v-model="search"
              append-icon="mdi-magnify"
              label="Search"
              single-line
              hide-details
            ></v-text-field>
          </v-card-title>
          <v-data-table :loading="isFetching" :headers="headers" :items="items" :search="search">
            <template v-slot:item.status="{ item }">
              <v-chip :color="getColor(item.status)" dark>{{ getStatus(item.status) }}</v-chip>
            </template>
            <template v-slot:item.eaos="{ item }">
              <div v-for="eao in item.eaos" :key="eao.id">{{eao.name }},</div>
            </template>
            <template v-slot:item.actions="{     item     }">
              <v-btn icon @click="viewExperiment(item)" class="mr-2">
                <v-icon small>mdi-eye</v-icon>
              </v-btn>
              <v-btn icon v-if="user.type == 'admin'" @click="assignEao(item)" class="mr-2">
                <v-icon small>mdi-clipboard-account</v-icon>
              </v-btn>
            </template>
            <template v-slot:no-data>
              <v-btn color="primary" @click="getExperiments">Reset</v-btn>
            </template>
          </v-data-table>
        </v-card>
      </v-col>
    </v-row>
    <v-row justify="center">
      <v-dialog v-model="dialog" persistent max-width="600px">
        <v-card>
          <v-card-title>
            <span class="headline">Create Experiment</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-text-field v-model="name" label="Title of Experiment*" required></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-textarea v-model="description" label="Description*" type="text" required></v-textarea>
                </v-col>
              </v-row>
              <v-card-title>
                <span class="headline">Ethics Section</span>
              </v-card-title>
              <v-row>
                <v-col cols="12">
                  <v-file-input
                    v-model="additionalFiles"
                    chips
                    :rules="rules"
                    multiple
                    label="Additional Files"
                  ></v-file-input>
                </v-col>
              </v-row>
            </v-container>
            <small>*indicates required field</small>
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn
              color="blue darken-1"
              text
              @click="dialog = false; name = '', description = '', additionalFiles = []"
            >Close</v-btn>
            <v-btn
              color="success darken-1"
              text
              @click="createExperiment"
              :loading="isCreating"
            >Save</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
    <v-row justify="center">
      <v-dialog v-model="showExperiment" persistent max-width="600px">
        <v-card>
          <v-card-title>
            <span
              class="headline"
            >{{ !isAssign ? 'View Experiment' : 'Assign Experiment Approval Officers'}}</span>
          </v-card-title>
          <v-card-text>
            <v-container>
              <v-row>
                <v-col cols="12">
                  <v-text-field
                    readonly
                    label="Title of Experiment"
                    :value="selectedExperiment.name"
                    required
                  ></v-text-field>
                </v-col>

                <v-col cols="12">
                  <v-textarea
                    readonly
                    label="Description"
                    :value="selectedExperiment.description"
                    type="text"
                    required
                  ></v-textarea>
                </v-col>
              </v-row>
              <v-card-title v-if="!isAssign">
                <span class="headline">Ethics Section</span>
              </v-card-title>
              <v-row v-if="!isAssign">
                <v-col cols="12">
                  
      <v-chip v-for="doc in selectedExperiment.files" :key="doc.id"
     
        small
        label
        color="primary"
      >
      <a :href="doc.file_path" target="_blank" class="white--text"> 
        {{ doc.name }}</a>
      </v-chip>
    
                </v-col>
              </v-row>
              <v-row v-if="isAssign">
                <v-col cols="12">
                  <v-select
                    v-model="selectedEaos"
                    :items="eaos"
                    item-text="name"
                    item-value="id"
                    chips
                    label="Select Experiment Approval Officers"
                    multiple
                  ></v-select>
                </v-col>
              </v-row>
            </v-container>
            <!-- <v-container>
            </v-container>-->
            <!-- <small>*indicates required field</small> -->
          </v-card-text>
          <v-card-actions>
            <v-spacer></v-spacer>
            <v-btn color="blue darken-1" text @click="closeDialog">Close</v-btn>
            <v-btn
              color="error darken-1"
              v-if="!isAssign && (user.type == 'admin' || user.type == 'eao')"
              text
              @click="handleExperiment(false)"
            >Decline</v-btn>
            <v-btn
              color="success darken-1"
              text
              v-if="!isAssign && (user.type == 'admin' || user.type == 'eao')"
              @click="handleExperiment(true)"
            >Approve</v-btn>
            <v-btn color="success darken-1" v-if="isAssign" @click="submitAssigned">Assign</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </v-row>
  </v-container>
</template>


<script>
import axios from "axios";
export default {
  data() {
    return {
      dialog: false,
      search: "",
      additionalFiles: [],
      selectedEaos: [],
      isCreating: false,
      isFetching: false,
      rules: [],
      headers: [
        {
          text: "Title",
          align: "start",
          filterable: true,
          value: "name"
        },
        { text: "Student", value: "student.name" },
        { text: "EAOs", value: "eaos" },
        { text: "Status", value: "status" },
        { text: "Date Created", value: "created_at" },
        { text: "Actions", value: "actions", sortable: false }
      ],
      items: [],
      selectedExperiment: {},
      showExperiment: false,
      isAssign: false,
      name: "",
      description: ""
    };
  },
  computed: {
    eaos() {
      return this.$store.state.eaos;
    },
    user() {
      return this.$store.state.user;
    }
  },
  methods: {
    getColor(status) {
      if (status == 0) return "red";
      else if (status == 1) return "orange";
      else return "green";
    },
    getStatus(status) {
      if (status == 0) return "declined";
      else if (status == 1) return "pending";
      else return "approved";
    },
    viewExperiment(experiment) {
      this.selectedExperiment = experiment;
      this.showExperiment = true;
    },
    assignEao(experiment) {
      this.isAssign = true;
      this.selectedExperiment = experiment;
      this.showExperiment = true;
    },
    handleExperiment(action = false) {
      this.showExperiment = false;
      this.isAssign = false;
      if (action) {
        axios
          .post(`handle-experiment`, {
            status: true,
            experiment_id: this.selectedExperiment.id
          })
          .then(res => {
            console.log(res.data);
            this.getExperiments();
          });
        console.log("approved");
      } else {
        axios
          .post(`handle-experiment`, {
            status: false,
            experiment_id: this.selectedExperiment.id
          })
          .then(res => {
            console.log(res.data);
            this.getExperiments();
          });
        console.log("declined");
      }
    },
    closeDialog() {
      this.showExperiment = false;
      this.isAssign = false;
      this.selectedExperiment = {};
      this.selectedEaos = [];
    },
    submitAssigned() {
      let body = {
        experiment_id: this.selectedExperiment.id,
        eao_ids: JSON.stringify(this.selectedEaos)
      };
      //   console.log(this.selectedEaos, body);

      axios.post("handle-assignment", body).then(res => {
        this.$store.commit("openSnackbar", res.data.message);

        this.getExperiments();
        this.closeDialog();
        // console.log(res);
      });
    },
    async getExperiments() {
      this.isFetching = true;
      let res = await this.$store.dispatch("getExperiments");

      if (res.status) {
        this.items = res.data ? res.data : [];
      }

      this.isFetching = false;

      console.log(res.data);
    },
    async createExperiment() {
      try {
        if (!this.name || !this.description) {
          return this.$store.commit(
            "openSnackbar",
            "Please provide a title and description for this experiment"
          );
        }
        this.isCreating = true;
        let formData = new FormData();

        for (var i = 0; i < this.additionalFiles.length; i++) {
          let file = this.additionalFiles[i];
          formData.append("additional_files[" + i + "]", file);
        }

        formData.append("name", this.name);
        formData.append("description", this.description);

        let res = await this.$store.dispatch("createExperiment", formData);
        console.log(res);
        if (res.status) {
          this.dialog = false;
          this.getExperiments();
        }
        this.$store.commit("openSnackbar", res.message);
        this.isCreating = false;
      } catch (error) {
        console.log(error);
      }
    }
  },
  mounted() {
    this.getExperiments();
    if (this.eaos.length == 0) {
      this.$store.dispatch("getEaos");
    }
  }
};
</script>