<?php

	//Объявляем массив с данными для подключения к базе данных.
	$config = array (
		'server'		=> 'localhost',
		'user'			=> '',
		'password'	=> '',
		'name'			=> 'freeshop'
	);
	
	//Объявляем переменную с соединением с базой данных.
	$connection = mysqli_connect(
		$config['server'],
		$config['user'],
		$config['password'],
		$config['name']
	);
	
	//Если соединением неудачное, выводим ошибку.
	if ( $connection == false ) {
		echo mysqli_connect_error();
		exit;
	}
	
	//Устанавливаем кодировку базы данных UTF8.
	mysqli_set_charset($connection, 'UTF8');
	
	//Открываем сессию
	session_start();
	
	//Объявляем нужные переменные
	$session_id = session_id();
	$date				= time();

	//Ищем в БД текущею сессию
	$session = mysqli_fetch_array(mysqli_query($connection, "SELECT * FROM `cart` WHERE `hash` = '{$session_id}'"));
	
	//Если не существует
	if (empty($session)) {
		
		//Добавляем новую сессию в БД
		mysqli_query($connection, "INSERT INTO `cart` (`hash`, `date`) VALUES ('{$session_id}', '{$date}')");
		
	} else {
		
		//Объявляем переменную с ID корзины
		$id_cart = $session['id_cart'];
		
		//Обновляем данные текущей сессии
		mysqli_query($connection, "UPDATE `cart` SET `date` = '{$date}' WHERE `hash` = '{$session_id}'");
		
	}

?>