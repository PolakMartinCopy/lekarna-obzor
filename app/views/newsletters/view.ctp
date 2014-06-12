<?
	$newsletter['Newsletter']['body'] = eregi_replace("\n", "<br />", $newsletter['Newsletter']['body']);	
	
	if ( eregi("##NEWSLETTER PRODUCTS##", $newsletter['Newsletter']['body']) ){
		$content = $this->element('newsletter_products', array('products' => $newsletter['Product']));
		$newsletter['Newsletter']['body'] = eregi_replace("##NEWSLETTER PRODUCTS##", $content, $newsletter['Newsletter']['body']);
	}
	
	echo $newsletter['Newsletter']['body']
?>