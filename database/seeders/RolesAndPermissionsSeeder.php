<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Créer le rôle Root
        $role = Role::create(['name' => 'root']);

        // Créer les groupes de permissions et les permissions associées
        $groups = [
            'Proprietaire',
            'Mandat',
            'Bien',
            'Locataire',
            'Rapport',
            'Bail',
            'Operations',
            'Rapports',
            'MonCompte',
            'GestionUtilisateur',
            'Preferences',
        ];

        foreach ($groups as $group) {
            $permissions = [
                'Ajouter ' . $group,
                'Modifier ' . $group,
                'Supprimer ' . $group,
            ];

            foreach ($permissions as $permission) {
                Permission::create(['name' => $permission]);
                $role->givePermissionTo($permission);
            }
        }

        // Créer le groupe Menu avec les permissions spécifiées
        $menuPermissions = [
            'Proprietaire',
            'Mandat',
            'Bien',
            'Locataire',
            'Rapport',
            'Bail',
            'Operations',
            'Rapports',
            'MonCompte',
            'GestionUtilisateur',
            'Preferences',
        ];

        foreach ($menuPermissions as $permission) {
            Permission::create(['name' => 'Menu ' . $permission]);
            $role->givePermissionTo('Menu ' . $permission);
        }
    }
}
