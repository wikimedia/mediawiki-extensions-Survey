<?php

/**
 * Administration interface for a survey.
 * 
 * @since 0.1
 * 
 * @file SpecialSurvey.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SpecialSurvey extends SpecialSurveyPage {
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 */
	public function __construct() {
		parent::__construct( 'Survey', 'surveyadmin' );
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
		
		global $wgRequest, $wgUser;
		
		if ( $wgRequest->wasPosted() && $wgUser->matchEditToken( $wgRequest->getVal( 'wpEditToken' ) ) ) {
			$this->handleSubmission();
		}
		else {
			if ( is_null( $subPage ) ) {
				$survey = new Survey( null );
			}
			else {
				$survey = Survey::newFromName( $subPage, true );
			}
			
			if ( $survey === false ) {
				$survey = new Survey( null, $subPage );
			}
			
			$this->showSurvey( $survey );
			$this->addModules( 'ext.survey.special.survey' );
		}
	}
	
	/**
	 * Handle submission of a survey.
	 * This conists of finding the posted survey data, constructing the
	 * corresponding objects, writing these to the db and then redirecting
	 * the user back to the surveys list.
	 * 
	 * @since 0.1
	 */
	protected function handleSubmission() {
		global $wgRequest;
		
		$values = $wgRequest->getValues();
		
		if ( $wgRequest->getInt( 'survey-id' ) == 0 ) {
			$survey = new Survey( null );
		}
		else {
			$survey = Survey::newFromId( $wgRequest->getInt( 'survey-id' ), false );
		}
		
		$survey->setName( $wgRequest->getText( 'survey-name' ) );
		$survey->setEnabled( $wgRequest->getCheck( 'survey-enabled' ) );
		
		$survey->setQuestions( $this->getSubmittedQuestions() );
		
		$survey->writeToDB();
		
		$this->getOutput()->redirect( SpecialPage::getTitleFor( 'Surveys' )->getLocalURL() );
	}
	
	/**
	 * Gets a list of submitted surveys.
	 * 
	 * @return array of SurveyQuestion
	 */
	protected function getSubmittedQuestions() {
		$questions = array();
		
//		foreach ( $GLOBALS['wgRequest']->getValues() as $name => $value ) {
//			
//		}
		
		foreach ( $GLOBALS['wgRequest']->getValues() as $name => $value ) {
			$matches = array();
			
			if ( preg_match( '/survey-question-text-(\d)+/', $name, $matches ) ) {
				$questions[] = $this->getSubmittedQuestion( $matches[1] );
			}
			else if ( preg_match( '/survey-question-text-new-(\d)+/', $name, $matches ) ) {
				$questions[] = $this->getSubmittedQuestion( $matches[1], true );
			}
		}
		
		return $questions;
	}
	
	/**
	 * Create a 
	 * 
	 * @since 0.1
	 * 
	 * @param integer|null $questionId
	 * 
	 * @return SurveyQuestion
	 */
	protected function getSubmittedQuestion( $questionId, $isNewQuestion = false ) {
		global $wgRequest;
		
		if ( $isNewQuestion ) {
			$questionDbId = null;
			$questionId = "new-$questionId";
		}
		else {
			$questionId = $questionId;
		}
		
		$question = new SurveyQuestion(
			$questionDbId,
			0,
			$wgRequest->getText( "survey-question-text-$questionId" ),
			$wgRequest->getInt( "survey-question-type-$questionId" ),
			$wgRequest->getCheck( "survey-question-required-$questionId" )
		);
		
		return $question;
	}
	
	/**
	 * Show error when requesting a non-existing survey.
	 * 
	 * @since 0.1
	 */
	protected function showNameError() {
		$this->getOutput()->addHTML(
			'<p class="errorbox">' . wfMsgHtml( 'surveys-special-unknown-name' ) . '</p>'
		);
	}
	
	/**
	 * Show the survey.
	 * 
	 * @since 0.1
	 * 
	 * @param Survey $survey
	 */
	protected function showSurvey( Survey $survey ) {
		$fields = array();
		
		$fields[] = array(
			'type' => 'hidden',
			'default' => $survey->getId(),
			'name' => 'survey-id',
			'id' => 'survey-id',
		);
		
		$fields[] = array(
			'type' => 'text',
			'default' => $survey->getName(),
			'label-message' => 'survey-special-label-name',
			'id' => 'survey-name',
			'name' => 'survey-name',
		);
		
		$fields[] = array(
			'type' => 'check',
			'default' => $survey->isEnabled() ? '1' : '0',
			'label-message' => 'survey-special-label-enabled',
			'id' => 'survey-enabled',
			'name' => 'survey-enabled',
		);
		
		$fields[] = array(
			'type' => 'text',
			'default' => $survey->getHeader(),
			'label-message' => 'survey-special-label-header',
			'id' => 'survey-header',
			'name' => 'survey-header',
		);
		
		$fields[] = array(
			'type' => 'text',
			'default' => $survey->getFooter(),
			'label-message' => 'survey-special-label-footer',
			'id' => 'survey-footer',
			'name' => 'survey-footer',
		);

		$fields[] = array(
			'type' => 'text',
			'default' => $survey->getThanks(),
			'label-message' => 'survey-special-label-thanks',
			'id' => 'survey-thanks',
			'name' => 'survey-thanks',
		);
		
		foreach ( $survey->getQuestions() as /* SurveyQuestion */ $question ) {
			$fields[] = array(
				'class' => 'SurveyQuestionField',
				'options' => array(
					'required' => $question->isRequired(),
					'text' => $question->getText(),
					'type' => $question->getType(),
					'id' => $question->getId(),
				)
			);
		}
		
		// getContext was added in 1.18 and since that version is
		// the second argument for the HTMLForm constructor.
		if ( is_callable( array( $this, 'getContext' ) ) ) {
			$form = new HTMLForm( $fields, $this->getContext() );
		}
		else {
			$form = new HTMLForm( $fields );
		}

		$form->show();
	}
	
}

class SurveyQuestionField extends HTMLFormField {
	
	public function getInputHTML( $value ) {
		$attribs = array(
			'class' => 'survey-question-data'
		);
		
		foreach ( $this->mParams['options'] as $name => $value ) {
			if ( is_bool( $value ) ) {
				$value = $value ? '1' : '0';
			}
			elseif( is_object( $value ) || is_array( $value ) ) {
				$value = FormatJson::encode( $value );
			}
			
			$attribs['data-' . $name] = $value;
		}
		
		return Html::element(
			'div',
			$attribs
		);
	}
	
}