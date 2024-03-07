<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;


use Illuminate\Support\Facades\Log;
use JustGeeky\LaravelCybersource\Exceptions\CybersourceException;
use JustGeeky\LaravelCybersource\Facades\Cybersource;

class HomeController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function test(Request $request)
    {
        try {
            // $cybersourceApiUrl = 'https://api.cybersource.com/';
            $cybersourceApiUrl = 'https://apitest.cybersource.com/';

            // Cybersource API credentials
            $apiKey = "0e7880b1-1662-4a7c-8c29-07b63414d494";
            $apiSecret = "Ou3zTfXOEWfjnRWouKTq5HLA+unS5PRpZm5WTBeLroc=";
            $merchantId = "gibson_1709801997";

            $amount = "10.00";
            $currency = "USD";
            $capture = $request->input('capture');
            $billingDetails = [
                'firstName' => $request->input('billing_name'),
                'lastName' => $request->input('billing_last_name', ''), // Optional last name
                'address1' => $request->input('billing_address'),
                'locality' => $request->input('billing_city'),
                'administrativeArea' => $request->input('billing_state'),
                'postalCode' => $request->input('billing_postal_code'),
                'country' => $request->input('billing_country'),
                'email' => $request->input('billing_email'),
            ];
            $cardDetails = [
                'number' => $request->input('card_number'),
                'expirationMonth' => $request->input('card_expiration_month'),
                'expirationYear' => $request->input('card_expiration_year'),
            ];

            try {
                $paymentRequest = [
                    'amount' => $amount,
                    'currency' => $currency,
                    'capture' => $capture,
                    'billTo' => $billingDetails,
                    'payment' => [
                        'paymentMethod' => 'credit_card',
                        'card' => $cardDetails,
                    ],
                ];

                $response = Cybersource::payments()->create($paymentRequest);

                // Handle response based on $response data
                // ... (check decision, handle success or error)

            } catch (CybersourceException $e) {
                Log::error('CyberSource Payment Error:', $e->getMessage());
                return back()->withErrors(['message' => 'Payment processing error']);
            }
            // Prepare the request payload
            // Prepare the request payload
            // $payload = [
            //     'amount' => "10.00",
            //     'currency' => "USD",
            //     // Add other necessary fields for your payment request
            // ];

            // $authToken = base64_encode("{$apiKey}:{$apiSecret}");
            // dd(base64_encode("$apiKey:$apiSecret"));
            // // Make the API request to Cybersource
            // $response = Http::withHeaders([
            //     'Content-Type' => 'application/json',
            //     'Authorization' => 'Bearer ' . $authToken,
            // ])->post($cybersourceApiUrl . 'pts/v2/payments', $payload);

            //     dd($response);
            // // Check the API response
            // if ($response->successful()) {
            //     // Payment successful
            //     $responseData = $response->json();

            //     // Validate the Cybersource response
            //     if ($this->validateCybersourceResponse($responseData)) {
            //         // Save transaction details in the database
            //         $transaction = Transaction::create([
            //             'transaction_id' => $responseData['transaction_id'],
            //             'amount' => $request->input('amount'),
            //             'currency' => $request->input('currency'),
            //             // Add other fields as needed
            //         ]);

            //         // Additional logic...

            //         return view('cybersource.payment-success', ['transaction' => $transaction]);
            //     } else {
            //         // Handle invalid Cybersource response
            //         return view('cybersource.payment-failure', ['error' => 'Invalid Cybersource response']);
            //     }
            // } else {
            //     // Handle API error response
            //     $errorData = $response->json();
            //     throw new \Exception('Cybersource API Error: ' . json_encode($errorData));
            // }
        } catch (\Exception $e) {
            // Handle general exceptions
            dd($e->getMessage());
            return view('cybersource.payment-error', ['errorMessage' => $e->getMessage()]);
        }
    }

    private function validateCybersourceResponse($responseData)
    {
        // Implement your validation logic here
        // Check if the required fields are present and have expected values
        // Return true if the response is valid, false otherwise
        return isset($responseData['transaction_id']) && $responseData['status'] === 'success';
    }
}
