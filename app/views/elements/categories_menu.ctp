<ul id="nav">
	<?
	foreach ( $categories as $category ){
		$link_options = array('title' => $category['Category']['name'], 'escape' => false);
		if (in_array($category['Category']['id'], $path_ids)) {
			$link_options['class'] = 'actual';
		}
	?>
	<li><?php
		echo $this->Html->link(mb_strtoupper($category['Category']['name'], 'utf-8'), '/' . $category['Category']['url'], $link_options);
		if (!empty($category['children'])) { ?>
		<ul>
	<?php 	foreach ($category['children'] as $child) {
				$link_options = array('title' => $child['Category']['name'], 'escape' => false);
				if (in_array($child['Category']['id'], $path_ids)) {
					$link_options['class'] = 'actual';
				} ?>
			<li><?php
				echo $this->Html->link(mb_strtoupper($child['Category']['name'], 'utf-8'), '/' . $child['Category']['url'], $link_options);
				if (!empty($child['children'])) { ?>
				<ul>
	<?php 			foreach ($child['children'] as $grandchild) {
						$link_options = array('title' => $grandchild['Category']['name'], 'escape' => false);
						if (in_array($grandchild['Category']['id'], $path_ids)) {
							$link_options['class'] = 'actual';
						} ?>
					<li><?php echo $this->Html->link(mb_strtoupper($grandchild['Category']['name'], 'utf-8'), '/' . $grandchild['Category']['url'], $link_options)?></li>
	<?php 			}?>
				</ul>
	<?php		}?>	
			</li>
	<?php 	} ?>
		</ul>
	<?php } ?>
	</li>
	<?php } ?>
</ul>