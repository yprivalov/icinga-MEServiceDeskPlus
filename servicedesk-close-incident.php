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
            "filterby": "2_MyView"
        }
    }
}
';

// Формируем документ для закрытия инцидента:
// "closeComment": "Инцидент закрыт системой мониторинга со статусом "' . $state . '"
$close_request='
{
    "operation": {
        "details": {
            "closeAccepted": "Accepted",
            "closeComment": "Инцидент закрыт системой мониторинга со статусом OK"
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
            "filterby": "2_MyView"
        }
    }
}
';

// Формируем документ для закрытия инцидента:
// "closeComment": "Инцидент закрыт системой мониторинга со статусом "' . $state . '"
$close_request='
{
    "operation": {
        "details": {
            "closeAccepted": "Accepted",
            "closeComment": "Инцидент закрыт системой мониторинга со статусом OK"
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

// Закрываем curl-сессию:
//curl_close($ch);

$arr = json_decode($requests);
//print_r($arr);
//echo $arr->operation->details[0]->WORKORDERID;
//echo $arr->operation->details[1]->WORKORDERID;

foreach($arr->operation->details as $item)
{

  // Получаем ID каждого инцедента для каждого массива details
  $request_id=$item->WORKORDERID;
  // Устанавливаем новый url для curl
  $url_id=$url."/".$request_id;
  curl_setopt($ch, CURLOPT_URL, $url_id);
  /* Передаем новую строку с данными для передачи HTTP POST.
  Для REST API параметром OPERATION_NAME=GET_REQUEST даем команду на получение параметров инцидента, параметром
  INPUT_DATA передаем request_id, а в конце строки указываем API Key в параметре TECHNICIAN_KEY */
  curl_setopt($ch, CURLOPT_POSTFIELDS, "OPERATION_NAME=GET_REQUEST&TECHNICIAN_KEY=$TK&format=json"); //INPUT_DATA=$request_id
  //Открываем curl-сессию и передаем полученный результат в переменную $req_details:
  $req_details = curl_exec($ch);
  $arr1 = json_decode($req_details);

  /* Анализируем полученные параметры инцидента: хост и служба должны соответствовать полученным от Nagios
  имени хоста(host) и названию сервиса(service) */

  if ($arr1->Хост==$host)
  {
    if ($arr1->Служба==$service)
    {

      /* Если условия выполнены, то устанавливаем новые параметры   для curl-сессии. Для REST API параметром OPERATION_
      NAME=CLOSE_REQUEST даем команду на закрытие инцидента, параметром INPUT_DATA передаем XML-документ, а в конце
      строки указываем API Key в параметре TECHNICIAN_KEY */

      curl_setopt($ch, CURLOPT_URL, $url_id);
      curl_setopt($ch, CURLOPT_POSTFIELDS, "OPERATION_NAME=CLOSE_REQUEST&INPUT_DATA=$close_request&TECHNICIAN_KEY=$TK&format=json");

      //открываем curl-сессию:
      curl_exec($ch);

    }
  }
}

// Закрываем curl-сессию:
curl_close($ch);

// DEBUG
//$server_output = curl_exec ($ch);
//$error_message = curl_errno($ch);
//curl_close ($ch);
//print_r($server_output);

?>
