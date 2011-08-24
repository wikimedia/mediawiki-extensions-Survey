<?php

/**
 * Administration interface for surveys.
 * 
 * @since 0.1
 * 
 * @file SpecialSurveys.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SpecialSurveys extends SpecialSurveyPage {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 */
	public function __construct() {
		parent::__construct( 'Surveys', 'surveyadmin' );
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
		
		global $wgRequest;
		
		if ( $wgRequest->wasPosted()
			&& $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) )
			&& $wgRequest->getCheck( 'newsurvey' ) ) {
				$this->getOutput()->redirect( SpecialPage::getTitleFor( 'Survey', $wgRequest->getVal( 'newsurvey' ) )->getLocalURL() );
		}
		else {
			$this->displaySurveys();
		}
	}
	
	/**
	 * Displays surveys.
	 * 
	 * @since 0.1
	 */
	protected function displaySurveys() {
		$this->displayAddNewControl();
		
		$dbr = wfGetDB( DB_SLAVE );
		
		$surveys = $dbr->select(
			'surveys',
			array(
				'survey_id',
				'survey_name',
				'survey_enabled',
			)
		);
		
		if ( $surveys->numRows() > 0 ) {
			$this->displaySurveysTable( $surveys );
		}
	}
	
	/**
	 * Displays a small form to add a new campaign.
	 * 
	 * @since 0.1
	 */
	protected function displayAddNewControl() {
		$out = $this->getOutput();
		
		$out->addHTML( Html::openElement(
			'form',
			array(
				'method' => 'post',
				'action' => $this->getTitle()->getLocalURL(),
			)
		) );
		
		$out->addHTML( '<fieldset>' );
		
		$out->addHTML( '<legend>' . htmlspecialchars( wfMsg( 'surveys-special-addnew' ) ) . '</legend>' );
		
		$out->addHTML( Html::element( 'p', array(), wfMsg( 'surveys-special-namedoc' ) ) );
		
		$out->addHTML( Html::element( 'label', array( 'for' => 'newcampaign' ), wfMsg( 'surveys-special-newname' ) ) );
		
		$out->addHTML( '&#160;' . Html::input( 'newsurvey' ) . '&#160;' );
		
		$out->addHTML( Html::input(
			'addnewsurvey',
			wfMsg( 'surveys-special-add' ),
			'submit'
		) );
		
		global $wgUser;
		$out->addHTML( Html::hidden( 'wpEditToken', $wgUser->editToken() ) );
		
		$out->addHTML( '</fieldset></form>' );
	}
	
	/**
	 * Displays a list of all survets.
	 * 
	 * @since 0.1
	 * 
	 * @param ResultWrapper $surveys
	 */
	protected function displaySurveysTable( ResultWrapper $surveys ) {
		$out = $this->getOutput();
		
		$out->addHTML( Html::element( 'h2', array(), wfMsg( 'surveys-special-existing' ) ) );
		
		$out->addHTML( Xml::openElement(
			'table',
			array( 'class' => 'wikitable', 'style' => 'width:400px' )
		) );
		
		$out->addHTML( 
			'<tr>' .
				Html::element( 'th', array(), wfMsg( 'surveys-special-name' ) ) .
				Html::element( 'th', array(), wfMsg( 'surveys-special-status' ) ) .
				Html::element( 'th', array(), wfMsg( 'surveys-special-edit' ) ) .
				Html::element( 'th', array(), wfMsg( 'surveys-special-delete' ) ) .
			'</tr>'
		);
		
		foreach ( $surveys as $survey ) {
			$out->addHTML(
				'<tr>' .
					'<td>' .
						Html::element( 
							'a',
							array(
								'href' => SpecialPage::getTitleFor( 'TakeSurvey', $survey->survey_name )->getLocalURL()
							),
							$survey->survey_name
						) .
					'</td>' .
					Html::element( 'td', array(), wfMsg( 'surveys-special-' . ( $survey->survey_enabled ? 'enabled' : 'disabled' ) ) ) .
					'<td>' .
						Html::element( 
							'a',
							array(
								'href' => SpecialPage::getTitleFor( 'Survey', $survey->survey_name )->getLocalURL()
							),
							wfMsg( 'surveys-special-edit' )
						) .
					'</td>' .
					'<td>' .
						Html::element( 
							'a',
							array(
								'href' => '#',
								'class' => 'survey-delete',
								'data-survey-id' => $survey->survey_id
							),
							wfMsg( 'surveys-special-delete' )
						) .
					'</td>' .
				'</tr>'
			);
		}
		
		$out->addHTML( '</table>' );
	}	
	
}
