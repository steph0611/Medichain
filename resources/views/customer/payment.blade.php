<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Pay Prescription | MediChain</title>
<script src="https://js.stripe.com/v3/"></script>
<style>
    body { font-family: Arial, sans-serif; padding: 20px; background-color: #f5f5f5; }
    .container { max-width: 500px; margin: auto; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    #card-element { padding: 10px; border: 1px solid #ccc; border-radius: 4px; margin-bottom: 15px; }
    button { background-color: #6772e5; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
    button:disabled { background-color: #bbb; cursor: not-allowed; }
    .message { margin: 15px 0; }
</style>
</head>
<body>
<div class="container">
    <h2>Pay Prescription</h2>
    <p>Amount: ${{ $amount }}</p>

    <div class="message" id="message"></div>

    <form id="payment-form">
        @csrf
        <input type="hidden" id="prescription_id" value="{{ $prescriptionId }}">
        <input type="hidden" id="amount" value="{{ $amount }}">
        <div id="card-element"></div>
        <button id="submit">Pay Now</button>
    </form>
</div>

<script>
const stripe = Stripe("{{ env('STRIPE_KEY') }}");
const elements = stripe.elements();
const cardElement = elements.create('card');
cardElement.mount('#card-element');

const form = document.getElementById('payment-form');

form.addEventListener('submit', async (e) => {
    e.preventDefault();

    // Disable the submit button to prevent multiple clicks
    document.getElementById('submit').disabled = true;

    const { paymentMethod, error } = await stripe.createPaymentMethod('card', cardElement);

    if (error) {
        alert(error.message);
        document.getElementById('submit').disabled = false;
        return;
    }

    const response = await fetch("{{ route('payment.process') }}", {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json', 
            'X-CSRF-TOKEN': '{{ csrf_token() }}' 
        },
        body: JSON.stringify({
            prescription_id: document.getElementById('prescription_id').value,
            amount: document.getElementById('amount').value,
            payment_method_id: paymentMethod.id
        })
    });

    const result = await response.json();

    if (result.error) {
        alert(result.error);
        document.getElementById('submit').disabled = false;
        return;
    }

    if (result.requires_action) {
        // Handle 3D Secure
        const { error: confirmError } = await stripe.confirmCardPayment(
            result.payment_intent_client_secret
        );

        if (confirmError) {
            alert(confirmError.message);
            document.getElementById('submit').disabled = false;
        } else {
            // Redirect to success page after 3D Secure
            window.location.href = "{{ url('/payment-success') }}?prescription_id=" + document.getElementById('prescription_id').value;
        }
    } else {
        // Payment succeeded instantly
        window.location.href = "{{ url('/payment-success') }}?prescription_id=" + document.getElementById('prescription_id').value;
    }
});
</script>

</body>
</html>
