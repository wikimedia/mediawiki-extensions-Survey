<?php

/**
 * Simple survey question object class.
 * 
 * @since 0.1
 * 
 * @file SurveyQuestion.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SurveyQuestion extends SurveyDBClass {
	
	public static $TYPE_TEXT = 0;
	public static $TYPE_NUMBER = 1;
	public static $TYPE_SELECT = 2;
	public static $TYPE_RADIO = 3;
	
	/**
	 * @see SurveyDBClass::getDBTable()
	 */
	protected static function getDBTable() {
		return 'survey_questions';
	}
	
	/**
	 * Gets the db field prefix. 
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	protected static function getFieldPrefix() {
		return 'question_';
	}
	
	/**
	 * Returns an array with the fields and their types this object contains.
	 * This corresponds directly to the fields in the database, without prefix.
	 * 
	 * survey_id:
	 * The ID of the survey this question belongs to.
	 * This can be null. When written to the db via Survey::writeToDB of
	 * a Survey holding this question, the survey ID will first be set.
	 * 
	 * text:
	 * The question text.
	 * 
	 * type:
	 * The question type.
	 * 
	 * required:
	 * Indicated if the question is required, 
	 * ie if the user can not submit the survey without answering it.
	 * 
	 * answers:
	 * List of allowed values for the question.
	 * Empty list for no restrictions.
	 * 
	 * removed:
	 * Indicated if the question was removed. 
	 * Removed questions are kept in the db so their answers can
	 * still be used untill the survey itself gets removed.
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	protected static function getFieldTypes() {
		return array(
			'id' => 'id',
			'survey_id' => 'int',
			'text' => 'str',
			'type' => 'int',
			'required' => 'bool',
			'answers' => 'array',
			'removed' => 'bool',
		);
	}
	
	/**
	 * Unserialization method for survey question data passed as a multi-value API parameter.
	 * Uses base64 and replaces padding = by !, so the values does not contain any = or |. 
	 * 
	 * @since 0.1
	 * 
	 * @param string $args
	 * 
	 * @return SurveyQuestion
	 */
	public static function newFromUrlData( $args ) {
		$args = (array)FormatJson::decode( base64_decode( str_replace( '!', '=', $args ) ) );
		return self::newFromArray( $args );
	}
	
	/**
	 * Serialization method for survey questions that need to be passed via multi-value API parameter.
	 * Uses base64 and replaces padding = by !, so the values does not contain any = or |.
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function toUrlData() {
		return str_replace( '=', '!', base64_encode( FormatJson::encode( $this->toArray() ) ) );
	}
	
	/**
	 * Returns the questions for the specified survey.
	 * 
	 * @since 0.1
	 * 
	 * @param integer $surveyId
	 * @param boolean $incRemoved
	 * 
	 * @return array of SurveyQuestion
	 */
	public static function getQuestionsForSurvey( $surveyId, $incRemoved = false ) {
		$conditions = array( 'survey_id' => $surveyId );
		
		if ( $incRemoved === false ) {
			$conditions['removed'] = 0;
		}
		
		return self::select( null, $conditions );
	}
	
}
