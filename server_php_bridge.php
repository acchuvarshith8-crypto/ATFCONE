<?php
session_start();
header('Content-Type: application/json');

// ❗ UPDATE THIS — insert your NEW OpenAI key
$OPENAI_API_KEY = "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";

$payload = json_decode(file_get_contents("php://input"), true);
$prompt = $payload["prompt"] ?? "";

if (!$prompt) {
    echo json_encode(["reply" => "No prompt given"]);
    exit;
}

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $OPENAI_API_KEY"
]);

curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "model" => "gpt-4o-mini",
    "messages" => [
        ["role" => "system", "content" => "You are ATFC Assistant."],
        ["role" => "user", "content" => $prompt]
    ]
]));

$response = curl_exec($ch);
$error = curl_error($ch);
$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($error) {
    echo json_encode(["reply" => "Backend error: $error"]);
    exit;
}

$decoded = json_decode($response, true);

// If API failed (wrong key, quota, etc.)
if (!isset($decoded["choices"][0]["message"]["content"])) {
    echo json_encode([
        "reply" => "Error: " . ($decoded["error"]["message"] ?? "Unknown API error")
    ]);
    exit;
}

$reply = $decoded["choices"][0]["message"]["content"];

// Final clean output to frontend
echo json_encode(["reply" => $reply]);
?>
