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
class SurveySubmission extends SurveyDBClass {
	
	/**
	 * The ID of the survey this submission is for.
	 * 
	 * @since 0.1
	 * @var integer
	 */
	protected $surveyId;
	
	/**
	 * The ID of the page this submission was made on.
	 * 
	 * @since 0.1
	 * @var integer
	 */
	protected $pageId;
	
	/**
	 * The name of the user that made the submission (username or ip).
	 * 
	 * @since 0.1
	 * @var string
	 */
	protected $userName;
	
	/**
	 * Timestamp idnicating when the submission was made.
	 * 
	 * @since 0.1
	 * @var string
	 */
	protected $timeStamp;
	
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param integer|null $id
	 * @param integer $surveyId
	 * @param string $userName
	 * @param integer $pageId
	 * @param string $timeStamp
	 */
	public function __construct( $id, $surveyId, $userName, $pageId, $timeStamp ) {
		$this->id = $id;
		$this->surveyId = $surveyId;
		$this->userName = $userName;
		$this->pageId = $pageId;
		$this->timeStamp = $timeStamp;
	}
	
	/**
	 * @see SurveyDBClass::getDBTable()
	 */
	protected function getDBTable() {
		return 'survey_submissions';
	}
	
	/**
	 * @see SurveyDBClass::getIDField()
	 */
	protected function getIDField() {
		return 'submission_id';
	}
	
	/**
	 * Gets the fields => values to write to the survey_sumissions table. 
	 * 
	 * @since 0.1
	 * 
	 * @return array
	 */
	protected function getWriteValues() {
		return array(
			'submission_survey_id' => $this->surveyId,
			'submission_user_name' => $this->userName,
			'submission_page_id' => $this->pageId,
			'submission_time' => $this->timeStamp,
		);
	}
	
}
