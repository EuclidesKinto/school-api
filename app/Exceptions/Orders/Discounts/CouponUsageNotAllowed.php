<?php

namespace App\Exceptions\Orders\Discounts;

use Exception;

class CouponUsageNotAllowed extends Exception
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

        if ($request->wantsJson()) {
            $response = [
                'message' => $this->getMessage(),
                'success' => false
            ];
            $status = 401;
            return response()->json($response, $status);
        }
    }
}
