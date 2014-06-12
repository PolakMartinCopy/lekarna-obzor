<h2>Editace dostupnosti</h2>
<?=$form->Create('Availability')?>
	<fieldset>
 		<legend>Dostupnost produktu</legend>
		<table class="leftHeading" cellpadding="5" cellspacing="3">
			<tr>
				<th>
					Název dostupnosti
				</th>
				<td>
					<?=$form->text('Availability.name')?>
					<?=$form->error('Availability.name')?>
				</td>
			</tr>
			<tr>

				<th>
					Barva dostupnosti (RGB)
				</th>
				<td>
					<?=$form->text('Availability.color')?>
				</td>
			</tr>
			<tr>

				<th>
					Povolit vložení do košíku?
				</th>
				<td>
					<?=$form->input('Availability.cart_allowed', array('label' => false))?>
				</td>
			</tr>
		</table>
	</fieldset>
<?
	echo $form->hidden('Availability.id');
	echo $form->end('upravit')
?>
<div class="actions">
	<ul>
		<li><?=$html->link('Zpět na seznam dostupností', array('action' => 'index'))?></li>
	</ul>
</div>