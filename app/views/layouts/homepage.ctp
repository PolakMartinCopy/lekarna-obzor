<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->element('default_head')?>

	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>
    <script type="text/javascript">
	var map;
    function initialize() {
		var point = new google.maps.LatLng(49.2284742, 16.6180531)
          
        var mapOptions = {
			zoom: 16,
			center: point,
			mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('map'), mapOptions);

		var marker = new google.maps.Marker({
			position: point,
			map: map,
			title: 'Lékárna Obzor'
        });
	}

    google.maps.event.addDomListener(window, 'load', initialize);
    </script>
</head>

<body>
	<div id="total_wrapper">
		<div id="header_wrapper">
			<div id="header_left"></div>
			<?php echo $this->element('login_box')?>
		</div>
		<div class="menu_spacer"></div>
		<?php echo $this->element('horizontal_menu')?>
		<div class="menu_spacer"></div>
		<div id="content" class="left">
			<?php echo $this->element('search_box')?>
			<div class="menu_spacer"></div>
			<div style="border: 5px solid #A5CF4E;padding:5px;font-size:14px">
				<p>Dovolujeme si upozornit naše zákazníky, že objednávky zpracované od 12.00 hod. 23.12.2014 budou expedovány 2.1.2015. Děkujeme za pochopení.</p>
			</div>
			<div class="menu_spacer"></div>
			<?php
			if ($this->Session->check('Message.flash')) {
				echo $this->Session->flash();
			}
			?>
			<?php if (!empty($content_for_layout)) { ?>
			<div id="hp_content">
				<?php echo $content_for_layout; ?>
			</div>
			<?php } ?>
			<div id="slides"></div>
			<div class="menu_spacer"></div>
			<div id="button_categories">KATEGORIE</div>
			<div id="categories_wrapper">
				<?php
				$i = 0;
				// pole s nazvem css tridy pro ikonu
				// @todo - preonacit do databaze a administrace...
				$icons_class = array(
					109 => 'vanoce',
					8 => 'chripka_kasel',
					9 => 'bolest',
					10 => 'klouby_svaly',
					11 => 'deti_maminky',
					12 => 'zdravotnicky_material',
					13 => 'kosmetika',
					14 => 'caje_diety',
					72 => 'veterinarni',
					78 => 'sportovni_vyziva',
					89 => 'homeopatika',
					93 => 'deti_maminky'
				);
				foreach ($hp_categories_list as $category) {
					$class = 'vitaminy';
					if ( isset( $icons_class[$category['Category']['id']] ) ){
						$class = $icons_class[$category['Category']['id']];
					}
				?>
				<div class="main_category<?php echo ($i % 3 == 0) ? ' first' : ''?><?php echo ($i < 3) ? ' top' : ''?>">
					<a class="top_cat" href="/<?php echo  $category["Category"]['url']?>"><?php echo $category['Category']['name']?></a>
					<?php if (!empty($category['children'])) { ?>
					<p class="<?php echo $class?>">
						<?php foreach ($category['children'] as $category_child) { ?>
						<a href="/<?php echo $category_child['Category']['url']?>"><?php echo $category_child['Category']['name']?></a>
						<?php } ?>
					</p>
					<?php } ?>
				</div>
				<?php $i++; 
				} ?>
				<div style="clear:both"></div>
			</div>
			<div class="menu_spacer"></div>
			<div id="navi_hours_wrapper">
				<div id="navigation">
					<div id="navi_header">KDE NÁS NAJDETE</div>
					<div id="map"></div>
				</div>
				<?php echo $this->element('opening_hours')?>
			</div>
		</div>
		<div id="sidebar">
			<?php echo $this->element('advantages')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('sukl')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('facebook')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('most_sold')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('newest')?>
		</div>
		<div class="menu_spacer"></div>
		<?php echo $this->element('footer')?>
	</div>
</body>
<?php echo $this->element('heureka_overeno_zakazniky')?>
</html>
<?php echo $this->element('sql_dump')?>