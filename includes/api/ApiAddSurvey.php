<?php
/**
 * API module to add surveys.
 *
 * @since 0.1
 *
 * @file ApiAddSurvey.php
 * @ingroup Survey
 * @ingroup API
 *
 * @license GPL-2.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiAddSurvey extends ApiBase {
	/**
	 * @param ApiMain $main
	 * @param string $action
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		$user = $this->getUser();

		if ( !$user->isAllowed( 'surveyadmin' ) || $user->getBlock() ) {
			$this->dieWithError( [ 'badaccess-groups' ] );
		}

		$params = $this->extractRequestParams();

		foreach ( $params['questions'] as &$question ) {
			$question = SurveyQuestion::newFromUrlData( $question );
		}

		try {
			$survey = new Survey( Survey::getValidFields( $params ) );
			$success = $survey->writeToDB();
		} catch ( DBQueryError $ex ) {
			if ( $ex->errno == 1062 ) {
				$this->dieWithError( $this->msg(
						'survey-err-duplicate-name',
						$params['name']
					)->text(),
					'duplicate-survey-name'
				);
			} else {
				throw $ex;
			}
		}

		$this->getResult()->addValue(
			null,
			'success',
			$success
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

	/**
	 * @return string
	 */
	public function getTokenSalt() {
		return 'addsurvey';
	}

	public function mustBePosted() {
		return true;
	}

	/** @inheritDoc */
	public function getAllowedParams() {
		$params = [
			'questions' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_DFLT => '',
			],
			'token' => null,
		];

		return array_merge( Survey::getAPIParams(), $params );
	}

	/** @inheritDoc */
	protected function getExamples() {
		return [
			'api.php?action=addsurvey&name=My awesome survey&enabled=1&questions=',
		];
	}

}
