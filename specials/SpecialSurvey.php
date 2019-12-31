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
		parent::__construct( 'EditSurvey', 'surveyadmin', false );
	}

	public function doesWrites() {
		return true;
	}

	/**
	 * Main method.
	 *
	 * @since 0.1
	 *
	 * @param null|string $subPage
	 * @return bool|void
	 */
	public function execute( $subPage ) {
		if ( !parent::execute( $subPage ) ) {
			return;
		}

		if ( $this->getRequest()->wasPosted() && $this->getUser()->matchEditToken( $this->getRequest()->getVal( 'wpEditToken' ) ) ) {
			$this->handleSubmission();
		} else {
			if ( is_null( $subPage ) || trim( $subPage ) === '' ) {
				$this->getOutput()->redirect( SpecialPage::getTitleFor( 'Surveys' )->getLocalURL() );
			} else {
				$subPage = trim( $subPage );

				$survey = Survey::newFromName( $subPage, null, true );

				if ( $survey === false ) {
					$survey = new Survey( array( 'name' => $subPage ), true );
				}
				else {
					$this->displayNavigation( array(
						$this->msg( 'survey-navigation-take', $subPage )->parse(),
						$this->msg( 'survey-navigation-stats', $subPage )->parse(),
						$this->msg( 'survey-navigation-list' )->parse()
					) );
				}

				$this->showSurvey( $survey );
				$this->addModules( 'ext.survey.special.survey' );
			}
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
		$req = $this->getRequest();

		if ( $req->getInt( 'survey-id' ) == 0 ) {
			$survey = new Survey( null );
		} else {
			$survey = Survey::newFromId( $req->getInt( 'survey-id' ), null, false );
		}

		foreach ( array( 'name', 'title', 'header', 'footer', 'thanks' ) as $field ) {
			$survey->setField( $field, $req->getText( 'survey-' . $field ) );
		}

		$survey->setField( 'enabled', $req->getCheck( 'survey-enabled' ) );

		foreach ( array( 'user_type', 'ratio', 'min_pages', 'expiry' ) as $field ) {
			$survey->setField( $field, $req->getInt( 'survey-' . $field ) );
		}

		$survey->setField( 'namespaces', array() );

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

		foreach ( $this->getRequest()->getValues() as $name => $value ) {
			$matches = array();

			if ( preg_match( '/survey-question-text-(\d+)/', $name, $matches ) ) {
				$questions[] = $this->getSubmittedQuestion( $matches[1] );
			} elseif ( preg_match( '/survey-question-text-new-(\d+)/', $name, $matches ) ) {
				$questions[] = $this->getSubmittedQuestion( $matches[1], true );
			}
		}

		return $questions;
	}

	/**
	 * Create and return a survey question object from the submitted data.
	 *
	 * @since 0.1
	 *
	 * @param integer|null $questionId
	 * @param bool $isNewQuestion
	 * @return SurveyQuestion
	 */
	protected function getSubmittedQuestion( $questionId, $isNewQuestion = false ) {
		$req = $this->getRequest();

		if ( $isNewQuestion ) {
			$questionDbId = null;
			$questionId = "new-$questionId";
		} else {
			$questionDbId = $questionId;
		}

		$answers = array_filter(
			explode( "\n", $req->getText( "survey-question-answers-$questionId" ) ),
			function( $line ) {
				return trim( $line ) != '';
			}
		);

		$question = new SurveyQuestion( array(
			'id' => $questionDbId,
			'removed' => 0,
			'text' => $req->getText( "survey-question-text-$questionId" ),
			'type' => $req->getInt( "survey-question-type-$questionId" ),
			'required' => 0, // $wgRequest->getCheck( "survey-question-required-$questionId" ),
			'answers' => $answers
		) );

		return $question;
	}

	/**
	 * Show error when requesting a non-existing survey.
	 *
	 * @since 0.1
	 */
	protected function showNameError() {
		$this->getOutput()->addHTML(
			'<p class="errorbox">' . $this->msg( 'surveys-special-unknown-name' )->escaped() . '</p>'
		);
	}

	/**
	 * Get an array of numbers with as keys the formatted version of the values.
	 *
	 * @since 0.1
	 *
	 * @param array $numbers
	 *
	 * @return array
	 */
	protected function getNumericalOptions( array $numbers ) {
		$lang = $this->getLanguage();

		return array_flip( array_map(
			function( $n ) use( $lang ) { return $lang->formatNum( $n ); },
			array_combine( $numbers, $numbers )
		) );
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
			'type' => 'hidden',
			'default' => $survey->getField( 'name' ),
			'name' => 'survey-name',
			'id' => 'survey-name',
		);

		$fields[] = array(
			'type' => 'hidden',
			'default' => $survey->getField( 'expiry' ),
			'name' => 'survey-expiry',
			'id' => 'survey-expiry',
		);

		$fields[] = array(
			'class' => 'SurveyNameField',
			'default' => $survey->getField( 'name' ),
			'label-message' => 'survey-special-label-name',
			'style' => 'font-weight: bold;'
		);

		$fields[] = array(
			'type' => 'text',
			'default' => $survey->getField( 'title' ),
			'label-message' => 'survey-special-label-title',
			'id' => 'survey-title',
			'name' => 'survey-title',
		);

		$fields[] = array(
			'type' => 'check',
			'default' => $survey->getField( 'enabled' ) ? '1' : '0',
			'label-message' => 'survey-special-label-enabled',
			'id' => 'survey-enabled',
			'name' => 'survey-enabled',
		);

		$fields[] = array(
			'type' => 'radio',
			'default' => $survey->getField( 'user_type' ),
			'label-message' => 'survey-special-label-usertype',
			'id' => 'survey-user_type',
			'name' => 'survey-user_type',
			'options' => array(
				$this->msg( 'survey-user-type-all' )->escaped() => Survey::$USER_ALL,
				$this->msg( 'survey-user-type-loggedin' )->escaped() => Survey::$USER_LOGGEDIN,
				$this->msg( 'survey-user-type-confirmed' )->escaped() => Survey::$USER_CONFIRMED,
				$this->msg( 'survey-user-type-editor' )->escaped() => Survey::$USER_EDITOR,
				$this->msg( 'survey-user-type-anon' )->escaped() => Survey::$USER_ANON,
			),
		);

		$fields[] = array(
			'type' => 'select',
			'default' => $survey->getField( 'ratio' ),
			'label-message' => 'survey-special-label-ratio',
			'id' => 'survey-ratio',
			'name' => 'survey-ratio',
			'options' => $this->getNumericalOptions( array_merge( array( 0.01, 0.1 ), range( 1, 100 ) ) ),
		);

		$fields[] = array(
			'type' => 'select',
			'default' => $survey->getField( 'min_pages' ),
			'label-message' => 'survey-special-label-minpages',
			'id' => 'survey-min_pages',
			'name' => 'survey-min_pages',
			'options' => $this->getNumericalOptions( range( 0, 250 ) ),
		);

		$fields[] = array(
			'type' => 'text',
			'default' => $survey->getField( 'header' ),
			'label-message' => 'survey-special-label-header',
			'id' => 'survey-header',
			'name' => 'survey-header',
		);

		$fields[] = array(
			'type' => 'text',
			'default' => $survey->getField( 'footer' ),
			'label-message' => 'survey-special-label-footer',
			'id' => 'survey-footer',
			'name' => 'survey-footer',
		);

		$fields[] = array(
			'type' => 'text',
			'default' => $survey->getField( 'thanks' ),
			'label-message' => 'survey-special-label-thanks',
			'id' => 'survey-thanks',
			'name' => 'survey-thanks',
		);

		/**
		 * @var $question SurveyQuestion
		 */
		foreach ( $survey->getQuestions() as $question ) {
			$fields[] = array(
				'class' => 'SurveyQuestionField',
				'options' => $question->toArray()
			);
		}

		$form = HTMLForm::factory( 'ooui', $fields, $this->getContext() );
		$form
			->setSubmitText( $this->msg( 'surveys-special-save' )->text() )
			->addButton(
				'cancelEdit',
				$this->msg( 'cancel' )->text(),
				'cancelEdit',
				array(
					'onclick' => 'window.location="' . SpecialPage::getTitleFor( 'Surveys' )->getFullURL() . '";return false;'
				)
			)
			->show();
	}

	protected function getGroupName() {
		return 'other';
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
			} elseif( is_object( $value ) || is_array( $value ) ) {
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

class SurveyNameField extends HTMLFormField {
	public function getInputHTML( $value ) {
		return Html::element(
			'span',
			array(
				'style' => $this->mParams['style']
			),
			$value
		);
	}
}
