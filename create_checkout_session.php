<?php
require 'vendor/autoload.php';
// Set API key 
$stripe = new \Stripe\StripeClient('sk_test_51OtGrLJl3yXQY8VOOYHWftIBtp0ZFGGMFHxs8QSeZ0WvPNeu2WpRDMGHrpOumkGnbZYr9BjD73roKEAyEpM3n53e00T8JuvcqY'); 
 
$response = array( 
    'status' => 0, 
    'error' => array( 
        'message' => 'Invalid Request!'    
    ) 
); 
 
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
    $input = file_get_contents('php://input'); 
    $request = json_decode($input);     
} 
 
if (json_last_error() !== JSON_ERROR_NONE) { 
    http_response_code(400); 
    echo json_encode($response); 
    exit; 
} 
 
if(!empty($request->createCheckoutSession)){ 
    // Convert product price to cent 
    $stripeAmount = round(5*100, 2); 
 
    // Create new Checkout Session for the order 
    try { 
        $checkout_session = $stripe->checkout->sessions->create([ 
            'line_items' => [
                [ 
                    'price_data' => [ 
                        'product_data' => [ 
                            'name' => 'Product Name', 
                            'metadata' => [ 
                                'pro_id' => 10 
                            ] 
                        ], 
                        'unit_amount' => $stripeAmount, 
                        'currency' => 'usd', 
                    ], 
                    'quantity' => 1 
                ],
                [ 
                    'price_data' => [ 
                        'product_data' => [ 
                            'name' => 'Product Name2', 
                            'metadata' => [ 
                                'pro_id' => 11
                            ] 
                        ], 
                        'unit_amount' => $stripeAmount, 
                        'currency' => 'usd', 
                    ], 
                    'quantity' => 2
                ]
            ], 
            'mode' => 'payment', 
            'success_url' => 'https://example.com/success.html',
            'cancel_url' => 'https://example.com/cancel.html',
        ]); 
    } catch(Exception $e) {  
        $api_error = $e->getMessage();  
    } 
     
    if(empty($api_error) && $checkout_session){ 
        $response = array( 
            'status' => 1, 
            'message' => 'Checkout Session created successfully!', 
            'sessionId' => $checkout_session->id 
        ); 
    }else{ 
        $response = array( 
            'status' => 0, 
            'error' => array( 
                'message' => 'Checkout Session creation failed! '.$api_error    
            ) 
        ); 
    } 
} 
 
// Return response 
echo json_encode($response); 



/* \Stripe\Stripe::setApiKey('sk_test_51OtGrLJl3yXQY8VOOYHWftIBtp0ZFGGMFHxs8QSeZ0WvPNeu2WpRDMGHrpOumkGnbZYr9BjD73roKEAyEpM3n53e00T8JuvcqY');
try {
    $session = \Stripe\Checkout\Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => 'Your Product Name',
                ],
                'unit_amount' => 1000, // Amount in cents
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'https://example.com/success.html',
        'cancel_url' => 'https://example.com/cancel.html',
    ]);

    header("Location: " . $session->url);
    exit;
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
} */
?>