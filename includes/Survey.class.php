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
	
	protected static $fields = array(
		'id' => 'int',
		'name' => 'str',
		'enabled' => 'bool',
		'header' => 'str',
		'footer' => 'str',
		'thanks' => 'str',
	);
	
	/**
	 * 
	 * 
	 * @since 0.1
	 * @var string
	 */
	protected static $fieldPrefix = 'survey_';
	
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
	 * Header text to display above the questions.
	 * 
	 * @since 0.1
	 * @var string
	 */
	protected $header;
	
	/**
	 * Footer text to display below the questions.
	 * 
	 * @since 0.1
	 * @var string
	 */
	protected $footer;
	
	/**
	 * Thanks message to display after submission of the survey.
	 * 
	 * @since 0.1
	 * @var string
	 */
	protected $thanksMessage;
	
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
	 * @param array|null $fields
	 * @param boolean $loadQuestions
	 * 
	 * @return Survey or false
	 */
	public static function newFromName( $surveyName, $fields = null, $loadQuestions = true ) {
		return self::newFromDB( array( 'survey_name' => $surveyName ), $fields, $loadQuestions );
	}
	
	/**
	 * Returns the Survey with specified ID, or false if there is no such survey.
	 * 
	 * @since 0.1
	 * 
	 * @param integer surveyId
	 * @param array|null $fields
	 * @param boolean $loadQuestions
	 * 
	 * @return Survey or false
	 */
	public static function newFromId( $surveyId, $fields = null, $loadQuestions = true ) {
		return self::newFromDB( array( 'survey_id' => $surveyId ), $fields, $loadQuestions );
	}
	
	/**
	 * Returns a new instance of Survey build from a database result
	 * obtained by doing a select with the porvided conditions on the surveys table.
	 * If no survey matches the conditions, false will be returned.
	 * 
	 * @since 0.1
	 * 
	 * @param array $conditions
	 * @param array|null $fields
	 * @param boolean $loadQuestions
	 * 
	 * @return Survey or false
	 */
	protected static function newFromDB( array $conditions, $fields = null, $loadQuestions = true ) {
		$dbr = wfGetDB( DB_SLAVE );
		
		if ( is_null( $fields ) ) {
			$fields = array_keys( self::$fields );
		}
		
		foreach ( $fields as &$field ) {
			$field = self::$fieldPrefix . $field;
		} 
		
		$survey = $dbr->selectRow(
			'surveys',
			$fields,
			$conditions
		);
		
		if ( !$survey ) {
			return false;
		}

		$survey = self::newFromDBResult( $survey );
		
		if ( $loadQuestions ) {
			$survey->loadQuestionsFromDB();
		}
		
		return $survey;
	}
	
	protected static function newFromDBResult( $result ) {
		$result = (array)$result;
		$data = array();
		
		foreach ( $result as $name => $value ) {
			$data[substr( $name, strlen( self::$fieldPrefix ) )] = $value;
		}
		
		return self::newFromArray( $data );
	}
	
	protected static function newFromArray( array $data ) {
		$survey = new Survey( array_key_exists( 'id', $data ) ? $data['id'] : null );
		
		$survey->setProps( $data );
		
		return $survey;
	}
	
	/**
	 * Returns the survey fields. 
	 * Field name => db field without prefix
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	public static function getSurveyProps() {
		return array_keys( self::$fields );
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
	 * Returns the surveys header.
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function getHeader() {
		return $this->header;
	}
	
	/**
	 * Returns the surveys footer.
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function getFooter() {
		return $this->footer;
	}
	
	/**
	 * Returns the surveys thanks message.
	 * 
	 * @since 0.1
	 * 
	 * @return string
	 */
	public function getThanks() {
		return $this->thanksMessage;
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
	 * @param null|array $props
	 * 
	 * @return array
	 */
	public function toArray( $props = null ) {
		$data = array(
			'questions' => array(),
		);
		
		foreach ( $this->questions as /* SurveyQuestion */ $question ) {
			$data['questions'][] = $question->toArray();
		}
		
		if ( !is_array( $props ) ) {
			$props = array_keys( self::$fields );
		}
		
		foreach ( $props as $prop ) {
			if ( !( $prop == 'id' && is_null( $this->{ $prop } ) ) ) {
				$data[$prop] = $this->getProp( $prop );
			}
		}
		
		return $data;
	}
	
	public static function fromArray( array $data ) {
		$survey = new Survey( array_key_exists( 'id', $data ) ? $data['id'] : null );
		
		foreach ( $data as $name => $value ) {
			if ( $name != 'id' && array_key_exists( $name, self::$fields ) ) {
				$survey->setProp( $name, $value );
			}
		}
		
		return $survey;
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
	
	public function setProp( $name, $value ) {
		if ( array_key_exists( $name, self::$fields ) ) {
			switch ( self::$fields[$name] ) {
				case 'int':
					$value = (int)$value;
				case 'bool':
					if ( is_string( $value ) ) {
						$value = $value != '0';
					}
			}
			
			$this->{ $name } = $value;
		}
	}
	
	public function getProp( $name ) {
		if ( array_key_exists( $name, self::$fields ) ) {
			return $this->{ $name };
		}
		else {
			// TODO: exception
		}
	}
	
	public function setProps( array $props ) {
		if ( array_key_exists( 'id', $props ) ) {
			unset( $props['id'] );
		}
		
		foreach ( $props as $name => $value ) {
			$this->setProp( $name, $value );
		}
	}
	
}
