<h1>Atributy produktů - nastavení</h1>
<? if (!empty($attributes)) { ?>
<div class="paging">
<?
	$paginator->options(array('url' => $this->passedArgs));
	echo $paginator->prev('<< Předchozí', array(), '<< Předchozí', array('class' => 'disabled_link'));
	echo '&nbsp;&nbsp;' . $paginator->numbers(array('separator' => false)) . '&nbsp;&nbsp;';
	echo $paginator->next('Další >>', array(), 'Další >>', array('class' => 'disabled_link'));
?>
	<div style="clear:both"></div>
</div>
<table class="top_headed" cellpadding="5" cellspacing="3">
	<tr>
		<th>název</th>
		<th>hodnota</th>
		<th>&nbsp;</th>
	</tr>
<? foreach ($attributes as $attribute) { ?>
	<tr>
		<td>
			<?=$attribute['Option']['name']?>
		</td>
		<td>
			<?=$attribute['Attribute']['value']?>
		</td>
		<td>
			<?php echo $html->link(__('Upravit', true), array('action'=>'edit', $attribute['Attribute']['id'])); ?>
			<?php echo $html->link(__('Smazat', true), array('action'=>'delete', $attribute['Attribute']['id']), null, __('Opravdu chcete smazat tento atribut?', true)); ?>
		</td>
	</tr>
<?php } ?>
</table>
<div class="paging">
<?
	$paginator->options(array('url' => $this->passedArgs));
	echo $paginator->prev('<< Předchozí', array(), '<< Předchozí', array('class' => 'disabled_link'));
	echo '&nbsp;&nbsp;' . $paginator->numbers(array('separator' => false)) . '&nbsp;&nbsp;';
	echo $paginator->next('Další >>', array(), 'Další >>', array('class' => 'disabled_link'));
?>
	<div style="clear:both"></div>
</div>
<? } ?>