<?php
namespace TYPO3\CommentsLanguagespam\Service;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \DetectLanguage\DetectLanguage;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Frederik Mogensen <frede@server-1.dk>
 *  
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('comments_languagespam') . ('/Resources/Libraries/DetectLanguage/lib/detectlanguage.php');

/**
 *
 *
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class LanguageService extends \TYPO3\CMS\Core\Service\AbstractService {


	/**
	 * action rateText
	 *
	 * Evaluate a text and return a spam rating
	 * - Based on settings from the EM
	 *
	 * @param   array    $params  Parameters to the function
	 * @param   integer  $pObj    Parent object
	 * @return  integer           Spam rating based on language
	 */
	function rateText($text) {

		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['comments_languagespam']);

		$allowed = GeneralUtility::trimExplode(",", $conf['allowedLanguages']);
		$disallowed = GeneralUtility::trimExplode(",", $conf['disallowedLanguages']);

		return $this->rateTextExtended($text, $allowed, $disallowed,
			$conf['blackPoints'], $conf['grayPoints']);
	}

	/**
	 * action rateTextExtended
	 *
	 * Evaluate a text and return a spam rating
	 *
	 * @param   array    $params  Parameters to the function
	 * @param   integer  $pObj    Parent object
	 * @return  integer           Spam rating based on language
	 */
	function rateTextExtended($text, $allowed, $disallowed, $bP, $gP) {
		$additionalPoints = 0;
		$form = $params['formdata'];
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['comments_languagespam']);

		// Init DetectLanguage API
		DetectLanguage::setApiKey($conf['API_KEY']);

		// Identify language
		$result = DetectLanguage::simpleDetect($text);
		error_log($result);

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
