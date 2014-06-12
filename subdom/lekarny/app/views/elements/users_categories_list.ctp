<?
	if ( $session->check('Company') ){
?>
<div class="boxWidthWrapper">
	<ul id="nav">
		<li><a href="#">Produkty</a>
<?php
		if ( $session->check('Cart') ){
			echo $form->Create('Search', array('url' => array('controller' => 'searches', 'action' => 'do_search')));
			echo $form->text('q', array('onClick' => 'return this.select();'));
			echo $form->submit('hledej', array('div' => false));
			echo $form->end();
?>
		</li>
	</ul>
	<ul>
			<?
					$categories = $this->requestAction('/categories/getCategoriesMenuList');
					$parents = array(0);
					foreach ( $categories['Categories'] as $category){
						if ( $category['Category']['parent_id'] != $parents[count($parents)-1] ){
							if ( in_array($category['Category']['parent_id'], $parents) ){
								while ( $parents[count($parents)-1] != $category['Category']['parent_id'] ){
									array_pop($parents);
								}
							} else {
								array_push($parents, $category['Category']['parent_id']);
							}
						}
						
						$spaces = '';
						for ( $i = 0; $i < count($parents) -1; $i++ ){
							$spaces .= '&nbsp;&nbsp;';
						}
				?>
						<li><?=$spaces ?><a<?=( $_SERVER['REQUEST_URI'] == '/users/categories_products/view/' . $category['Category']['id']  ? ' class="activeItem"' : '' ) ?> href="/users/categories_products/view/<?=$category['Category']['id'] ?>"><?=$category['Category']['name']?></a></li>
				<?
					}
				?>
	<?
		} else {
	?>
			<p>Pro zobrazení produktových kategorií nejdříve zvolte, zda chcete 
			<?=$html->link('vytvořit novou objednávku', array('users' => true, 'controller' => 'carts', 'action' => 'add')) ?>,
			nebo zda chcete pracovat s některou z 
			<?=$html->link('rozpracovaných objenávek', array('users' => true, 'controller' => 'carts', 'action' => 'index')) ?>.</p>
	<?
		}
	?>		
		</li>
	</ul>
</div>
<div style="clear:both;"></div>
<?
	}
?>