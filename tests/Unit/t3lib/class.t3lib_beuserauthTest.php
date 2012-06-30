<?php
/***************************************************************
* Copyright notice
*
* (c) 2010-2011 Oliver Klee (typo3-coding@oliverklee.de)
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Testcase for the t3lib_beUserAuth class in the TYPO3 Core extension.
 *
 * @package TYPO3
 * @subpackage t3lib
 *
 * @author Oliver Klee <typo3-coding@oliverklee.de>
 */
class t3lib_beUserAuthTest extends tx_phpunit_testcase {
	/**
	 * @var t3lib_beUserAuth
	 */
	private $fixture = NULL;

	/**
	 * Enable backup of global and system variables
	 *
	 * @var boolean
	 */
	protected $backupGlobals = TRUE;

	/**
	 * Exclude TYPO3_DB from backup/ restore of $GLOBALS
	 * because resource types cannot be handled during serializing
	 *
	 * @var array
	 */
	protected $backupGlobalsBlacklist = array('TYPO3_DB');

	public function setUp() {
			// reset hooks
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'] = array();
		$this->fixture = new t3lib_beUserAuth();
		$this->fixture->user['uid'] = 1;
		$GLOBALS['BE_USER'] = $this->fixture;
	}

	public function tearDown() {
		unset($this->fixture);
		unset($GLOBALS['BE_USER']);
		t3lib_formProtection_Factory::purgeInstances();
	}


	/////////////////////////////////////////
	// Tests concerning the form protection
	/////////////////////////////////////////

	/**
	 * @test
	 */
	public function logoffCleansFormProtection() {
		$formProtection = $this->getMock(
			't3lib_formprotection_BackendFormProtection', array('clean'), array(), '', FALSE
		);
		$formProtection->expects($this->atLeastOnce())->method('clean');
		t3lib_formProtection_Factory::set(
			't3lib_formprotection_BackendFormProtection', $formProtection
		);

		$this->fixture->logoff();
	}
}
?>