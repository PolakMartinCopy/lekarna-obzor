<script type="text/javascript" src="/plugins/fancybox/jquery.fancybox-1.3.2.pack.js"></script>
<script type="text/javascript" src="/plugins/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="/plugins/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
<link rel="stylesheet" href="/plugins/fancybox/jquery.fancybox-1.3.2.css" type="text/css" media="screen" />

<div style="display:none">
	<form id="new_product_form">
		<p id="login_error" style="display:none">Zadejte název produktu.</p>
		<?php echo $this->element('product_variants/add_edit_form')?>
		<input type="submit" value="Uložit" />
	</form>
</div>