<?php
/**
 * Initialization file for the Survey extension.
 *
 * Documentation: https://www.mediawiki.org/wiki/Extension:Survey
 * Support: https://www.mediawiki.org/wiki/Extension_talk:Survey
 * Source code: https://gerrit.wikimedia.org/r/gitweb?p=mediawiki/extensions/Survey.git
 *
 * @file Survey.php
 * @ingroup Survey
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'Survey' );
	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['Survey'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['Survey'] = __DIR__ . '/Survey.alias.php';
	wfWarn(
		'Deprecated PHP entry point used for the Survey extension. ' .
		'Please use wfLoadExtension instead, ' .
		'see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return;
} else {
	die( 'This version of the Survey extension requires MediaWiki 1.39+' );
}
