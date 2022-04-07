<?php

	if(isset($_POST["sum"]) && $_POST["sum"]>0){
		$comment = 'АЗС:' . $_POST["azs"] . "<br>" .
				'Количество литров:' . $_POST["amount"] . "<br>" .
				'Автомобиль:' . $_POST["auto"] . "<br>" .
				'ФИО водителя:' . $_POST["driverName"] . "<br>" .
				'Сумма:' . $_POST["sum"];
		$curl = curl_init();
		curl_setopt_array($curl, array(
		    CURLOPT_URL => '',
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => '',
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 0,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    CURLOPT_POSTFIELDS => array(
		        'user_id' => '7',
		        'api_token' => '',
		        'callback_url' => 'http://oplata.lc/', // Сюда вставить callback url (string)
		        'order_id' => time(), // Уникальный ID транзакции (int)
		        'amount' => $_POST["sum"], // Сумма транзакции (float)
		        'comment' => $comment,
		    ),
		    CURLOPT_HTTPHEADER => array(
		        'Content-Type: multipart/form-data'
		    ),
		));

		$response = curl_exec($curl);

		curl_close($curl);
		// echo $response;
		$response = json_decode($response, true);
		$url = $response['data']['pay_url'];
		// echo $url;
		header('Location: ' . $url);

	}
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/style.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<title>Оплатить</title>
</head>
<body>
	<section id="cover">
		<div id="cover-caption">
			<!-- <h1 class="display-3">Title</h1> -->
			<form action="" class="form-inline col-12" method="POST">
				<div class="container pt-3">
					<div class="row">
						<div class="col col-xs-12 col-lg-2">
					    	<h5 class="text-white">АЗС</h4>
					    	<input class="form-control" placeholder="АЗС" name="azs" required>
						</div>
						<div class="col-xs-12 col-lg-2">
					    	<h5 class="text-white">Кол-во литров</h5>
					    	<input type="number" class="form-control" placeholder="Количество литров" min="0" data-bind="value:replyNumber" name="amount" required>
						</div>
						<div class="col-xs-12 col-lg-2">
					    	<h5 class="text-white">Автомобиль</h5>
					    	<input type="text" class="form-control" placeholder="Автомобиль" name="auto" required>
						</div>
						<div class="col-xs-12 col-lg-2">
					    	<h5 class="text-white">ФИО водителя</h5>
					    	<input type="text" class="form-control" placeholder="ФИО" name="driverName" required>
						</div>
						<div class="col-xs-12 col-lg-2">
					    	<h5 class="text-white">Сумма</h5>
					    	<input value="0" type="number" class="form-control" id="sum" placeholder="Сумма" min="0" data-bind="value:replyNumber" name="sum" required>
						</div>
						<div class="col-xs-12 col-lg-2 d-flex align-items-end">
					    	<button type="submit" class="btn btn-success btn-block btn-oplata">Оплатить!</button>
						</div>
					</div>
					<div class="text-white pt-2" id="commission">Комиссия сервиса за опрацию составляет 0.5%.</div><!-- Комиссия сервиса за опрацию составляет 0.5%. Итоговая сумма {sum}
 -->
				</div>
				
				<br>
					
			</form>
		</div>
	</section>	
</body>
<script>
	$( "#sum" ).change(function() {
		sum = $(this).val();

		if(sum > 0){
			percent = (sum  / 100 * 0.5);
			sum = parseFloat(sum) + parseFloat(percent);
			$('#commission').text('Комиссия сервиса за опрацию составляет 0.5%. Итоговая сумма ' + sum + ' UAH');
		}else{
			$('#commission').text('Комиссия сервиса за опрацию составляет 0.5%.');

		}
	});
</script>
</html>

