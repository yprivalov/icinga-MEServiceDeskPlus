<?php

// Задаем URL, по которому будем обращаться к REST API:
$url='http://192.168.43.250/sdpapi/request';

// Принимаем статус события от icinga в качестве первого параметра скрипта:
$state=$argv[1];

// Принимаем имя хоста от icinga в качестве второго параметра скрипта:
$subcategory=$argv[2];

// Принимаем имя сервиса от icinga в качестве третьего параметра скрипта:
$item=$argv[3];

// Формируем тему инцидента:
$subject=$state." state on ".$subcategory." with service ".$item;

// Задаем API Key специалиста:
$TK='50B971C0-4C98-4011-995E-30B17B957CCD';

// Формируем инцидент:
$request_string=<<<JSON
{
    "operation": {
        "details": {
            "requester": "Monitoring",
            "requestType": "Incident",
            "subject": "$subject",
            "category": "Monitoring",
            "subcategory": "$subcategory",
            "description": "Specify Description",
            "priority": "High",
            "site": "New York",
            "group": "Инфраструктура",
            "technician": "Привалов Юрий",
            "service": "Email"
        }
    }
}
JSON;

// Инициализируем curl-сессию:
$ch = curl_init();

// Задаем опции для curl-сессии:
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_POST, true);

/* Передаем строку с данными для передачи HTTP POST.
Для REST API параметром OPERATION_NAME=ADD_REQUEST даем команду на добавление инцидента, параметром INPUT_DATA
передаем XML-документ, а в конце строки указываем API Key в параметре TECHNICIAN_KEY */
curl_setopt($ch, CURLOPT_POSTFIELDS, "OPERATION_NAME=ADD_REQUEST&INPUT_DATA=$request_xmlstring&TECHNICIAN_KEY=$TK");

// Открываем curl-сессию:
curl_exec($ch);

// Закрываем curl-сессию:
curl_close($ch);

?>
