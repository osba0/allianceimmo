<template>
  <div>
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex justify-content-between align-items-center gap-15">
            <div class="d-flex align-items-center">
                <label class="text-nowrap mr-2 mb-0">Nbre de ligne par Page</label>
                <select class="form-control form-control-sm" v-model="paginate">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="15">15</option>
                    <option value="20">20</option>
                </select>
            </div>

        </div>




    </div>
    <table class="table table-striped">
      <thead >
        <tr>
          <th>#</th>
          <th>Titre</th>
          <th>Message</th>
          <th>Date</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(notif, index) in notifications.data" :key="notif.id">
          <td>{{ index + 1 }}</td>
          <td>{{ notif.titre }}</td>
          <td>{{ notif.message }}</td>
          <td>{{ formatDate(notif.created_at) }}</td>
          <td>
            <span :class="notif.users[0]?.pivot.is_read ? 'text-success' : 'text-danger'">
              {{ notif.users[0]?.pivot.is_read ? '✔️ Lu' : '❗ Non lu' }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>

     <div class="d-flex mt-4 justify-content-center">
            <pagination
                :data="paginationMeta"
                :limit=10
                @pagination-change-page="fetchNotifications"
            ></pagination>
        </div>
  </div>
</template>

<script>
import axios from "axios";
import moment from "moment";

export default {
  data() {
    return {
      notifications: [],
      search: "",
      paginationMeta: {},
      paginate: 5
    };
  },
  watch: {
    deep: true,
     paginate: function(){
          this.fetchNotifications();
     },

  },
  methods: {
    fetchNotifications(page=1) {

      axios.get('/notifications/all?paginate='+this.paginate+'&page=' + page).then(res => {
        this.notifications = res.data.notifications;
        this.paginationMeta = res.data.meta.pagination;
      });
    },
    formatDate(date) {
      return moment(date).format("DD/MM/YYYY HH:mm");
    }
  },
  mounted() {
    this.fetchNotifications();
  }
};
</script>

<style scoped>
.table th, .table td {
  vertical-align: middle;
}
</style>
