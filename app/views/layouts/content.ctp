<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->element('default_head')?>
	
	<?php if (isset($layout)) { 
		$layout->output($subheader_for_layout);
	} ?>
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
		<?php echo $this->element('breadcrumbs')?>
		<div class="menu_spacer"></div>
		<?php echo $this->element('search_box')?>
		<div id="content" class="right">
			<?php
			if ($this->Session->check('Message.flash')) {
				echo $this->Session->flash();
			}
			echo $content_for_layout;
			?>
		</div>
		
		<div id="sidebar">
			<div class="menu_spacer"></div>
			<?php echo $this->element('categories_menu', $categories_menu)?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('advantages')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('sukl_overeni')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('facebook')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('most_sold')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('sukl_link')?>
		</div>
		<div class="menu_spacer"></div>
		<?php echo $this->element('footer')?>
	</div>
</body>
<?php echo $this->element('heureka_overeno_zakazniky')?>
</html>
<?php echo $this->element('sql_dump')?>