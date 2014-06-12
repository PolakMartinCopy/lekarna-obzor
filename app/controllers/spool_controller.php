<?
class SpoolController extends AppController {
   var $name = 'Spool';


	function add() {
		$query = "INSERT INTO `categories` (`id`, `parent_id`, `name`, `title`, `description`, `lft`, `rght`) VALUES
(1, 5, 'Glukometry', '', '', 2, 7),
(5, 0, 'KATALOG', '', '', 1, 22),
(6, 5, 'Kompresivní punčochy', '', '', 16, 17),
(15, 5, 'Obuv pro diabetiky', '', '', 8, 13),
(16, 15, 'Pánská', '', '', 11, 12),
(17, 15, 'Dámská', '', '', 9, 10),
(23, 5, 'Zdravotní ponožky', '', '', 14, 15),
(24, 5, 'Podpůrné punčochy', '', '', 18, 19),
(25, 5, 'Kompresivní návleky', '', '', 20, 21),
(26, 1, 'Testovací proužky', '', '', 3, 4),
(27, 1, 'Příslušenství', '', '', 5, 6)";
		$data = $this->Spool->query($query);
		debug($data);

	}


}
?>