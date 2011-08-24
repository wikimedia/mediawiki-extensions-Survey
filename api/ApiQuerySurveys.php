<?php

/**
 * API module to get a list of surveys.
 *
 * @since 0.1
 *
 * @file ApiQuerySurveys.php
 * @ingroup Surveys
 * @ingroup API
 *
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiQuerySurveys extends ApiQueryBase {
	
	public function __construct( $main, $action ) {
		parent :: __construct( $main, $action, 'su' );
	}

	/**
	 * Retrieve the specil words from the database.
	 */
	public function execute() {
		global $wgUser;
		
		if ( $wgUser->isBlocked() ) {
			$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}		
		
		// Get the requests parameters.
		$params = $this->extractRequestParams();
		
		$surveys = array();

		foreach ( $params['ids'] as $surveyId ) {
			$survey = Survey::newFromId( $surveyId, $params['incquestions'] == 1 )->toArray();
			$this->getResult()->setIndexedTagName( $survey['questions'], 'question' );
			$surveys[] = $survey;
		}

		$this->getResult()->setIndexedTagName( $surveys, 'survey' );
		
		$this->getResult()->addValue(
			null,
			'surveys',
			$surveys
		);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see includes/api/ApiBase#getAllowedParams()
	 */
	public function getAllowedParams() {
		return array (
			'ids' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => true,
			),
			'incquestions' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => '0',
			),
			'limit' => array(
				ApiBase :: PARAM_DFLT => 20,
				ApiBase :: PARAM_TYPE => 'limit',
				ApiBase :: PARAM_MIN => 1,
				ApiBase :: PARAM_MAX => ApiBase :: LIMIT_BIG1,
				ApiBase :: PARAM_MAX2 => ApiBase :: LIMIT_BIG2
			),
			'continue' => null,
		);
		
	}

	/**
	 * (non-PHPdoc)
	 * @see includes/api/ApiBase#getParamDescription()
	 */
	public function getParamDescription() {
		return array (
			'ids' => 'The IDs of the surveys to return',
			'incquestions' => 'Include the questions of the surveys or not',
			'continue' => 'Offset number from where to continue the query',
			'limit'   => 'Max amount of words to return',
		);
	}

	/**
	 * (non-PHPdoc)
	 * @see includes/api/ApiBase#getDescription()
	 */
	public function getDescription() {
		return 'API module for obatining surveys and optionaly their questions';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see includes/api/ApiBase#getPossibleErrors()
	 */
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'surveyids', 'name' ),
		) );
	}	
	
	/**
	 * (non-PHPdoc)
	 * @see includes/api/ApiBase#getExamples()
	 */
	protected function getExamples() {
		return array (
			'api.php?action=query&list=surveys&',
		);
	}

	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}	
	
}
