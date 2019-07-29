<?php
	
	//Если была нажата кнопка '+'
	if (isset($_POST['plus'])) {
		
		//Объявляем переменную с ID товара
		$id_product = key($_POST['plus']);
		
		//Делаем запрос в БД и добавляем товар в заказы
		mysqli_query($connection, "INSERT INTO `order_product` (`id_cart`, `id_product`) VALUES ('{$id_cart}', '{$id_product}')");
		
		//Перенаправляем пользователя обратно
		header('Location: ' . $_SERVER['HTTP_REFERER']);
		
	}
	
	//Если была нажата кнопка '-'
	if (isset($_POST['minus'])) {
		
		//Объявляем переменную с ID товара
		$id_product = key($_POST['minus']);
		
		//Делаем запрос в БД и ищем заказы данного товара для пользователя
		$order = mysqli_fetch_array(mysqli_query($connection, "SELECT `id_order_product` FROM `order_product` WHERE `id_cart` = '{$id_cart}' AND `id_product` = '{$id_product}' LIMIT 1"));
		
		//Если нашли, то продолжаем
		if (!empty($order)) {
			
			//Объявляем переменную с ID заказа
			$id_order_product = $order['id_order_product'];
			
			//Делаем запрос в БД и удаляем товар из заказа
			mysqli_query($connection, "DELETE FROM `order_product` WHERE `id_order_product` = '{$id_order_product}'");
			
		}
		
		//Перенаправляем пользователя обратно
		header('Location: ' . $_SERVER['HTTP_REFERER']);
			
	}
	
	//Объявляем функцию со списком товаров
	function products($connection, $id_cart) {
		
		//Делаем запрос в БД и получаем информацию о товарах
		$products = mysqli_query($connection, "SELECT *, (SELECT COUNT(`id_order_product`) FROM `order_product` WHERE `id_product` = p.`id_product` AND `id_cart` = '{$id_cart}' AND `id_order` IS NULL) product_count FROM `product` p");
		
		//Если товары есть, то продолжаем
		if ($products->num_rows != '0') {
			
			//Выводим список товаров
			while ($product = mysqli_fetch_assoc($products)) {
				
				//Выводим нужные переменные
				$id_product 		= $product['id_product'];
				$name						=	$product['name'];
				$price					= number_format($product['price'], 0, '', ' ');
				$product_count	= $product['product_count'];
				
				echo "
				<div class=\"product_block\">
					<img src=\"/core/uploads/{$id_product}/1.jpg\" alt=\"{$name}\" title=\"{$name}\" class=\"product_preview\">
					<span class=\"product_name\" title=\"{$name}\">{$name}</span>
					<span class=\"product_price\">{$price} ₽</span>
					<button type=\"submit\" name=\"minus[{$id_product}]\" title=\"Убрать товар из корзины\">-</button>
					<span class=\"product_count\">{$product_count}</span>
					<button type=\"submit\" name=\"plus[{$id_product}]\" title=\"Добавить товар в корзину\">+</button>
				</div>
				";
				
			}
			
		} else {
			
			echo "<div class=\"product_none\">Товары отсутствуют</div>";
			
		}

	}

?>
<form method="POST" class="catalog_list">
	<?php products($connection, $id_cart); ?>
</form>