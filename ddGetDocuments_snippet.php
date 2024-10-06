<?php
/**
 * ddGetDocuments
 * @version 1.6 (2022-09-30)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddgetdocuments
 * 
 * @copyright 2015–2022 Ronef {@link https://Ronef.me }
 */

// Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddGetDocuments',
	'params' => $params,
]);
?>