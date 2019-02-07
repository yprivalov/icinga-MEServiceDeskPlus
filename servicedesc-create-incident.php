<?php

// Задаем URL, по которому будем обращаться к REST API:
$url='http://192.168.43.250/sdpapi/request';

// Принимаем статус события от icinga в качестве первого параметра скрипта:
$state=$argv[1];

// Принимаем имя хоста от icinga в качестве второго параметра скрипта:
$host=$argv[2];

// Принимаем имя сервиса от icinga в качестве третьего параметра скрипта:
$service=$argv[3];

// Формируем тему инцидента:
//$subject=$state." state on ".$host." with service ".$service;
$subject=$state." service ".$service." on ".$host;

// Формируем описание инцидента
$desc='Host: '.$host.' <br/> Service: '.$service.' <br/> Status: '.$state.' <br/><br/> http://192.168.42.58/icingaweb2/monitoring/service/show?host='.$host.'%26service='.$service.'';

// Задаем API Key специалиста:
$TK='50B971C0-4C98-4011-995E-30B17B957CCD';

// Формируем инцидент:
$request_string='
{
    "operation": {
        "details": {
            "requester": "Monitoring",
            "requestType": "Incident",
            "subject": "' . $subject . '",
            "category": "Monitoring",
            "Хост": "' . $host . '",
            "Служба": "' . $service . '",
            "Статус": "' . $state . '",
            "requesttemplate": "Monitoring",
            "description": "' . $desc . '",
            "priority": "High",
            "site": "New York",
            "group": "Инфраструктура",
            "technician": "Привалов Юрий",
            "service": "Email"
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
Для REST API параметром OPERATION_NAME=ADD_REQUEST даем команду на добавление инцидента, параметром INPUT_DATA
передаем XML-документ, а в конце строки указываем API Key в параметре TECHNICIAN_KEY */
curl_setopt($ch, CURLOPT_POSTFIELDS, "OPERATION_NAME=ADD_REQUEST&INPUT_DATA=$request_string&TECHNICIAN_KEY=$TK&format=json");

// Открываем curl-сессию:
curl_exec($ch);

// Закрываем curl-сессию:
curl_close($ch);

// DEBUG
//$server_output = curl_exec ($ch);
//$error_message = curl_errno($ch);
//curl_close ($ch);
//print_r($server_output);

?>