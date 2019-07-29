<?php
	
	//Подключаем конфиг
	require ''. $_SERVER['DOCUMENT_ROOT'] . '/core/config.php'; 
	
	//Получаем запрос
	$products = json_decode(file_get_contents('php://input'));
	
	//Если пустой, завершаем скрипт
	if (empty($products)) {

		exit('Hacking attempt.');
		
	}
	
	//Объявляем нужные переменные
	$id_cart	= $products->id_cart;
	$date 		= time();
	
	//Делаем запрос в БД и добавляем новый заказ
	mysqli_query($connection, "INSERT INTO `order` (`id_cart`, `date`) VALUES ('{$id_cart}', '{$date}')");
	
	//Объявляем переменную с идентификатором заказа
	$id_order = mysqli_insert_id($connection);
	
	//Делаем запрос в БД и добавляем идентификатор заказа на товары данного заказа
	mysqli_query($connection, "UPDATE `order_product` SET `id_order` = '{$id_order}' WHERE `id_cart` = '{$id_cart}' AND `id_order` IS NULL");
	
	//Объявляем переменны для письма
	$to 			= "awikong@mail.ru";
	$subject 	= "Заказ №{$id_order}";
	$headers 	= "Content-type:text/html; charset = utf-8 \r\n";
	
	//Формируем наполнение письма
	$message = "
		<style>

			@import url(\"https://fonts.googleapis.com/css?family=Open+Sans:400,700&display=swap\");

			hr {
				border: none;
				background: #e6e6e6;
				height: 1px;
				width: 100%;
			}
			
			.panel {
				font-family: 'Open Sans', sans-serif;
			}
			
			.panel span {
				color: #777777;
				text-transform: uppercase;
				font-size: 12px;
			}

			.panel_product_name {
				margin: 15px 0 0 15px;
				float: left;
			}

			.panel_product_price {
				margin: 15px 15px 0 0;
				float: right;
				text-align: center;
			}
			
			.product {
				margin: 0 0 15px 0;
				font-family: 'Open Sans', sans-serif;
				clear: both;
			}
			
			.product_name {
				margin: 15px 0 15px 15px;
				float: left;
				white-space: nowrap;
				overflow: hidden;
				text-overflow: ellipsis;
				color: #262626;
				font-size: 18px;
				font-weight: 700;
			}

			.product_price {
				margin: 15px 0 0 0;
				float: right;
				text-align: center;
				font-weight: 700;
				font-size: 18px;
				color: #16a71a;
				text-align: center;
				margin: 15px 15px 0 0;;
			}
			
			.product_count {
				font-weight: 400;
				color: #262626;
			}
			
			.price_all {
				font-weight: 700;
				font-size: 18px;
				color: #262626;
				float: right;
				margin: 5px 15px 0 0;
			}

			.price_all span {
				color: #16a71a;
			}
			
		</style>
		<div class=\"panel\">
			<span class=\"panel_product_name\">
				Наименование товара
			</span>
			<span class=\"panel_product_price\">
				Цена
			</span>
		</div>
	";
	
	foreach ($products->items as $product) {
		
		$name 					= $product->name;
		$price					= $product->price;
		$price_format 	= number_format($price, 0, '', ' ');
		$product_count 	= $product->product_count;
		
		$message .= "
			<div class=\"product\">
				<span class=\"product_name\">
					{$name}
				</span>
				<span class=\"product_price\">
					{$price_format} ₽ <span class=\"product_count\">x {$product_count}</span>
				</span>
			</div>
			<hr />
		";
		
		$price_product = $price * $product_count;
		
		$price_all = $price_all + $price_product;
		
	}
	
	$price_all_format	= number_format($price_all, 0, '', ' ');
	
	$message .= "
		<div class=\"price_all\">Итого: <span>{$price_all_format} ₽</span></div>
	";
	
	//Отправляем письмо
	mail($to, $subject, $message, $headers);

?> 