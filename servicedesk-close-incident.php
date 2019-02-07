<?php

// Задаем URL, по которому будем обращаться к REST API:
$url='http://192.168.43.250/sdpapi/request';

// Принимаем статус события от icinga в качестве первого параметра скрипта:
$state=$argv[1];
// Принимаем имя хоста от icinga в качестве второго параметра скрипта:
$host=$argv[2];
// Принимаем имя сервиса от icinga в качестве третьего параметра скрипта:
$service=$argv[3];

// Задаем API Key специалиста:
$TK='50B971C0-4C98-4011-995E-30B17B957CCD';

// Формируем документ для получения списка инцидентов:
$get_requests='
{
    "operation": {
        "details": {
            "from": "0",
            "limit": "50",
            "filterby": "1_MyView"
        }
    }
}
';

// Формируем документ для закрытия инцидента:
$close_request='
{
    "operation": {
        "details": {
            "closeAccepted": "Accepted",
            "closeComment": "Инцидент закрыт системой мониторинга со статусом "' . $state . '"
        }
    }
}
';

// Инициализируем curl-сессию:
$ch = curl_init();

// Задаем опции для curl-сессии:
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_POST, true);

/* Передаем строку с данными для передачи HTTP POST.
Для REST API параметром OPERATION_NAME=GET_REQUESTS даем команду на получение списка инцидентов, 
параметром INPUT_DATA передаем XML-документ, а в конце строки указываем
API Key в параметре TECHNICIAN_KEY */

curl_setopt($ch, CURLOPT_POSTFIELDS, "OPERATION_NAME=GET_REQUESTS&INPUT_DATA=$get_requests&TECHNICIAN_KEY=$TK&format=json");

// Открываем curl-сессию:
$requests = curl_exec($ch);

//$jsonArray = json_decode($requests,true);
//$key = "WORKORDERID";
//$WORKORDERID = $jsonArray[$key];

$arr = json_decode($requests, true);
foreach($arr as $key)
{
    //echo '<div class="row"><div class="span6">'.$v->productName.'</div><div class="span3">'.$v->productPrice.'</div></div>';
    $WORKORDERID = $v->WORKORDERID;
    echo $WORKORDERID;
}




// Закрываем curl-сессию:
curl_close($ch);

// DEBUG
//$server_output = curl_exec ($ch);
//$error_message = curl_errno($ch);
//curl_close ($ch);
//print_r($server_output);

?>