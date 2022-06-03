<?php
/**
 * ddGetDocuments
 * @version 1.5 (2022-06-03)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddgetdocuments
 * 
 * @copyright 2015–2022 DD Group {@link https://DivanDesign.biz }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddGetDocuments',
	'params' => $params
]);
?>