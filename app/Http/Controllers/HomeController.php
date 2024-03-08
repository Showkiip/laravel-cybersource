<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use JustGeeky\LaravelCybersource\Exceptions\CybersourceException;
use JustGeeky\LaravelCybersource\Facades\Cybersource;
use CyberSource\Authentication\Core\MerchantConfiguration;

class HomeController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function test(Request $request)
    {
        try {
            // $cybersourceApiUrl = 'https://api.cybersource.com/'; // Change to production URL when ready

            // Cybersource API credentials
            $apiKey = "0e7880b1-1662-4a7c-8c29-07b63414d494";
            $apiSecret = "Ou3zTfXOEWfjnRWouKTq5HLA+unS5PRpZm5WTBeLroc=";
            $merchantId = "gibson_1709801997";
            $useMetaKey = false;



            $apiEndpoint = 'https://apitest.cybersource.com/pts/v2/payments';

            $requestData = [
                "clientReferenceInformation" => [
                    "code" => "TC50171_3",
                ],
                "paymentInformation" => [
                    "card" => [
                        "number" => "4111111111111111",
                        "expirationMonth" => "12",
                        "expirationYear" => "2031",
                    ],
                ],
                "orderInformation" => [
                    "amountDetails" => [
                        "totalAmount" => "102.21",
                        "currency" => "USD",
                    ],
                    "billTo" => [
                        "firstName" => "John",
                        "lastName" => "Doe",
                        "address1" => "1 Market St",
                        "locality" => "san francisco",
                        "administrativeArea" => "CA",
                        "postalCode" => "94105",
                        "country" => "US",
                        "email" => "test@cybs.com",
                        "phoneNumber" => "4158880000",
                    ],
                ],
            ];

            // Headers
            // $headers = [
            //     'host' => 'apitest.cybersource.com',
            //     'Content-Type' => 'application/json',
            //     'v-c-merchant-id' => 'testrest',
            //     'v-c-merchantId' =>	'gibson_1709801997',
            //     'v-c-correlation-id' =>	'ced019f1-a0e3-4375-ad5d-5adb1b2e5bf9',
            //     'v-c-date' => 'Send Request to Generate Date Value', // Replace with actual date value
            //     'digest' => 'Send Request to Generate Digest', // Replace with actual digest value
            //     'signature' => 'Send Request to Generate Signature', // Replace with actual signature value
            // ];

            $vCDate = gmdate('D, d M Y H:i:s T');
            $digest = 'SHA-256=' . base64_encode(hash('sha256', json_encode($requestData), true));
            $signatureString = '(request-target): post /pts/v2/payments' . "\n" .
                'host: apitest.cybersource.com' . "\n" .
                'digest: ' . $digest . "\n" .
                'v-c-merchant-id: ' . $merchantId;
            $signature = base64_encode(hash_hmac('sha256', $signatureString, base64_decode($apiSecret), true));

            // Headers
            $headers = [
                'host' => 'apitest.cybersource.com',
                'signature' => 'keyid="' . $apiKey . '", algorithm="HmacSHA256", headers="(request-target) host digest v-c-merchant-id", signature="' . $signature . '"',
                'digest' => $digest,
                'v-c-merchant-id' => $merchantId,
                'v-c-date' => $vCDate,
                'Content-Type' => 'application/json',
            ];

            // Make the API request
            $response = Http::withHeaders($headers)->post($apiEndpoint, $requestData);

            // Print the response
            dd($response->json());
            return view('home')->with(['response' => $response]);

            // $merchantConfig = new MerchantConfiguration($merchantId, $apiKey, $apiSecret, $useMetaKey);

            // // Construct Authorization header
            // $authorizationHeader = base64_encode("{$apiKey}:{$apiSecret}");

            // // Make API request
            // $client = new Client();
            // $response = $client->request('POST', $apiEndpoint, [
            //     'headers' => [
            //         'Content-Type' => 'application/json',
            //         'Authorization' => 'Basic ' . $authorizationHeader,
            //     ],
            //     'json' => $requestData,
            // ]);

            // Get the response body
            $responseBody = $response->getBody()->getContents();

            // Parse the JSON response
            $parsedResponse = json_decode($responseBody, true);

            dd($parsedResponse);

            // Process the response as needed
            // $responseData = json_decode($response->body(), true);

            // // Handle success or failure
            // if ($response->successful()) {
            //     // Payment successful logic
            //     return response()->json(['message' => 'Payment successful', 'token' => $responseData['token']]);
            // } else {
            //     // Payment failed logic
            //     $errorDetails = json_decode($response->body(), true);
            //     dd($errorDetails);

            //     return response()->json(['message' => 'Payment failed', 'error' => $responseData['error']]);
            // }


            //     $payload = [
            //         'amount' => "10.00",
            //         'currency' => "USD",
            //         // Add other necessary fields for your payment request
            //     ];

            //     $authToken = base64_encode("{$apiKey}:{$apiSecret}");
            //     dd(base64_encode("$apiKey:$apiSecret"));
            //     // Make the API request to Cybersource
            //     $response = Http::withHeaders([
            //         'Content-Type' => 'application/json',
            //         'Authorization' => 'Bearer ' . $authToken,
            //     ])->post($cybersourceApiUrl . 'pts/v2/payments', $payload);

            //       dd($response);
            //     // Check the API response
            //     if ($response->successful()) {
            //         // Payment successful
            //         $responseData = $response->json();

            //         // Validate the Cybersource response
            //         if ($this->validateCybersourceResponse($responseData)) {
            //             // Save transaction details in the database
            //             $transaction = Transaction::create([
            //                 'transaction_id' => $responseData['transaction_id'],
            //                 'amount' => $request->input('amount'),
            //                 'currency' => $request->input('currency'),
            //                 // Add other fields as needed
            //             ]);

            //             // Additional logic...

            //             return view('cybersource.payment-success', ['transaction' => $transaction]);
            //         } else {
            //             // Handle invalid Cybersource response
            //             return view('cybersource.payment-failure', ['error' => 'Invalid Cybersource response']);
            //         }
            //     } else {
            //         // Handle API error response
            //         $errorData = $response->json();
            //         throw new \Exception('Cybersource API Error: ' . json_encode($errorData));
            //     }
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
