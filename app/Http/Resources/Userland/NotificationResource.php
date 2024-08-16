<?php

namespace App\Http\Resources\Userland;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
	/**
	 * Transform the resource into an array.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
	 */
	public function toArray($request)
	{
		return [
			"id" => $this->id,
			"title" => $this->title,
			"body" => $this->body,
			"is_read" => $this->is_read,
			"notifiable" => $this->notifiable,
			"notifiable_type" => $this->notifiable_type,
			"user" => [
				"name" => $this->user->name,
				"nick" => $this->user->nick,
				"avatar" => $this->user->avatar,
			],
			"type" => $this->type,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
		];
	}
}
