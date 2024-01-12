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
            'merchant' => 'required',
            'amount' => 'required',
            'intid' => 'required',
            'merchant_id' => 'required',
            'sign' => 'required',
            'sign_2' => 'required',
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
        $sign = $this->getSignature(config('payokio.project_id'), $request->input('amount'), config('payokio.secret_key'), $request->input('merchant_id'));

        if ($request->input('sign_2') != $sign) {
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
