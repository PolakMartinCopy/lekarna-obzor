<h1>Hlavní stránka administrace</h1>
<h2>Statistiky aktuálního měsíce</h2>
<ul>
	<li><?=$html->link('Celkový počet objednávek', '/rep/orders/index') ?>: <?=$orders_count ?>
		<ul>
			<li><?=$html->link('nepřevzatých', '/rep/orders/index/status_id:1') ?>: <?=$not_taken_over_orders_count ?></li>
			<li><?=$html->link('převzatých', '/rep/orders/index/status_id:2') ?>: <?=$taken_over_orders_count ?></li>
			<li><?=$html->link('zboží odesláno', '/rep/orders/index/status_id:3') ?>: <?=$goods_sent_orders_count ?></li>
		</ul>
	</li>
	<li>Průměrná hodnota objednávky: <?=$average_order ?> Kč</li>
	<li>Celkový součet objednávek: <?=$sum_order ?> Kč</li>
	<li>Počet registrovaných zákazníků: <?=$companies_count ?></li>
</ul>	