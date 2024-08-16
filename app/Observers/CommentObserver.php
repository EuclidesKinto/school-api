<?php

namespace App\Observers;

use App\Models\Comment;
use App\Models\Notification;


class CommentObserver
{
    public function created(Comment $comment){

        $nick_name = $comment->user->nick ? $comment->user->nick : $comment->user->name;


        if($comment->commentable->user_id != $comment->user_id){

            $notification = new Notification();
            $notification->title = 'New comment on your hacktivity';
            $notification->body = 'A new comment from ' . $nick_name . ' was posted on your hacktivity: ' . implode(' ', array_slice(explode(' ', $comment->message), 0, 5)) . '...';
            $notification->type = 'comment';
            $notification->notifiable()->associate($comment->commentable);
            $notification->user_id = $comment->commentable->user_id;
            $notification->save();


            $users = \App\Models\User::where('is_admin', true)->get();

            foreach($users as $user){
                if($user->id != $comment->user_id and $user->id != $comment->commentable->user_id){
                    $notification = new Notification();
                    $notification->title = 'New comment on a hacktivity';
                    $notification->body = 'A new comment from ' . $nick_name . ' was posted on a hacktivity: ' . implode(' ', array_slice(explode(' ', $comment->message), 0, 5)) . '...';
                    $notification->type = 'comment';
                    $notification->notifiable()->associate($comment->commentable);
                    $notification->user_id = $user->id;
                    $notification->save();
                }
            }

        }


        $all_chunked = array_chunk(explode(' ', $comment->message), 1);
        $all_nick = [];

        foreach($all_chunked as $chunk){
            if($chunk[0][0] == '@'){
                if(!in_array($chunk[0], $all_nick)) array_push($all_nick, $chunk[0]);
            }
        }

        foreach($all_nick as $nick){

            $user = \App\Models\User::where('nick', str_replace('@','',$nick))->get();

            if($user->first() and $user->first()->id != $comment->user_id){
                $notification_mention = new Notification();
                $notification_mention->title = 'You were mentioned on a comment';
                $notification_mention->body = 'You were mentioned on a comment from ' . $nick_name . ' on a hacktivity: ' . implode(' ', array_slice(explode(' ', $comment->message), 0, 5)) . '...';
                $notification_mention->type = 'mention';
                $notification_mention->notifiable()->associate($comment->commentable);
                $notification_mention->user_id = $user->first()->id;
                $notification_mention->save();
            }
        }
    }
}
