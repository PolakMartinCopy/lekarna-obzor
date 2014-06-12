<div class="boxWidthWrapper">
	<ul id="nav">
		<li><a class="first boxTop eshopBack" href="/users/companies/index">Uživatelské menu</a></li>
		<li>
			<ul>
<?
	if ( $session->check('Company') ){
?>
				<li><a<?=( $_SERVER['REQUEST_URI'] == '/users/companies/index' ? ' class="activeItem"' : ' class="mainLink"' ) ?> href="/users/companies/index">úvodní stránka</a></li>
				<li><a<?=( $_SERVER['REQUEST_URI'] == '/users/companies/edit' ? ' class="activeItem"' : '' ) ?> href="/users/companies/edit">upravit údaje o společnosti</a></li>
				<li><a<?=( $_SERVER['REQUEST_URI'] == '/users/companies/access_change' ? ' class="activeItem"' : '' ) ?> href="/users/companies/access_change">změna loginu a hesla</a></li>
			</ul>
		</li>
		<li><a<?=( $_SERVER['REQUEST_URI'] == '/users/orders/index' ? ' class="activeItem"' : ' class="mainLink"' ) ?> href="/users/orders/index">Objednávky</a>
			<ul>
				<li>
					<a<?=( $_SERVER['REQUEST_URI'] == '/users/orders/index' ? ' class="activeItem"' : '' ) ?> href="/users/orders/index">dokončené (<?=$this->requestAction('/orders/count/' . $session->read('Company.id')) ?>)</a>
				</li>
				<li>
					<a<?=( $_SERVER['REQUEST_URI'] == '/users/carts/index' ? ' class="activeItem"' : '' ) ?> href="/users/carts/index">rozpracované (<?=$this->requestAction('/carts/count/' . $session->read('Company.id')) ?>)</a>
				</li>
				<li><a<?=( $_SERVER['REQUEST_URI'] == '/users/carts/add' ? ' class="activeItem"' : '' ) ?> href="/users/carts/add">vytvořit novou obj.</a></li>			
			</ul>
		</li>
<?
	} else {
?>
		<li><a<?=( $_SERVER['REQUEST_URI'] == '/users/companies/login' ? ' class="activeItem"' : '' ) ?> href="/users/companies/login">přihlásit se</a></li>
		<li><a<?=( $_SERVER['REQUEST_URI'] == '/companies/add' ? ' class="activeItem"' : '' ) ?> href="/companies/add">zaregistrovat se</a></li>
<?
	}
?>
			</ul>
		</li>
	</ul>
</div>
<div style="clear:both;"></div>