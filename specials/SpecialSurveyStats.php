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
		$this->displaySummary( $this->getSummaryData( $survey ) );
		
		// TODO: magic
		//$this->displayQuestionStats();
	}
	
	protected function getSummaryData( Survey $survey ) {
		$stats = array();
		
		$stats['name'] = $survey->getField( 'name' );
		$stats['status'] = wfMsg( 'surveys-surveystats-' . ( $survey->getField( 'enabled' ) ? 'enabled' : 'disabled' ) );
		$stats['questioncount'] = count( $survey->getQuestions() ) ;
		$stats['submissioncount'] = SurveySubmission::count( array( 'survey_id' => $survey->getId() ) );
		
		return $stats;
	}
	
	protected function displaySummary( array $stats ) {
		$out = $this->getOutput();
		
		$out->addHTML( Html::openElement( 'table', array( 'class' => 'wikitable survey-stats' ) ) );
		
		foreach ( $stats as $stat => $value ) {
			$out->addHTML( '<tr>' );
			
			$out->addHTML( Html::element(
				'th',
				array( 'class' => 'survey-stat-name' ),
				wfMsg( 'surveys-surveystats-' . $stat )
			) );
			
			$out->addHTML( Html::element(
				'td',
				array( 'class' => 'survey-stat-value' ),
				$value
			) );
			
			$out->addHTML( '</tr>' );
		}
		
		$out->addHTML( Html::closeElement( 'table' ) );
	}
	
	protected function displayQuestionStats( SurveyQuestion $question ) {
		
	}
	
}
