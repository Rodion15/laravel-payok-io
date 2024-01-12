<?php

namespace Rodion15\PayokIo\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static string handle(Request $request)
 * @method static string getPayUrl($amount, $order_id, $desc = null, $payment_method = null, $user_parameters = [])
 * @method static string redirectToPayUrl($amount, $order_id, $desc = null, $payment_method = null, $user_parameters = [])
 * @method static string getFormSignature($project_id, $amount, $desc, $currency, $secret, $order_id)
 *
 * @see \Rodion15\PayokIo\PayokIo
 */
class PayokIo extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'payokio';
    }
}
