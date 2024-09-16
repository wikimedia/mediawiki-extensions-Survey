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
 * @license GPL-2.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiQuerySurveys extends ApiQueryBase {
	/**
	 * @param ApiMain $main
	 * @param string $action
	 */
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action, 'su' );
	}

	/**
	 * Retrieve the special words from the database.
	 */
	public function execute() {
		$user = $this->getUser();
		if ( !$user->isAllowed( 'surveysubmit' ) || $user->getBlock() ) {
			$this->dieWithError( [ 'badaccess-groups' ] );
		}

		// Get the requests parameters.
		$params = $this->extractRequestParams();

		if ( !( ( isset( $params['ids'] ) && count( $params['ids'] ) > 0 )
			 xor ( isset( $params['names'] ) && count( $params['names'] ) > 0 )
			 ) ) {
			$this->dieWithError( $this->msg( 'survey-err-ids-xor-names' )->text(), 'ids-xor-names' );
		}

		$this->addTables( 'surveys' );

		$starPropPosition = array_search( '*', $params['props'] );

		if ( $starPropPosition !== false ) {
			unset( $params['props'][$starPropPosition] );
			$params['props'] = array_merge( $params['props'], Survey::getFieldNames() );
		}

		$fields = array_merge( [ 'id' ], $params['props'] );

		$this->addFields( Survey::getPrefixedFields( $fields ) );

		if ( isset( $params['ids'] ) ) {
			$this->addWhere( [ 'survey_id' => $params['ids'] ] );
		} else {
			$this->addWhere( [ 'survey_name' => $params['names'] ] );
		}

		if ( !$user->isAllowed( 'surveyadmin' ) ) {
			$this->addWhere( [ 'survey_enabled' => 1 ] );
		} elseif ( isset( $params['enabled'] ) ) {
			$this->addWhere( [ 'survey_enabled' => $params['enabled'] ] );
		}

		if ( isset( $params['continue'] ) ) {
			$this->addWhere( 'survey_id >= ' . wfGetDB( DB_REPLICA )->addQuotes( $params['continue'] ) );
		}

		$this->addOption( 'LIMIT', $params['limit'] + 1 );
		$this->addOption( 'ORDER BY', 'survey_id ASC' );

		$surveys = $this->select( __METHOD__ );
		$count = 0;
		$resultSurveys = [];

		foreach ( $surveys as $survey ) {
			if ( ++$count > $params['limit'] ) {
				// We've reached the one extra which shows that
				// there are additional pages to be had. Stop here...
				$this->setContinueEnumParameter( 'continue', $survey->survey_id );
				break;
			}

			$surveyObject = Survey::newFromDBResult( $survey );

			if ( $params['incquestions'] ) {
				$surveyObject->loadQuestionsFromDB();
			}

			$resultSurveys[] = $this->getSurveyData( $surveyObject->toArray( $fields ) );
		}

		$this->getResult()->setIndexedTagName( $resultSurveys, 'survey' );

		$this->getResult()->addValue(
			null,
			'surveys',
			$resultSurveys
		);
	}

	/**
	 * @since 0.1
	 *
	 * @param array $survey
	 *
	 * @return array $survey
	 */
	protected function getSurveyData( array $survey ) {
		foreach ( $survey['questions'] as $nr => $question ) {
			$this->getResult()->setIndexedTagName( $survey['questions'][$nr], 'answer' );
		}

		$this->getResult()->setIndexedTagName( $survey['questions'], 'question' );

		return $survey;
	}

	/** @inheritDoc */
	public function getAllowedParams() {
		return [
			'ids' => [
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_ISMULTI => true,
			],
			'names' => [
				ApiBase::PARAM_TYPE => 'string',
				ApiBase::PARAM_ISMULTI => true,
			],
			'props' => [
				ApiBase::PARAM_TYPE => array_merge( Survey::getFieldNames(), [ '*' ] ),
				ApiBase::PARAM_ISMULTI => true,
				ApiBase::PARAM_DFLT => 'id|name|enabled'
			],
			'incquestions' => [
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_DFLT => 0,
			],
			'enabled' => [
				ApiBase::PARAM_TYPE => 'integer',
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
	}

	/** @inheritDoc */
	protected function getExamples() {
		return [
			'api.php?action=query&list=surveys&suids=4|2',
			'api.php?action=query&list=surveys&suenabled=1&suprops=id|name',
		];
	}

}
