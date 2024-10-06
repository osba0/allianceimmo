<template>
    <div>
        <h2 class="titreH2"><i class="fa fa-arrow-right" aria-hidden="true"></i> Gestion des Rôles</h2>
        <form @submit.prevent="addRole">
            <label for="role">Nom du Rôle :</label>
            <input type="text" v-model="roleName" placeholder="Nouveau rôle"/>
            <button type="submit" class="btnAdd">Ajouter Rôle</button>
        </form>

        <ul>
            <li v-for="role in roles" :key="role.id">{{ role.name }}</li>
        </ul>
    </div>
</template>

<script>
export default {
    data() {
        return {
            roles: [],
            roleName: ''
        };
    },
    created() {
        this.fetchRoles();
    },
    methods: {
        fetchRoles() {
            axios.get('/api/roles').then(response => {
                this.roles = response.data;
            });
        },
        addRole() {
            axios.post('/api/roles', { name: this.roleName }).then(response => {
                this.roles.push(response.data.role);
                this.roleName = '';
            });
        }
    }
}
</script>
