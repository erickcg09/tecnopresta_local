<?php 
// https://api.telegram.org:443/bot1600580132:AAED42pXqSzE-JGAYHEy_Kt9YZ9JGfU_D5k/setwebhook?url=https://tecnopresta.mep.go.cr/enviar_telegram.php

// https://api.telegram.org/bot1600580132:AAED42pXqSzE-JGAYHEy_Kt9YZ9JGfU_D5k/setwebhook?url=https://tecnopresta.mep.go.cr/enviar_telegram.php

// https://api.telegram.org/bot1600580132:AAED42pXqSzE-JGAYHEy_Kt9YZ9JGfU_D5k/setwebhook?url=https://tecnopresta.mep.go.cr/enviar_telegram.php
$token = '1600580132:AAED42pXqSzE-JGAYHEy_Kt9YZ9JGfU_D5k';
$website = 'https://api.telegram.org/bot'.$token;

$input = file_get_contents('php://input');
$update = json_decode($input, TRUE);

$chatId = $update['message']['chat']['id'];
$message = $update['message']['text'];

switch($message) {
    case '/start':
        $response = 'Me has iniciado';
        sendMessage($chatId, $response);
        break;
    case '/ayuda':
        $response = 'Hola! Soy @tecnopresta_bot';
        sendMessage($chatId, $response);
        break;
    default:
        $response = 'No te he entendido';
        sendMessage($chatId, $response);
        break;
}

function sendMessage($chatId, $response) {
    $url = $GLOBALS['website'].'/sendMessage?chat_id='.$chatId.'&parse_mode=HTML&text='.urlencode($response);
    file_get_contents($url);
}
?>