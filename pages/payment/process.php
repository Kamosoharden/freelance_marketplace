<?php 
include 'configs.php';
session_start();

// Define base URL dynamically
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$baseUrl = "{$protocol}://{$host}";

if(isset($_GET['status']))
{
    $redirect_url = $baseUrl . '/employerdashboard.php';

    if($_GET['status'] == 'cancelled')
    {
        header("Location: $redirect_url?payment=cancelled");
        exit();
    }
    elseif($_GET['status'] == 'successful')
    {
        $txid = $_GET['transaction_id'];

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$txid}/verify",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              "Content-Type: application/json",
              "Authorization: Bearer $SecretKey"
            ),
          ));
          
          $response = curl_exec($curl);
          
          curl_close($curl);
          
          $res = json_decode($response);
          if($res->status)
          {
            $amountPaid = $res->data->charged_amount;
            $amountToPay = $res->data->meta->price;
            $hire_id = $res->data->meta->hire_id;

            if($amountPaid >= $amountToPay)
            {
                // Update the hire status in the database
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "freelance";

                $conn = new mysqli($servername, $username, $password, $dbname);

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "UPDATE hires SET status = 'paid' WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $hire_id);
                $stmt->execute();

                $stmt->close();
                $conn->close();

                header("Location: $redirect_url?payment=success");
                exit();
            }
            else
            {
                header("Location: $redirect_url?payment=fraud");
                exit();
            }
          }
          else
          {
              header("Location: $redirect_url?payment=failed");
              exit();
          }
    }
}
else
{
    // If no status is set, redirect to dashboard with an error message
    header("Location: " . $baseUrl . "/employerdashboard.php?payment=error");
    exit();
}
?>