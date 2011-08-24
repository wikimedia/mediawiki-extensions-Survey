<?php

/**
 * API module to edit surveys.
 *
 * @since 0.1
 *
 * @file ApiEditSurvey.php
 * @ingroup Survey
 * @ingroup API
 *
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiEditSurvey extends ApiBase {
	
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}
	
	public function execute() {
		global $wgUser;
		
		if ( !$wgUser->isAllowed( 'surveyadmin' ) || $wgUser->isBlocked() ) {
			$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}			
		
		$params = $this->extractRequestParams();
		
//		$q = new SurveyQuestion(null, 'new q', 1, false);
//		var_dump($q->toUrlData());exit;
		// eyJ0ZXh0IjoibmV3IHEiLCJ0eXBlIjoxLCJyZXF1aXJlZCI6ZmFsc2UsImFuc3dlcnMiOltdfQ!!
		
		foreach ( $params['questions'] as &$question ) {
			$question = SurveyQuestion::newFromUrlData( $question );
		}
		
		$survey = new Survey(
			$params['id'],
			$params['name'],
			$params['enabled'] == 1,
			$params['questions']
		);
		
		$this->getResult()->addValue(
			null,
			'success',
			$survey->writeToDB()
		);
		
		$this->getResult()->addValue(
			'survey',
			'id',
			$survey->getId()
		);
		
		$this->getResult()->addValue(
			'survey',
			'name',
			$survey->getName()
		);
	}

	public function getAllowedParams() {
		return array(
			'id' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'name' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_REQUIRED => true,
			),
			'enabled' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'questions' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_REQUIRED => true,
			),
		);
	}
	
	public function getParamDescription() {
		return array(
			'id' => 'The ID of the survey to modify',
			'name' => 'The name of the survey',
			'enabled' => 'Enable the survey or not',
			'questions' => 'The questions that make up the survey',
		);
	}
	
	public function getDescription() {
		return array(
			'API module for editing a survey.'
		);
	}
	
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'id' ),
			array( 'missingparam', 'name' ),
			array( 'missingparam', 'enabled' ),
			array( 'missingparam', 'questions' ),
		) );
	}

	protected function getExamples() {
		return array(
			'api.php?action=editsurvey&',
		);
	}	
	
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}		
	
}
