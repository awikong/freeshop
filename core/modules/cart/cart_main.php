<a href="/cart" title="Перейти в корзину" class="cart_main">
	<img src="template/images/cart.png" alt="">
	<?php
			
		//Делаем запрос в БД и проверяем содержимое корзины и считаем стоимость товаров
		$price_all = mysqli_fetch_array(mysqli_query($connection, "
			SELECT SUM(`price`) price_all
			FROM `order_product` o
			LEFT JOIN `product` p ON p.`id_product` = o.`id_product` 
			WHERE `id_cart` = '{$id_cart}' AND `id_order` IS NULL
		"));
	
		//Если корзина не пустая, то продолжаем
		if ($price_all['price_all'] != NULL) {
			
			//Объявляем переменную со стоимостью товаров
			$price_all = number_format($price_all['price_all'], 0, '', ' ');
			
			echo "<font class=\"cart_main_price\">{$price_all} ₽</font>";
			
		} else {
			
			echo "<span>Корзина пуста</span>";
			
		}
			
	?>
</a>