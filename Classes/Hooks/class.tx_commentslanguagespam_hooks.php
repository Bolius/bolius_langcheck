<?php
namespace Bolius\BoliusLangcheck\Hooks;

require_once('Resources/Libraries/DetectLanguage/lib/detectlanguage.php');

use TYPO3\CMS\Core\Utility\GeneralUtility;
use \DetectLanguage\DetectLanguage;

/**
 *
 * Commenting system hooks.
 *
 */
class CommentsHooks {

	/**
	 * Inserts responses into the marker array
	 *
	 * @param   array    $params  Parameters to the function
	 * @param   integer  $pObj    Parent object
	 * @return  integer           integer value to be added to the current spam points
	 */
	function hook_externalSpamCheck(&$params, &$pObj) {
		$additionalPoints = 0;
		$form = $params['formdata'];
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['bolius_langcheck']);

		$this->conf['allow'] = GeneralUtility::trimExplode(",", $this->conf['allowedLanguages']);
		$this->conf['disallow'] = GeneralUtility::trimExplode(",", $this->conf['disallowedLanguages']);

		// If there are a minLength and the string is shorter than that,
		// assign 0 points to it
		if ($this->conf['minLength'] > 0 && strlen($text) < $this->conf['minLength']) {
			return 0;
		}

		// Init DetectLanguage API
		DetectLanguage::setApiKey($this->conf['API_KEY']);

		// Identify language
		$result = DetectLanguage::simpleDetect($form['content']);

		if ( in_array($result, $this->conf['allow']) ) {
			return 0;

		} else if ( in_array($result, $this->conf['disallow']) ) {
			// Language is on disallow list
			return $this->conf['blackPoints'];

		} else {
			// Language is on gray list
			return $this->conf['grayPoints'];

		}
	}
}

?>