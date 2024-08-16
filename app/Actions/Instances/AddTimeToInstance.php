<?php

namespace App\Actions\Instances;

use Lorisleiva\Actions\Action;
use Carbon\Carbon;

class AddTimeToInstance extends Action
{
    /**
     * Determine if the user is authorized to make this action.
     *
     * @return bool
     */
    public function authorize()
    {
        $instance = $this->user()->instances()->active()->first();

        if (!empty($instance)) {
            $minutes_left = intval($instance->minutes_left);
            if ($minutes_left > 59) {
                return back()->with(['type' => 'error', 'message' => 'Voce nao pode adicionar mais tempo a esta maquina!']);
            }
            return true;
        }

        return redirect()->route('dashboard')->with(['type' => 'error', 'message' => 'Voce nao pode adicionar mais tempo a esta maquina!']);
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
        $instance = $this->user()->instances()->active()->first();
        if (!empty($instance)) {
            if (intval($instance->minutes_left) > 59) {
                return ["success" => false, "instance" => $instance];
            }
        }
        $instance->shutdown = $instance->shutdown->addHour();
        $instance->save();
        return ["success" => true, "instance" => $instance];
    }

    public function response($result)
    {
        $instance = $result['instance'];
        if ($result["success"]) {
            return redirect()->route('labs.show', ['id' => $instance->machine_id])->with(['type' => 'success', 'message' => '+1 hora foi adicionada ao lab ativo!']);
        }
        return redirect()->route('labs.show', ['id' => $instance->machine_id])->with(['type' => 'error', 'message' => 'Você não pode adicionar mais tempo nesta máquina!']);
    }
}
