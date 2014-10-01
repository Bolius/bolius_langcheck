<?php

if (!defined ('TYPO3_MODE')) {
	  die('Access denied.');
}

// Hook to comments for handling the display of responses
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['comments']['externalSpamCheck'][$_EXTKEY] = 'Bolius\\BoliusLangcheck\\Hooks\\CommentsHooks->hook_externalSpamCheck';


?>