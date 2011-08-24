<?php

/**
 * Simple survey submission object class.
 * 
 * @since 0.1
 * 
 * @file SurveySubmission.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SurveyAnswer {
	
	/**
	 * The ID of the question DB record, or null if not inserted yet.
	 * 
	 * @since 0.1
	 * @var integer|null
	 */
	protected $id;
	
	/**
	 * The answer text.
	 * 
	 * @since 0.1
	 * @var string
	 */
	protected $text;
	
	/**
	 * The ID of the submission this answer is part of.
	 * 
	 * @since 0.1
	 * @var integer
	 */
	protected $submissionId;
	
	/**
	 * The ID of the question this answer corresponds to.
	 * 
	 * @since 0.1
	 * @var integer
	 */
	protected $questionId;
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param integer|null $id
	 * @param integer $submissionId
	 * @param integer $questionId
	 * @param string $text
	 */
	public function __construct( $id, $submissionId, $questionId, $text ) {
		$this->id = $id;
		$this->submissionId = $submissionId;
		$this->questionId = $questionId;
		$this->text = $text;
	}
	
	/**
	 * Writes the answer to the database, either updating it
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
	 * Updates the answer in the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	protected function updateInDB() {
		$dbr = wfGetDB( DB_MASTER );
		
		return $dbr->update(
			'survey_answers',
			$this->getWriteValues(),
			array( 'answer_id' => $this->id )
		);
	}
	
	/**
	 * Inserts the answer into the database.
	 * 
	 * @since 0.1
	 * 
	 * @return boolean Success indicator
	 */
	protected function insertIntoDB() {
		$dbr = wfGetDB( DB_MASTER );
		
		$result = $dbr->insert(
			'survey_answers',
			$this->getWriteValues()
		);
		
		$this->id = $dbr->insertId();
		
		return $result;
	}
	
	/**
	 * Gets the fields => values to write to the survey_answers table. 
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	protected function getWriteValues() {
		return array(
			'answer_submission_id' => $this->submissionId,
			'answer_question_id' => $this->questionId,
			'answer_text' => $this->text,
		);
	}
	
	/**
	 * Returns the answer database id.
	 * 
	 * @since 0.1
	 * 
	 * @return integer|null
	 */
	public function getId() {
		return $this->id;
	}
	
}
