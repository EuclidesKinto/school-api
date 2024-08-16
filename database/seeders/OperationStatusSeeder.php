<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\OperationStatus;
use Illuminate\Support\Facades\DB;

class OperationStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // creates a set of default operation statuses
        DB::table('operation_statuses')->delete();
        collect(
            [
                [
                    'name' => 'authorized_pending_capture',
                    'description' => 'Transação Autorizada pendente de captura',
                    'category' => 'payment'
                ],
                [
                    'name' => 'not_authorized',
                    'description' => 'Não autorizada',
                    'category' => 'payment'
                ],
                [
                    'name' => 'captured',
                    'description' => 'Transação realizada com sucesso!',
                    'category' => 'payment'
                ],
                [
                    'name' => 'partial_capture',
                    'description' => 'Transação Capturada parcialmente.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'waiting_capture',
                    'description' => 'Transação Aguardando captura.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'refunded',
                    'description' => 'Transação Estornada.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'voided',
                    'description' => 'Transação Cancelada.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'partial_refunded',
                    'description' => 'Transação Estornada parcialmente.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'partial_void',
                    'description' => 'Transação Cancelada parcialmente.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'error_on_voiding',
                    'description' => 'Erro no cancelamento da transação.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'error_on_refunding',
                    'description' => 'Erro no estorno da transação.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'waiting_cancellation',
                    'description' => 'Aguardando cancelamento da transação.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'with_error',
                    'description' => 'Transação realizada com erro.',
                    'category' => 'payment'
                ],
                [
                    'name' => 'failed',
                    'description' => 'Falha no processamento da transação.',
                    'category' => 'payment'
                ]
            ]
        )->each(function ($opStatus) {
            OperationStatus::create($opStatus);
        });
    }
}
