<div id="product_detail">
	<?php if (!empty($product['Image'])) { ?>
	<div id="main_image" style="height:270px;width:270px;">
		<a href="/product-images/<?php echo $product['Image'][0]['name']?>" title="<?php echo $product['Product']['name'] ?>" class="thickbox">
			<img src="/product-images/medium/<?php echo $product['Image'][0]['name']?>" width="270px" height="270px" alt="produkt <?php echo $product['Product']['name'] ?>" />
		</a>
	</div>
	<?php } ?>
	<div style="border: 5px solid #A5CF4E;padding:5px;font-size:14px;width:641px;margin-top:5px;float:left">
		<p>Dovolujeme si upozornit naše zákazníky, že objednávky zpracované od 12.00 hod. 23.12.2014 budou expedovány 2.1.2015. Děkujeme za pochopení.</p>
	</div>

	<h1><?php echo $product['Product']['heading']?></h1>
	
	<div id="basic_info">
		<p class="manufacturer">Výrobce: <?php echo $product['Manufacturer']['name']?></p>
		<?php
		$product['Product']['note'] = trim($product['Product']['note']);
		if (!empty($product['Product']['note'])) { ?>
		<p class="note"><span class="header">Poznámka: </span><?php echo $product['Product']['note']?></p>
		<?php } ?>
		<p class="availability"><span class="header">Dostupnost:</span> <?php echo $product['Availability']['name']?><?php if ($product['Availability']['cart_allowed']) { ?> (<a href="/cenik-dopravy">kdy zboží dostanu</a>)<?php  } ?></p>
		<p class="price"><span class="header">Cena:</span> <span class="nominal"><?php echo round($product['Product']['discount_price'])?>,-</span></p>
		<?php // form pro vlozeni do kosiku
		if ($product['Availability']['cart_allowed']) {
			echo $this->Form->create('Product', array('url' => '/' . $product['Product']['url'], 'id' => 'cart', 'encoding' => false));
			echo $this->element('subproducts_choices', $this->requestAction('/subproducts/control/' . $product['Product']['id']));
		?>
		<table cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><?php
				echo $this->Form->input('Subproduct.quantity', array('type' => 'text', 'value' => 1, 'label' => false, 'div' => false, 'class' => 'text_box_quantity', 'after' => '&nbsp;'));
				echo $this->Form->submit('Koupit produkt', array('class' => 'submit_cart', 'div' => false));
				echo $this->Form->hidden('Product.id', array('value' => $product['Product']['id']));
				?></td>
			</tr>
		</table>
		<?php echo $this->Form->end();
		} ?>
		
		<!-- prostor pro facebook LIKE -->
		<!-- prostor pro Google PLUS -->
	</div>
	<div id="contact_info">
		<p>
			<span class="bolder">601 351 448</span><br />
			Volejte mezi 08:00 - 17:30 ve všední dny
		</p>
		<p>
			<span class="bolder">eshop@pharmacorp.cz</span><br />
			Na emaily reagujeme zpravidla do 24 hodin
		</p>
		<ul>
			<li><?php echo $this->Html->link('Chci se zeptat na produkt', '#diskuze', array('id' => 'askProductLink')); ?></li>
			<li>
				<a href="#recommendation" id="recommendationLink">Poslat produkt známému</a>
				<div style="display:none">
					<?php echo $this->Form->create('Recommendation', array('url' => array('controller' => 'recommendations', 'action' => 'send'), 'id' => 'RecommendationFormProduct', 'encoding' => false))?>
					<table cellspacing="0" cellpadding="0" border="0">
						<tr>
							<th>Vaše jméno</th>
							<td><?php echo $this->Form->input('Recommendation.source_name', array('label' => false, 'type' => 'text', 'size' => 50))?></td>
						</tr>
						<tr>
							<th>Váš email<sup>*</sup></th>
							<td>
								<?php echo $this->Form->input('Recommendation.source_email', array('label' => false, 'type' => 'text', 'size' => 50))?>
								<div class="formErrors"></div>
							</td>
						</tr>
						<tr>
							<th>Email adresáta<sup>*</sup></th>
							<td>
								<?php
								echo $this->Form->input('Recommendation.target_email', array('label' => false, 'type' => 'text', 'size' => 50));
								echo $this->Form->hidden('Recommendation.request_uri', array('value' => $_SERVER['REQUEST_URI'], 'id' => 'RecommendationRequestUriProduct'));
								?>
								<div class="formErrors"></div>
							</td>
						</tr>
					</table>
					<?php 
						require_once 'recaptchalib.php';
					  	$publickey = "6LdMatsSAAAAAIq9qS9oC_fOWb7hCwFcyoYQcYSc"; // you got this from the signup page
					  	echo recaptcha_get_html($publickey);
					?>
					<div id="RecommendationRecaptchaErrorProduct" class="formErrors"></div>
					<?php 
						echo $this->Form->submit('ODESLAT');
						echo $this->Form->end();
					?>
				</div>
			</li>
		</ul>
	</div>
