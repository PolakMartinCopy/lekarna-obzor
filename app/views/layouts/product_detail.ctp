<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->element('default_head')?>
	<script type="text/javascript" src="/js/comment_form_management.js"></script>
	<script type="text/javascript" src="/js/recommend_form_management.js"></script>
	
	<script type="text/javascript" src="http://www.google.com/recaptcha/api/js/recaptcha_ajax.js"></script>
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
		
		<?php
			if ($this->Session->check('Message.flash')) {
				echo $this->Session->flash();
			}
			echo $content_for_layout;
		?>
		
		<div id="sidebar">
			<?php echo $this->element('categories_menu', $categories_menu)?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('advantages')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('sukl_overeni')?>
			<div class="menu_spacer"></div>
			<?php echo $this->element('facebook')?>
			
		</div>
		<div class="menu_spacer"></div>
		<?php echo $this->element('footer')?>
	</div>
</body>
<?php echo $this->element('heureka_overeno_zakazniky')?>
</html>