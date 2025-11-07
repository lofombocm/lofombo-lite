<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function showNotificationView(string $notificationid){
        $notification = Notification::where('id', $notificationid)->first();
        return view('notification.detail', ['notification' => $notification, 'data' => json_decode($notification->data, true)]);
    }

    public function setAsReadOrUnread(Request $request, string $notificationid){
        if (!Auth::check() && !Auth::guard('client')->check()) {
            session()->flash('error', 'Vous n\'etes pas autorise');
            return back()->withErrors(['error' => 'Vous n\'etes pas autorise']);
        }
        $validator = Validator::make($request->all(), [
            'action'            => 'required|string|in:read,unread',
        ]);

        if($validator->fails()){
            session()->flash('error', $validator->errors()->first());
            return back()->withErrors(['error' => $validator->errors()->first()]);
        }

        $notification = Notification::where('id', $notificationid)->first();
        if ($notification == null) {
            session()->flash('error', 'Notification not found');
            return back()->withErrors(['error' => 'Notification not found']);
        }

        $read = false;
        if ($request->get('action') == 'read') {
            $read = true;
        }else if ($request->get('action') == 'unread') {
            $read = false;
        }else{
            session()->flash('error', 'Action not allowed');
            return back()->withErrors(['error' => 'Action not allowed']);
        }

        Notification::where('id', $notificationid)->update(['read' => $read]);

        $msg = 'Notification marquee comme ' . ($read ? ' lue ' : ' non lue') . ' avec succes!';
        session()->flash('status', $msg);
        return back()->with('status', $msg);
    }
}
