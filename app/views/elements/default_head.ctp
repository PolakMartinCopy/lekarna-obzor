<?php 
	if (!isset($title_for_content)) {
		$title_for_content = 'Lékárna Obzor Brno';
	}
	
	if (!isset($description_for_content)) {
		$description_for_content = 'Lékárna Obzor Brno.';
	}
?>

<meta http-equiv="content-type" content="text/html; charset=utf-8"/>
<meta http-equiv="content-language" content="cs"/>
<title><?php echo $title_for_content?> | PharmaCorp</title>
<meta name="description" content="<?php echo $description_for_content?>"/>
<link rel="stylesheet" type="text/css" href="/css/styles.css?v=1" />
<script type="text/javascript" src="/js/jquery-1.8.3.min.js"></script>
<script type="text/javascript" src="/js/bootstrap.js"></script>
<script type="text/javascript" src="/js/login_box_management.js"></script>
<script type="text/javascript" src="/js/search_box_management.js"></script>
<script type="text/javascript" src="/js/footer_forms_management.js"></script>

<link rel="stylesheet" href="/css/fancybox/jquery.fancybox-1.3.2.css" type="text/css" media="screen" />
<script type="text/javascript" src="/js/fancybox/jquery.fancybox-1.3.2.pack.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.easing-1.3.pack.js"></script>
<script type="text/javascript" src="/js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>

<script type="text/javascript">
	$(document).ready(function() {
		/* Apply fancybox to multiple items */
		$("a.thickbox").fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'overlayShow'	:	false
		});
	});
</script>
<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-38268275-1']);
  _gaq.push(['_trackPageview']);
  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>