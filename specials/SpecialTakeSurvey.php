<?php

/**
 * Page on which a survey is displayed.
 * 
 * @since 0.1
 * 
 * @file SpecialTakeSurvey.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SpecialTakeSurvey extends SpecialSurveyPage {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 */
	public function __construct() {
		parent::__construct( 'TakeSurvey', 'surveysubmit' );
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
		
		$this->getOutput()->addWikiText( Xml::element( 
			'survey',
			array(
				'name' => $subPage
			),
			wfMsg( 'surveys-takesurvey-loading' )
		) );
	}
	
}
