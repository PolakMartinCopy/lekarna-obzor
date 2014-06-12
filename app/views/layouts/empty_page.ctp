<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="cs" />
	<title>administrace <?php echo CUST_ROOT ?></title>
	<style type="text/css">
		body{
			font-family:Arial, Helvetica, sans-serif;
		}
		fieldset{
			width:300px;
			text-align:left;
			display:block;
			border:none;
		}
	</style>
</head>

<body>

	<div style="width:800px;margin:auto;">
<?
		if ($session->check('Message.flash')){
			echo $session->flash();
		}
?>

	<?
	echo $content_for_layout 
	?>
	</div>
</body>
</html>
