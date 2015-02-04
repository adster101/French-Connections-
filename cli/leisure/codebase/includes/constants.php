<?PHP
	//$firstdate will always be the fist date in Avail-, Price-, ...String. Date can be changed regularly
	$firstDate = DateTime::createFromFormat('Y-m-d', FIRSTDATE);
	//define("FIRST_DATE",$firstDate);

	define("MAX_LENGTH_OF_STAY",31); //max days of a bookable period, still to be defined
	
	$avail_langs = array('nl','de','fr','en','it','es','pl');
	$std_cust_country = array(
		'nl'=>'nl','de'=>'de','fr'=>'fr','es'=>'es','it'=>'it','pl'=>'pl','en'=>'gb'
	);
	$lang = (!in_array($_REQUEST['lang'],$avail_langs)) ? 'en' : $_REQUEST['lang'];
	define('LANG',strtolower($lang));
	
	$all_countries_trans = array('nl'=>'Alle landen','fr'=>'Tous les pays','de'=>'Alle L�nder','en'=>'All countries','it'=>'Tutti i paesi','es'=>'Todos los pa�ses','pl'=>'Wszystkie kraje');
	$all_regions_trans = array('nl'=>'Alle regio\'s','fr'=>'Toutes les r�gions','de'=>'Alle Regionen','en'=>'All regions','it'=>'Tutte le regioni','es'=>'todas las regiones','pl'=>'Wszystkie regiony');
	
	$dayOfWeekTransShort = array(
		'nl' => array('Zo','Ma','Di','Wo','Do','Vr','Za','Zo'),
		'fr' => array(),
		'de' => array(),
		'en' => array('Su','Mo','Tu','We','Th','Fr','Sa','Su'),
		'it' => array(),
		'es' => array(),
		'pl' => array()
	);
	$monthNames = array(
		'nl' => array('Januari','Februari','Maart','April','Mei','Juni','Juli','Augustus','September','Oktober','November','December'),
		'fr' => array(),
		'de' => array(),
		'en' => array('January','February','March','April','May','June','July','August','September','October','November','December'),
		'it' => array(),
		'es' => array(),
		'pl' => array()
	);
	

?>