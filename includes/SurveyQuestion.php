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
	 * The ID of the survey this question belongs to.
	 * 
	 * @since 0.1
	 * @var integer
	 */
	protected $surveyId;
	
	/**
	 * The question text.
	 * 
	 * @since 0.1
	 * @var string
	 */
	protected $text;
	
	/**
	 * The question type.
	 * 
	 * @since 0.1
	 * @var integer
	 */
	protected $type;
	
	/**
	 * Indicated if the question is required, 
	 * ie if the user can not submit the survey without answering it.
	 * 
	 * @since 0.1
	 * @var boolean
	 */
	protected $required;
	
	/**
	 * List of allowed values for the question.
	 * Empty list for no restrictions.
	 * 
	 * @since 0.1
	 * @var array of string|number
	 */
	protected $answers;
	
	/**
	 * Indicated if the question was removed. 
	 * Removed questions are kept in the db so their answers can
	 * still be used untill the survey itself gets removed.
	 * 
	 * @since 0.1
	 * @var boolean
	 */
	protected $removed;
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param integer|null $id
	 * @param integer $surveyId
	 * @param string $text
	 * @param integer $type
	 * @param boolean $required
	 * @param array $answers
	 * @param boolean $removed
	 */
	public function __construct( $id, $surveyId, $text, $type, $required, array $answers = array(), $removed = false ) {
		$this->id = is_null( $id ) ? $id : (int)$id;
		$this->surveyId = (int)$surveyId;
		$this->text = $text;
		$this->type = $type;
		$this->required = (boolean)$required;
		$this->answers = $answers;
		$this->removed = $removed;
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
	 * Unserializes a survey question in the form of an associative array.
	 * 
	 * @since 0.1
	 * 
	 * @param array $args
	 * 
	 * @return SurveyQuestion
	 */
	public static function newFromArray( array $args ) {
		return new self(
			array_key_exists( 'id', $args ) ? $args['id'] : null,
			array_key_exists( 'surveyId', $args ) ? $args['surveyId'] : null,
			$args['text'],
			$args['type'],
			$args['required'],
			$args['answers'],
			$args['removed']
		);
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
	 * Serializes the survey question to an associative array which
	 * can then easily be converted into JSON or similar.
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	public function toArray() {
		$args = array(
			'surveyId' => $this->surveyId,
			'text' => $this->text,
			'type' => $this->type,
			'required' => $this->required,
			'answers' => $this->answers,
			'removed' => $this->removed,
		);
		
		if ( !is_null( $this->id ) ) {
			$args['id'] = $this->id;
		}
		
		return $args;
	}
	
	/**
	 * Returns the questions for the specified survey.
	 * 
	 * @since 0.1
	 * 
	 * @param integer $surveyId
	 * 
	 * @return array of SurveyQuestion
	 */
	public static function getQuestionsForSurvey( $surveyId ) {
		return self::getQuestionsFromDB( array( 'question_survey_id' => $surveyId ) );
	}
	
	/**
	 * Returns the questions matching the specified conditions.
	 * 
	 * @since 0.1
	 * 
	 * @param array $conditions
	 * 
	 * @return array of SurveyQuestion
	 */
	public static function getQuestionsFromDB( array $conditions ) {
		$dbr = wfgetDB( DB_SLAVE );
		
		$questions = $dbr->select(
			'survey_questions',
			array(
				'question_id',
				'question_survey_id',
				'question_text',
				'question_type',
				'question_required',
				'question_answers',
			),
			$conditions
		);
		
		if ( !$questions ) {
			return array();
		}
		
		$sQuestions = array();
		
		foreach ( $questions as $question ) {
			$sQuestions[] = new SurveyQuestion(
				$question->question_id,
				$question->question_survey_id,
				$question->question_text,
				$question->question_type,
				$question->question_required,
				unserialize( $question->question_answers )
			);
		}
		
		return $sQuestions;
	}
	
	/**
	 * @see SurveyDBClass::getDBTable()
	 */
	protected function getDBTable() {
		return 'survey_questions';
	}
	
	/**
	 * @see SurveyDBClass::getIDField()
	 */
	protected function getIDField() {
		return 'question_id';
	}
	
	/**
	 * Gets the fields => values to write to the survey_questions table. 
	 * 
	 * @since 0.1
	 * 
	 * @param integer $surveyId
	 * 
	 * @return array
	 */
	protected function getWriteValues() {
		return array(
			'question_survey_id' => $this->surveyId,
			'question_text' => $this->text,
			'question_type' => $this->type,
			'question_required' => $this->required,
			'question_answers' => serialize( $this->answers ), 
		);
	}
	
	/**
	 * Gets the question text.
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function getText() {
		return $this->text;
	}
	
	/**
	 * Gets the questions type.
	 * 
	 * @since 0.1
	 * 
	 * @return integer
	 */
	public function getType() {
		return $this->type;
	}
	
	/**
	 * Gets if the question is required.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean
	 */
	public function isRequired() {
		return $this->required;
	}
	
}
