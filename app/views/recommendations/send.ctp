<h1>Doporučte nás</h1>
<p>Vyplňte prosím všechna pole označená <sup>*</sup> a kontrolní pole.</p>
<?php echo $this->Form->create('Recommendation', array('url' => array('controller' => 'recommendations', 'action' => 'send')))?>
<table cellspacing="0" cellpadding="0" border"0">
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
			echo $this->Form->hidden('Recommendation.request_uri', array('id' => 'RecommendationRequestUriProduct'));
			?>
			<div class="formErrors"></div>
		</td>
	</tr>
</table>
<?php 
	require_once 'recaptchalib.php';
  	$publickey = "6LdMatsSAAAAAIq9qS9oC_fOWb7hCwFcyoYQcYSc"; // you got this from the signup page
  	echo recaptcha_get_html($publickey);
	echo $this->Form->submit('ODESLAT');
	echo $this->Form->end();
?>