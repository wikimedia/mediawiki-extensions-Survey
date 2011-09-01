<?php

/**
 * Simple Survey object class.
 * 
 * @since 0.1
 * 
 * @file Survey.class.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Survey extends SurveyDBClass {
	
	/**
	 * The name of the survey.
	 * 
	 * @since 0.1
	 * @var string
	 */
	protected $name;
	
	/**
	 * If the survey is enabled or not.
	 * 
	 * @since 0.1
	 * @var boolean
	 */
	protected $enabled;
	
	/**
	 * The questions that go with this survey.
	 * 
	 * @since 0.1
	 * @var array of SurveyQuestion
	 */
	protected $questions;
	
	/**
	 * @see SurveyDBClass::getDBTable()
	 */
	protected function getDBTable() {
		return 'surveys';
	}
	
	/**
	 * @see SurveyDBClass::getIDField()
	 */
	protected function getIDField() {
		return 'survey_id';
	}
	
	/**
	 * Gets the fields => values to write to the surveys table. 
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	protected function getWriteValues() {
		return array(
			'survey_enabled' => $this->enabled,
			'survey_name' => $this->name, 
		);
	}
	
	/**
	 * Returns the Survey with specified name, or false if there is no such survey.
	 * 
	 * @since 0.1
	 * 
	 * @param string $surveyName
	 * @param boolean $loadQuestions
	 * 
	 * @return Survey or false
	 */
	public static function newFromName( $surveyName, $loadQuestions = true ) {
		return self::newFromDB( array( 'survey_name' => $surveyName ), $loadQuestions );
	}
	
	/**
	 * Returns the Survey with specified ID, or false if there is no such survey.
	 * 
	 * @since 0.1
	 * 
	 * @param integer surveyId
	 * @param boolean $loadQuestions
	 * 
	 * @return Survey or false
	 */
	public static function newFromId( $surveyId, $loadQuestions = true ) {
		return self::newFromDB( array( 'survey_id' => $surveyId ), $loadQuestions );
	}
	
	/**
	 * Returns a new instance of Survey build from a database result
	 * obtained by doing a select with the porvided conditions on the surveys table.
	 * If no survey matches the conditions, false will be returned.
	 * 
	 * @since 0.1
	 * 
	 * @param array $conditions
	 * @param boolean $loadQuestions
	 * 
	 * @return Survey or false
	 */
	protected static function newFromDB( array $conditions, $loadQuestions = true ) {
		$dbr = wfGetDB( DB_SLAVE );
		
		$survey = $dbr->selectRow(
			'surveys',
			array(
				'survey_id',
				'survey_name',
				'survey_enabled',
			),
			$conditions
		);
		
		if ( !$survey ) {
			return false;
		}

		$survey = new self(
			(int)$survey->survey_id,
			$survey->survey_name,
			(int)$survey->survey_enabled == 1
		);
		
		if ( $loadQuestions ) {
			$survey->loadQuestionsFromDB();
		}
		
		return $survey;
	}
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param integer|null $id
	 * @param string $name
	 * @param boolean $enabled
	 * @param array $questions
	 */
	public function __construct( $id, $name = '', $enabled = false, array $questions = array() ) {
		$this->id = $id;
		$this->name = $name;
		$this->enabled = $enabled;
		
		$this->setQuestions( $questions );
	}
	
	/**
	 * Load the surveys questions from the database.
	 * 
	 * @since 0.1
	 */
	public function loadQuestionsFromDB() {
		$this->questions = SurveyQuestion::getQuestionsForSurvey( $this->id );
	}
	
	/**
	 * Writes the survey to the database, either updating it
	 * when it already exists, or inserting it when it doesn't.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	public function writeToDB() {
		$success = parent::writeToDB();
		
		$dbw = wfGetDB( DB_MASTER );
		
		$dbw->begin();
		
		foreach ( $this->questions as /* SurveyQuestion */ $question ) {
			$question->setSurveyId( $this->getId() );
			$success = $question->writeToDB() && $success;
		}
		
		$dbw->commit();
		
		return $success;
	}
	
	
	/**
	 * Returns the survey name.
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}
	
	/**
	 * Sets the survey name.
	 * 
	 * @since 0.1
	 * 
	 * @param string $name
	 */
	public function setName( $name ) {
		$this->name = $name;
	}
	
	/**
	 * Returns if the survey is enabled.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean
	 */
	public function isEnabled() {
		return $this->enabled;
	}
	
	/**
	 * Sets if the survey is enabled or not.
	 * 
	 * @since 0.1
	 * 
	 * @param boolean $enabled
	 */
	public function setEnabled( $enabled ) {
		$this->enabled = $enabled;
	}
	
	/**
	 * Returns the surveys questions.
	 * 
	 * @since 0.1
	 * 
	 * @return array of SurveyQuestion
	 */
	public function getQuestions() {
		return $this->questions;
	}
	
	/**
	 * Sets the surveys questions.
	 * 
	 * @since 0.1
	 * 
	 * @param array $questions list of SurveyQuestion
	 */
	public function setQuestions( array /* of SurveyQuestion */ $questions ) {
		$this->questions = $questions;
	}
	
	/**
	 * Serializes the survey to an associative array which
	 * can then easily be converted into JSON or similar.
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	public function toArray() {
		$data = array(
			'enabled' => $this->enabled,
			'name' => $this->name,
			'questions' => array(),
		);
		
		foreach ( $this->questions as /* SurveyQuestion */ $question ) {
			$data['questions'][] = $question->toArray();
		}
		
		if ( !is_null( $this->id ) ) {
			$data['id'] = $this->id;
		}
		
		return $data;
	}
	
	/**
	 * Removes the object from the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	public function removeFromDB() {
		$dbr= wfgetDB( DB_SLAVE );
		
		$submissionsForSurvey = $dbr->select(
			'survey_submissions',
			array( 'submission_id' ),
			array( 'submission_survey_id' => $this->id )
		);
		
		$dbw = wfgetDB( DB_MASTER );
		
		$dbw->begin();
		
		$sucecss = parent::removeFromDB();
		
		$sucecss = $dbw->delete(
			'survey_questions',
			array( 'question_survey_id' => $this->id )
		) && $sucecss;
		
		$sucecss = $dbw->delete(
			'survey_submissions',
			array( 'submission_survey_id' => $this->id )
		) && $sucecss;
		
		foreach ( $submissionsForSurvey as $nr => $submission ) {
			$sucecss = $dbw->delete(
				'survey_answers',
				array( 'answer_submission_id' => $submission->submission_id )
			) && $sucecss;
			
			if ( $nr % 500 == 0 ) {
				$dbw->commit();
				$dbw->begin();
			}
		}
		
		$dbw->commit();
		
		return $sucecss;
	}
	
}
