<script type="text/javascript">
	$(function() {
		$( "#RateFrom" ).datepicker({
			onClose: function( selectedDate ) {
				$( "#RateTo" ).datepicker( "option", "minDate", selectedDate );
			}
		});
		$( "#RateTo" ).datepicker({
			onClose: function( selectedDate ) {
				$( "#RateFrom" ).datepicker( "option", "maxDate", selectedDate );
			}
		});
	});
</script>

<h1>Seznam kurzů</h1>
<!-- filtr dat -->
<?php echo $this->Form->create('Rate', array('action' => 'index', 'url' => $this->passedArgs))?>
<table class="left_heading">
	<tr>
		<th>Od</th>
		<td><?php echo $this->Form->input('Rate.from', array('label' => false, 'class' => 'datepicker'))?></td>
		<th>Do</th>
		<td><?php echo $this->Form->input('Rate.to', array('label' => false, 'class' => 'datepicker'))?></td>
		<td><?php echo $this->Form->submit('Vyhledat')?></td>
	</tr>
	<tr>
		<td colspan="5">
			<?php echo $this->Html->link('reset filtru', array('action' => 'index', 'reset' => 'rates') + $this->passedArgs)?>
		</td>
</table>
<?php echo  $this->Form->end()?>
<?php if (empty($rates)) { ?>
<p><em>V systému nejsou žádná data k zobrazení.</em></p>
<?php } else { ?>
<!-- seznam vysledku -->
<table class="top_heading">
	<tr>
		<th><?php echo $this->Paginator->sort('Datum', 'Rate.created')?></th>
		<th><?php echo $this->Paginator->sort('Kurz', 'Rate.value')?></th>
	</tr>
	<?php foreach ($rates as $rate) { ?>
	<tr>
		<td><?php echo $rate['Rate']['created']?></td>
		<td><?php echo $rate['Rate']['value']?></td>
	</tr>
	<?php } ?>
</table>
<?php echo $this->Paginator->prev('« Předchozí', null, null, array('class' => 'disabled')); ?> 
<?php echo $this->Paginator->numbers(); ?> 
<?php echo $this->Paginator->next('Další »', null, null, array('class' => 'disabled')); ?>
<br/>
<?php echo $this->Html->link('CSV export', array('action' => 'csv_export') + $this->passedArgs)?>
<?php }?>