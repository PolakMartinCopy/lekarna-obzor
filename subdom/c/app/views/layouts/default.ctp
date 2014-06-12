<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>CRM</title>

	<link href="/favicon.ico" type="image/x-icon" rel="icon" />
	<link href="/favicon.ico" type="image/x-icon" rel="shortcut icon" />
	<link rel="stylesheet" type="text/css" href="/css/admin.css" />
	<link rel="stylesheet" type="text/css" href="/css/debug.css" />
	<link rel="stylesheet" type="text/css" href="/css/black-tie/jquery-ui-1.8.10.custom.css" />
	
	<script type="text/javascript" src="/js/jquery-1.5.1.min.js"></script>
	<script type="text/javascript" src="/js/jquery-ui-1.8.11.custom.min.js"></script>
	<script type="text/javascript" src="/js/jquery.ui.datepicker-cs.js"></script>
	<script type="text/javascript" src="/js/jquery.cookie.js"></script>
	
	<script type="text/javascript">
		function showloader() {
			document.getElementById('loading').style.display='';
			document.getElementById("darkLayer").style.display = "";
		}

		function hideloader() {
			document.getElementById('loading').style.display='none';
			document.getElementById("darkLayer").style.display = "none";
		} 
	</script>

	<script>
		$(function() {
			$( "#tabs" ).tabs({
				cookie : {
					expires : 1
				}
			});
		});
	</script>
</head>
<body onload="hideloader()">
	<div id="darkLayer" class="darkClass" style="display:none"></div>
	<div id="loading" style="position:absolute; width:100%; text-align:center; top:100px; display:none">
		<img src="/images/loading.gif" border=0 />
	</div>

	<div id="content_container_center">
		<div id="top_lista_left">CRM</div>
		<div id="top_lista_right">
			<?php if (!empty($logged_in_user)) {?>
			Jste přihlášen(a) jako: <em><?php echo $logged_in_user['User']['first_name'] . ' ' . $logged_in_user['User']['last_name']?> | <?php echo $html->link('odhlásit se', array('controller' => 'users', 'action' => 'logout'))?></em>
			<?php } else { ?>
			Nejste přihlášen(a), <?php echo $html->link('přihlašte se', array('controller' => 'users', 'action' => 'login'))?>
			<?php } ?>
		</div>
		<div class="clearer"></div>
		<?php
		if ($session->check('Auth.User')) {
			echo $this->element('admin_menu');
		} ?>

		<div id="container">
			<div id="content">
				<div id="leftContent">
					<?php echo $this->element('admin_left_menu'); ?>
				</div>
				<div id="rightContent">
				<img src="/images/loading.gif" id="loading" style="display:none"/>

				<?php echo $session->flash('auth'); ?>
				<?php echo $session->flash(); ?>
	
				<?php echo $content_for_layout; ?>
				</div>
				<div class="clearer"></div>
			</div>
			<div id="footer">
				<a href="http://www.cakephp.org/" target="_blank"><img src="/img/cake.power.gif" alt="CakePHP: the rapid development php framework" border="0" /></a>		</div>
		 </div>
		<?php echo $this->element('sql_dump')?>
	</div>
</body>
</html>