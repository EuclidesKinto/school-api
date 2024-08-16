<?php

namespace App\Actions\Lab;

use Lorisleiva\Actions\Action;
use App\Services\AwsService;
use App\Services\InstancesManager;

class DeployInstance extends Action
{

    protected $getAttributesFromConstructor = true;
    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize()
    {
        // check if user has an active instance (we actually allow allow one instance per user at the same time)
        if ((new InstancesManager)->getCurrentInstance()) {
            return false;
        }
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
    public function handle($resource_id)
    {
        $aws = new InstancesManager();
        return $aws->start($resource_id);
    }
}
