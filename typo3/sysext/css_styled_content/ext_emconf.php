<?php

/***************************************************************
* Extension Manager/Repository config file for ext "css_styled_content".
*
* Auto generated 25-10-2011 13:10
*
* Manual updates:
* Only the data in the array - everything else is removed by next
* writing. "version" and "dependencies" must not be touched!
***************************************************************/

$EM_CONF[$_EXTKEY] = array(
	'title' => 'CSS styled content',
	'description' => 'Contains configuration for CSS content-rendering of the table "tt_content". This is meant as a modern substitute for the classic "content (default)" template which was based more on <font>-tags, while this is pure CSS. It is intended to work with all modern browsers (which excludes the NS4 series).',
	'category' => 'fe',
	'shy' => 0,
	'dependencies' => 'cms',
	'conflicts' => '',
	'priority' => 'top',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => 1,
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 1,
	'lockType' => '',
	'author' => 'Kasper Skaarhoj',
	'author_email' => 'kasperYYYY@typo3.com',
	'author_company' => 'Curby Soft Multimedia',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '1.1.0',
	'_md5_values_when_last_written' => 'a:31:{s:21:"ext_conf_template.txt";s:4:"75d9";s:12:"ext_icon.gif";s:4:"1845";s:17:"ext_localconf.php";s:4:"ada5";s:14:"ext_tables.php";s:4:"e4a0";s:15:"flexform_ds.xml";s:4:"22b2";s:16:"locallang_db.xlf";s:4:"a4f1";s:16:"pageTSconfig.txt";s:4:"2520";s:15:"css/example.css";s:4:"86e7";s:24:"css/example_outlines.css";s:4:"85b2";s:14:"css/readme.txt";s:4:"ee9d";s:31:"css/img/background_gradient.gif";s:4:"7681";s:28:"css/img/red_arrow_bullet.gif";s:4:"82d6";s:12:"doc/TODO.txt";s:4:"6534";s:14:"doc/manual.sxw";s:4:"68ac";s:37:"pi1/class.tx_cssstyledcontent_pi1.php";s:4:"b09a";s:17:"pi1/locallang.xlf";s:4:"2a96";s:20:"static/constants.txt";s:4:"16bb";s:20:"static/editorcfg.txt";s:4:"f961";s:16:"static/setup.txt";s:4:"dc0d";s:25:"static/v3.8/constants.txt";s:4:"ce02";s:21:"static/v3.8/setup.txt";s:4:"1068";s:25:"static/v3.9/constants.txt";s:4:"c519";s:21:"static/v3.9/setup.txt";s:4:"7661";s:25:"static/v4.2/constants.txt";s:4:"1a6e";s:21:"static/v4.2/setup.txt";s:4:"3118";s:25:"static/v4.3/constants.txt";s:4:"3945";s:21:"static/v4.3/setup.txt";s:4:"7556";s:25:"static/v4.4/constants.txt";s:4:"be03";s:21:"static/v4.4/setup.txt";s:4:"2a99";s:25:"static/v4.5/constants.txt";s:4:"be03";s:21:"static/v4.5/setup.txt";s:4:"6d58";}',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'php' => '5.3.0-0.0.0',
			'typo3' => '4.6.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'suggests' => array(
	),
);

?>