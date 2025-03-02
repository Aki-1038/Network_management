<?php
header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);
$message = $input['message'];

// 使用 OpenAI API - php
$apiKey = ' OpenAI API 金鑰'; // 請替換為你的 OpenAI API 金鑰
$url = 'https://api.openai.com/v1/chat/completions';
/*
$data = [
    'model' => 'gpt-4o-mini',
    'messages' => [
        ['role' => 'system', 'content' => '**Context（上下文）**  你正在尋找一位英文老師來幫助你提高英語能力。ChatGPT將作為你的英文老師，提供各種學習資源和練習機會。**Objective（目標）**  利用ChatGPT的多種功能來提升你的英語寫作、口語、單字和文法能力，並制定有效的學習計劃。**Style（風格）**  - **友好且鼓勵**：以支持和鼓勵的語氣進行教學，讓學習者感到放鬆。- **清晰易懂**：使用簡單明瞭的語言解釋概念和技巧。**Tone（語氣）**  - **專業但輕鬆**：保持專業的態度，同時讓學習過程變得輕鬆愉快。**Audience（受眾）**  針對想要提高英語能力的學生，無論是初學者還是進階學習者。**Response（回應格式）**  以對話形式呈現，包括具體的指導、範例和練習建議。'],
        ['role' => 'user', 'content' => $message],
    ],
];
*/


// 這是你存儲上下文的地方
session_start();
if (!isset($_SESSION['context'])) {
    $_SESSION['context'] = [];
}

// 將當前用戶消息添加到上下文中
array_push($_SESSION['context'], ['role' => 'user', 'content' => $message]);
// 合併上下文與當前消息



$data = [
    'model' => 'gpt-4o-mini',

    'messages' => [//array_merge($context,
    //'messages' => array_merge($_SESSION['context'], [['role' => 'user', 'content' => $message]]);

    //我希望 ChatGPT 在回應時使用臺灣特有的慣用語和口語表達，讓交流更自然。 
        ['role' => 'system', 'content' => '作為一名網路規劃與管理新手，
        我希望了解cisco路由器和交換機在packet tracer 的CLI介面使用指令操作，
        我希望 # ChatGPT # 在回應時使用臺灣特有的慣用語和口語表達，讓交流更自然。 

        # CONTEXT # 網路規劃與管理實務 
        # OBJECTIVE # 獲取網路設計和管理的實務建議，以cisco路由器和交換機在packet tracer 的CLI操作介面使用為例。 
        # STYLE # 專業且系統化 
        # TONE # 友好且具教育意義 
        # AUDIENCE # 網路管理初學者 
        # RESPONSE # 提供具體的操作步驟和相關範例
        # zh-TW # 使用臺灣特有的慣用語和口語表達，讓交流更自然。'],
        ['role' => 'user', 'content' => $message],
],
];

$options = [
    'http' => [
        'header'  => "Content-type: application/json\r\n" .
                     "Authorization: Bearer $apiKey\r\n",
        'method'  => 'POST',
        'content' => json_encode($data),
    ],
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
$response = json_decode($result, true);

$reply = $response['choices'][0]['message']['content'] ?? '抱歉，我無法回答。';

error_log(print_r($data, true)); // 輸出請求資料
error_log(print_r($response, true)); // 輸出回應資料

echo json_encode(['reply' => $reply]);
?>
