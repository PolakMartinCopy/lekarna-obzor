<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="cs" />
	<title>Systém pro sledování kurzů</title>
	<link rel="stylesheet" type="text/css" href="/css/admin.css" />
	<link rel="stylesheet" href="/css/jquery-ui/jquery.ui.all.css">
	<script src="/js/jquery-1.9.0.js"></script>
	<script src="/js/ui/jquery.ui.core.js"></script>
	<script src="/js/ui/jquery.ui.widget.js"></script>
	<script src="/js/ui/jquery.ui.datepicker.js"></script>
	<script src="/js/ui/jquery.ui.datepicker-cs.js"></script>
</head>

<body>
	<div id="content_container_center">
		<div id="top_lista_left">Systém pro sledování kurzů</div>
		<div id="top_lista_right">
			<?php if (!empty($administrator)) {?>
			Jste přihlášen(a) jako: <em><?php echo $administrator['Administrator']['name'] ?> | <?php echo $html->link('odhlásit se', array('controller' => 'administrators', 'action' => 'logout'))?></em>
			<?php } else { ?>
			Nejste přihlášen(a), <?php echo $html->link('přihlašte se', array('controller' => 'administrators', 'action' => 'login'))?>
			<?php } ?>
		</div>
		<div class="clearer"></div>
		<div id="container">
			<div id="content">
				<div id="leftContent">
					<?php echo $this->element('left_menu'); ?>
				</div>
				<div id="rightContent">
				<?php echo $this->Session->flash('auth'); ?>
				<?php echo $this->Session->flash(); ?>
	
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