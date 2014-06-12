<div id="header_right">
	<div class="button prihlaseni active"><a href="/users/companies/login" id="loginLink">Přihlášení</a></div>
	<div id="menu_login">
<?
	if ( $session->check('Company') ){
		// je prihlaseny
		$company = $session->read('Company');
?>
		<p><strong>Jste přihlášen(a) jako:</strong> <?php echo $company['person_first_name'] ?> <?php echo $company['person_last_name'] ?><br/>
		<strong>Společnost:</strong> <?php echo $company['name'] ?><br/>
		<?php echo $html->link('odhlásit se', array('users' => true, 'controller' => 'companies', 'action' => 'logout')) ?></p>
<?php 
		if ( $session->check('Cart') ){
			$cart_stats = $this->requestAction('/carts/get_stats');
?>
		<div id="actual_order_stats">
			Rozpracovaná objednávka: <span class="login_notify"><?=$session->read('Cart.name') ?></span><br />
			Produktů: <?=$cart_stats['products_quantity']?>, Cena celkem: <?=$cart_stats['price']?> Kč bez DPH<br />
			<?=$html->link('zobrazit', array('users' => true, 'controller' => 'carts', 'action' => 'view')) ?>
		</div>
<?
		}
	} else {
		echo $form->create('Customer', array('url' => array('users' => true, 'controller' => 'companies', 'action' => 'login'), 'id' => 'login_form_top', 'encoding' => false)); ?>
	<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td><?php
				echo $form->input('Company.login', array('label' => false, 'class' => 'text_box', 'div' => false, 'id' => 'loginUsername', 'value' => 'Login'));
				echo $form->input('Company.password', array('label' => false, 'class' => 'text_box', 'div' => false, 'id' => 'loginPassword', 'value' => 'Heslo'));
				echo $form->hidden('Company.backtrace_url', array('value' => $_SERVER['REQUEST_URI']));
				echo $form->submit('OK', array('class' => 'submit', 'div' => false));
				?>
			</td>
		</tr>
	</table>
<?php echo $form->end() ?>
<?php } ?>
	</div>
</div>