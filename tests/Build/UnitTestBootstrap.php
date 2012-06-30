<?php

define('TYPO3_MODE', 'BE');
//define('TYPO3_cliMode', TRUE);
define('TYPO3_PROCEED_IF_NO_USER', 1);

if (!isset($_SERVER['TYPO3_ROOTPATH'])) {
	$_SERVER['TYPO3_ROOTPATH'] = '/Users/helmut/Sites/htdocs/master/';
}
//$_SERVER['TYPO3_SCRIPT'] = $_SERVER['TYPO3_ROOTPATH'] . 'tests/Build/UnitTestBootstrap.php';
$_SERVER['TYPO3_SCRIPT'] = __FILE__;

$_SERVER['ORIG_PATH_INFO'] =
$_SERVER['PATH_INFO'] =
$_SERVER['ORIG_SCRIPT_NAME'] =
$_SERVER['SCRIPT_NAME'] = '/typo3';
$_SERVER['HTTP_HOST'] = 'test.tld';




require('../../typo3/classes/Bootstrap.php');
Typo3_Bootstrap::getInstance()
//	->startOutputBuffering()
	->baseSetup()
//	->registerExtDirectComponents()
	->checkLocalconfExistsOrDie()
	->setGlobalDatabaseVariablesToEmptyString()
	->loadMainConfigurationFile()
	->defineTypo3DatabaseConstants()
	->initializeCachingFramework()
	->registerAutoloader()
//	->checkUtf8DatabaseSettingsOrDie()
//	->transferDeprecatedCurlSettings()
//	->setCacheHashOptions()
//	->enforceCorrectProxyAuthScheme()
	->setDefaultTimezone()
//	->initializeL10nLocales()
	->configureImageProcessingOptions()
//	->convertPageNotFoundHandlingToBoolean()
	->registerGlobalDebugFunctions()
//	->registerSwiftMailer()
	->configureExceptionHandling()
	->setMemoryLimit()
	->defineTypo3RequestTypes()
	->populateTypo3LoadedExtGlobal(FALSE)
	->loadAdditionalConfigurationFromExtensions(FALSE)
	->deprecationLogForOldXclassRegistration()
	->deprecationLogForOldExtCacheSetting()
	->initializeExceptionHandling()
	->requireAdditionalExtensionFiles()
	->setFinalCachingFrameworkCacheConfiguration()
//	->defineLoggingAndExceptionConstants()
	->unsetReservedGlobalVariables()
	->initializeTypo3DbGlobal(FALSE)
//	->checkLockedBackendAndRedirectOrDie()
//	->checkBackendIpOrDie()
//	->checkSslBackendAndRedirectIfNeeded()
//	->redirectToInstallToolIfDatabaseCredentialsAreMissing()
//	->checkValidBrowserOrDie()
//	->establishDatabaseConnection()
	->loadExtensionTables(FALSE)
//	->initializeSpriteManager(TRUE)
//	->initializeBackendUser()
//	->initializeBackendUserMounts()
	->initializeLanguageObject()
//	->initializeModuleMenuObject()
//	->initializeBackendTemplate()
//	->endOutputBufferingAndCleanPreviousOutput()
//	->initializeOutputCompression();


?>
