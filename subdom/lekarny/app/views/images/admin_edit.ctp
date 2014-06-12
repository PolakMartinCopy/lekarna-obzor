<h1>Editace obrázku</h1>
<?=$form->create('Image') ?>
<?=$form->input('Image.name', array('size' => 100, 'label' => false)) ?>
<?=$form->hidden('Image.product_id') ?>
<?=$form->end('změnit') ?>
