<?php

namespace App\Actions\Instances;

use Lorisleiva\Actions\Action;
use App\Models\Instance;
use App\Actions\Lab\DestroyInstance;

class ShutdownInstances extends Action
{
    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the action.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }

    /**
     * Execute the action and return a result.
     *
     * @return mixed
     */
    public function handle()
    {

       $active_instances = Instance::mustShutdown()->count();
       if($active_instances > 0)
       {
            $instances = Instance::mustShutdown()->get();
            $instances->each(function ($instance) {
                $instance->is_active = false;
                $instance->shutdown = now();
                $instance->save();
            });
       }
    }
}
