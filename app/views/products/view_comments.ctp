<div class="mainContentWrapper">
<h2><?=$product['Product']['name'] ?></h2>
<p><?=$product['Product']['short_description'] ?></p>
<h2>Komentáře</h2>
<? if (empty($comments)) { ?>
	<p>Diskuze neobsahuje žádné komentáře pro tento produkt.</p>
<? } else {
		foreach ($comments as $comment) { ?>
			<p>
				<div style="background-color:silver;padding:3px;">
				<strong><?=$comment['Comment']['subject'] ?></strong>
				od <strong><?=$comment['Comment']['author']?></strong> ze dne <em><?=cz_date_time($comment['Comment']['created']) ?></em><br/>
				</div>
				<?
					echo $comment['Comment']['body'];
					if ( !empty($comment['Comment']['reply']) ){
						echo '<div style="margin-top:5px;padding-left:5px;margin-left:15px;border-left:1px solid black;">' . $comment['Comment']['reply'] . '<br />
						<br />za <em>Top Bazény CZ</em><br />' . $comment['Administrator']['first_name'] . ' ' . $comment['Administrator']['last_name'] .'
						</div>';
					}
				?>
			</p>
<?		}
	}
?>
	<div class="actions">
		<ul>
			<li><?=$html->link('přidat nový komentář / dotaz', array('controller' => 'comments', 'action' => 'add', $product['Product']['id']))?></li>
			<li><?=$html->link('zpět na detaily o produktu', '/' . $product['Product']['url'])?></li>
		</ul>
	</div>
</div>