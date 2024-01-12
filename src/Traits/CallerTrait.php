<?php

namespace Rodion15\PayokIo\Traits;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Rodion15\PayokIo\Exceptions\InvalidPaidOrder;
use Rodion15\PayokIo\Exceptions\InvalidSearchOrder;

trait CallerTrait
{
    /**
     * @param Request $request
     * @return mixed
     *
     * @throws InvalidSearchOrder
     */
    public function callSearchOrder(Request $request)
    {
        if (is_null(config('payokio.searchOrder'))) {
            throw new InvalidSearchOrder();
        }

        return App::call(config('payokio.searchOrder'), ['order_id' => $request->input('payment')]);
    }

    /**
     * @param Request $request
     * @param $order
     * @return mixed
     * @throws InvalidPaidOrder
     */
    public function callPaidOrder(Request $request, $order)
    {
        if (is_null(config('payokio.paidOrder'))) {
            throw new InvalidPaidOrder();
        }

        return App::call(config('payokio.paidOrder'), ['order' => $order]);
    }
}
