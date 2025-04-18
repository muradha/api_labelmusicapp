<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\User;
use App\Notifications\AnnouncementNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('view announcements');
        $announcements = Announcement::all();

        return response()->json([
            'data' => $announcements
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create announcements');
        $data = $request->validate([
            'title' => 'required|string|max:100|unique:announcements,title',
            'content' => 'required|string|max:100'
        ]);

        $announcement = Announcement::create($data);

        $users = User::whereNotNull('email_verified_at')->get();

        Notification::send($users, new AnnouncementNotification($announcement));

        return response()->json([
            'data' => $announcement
        ], 201);
    }

    public function notification()
    {
        $notifications = Auth::user()->unreadNotifications->where('type', AnnouncementNotification::class)->first();

        $data = [];

        if (!empty($notifications) && $notifications->count() > 0) {
            $data = [
                'id' => $notifications->id,
                'title' => $notifications->data['title'],
                'content' => $notifications->data['content'],
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }
}
