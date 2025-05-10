<template>
  <div class="dropdown position-relative d-inline-block">
    <!-- Cloche avec badge -->
    <button class="btn btn-transparent position-relative" @click="toggleDropdown">
      <span class="text-info h3 mb-0"><i class="fas fa-bell"></i></span>
      <transition name="badge">
        <span v-if="unread > 0" class="badge badge-danger position-absolute" style="top: -2px; right: -0px;">
          {{ unread }}
        </span>
      </transition>
    </button>

    <!-- Dropdown -->
    <transition name="fade">
      <div v-if="open" class="dropdown-menu dropdown-menu-right show p-2" style="width: 320px; max-height: 400px; overflow-y: auto;">
        <div v-if="notifications.length === 0" class="text-center text-muted">Pas de notifications</div>

        <div v-for="notif in notifications" :key="notif.id" class="dropdown-item small d-flex justify-content-between align-items-center" @click="markAsRead(notif.id)">
          <div>
            <strong style="text-transform: capitalize;">{{ notif.titre }}</strong><br/>
            <small>{{ notif.message }}</small>
          </div>
          <span v-if="notif.pivot && !notif.pivot.is_read" class="badge badge-pill badge-primary ml-2">Nouveau</span>
        </div>

        <div class="dropdown-divider"></div>

        <button v-if="unread > 0" class="btn btn-sm btn-block btn-outline-primary" @click="markAllAsRead">
          Tout marquer comme lu
        </button>
      </div>
    </transition>
  </div>
</template>

<script>
export default {
  data() {
    return {
      open: false,
      notifications: [],
      unread: 0
    };
  },
  methods: {
    toggleDropdown() {
      this.open = !this.open;
      if (this.open) {
        this.fetchNotifications();
      }
    },
    fetchNotifications() {
      axios.get('/notifications').then(res => {
        this.notifications = res.data.notifications;
        this.unread = res.data.unread_count;
      });
    },
    markAsRead(notificationId) {
      axios.post(`/notifications/${notificationId}/mark-read`).then(() => {
        this.fetchNotifications();
      });
    },
    markAllAsRead() {
      axios.post('/notifications/mark-all-read').then(() => {
        this.fetchNotifications();
      });
    },
    closeDropdown() {
      this.open = false;
    },
    handleClickOutside(event) {
      if (!this.$el.contains(event.target)) {
        this.open = false;
      }
    }
  },
  mounted() {
     document.addEventListener('click', this.handleClickOutside);
    this.fetchNotifications();
    setInterval(() => {
      this.fetchNotifications();
    }, 60000); // Toutes les 60 secondes
  },
  beforeDestroy() {
    document.removeEventListener('click', this.handleClickOutside);
  }
}
</script>

<style scoped>
.dropdown-menu {
  box-shadow: 0px 2px 10px rgba(0,0,0,0.15);
}
.badge-danger {
  font-size: 0.7rem;
  animation: pulse 1.2s infinite;
}

/* Petite animation de badge */
@keyframes pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.2); }
  100% { transform: scale(1); }
}

/* Fade animation du dropdown */
.fade-enter-active, .fade-leave-active {
  transition: all .3s ease;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
  transform: translateY(-10px);
}
</style>
