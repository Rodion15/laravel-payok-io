<?php

namespace Rodion15\PayokIo\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait ValidateTrait
{
    /**
     * @param Request $request
     * @return bool
     */
    public function validate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'desc' => 'required',
            'currency' => 'required',
            'shop' => 'required',
            'payment_id' => 'required',
            'amount' => 'required',
            'sign' => 'required',
        ]);

        if ($validator->fails()) {
            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function validateSignature(Request $request)
    {
        $sign = $this->getSignature(config('payokio.secret_key'), $request->input('desc'), $request->input('currency'), config('payokio.project_id'), $request->input('payment_id'), $request->input('amount'));

        if ($request->input('sign') != $sign) {
            return false;
        }

        return true;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function validateOrderFromHandle(Request $request)
    {
        return $this->validate($request)
            && $this->validateSignature($request);
    }
}
