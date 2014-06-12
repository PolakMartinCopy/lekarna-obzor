<div id="header_right">
	<div class="button kosik active"><a href="/kosik" id="cartLink">Nákupní košík</a></div>
	<div class="button prihlaseni"><a href="/customers/login" id="loginLink">Přihlášení</a></div>
	<div class="button o_lekarne"><a href="/vse-o-nakupu" id="aboutLink">Vše o nákupu</a></div>

	<div id="menu_cart">
		<?php if ($carts_stats['products_count']) { ?>
		<p id="cart_stats">Celková cena: <span class="price"><?php echo $carts_stats['total_price']?> Kč</span></p>
		<?php } else { ?>
		<p id="cart_stats">Košík je prázdný.</p>
		<?php } ?>
		<p id="cart_links"><a class="dump" href="/vysypat-kosik" id="dump">Vysypat košík</a><a class="to_cart" href="/kosik">Zobrazit košík</a><a class="to_order" href="/orders/add">Objednat</a></p>
	</div>
	<div id="menu_login">
	<?php
		if (!$this->Session->check('Customer')) {
			echo $this->Form->create('Customer', array('url' => array('controller' => 'customers', 'action' => 'login'), 'id' => 'login_form_top', 'encoding' => false)); ?>
	<table cellspacing="0" cellpadding="0" border="0">
		<tr>
			<td><?php
				echo $this->Form->input('Customer.login', array('label' => false, 'class' => 'text_box', 'div' => false, 'id' => 'loginUsername', 'value' => 'Login'));
				echo $this->Form->input('Customer.password', array('label' => false, 'class' => 'text_box', 'div' => false, 'id' => 'loginPassword', 'value' => 'Heslo'));
				echo $this->Form->hidden('Customer.backtrace_url', array('value' => $_SERVER['REQUEST_URI']));
				echo $this->Form->submit('OK', array('class' => 'submit', 'div' => false));
				?>
			</td>
		</tr>
	</table>
	<?php echo $this->Form->end() ?>
		<p class="text_links"><a href="/obnova-hesla">Zapomenuté heslo</a> | <a href="/registrace">Nová registrace</a></p>
		<?php } else { 
			$customer = $this->Session->read('Customer'); ?>
			
			<p class="text_links" style="padding-top:24px">Jste přihlášen jako <strong><?php echo $customer['first_name']?> <?php echo $customer['last_name']?></strong>.</p>
			<p class="text_links"><a href="/customers">Můj účet</a> | <a href="/customers/logout">Odhlásit</a></p>			
		<?php }?>
	</div>
	<div id="menu_about">
		<ul class="text_links">
			<li><a href="/jak-nakupovat">Jak nakupovat</a></li>
			<li><a href="/cenik-dopravy">Způsoby a ceny dopravy</a></li>
			<li><a href="/osobni-odber">Osobní odběr</a></li>
			<li><a href="/obchodni-podminky">Obchodní podmínky</a></li>
		</ul>
		<ul class="text_links">
			<li><a href="/o-provozovateli">Informace o provozovateli</a></li>
			<li><a href="/prodejna">Naše prodejna</a></li>
			<li><a href="/kontakty">Jak nás kontaktovat</a></li>
		</ul>
	</div>
</div>