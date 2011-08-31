<?php

/**
 * API module to submit surveys.
 *
 * @since 0.1
 *
 * @file ApiSubmitSurvey.php
 * @ingroup Survey
 * @ingroup API
 *
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiSubmitSurvey extends ApiBase {
	
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}
	
	public function execute() {
		global $wgUser;
		
		if ( !$wgUser->isAllowed( 'surveysubmit' ) || $wgUser->isBlocked() ) {
			$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}			
		
		$params = $this->extractRequestParams();
		
		if ( !( isset( $params['id'] ) XOR isset( $params['name'] ) ) ) {
			$this->dieUsage( wfMsgExt( 'survey-err-id-xor-name' ), 'id-xor-name' );
		}
		
		if ( isset( $params['name'] ) ) {
			$survey = Survey::newFromName( $params['name'], false );
			
			if ( $survey === false ) {
				$this->dieUsage( wfMsgExt( 'survey-err-survey-name-unknown', 'parsemag', $params['name'] ), 'survey-name-unknown' );
			}
			
			$params['id'] = $survey->getId();
		}
		
		$submission = new SurveySubmission();
	}

	public function needsToken() {
		return true;
	}
	
	public function getTokenSalt() {
		return '';
	}
	
	public function getAllowedParams() {
		return array(
			'id' => array(
				ApiBase::PARAM_TYPE => 'integer',
			),
			'name' => array(
				ApiBase::PARAM_TYPE => 'string',
			),
			'token' => null,
		);
	}
	
	public function getParamDescription() {
		return array(
			'id' => 'The ID of the survey being submitted.',
			'name' => 'The name of the survey being submitted.',
			'token' => 'Edit token. You can get one of these through prop=info.',
		);
	}
	
	public function getDescription() {
		return array(
			''
		);
	}
	
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
		) );
	}

	protected function getExamples() {
		return array(
			'api.php?action=submitsurvey&',
		);
	}	
	
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}		
	
}
