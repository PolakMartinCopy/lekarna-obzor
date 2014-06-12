<div>
	<h2>Vytvoření nového newsletteru</h2>
	<?
		echo $form->Create('Newsletter');
		echo 'název newsletteru: ' . $form->input('Newsletter.name', array('label' => false));
		echo $form->end('Vytvořit nový newsletter');
	?>
</div>