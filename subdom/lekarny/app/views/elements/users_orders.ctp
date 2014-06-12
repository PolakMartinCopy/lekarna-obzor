<?
	if ( $session->check('Company') ){
?>
<a class="picto" href="/users/orders/index"><img src="http://www.mte.cz/images/pictoMenu.gif" width="67px" height="60px" alt="" /></a>
<a class="first boxTop menuBack" href="/users/orders/index">Objednávky</a>
<div class="boxWidthWrapper">
	<ul class="boxMenu">
		<li><a<?=( $_SERVER['REQUEST_URI'] == '/users/orders/index' ? ' class="activeItem"' : ' class="mainLink"' ) ?> href="/users/orders/index">historie objednávek</a>
	</ul>
</div>
<div style="clear:both;"></div>
<?
	}
?>