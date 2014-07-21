<?php
namespace Bolius\BoliusLangcheck\Service;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \DetectLanguage\DetectLanguage;

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('bolius_langcheck') . ('/Resources/Libraries/DetectLanguage/lib/detectlanguage.php');

/**
 * Assignes spam points to texts based on language detemined by using
 * DetectLanguage.com API
 *
 */
class LanguageService extends \TYPO3\CMS\Core\Service\AbstractService {


	/**
	 * action rateText
	 *
	 * Evaluate a text and return a spam rating
	 * - Based on settings from the EM
	 *
	 * @param   text     The text to determine a spam rating for
	 * @return  integer  Spam rating based on language
	 */
	public function rateText($text) {

		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['bolius_langcheck']);

		$allowed = GeneralUtility::trimExplode(",", $conf['allowedLanguages']);
		$disallowed = GeneralUtility::trimExplode(",", $conf['disallowedLanguages']);

		return $this->rateTextExtended($text, $allowed, $disallowed,
			$conf['blackPoints'], $conf['grayPoints'], $conf['minLength']);
	}

	/**
	 * action rateTextExtended
	 *
	 * Evaluate a text and return a spam rating
	 *
	 * @param   text     text        The text to determine a spam rating for
	 * @param   array    allowed     List of whitlisted languages
	 * @param   array    disallowed  List of blacklisted languages
	 * @param   integer  bP          The points to apply to a language on the blacklist
	 * @param   integer  gP          The points to apply to a language that are neither white- or blacklisted
	 * @param   integer  minLength   The minimun length of the text required to call DetectLanguage
	 * @return  integer              Spam rating based on language
	 */
	public function rateTextExtended($text, $allowed, $disallowed, $bP, $gP, $minLength = 0) {

		// If there are a minLength and the string is shorter than that,
		// assign 0 points to it
		if ($minLength > 0 && strlen($text) < $minLength) {
			return 0;
		}

		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['bolius_langcheck']);

		// Init DetectLanguage API
		DetectLanguage::setApiKey($conf['API_KEY']);

		// Identify language
		$result = DetectLanguage::simpleDetect($text);

		if ( in_array($result, $allowed) ) {
			return 0;

		} else if ( in_array($result, $disallowed) ) {
			// Language is on disallow list
			return $bP;

		} else {
			// Language is gray listed
			return $gP;
		}
	}
}
?>