<?php
/**
 * API module to get a list of survey answers.
 *
 * @since 0.1
 *
 * @file ApiQuerySurveyAnswers.php
 * @ingroup Surveys
 * @ingroup API
 *
 * @license GPL-2.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiQuerySurveyAnswers extends ApiQueryBase {
	/**
	 * @param ApiMain $main
	 * @param string $action
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action, 'qa' );
	}

	/**
	 * Retrieve the special words from the database.
	 */
	public function execute() {
		$user = $this->getUser();

		if ( !$user->isAllowed( 'surveyadmin' ) || $user->getBlock() ) {
			$this->dieWithError( [ 'badaccess-groups' ] );
		}

		// Get the requests parameters.
		$params = $this->extractRequestParams();

		$starPropPosition = array_search( '*', $params['props'] );

		if ( $starPropPosition !== false ) {
			unset( $params['props'][$starPropPosition] );
			$params['props'] = array_merge( $params['props'], SurveyAnswer::getFieldNames() );
		}

		$params = array_filter( $params, static function ( $param ) {
			return $param !== null;
		} );

		$answers = SurveyAnswer::select(
			$params['props'],
			SurveyAnswer::getValidFields( $params ),
			[
				'LIMIT' => $params['limit'] + 1,
				'ORDER BY' => SurveyAnswer::getPrefixedField( 'id' ) . ' ASC'
			]
		);

		$serializedAnswers = [];
		$count = 0;

		/**
		 * @var $answer SurveyAnswer
		 */
		foreach ( $answers as $answer ) {
			if ( ++$count > $params['limit'] ) {
				// We've reached the one extra which shows that
				// there are additional pages to be had. Stop here...
				$this->setContinueEnumParameter( 'continue', $answer->getId() );
				break;
			}

			$serializedAnswers[] = $answer->toArray();
		}

		$this->getResult()->setIndexedTagName( $serializedAnswers, 'answer' );

		$this->getResult()->addValue(
			null,
			'answers',
			$serializedAnswers
		);
	}

	/** @inheritDoc */
	public function getAllowedParams() {
		$params = [
			'props' => [
				ApiBase::PARAM_TYPE => array_merge( SurveyAnswer::getFieldNames(), [ '*' ] ),
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_DFLT => '*'
			],
			'limit' => [
				ApiBase::PARAM_DFLT => 20,
				ApiBase::PARAM_TYPE => 'limit',
				ApiBase::PARAM_MIN => 1,
				ApiBase::PARAM_MAX => ApiBase::LIMIT_BIG1,
				ApiBase::PARAM_MAX2 => ApiBase::LIMIT_BIG2
			],
			'continue' => null,
		];

		return array_merge( SurveyAnswer::getAPIParams( false ), $params );
	}

	/** @inheritDoc */
	protected function getExamples() {
		return [
			'api.php?action=query&list=surveyanswers&qaid=42',
			'api.php?action=query&list=surveyanswers&qaid=42&qaprops=text|submission_id',
			'api.php?action=query&list=surveyanswers&qaquestion_id=9001&qaprops=text',
		];
	}

}
