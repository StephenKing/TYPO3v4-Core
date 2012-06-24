<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Fabien Udriot <fabien.udriot@ecodev.ch>
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
 * ************************************************************* */

/**
 * Testcase for class t3lib_extFileFunctions
 *
 * @author Fabien Udriot <fabien.udriot@ecodev.ch>
 * @package TYPO3
 * @subpackage t3lib
 */
class t3lib_extFileFunctionsTest extends tx_phpunit_testcase {

	/**
	 * @var boolean Enable backup of global and system variables
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
	 * @var fileProcessor
	 */
	protected $fileProcessor;

	/**
	 * @var t3lib_file_Repository_StorageRepository
	 */
	protected $storageRepository;

	/**
	 * @var string
	 */
	protected $newFileNameInput = '_unitTestedFile.txt';

	/**
	 * @var string
	 */
	protected $newFolderNameInput = '_unitTestedFolder';

	/**
	 * @var string
	 */
	protected $renameFileNameInput = '_unitTestedFileRenamed.txt';

	/**
	 * @var string
	 */
	protected $renameFolderNameInput = '_unitTestedFolderRenamed';

	/**
	 * @var string
	 */
	protected $copyFolderNameInput = '_unitTestedFolderCopied';

	/**
	 * @var string
	 */
	protected $moveFolderNameInput = '_unitTestedFolderMoved';

	/**
	 * @var array
	 */
	protected $objectsToTearDown = array();

	/**
	 * Sets up this testcase
	 */
	public function setUp() {

		t3lib_div::purgeInstances();

		$this->storageRepository = t3lib_div::makeInstance('t3lib_file_Repository_StorageRepository');

		// Initializing file processor
		$GLOBALS['BE_USER'] = $this->getMock(
			't3lib_beUserAuth', array('getSessionData', 'setAndSaveSessionData')
		);
		$GLOBALS['BE_USER']->user['uid'] = 1;

		$GLOBALS['FILEMOUNTS'] = array();

		// Initializing:
		$this->fileProcessor = t3lib_div::makeInstance('t3lib_extFileFunctions');
		$this->fileProcessor->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
		$this->fileProcessor->init_actionPerms($GLOBALS['BE_USER']->getFileoperationPermissions());
		$this->fileProcessor->dontCheckForUnique = 1;
	}

	/**
	 * Tears down this testcase
	 */
	public function tearDown() {
		foreach ($this->objectsToTearDown as $object) {
			if ($object instanceof t3lib_file_File || $object instanceof t3lib_file_Folder) {
				$object->delete();
			}
		}
		$this->objectsToTearDown = array();
		t3lib_div::purgeInstances();
	}

	/**
	 * @return t3lib_file_Storage
	 */
	protected function getDefaultStorage() {

		// Get the first storage available.
		// Notice if no storage is found, a storage is created on the fly.
		$storages = $this->storageRepository->findAll();

		// Makes sure to return a storage having a local driver
		foreach ($storages as $storage) {
			// @todo how to retrieve information about the driver since @driver@ and @storageRecord@ are private
		}

		//* @var $storage t3lib_file_Storage */
		return $storages[0];
	}
	/**
	 * @return string
	 */
	protected function getRootFolderIdentifier() {
		$storage = $this->getDefaultStorage();

		$folderIdentifier = '/'; // the root of the storage
		$folderCombinedIdentifier = $storage->getUid() . ':' . $folderIdentifier;
		return $folderCombinedIdentifier;
	}

