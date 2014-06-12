<?php if (isset($breadcrumbs)) { ?>
<div id="breadcrumbs">
<?	$i = 0;
	foreach ($breadcrumbs as $breadcrumb) {
		echo $html->link($breadcrumb['anchor'], $breadcrumb['href']);
		if ($i < count($breadcrumbs)-1) {
			echo ' &gt; ';
		}
		$i++;
	} ?>
</div>
<?php } ?>