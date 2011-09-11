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
	 * Register the survey tag extension when the parser initializes.
	 * 
	 * @since 0.1
	 * 
	 * @param Parser $parser
	 * 
	 * @return true
	 */
	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'survey', __CLASS__ . '::onSurveyRender' );
		return true;
	}
	
	/**
	 * Render the survey tag.
	 * 
	 * @since 0.1
	 * 
	 * @param mixed $input
	 * @param array $args
	 * @param Parser $parser
	 * @param PPFrame $frame
	 */
	public static function onSurveyRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		$tag = new SurveyTag( $args, $input );
		return $tag->render( $parser );
	}
	
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
		$updater->addExtensionUpdate( array(
			'addIndex',
			'survey_answers',
			'answer_question_id',
			dirname( __FILE__ ) . '/sql/Survey_indexQuestionId.sql',
			true
		) );
		$updater->addExtensionUpdate( array(
			'addIndex',
			'survey_answers',
			'answer_submission_id',
			dirname( __FILE__ ) . '/sql/Survey_indexSubmissionId.sql',
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
