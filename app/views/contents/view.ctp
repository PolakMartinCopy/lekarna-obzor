<?
/* // vypis odkazu z linkeru
if ( preg_match('/##LINKER##/', $page_content) ){
	$linker_output = file_get_contents ('http://www.top-bazeny.cz/__linker/uploader.php?layout_name=top-bazeny_linkovaci_stranka&ru=' . base64_encode($_SERVER['REQUEST_URI']));
	$page_content = preg_replace('/##LINKER##/', $linker_output, $page_content);
} */

// vypis mapy na strance o prodejne
if (preg_match('/##MAP##/', $page_content)) {
	$subheader = '	<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?v=3.exp&amp;sensor=false"></script>
    <script type="text/javascript">
	var map;
    function initialize() {
		var point = new google.maps.LatLng(49.2284742, 16.6180531)
          
        var mapOptions = {
			zoom: 16,
			center: point,
			mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById(\'map2\'), mapOptions);

		var marker = new google.maps.Marker({
			position: point,
			map: map,
			title: \'Lékárna Obzor\'
        });
	}

    google.maps.event.addDomListener(window, \'load\', initialize);
    </script>';
	
	$layout->blockStart('subheader');
	echo $subheader;
	$layout->blockEnd();
    		
    $map_output = '<div id="map2"></div>';
    $page_content = preg_replace('/##MAP##/', $map_output, $page_content);
}

echo $page_content;
?>