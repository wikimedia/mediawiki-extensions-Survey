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
	 * @param null|string $subPage
	 * @return bool|void
	 */
	public function execute( $subPage ) {
		if ( !parent::execute( $subPage ) ) {
			return;
		}

		$req = $this->getRequest();

		if ( $req->wasPosted()
			&& $this->getUser()->matchEditToken( $req->getVal( 'wpEditToken' ) )
			&& $req->getCheck( 'newsurvey' ) ) {
				$this->getOutput()->redirect( SpecialPage::getTitleFor( 'EditSurvey', $req->getVal( 'newsurvey' ) )->getLocalURL() );
		} else {
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

		$surveys = Survey::select( array( 'id', 'name', 'enabled', 'title' ) );

		if ( count( $surveys ) > 0 ) {
			$this->displaySurveysTable( $surveys );
		}

		$this->addModules( 'ext.survey.special.surveys' );
	}

	/**
	 * Displays a small form to add a new campaign.
	 *
	 * @since 0.1
	 */
	protected function displayAddNewControl() {
		$out = $this->getOutput();

		$formDescriptor = [
			'textbox' => [
				'type' => 'text',
				'name' => 'newsurvey',
				'label' => $this->msg( 'surveys-special-newname' )->text(),
			]
		];

		$htmlForm = HTMLForm::factory( 'ooui', $formDescriptor, $this->getContext() );
		$htmlForm
			->setHeaderText( $this->msg( 'surveys-special-namedoc' )->text() )
			->setAction( $this->getPageTitle()->getLocalURL() )
			->setSubmitName( 'addnewsurvey' )
			->setSubmitTextMsg( 'surveys-special-add' )
			->setWrapperLegendMsg( 'surveys-special-addnew' )
			->prepareForm()
			->displayForm( false );
	}

	/**
	 * Displays a list of all survets.
	 *
	 * @since 0.1
	 *
	 * @param array $surveys
	 */
	protected function displaySurveysTable( array /* of Survey */ $surveys ) {
		$out = $this->getOutput();

		$out->addHTML( Html::element( 'h2', array(), $this->msg( 'surveys-special-existing' )->text() ) );

		$out->addHTML( Xml::openElement(
			'table',
			array( 'class' => 'wikitable sortable' )
		) );

		$out->addHTML(
			'<thead><tr>' .
				Html::element( 'th', array(), $this->msg( 'surveys-special-title' )->text() ) .
				Html::element( 'th', array(), $this->msg( 'surveys-special-status' )->text() ) .
				Html::element( 'th', array( 'class' => 'unsortable' ), $this->msg( 'surveys-special-stats' )->text() ) .
				Html::element( 'th', array( 'class' => 'unsortable' ), $this->msg( 'surveys-special-edit' )->text() ) .
				Html::element( 'th', array( 'class' => 'unsortable' ), $this->msg( 'surveys-special-delete' )->text() ) .
			'</tr></thead>'
		);

		$out->addHTML( '<tbody>' );

		/**
		 * @var $survey Survey
		 */
		foreach ( $surveys as $survey ) {
			$out->addHTML(
				'<tr>' .
					'<td data-sort-value="' . htmlspecialchars( $survey->getField( 'title' ) ) . '">' .
						Html::element(
							'a',
							array(
								'href' => SpecialPage::getTitleFor( 'TakeSurvey', $survey->getField( 'name' ) )->getLocalURL()
							),
							$survey->getField( 'title' )
						) .
					'</td>' .
					// Give grep a chance to find the usages:
					// surveys-special-enabled, surveys-special-disabled
					Html::element( 'td', array(), $this->msg( 'surveys-special-' . ( $survey->getField( 'enabled' ) ? 'enabled' : 'disabled' ) )->text() ) .
					'<td>' .
						Html::element(
							'a',
							array(
								'href' => SpecialPage::getTitleFor( 'SurveyStats', $survey->getField( 'name' ) )->getLocalURL()
							),
							$this->msg( 'surveys-special-stats' )->text()
						) .
					'</td>' .
					'<td>' .
						Html::element(
							'a',
							array(
								'href' => SpecialPage::getTitleFor( 'EditSurvey', $survey->getField( 'name' ) )->getLocalURL()
							),
							$this->msg( 'surveys-special-edit' )->text()
						) .
					'</td>' .
					'<td>' .
						Html::element(
							'a',
							array(
								'href' => '#',
								'class' => 'survey-delete',
								'data-survey-id' => $survey->getId(),
								'data-survey-token' => $this->getUser()->getEditToken(
									'deletesurvey' .
										$survey->getId() )
							),
							$this->msg( 'surveys-special-delete' )->text()
						) .
					'</td>' .
				'</tr>'
			);
		}

		$out->addHTML( '</tbody>' );
		$out->addHTML( '</table>' );
	}

	protected function getGroupName() {
		return 'other';
	}
}
