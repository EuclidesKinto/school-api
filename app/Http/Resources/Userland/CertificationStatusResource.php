<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class CertificationStatusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->when(Auth::user()->hasRole('admin'), $this->id),
            'deadline' => $this->deadline,
            'deadline_send_report' => $this->deadline_send_report,
            'user_report' => $this->user_report,
            'current_step' => $this->current_step,
            'comment' => $this->comment,
            'grade' => $this->grade,
            'approved' => $this->approved,
            'timeout' => $this->timeout,
            'url' => $this->url,
            'created_at' => $this->created_at
        ];
    }
}
