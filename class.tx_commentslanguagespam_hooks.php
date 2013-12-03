<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Frederik Mogensen <frede@server-1.dk>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * class.tx_commentslanguagespam_hooks.php
 *
 * Commenting system hooks.
 *
 * @author Frederik Mogensen <frede@server-1.dk>
 */

require_once('Resources/Libraries/DetectLanguage/lib/detectlanguage.php');

use TYPO3\CMS\Core\Utility\GeneralUtility;
use \DetectLanguage\DetectLanguage;


class tx_commentslanguagespam_hooks {

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
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['comments_languagespam']);

		$this->conf['allow'] = GeneralUtility::trimExplode(",", $this->conf['allowedLanguages']);
		$this->conf['disallow'] = GeneralUtility::trimExplode(",", $this->conf['disallowedLanguages']);

		// Init DetectLanguage API
		DetectLanguage::setApiKey($this->conf['API_KEY']);

		// Identify language
		$result = DetectLanguage::simpleDetect($form['content']);

		if ( in_array($result, $this->conf['allow']) ) {
			// Base case
			// Do nothing

		} else if ( in_array($result, $this->conf['disallow']) ) {
			// Language is on disallow list
			$additionalPoints += $this->conf['blackPoints'];

		} else {
			// Language is on gray list
			$additionalPoints += $this->conf['grayPoints'];

		}

		return $additionalPoints;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/comments_languagespam/class.tx_commentslanguagespam_hooks.php']) {

	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/comments_languagespam/class.tx_commentslanguagespam_hooks.php']);
}

?>

