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
class SurveyAnswer extends SurveyDBClass {
	
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
	 * @see SurveyDBClass::getDBTable()
	 */
	protected function getDBTable() {
		return 'survey_answers';
	}
	
	/**
	 * @see SurveyDBClass::getIDField()
	 */
	protected function getIDField() {
		return 'answer_id';
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
	
}
