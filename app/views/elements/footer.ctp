<div id="footer_wrapper">
	<div class="footer_area narrow">
		<div class="header">INFORMACE</div>
		<ul>
			<li><a href="/jak-nakupovat">Jak nakupovat?</a></li>
			<li><a href="/kontakty">Kontaktní a reklamační údaje</a></li>
			<li><a href="/cenik-dopravy">Ceník dopravy</a></li>
		</ul>
	</div>
	<div class="footer_area wide">
		<div class="header" id="recommendation">DOPORUČTE ZNÁMÉMU</div>
		<p>Zadejte emailovou adresu,<br />kam máme poslat odkaz:</p>
		<?php echo $this->Form->create('Recommendation', array('url' => array('controller' => 'recommendations', 'action' => 'send'), 'encoding' => false)); ?>
		<table cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><?php
				echo $this->Form->input('Recommendation.target_email', array('label' => false, 'div' => false, 'class' => 'text_box_suggest', 'value' => 'emailová adresa', 'after' => '&nbsp;', 'error' => false));
				echo $this->Form->hidden('Recommendation.request_uri', array('value' => $_SERVER['REQUEST_URI']));
				echo $this->Form->submit('ODESLAT', array('class' => 'submit_suggest', 'div' => false));
				?></td>
			</tr>
		</table>
		<?php echo $this->Form->end();?>
	</div>
	<div class="footer_area narrow">
		<div class="header">PROVOZOVATEL</div>
		<ul>
			<li>Pharmacorp CZ s.r.o.</li>
			<li>IČ: 29372828</li>
			<li>DIČ: CZ29372828</li>
			<li>tel.: 601 351 448</li>
			<li>tel.: 533 101 360</li>
		</ul>
	</div>
	<div class="footer_area wide">
		<div class="header" id="subscription">NEWSLETTER</div>
		<p>Odebírejte naše novinky emailem:<br />&nbsp;</p>
		<?php echo $this->Form->create('Subscriber', array('url' => array('controller' => 'subscribers', 'action' => 'add'), 'encoding' => false)); ?>
		<table cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td><?php
				echo $this->Form->input('Subscriber.email', array('label' => false, 'div' => false, 'class' => 'text_box_newsletter', 'value' => 'emailová adresa', 'after' => '&nbsp;', 'error' => false));
				echo $this->Form->hidden('Subscriber.request_uri', array('value' => $_SERVER['REQUEST_URI']));
				echo $this->Form->submit('ODEBÍRAT', array('class' => 'submit_newsletter', 'div' => false));
				echo $this->Form->error('Subscriber.email');
				?></td>
			</tr>
		</table>
		<?php echo $this->Form->end();?>
	</div>
	<div class="menu_spacer"></div>
<?php if ($this->params['controller'] != 'orders' && $this->params['action'] != 'finished') { ?>
 		<script type="text/javascript" src="/js/ga-add.js"></script>
<?php } ?>
</div>