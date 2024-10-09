<?php 
include 'configs.php';
session_start();

// Define base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$baseUrl = "{$protocol}://{$host}";

if(isset($_POST['pay']))
{
    $email = $_POST['email'];
    $amount = $_POST['amount'];
    $name = $_POST['name'];
    $hire_id = $_POST['hire_id'];

    $_SESSION['current_hire_id'] = $hire_id;

    $request = [
        'tx_ref' => time(),
        'amount' => $amount,
        'currency' => 'RWF',
        'payment_options' => 'card',
        'redirect_url' => $baseUrl . '/pages/payment/process.php',
        'customer' => [
            'email' => $email,
            'name' => $name
        ],
        'meta' => [
            'price' => $amount,
            'hire_id' => $hire_id
        ],
        'customizations' => [
            'title' => 'Freelancer Payment',
            'description' => 'Depositing funds into escrow account'
        ]
    ];

    $curl = curl_init();

    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => json_encode($request),
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '. $SecretKey,
        'Content-Type: application/json'
    ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    
    $res = json_decode($response);
    if($res->status == 'success')
    {
        $link = $res->data->link;
        header('Location: '.$link);
    }
    else
    {
        echo 'We can not process your payment';
    }
}
?>