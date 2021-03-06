<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010-2011 Christian Kuhn <lolli@schwarzbu.ch>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Testcase for the PDO cache backend
 *
 * @author Christian Kuhn <lolli@schwarzbu.ch>
 * @package TYPO3
 * @subpackage tests
 */
class t3lib_cache_backend_PdoBackendTest extends tx_phpunit_testcase {

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

	/**
	 * Sets up this testcase
	 *
	 * @author Christian Kuhn <lolli@schwarzbu.ch>
	 */
	public function setUp() {
		if (!extension_loaded('pdo_sqlite')) {
			$this->markTestSkipped('pdo_sqlite extension was not available');
		}
	}

	/**
	 * @test
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 * @expectedException t3lib_cache_Exception
	 */
	public function setThrowsExceptionIfNoFrontEndHasBeenSet() {
		$backend = new t3lib_cache_backend_PdoBackend('Testing');
		$data = 'Some data';
		$identifier = 'MyIdentifier';
		$backend->set($identifier, $data);
	}

	/**
	 * @test
	 * @author Christian Jul Jensen <julle@typo3.org>
	 */
	public function itIsPossibleToSetAndCheckExistenceInCache() {
		$backend = $this->setUpBackend();
		$data = 'Some data';
		$identifier = 'MyIdentifier';
		$backend->set($identifier, $data);
		$this->assertTrue($backend->has($identifier));
	}

	/**
	 * @test
	 * @author Christian Jul Jensen <julle@typo3.org>
	 */
	public function itIsPossibleToSetAndGetEntry() {
		$backend = $this->setUpBackend();
		$data = 'Some data';
		$identifier = 'MyIdentifier';
		$backend->set($identifier, $data);
		$fetchedData = $backend->get($identifier);
		$this->assertEquals($data, $fetchedData);
	}

	/**
	 * @test
	 * @author Christian Jul Jensen <julle@typo3.org>
	 */
	public function itIsPossibleToRemoveEntryFromCache() {
		$backend = $this->setUpBackend();
		$data = 'Some data';
		$identifier = 'MyIdentifier';
		$backend->set($identifier, $data);
		$backend->remove($identifier);
		$this->assertFalse($backend->has($identifier));
	}

	/**
	 * @test
	 * @author Christian Jul Jensen <julle@typo3.org>
	 */
	public function itIsPossibleToOverwriteAnEntryInTheCache() {
		$backend = $this->setUpBackend();
		$data = 'Some data';
		$identifier = 'MyIdentifier';
		$backend->set($identifier, $data);
		$otherData = 'some other data';
		$backend->set($identifier, $otherData);
		$fetchedData = $backend->get($identifier);
		$this->assertEquals($otherData, $fetchedData);
	}

	/**
	 * @test
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 */
	public function findIdentifiersByTagFindsSetEntries() {
		$backend = $this->setUpBackend();

		$data = 'Some data';
		$entryIdentifier = 'MyIdentifier';
		$backend->set($entryIdentifier, $data, array('UnitTestTag%tag1', 'UnitTestTag%tag2'));

		$retrieved = $backend->findIdentifiersByTag('UnitTestTag%tag1');
		$this->assertEquals($entryIdentifier, $retrieved[0]);

		$retrieved = $backend->findIdentifiersByTag('UnitTestTag%tag2');
		$this->assertEquals($entryIdentifier, $retrieved[0]);
	}

	/**
	 * @test
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 */
	public function setRemovesTagsFromPreviousSet() {
		$backend = $this->setUpBackend();

		$data = 'Some data';
		$entryIdentifier = 'MyIdentifier';
		$backend->set($entryIdentifier, $data, array('UnitTestTag%tag1', 'UnitTestTag%tag2'));
		$backend->set($entryIdentifier, $data, array('UnitTestTag%tag3'));

		$retrieved = $backend->findIdentifiersByTag('UnitTestTag%tag2');
		$this->assertEquals(array(), $retrieved);
	}

	/**
	 * @test
	 * @author Christian Jul Jensen <julle@typo3.org>
	 */
	public function hasReturnsFalseIfTheEntryDoesntExist() {
		$backend = $this->setUpBackend();
		$identifier = 'NonExistingIdentifier';
		$this->assertFalse($backend->has($identifier));
	}

	/**
	 * @test
	 * @author Christian Jul Jensen <julle@typo3.org>
	 */
	public function removeReturnsFalseIfTheEntryDoesntExist() {
		$backend = $this->setUpBackend();
		$identifier = 'NonExistingIdentifier';
		$this->assertFalse($backend->remove($identifier));
	}

