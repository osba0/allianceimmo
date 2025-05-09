<template>
  <div>
    <input
      type="text"
      v-model="proprioSearch"
      :placeholder="placeholder"
      :class = "proprioSearch=='' ? errorValidation :'' "
      @focus="showDropdown = true"
      @blur="closeDropdown"
      class="form-control"
    />
    <div v-if="showDropdown" class="dropdown bg-white border rounded shadow mt-1">
      <ul class="list-unstyled m-0">
        <li
          v-for="proprio in filteredPropriotaires"
          :key="proprio.id"
          @mousedown="selectLocataire(proprio)"
          class="px-3 py-2 hover:bg-light cursor-pointer"
        >
          {{ proprio.proprio_nom }} {{ proprio.proprio_prenom }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>

export default {
  name: 'ProprietaireSelect',
  props: {
    list: Array,
    errorValidation: String | Object,
    placeholder: {
      type: String,
      default: "Rechercher un propriétaire..."
    }
  },
  data() {
    return {
      proprioSearch: "",
      showDropdown: false
    };
  },
  computed: {
    filteredPropriotaires() {
      const s = this.proprioSearch.toLowerCase();
      return this.list.filter((l) =>
        l.proprio_nom?.toLowerCase().includes(s) ||
        l.proprio_prenom?.toLowerCase().includes(s) ||
        String(l.proprio_tel_1 || '').includes(s)
      );
    }
  },
  methods: {
    closeDropdown() {
      setTimeout(() => {
        this.showDropdown = false;
      }, 200);
    },
    selectLocataire(proprio) {
      this.$emit("selected", proprio);
      this.proprioSearch = `${proprio.proprio_nom} ${proprio.proprio_prenom}`;
      this.showDropdown = false;
    }
  }
};
</script>

<style scoped>
.cursor-pointer {
  cursor: pointer;
}
.hover\:bg-light:hover {
  background-color: #f8f9fa;
}
</style>
