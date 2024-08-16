<?php

namespace App\Exceptions\Pagarme;

use Exception;

class CardVerificationException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        return true;
    }

    /**
     * Render the exception as an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function render($request)
    {
        // reporta a exceção
        report($this);

        if ($request->wantsJson()) {
            $response = [
                'message' => 'Não foi possível registrar o cartão de crédito no gateway de pagamento. Por favor, tente novamente.',
                'success' => false,
                'errors' => [
                    'code' => $this->getCode(),
                    'message' => $this->getMessage(),
                ],
            ];

            $status = 400;

            return response()->json($response, $status);
        }
    }
}
