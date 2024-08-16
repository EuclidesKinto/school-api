<?php

namespace App\Exceptions\Orders;

use Exception;

class InvalidOrderException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report()
    {
        //
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

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $this->getMessage(),
                'success' => false,
            ], $this->getCode());
        }
    }
}
