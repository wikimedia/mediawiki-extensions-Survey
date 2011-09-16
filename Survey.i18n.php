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
	'special-editsurvey' => 'Survey admin',
	'special-surveys' => 'Surveys admin',
	'special-surveystats' => 'Survey statistics',
	'special-takesurvey' => 'Take survey',

	// API errors
	'survey-err-id-xor-name' => 'You need to provide either the ID or the name of the survey to submit',
	'survey-err-survey-name-unknown' => 'There is no survey with the name "$1"',
	'survey-err-duplicate-name' => 'There already is a survey with the name "$1"',
	'survey-err-ids-xor-names' => 'You need to provide either the IDs or the names of the surveys to query',

	// Question types
	'survey-question-type-text' => 'Single-line text field',
	'survey-question-type-number' => 'Number',
	'survey-question-type-select' => 'Dropdown menu',
	'survey-question-type-radio' => 'Radio buttons',
	'survey-question-type-textarea' => 'Multi-line text field',
	'survey-question-type-check' => 'Checkbox',

	// User types
	'survey-user-type-all' => 'Everyone',
	'survey-user-type-loggedin' => 'Logged in users',
	'survey-user-type-confirmed' => 'Confirmed users',
	'survey-user-type-editor' => 'Editors',
	'survey-user-type-anon' => 'Anonymous users',

	// Navigation
	'survey-navigation-edit' => '[[Special:Survey/$1|Edit this survey]]',
	'survey-navigation-take' => '[[Special:TakeSurvey/$1|Take this survey]]',
	'survey-navigation-list' => '[[Special:Surveys|Surveys list]]',
	'survey-navigation-stats' => '[[Special:SurveyStats/$1|View statistics]]',

	// Special:Surveys
	'surveys-special-addnew' => 'Add a new survey',
	'surveys-special-namedoc' => 'Enter an unique identifier (ID) for the new survey. Can not be changed later. For example: editor-motivation.',
	'surveys-special-newname' => 'New survey ID:',
	'surveys-special-add' => 'Add survey',
	'surveys-special-existing' => 'Existing surveys',
	'surveys-special-title' => 'Title',
	'surveys-special-status' => 'Status',
	'surveys-special-stats' => 'Statistics',
	'surveys-special-edit' => 'Edit',
	'surveys-special-save' => 'Save',
	'surveys-special-delete' => 'Delete',
	'surveys-special-enabled' => 'Enabled',
	'surveys-special-disabled' => 'Disabled',
	'surveys-special-confirm-delete' => 'Are you sure you want to delete this survey?',
	'surveys-special-delete-failed' => 'Failed to delete survey.',
	'survey-special-label-usertype' => 'Users that should get the survey',
	'survey-special-label-minpages' => 'Minimun amount of pages the user needs to visit before getting the survey',

	// Special:TakeSurvey
	'surveys-takesurvey-loading' => 'Loading survey...',
	'surveys-takesurvey-nosuchsurvey' => 'The requested survey does not exist.',
	'surveys-takesurvey-warn-notenabled' => 'This survey has not been enabled yet, and therefore is not visible to users.',
	'surveys-takesurvey-surveynotenabled' => 'The requested survey has not been enabled yet.',

	// Special:SurveyStats
	'surveys-surveystats-nosuchsurvey' => 'The requested survey does not exist. You can view a [[Special:Surveys|list of available surveys]].',
	'surveys-surveystats-name' => 'Survey ID',
	'surveys-surveystats-title' => 'Survey title',
	'surveys-surveystats-status' => 'Survey status',
	'surveys-surveystats-questioncount' => 'Number of questions',
	'surveys-surveystats-submissioncount' => 'Number of submissions',
	'surveys-surveystats-enabled' => 'Enabled',
	'surveys-surveystats-disabled' => 'Disabled',
	'surveys-surveystats-questions' => 'Question statistics',
	'surveys-surveystats-question-nr' => '#',
	'surveys-surveystats-question-#' => '$1',
	'surveys-surveystats-question-type' => 'Question type',
	'surveys-surveystats-question-text' => 'Question text',
	'surveys-surveystats-question-answercount' => 'Number of answers',
	'surveys-surveystats-question-answers' => 'Most provided answers',
	'surveys-surveystats-question-answer' => '$1 ($2 {{PLURAL:$2|answer|answers}})',
	'surveys-surveystats-unchecked' => 'Unchecked',
	'surveys-surveystats-checked' => 'Checked',

	// Special:Survey
	'surveys-special-unknown-name' => 'There is no survey with the requested ID.',
	'survey-special-label-name' => 'Survey ID',
	'survey-special-label-title' => 'Survey title',
	'survey-special-label-enabled' => 'Survey enabled',
	'survey-special-label-ratio' => 'Percentage of people to show the survey to',
	'survey-special-label-add-first' => 'Add question',
	'survey-special-label-add-another' => 'Add another question',
	'survey-special-label-addquestion' => 'New question',
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

/** Message documentation (Message documentation)
 * @author Jeroen De Dauw
 */
$messages['qqq'] = array(
	'right-surveyadmin' => 'User right',
	'right-surveysubmit' => 'User right',
	'special-editsurvey' => 'Special page name/title',
	'special-surveys' => 'Special page name/title',
	'special-surveystats' => 'Special page name/title',
	'special-takesurvey' => 'Special page name/title',
	'survey-err-id-xor-name' => 'API error message.',
	'survey-err-survey-name-unknown' => 'API error message. $1 is the name of a survey.',
	'survey-err-duplicate-name' => 'API error message. $1 is the name of a survey.',
	'survey-err-ids-xor-names' => 'API error message.',
	'survey-question-type-text' => 'Input type for a question, selectable in administration interface.',
	'survey-question-type-number' => 'Input type for a question, selectable in administration interface.',
	'survey-question-type-select' => 'Input type for a question, selectable in administration interface.',
	'survey-question-type-radio' => 'Input type for a question, selectable in administration interface.',
	'survey-question-type-textarea' => 'Input type for a question, selectable in administration interface.',
	'survey-question-type-check' => 'Input type for a question, selectable in administration interface.',
	'survey-user-type-all' => 'Group of users for which a survey can be available.',
	'survey-user-type-loggedin' => 'Group of users for which a survey can be available.',
	'survey-user-type-confirmed' => 'Group of users for which a survey can be available.',
	'survey-user-type-editor' => 'Group of users for which a survey can be available.',
	'survey-user-type-anon' => 'Group of users for which a survey can be available.',
	'surveys-surveystats-question-answers' => 'Header for a column listing the most provided answers per question',
	'surveys-surveystats-question-answer' => 'Header for a column listing the amount of answers per question',
);

