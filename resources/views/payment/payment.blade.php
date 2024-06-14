<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body class="bg-white">
    <div class="container mx-auto py-10">
        <h1 class="text-3xl text-black font-bold mb-6">Stripe Payment</h1>
        <div class="flex justify-center">
            <div class="w-full md:w-1/2 ">
                <div class="bg-slate-700 shadow-lg rounded-lg p-6">
                    <h3 class="text-xl font-bold text-white mb-4">Payment Details</h3>
                    <div class="mb-4">
                        <div class="error">
                            <div class="alert-danger alert bg-red-500 text-white rounded-md hide"></div>
                        </div>
                    </div>
                    @if (Session::has('success'))
                    <div class="alert alert-success bg-green-600 text-white rounded-md p-3 mb-4">
                        <a href="{{ route('stripe') }}" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <p>{{ Session::get('success') }}</p>
                    </div>
                    @endif
                    @if (Session::has('error'))
                    <div class="alert alert-success bg-red-500 text-white rounded-md p-3 mb-4">
                        <a href="{{ route('stripe') }}" class="close" data-dismiss="alert" aria-label="close">×</a>
                        <p>{{ Session::get('error') }}</p>
                    </div>
                    @endif

                    <form role="form" action="{{ route('stripe.post') }}" method="post" class="require-validation"
                        data-cc-on-file="false" data-stripe-publishable-key="{{ config('services.stripe.key') }}"
                        id="payment-form">
                        @csrf

                        <div class="mb-4">
                            <label class="text-white">Recipient Name</label>
                            <select class="form-control name" name="recipientName" required>
                                <option value="">Select recipient</option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->name }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="text-white">Card Number</label>
                            <input autocomplete="off"
                                class="form-control card-number"
                                size="20" type="text" name="cardNumber" placeholder="Ex: 4242 4242 4242 4242">
                        </div>

                        <div class="mb-4">
                            <label class="text-white">Amount</label>
                            <input class="form-control" type="number"
                                min="1" step="0.01" name="amount" required>
                        </div>

                        <div class="flex mb-4">
                            <div class="mr-2 w-1/3">
                                <label class="text-white">CVC</label>
                                <input autocomplete="off"
                                    class="form-control card-cvc"
                                    placeholder="ex. 311" size="4" type="text" name="cardCvc">
                            </div>
                            <div class="mr-2 w-1/3">
                                <label class="text-white">Expiration Month</label>
                                <input
                                    class="form-control card-expiry-month"
                                    placeholder="MM" size="2" type="text" name="cardExpiryMonth">
                            </div>
                            <div class="w-1/3">
                                <label class="text-white">Expiration Year</label>
                                <input
                                    class="form-control bbg-white text-slate-700 rounded-md px-4 py-2 w-full card-expiry-year"
                                    placeholder="YYYY" size="4" type="text" name="cardExpiryYear">
                            </div>
                        </div>

                        <div class="flex justify-center">
                            <button
                                class="btn btn-primary btn-lg w-full md:w-auto px-4 py-2 rounded-md shadow-lg text-white hover:bg-white hover:text-slate-700"
                                type="submit">Pay Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclure jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Inclure Stripe.js -->
    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    <script type="text/javascript">
        $(function() {
            /*------------------------------------------
            --------------------------------------------
            Stripe Payment Code
            --------------------------------------------
            --------------------------------------------*/
            var $form = $(".require-validation");
            $('form.require-validation').bind('submit', function(e) {
                var $form = $(".require-validation"),
                    inputSelector = ['input[type=email]', 'input[type=password]', 'input[type=text]',
                        'input[type=file]', 'textarea'
                    ].join(', '),
                    $inputs = $form.find('.required').find(inputSelector),
                    $errorMessage = $form.find('div.error'),
                    valid = true;
                $errorMessage.addClass('hide');
                $('.has-error').removeClass('has-error');
                $inputs.each(function(i, el) {
                    var $input = $(el);
                    if ($input.val() === '') {
                        $input.parent().addClass('has-error');
                        $errorMessage.removeClass('hide');
                        e.preventDefault();
                    }
                });
                if (!$form.data('cc-on-file')) {
                    e.preventDefault();
                    Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        name: $('.name').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                }
            });

            /*------------------------------------------
            --------------------------------------------
            Stripe Response Handler
            --------------------------------------------
            --------------------------------------------*/
            function stripeResponseHandler(status, response) {
                if (response.error) {
                    $('.error').removeClass('hide').find('.alert').text(response.error.message);
                } else {
                    /* token contient id, last4, et type de carte */
                    var token = response['id'];
                    /* insérer le token dans le formulaire afin qu'il soit soumis au serveur */
                    $form.find('input[type=text]').empty();
                    $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                    $form.get(0).submit();
                }
            }
        });
    </script>
</body>
</html>
