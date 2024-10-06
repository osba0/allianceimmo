<template>
    <div>
        <h2 class="titreH2"><i class="fa fa-arrow-right" aria-hidden="true"></i> Assignation des Permissions aux Rôles</h2>

        <!-- Sélection du rôle -->
        <div>
            <label for="selectedRole">Sélectionner un rôle :</label>
            <select v-model="selectedRole" @change="fetchRolePermissions">
                <option :value="null">Liste des rôles</option>
                <option v-for="role in roles" :key="role.id" :value="role.id" v-if="role.name!='root'"> <!--Filter le root-->
                    {{ role.name }}
                </option>
            </select>
            <button title="Actualiser les profils" class="text-primary h4 mb-0 ml-3 mr-2 rounded-circle bg-white" @click="refresh()"><i class="fas fa-sync-alt"></i></button>
        </div>

        <!-- Affichage des permissions par groupe -->
        <div v-if="selectedRole">
            <h3 class="titreH3"><i class="fa fa-cog" aria-hidden="true"></i> Permissions pour le rôle sélectionné</h3>
            <ul class="permissionUl">
                <li v-for="group in groups" :key="group.id">

                    <span @click="toggleGroupPermissions(group.id)">
                        <i class="fa fa-chevron-down" aria-hidden="true" v-if="group.showPermissions"></i>
                        <i class="fa fa-chevron-right" aria-hidden="true" v-else></i>
                        {{ group.name }}
                    </span>
                    <ul v-if="group.showPermissions">
                        <li v-for="permission in group.permissions" :key="permission.id">
                            <label>
                                <input type="checkbox" :value="permission.id" v-model="selectedRolePermissions" @change="updateRolePermissions(permission.id)" />
                                {{ permission.name.split(".")[1] }}
                            </label>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            roles: [],
            groups: [],
            selectedRole: null,
            selectedRolePermissions: []
        };
    },
    created() {
        this.fetchRoles();
        this.fetchGroupsWithPermissions();
    },
    methods: {
        fetchRoles() {
            axios.get('/api/roles')
                .then(response => {
                    this.roles = response.data;
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des rôles', error);
                });
        },
        fetchGroupsWithPermissions() {
            axios.get('/api/permission-groups')
                .then(response => {
                    this.groups = response.data.map(group => {
                        return { ...group, showPermissions: false };
                    });
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des groupes et permissions', error);
                });
        },
        fetchRolePermissions() {
            if (!this.selectedRole) return;

            axios.get(`/api/roles/${this.selectedRole}/permissions`)
                .then(response => {
                    this.selectedRolePermissions = response.data.map(permission => permission.id);
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des permissions du rôle', error);
                });
        },
        toggleGroupPermissions(groupId) {
            const group = this.groups.find(group => group.id === groupId);
            group.showPermissions = !group.showPermissions;
        },
        updateRolePermissions(permissionId) {
            const index = this.selectedRolePermissions.indexOf(permissionId);

            if (! (index > -1)) {
                axios.delete(`/api/roles/${this.selectedRole}/permissions/${permissionId}`)
                    .then(response => {
                        //this.selectedRolePermissions.splice(index, 1);
                    })
                    .catch(error => {
                        console.error('Erreur lors de la suppression de la permission du rôle', error);
                    });
            } else {
                axios.post(`/api/roles/${this.selectedRole}/permissions`, { permission_id: permissionId })
                    .then(response => {
                        //this.selectedRolePermissions.push(permissionId);
                    })
                    .catch(error => {
                        console.error('Erreur lors de l\'ajout de la permission au rôle', error);
                    });
            }
        },
        refresh(){
            this.fetchRoles();
            this.fetchGroupsWithPermissions();
        }
    }
};
</script>
