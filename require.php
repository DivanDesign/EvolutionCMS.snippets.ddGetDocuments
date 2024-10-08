<?php
// Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	// Path to `assets`
	dirname(
		__DIR__,
		2
	)
	. '/libs/ddTools/modx.ddtools.class.php'
);

require_once('src/DataProvider/DataProviderOutput.php');
require_once('src/Output.php');
require_once('src/Input.php');
require_once('src/Extender/Extender.php');
require_once('src/Outputter/Outputter.php');
require_once('src/Outputter/String/Outputter.php');
require_once('src/DataProvider/DataProvider.php');
require_once('src/DataProvider/Parent/DataProvider.php');

require_once('src/Snippet.php');
?>