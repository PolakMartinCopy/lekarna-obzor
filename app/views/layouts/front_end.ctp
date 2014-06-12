<?php
if (!isset($title_for_content) || empty($title_for_content)) {
	$title_for_content = 'Online lékárna';
}

if (!isset($description_for_content) || empty($description_for_content)) {
	$description_for_content = '{Lékárna-Obzor.cz} - Online lékárna';
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns='http://www.w3.org/1999/xhtml' xml:lang='cs'>
<head>
<title><?php echo $title_for_content?> - Lékarna-obzor.cz</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta http-equiv="Cache-Control" content="no-cache" />
<meta http-equiv='Pragma' content='no-cache' />
<meta http-equiv="Expires" content="-1" />
<meta http-equiv="Content-Language" content="cs" />
<meta name="description" content="<?php echo $description_for_content?>" />
<meta name="keywords"
	content="online lékarna, vitamíny, minerály, doplňky stravy" />
<meta name="robots" content="all,follow" />
<meta name="distribution" content="global" />
<meta name="resource-type" content="document" />
<link rel="stylesheet" type="text/css" href="/css/styl.css" media="screen,projection" />
</head>
<body>
<div id="obal">
<div id="top"><a href="/" title="lekarna-obzor.cz" id="loga"></a>
<?php
echo $this->element('horizontal_menu');
echo $this->element('info');
echo $this->element('cart_info_box', $carts_stats);
?>
</div>
<div id="maj">
<div id="obsah">
<div id="produkty">
<?php
if ($this->Session->check('Message.flash')) {
	echo $this->Session->flash();
}
echo $content_for_layout;
?>
</div>
<div id="leva">
<ul>
	<li><h3>Kategorie</h3></li>
	<?php echo $this->element('categories_menu', $categories_menu)?>
</ul>
</div>
</div>
<div id="prava">
<?php echo $this->Form->create('Search', array('url' => '/vyhledavani.htm'), array('id' => 'hledej'))?>
<fieldset><label>Zadejte frázi nebo klíčové slovo</label><br />
<?php echo $this->Form->input('Search.q', array('label' => false, 'type' => 'text', 'maxlength' => 100, 'size' => 18, 'div' => false))?>
<?php echo $this->Form->submit('OK', array('id' => 'searchsubmit', 'div' => false))?>
</fieldset>
<?php echo $this->Form->end()?>

<h3><a href="/aktuality.htm">Aktuality a články</a></h3>
<div id="aktuality">
<dl>
	<dd>
	<h4><a href="#">Aktualita</a></h4>
	<p>Text aktuality</p>
	</dd>
</dl>
<a href="/aktuality.htm" id="vice">více aktualit ...</a></div>
<?php
if (isset($newest)) {
	echo $this->element('newest', $newest);
}
if (isset($most_sold)) {
	echo $this->element('most_sold', $most_sold);
} ?>
</div>
</div>
<div id="pata">
<?
	// echo file_get_contents ('http://www.lekarna-obzor.cz/__linker/uploader.php?layout_name=lekarna-obzor_footer&ru=' . base64_encode($_SERVER['REQUEST_URI']));
?>
</div>
</div>
<script src="http://www.google-analytics.com/urchin.js"
	type="text/javascript"></script>
<script type="text/javascript">_uacct = "UA-6185912-8"; urchinTracker();</script>
</body>
</html>
<?php echo $this->element('sql_dump')?>