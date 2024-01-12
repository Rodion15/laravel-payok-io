# Laravel payment processor package for PayokIo gateway

Accept payments via PayokIo ([payok.io](https://payok.io/)) using this Laravel framework package ([Laravel](https://laravel.com)).

- receive payments, adding just the two callbacks

#### Laravel >= 10.*, PHP >= 8.1

## Installation

Require this package with composer.

``` bash
composer require rodion15/laravel-payok-io
```

If you don't use auto-discovery, add the ServiceProvider to the providers array in `config/app.php`

```php
Rodion15\PayokIo\PayokIoServiceProvider::class,
```

Add the `PayokIo` facade to your facades array:

```php
'PayokIo' => Rodion15\PayokIo\Facades\PayokIo::class,
```

Copy the package config to your local config with the publish command:
``` bash
php artisan vendor:publish --provider="Rodion15\PayokIo\EnotIoServiceProvider"
```

## Configuration

Once you have published the configuration files, please edit the config file in `config/payokio.php`.

- Create an account on [payok.io](payok.io)
- Add your project, copy the `project_id`, `secret_key` params and paste into `config/payokio.php`
- After the configuration has been published, edit `config/payokio.php`
- Set the callback static function for `searchOrder` and `paidOrder`
- Create route to your controller, and call `PayokIo::handle` method
 
## Usage

1) Generate a payment url or get redirect:

```php
$amount = 100; // Payment`s amount

$url = PayokIo::getPayUrl($amount, $order_id);

$redirect = PayokIo::redirectToPayUrl($amount, $order_id);
```

You can add custom fields to your payment:

```php
$rows = [
    'time' => Carbon::now(),
    'info' => 'Local payment'
];

$url = PayokIo::getPayUrl($amount, $order_id, $desc, $payment_methood, $rows);

$redirect = PayokIo::redirectToPayUrl($amount, $order_id, $desc, $payment_methood, $rows);
```

`$desc` and `$payment_methood` can be null.

2) Process the request from PayokIo:
``` php
PayokIo::handle(Request $request)
```

## Important

You must define callbacks in `config/payokio.php` to search the order and save the paid order.


``` php
'searchOrder' => null  // PayokIoController@searchOrder(Request $request)
```

``` php
'paidOrder' => null  // PayokIoController@paidOrder(Request $request, $order)
```

## Example

The process scheme:

1. The request comes from `payok.io` `GET` / `POST` `http://yourproject.com/payokio/result` (with params).
2. The function`PayokIoController@handlePayment` runs the validation process (auto-validation request params).
3. The method `searchOrder` will be called (see `config/payokio.php` `searchOrder`) to search the order by the unique id.
4. If the current order status is NOT `paid` in your database, the method `paidOrder` will be called (see `config/payokio.php` `paidOrder`).

Add the route to `routes/web.php`:
``` php
 Route::get('/payokio/result', 'PayokIoController@handlePayment');
```

> **Note:**
don't forget to save your full route url (e.g. http://example.com/payokio/result ) for your project on [payok.io](payok.io).

Create the following controller: `/app/Http/Controllers/PayokIoController.php`:

``` php
class PayokIoController extends Controller
{
    /**
     * Search the order in your database and return that order
     * to paidOrder, if status of your order is 'paid'
     *
     * @param Request $request
     * @param $order_id
     * @return bool|mixed
     */
    public function searchOrder(Request $request, $order_id)
    {
        $order = Order::where('id', $order_id)->first();

        if($order) {
            $order['_orderSum'] = $order->sum;

            // If your field can be `paid` you can set them like string
            $order['_orderStatus'] = $order['status'];

            // Else your field doesn` has value like 'paid', you can change this value
            $order['_orderStatus'] = ('1' == $order['status']) ? 'paid' : false;

            return $order;
        }

        return false;
    }

    /**
     * When paymnet is check, you can paid your order
     *
     * @param Request $request
     * @param $order
     * @return bool
     */
    public function paidOrder(Request $request, $order)
    {
        $order->status = 'paid';
        $order->save();

        //

        return true;
    }

    /**
     * Start handle process from route
     *
     * @param Request $request
     * @return mixed
     */
    public function handlePayment(Request $request)
    {
        return PayokIo::handle($request);
    }
}
```


## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please send me an email at ya@sanek.dev instead of using the issue tracker.

## Credits

- [Rodion15](https://github.com/Rodion15)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
