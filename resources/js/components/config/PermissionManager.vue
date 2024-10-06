<template>
    <div>
        <h2 class="titreH2">
            <i class="fa fa-arrow-right" aria-hidden="true"></i> Gestion des Permissions et des Groupes
        </h2>

        <!-- Gestion des groupes de permissions -->
        <div>
            <h3 class="titreH3"><i class="fa fa-cog" aria-hidden="true"></i> Groupes de Permissions</h3>
            <div>
                <label for="newGroupName">Nom du nouveau groupe :</label>
                <input type="text" v-model="newGroupName" placeholder="Nouveau groupe"/>
                <button @click="createGroup" class="btnAdd">Créer Groupe</button>
            </div>
            <div v-if="groups.length">
                <h3 class="titreH3 mt-3"><i class="fa fa-cog" aria-hidden="true"></i> Liste des Permissions :</h3>
                <ul class="permissionUl">
                    <li v-for="group in groups" :key="group.id">
                        <span @click="toggleGroupPermissions(group.id)">
                            <i class="fa fa-chevron-down" aria-hidden="true" v-if="group.showPermissions"></i>
                            <i class="fa fa-chevron-right" aria-hidden="true" v-else></i>
                            {{ group.name }}
                        </span>

                        <ul v-if="group.showPermissions">
                            <li v-for="permission in group.permissions" :key="permission.id">
                                {{ permission.name.split(".")[1] }}
                            </li>
                            <li>
                                <input type="text" placeholder="Nouvelle permission" v-model="newPermissionNames[group.id]" />
                                <button @click="addPermissionByGroupe(group)" class="btnAdd">Ajouter Permission</button>
                            </li>
                        </ul>

                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            groups: [],
            permissions: [],
            newGroupName: '',
            selectedGroup: null,
            selectedPermissions: [],
            newPermissionName: '',
            newPermissionNames: []
        };
    },
    created() {
        this.fetchGroups();
        this.fetchPermissions();
    },
    methods: {
        fetchGroups() {
            axios.get('/api/permission-groups')
                .then(response => {
                    console.log(">>> Groupe", response.data)
                    this.groups = response.data.map(group => {
                        return { ...group, showPermissions: false };
                    });
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des groupes', error);
                });
        },
        fetchPermissions() {
            axios.get('/api/permissions')
                .then(response => {
                    console.log(">> permis", response.data)
                    this.permissions = response.data;
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des permissions', error);
                });
        },
        createGroup() {
            if (this.newGroupName.trim() === '') {
                alert('');
                 Vue.swal.fire(
                    'warning!',
                    'Le nom du groupe ne peut pas être vide',
                    'warning'
                  );
                return;
            }

            axios.post('/api/permission-groups/add', { name: this.newGroupName })
                .then(response => {
                    this.groups.push({ ...response.data.group, showPermissions: false });
                    this.newGroupName = '';
                    Vue.swal.fire({
                      title: 'Succès!',
                      text: 'Groupe créé avec succès',
                      icon: 'success',
                      confirmButtonText: 'OK',
                    }).then((result) => {
                      if (result.isConfirmed) {
                        window.location.reload();
                      }
                    });

                })
                .catch(error => {
                    console.error('Erreur lors de la création du groupe', error);
                });
        },
        toggleGroupPermissions(groupId) {
            const group = this.groups.find(group => group.id === groupId);
            group.showPermissions = !group.showPermissions;
        },
        addPermission() {
            if (this.newPermissionName.trim() === '') {
                alert('Le nom de la permission ne peut pas être vide');
                return;
            }

            axios.post('/api/permissions/store', { name: this.newPermissionName, group_id: this.selectedGroup.id })
                .then(response => {
                    this.permissions.push(response.data.permission);
                    this.selectedPermissions.push(response.data.permission.id);
                    this.newPermissionName = '';
                     Vue.swal.fire(
                        'succés!',
                        'Permission ajoutée avec succès',
                        'success'
                      );
                })
                .catch(error => {
                    console.error('Erreur lors de l\'ajout de la permission', error);
                });
        },
        addPermissionByGroupe(group){

             if (this.newPermissionNames[group.id].trim() === '') {
                alert('Le nom de la permission ne peut pas être vide');
                return;
            }

            this.selectedGroup = group;

            axios.post('/api/permissions/store', { name: this.selectedGroup.name+"."+this.newPermissionNames[group.id], group_id: this.selectedGroup.id })
                .then(response => {
                    this.selectedGroup.permissions.push(response.data.permission);

                    this.selectedPermissions.push(response.data.permission.id);
                    this.newPermissionNames[group.id] = '';
                    alert('Permission ajoutée avec succès');
                })
                .catch(error => {
                    console.error('Erreur lors de l\'ajout de la permission', error);
                });
        }
    }
};
</script>
