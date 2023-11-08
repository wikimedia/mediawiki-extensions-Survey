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
 * @license GPL-2.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiDeleteSurvey extends ApiBase {
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
			$this->dieUsageMsg( [ 'badaccess-groups' ] );
		}

		$params = $this->extractRequestParams();

		$everythingOk = true;

		foreach ( $params['ids'] as $id ) {
			$surey = new Survey( [ 'id' => $id ] );
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

	/**
	 * @return string
	 */
	public function getTokenSalt() {
		$params = $this->extractRequestParams();
		return $this->getWebUITokenSalt( $params );
	}

	/**
	 * @param array $params
	 * @return string
	 */
	protected function getWebUITokenSalt( array $params ) {
		return 'deletesurvey' . implode( '|', $params['ids'] );
	}

	public function mustBePosted() {
		return true;
	}

	/** @inheritDoc */
	public function getAllowedParams() {
		return [
			'ids' => [
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => true,
			],
			'token' => null,
		];
	}

	/** @inheritDoc */
	protected function getExamples() {
		return [
			'api.php?action=deletesurvey&ids=42',
			'api.php?action=deletesurvey&ids=4|2',
		];
	}

}
