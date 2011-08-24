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
class SurveyQuestion {
	
	/**
	 * The ID of the question DB record, or null if not inserted yet.
	 * 
	 * @since 0.1
	 * @var integer|null
	 */
	protected $id;
	
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
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param integer|null $id
	 * @param string $text
	 * @param integer $type
	 * @param boolean $required
	 * @param array $answers
	 */
	public function __construct( $id, $text, $type, $required, array $answers = array() ) {
		$this->id = $id;
		$this->text = $text;
		$this->type = $type;
		$this->required = $required;
		$this->answers = $answers;
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
	protected static function getQuestionsForSurvey( $surveyId ) {
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
	protected static function getQuestionsFromDB( array $conditions ) {
		$dbr = wfgetDB( DB_SLAVE );
		
		$questions = $dbr->select(
			'survey_questions',
			array(
				'question_id',
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
		
		foreach ( $questions as &$question ) {
			$question = new SurveyQuestion(
				$question->question_id,
				$question->question_text,
				$question->question_type,
				$question->question_required,
				unserialize( $question->question_answers )
			);
		}
		
		return $questions;
	}
	
	/**
	 * Writes the question to the database, either updating it
	 * when it already exists, or inserting it when it doesn't.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	public function writeToDB() {
		if ( is_null( $this->id ) ) {
			return $this->insertIntoDB();
		}
		else {
			return  $this->updateInDB();
		}
	}
	
	/**
	 * Updates the question in the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	protected function updateInDB() {
		$dbr = wfGetDB( DB_MASTER );
		
		return $dbr->update(
			'survey_questions',
			$this->getWriteValues(),
			array( 'question_id' => $this->id )
		);
	}
	
	/**
	 * Inserts the question into the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	protected function insertIntoDB() {
		$dbr = wfGetDB( DB_MASTER );
		
		$result = $dbr->insert(
			'survey_questions',
			$this->getWriteValues()
		);
		
		$this->id = $dbr->insertId();
		
		return $result;
	}
	
	/**
	 * Gets the fields => values to write to the survey_questions table. 
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	protected function getWriteValues() {
		return array(
			'question_text' => $this->text,
			'question_type' => $this->type,
			'question_required' => $this->required,
			'question_answers' => serialize( $this->answers ), 
		);
	}
	
	/**
	 * Returns the question database id.
	 * 
	 * @since 0.1
	 * 
	 * @return integer|null
	 */
	public function getId() {
		return $this->id;
	}
	
}
