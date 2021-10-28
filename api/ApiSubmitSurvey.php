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
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiSubmitSurvey extends ApiBase {
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		$user = $this->getUser();

		if ( !$user->isAllowed( 'surveysubmit' ) || $user->getBlock() ) {
			$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}

		$params = $this->extractRequestParams();

		if ( !( isset( $params['id'] ) XOR isset( $params['name'] ) ) ) {
			$this->dieUsage( $this->msg( 'survey-err-id-xor-name' )->text(), 'id-xor-name' );
		}

		if ( isset( $params['name'] ) ) {
			$survey = Survey::newFromName( $params['name'], null, false );

			if ( $survey === false ) {
				$this->dieUsage( $this->msg( 'survey-err-survey-name-unknown',
						$params['name'] )->text(), 'survey-name-unknown' );
			}
		} else {
			$survey = Survey::newFromId( $params['id'], null, false );

			if ( $survey === false ) {
				$this->dieUsage( $this->msg( 'survey-err-survey-id-unknown',
						$params['id'] )->text(), 'survey-id-unknown' );
			}
		}

		$submission = new SurveySubmission( array(
			'survey_id' => $survey->getId(),
			'page_id' => 0, // TODO
			'user_name' => $user->getName(),
			'time' => wfTimestampNow()
		) );

		foreach ( FormatJson::decode( $params['answers'] ) as $answer ) {
			$submission->addAnswer( SurveyAnswer::newFromArray( (array)$answer ) );
		}

		$submission->writeToDB();
	}

	public function needsToken() {
		return 'csrf';
	}

	public function getTokenSalt() {
		return serialize( array( 'submitsurvey', $this->getUser()->getName() ) );
	}

	public function mustBePosted() {
		return true;
	}

	public function getAllowedParams() {
		return array(
			'id' => array(
				ApiBase::PARAM_TYPE => 'integer',
			),
			'name' => array(
				ApiBase::PARAM_TYPE => 'string',
			),
			'answers' => array(
				ApiBase::PARAM_TYPE => 'string',
			),
			'token' => null,
		);
	}

	protected function getExamples() {
		return array(
			'api.php?action=submitsurvey&',
		);
	}
}
