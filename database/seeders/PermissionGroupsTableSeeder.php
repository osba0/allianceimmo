<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use DB;

class PermissionGroupsTableSeeder extends Seeder
{
    public function run()
    {
        // Créer les groupes de permissions
        $groups = [
            'Menu' => [
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
                'LoyerPaiement',
                'ChargeFrais',
                'GestionAgence'
            ],
            'Proprietaire' => ['Ajouter', 'Modifier', 'Supprimer'],
            'Mandat' => ['Ajouter', 'Modifier', 'Supprimer'],
            'Bien' => ['Ajouter', 'Modifier', 'Supprimer'],
            'Locataire' => ['Ajouter', 'Modifier', 'Supprimer'],
            'Rapport' => ['Ajouter', 'Modifier', 'Supprimer'],
            'Bail' => ['Ajouter', 'Modifier', 'Supprimer'],
            'Operations' => ['Ajouter', 'Modifier', 'Supprimer'],
            'GestionUtilisateur' => ['Ajouter', 'Modifier', 'Supprimer'],
            'Preferences' => ['Ajouter', 'Modifier', 'Supprimer'],
            'LoyerPaiement' => ['Ajouter', 'Modifier', 'Supprimer'],
            'ChargeFrais' => ['Ajouter', 'Modifier', 'Supprimer'],
            'GestionAgence' => ['Ajouter', 'Modifier', 'Supprimer'],
            'AfficherSolde' => ['Oui']
        ];

        foreach ($groups as $group => $permissions) {
            // Insérer le groupe dans la table permission_groups
            $groupId = DB::table('permission_groups')->insertGetId([
                'name' => $group,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Associer les permissions au groupe
            foreach ($permissions as $permission) {
                $permissionName = "{$group}.{$permission}";
                Permission::firstOrCreate(['name' => $permissionName, 'group_id' => $groupId]);
            }
        }
    }
}
