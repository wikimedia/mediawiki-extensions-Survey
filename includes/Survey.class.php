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
class Survey {
	
	/**
	 * The ID of the surveys DB record, or null if not inserted yet.
	 * 
	 * @since 0.1
	 * @var integer|null
	 */
	protected $id;
	
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
	
	public static function newFromDBResult() {
		
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
	public function __construct( $id, $name, $enabled, array $questions ) {
		$this->id = $id;
		$this->name = $name;
		$this->enabled = $enabled;
		$this->questions = $questions;
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
		if ( is_null( $this->id ) ) {
			return $this->insertIntoDB();
		}
		else {
			return  $this->updateInDB();
		}
	}
	
	/**
	 * Updates the group in the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	protected function updateInDB() {
		$dbr = wfGetDB( DB_MASTER );
		
		return $dbr->update(
			'surveys',
			$this->getWriteValues(),
			array( 'survey_id' => $this->id )
		);
	}
	
	/**
	 * Inserts the group into the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	protected function insertIntoDB() {
		$dbr = wfGetDB( DB_MASTER );
		
		$result = $dbr->insert(
			'surveys',
			$this->getWriteValues()
		);
		
		$this->id = $dbr->insertId();
		
		return $result;
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
	 * Returns the survey database id.
	 * 
	 * @since 0.1
	 * 
	 * @return integer|null
	 */
	public function getId() {
		return $this->id;
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
	 * Returns the surveys questions.
	 * 
	 * @since 0.1
	 * 
	 * @return array of SurveyQuestion
	 */
	public function getQuestions() {
		return $this->questions;
	}
	
}