	/*********************************
	 * CREATE
	 ********************************/
	/**
	 * @test
	 */
	public function createNewFileInLocalStorage() {

		// Defines values
		$fileValues = array(
			'newfile' => array(
				array(
					'data' => $this->newFileNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$fileObject = NULL;
		if (!empty ($results['newfile'][0])) {
			$fileObject = $results['newfile'][0];
		}

		$this->objectsToTearDown[] = $fileObject;
		$this->assertEquals(TRUE, $fileObject instanceof t3lib_file_File);
	}

	/**
	 * @test
	 */
	public function createNewFolderInLocalStorage() {

		// Defines values
		$fileValues = array(
			'newfolder' => array(
				array(
					'data' => $this->newFolderNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$folderObject = NULL;
		if (!empty ($results['newfolder'][0])) {
			$folderObject = $results['newfolder'][0];
		}

		$this->objectsToTearDown[] = $folderObject;
		$this->assertEquals(TRUE, $folderObject instanceof t3lib_file_Folder);
	}

	/*********************************
	 * DELETE
	 ********************************/
	/**
	 * @test
	 */
	public function deleteFileInLocalStorage() {

		// Computes a $fileIdentifier which looks like 8:/fileName.txt where 8 is the storage Uid
		$storage = $this->getDefaultStorage();
		$fileIdentifier = $storage->getUid() . ':/' . $this->newFileNameInput;

		// Defines values
		$fileValues = array(
			'newfile' => array(
				array(
					'data' => $this->newFileNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'delete' => array(
				array(
					'data' => $fileIdentifier,
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$this->assertEquals(TRUE, empty ($results['delete'][1]));
	}

	/**
	 * @test
	 */
	public function deleteFolderInLocalStorage() {

		// Computes a $fileIdentifier which looks like 8:/fileName.txt where 8 is the storage Uid
		$storage = $this->getDefaultStorage();
		$folderIdentifier = $storage->getUid() . ':/' . $this->newFolderNameInput;

		// Defines values
		$fileValues = array(
			'newfolder' => array(
				array(
					'data' => $this->newFolderNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'delete' => array(
				array(
					'data' => $folderIdentifier,
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$this->assertEquals(TRUE, $results['delete'][0]);
	}

	/*********************************
	 * RENAME
	 ********************************/
	/**
	 * @test
	 */
	public function renameFileInLocalStorage() {

		// Computes a $fileIdentifier which looks like 8:/fileName.txt where 8 is the storage Uid
		$storage = $this->getDefaultStorage();
		$fileIdentifier = $storage->getUid() . ':/' . $this->newFileNameInput;

		// Defines values
		$fileValues = array(
			'newfile' => array(
				array(
					'data' => $this->newFileNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'rename' => array(
				array(
					'data' => $fileIdentifier,
					'target' => $this->renameFileNameInput,
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$fileObject = NULL;
		if (!empty ($results['rename'][0])) {
			$fileObject = $results['rename'][0];
		}

		$this->objectsToTearDown[] = $fileObject;
		$this->assertEquals(TRUE, $fileObject instanceof t3lib_file_File);
	}

	/**
	 * @test
	 */
	public function renameFolderInLocalStorage() {

		// Computes a $fileIdentifier which looks like 8:/fileName.txt where 8 is the storage Uid
		$storage = $this->getDefaultStorage();
		$folderIdentifier = $storage->getUid() . ':/' . $this->newFolderNameInput;

		// Defines values
		$fileValues = array(
			'newfolder' => array(
				array(
					'data' => $this->newFolderNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'rename' => array(
				array(
					'data' => $folderIdentifier,
					'target' => $this->renameFolderNameInput,
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$folderObject = NULL;
		if (!empty ($results['rename'][0])) {
			$folderObject = $results['rename'][0];
		}

		$this->objectsToTearDown[] = $folderObject;
		$this->assertEquals(TRUE, $folderObject instanceof t3lib_file_Folder);
	}

	/*********************************
	 * MOVE
	 ********************************/
	/**
	 * @test
	 */
	public function moveFileInLocalStorage() {

		// Computes a $fileIdentifier which looks like 8:/fileName.txt where 8 is the storage Uid
		$storage = $this->getDefaultStorage();
		$fileIdentifier = $storage->getUid() . ':/' . $this->newFileNameInput;
		$targetFolder = $this->getRootFolderIdentifier() . $this->newFolderNameInput;

		// Defines values
		$fileValues = array(
			'newfile' => array(
				array(
					'data' => $this->newFileNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'newfolder' => array(
				array(
					'data' => $this->newFolderNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'move' => array(
				array(
					'data' => $fileIdentifier,
					'target' => $targetFolder,
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$fileObject = NULL;
		if (!empty ($results['move'][0])) {
			$fileObject = $results['move'][0];
		}

		// remove parent folder
		if (!empty ($results['newfolder'][0])) {
			$this->objectsToTearDown[] = $results['newfolder'][0];
		}

		$this->assertEquals(TRUE, $fileObject instanceof t3lib_file_File);
	}

	/**
	 * @test
	 */
	public function moveFolderInLocalStorage() {

		// Computes a $folderIdentifier which looks like 8:/folderName.txt where 8 is the storage Uid
		$storage = $this->getDefaultStorage();
		$folderIdentifier = $storage->getUid() . ':/' . $this->moveFolderNameInput;
		$targetFolder = $this->getRootFolderIdentifier() . $this->newFolderNameInput;

		// Defines values
		$fileValues = array(
			'newfolder' => array(
				array(
					'data' => $this->newFolderNameInput,
					'target' => $this->getRootFolderIdentifier(),
				),
				array(
					'data' => $this->moveFolderNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'move' => array(
				array(
					'data' => $folderIdentifier,
					'target' => $targetFolder,
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$folderObject = NULL;
		if (!empty ($results['move'][0])) {
			$folderObject = $results['move'][0];
		}

		// remove parent folder
		if (!empty ($results['newfolder'][0])) {
			$this->objectsToTearDown[] = $results['newfolder'][0];
		}

		$this->assertEquals(TRUE, $folderObject instanceof t3lib_file_Folder);
	}

	/*********************************
	 * COPY
	 ********************************/
	/**
	 * @test
	 */
	public function copyFileInLocalStorage() {

		// Computes a $fileIdentifier which looks like 8:/fileName.txt where 8 is the storage Uid
		$storage = $this->getDefaultStorage();
		$fileIdentifier = $storage->getUid() . ':/' . $this->newFileNameInput;
		$targetFolder = $this->getRootFolderIdentifier() . $this->newFolderNameInput;

		// Defines values
		$fileValues = array(
			'newfile' => array(
				array(
					'data' => $this->newFileNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'newfolder' => array(
				array(
					'data' => $this->newFolderNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'copy' => array(
				array(
					'data' => $fileIdentifier,
					'target' => $targetFolder,
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$fileObject = NULL;
		if (!empty ($results['copy'][0])) {
			$fileObject = $results['copy'][0];
		}

		// remove parent folder
		if (!empty ($results['newfolder'][0])) {
			$this->objectsToTearDown[] = $results['newfolder'][0];
		}
		if (!empty ($results['newfile'][0])) {
			$this->objectsToTearDown[] = $results['newfile'][0];
		}

		$this->assertEquals(TRUE, $fileObject instanceof t3lib_file_File);
	}

	/**
	 * @test
	 */
	public function copyFolderInLocalStorage() {

			// Computes a $folderIdentifier which looks like 8:/folderName.txt where 8 is the storage Uid
		$storage = $this->getDefaultStorage();
		$folderIdentifier = $storage->getUid() . ':/' . $this->copyFolderNameInput;
		$targetFolder = $this->getRootFolderIdentifier() . $this->newFolderNameInput;

			// Defines values
		$fileValues = array(
			'newfolder' => array(
				array(
					'data' => $this->newFolderNameInput,
					'target' => $this->getRootFolderIdentifier(),
				),
				array(
					'data' => $this->copyFolderNameInput,
					'target' => $this->getRootFolderIdentifier(),
				)
			),
			'copy' => array(
				array(
					'data' => $folderIdentifier,
					'target' => $targetFolder,
				)
			)
		);

		$this->fileProcessor->start($fileValues);
		$results = $this->fileProcessor->processData();

		$folderObject = NULL;
		if (!empty ($results['copy'][0])) {
			$folderObject = $results['copy'][0];
		}

			// remove parent folder
		if (!empty ($results['newfolder'][0])) {
			$this->objectsToTearDown[] = $results['newfolder'][0];
		}
		if (!empty ($results['newfolder'][1])) {
			$this->objectsToTearDown[] = $results['newfolder'][1];
		}

		$this->assertEquals(TRUE, $folderObject instanceof t3lib_file_Folder);
	}
}

?>