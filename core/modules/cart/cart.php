<form method="POST">
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
		
		//Если была нажать кнопка 'Очистить корзину'
		if (isset($_POST['clear'])) {
			
			//Делаем запрос в БД и удаляем все заказы пользователя
			mysqli_query($connection, "DELETE FROM `order_product` WHERE `id_cart` = '{$id_cart}'");
			
			//Перенаправляем пользователя обратно
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			
		}
		
			
		//Делаем запрос в БД и получаем информацию о заказах
		$orders = mysqli_query($connection, "
			SELECT *, (SELECT COUNT(`id_order_product`) FROM `order_product` WHERE `id_product` = p.`id_product` AND `id_cart` = '{$id_cart}' AND `id_order` IS NULL) product_count
			FROM `product` p 
			LEFT JOIN `order_product` o ON o.`id_product` = p.`id_product`
			WHERE `id_cart` = '{$id_cart}' AND `id_order` IS NULL
			GROUP BY p.`id_product`
		");
		
		if ($orders->num_rows != '0') {
		
		$product_array = array(
			"id_cart" => "{$id_cart}"
		);
		
	?>
	<div class="cart_panel">
		<span class="cart_panel_product_name">
			Наименование товара
		</span>
		<span class="cart_panel_product_price">
			Цена
		</span>
		<span class="cart_panel_product_count">
			Количество
		</span>
	</div>
	<?php
			
			//Выводим список товаров
			while ($order = mysqli_fetch_assoc($orders)) {
				
				//Объявляем нужные переменные
				$id_order_product	= $order['id_order_product'];
				$id_product				=	$order['id_product'];
				$name							=	$order['name'];
				$price						= $order['price'];
				$price_format			= number_format($price, 0, '', ' ');
				$product_count		= $order['product_count'];
				
				$product_array['items'][] = array(
					"id_order_product"	=> "{$id_order_product}",
					"id_product" 				=> "{$id_product}",
					"name" 							=> "{$name}",
					"price" 						=> "{$price}",
					"product_count" 		=> "{$product_count}"
				);
				
				echo "
					<div class=\"cart_product_block\">
					<img src=\"/core/uploads/{$id_product}/1.jpg\" alt=\"{$name}\" title=\"{$name}\" class=\"cart_product_preview\">
					<span class=\"cart_product_name\">
						{$name}
					</span>
					<span class=\"cart_product_price\">
						{$price_format} ₽
					</span>
					<button type=\"submit\" name=\"minus[{$id_product}]\" title=\"Убрать товар из корзины\">-</button>
					<span class=\"cart_product_count\">{$product_count}</span>
					<button type=\"submit\" name=\"plus[{$id_product}]\" title=\"Добавить товар в корзину\">+</button>
					<hr style=\"clear: both;\" />
					</div>
				";
				
			}
			
			//Если была нажата кнопка 'Заказать'
			if (isset($_POST['order'])) {
				
				//Преобразуем массив с товарами в JSON
				$product_json = json_encode($product_array);
				
				//Делаем AJAX запрос
				echo "
					<script>
						var product_json = '" . $product_json . "';
						$.ajax({
							type: 'POST',
							url: '/core/modules/cart/cart_handler.php',
							data: product_json,
							dataType: 'json'
						});

						location.href = '" . $_SERVER['HTTP_REFERER'] . "';
						
						alert('Ваш заказ успешно отправлен!');
						
					</script>
				";
			}
			
	?>
	<div class="cart_price_all">Итого: <span><?php echo $price_all; ?> ₽</span></div>
	<div style="clear:both;"></div>
	<button type="submit" name="clear" class="clear" title="Очистить корзину">Очистить корзину</button>
	<button type="submit" name="order" class="order" title="Заказать">Заказать</button>
	<?php
			
		} else {
			
			echo "<span class=\"cart_product_none\">Корзина пуста</span>";
			
		}

	?>
</form>