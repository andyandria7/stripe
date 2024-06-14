<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Stripe\Charge;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    public function stripe()
    {
        $users = User::all();
        return view('payment.payment', [
            'users' => $users
        ]);
    }

    public function stripePost(Request $request)
    {
        try {
            Stripe::setApiKey(config('services.stripe.secret'));


            $amount = $request->input('amount');
            $amountInCents = $amount * 100;

            $recipientName = $request->input('recipientName');
            $recipient = User::where('name', $recipientName)->first();

            if (!$recipient) {
                Session::flash('error', 'Invalid recipient name');
                return back();
            }

            Charge::create([
                "amount" => $amountInCents,
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Test payment from Andy."
            ]);

            Session::flash('success', 'Payment successful!');
        } catch (\Exception $e) {
            Session::flash('error', $e->getMessage());
        }

        return back();
    }
}

