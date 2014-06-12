<?
	header("Content-type: text/csv; charset=windows-1250");
	header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
	echo $content_for_layout;
?>