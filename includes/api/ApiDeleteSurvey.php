<?php

/**
 * API module to delete surveys.
 *
 * @since 0.1
 *
 * @file ApiDeleteSurvey.php
 * @ingroup Survey
 * @ingroup API
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiDeleteSurvey extends ApiBase {

	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}

	public function execute() {
		$user = $this->getUser();

		if ( !$user->isAllowed( 'surveyadmin' ) || $user->getBlock() ) {
			$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}

		$params = $this->extractRequestParams();

		$everythingOk = true;

		foreach ( $params['ids'] as $id ) {
			$surey = new Survey( array( 'id' => $id ) );
			$everythingOk = $surey->removeFromDB() && $everythingOk;
		}

		$this->getResult()->addValue(
			null,
			'success',
			$everythingOk
		);
	}

	public function needsToken() {
		return 'csrf';
	}

	public function getTokenSalt() {
		$params = $this->extractRequestParams();
		return $this->getWebUITokenSalt( $params );
	}

	protected function getWebUITokenSalt( array $params ) {
		return 'deletesurvey' . implode( '|', $params['ids'] );
	}

	public function mustBePosted() {
		return true;
	}

	public function getAllowedParams() {
		return array(
			'ids' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => true,
			),
			'token' => null,
		);
	}

	protected function getExamples() {
		return array(
			'api.php?action=deletesurvey&ids=42',
			'api.php?action=deletesurvey&ids=4|2',
		);
	}

}
