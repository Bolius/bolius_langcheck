<?php

if (!defined ('TYPO3_MODE')) {
	  die('Access denied.');
}

// Hook to comments for handling the display of responses
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['comments']['externalSpamCheck'][$_EXTKEY] =
	'EXT:comments_languagespam/class.tx_commentslanguagespam_hooks.php:tx_commentslanguagespam_hooks->hook_externalSpamCheck';

?>
