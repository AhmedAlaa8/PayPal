<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class PaypalController extends Controller
{
    public function pay(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $order = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success'),
                "cancel_url" => route('cancel')
            ],



            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => "2.00"
                    ]
                ]
            ]
        ]);

        // dd($order);

        if (isset($order['id']) && isset($order['id']) != null) {
            foreach ($order['links'] as $link) {
                if ($link['rel'] == 'approve') {
                    session()->put('product_name', $request->name);
                    session()->put('quan', $request->quan);
                    return redirect()->away($link['href']);
                }
            }
        } else {
            return redirect()->route('cancel');
        }
    }
    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();
        $order = $provider->capturePaymentOrder($request->token);
        // dd($order);
        if (isset($order['status']) && $order['status'] == 'COMPLETED') {
            //insert data
            $payment = new Payment;
            $payment->pay_id = $order['id'];
            $payment->name = session()->get('product_name');
            $payment->quantity = session()->get('quan');
            $payment->amount = $order['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $payment->currency = $order['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
            $payment->payer_name = $order['payer']['name']['given_name'];
            $payment->payer_email = $order['payer']['email_address'];
            $payment->payment_status = $order['status'];
            $payment->payment_method = 'PayPal';
            $payment->save();

            return 'payment is successful';

            unset($_SESSION['product_name']);
            unset($_SESSION['quan']);
        } else {
            return redirect()->route('cancel');
        }
    }
    public function cancel()
    {

        dd('cancel');
    }
}
