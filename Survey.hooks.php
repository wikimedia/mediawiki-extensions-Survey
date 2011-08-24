<?php

/**
 * Static class for hooks handled by the Survey extension.
 *
 * @since 0.1
 *
 * @file Survey.hooks.php
 * @ingroup Survey
 *
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class SurveyHooks {
	
	/**
	 * Schema update to set up the needed database tables.
	 *
	 * @since 0.1
	 *
	 * @param DatabaseUpdater $updater
	 *
	 * @return true
	 */
	public static function onSchemaUpdate( /* DatabaseUpdater */ $updater = null ) {
		global $wgDBtype;

		$updater->addExtensionUpdate( array(
			'addTable',
			'surveys',
			dirname( __FILE__ ) . '/Survey.sql',
			true
		) );
		$updater->addExtensionUpdate( array(
			'addTable',
			'survey_questions',
			dirname( __FILE__ ) . '/Survey.sql',
			true
		) );
		$updater->addExtensionUpdate( array(
			'addTable',
			'survey_submissions',
			dirname( __FILE__ ) . '/Survey.sql',
			true
		) );
		$updater->addExtensionUpdate( array(
			'addTable',
			'survey_answers',
			dirname( __FILE__ ) . '/Survey.sql',
			true
		) );
		$updater->addExtensionUpdate( array(
			'addIndex',
			'surveys',
			'surveys_survey_name',
			dirname( __FILE__ ) . '/sql/Survey_indexSurveyName.sql',
			true
		) );

		return true;
	}
	
	/**
	 * Hook to add PHPUnit test cases.
	 * 
	 * @since 0.1
	 * 
	 * @param array $files
	 */
	public static function registerUnitTests( array &$files ) {
		$testDir = dirname( __FILE__ ) . '/test/';
		
		$files[] = $testDir . 'SurveyQuestionTest.php';
		
		return true;
	}
	
}
