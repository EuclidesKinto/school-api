<?php

namespace App\Observers;

class HacktivityObserver
{
    public function created($hacktivity)
    {
        if($hacktivity->type == 'release'){
            
            $notification = new \App\Models\Notification();
            $notification->title = 'New release available';
            $notification->body = 'The ' . $hacktivity->subject->name . ' machine was released!';
            $notification->type = 'release';
            $notification->notifiable()->associate($hacktivity);
            // associate this notification to all users
            $notification->user()->associate(\App\Models\User::all());
            $notification->save();
            
        }

        if($hacktivity->type == 'lesson'){

            // generate notification for all users with is_admin = true

            $all_admins = \App\Models\User::where('is_admin', true)->get();
            
            foreach($all_admins as $admin){
                $notification = new \App\Models\Notification();
                $notification->title = 'New comment on lesson';
                $notification->body = 'The ' . $hacktivity->user->name . ' posted comment on: ' . $hacktivity->subject->name;
                $notification->type = 'lesson';
                $notification->notifiable()->associate($hacktivity);
                $notification->user()->associate($admin);
                $notification->save();
            }

        }


        
    }
}
