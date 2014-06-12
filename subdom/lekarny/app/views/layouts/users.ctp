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
			<?php echo $this->element('users_login_box')?>
		</div>
		<div class="menu_spacer"></div>
		<?php echo $this->element('horizontal_menu')?>
		<?php // echo $this->element('search_box')?>
		<div id="content" class="right">
			<?php
			if ($session->check('Message.flash')) {
				$session->flash();
			}
			echo $content_for_layout;
			?>
		</div>
		
		<div id="sidebar">
			<div class="menu_spacer"></div>
			<?php echo $this->renderElement('users_left_box'); ?>
			<div class="menu_spacer"></div>
			<?php echo $this->renderElement('users_categories_list'); ?>
			<div class="menu_spacer"></div>
			<?php echo $this->renderElement('users_price_list'); ?>
		</div>
		<div class="menu_spacer"></div>
		<?php echo $this->element('footer')?>
	</div>
</body>
</html>