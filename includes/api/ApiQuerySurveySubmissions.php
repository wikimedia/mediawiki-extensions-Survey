<?php
/**
 * API module to get a list of survey submissions.
 *
 * @since 0.1
 *
 * @file ApiQuerySurveySubmissions.php
 * @ingroup Surveys
 * @ingroup API
 *
 * @license GPL-2.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiQuerySurveySubmissions extends ApiQueryBase {
	/**
	 * @param ApiMain $main
	 * @param string $action
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action, 'qs' );
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
			$params['props'] = array_merge( $params['props'], SurveySubmission::getFieldNames() );
		}

		$params = array_filter( $params, static function ( $param ) {
			return $param !== null;
		} );

		$results = SurveySubmission::select(
			$params['props'],
			SurveySubmission::getValidFields( $params ),
			[
				'LIMIT' => $params['limit'] + 1,
				'ORDER BY' => SurveySubmission::getPrefixedField( 'id' ) . ' ASC'
			]
		);

		$serializedResults = [];
		$count = 0;

		/**
		 * @var $result SurveyDBClass
		 */
		foreach ( $results as $result ) {
			if ( ++$count > $params['limit'] ) {
				// We've reached the one extra which shows that
				// there are additional pages to be had. Stop here...
				$this->setContinueEnumParameter( 'continue', $result->getId() );
				break;
			}

			$serializedResults[] = $result->toArray();
		}

		$this->getResult()->setIndexedTagName( $serializedResults, 'submission' );

		$this->getResult()->addValue(
			null,
			'submissions',
			$serializedResults
		);
	}

	/** @inheritDoc */
	public function getAllowedParams() {
		$params = [
			'props' => [
				ApiBase::PARAM_TYPE => array_merge( SurveySubmission::getFieldNames(), [ '*' ] ),
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

		return array_merge( SurveySubmission::getAPIParams( false ), $params );
	}

	/** @inheritDoc */
	protected function getExamples() {
		return [
			'api.php?action=query&list=surveysubmissions&qsid=42',
			'api.php?action=query&list=surveysubmissions&qssurvey_id=9001',
			'api.php?action=query&list=surveysubmissions&qsuser_name=Jeroen%20De%20Dauw&qsprops=survey_id|page_id|time',
		];
	}

}
