<?php

/*                                                                        *
 * This script belongs to the TYPO3 extension "formhandler_subscription". *
 *                                                                        *
 * It is free software; you can redistribute it and/or modify it under    *
 * the terms of the GNU General Public License, either version 3 of the   *
 * License, or (at your option) any later version.                        *
 *                                                                        *
 * The TYPO3 project - inspiring people to share!                         *
 *                                                                        */

/**
 * If a valid auth code was submitted the referenced record is deleted from the database
 */
class Tx_FormhandlerSubscription_Finisher_RemoveAuthCodeRecord extends Tx_Formhandler_AbstractFinisher {

	/**
	 * Auth code related utility functions
	 *
	 * @var Tx_FormhandlerSubscription_Utils_AuthCode
	 */
	protected $utils;

	/**
	 * Inits the finisher mapping settings values to internal attributes.
	 *
	 * @param array $gp
	 * @param array $settings
	 */
	public function init($gp, $settings) {

		parent::init($gp, $settings);

		$this->utils = Tx_FormhandlerSubscription_Utils_AuthCode::getInstance();
	}

	/**
	 * Checks, if a valid auth code was submitted and deletes the referenced record
	 * from the database
	 *
	 * @return array the GET/POST data array
	 */
	public function process() {

		$authCode = $this->utils->getAuthCode();

		if (empty($authCode)) {
			$this->utilityFuncs->throwException('validateauthcode_insufficient_params');
		}

		$authCodeData = $this->utils->getAuthCodeDataFromDB($authCode);
		if (!isset($authCodeData)) {
			$this->utilityFuncs->throwException('validateauthcode_no_record_found');
		}

		$markAsDeleted = FALSE;
		if (intval($this->settings['markAsDeleted'])) {
			$markAsDeleted = TRUE;
		}
		$this->utils->removeAuthCodeRecordFromDB($authCodeData, $markAsDeleted);

		$this->utils->clearAuthCodeFromSession();
		$this->utils->clearAuthCodesByRowData($authCodeData);
		$this->gp = $this->utils->clearAuthCodeFromGP($this->gp);

		return $this->gp;
	}
}
?>