<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use DB;

class NotificationService
{
    /**
     * Créer une notification simple.
     */
    public static function create($type, $titre, $message, $payload = [], $actionBy = null, $role = null)
    {
        $notif = Notification::create([
            'type' => $type,
            'titre' => $titre,
            'message' => $message,
            'payload' => json_encode($payload),
            'action_by' => $actionBy ?? auth()->user()->username ?? 'Système',
            'target_role' => $role,
        ]);

        return $notif;
    }

     public static function creerNotification($type, $titre, $message, $payload = [], $targetRole = null)
    {
        DB::beginTransaction();
        try {
            // Création notification
            $notification = Notification::create([
                'type' => $type,
                'titre' => $titre,
                'message' => $message,
                'action_by' => auth()->user()->id ?? null,
                'payload' => json_encode($payload),
                'target_role' => $targetRole,
            ]);

            // 🎯 Si un rôle est ciblé
            if ($targetRole) {
                // ➡️ Spatie : récupère tous les users ayant ce rôle
                $users = User::role($targetRole)->get();
            } else {
                // ➡️ Sinon tous les utilisateurs
                $users = User::all();
            }

            // ➡️ Attacher les utilisateurs à la notification
            foreach ($users as $user) {
                $notification->users()->attach($user->id, ['is_read' => false]);
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur Notification : '.$e->getMessage());
            return false;
        }
    }

    /**
     * Créer une notification et la rattacher à plusieurs utilisateurs (notification_user).
     */
    public static function createForUsers($type, $titre, $message, $users, $payload = [], $actionBy = null)
    {
        $notif = self::create($type, $titre, $message, $payload, $actionBy);

        foreach ($users as $user) {
            $notif->users()->attach($user->id); // liaison dans notification_user
        }

        return $notif;
    }

    /**
     * Créer une notification pour tous les utilisateurs d'un rôle spécifique.
     */
    public static function createForRole($type, $titre, $message, $role, $payload = [], $actionBy = null)
    {
        $users = User::where('role', $role)->get();

        return self::createForUsers($type, $titre, $message, $users, $payload, $actionBy);
    }
}
