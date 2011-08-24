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
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class ApiDeleteSurvey extends ApiBase {
	
	public function __construct( $main, $action ) {
		parent::__construct( $main, $action );
	}
	
	public function execute() {
		global $wgUser;
		
		if ( !$wgUser->isAllowed( 'surveyadmin' ) || $wgUser->isBlocked() ) {
			$this->dieUsageMsg( array( 'badaccess-groups' ) );
		}			
		
		$params = $this->extractRequestParams();
		
		$everythingOk = true;
		
		foreach ( $params['ids'] as $id ) {
			$surey = new Survey( $id );
			$everythingOk = $surey->removeFromDB() && $everythingOk;
		}
		
		$this->getResult()->addValue(
			null,
			'success',
			$everythingOk
		);
	}
	
	public function getAllowedParams() {
		return array(
			'ids' => array(
				ApiBase::PARAM_TYPE => 'integer',
				ApiBase::PARAM_REQUIRED => true,
				ApiBase::PARAM_ISMULTI => true,
			),
		);
	}
	
	public function getParamDescription() {
		return array(
			'ids' => 'The IDs of the surveys to delete'
		);
	}
	
	public function getDescription() {
		return array(
			'API module for deleting surveys.'
		);
	}
	
	public function getPossibleErrors() {
		return array_merge( parent::getPossibleErrors(), array(
			array( 'missingparam', 'ids' ),
		) );
	}

	protected function getExamples() {
		return array(
			'api.php?action=deletesurvey&ids=42',
			'api.php?action=deletesurvey&ids=4|2',
		);
	}	
	
	public function getVersion() {
		return __CLASS__ . ': $Id$';
	}		
	
}
