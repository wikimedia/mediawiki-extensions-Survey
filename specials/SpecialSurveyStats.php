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
		parent::__construct( 'SurveyStats', 'surveyadmin', false );
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
		$this->displayQuestions( $survey );
	}
	
	/**
	 * Gets the summary data.
	 * 
	 * @since 0.1
	 * 
	 * @param Survey $survey
	 * 
	 * @return array
	 */
	protected function getSummaryData( Survey $survey ) {
		$stats = array();
		
		$stats['name'] = $survey->getField( 'name' );
		$stats['status'] = wfMsg( 'surveys-surveystats-' . ( $survey->getField( 'enabled' ) ? 'enabled' : 'disabled' ) );
		$stats['questioncount'] = count( $survey->getQuestions() ) ;
		$stats['submissioncount'] = SurveySubmission::count( array( 'survey_id' => $survey->getId() ) );
		
		return $stats;
	}
	
	/**
	 * Display a summary table with the provided data.
	 * The keys are messages that get prepended with surveys-surveystats-.
	 * message => value
	 * 
	 * @since 0.1
	 * 
	 * @param array $stats
	 */
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
	
	protected function displayQuestions( Survey $survey ) {
		$out = $this->getOutput();
		
		$out->addHTML( '<h2>' . wfMsgHtml( 'surveys-surveystats-questions' ) . '</h2>' );
		
		$out->addHTML( Html::openElement( 'table', array( 'class' => 'wikitable sortable survey-questions' ) ) );
		
		$out->addHTML(
			'<thead><tr>' .
				'<th>' . wfMsgHtml( 'surveys-surveystats-question-nr' ) . '</th>' .
				'<th>' . wfMsgHtml( 'surveys-surveystats-question-type' ) . '</th>' .
				'<th class="unsortable">' . wfMsgHtml( 'surveys-surveystats-question-text' ) . '</th>' .
				'<th>' . wfMsgHtml( 'surveys-surveystats-question-answercount' ) . '</th>' .
				//'<th class="unsortable">' . wfMsgHtml( 'surveys-surveystats-question-answers' ) . '</th>' .
			'</tr></thead>'	
		);
		
		$out->addHTML( '<tbody>' );
		
		foreach ( $survey->getQuestions() as /* SurveyQuestion */ $question ) {
			$this->displayQuestionStats( $question );
		}
		
		$out->addHTML( '</tbody>' );
		
		$out->addHTML( Html::closeElement( 'table' ) );
	}
	
	protected function displayQuestionStats( SurveyQuestion $question ) {
		static $qNr = 0;
		
		$out = $this->getOutput();
		
		$out->addHTML( '<tr>' );
		
		$out->addHTML( Html::element(
			'td',
			array( 'data-sort-value' => ++$qNr ),
			wfMsgExt( 'surveys-surveystats-question-#', 'parsemag', $qNr )
		) );
		
		$out->addHTML( Html::element(
			'td',
			array(),
			wfMsg( SurveyQuestion::getTypeMessage( $question->getField( 'type' ) ) )
		) );
		
		$out->addHTML( Html::element(
			'td',
			array(),
			$question->getField( 'text' )
		) );
		
		$out->addHTML( Html::element(
			'td',
			array(),
			SurveyAnswer::count( array( 'question_id' => $question->getId() ) )
		) );
		
//		$out->addHTML( Html::element(
//			'td',
//			array(),
//			'...'
//		) );
		
		$out->addHTML( '</tr>' );
	}
	
}
