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
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiEditSurvey extends ApiBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		$user = $this->getUser();

		if ( !$user->isAllowed( 'surveyadmin' ) || $user->getBlock() ) {
			$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}

		$params = $this->extractRequestParams();

		foreach ( $params['questions'] as &$question ) {
			$question = SurveyQuestion::newFromUrlData( $question );
		}

		$survey = new Survey( Survey::getValidFields( $params, $params['id'] ) );

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
			$survey->getField( 'name' )
		);
	}

	public function needsToken() {
		return 'csrf';
	}

	public function getTokenSalt() {
		return 'editsurvey';
	}

	public function mustBePosted() {
		return true;
	}

	public function getAllowedParams() {
		$params = array(
			'id' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
			),
			'questions' => array(
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_REQUIRED => true,
			),
			'token' => null,
		);

		return array_merge( Survey::getAPIParams(), $params );
	}

	protected function getExamples() {
		return array(
			'api.php?action=editsurvey&',
		);
	}

}
