<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * 📥 Liste des notifications de l'utilisateur connecté.
     */
    public function index()
    {
       $user = Auth::user();
        $roles = $user->getRoleNames();

        $notifications = Notification::with(['users' => function($query) use ($user) {
            $query->where('user_id', $user->id)->select('user_id', 'notification_id', 'is_read');
        }])
        ->where(function ($query) use ($roles) {
            $query->whereNull('target_role')
                  ->orWhereIn('target_role', $roles);
        })
        ->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('is_read', false);
        })
        ->orderBy('created_at', 'desc')
        ->get();

        $unreadCount = $notifications->count();

        return response()->json([
            'code' => 0,
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * ✅ Marquer une notification comme lue.
     */
    public function markAsRead($notificationId)
    {
        $user = Auth::user();

        $user->notifications()->updateExistingPivot($notificationId, [
            'is_read' => true,
        ]);

        return response()->json([
            'code' => 0,
            'message' => 'Notification marquée comme lue.'
        ]);
    }

    /**
     * 🔥 Marquer toutes les notifications comme lues.
     */
    public function markAllAsRead()
    {
        $user = Auth::user();

        $user->notifications()->update(['is_read' => true]);

        return response()->json([
            'code' => 0,
            'message' => 'Toutes les notifications marquées comme lues.'
        ]);
    }

    /**
     * 🧹 Supprimer toutes les notifications de l'utilisateur.
     */
    public function clearAll()
    {
        $user = Auth::user();

        $user->notifications()->detach();

        return response()->json([
            'code' => 0,
            'message' => 'Toutes les notifications ont été supprimées.'
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function allNotif()
    {

        return view('notifications/index');
    }

    public function all(Request $request)
    {
        $paginate = $request->input('paginate');
        $user = Auth::user();
        $roles = $user->getRoleNames();

        $notifications = Notification::with(['users' => function($query) use ($user) {
            $query->where('user_id', $user->id)->select('user_id', 'notification_id', 'is_read');
        }])
        ->where(function ($query) use ($roles) {
            $query->whereNull('target_role')
                  ->orWhereIn('target_role', $roles);
        })
        ->whereHas('users', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })

        ;

         // Pagination ou non
        if ($paginate) {
            $results = $notifications->orderby("created_at","desc")->paginate($paginate);

        } else {
            $results = $notifications->orderby("created_at","desc")->get();
        }

        return response()->json([
            'code' => 0,
            'notifications' => $results,
            'meta' => [
                'pagination' => $results, // 👈 très important

            ]
        ]);
    }
}
