<?php
require_once 'includes/config.php';
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$message = trim($input['message'] ?? '');

if (!$message) { echo json_encode(['reply' => 'Please ask me something.']); exit; }

$systemContext = "You are a helpful AI assistant for " . SITE_NAME . ", a premium food ordering restaurant. You help customers with menu recommendations, order queries, and food questions. Keep responses concise and friendly. Our menu includes burgers, pizzas, fried chicken, wraps, fries, and drinks. Prices range from PKR 180 to PKR 1200. Delivery fee is PKR 99. We operate daily 10AM-11PM.";

// Prepare prompt for Groq API
$prompt = $systemContext . "\n\nUser: " . $message . "\nAssistant:";

$url = GROQ_API_URL;
$payload = json_encode([
    'input' => $prompt,
    'maxOutputTokens' => 200
]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Authorization: Bearer ' . GROQ_API_KEY
    ]
]);
$response = curl_exec($ch);
$err = curl_error($ch);
curl_close($ch);

if ($err) { echo json_encode(['reply' => 'Sorry, AI service is unavailable right now. Please contact us directly.']); exit; }

$data = json_decode($response, true);

// Try a few common response paths depending on provider response structure
$reply = null;
if (isset($data['outputs']) && is_array($data['outputs']) && isset($data['outputs'][0]['content'])) {
    // groq-like structure: outputs[0].content[0].text
    foreach ($data['outputs'][0]['content'] as $part) {
        if (isset($part['text'])) { $reply = ($reply ? $reply . "\n" : '') . $part['text']; }
    }
}
if (!$reply && isset($data['output'])) { $reply = is_string($data['output']) ? $data['output'] : json_encode($data['output']); }
if (!$reply && isset($data['choices'][0]['text'])) { $reply = $data['choices'][0]['text']; }
if (!$reply) { $reply = 'Sorry, I could not generate a response. Please try again.'; }

echo json_encode(['reply' => $reply]);
