<?php

namespace Rodion15\PayokIo;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Rodion15\PayokIo\Traits\CallerTrait;
use Rodion15\PayokIo\Traits\ValidateTrait;

class PayokIo
{
    use ValidateTrait;
    use CallerTrait;

    //

    /**
     * EnotIo constructor.
     */
    public function __construct()
    {
        //
    }

    /**
     * @param $amount
     * @param $order_id
     * @param null $desc
     * @param null $payment_method
     * @param array $user_parameters
     * @return string
     */
    public function getPayUrl($amount, $order_id, $desc = null, $payment_method = null, $user_parameters = [])
    {
        // Url to init payment on EnotIo
        $url = config('payokio.pay_url');

        // Array of url query
        $query = [];

        // If user parameters array doesn`t empty
        // add parameters to payment query
        if (! empty($user_parameters)) {
            foreach ($user_parameters as $parameter => $value) {
                $query['custom'][$parameter] = $value;
            }
        }

        // Project id (merchat id)
        $query['shop'] = config('payokio.project_id');

        // Amount of payment
        $query['amount'] = $amount;

        // Order id
        $query['payment'] = $order_id;

        // Payment description (optional)
        if (! is_null($desc)) {
            $query['desc'] = $desc;
        }

        // Payment Method (optional)
        if (! is_null($payment_method)) {
            $query['method'] = $payment_method;
        }

        // Payment currency
        if (! is_null(config('payokio.currency'))) {
            $query['currency'] = config('payokio.currency');
        }

        // Payment success_url
        if (! is_null(config('payokio.success_url'))) {
            $query['success_url'] = config('payokio.success_url');
        }

        // Payment fail_url
        if (! is_null(config('payokio.fail_url'))) {
            $query['fail_url'] = config('payokio.fail_url');
        }

        $query['sign'] = $this->getFormSignature(
            config('payokio.project_id'),
            $amount,
            $desc,
            config('payokio.currency'),
            config('payokio.secret_key'),
            $order_id
        );

        // Merge url ang query and return
        return $url.'?'.http_build_query($query);
    }

    /**
     * @param $amount
     * @param $order_id
     * @param null $desc
     * @param null $payment_method
     * @param array $user_parameters
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToPayUrl($amount, $order_id, $desc = null, $payment_method = null, $user_parameters = [])
    {
        return redirect()->away($this->getPayUrl($amount, $order_id, $desc, $payment_method, $user_parameters));
    }

    /**
     * @param $project_id
     * @param $amount
     * @param $desc
     * @param $currency
     * @param $secret
     * @param $order_id
     * @return string
     */
    public function getFormSignature($project_id, $amount, $desc, $currency, $secret, $order_id)
    {
        $array = array ($amount, $order_id, $project_id, $currency, $desc, $secret);
        return md5 ( implode ( '|', $array ) );
    }

    /**
     * @param $secret
     * @param $desc
     * @param $currency
     * @param $project_id
     * @param $order_id
     * @param $amount
     * @return string
     */
    public function getSignature($secret, $desc, $currency, $project_id, $order_id, $amount)
    {
        $array = array ($secret, $desc, $currency, $project_id, $order_id, $amount);
        return md5 ( implode ( '|', $array ) );
    }

    /**
     * @param Request $request
     * @return string
     * @throws Exceptions\InvalidPaidOrder
     * @throws Exceptions\InvalidSearchOrder
     */
    public function handle(Request $request)
    {
        // Validate request from FreeKassa
        if (! $this->validateOrderFromHandle($request)) {
            return $this->responseError('validateOrderFromHandle');
        }

        // Search and get order
        $order = $this->callSearchOrder($request);

        if (! $order) {
            return $this->responseError('searchOrder');
        }

        // If order already paid return success
        if (Str::lower($order['_orderStatus']) === 'paid') {
            return $this->responseYES();
        }

        // PaidOrder - update order info
        // if return false then return error
        if (! $this->callPaidOrder($request, $order)) {
            return $this->responseError('paidOrder');
        }

        // Order is paid and updated, return success
        return $this->responseYES();
    }

    /**
     * @param $error
     * @return string
     */
    public function responseError($error)
    {
        return config('payokio.errors.'.$error, $error);
    }

    /**
     * @return string
     */
    public function responseYES()
    {
        // Must return 'YES' if paid successful

        return 'YES';
    }
}