	/**
	 * @test
	 * @author Robert Lemke <robert@typo3.org>
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 */
	public function flushByTagRemovesCacheEntriesWithSpecifiedTag() {
		$backend = $this->setUpBackend();

		$data = 'some data' . microtime();
		$backend->set('PdoBackendTest1', $data, array('UnitTestTag%test', 'UnitTestTag%boring'));
		$backend->set('PdoBackendTest2', $data, array('UnitTestTag%test', 'UnitTestTag%special'));
		$backend->set('PdoBackendTest3', $data, array('UnitTestTag%test'));

		$backend->flushByTag('UnitTestTag%special');

		$this->assertTrue($backend->has('PdoBackendTest1'), 'PdoBackendTest1');
		$this->assertFalse($backend->has('PdoBackendTest2'), 'PdoBackendTest2');
		$this->assertTrue($backend->has('PdoBackendTest3'), 'PdoBackendTest3');
	}

	/**
	 * @test
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 */
	public function flushRemovesAllCacheEntries() {
		$backend = $this->setUpBackend();

		$data = 'some data' . microtime();
		$backend->set('PdoBackendTest1', $data);
		$backend->set('PdoBackendTest2', $data);
		$backend->set('PdoBackendTest3', $data);

		$backend->flush();

		$this->assertFalse($backend->has('PdoBackendTest1'), 'PdoBackendTest1');
		$this->assertFalse($backend->has('PdoBackendTest2'), 'PdoBackendTest2');
		$this->assertFalse($backend->has('PdoBackendTest3'), 'PdoBackendTest3');
	}

	/**
	 * @test
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 */
	public function flushRemovesOnlyOwnEntries() {
		$thisCache = $this->getMock('t3lib_cache_frontend_Frontend', array(), array(), '', FALSE);
		$thisCache->expects($this->any())->method('getIdentifier')->will($this->returnValue('thisCache'));
		$thisBackend = $this->setUpBackend();
		$thisBackend->setCache($thisCache);

		$thatCache = $this->getMock('t3lib_cache_frontend_Frontend', array(), array(), '', FALSE);
		$thatCache->expects($this->any())->method('getIdentifier')->will($this->returnValue('thatCache'));
		$thatBackend = $this->setUpBackend();
		$thatBackend->setCache($thatCache);

		$thisBackend->set('thisEntry', 'Hello');
		$thatBackend->set('thatEntry', 'World!');
		$thatBackend->flush();

		$this->assertEquals('Hello', $thisBackend->get('thisEntry'));
		$this->assertFalse($thatBackend->has('thatEntry'));
	}

	/**
	 * @test
	 * @author Ingo Renner <ingo@typo3.org>
	 * @author Christian Kuhn <lolli@schwarzbu.ch>
	 */
	public function collectGarbageReallyRemovesAnExpiredCacheEntry() {
		$backend = $this->setUpBackend();

		$data = 'some data' . microtime();
		$entryIdentifier = 'BackendPDORemovalTest';
		$backend->set($entryIdentifier, $data, array(), 1);

		$this->assertTrue($backend->has($entryIdentifier));

		$GLOBALS['EXEC_TIME'] += 2;
		$backend->collectGarbage();

		$this->assertFalse($backend->has($entryIdentifier));
	}

	/**
	 * @test
	 * @author Ingo Renner <ingo@typo3.org>
	 * @author Christian Kuhn <lolli@schwarzbu.ch>
	 */
	public function collectGarbageReallyRemovesAllExpiredCacheEntries() {
		$backend = $this->setUpBackend();

		$data = 'some data' . microtime();
		$entryIdentifier = 'BackendPDORemovalTest';

		$backend->set($entryIdentifier . 'A', $data, array(), NULL);
		$backend->set($entryIdentifier . 'B', $data, array(), 10);
		$backend->set($entryIdentifier . 'C', $data, array(), 1);
		$backend->set($entryIdentifier . 'D', $data, array(), 1);

		$this->assertTrue($backend->has($entryIdentifier . 'A'));
		$this->assertTrue($backend->has($entryIdentifier . 'B'));
		$this->assertTrue($backend->has($entryIdentifier . 'C'));
		$this->assertTrue($backend->has($entryIdentifier . 'D'));

		$GLOBALS['EXEC_TIME'] += 2;
		$backend->collectGarbage();

		$this->assertTrue($backend->has($entryIdentifier . 'A'));
		$this->assertTrue($backend->has($entryIdentifier . 'B'));
		$this->assertFalse($backend->has($entryIdentifier . 'C'));
		$this->assertFalse($backend->has($entryIdentifier . 'D'));
	}

	/**
	 * Sets up the PDO backend used for testing
	 *
	 * @return t3lib_cache_backend_PdoBackend
	 * @author Karsten Dambekalns <karsten@typo3.org>
	 */
	protected function setUpBackend() {
		$mockCache = $this->getMock('t3lib_cache_frontend_Frontend', array(), array(), '', FALSE);
		$mockCache->expects($this->any())->method('getIdentifier')->will($this->returnValue('TestCache'));

		$backend = t3lib_div::makeInstance('t3lib_cache_backend_PdoBackend', 'Testing');
		$backend->setCache($mockCache);
		$backend->setDataSourceName('sqlite::memory:');
		$backend->initializeObject();

		return $backend;
	}
}

?>