<?php


	require 'botlib.php';
	error_reporting(0);
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	define('BOT_TOKEN', ''); //Тестовый бот\
	define('API_URL', 'https://api.telegram.org/bot'.BOT_TOKEN.'/'); 
	$APICRM = 'https://dev.crm.inta.group/telegram/';


	$content = file_get_contents("php://input");
	$update = json_decode($content, true);
	$message = $update["message"];
	$messagetext = $message['text'];

	$connect = new mysqli('localhost', '', '', '');

	$tgid = $message['from']['id'];
	
	if(empty($tgid)){
		file_put_contents('1.txt', 'good');
		$tgid = $update['callback_query']['from']['id'];
		$message['chat']['id'] = $update['callback_query']['message']['chat']['id'];
	}

	$query='SELECT * FROM cargo_info_tgbot WHERE tgid = ?';
	$stmt = $connect->prepare($query);
	$stmt->bind_param('i', $tgid);
	$stmt->execute();
	$result=$stmt->get_result();
	$row = $result->fetch_row();
	$rows=$result->num_rows;
	if($rows!=0){
		$user['id'] = $row[0];
		$user['tgid'] = $row[1];
		$user['act'] = $row[2];
		$user['act_information'] = $row[3];
		$act = $row['act'];
		$act_information = $row['act_information'];
		//Пользователь найден
		// INSERT INTO `telegram_message` (`id`, `message_id`, `chat_id`, `message`, `user_id`) VALUES (NULL, '2321', '12312', '1232321', '2321');
		if(!empty($messagetext)){
			$query='INSERT INTO telegram_message (id, message_id, chat_id, message, user_id) VALUES (NULL, ?, ?, ?, ?)';
			$stmt = $connect->prepare($query);
			$stmt->bind_param('iisi', $message['message_id'],$message['chat']['id'], $messagetext ,$user['id']);
			$stmt->execute();
			$result=$stmt->get_result();
		}
		
	}
	else{ //регистрация пользователя
		$query='INSERT INTO cargo_info_tgbot (id, tgid, act, act_information) VALUES (NULL, ?, 0, 0)';
		$stmt = $connect->prepare($query);
		$stmt->bind_param('i', $tgid);
		$stmt->execute();
		sendanswer('Инструкция к пользованию ботом', $message);
		die();
	}
	

	$auth = curl_init($APICRM . 'usercheck?user_id=' . $tgid);
	curl_setopt($auth, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($auth, CURLOPT_CONNECTTIMEOUT, 5);
	curl_setopt($auth, CURLOPT_TIMEOUT, 60);
	$authres = curl_exec($auth);

	if($authres !=1)
	{
		sendanswer('Вы не авторизированы в данном боте. Обратитесь к it отделу, ваш телеграм id' . $tgid, $message);
		die();
	}


	if (!empty($update['callback_query'])) {
		
		$data = $update['callback_query']['data'];
		$messagetext = $data;
		$message['chat']['id'] = $update['callback_query']['message']['chat']['id'];
		answerCallbackQuery($update['callback_query']['id']);

		if(stripos($messagetext, "clientcargos|") !==false){

			// file_put_contents('1.txt', 'good');
			$client_id = str_replace("clientcargos|", "", $data);
			//API запрос к crm для получения всех грузов пользователя по id //good




			
			$handle = curl_init($APICRM .'showcargos?client_id=' . $client_id);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($handle, CURLOPT_TIMEOUT, 60);
			$cargos = curl_exec($handle);


			$cargos = json_decode($cargos, true);
			
			$answer = '';
			foreach ($cargos as $cargo) {
			 	$answer = $answer .  '/CC'	 . $cargo . "\n";
			 } 
			 if($answer == ''){
			 	$answer = 'Грузов не найдено🙊';
			 }
			sendanswer($answer,$message);
			die();
		}



		if(stripos($messagetext, "setinsurance|") !==false){
			$cargo_id = str_replace("setinsurance|", "", $data);
			$answer = 'Выберите процент страховки:';
			$inlinek['inline_keyboard'] = array(array());
			$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => '0%','callback_data' => "insurance|0|$cargo_id"]]);
			$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => '1%','callback_data' => "insurance|1|$cargo_id"]]);
			$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => '2%','callback_data' => "insurance|2|$cargo_id"]]);
			$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => 'Hold','callback_data' => "insurance|Hold|$cargo_id"]]);
			sendanswer($answer,$message, $inlinek);
			die();
		}


		if(stripos($messagetext, "changestatus|") !==false){
			$cargo_id = str_replace("changestatus|", "", $data);
			$answer = 'Выберите метод доставки:';

			$inlinek['inline_keyboard'] = array(array());
			$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => 'Post','callback_data' => "changestatusdone|Post|$cargo_id"]]);
			$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => 'Special','callback_data' => "changestatusdone|Special|$cargo_id"]]);
			$inlinek['inline_keyboard'][1] = array();
			$inlinek['inline_keyboard'][1] = array_merge($inlinek['inline_keyboard'][1],[['text' => 'Auto','callback_data' => "changestatusdone|Auto|$cargo_id"]]);
			$inlinek['inline_keyboard'][1] = array_merge($inlinek['inline_keyboard'][1],[['text' => 'Air+EU ','callback_data' => "changestatusdone|Air+EU|$cargo_id"]]);
			$inlinek['inline_keyboard'][1] = array_merge($inlinek['inline_keyboard'][1],[['text' => 'Railway+EU','callback_data' => "changestatusdone|Railway+EU|$cargo_id"]]);
			$inlinek['inline_keyboard'][2] = array();
			$inlinek['inline_keyboard'][2] = array_merge($inlinek['inline_keyboard'][2],[['text' => 'Sea+EU','callback_data' => "changestatusdone|Sea+EU|$cargo_id"]]);
			$inlinek['inline_keyboard'][2] = array_merge($inlinek['inline_keyboard'][2],[['text' => 'Air+Freight','callback_data' => "changestatusdone|Air+Freight|$cargo_id"]]);
			$inlinek['inline_keyboard'][2] = array_merge($inlinek['inline_keyboard'][2],[['text' => 'Auto+Freight','callback_data' => "changestatusdone|Auto+Freight|$cargo_id"]]);
			$inlinek['inline_keyboard'][3] = array();
			$inlinek['inline_keyboard'][3] = array_merge($inlinek['inline_keyboard'][3],[['text' => 'Railway+Freight','callback_data' => "changestatusdone|Railway+Freight|$cargo_id"]]);
			$inlinek['inline_keyboard'][3] = array_merge($inlinek['inline_keyboard'][3],[['text' => 'Sea+Freight ','callback_data' => "changestatusdone|Sea+Freight |$cargo_id"]]);
			$inlinek['inline_keyboard'][4] = array();
			$inlinek['inline_keyboard'][4] = array_merge($inlinek['inline_keyboard'][4],[['text' => 'Hold','callback_data' => "changestatusdone|Hold|$cargo_id"]]);
			sendanswer($answer,$message, $inlinek);
			die();
		}
		

		if(stripos($messagetext, "changestatusdone|") !==false){
			$query = str_replace("changestatusdone|", "", $data);
			$query = explode('|', $query);
			$status = $query[0];
			$cargo_id = $query[1];
			//API для смены статуса груза 
			$handle = curl_init($APICRM . 'changecargostatus?cargo_id='.$cargo_id . "&shipment_type=" . $status);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($handle, CURLOPT_TIMEOUT, 60);
			$res = curl_exec($handle);

			if($res == true){
			$answer = 'Статус успешно сменен на ' . $status;
			}
			if($res == false){
			$answer = 'Произошла ошибка☹️';
			}
			sendanswer($APICRM . 'changecargostatus?cargo_id='.$cargo_id . "&shipment_type=" . $status, $message);
			sendanswer($answer,$message);
			die();
		}


		

		if(stripos($messagetext, "insurance|") !==false){

			$query = str_replace("insurance|", "", $data);
			$query = explode('|', $query);	
			$percent = $query[0];
			$cargo_id = $query[1];
			//api запрос на изменение процента страховки
			//---------------------------------------
			//------------

			
			$handle = curl_init($APICRM . 'changeinsurance?cargo_id=' . $cargo_id . '&insurance=' . $percent);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($handle, CURLOPT_TIMEOUT, 60);
			$res = curl_exec($handle);
			
			//sendanswer($APICRM . 'changeinsurance?cargo_id=' . $cargo_id . '&insurance=' . $percent, $message);

			
			//api запрос на получение информации о грузе
			//---------------------------------------
			//------------

			// $res = file_get_contents($APICRM . 'getcargo?cargo_id=' . $cargo_id);	
			// $cargo_res = json_decode($res, true);

			// $cargo['id'] = $cargo_res['cargo_id'];
			// if($percent  != 'Hold'){
			// 	$percent = $percent . '%';
			// }
			// $cargo['insurance'] = $cargo_res['cargo_id'];
			// $cargo['cost'] = '2500';
			// $cargo['insurance_sum'] = $cargo_res['insurancecost'];
			// $cargo['manager'] = '';

			//$answer = "$cargo[id] \nУ груза указана страховка\nInsurance: $cargo[insurance]\nCargo cost: $cargo[cost]\nInsurance sum: $cargo[insurance_sum]\n-------------\nManager edit: $cargo[manager]";
			
			deleteMessage($update['callback_query']['message']['chat']['id'], $update['callback_query']['message']['message_id']);
			
			if($percent !=0 && $percent != 'Hold'){
				$answer = 'Укажите цену груза:';
				$query='UPDATE cargo_info_tgbot SET act = 1 WHERE cargo_info_tgbot.id = ?';
				$stmt = $connect->prepare($query);
				$stmt->bind_param('s', $user['id']);
				$stmt->execute();

				$query='UPDATE cargo_info_tgbot SET act_information = ? WHERE cargo_info_tgbot.id = ?';
				$stmt = $connect->prepare($query);
				$stmt->bind_param('si', $cargo_id, $user['id']);
				$stmt->execute();

				sendanswer($answer,$message);
			}	
			

			
			die();
		}
		

		if(stripos($messagetext, "leadtoclient|") !==false){
			$answer = 'Лид переведен в клиенты!';
			$lead_id = str_replace("leadtoclient|", "", $data);
			//API перевода лида в клиенты


			$handle = curl_init($APICRM . 'leadtoclient?lead_id=' . $lead_id);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
			curl_setopt($handle, CURLOPT_TIMEOUT, 60);
			$lead_ret = curl_exec($handle);
			$lead_ret = json_decode($lead_ret, true);
			
			$lead['id'] = $lead_ret['client_id'];
			$lead['name'] = $lead_ret['name'];
			$lead['phone'] = $lead_ret['phone'];
			$lead['description'] = $lead_ret['comment'];
			$lead['manager'] = $lead_ret['manager'];
			$lead['datecreate'] = $lead_ret['dateadd'];
			$lead['email'] = $lead_ret['email'];
			$lead['source'] = $lead_ret['source_id'];
			$lead['status'] = $lead_ret['status'];

			$newtext = "Код клиента: $lead[id] \nИмя: $lead[name]\nТелефон: $lead[phone]\nEmail: $lead_ret[email]\nSource: $lead[source] \nManager: $lead[manager]\nСтатус: $lead[status]\nDescription: $lead[description]";
							//$chat_id, $message_id, $text
			



			editMessage($update['callback_query']['message']['chat']['id'], $update['callback_query']['message']['message_id'], $newtext);

			sendanswer($answer,$message);
			die();
		}

	}




	$clientCodeIdentifier = "/[0-9][0-9][\-]/";
	$cargosIdentifier = 'CC';
	$leadIdentifier = 'LD-';

	if(preg_match($clientCodeIdentifier, $messagetext) ){
		$client_id  = preg_replace($clientCodeIdentifier, '', $messagetext);
		// Тут должен быть API поиск клиента по id;
	
		

		$handle = curl_init($APICRM .'getclient?client_id=' . $client_id . "&user_id=" . $tgid);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($handle, CURLOPT_TIMEOUT, 60);
		$client_ret = curl_exec($handle);

			
			

		if(empty($client_ret)){
			sendanswer('Клиент не найден☹️', $message);
			die();
		}
		$client_ret = json_decode($client_ret, true);


		
		$client['id'] = $client_ret['client_id'];
		$client['name'] = $client_ret['name'];
		$client['phone'] = $client_ret['phone'];
		$client['email'] = $client_ret['email'];
		$client['balance'] = $client_ret['balance'] . 'UAH';
		$client['status'] = $client_ret['status'];
		$client['source'] = $client_ret['source_id'];
		$client['description'] = $client_ret['comment'];

		$answer = "Id: $client[id]\nИмя: $client[name]\nТелефон: $client[phone]\nEmail: $client[email]\nБаланс: $client[balance]\nСтатус: $client[status]\nSource: $client[source]\nОписание: $client[description]";
		
		$inlinek['inline_keyboard'] = array(array());
		$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => 'Вывести грузы👆','callback_data' => "clientcargos|$client_id"]]);
		
		sendanswer($answer, $message, $inlinek);
		die();
	}


	if(stripos($messagetext, $leadIdentifier) !==false){
		$lead_id  = str_replace($leadIdentifier, '', $messagetext);
		// Тут должен быть API поиск лида по id;

		$handle = curl_init($APICRM .'getlead?lead_id=' . $lead_id . "&user_id=" . $tgid);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($handle, CURLOPT_TIMEOUT, 60);
		$lead_ret = curl_exec($handle);

		if(empty($lead_ret)){
			//sendanswer($APICRM .'getlead?lead_id=' . $lead_id ,$message);

			sendanswer('Лид не найден☹️', $message);
			// sendanswer(print_r($lead_ret, true) . 'a', $message);
			die();
		}
		$lead_ret = json_decode($lead_ret, true);
		$lead['id'] = $lead_ret['client_id'];
		$lead['name'] = $lead_ret['name'];
		$lead['phone'] = $lead_ret['phone'];
		$lead['description'] = $lead_ret['comment'];
		$lead['manager'] = $lead_ret['manager'];
		$lead['datecreate'] = $lead_ret['dateadd'];
		$lead['email'] = $lead_ret['email'];
		$lead['source'] = $lead_ret['source_id'];
		$lead['status'] = $lead_ret['status'];

		$answer = "Имя: $lead[name]\nТелефон: $lead[phone]\nEmail: $lead_ret[email]\nSource: $lead[source] \nManager: $lead[manager]\nСтатус: $lead[status]\nDescription: $lead[description]";
		
		$inlinek['inline_keyboard'] = array(array());
		$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => 'Перевести в клиенты👆','callback_data' => "leadtoclient|$lead_id"]]);
	

		sendanswer($answer, $message, $inlinek);
		die();
	}


	if(stripos($messagetext, $cargosIdentifier) !==false){
		$cargosid  = str_replace('/CC', '', $messagetext);
		$cargosid  = str_replace($cargosIdentifier . '-', '', $cargosid);
		// Тут должен быть API поиск CC по id;	
		

		$handle = curl_init($APICRM . 'getcargo?cargo_id=' . $cargosid . "&user_id=" . $tgid);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($handle, CURLOPT_TIMEOUT, 60);
		$cargo_res = curl_exec($handle);

		$cargo_res = json_decode($cargo_res, true);

		if(empty($cargo_res)){
			sendanswer('Груз не найден☹️' . $APICRM . 'getcargo?cargo_id=' . $cargosid, $message);
			die();
		}

		$cargo['manager'] = $cargo_res['managercreate'];
		$cargo['from'] = $cargo_res['from'];
		$cargo['to'] = $cargo_res['to'];
		$cargo['checkpoint'] = $cargo_res[''];
		$cargo['type'] = $cargo_res['type_id'];
		$cargo['status'] = $cargo_res['status'];
		$cargo['weight'] = $cargo_res['weight'] . " кг.";
		$cargo['volume'] = $cargo_res['volume'] . ' м³';
		$cargo['cartons'] = $cargo_res['cartons'];
		$cargo['localtrack'] = $cargo_res['local_track'];
		$cargo['insurance'] = $cargo_res['insurancepercent'] ? $cargo_res['insurancepercent'] . '%' : "Отсутствует";
		$cargo['cargocost'] = $cargo_res['cargocost'] ?? 'Отсутствует';

		
		$answer = "✅Груз найден:\n-------------\nManager created: $cargo[manager]\n-------------\nFrom: $cargo[from]\nTo: $cargo[to]\nCheckpoint: $cargo[checkpoint]\nType: $cargo[type]\nStatus: $cargo[status]\nWeight: $cargo[weight]\nVolume: $cargo[volume]\nCartons: $cargo[cartons]\nLocal track: $cargo[localtrack]\nInsurance: $cargo[insurance]";
		$inlinek['inline_keyboard'] = array(array());
		if($cargo['insurance'] === 'Отсутствует'){
			$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => 'Установить страховку','callback_data' => "setinsurance|$cargosid"]]);			
		}
		$inlinek['inline_keyboard'][0] = array_merge($inlinek['inline_keyboard'][0],[['text' => 'Изменить метод доставки','callback_data' => "changestatus|$cargosid"]]);		
		sendanswer($answer, $message, $inlinek);
		die();
	}


	if($user['act'] == 1){
		$newmessagetext = preg_replace('~[^0-9]+~','',$messagetext);
		if($messagetext != $newmessagetext){ 
			sendanswer("Неправильный формат цены☹️", $message);
			die();
		}
		//api запрос для того чтобы выставить стоиость груза:
		$price = $messagetext;
		$cargo_id = $user['act_information'];
		file_get_contents();

		$handle = curl_init($APICRM . 'changecost?cargo_id=' . $cargo_id . "&cost="  . $price);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 5);
		curl_setopt($handle, CURLOPT_TIMEOUT, 60);
		curl_exec($handle);

		$query='UPDATE cargo_info_tgbot SET act = 0 WHERE cargo_info_tgbot.id = ?';
		$stmt = $connect->prepare($query);
		$stmt->bind_param('s', $user['id']);
		$stmt->execute();

		sendanswer($APICRM . 'changecost?cargo_id=' . $cargo_id . "&cost="  . $price, $message);
		sendanswer("Цена была успешно изменена", $message);
		die();


	}
	sendanswer('Неправильный формат сообщения!', $message);

 ?>