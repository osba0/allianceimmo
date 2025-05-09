<template>
  <div>
    <input
      type="text"
      v-model="localSearch"
      :placeholder="placeholder"
      :class = "localSearch=='' ? errorValidation :'' "
      @focus="showDropdown = true"
      @blur="closeDropdown"
      class="form-control"
    />
    <div v-if="showDropdown" class="dropdown bg-white border rounded shadow mt-1">
      <ul class="list-unstyled m-0">
        <li
          v-for="locataire in filteredLocataires"
          :key="locataire.id"
          @mousedown="selectLocataire(locataire)"
          class="px-3 py-2 hover:bg-light cursor-pointer"
        >
          {{ locataire.locat_nom }} {{ locataire.locat_prenom }}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>

export default {
  name: 'LocataireSelect',
  props: {
    list: Array,
    errorValidation: String | Object,
    placeholder: {
      type: String,
      default: "Rechercher un locataire..."
    }
  },
  data() {
    return {
      localSearch: "",
      showDropdown: false
    };
  },
  computed: {
    filteredLocataires() {
      const s = this.localSearch.toLowerCase();
      return this.list.filter((l) =>
        l.locat_nom?.toLowerCase().includes(s) ||
        l.locat_prenom?.toLowerCase().includes(s) ||
        String(l.locat_tel_1 || '').includes(s)
      );
    }
  },
  methods: {
    closeDropdown() {
      setTimeout(() => {
        this.showDropdown = false;
      }, 200);
    },
    selectLocataire(locataire) {
      this.$emit("selected", locataire);
      this.localSearch = `${locataire.locat_nom} ${locataire.locat_prenom}`;
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
