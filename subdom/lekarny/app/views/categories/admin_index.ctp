<h1><?php echo $category['Category']['name']?> - seznam podkategorií</h1>

<ul class="actions">
	<li><?=$html->link('nová podkategorie', array('controller' => 'categories', 'action' => 'add', 'id' => 0)); ?>
</ul>

<?
	if ( !empty($categories) ){
?>
	<table class="top_headed" cellpadding="5" cellspacing="3">
		<tr>
			<th>
				název
			</th>
			<th>
				&nbsp;
			</th>
		</tr>
<?
	$parents = array(0);
	$odd = ' class="odd"';
	foreach ( $categories as $category ){
		if ( $category['Category']['parent_id'] != $parents[count($parents)-1] ){
			if ( in_array($category['Category']['parent_id'], $parents) ){
				while ( $parents[count($parents)-1] != $category['Category']['parent_id'] ){
					array_pop($parents);
				}
			} else {
				array_push($parents, $category['Category']['parent_id']);
			}
		}
?>
		<tr<?php echo $odd?>>
			<td>
				<?
					for ( $i = 0; $i < count($parents) -1; $i++ ){
						echo '&nbsp;&nbsp;';
					}
					echo $html->link($category['Category']['name'], array('controller' => 'categories', 'action' => 'index', 'id' => $category['Category']['id']))
				?>
			</td>
			<td>
				<?=$html->link('seznam produktů (' . $this->requestAction('/categories/count_products/' . $category['Category']['id']) . ')' , array('controller' => 'products', 'action' => 'index', 'category_id' => $category['Category']['id']))?> |
				<?=$html->link('vložit produkt', array('controller' => 'products', 'action' => 'add', 'category_id' => $category['Category']['id']))?> |
				<?=$html->link('upravit', array('controller' => 'categories', 'action' => 'edit', 'id' => $category['Category']['id'])) ?> |
				<?=$html->link('nová podkategorie', array('controller' => 'categories', 'action' => 'add', 'parent_id' => $category['Category']['id'])) ?> |
				<?=$html->link('smazat', array('controller' => 'categories', 'action' => 'delete', 'id' => $category['Category']['id'])) ?> |
				<?=$html->link('přesunout', array('controller' => 'categories', 'action' => 'move', 'id' => $category['Category']['id'])) ?>
			</td>
		</tr>
		
<?
		$odd = $odd == ' class="odd"' ? '' : ' class="odd"';
	}
?>
	</table>
	
	<ul class="actions">
		<li><?=$html->link('nová podkategorie', array('controller' => 'categories', 'action' => 'add', 'id' => 0)); ?>
	</ul>
<?
	}
?>