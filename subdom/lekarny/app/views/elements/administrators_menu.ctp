<ul id="nav">
	<li><?echo $html->link('Objednávky', array('controller' => 'orders', 'action' => 'index')) ?>
		<ul>
			<li><?=$html->link('zobrazit celý seznam', array('controller' => 'orders', 'action' => 'index')) ?></li>
			<li><a href="#">---</a></li>
<? foreach ( $menu_statuses as $status ){ ?>
			<li>
				<?php echo $html->link(
					$status['Status']['name'] . ' (' . $status['Status']['count'] . ')',
					array('controller' => 'orders', 'action' => 'index', 'status_id' => $status['Status']['id'], 'rep' => true), 
					array(),
					false,
				false
				) . " "; ?>
			</li>
<?php } ?>
		</ul>
	</li>

	<li><a href="#">Kategorie</a>
		<ul>
			<?php foreach ($menu_categories as $category) { ?>
			<li>
				<?php echo $html->link($category['Category']['name'], array('controller' => 'categories', 'action' => 'index', $category['Category']['id'])) ?>
				<?php if (!empty($category['CategoriesProduct'])) { ?>
				<ul>
					<?php foreach ($category['CategoriesProduct'] as $product) { ?>
					<li><?php echo $html->link($product['Product']['name'], array('controller' => 'products', 'action' => 'view', $product['Product']['id']))?></li>
					<?php } ?>
					<li><a href="#">---</a></li>
					<li><?php echo $html->link('seznam produktů (' . count($category['CategoriesProduct']) . ')', array('controller' => 'products', 'action' => 'index', 'category_id' => $category['Category']['id']))?></li>
					<li><?php echo $html->link('vložit nový produkt', array('controller' => 'products', 'action' => 'add', 'category_id' => $category['Category']['id']))?></li>
					<li><?php echo $html->link('upravit kategorii', array('controller' => 'categories', 'action' => 'edit', $category['Category']['id']))?></li>
					<li><?php echo $html->link('vložit podkategorii', array('controller' => 'categories', 'action' => 'add', 'parent_id' => $category['Category']['id']))?></li>
					<li><?php echo $html->link('přesunout', array('controller' => 'categories', 'action' => 'move', $category['Category']['id']))?></li>
				</ul>
				<?php } ?>
				<ul>
					<?php foreach ($category['CategoriesProduct'] as $product) { ?>
					<li><?php echo $html->link($product['Product']['name'], array('controller' => 'products', 'action' => 'view', $product['Product']['id']))?></li>
					<?php } ?>
					<?php if (!empty($category['CategoriesProduct'])) { ?>
					<li><a href="#">---</a></li>
					<?php } ?>
					<?php foreach ($category['children'] as $child) { ?>
					<li>
						<?php echo $html->link($child['Category']['name'], array('controller' => 'categories', 'action' => 'index', $child['Category']['id'])) ?>
						<ul>
							<?php foreach ($child['CategoriesProduct'] as $product) { ?>
							<li><?php echo $html->link($product['Product']['name'], array('controller' => 'products', 'action' => 'view', $product['Product']['id']))?></li>
							<?php } ?>
							<li><a href="#">---</a></li>
							<li><?php echo $html->link('seznam produktů (' . count($child['CategoriesProduct']) . ')', array('controller' => 'products', 'action' => 'index', 'category_id' => $child['Category']['id']))?></li>
							<li><?php echo $html->link('vložit nový produkt', array('controller' => 'products', 'action' => 'add', 'category_id' => $child['Category']['id']))?></li>
							<li><?php echo $html->link('upravit kategorii', array('controller' => 'categories', 'action' => 'edit', $child['Category']['id']))?></li>
							<li><?php echo $html->link('vložit podkategorii', array('controller' => 'categories', 'action' => 'add', 'parent_id' => $child['Category']['id']))?></li>
							<li><?php echo $html->link('přesunout', array('controller' => 'categories', 'action' => 'move', $child['Category']['id']))?></li>
						</ul>
					</li>
					<?php } ?>
					<?php if (!empty($category['children'])) { ?>
					<li><a href="#">---</a></li>
					<?php } ?>
					<li><?php echo $html->link('seznam produktů (' . count($category['CategoriesProduct']) . ')', array('controller' => 'products', 'action' => 'index', 'category_id' => $category['Category']['id']))?></li>
					<li><?php echo $html->link('vložit nový produkt', array('controller' => 'products', 'action' => 'add', 'category_id' => $category['Category']['id']))?></li>
					<li><?php echo $html->link('upravit kategorii', array('controller' => 'categories', 'action' => 'edit', $category['Category']['id']))?></li>
					<li><?php echo $html->link('vložit podkategorii', array('controller' => 'categories', 'action' => 'add', 'parent_id' => $category['Category']['id']))?></li>
					<li><?php echo $html->link('přesunout', array('controller' => 'categories', 'action' => 'move', $category['Category']['id']))?></li>
				</ul>
			<?php } ?>
			<li><a href="#">---</a></li>
			<li><?php echo $html->link('vložit kategorii', array('controller' => 'categories', 'action' => 'add', 'parent_id' => 0))?></li>
		</ul>
	</li>
	<li><?php echo $html->link('Zákazníci', array('controller' => 'companies', 'action' => 'index'))?>
		<ul>
			<li><?=$html->link('zobrazit seznam', array('controller' => 'companies', 'action' => 'index')) ?></li>
			<li><?=$html->link('zákazníci ke schválení', array('controller' => 'companies', 'action' => 'index', 'authorized' => 0)) ?></li>
		</ul>
	</li>
	<li><?php echo $html->link('Reprezentanti', array('controller' => 'reps', 'action' => 'index'))?></a>
		<ul>
			<?php foreach ($menu_reps as $rep) { ?>
			<li>
				<?php echo $html->link($rep['Rep']['first_name'] . ' ' . $rep['Rep']['last_name'], array('controller' => 'reps', 'action' => 'edit', $rep['Rep']['id']))?>
				<ul>
					<li><? echo $html->link('upravit', array('controller' => 'reps', 'action' => 'edit', 'id' => $rep['Rep']['id'])); ?></li>
					<li><?php echo $html->link('zobrazit oblasti', array('controller' => 'rep_areas', 'action' => 'index', 'rep_id' => $rep['Rep']['id'])); ?></li>
					<li><?php 
						if ( $rep['Rep']['active'] ) {
							echo $html->link('deaktivovat', array('controller' => 'reps', 'action' => 'deactivate', 'id' => $rep['Rep']['id']));
						} else {
							echo $html->link('aktivovat', array('controller' => 'reps', 'action' => 'activate', 'id' => $rep['Rep']['id']));
						} ?>
					</li>
				</ul>
			</li>
			<?php } ?>
			<li><a href="#">---</a></li>
			<li><?=$html->link('zobrazit seznam', array('controller' => 'reps', 'action' => 'index')) ?></li>
			<li><?=$html->link('vytvořit nového repa', array('controller' => 'reps', 'action' => 'add')) ?></li>
		</ul>
	</li>
	<li><a href="#">Nastavení</a>
		<ul>
			<li><?=$html->link('Atributy produktů', array('controller' => 'attributes', 'action' => 'index')) ?>
				<ul>
					<li><?=$html->link('zobrazit seznam atributů', array('controller' => 'attributes', 'action' => 'index')) ?></li>
				</ul>
			</li>
			<li><?=$html->link('Názvy atributů', array('controller' => 'options', 'action' => 'index')) ?>
				<ul>
					<li><?=$html->link('zobrazit názvy atributů', array('controller' => 'options', 'action' => 'index')) ?></li>
					<li><?=$html->link('vytvořit nový název', array('controller' => 'options', 'action' => 'add')) ?></li>
				</ul>
			</li>
			<li><?=$html->link('Výrobci', array('controller' => 'manufacturers', 'action' => 'index')) ?>
				<ul>
					<li><?=$html->link('zobrazit seznam výrobců', array('controller' => 'manufacturers', 'action' => 'index')) ?></li>
					<li><?=$html->link('vytvořit nový název výrobce', array('controller' => 'manufacturers', 'action' => 'add')) ?></li>
				</ul>
			</li>
			<li><?=$html->link('Hladiny DPH', array('controller' => 'tax_classes', 'action' => 'index')) ?>
				<ul>
					<li><?=$html->link('zobrazit seznam hladin DPH', array('controller' => 'tax_classes', 'action' => 'index')) ?></li>
					<li><?=$html->link('vytvořit novou hladinu DPH', array('controller' => 'tax_classes', 'action' => 'add')) ?></li>
				</ul>
			</li>
			<li><?=$html->link('Stavy objednávek', array('controller' => 'statuses', 'action' => 'index')) ?>
				<ul>
					<li><?=$html->link('zobrazit seznam stavů', array('controller' => 'statuses', 'action' => 'index')) ?></li>
					<li><?=$html->link('vytvořit nový stav', array('controller' => 'statuses', 'action' => 'add')) ?></li>
				</ul>
			</li>
			<li><?=$html->link('Emailové šablony', array('controller' => 'mail_templates', 'action' => 'index')) ?>
				<ul>
					<li><?=$html->link('zobrazit seznam šablon', array('controller' => 'mail_templates', 'action' => 'index')) ?></li>
					<li><?=$html->link('vytvořit novou šablonu', array('controller' => 'mail_templates', 'action' => 'add')) ?></li>
				</ul>
			</li>
			<li><?=$html->link('Způsoby platby', array('controller' => 'payments', 'action' => 'index')) ?>
				<ul>
					<li><?=$html->link('zobrazit seznam způsobů platby', array('controller' => 'payments', 'action' => 'index')) ?></li>
					<li><?=$html->link('vytvořit nový způsob platby', array('controller' => 'payments', 'action' => 'add')) ?></li>
				</ul>
			</li>
			<li><?=$html->link('Způsoby dopravy', array('controller' => 'shippings', 'action' => 'index')) ?>
				<ul>
					<li><?=$html->link('zobrazit seznam způsobů dopravy', array('controller' => 'shippings', 'action' => 'index')) ?></li>
					<li><?=$html->link('vytvořit nový způsob dopravy', array('controller' => 'shippings', 'action' => 'add')) ?></li>
				</ul>
			</li>
		</ul>
	</li>
	<li><?=$html->link('Odhlásit', array('controller' => 'administrators', 'action' => 'logout')) ?>
</ul>
<div class="clearer"></div>