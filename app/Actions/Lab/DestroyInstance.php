<?php

namespace App\Actions\Lab;

use App\Services\AwsService;
use App\Services\InstancesManager;
use Lorisleiva\Actions\Action;
use app\Models\Instance;

class DestroyInstance extends Action
{
    protected $getAttributesFromConstructor = true;
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
    public function handle(Instance $instance)
    {
        $aws = new InstancesManager();
        $ami_status = $aws->terminate($instance->remote_instance_id);
        return $ami_status;
    }
}
