<?php

/**
 * Statistics interface for surveys.
 * 
 * @since 0.1
 * 
 * @file SpecialSurveyStats.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SpecialSurveyStats extends SpecialSurveyPage {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 */
	public function __construct() {
		parent::__construct( 'SurveyStats', 'surveyadmin' );
	}
	
	/**
	 * Main method.
	 * 
	 * @since 0.1
	 * 
	 * @param string $arg
	 */
	public function execute( $subPage ) {
		parent::execute( $subPage );
		
		if ( is_null( $subPage ) || trim( $subPage ) === '' ) {
			$this->getOutput()->redirect( SpecialPage::getTitleFor( 'Surveys' )->getLocalURL() );
		} else {
			$subPage = trim( $subPage );
			
			if ( Survey::has( array( 'name' => $subPage ) ) ) {
				$this->displayStats( Survey::newFromName( $subPage ) );
			}
			else {
				$this->showError( 'surveys-surveystats-nosuchsurvey' );
			}
		}
	}
	
	protected function displayStats( Survey $survey ) {
		// TODO
	}
	
}
