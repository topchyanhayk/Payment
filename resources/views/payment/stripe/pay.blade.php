<!DOCTYPE html>
<html lang="en">
    <body>
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            const stripe = Stripe('{{ config('payment.stripe.publicKey') }}');
            stripe.redirectToCheckout({
                sessionId: '{{ $sessionId }}'
            });
        </script>
    </body>
</html>
