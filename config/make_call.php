<?php
require __DIR__ . '/../vendor/autoload.php';

use Twilio\Rest\Client;

// === Twilio Account Credentials ===
$sid    = "YOUR_TWILIO_ACCOUNT_SID";      // From Twilio Console
$token  = "YOUR_TWILIO_AUTH_TOKEN";       // From Twilio Console
$twilio = new Client($sid, $token);

// === Destination Number (Philippines) ===
$to = "+639458252517"; // Replace with the actual number you want to call
$from = "+1234567890"; // Your Twilio verified number (e.g., +15017122661)

try {
    // Initiate the call
    $call = $twilio->calls->create(
        $to,    // To
        $from,  // From (Twilio Number)
        [
            "url" => "https://yourdomain.com/twilio-response.xml"
            // Twilio will fetch this file to know what to say when the call connects
        ]
    );

    // Return JSON response
    echo json_encode([
        "status" => "success",
        "message" => "Call initiated successfully!",
        "sid" => $call->sid
    ]);

} catch (Exception $e) {
    // Error Handling
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}