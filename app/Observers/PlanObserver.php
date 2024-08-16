<?php

namespace App\Observers;

use App\Models\Plan;
use stdClass;

class PlanObserver
{

    /**
     * Handle the Plan "creating" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function creating(Plan $plan)
    {
       
    }

    /**
     * Handle the Plan "created" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function created(Plan $plan)
    {

    }

    /**
     * Handle the Plan "updated" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function updated(Plan $plan)
    {
        /**
         * Atualiza os detalhes do produto junto com o plano
         */
        $plan->updateDefaultProduct();

        /**
         * Deleta o plano com as informações antigas na pagarme
         * E cria um plano novo, com info atualizada
         * @todo Criar método para atualizar o plano na pagarme
         */
        // $plan->deleteOnPagarme();
        // $plan->registerOnPagarme();
    }

    /**
     * Handle the Plan "deleted" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function deleted(Plan $plan)
    {
        /**
         * Deleta todas as informações
         * relacionadas ao plano no 
         * banco de dados
         */
        // $plan->deleteOnPagarme();
    }

    /**
     * Handle the Plan "restored" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function restored(Plan $plan)
    {
        // $plan->registerOnPagarme();
    }

    /**
     * Handle the Plan "force deleted" event.
     *
     * @param  \App\Models\Plan  $plan
     * @return void
     */
    public function forceDeleted(Plan $plan)
    {
        // $plan->deleteOnPagarme();
    }
}