</div>
<div class="menu_spacer"></div>
<div id="content" class="right">
	<a name="diskuze"></a>
	<div id="button_details" class="button_active">DETAILNÍ POPIS PRODUKTU</div>
	<div id="button_comments" class="button_noactive">KOMENTÁŘE / DOTAZY</div>
	<div id="product_details_wrapper">
<?php // vypisuju podminene atributy - pokud obsahuji hodnotu, vypisu je
	if ($product['Product']['product_type_id'] || $product['Product']['code'] || $product['Product']['ean'] || $product['Product']['sukl'] || $product['Product']['group']) { ?>
		<ul>
	<?php if ($product['Product']['product_type_id']) { ?>
			<li>Skupina: <?php echo $product['ProductType']['name']?></li>
	<?php } ?>
	<?php if ($product['Product']['ean']) { ?>
			<li>EAN: <?php echo $product['Product']['ean']?></li>
	<?php } ?>
	<?php if ($product['Product']['code']) { ?>
			<li>Kód zboží: <?php echo $product['Product']['code']?></li>
	<?php } ?>
	<?php if ($product['Product']['sukl']) { ?>
			<li>Kód SÚKL: <?php echo $product['Product']['sukl']?></li>
	<?php } ?>
	<?php if ($product['Product']['group']) { ?>
			<li>Farmakoterapeutická skupina: <?php echo $product['Product']['group']?></li>
	<?php } ?>
		</ul>
<?php } ?>
		<p><?php echo $product['Product']['description']?></p>
	</div>
	<div id="product_comments_wrapper">
		<?php echo $this->Form->create('Comment', array('url' => array('controller' => 'comments', 'action' => 'add'), 'id' => 'CommentAddForm', 'encoding' => false))?>
		<table border="0">
			<tr>
				<th>Jméno:</th>
				<td><?php echo $this->Form->input('Comment.author', array('label' => false, 'size' => 50))?><div class="formErrors"></div></td>
			</tr>
			<tr>
				<th>Email:</th>
				<td><?php echo $this->Form->input('Comment.email', array('label' => false, 'size' => 50))?><div class="formErrors"></div></td>
			</tr>
			<tr>
				<th>Předmět:</th>
				<td><?php echo $this->Form->input('Comment.subject', array('label' => false, 'size' => 50))?><div class="formErrors"></div></td>
			</tr>
			<tr>
				<th>Dotaz</th>
				<td>
					<?php echo $this->Form->input('Comment.body', array('label' => false, 'cols' => 63, 'rows' => 10))?><div class="formErrors"></div>
					<?php echo $this->Form->hidden('Comment.product_id', array('value' => $product['Product']['id']));
					echo $this->Form->hidden('Comment.request_uri', array('value' => $_SERVER['REQUEST_URI'])); ?>
				</td>
			</tr>
		</table>
		<?php 
			echo $this->Form->submit('Odeslat dotaz');
			echo $this->Form->end();
		?>
		
		<?php if (empty($product['Comment'])) { ?>
		<p>Diskuze neobsahuje žádné komentáře pro tento produkt.</p>
		<?php } else { ?>
		<div style="margin-top:10px;">
		<?php foreach ($product['Comment'] as $comment) { ?>
			<div style="background-color:silver;padding:3px;">
				<p><strong><?php echo $comment['subject']?></strong> od <strong><?php echo $comment['author']?></strong> ze dne <em><?php echo cz_date_time($comment['created'])?></em></p>
			</div>
			<?php echo $comment['body']?>
			<?php if (!empty($comment['reply'])) { ?>
				<div style="margin-top:5px;padding-left:5px;margin-left:15px;border-left:1px solid black;">
					<p><?php echo $comment['reply']?></p>
					<p>za <em><?php echo CUST_NAME?></em><br /><?php echo $comment['Administrator']['first_name']?> <?php echo $comment['Administrator']['last_name']?></p>
				</div>
			<?php } ?>
		<?php } ?>
		</div>
		<?php } ?>
	</div>
</div>