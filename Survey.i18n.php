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

	// Rights
	'right-surveyadmin' => 'Manage surveys',
	'right-surveysubmit' => 'Participate in surveys',

	// Special page names
	'special-survey' => 'Survey admin',
	'special-surveys' => 'Surveys admin',
	'special-surveystats' => 'Survey statistics',
	'special-takesurvey' => 'Take survey',

	// API errors
	'survey-err-id-xor-name' => 'You need to provide either the id or the name of the survey to submit',
	'survey-err-survey-name-unknown' => 'There is no survey with the name "$1"',
	'survey-err-duplicate-name' => 'There already is a survey with name "$1"',
	'survey-err-ids-xor-names' => 'You need to provide either the ids or the names of the surveys to query',

	// Special:Surveys
	'surveys-special-addnew' => 'Add a new survey',
	'surveys-special-namedoc' => 'Enter the name for the new survey.',
	'surveys-special-newname' => 'New survey name:',
	'surveys-special-add' => 'Add survey',
	'surveys-special-existing' => 'Existing surveys',
	'surveys-special-name' => 'Name',
	'surveys-special-status' => 'Status',
	'surveys-special-edit' => 'Edit',
	'surveys-special-save' => 'Save',
	'surveys-special-delete' => 'Delete',
	'surveys-special-enabled' => 'Enabled',
	'surveys-special-disabled' => 'Disabled',
	'surveys-special-confirm-delete' => 'Are you sure you want to delete this survey?',
	'surveys-special-delete-failed' => 'Failed to delete survey.',
	'survey-special-label-usertype' => 'Users that should get the survey',
	'survey-user-type-all' => 'Everyone',
	'survey-user-type-loggedin' => 'Logged in users',
	'survey-user-type-confirmed' => 'Confirmed users',
	'survey-user-type-editor' => 'Editors',
	'survey-user-type-anon' => 'Anonymouse users',

	// Special:TakeSurvey
	'surveys-takesurvey-loading' => 'Loading survey...',
	'surveys-takesurvey-nosuchsurvey' => 'There requested survey does not exist.',
	'surveys-takesurvey-warn-notenabled' => 'This survey has not been enabled yet, and therefore is not visible to users.',
	'surveys-takesurvey-surveynotenabled' => 'The requested survey has not been enabled yet.',

	// Special:Survey
	'surveys-special-unknown-name' => 'There is no survey with the requested name.',
	'survey-special-label-name' => 'Survey name',
	'survey-special-label-enabled' => 'Survey enabled',
	'survey-special-label-button' => 'Add question',
	'survey-special-label-addquestion' => 'New question',
	'survey-special-label-add' => 'New question name',
	'survey-question-type-text' => 'Single line text field',
	'survey-question-type-number' => 'Number',
	'survey-question-type-select' => 'Dropdown menu',
	'survey-question-type-radio' => 'Radio boxes',
	'survey-question-type-textarea' => 'Multi line text field',
	'survey-question-type-check' => 'Checkbox',
	'survey-question-label-nr' => 'Question #$1',
	'survey-special-label-required' => 'Question is required',
	'survey-special-label-type' => 'Question type',
	'survey-special-label-text' => 'Question text',
	'survey-special-remove' => 'Remove question',
	'survey-special-remove-confirm' => 'Are you sure you want to remove this question?',
	'survey-special-label-header' => 'Text to display above the survey',
	'survey-special-label-footer' => 'Text to display below the survey',
	'survey-special-label-thanks' => 'Thanks message to display after submission of the survey',
	'survey-special-label-answers' => 'Available answers, one per line.',

	// Survey jQuery
	'survey-jquery-submit' => 'Submit',
	'survey-jquery-finish' => 'Finish',
	'survey-jquery-load-failed' => 'Could not load the survey.',
);
