<?php

/**
 * Initialization file for the Survey extension.
 *
 * Documentation:	 		http://www.mediawiki.org/wiki/Extension:Survey
 * Support					http://www.mediawiki.org/wiki/Extension_talk:Survey
 * Source code:			    http://svn.wikimedia.org/viewvc/mediawiki/trunk/extensions/Survey
 *
 * @file Survey.php
 * @ingroup Survey
 *
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

/**
 * This documenation group collects source code files belonging to Survey.
 *
 * @defgroup Survey Survey
 */

if ( !defined( 'MEDIAWIKI' ) ) {
	die( 'Not an entry point.' );
}

if ( version_compare( $wgVersion, '1.16', '<' ) ) {
	die( '<b>Error:</b> Survey requires MediaWiki 1.16 or above.' );
}

define( 'Survey_VERSION', '0.1 alpha' );

$wgExtensionCredits['other'][] = array(
	'path' => __FILE__,
	'name' => 'Survey',
	'version' => Survey_VERSION,
	'author' => array(
		'[http://www.mediawiki.org/wiki/User:Jeroen_De_Dauw Jeroen De Dauw]',
	),
	'url' => 'http://www.mediawiki.org/wiki/Extension:Survey',
	'descriptionmsg' => 'survey-desc'
);

$wgExtensionMessagesFiles['Survey'] 		= dirname( __FILE__ ) . '/Survey.i18n.php';
$wgExtensionMessagesFiles['SurveyAlias']	= dirname( __FILE__ ) . '/Survey.alias.php';

$wgAutoloadClasses['SurveyHooks'] 			= dirname( __FILE__ ) . '/Survey.hooks.php';
$wgAutoloadClasses['SurveySettings'] 		= dirname( __FILE__ ) . '/Survey.settings.php';

$wgAutoloadClasses['ApiAddSurvey'] 			= dirname( __FILE__ ) . '/api/ApiAddSurvey.php';
$wgAutoloadClasses['ApiDeleteSurvey'] 		= dirname( __FILE__ ) . '/api/ApiDeleteSurvey.php';
$wgAutoloadClasses['ApiEditSurvey'] 		= dirname( __FILE__ ) . '/api/ApiEditSurvey.php';
$wgAutoloadClasses['ApiQuerySurveys'] 		= dirname( __FILE__ ) . '/api/ApiQuerySurveys.php';
$wgAutoloadClasses['ApiSubmitSurvey'] 		= dirname( __FILE__ ) . '/api/ApiSubmitSurvey.php';

$wgAutoloadClasses['Survey'] 				= dirname( __FILE__ ) . '/includes/Survey.class.php';
$wgAutoloadClasses['SurveyAnswer']	 		= dirname( __FILE__ ) . '/includes/SurveyAnswer.php';
$wgAutoloadClasses['SurveyQuestion'] 		= dirname( __FILE__ ) . '/includes/SurveyQuestion.php';
$wgAutoloadClasses['SurveySubmission'] 		= dirname( __FILE__ ) . '/includes/SurveySubmission.php';

$wgAutoloadClasses['SpecialSurveys'] 		= dirname( __FILE__ ) . '/specials/SpecialSurveys.php';
$wgAutoloadClasses['SpecialSurveyStats'] 	= dirname( __FILE__ ) . '/specials/SpecialSurveyStats.php';

$wgSpecialPages['Surveys'] 					= 'SpecialSurveys';
$wgSpecialPages['SurveyStats'] 				= 'SpecialSurveyStats';

$wgSpecialPageGroups['Surveys'] 			= 'other';
$wgSpecialPageGroups['SurveyStats'] 		= 'other';

$wgAPIModules['addsurvey'] 					= 'ApiAddSurvey';
$wgAPIModules['deletesurvey'] 				= 'ApiDeleteSurvey';
$wgAPIModules['editsurvey'] 				= 'ApiEditSurvey';
$wgAPIModules['submitsurvey'] 				= 'ApiSubmitSurvey';
$wgAPIListModules['surveys'] 				= 'ApiQuerySurveys';

$wgHooks['LoadExtensionSchemaUpdates'][] 	= 'SurveyHooks::onSchemaUpdate';

$wgAvailableRights[] = 'surveyadmin';
$wgAvailableRights[] = 'surveysubmit';

# Users that can manage the surveys.
$wgGroupPermissions['*'            ]['surveyadmin'] = false;
$wgGroupPermissions['user'         ]['surveyadmin'] = false;
$wgGroupPermissions['autoconfirmed']['surveyadmin'] = false;
$wgGroupPermissions['bot'          ]['surveyadmin'] = false;
$wgGroupPermissions['sysop'        ]['surveyadmin'] = true;

# Users that can submit surveys.
$wgGroupPermissions['*'            ]['surveysubmit'] = true;
$wgGroupPermissions['user'         ]['surveysubmit'] = true;
$wgGroupPermissions['autoconfirmed']['surveysubmit'] = true;
$wgGroupPermissions['bot'          ]['surveysubmit'] = false;
$wgGroupPermissions['sysop'        ]['surveysubmit'] = true;

$egSurveyScriptPath = $wgExtensionAssetsPath === false ? $wgScriptPath . '/extensions' : $wgExtensionAssetsPath;
$egSurveyScriptPath .= '/Survey/resources';

$moduleTemplate = array(
	'localBasePath' => dirname( __FILE__ ),
	'remoteBasePath' => $egSurveyScriptPath
);

$wgResourceModules['ext.survey'] = $moduleTemplate + array(
	'styles' => array(),
	'scripts' => array(
		'jquery.survey.js'
	),
	'dependencies' => array(),
	'messages' => array(
	)
);

$egSurveySettings = array();
