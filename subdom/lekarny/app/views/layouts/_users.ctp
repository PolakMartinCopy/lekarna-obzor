<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>objednávkový systém LEKARNY.LEKARNA-OBZOR.CZ</title>
	<meta name="description" content="Objednávkový systém společnosti Pharmacorp CZ s.r.o. pro lékárny." />
	<?=$html->css('users') ?>
	<script type="text/javascript" src="/js/users_functions.js"></script>
</head>

<body>

<div id="totalWrapper">


<div id="topWrapMte">
	<div id="top">
		<div id="mainImage"></div>
		<div id="logo">
			<a href="/" title="Domů"><img src="http://www.mte.cz/images/logor.jpg" width="139px" height="34px" alt="www.mte.cz" /></a>	
		</div>
		<div id="users_login_box">
			<?=$this->renderElement('users_login_box')?>
		</div>
	</div>
</div>

<div id="menuWrapMte">
	<div id="menu">
	</div>
</div>

<div id="breadcrumbWrapMte">
	<div id="breadcrumb">
	</div>
</div>

<div id="bodyContainerCenBack">
	<div id="bodyContainerTopBackMte">
		<div id="bodyContainerBotBack">
			<div id="contentWidth">
				<div id="leftMainSeparator">
					<div id="leftColContainer">
						<?
							echo $this->renderElement('users_left_box');
							echo $this->renderElement('users_categories_list');
							echo $this->renderElement('users_price_list');
						?>
						
					</div>
					<div id="mainContainer">
						<?
							if ($session->check('Message.flash')){
								$session->flash();
							}
							echo $content_for_layout;
						?>
					</div>
					<div style="clear:both;"></div>
				</div>
			</div>
		</div>
	</div>
</div>

	<div id="footer">
		<div id="footerWrapper">
			<ul id="address">
				<li>Hybešova 43</li>
				<li>602 00 Brno</li>
				<li>tel.: 543 432 400</li>
				<li>fax.: 543 432 405</li>
			</ul>
			<a id="kontaktyPiktogram" href="/kontakty-mte.htm">
 				<img src="http://www.mte.cz/images/kontakty-picto.jpg" width="66px" height="61px" alt="" />
			</a>
		</div>
	</div>
	
	
</div>
</body>
</html>