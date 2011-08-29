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
		
		$survey = Survey::newFromName( $subPage, true );
		
		if ( $survey === false ) {
			$this->showNameError();
		}
		else {
			$this->showSurvey( $survey );
			$this->addModules( 'ext.survey.special.survey' );
		}
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
			'type' => 'text',
			//'options' => array(),
			'default' => 'ohi',
			'label-message' => 'survey-special-label-name',
			'required' => true
		);
		
		$fields[] = array(
			'type' => 'check',
			//'options' => array(),
			'default' => 'there',
			'label-message' => 'survey-special-label-enabled',
			'required' => true
		);
		
		foreach ( $survey->getQuestions() as /* SurveyQuestion */ $question ) {
			$fields[] = array(
				'class' => 'SurveyQuestionField',
				'options' => array(
					'required' => $question->isRequired(),
					'text' => $question->getText(),
					'type' => $question->getType(),
					'id' => $question->getId(),
					'type' => $question->getType(),
				)
			);
		}
		
		$fields[] = array(
			'class' => 'SurveyAddQuestionField',
			'default' => 'foo',
			'label-message' => 'survey-special-label-add',
			'id' => 'survey-add-question-text'
		);
		
		// getContext was added in 1.18 and since that version is
		// the second argument for the HTMLForm constructor.
		if ( is_callable( array( $this, 'getContext' ) ) ) {
			$form = new HTMLForm( $fields, $this->getContext() );
		}
		else {
			$form = new HTMLForm( $fields );
		}

//		$q = new SurveyQuestion( null, 5, 'foo bar', 0, false, array(), false );
//		var_dump($q->toUrlData());exit;
		$form->displayForm( '' );
	}
	
}

class SurveyAddQuestionField extends HTMLTextField {
	
	/*
			$fields[] = array(
				'type' => 'text',
				//'options' => array(),
				'default' => $question->getText(),
				'label-message' => 'survey-special-label-question',
				'required' => $question->isRequired()
			);
			
			$fields[] = array(
				'type' => 'select',
				'options' => array(
			
				),
				'label-message' => 'survey-special-label-type',
				'required' => $question->isRequired()
			);
			
			$fields[] = array(
				'class' => 'SurveyQuestionValuesField',
				//'options' => array(),
				'default' => '',
				'label-message' => 'survey-special-label-required',
				'required' => true
			);
	 */
	
	public function getInputHTML( $value ) {
		return parent::getInputHTML( $value ) . '&#160;' . Html::element(
			'button',
			array( 'id' => 'survey-add-question-button' ),
			wfMsg( 'survey-special-label-button' )
		);
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