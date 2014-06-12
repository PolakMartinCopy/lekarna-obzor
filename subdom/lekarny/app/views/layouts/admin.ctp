<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-language" content="cs" />
	<title>LEKARNY.LEKARNA-OBZOR.CZ - administrace</title>
	
	<?php echo $html->css('jquery_ui_tabs/jquery-ui-1.8.14.custom')?>
	
	<script type="text/javascript" src="/js/jquery_ui_tabs/jquery-1.5.1.js"></script>
	<script type="text/javascript" src="/js/jquery_ui_tabs/jquery.ui.core.js"></script>
	<script type="text/javascript" src="/js/jquery_ui_tabs/jquery.ui.widget.js"></script>
	<script type="text/javascript" src="/js/jquery_ui_tabs/jquery.ui.tabs.js"></script>
	<script type="text/javascript" src="/js/jquery_ui_tabs/jquery.cookie.js"></script>
	
	<script>
	$(function() {
		$( "#tabs" ).tabs({ cookie: {expires: 30} });
	});
	</script>
	
	<?=$html->css('admin') ?>
	<?=$html->css('admin_print', 'stylesheet', array('media' => 'print')) ?>
  	<script>
function mainmenu(){
	$(" #nav ul ").css({display: "none"}); // Opera Fix
	$(" #nav li").hover(function(){
		$(this).find('ul:first').css({visibility: "visible",display: "none"}).show(0);
		},function(){
		$(this).find('ul:first').css({visibility: "hidden"});
		});
}

$(document).ready(function(){
	mainmenu();
});
	</script>
</head>

<body>
	<div id="body_wrapper">
		<div id="main_container">
			<div class="menu">
				<?=$this->renderElement('administrators_menu') ?>
			</div>

			<div class="content">
				<?
					if ($session->check('Message.flash')){
						$session->flash();
					}
					echo $content_for_layout
				?>
			</div>
		</div>
	</div>
	<div class="cleaner"></div>
</body>
</html>