<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8" />
	<meta http-equiv="content-language" content="cs" />
	<title>LEKARNY.LEKARNA-OBZOR.CZ - administrace</title>
	<?=$html->css('admin') ?>
	<?=$html->css('admin_print', 'stylesheet', array('media' => 'print')) ?>
</head>

<body>
	<div id="body_wrapper">
		<div id="main_container">
			<div class="menu">
				<?php echo $this->renderElement('reps_menu') ?>
				<?php echo $form->create('Company', array('url' => array('rep' => true, 'controller' => 'companies', 'action' => 'search'), 'style' => 'float:left; padding-left:15px')) ?>
				<?php echo $form->input('Company.query', array('label' => false, 'div' => false)) ?>
				<?php echo $form->submit('Hledat', array('div' => false)) ?>
				<?php echo $form->end() ?>
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
</body>
</html>