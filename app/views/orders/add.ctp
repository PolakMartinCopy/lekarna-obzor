<div class="mainContentWrapper">
<?
	if ( !$session->check('Customer') ){
?>
<p><strong>Jste-li již našim zákazníkem</strong>, přihlašte se prosím pomocí formuláře v záhlaví stranky,<br />
nebo použijte <a href="/customers/login">příhlašovací formulář</a>.</p>
<?
	}
?>

<?=$form->Create('Order', array('url' => '/orders/add', 'id' => 'orderForm'))?>
<?if ( !$session->check('Customer.id') ){ ?>
	<fieldset>
		<legend>Adresa doručení</legend>
		<table id="orderForm">
			<tr>
				<th><sup>*</sup>Jméno</th>
				<td><?=$form->input('Customer.first_name', array('label' => false, 'div' => false))?></td>
			</tr>
			<tr>
				<th><sup>*</sup>Příjmení</th>
				<td><?=$form->input('Customer.last_name', array('label' => false, 'div' => false))?></td>
			</tr>	
			<tr>
				<th><sup>*</sup>Ulice</th>
				<td><?=$form->input('Address.street', array('label' => false, 'div' => false))?></td>
			</tr>	
			<tr>
				<th><sup>*</sup>Číslo popisné</th>
				<td><?=$form->input('Address.street_no', array('label' => false, 'div' => false))?></td>
			</tr>	
			<tr>
				<th><sup>*</sup>PSČ</th>
				<td><?=$form->input('Address.zip', array('label' => false, 'div' => false))?></td>
			</tr>	
			<tr>
				<th><sup>*</sup>Město</th>
				<td><?=$form->input('Address.city', array('label' => false, 'div' => false))?></td>
			</tr>
			<tr>
				<th><sup>*</sup>Stát</th>
				<td><?=$form->select('Address.state', array('Česká Republika' => 'Česká Republika'), null, array('empty' => false)) ?></td>
			</tr>
			<tr>
				<td colspan="2">
					<span style="font-size:10px;">Chcete-li vyplnit odlišnou fakturační adresu, můžete tak učinit v dalším kroku objednávky.</span>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<th><sup>*</sup>Kontaktní telefon</th>
				<td><?=$form->input('Customer.phone', array('label' => false, 'div' => false))?></td>
			</tr>
			<tr>
				<th><sup>*</sup>Emailová adresa</th>
				<td><?=$form->input('Customer.email', array('label' => false, 'div' => false))?></td>
			</tr>

		</table>
	</fieldset>

	<fieldset>
		<legend>Fakturační údaje</legend>
		<table id="orderForm">
			<tr>
				<td colspan="2">
					<span style="font-size:10px;">Vyplňte pouze jste-li podnikající fyzická osoba, nebo zástupce právnické osoby.</span>
				</td>
			</tr>
			<tr>
				<th>Název společnosti</th>
				<td><?=$form->input('Customer.company_name', array('label' => false, 'div' => false))?></td>
			</tr>
			<tr>
				<th>IČO</th>
				<td><?=$form->input('Customer.company_ico', array('label' => false, 'div' => false))?></td>
			</tr>	
			<tr>
				<th>DIČ</th>
				<td><?=$form->input('Customer.company_dic', array('label' => false, 'div' => false))?></td>
			</tr>
		</table>
	</fieldset>
<?}?>
	<fieldset>
		<legend>Detaily objednávky</legend>
		<table id="orderForm">
			<tr>
				<th>Způsob doručení<sup>*</sup></th>
				<td>
					<?
						if ( !isset($this->data['Order']['shipping_id']) ){
							$this->data['Order']['shipping_id'] = null;
						}
						echo $form->select('Order.shipping_id', $shipping_choices, $this->data['Order']['shipping_id'], array('empty' => false));
					?>
				</td>
			</tr>
			<tr>
				<th>Váš komentář k objednávce</th>
				<td><?=$form->textarea('Order.comments', array('cols' => 40, 'rows' => 5))?></td>
			</tr>
		</table>
	</fieldset>
		
	<table id="orderForm">
		<tr>
			<th>&nbsp;</th>
			<td><?=$form->Submit('Rekapitulace objednávky');?></td>
		</tr>
	</table>
<?=$form->end()?>

</div>