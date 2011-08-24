<?php

/**
 * Internationalization file for the Survey extension.
 *
 * @since 0.1
 *
 * @file Survey.i18n.php
 * @ingroup Survey
 *
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

$messages = array();

/** English
 * @author Jeroen De Dauw
 */
$messages['en'] = array(
	'survey-desc' => 'Survey tool for MediaWiki',

	'right-surveyadmin' => 'Manage surveys',
	'right-surveysubmit' => 'Participate in surveys',

	'special-surveys' => 'Surveys admin',
	'special-survey' => 'Survey admin',
	'special-surveystats' => 'Survey statistics',

	'survey-err-id-xor-name' => 'You need to provide either the id or the name of the survey to submit',
	'survey-err-survey-name-unknown' => 'There is no survey with the name "$1"',
	'survey-err-duplicate-name' => 'There already is a survey with name "$1"',
);
