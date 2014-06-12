<h2>Přesun kategorie do jiného uzlu</h2>
<p>Zvolte do kterého uzlu chcete kategorii <strong><?=$this->data['Category']['name']?></strong> přesunout.</p>
<?
	echo $form->create('Category', array('url' => array('action' => 'movenode', $this->data['Category']['id'])));
	echo $form->select('Category.target_id', $categories, null, array('empty' => false));
	echo $form->hidden('Category.id');
	echo $form->submit('Přesunout');
	echo $form->end();
?>