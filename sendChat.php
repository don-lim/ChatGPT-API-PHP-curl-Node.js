<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $ch = curl_init();
  $url = 'https://api.openai.com/v1/chat/completions';
  $api_key = 'sk-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'; // replace with your key
  
  $temperature = (float)$_POST['tempRange'];
  $n = (int)$_POST['numResponses'];
  $prompt = $_POST['prompt'];

  $messages = [];

  if (!empty($_POST['systemRole'])) {
    $messages[] = [
      "role" => "system",
      "content" => $_POST['systemRole']
    ];
  }

  if (!empty($_POST['assistantRole'])) {
    $messages[] = [
      "role" => "assistant",
      "content" => $_POST['assistantRole']
      ];
  }

  if (!empty($_POST['prompt'])) {
    $messages[] = [
      "role" => "user",
      "content" => $_POST['prompt']
    ];
  } else {
    header("Content-Type: application/json");
    echo '{"response": "Error: Prompt cannot be empty."}';
    exit;
  }

  foreach ($messages as $message) {
    $json_messages[] = [
      "role" => $message["role"],
      "content" => $message["content"]
    ];
  }

  $post_fields = array(
    "model" => "gpt-3.5-turbo",
    "messages" => $messages,
    "max_tokens" => 200,
    "temperature" => $tempRange,
    "n" => $n
  );

  $header  = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key
  ];

  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_fields));
  curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

  $result = curl_exec($ch);
  if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
  }
  curl_close($ch);

  $response = json_decode($result);
  header("Content-Type: application/json");
  echo json_encode(['response' => $response]);
}
?>
