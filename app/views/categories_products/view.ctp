<h1><?php echo $category['Category']['heading']?></h1>
<?php if ($category['Category']['content']) { ?>
<div class="category_description"><?php echo $category['Category']['content']; ?></div>
<?php } ?>

<?php if (!empty($category_most_sold_products)) { ?>
<div id="product_listing_featured_container">
	<h2>Nejprodávanější z kategorie</h2>
	<?php 
		$class = ' first';
		foreach ($category_most_sold_products as $cms_product) {
	?>
	<div class="product_listing_featured<?php echo $class?>">
		<?php if (!empty($cms_product['Product']['Image'])) { ?>
		<a href="/<?php echo $cms_product['Product']['url']?>" title="<?php echo $cms_product['Product']['name']?>">
			<img src="/product-images/medium/<?php echo $cms_product['Product']['Image'][0]['name']?>" width="210px" height="210px" alt="produkt <?php echo $cms_product['Product']['name']?>" />
		</a>
		<?php } ?>
		<h3><a href="/<?php echo $cms_product['Product']['url']?>"><?php echo $cms_product['Product']['name']?></a></h3>
	</div>
	<?php
			$class = '';
		}
	}
	?>
	<div class="clearer"></div>
</div>

<div class="menu_spacer"></div>
<h2 class="product_listing">Produkty</h2>
<div id="sort_options">
	<?php
		$url = $this->params['url']['url'];
		$class = '';
		if ((isset($_GET['sort_name']) && isset($_GET['sort_order']) && $_GET['sort_name'] == 'name' && $_GET['sort_order'] == 'asc') || (!isset($_GET['sort_name']) && !isset($_GET['sort_order']))) {
			$class = ' class="active"';
		}
		?>
		<a href="/<?php echo $url?>"<?php echo $class?>>Abecedně</a> |

	<?php 
		$class = '';
		if (isset($_GET['sort_name']) && isset($_GET['sort_order']) && $_GET['sort_name'] == 'price' && $_GET['sort_order'] == 'asc') {
			$class = ' class="active"';
		}
	?>
	<a href="/<?php echo $url?>?sort_name=price&amp;sort_order=asc"<?php echo $class?>>Od nejlevnějšího</a> |
	<?php
		$class = '';
		if (isset($_GET['sort_name']) && isset($_GET['sort_order']) && $_GET['sort_name'] == 'price' && $_GET['sort_order'] == 'desc') {
			$class = ' class="active"';
		}
	?>
	<a href="/<?php echo $url?>?sort_name=price&amp;sort_order=desc"<?php echo $class?>>Od nejdražšího</a>
</div>
<div style="clear:both;border-bottom:1px solid #008A00;"></div>
<div class="menu_spacer"></div>

<?php 
	if (!empty($products)) {
		echo $this->element($listing_style);
	}
?>