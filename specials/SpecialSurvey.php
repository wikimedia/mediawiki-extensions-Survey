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
			'type' => 'hidden',
			'default' => $survey->getId(),
			'name' => 'survey-id',
			'id' => 'survey-id',
		);
		
		$fields[] = array(
			'type' => 'text',
			//'options' => array(),
			'default' => 'ohi',
			'label-message' => 'survey-special-label-name',
			'required' => true,
			'id' => 'survey-name',
		);
		
		$fields[] = array(
			'type' => 'check',
			//'options' => array(),
			'default' => 'there',
			'label-message' => 'survey-special-label-enabled',
			'required' => true,
			'id' => 'survey-enabled',
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