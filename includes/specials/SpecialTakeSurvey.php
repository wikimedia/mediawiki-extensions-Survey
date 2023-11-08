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
	 * @param null|string $subPage
	 * @return bool|void
	 */
	public function execute( $subPage ) {
		if ( !parent::execute( $subPage ) ) {
			return;
		}

		$survey = Survey::selectRow(
			array( 'enabled' ),
			array( 'name' => $subPage )
		);

		if ( $survey === false ) {
			$this->showError( 'surveys-takesurvey-nosuchsurvey' );
		}
		elseif ( $survey->getField( 'enabled' ) ) {
			$this->displaySurvey( $subPage );
		}
		elseif ( $this->getUser()->isAllowed( 'surveyadmin' ) ) {
			$this->showWarning( 'surveys-takesurvey-warn-notenabled' );
			$this->getOutput()->addHTML( '<br /><br /><br /><br />' );
			$this->displaySurvey( $subPage );
		}
		else {
			$this->showError( 'surveys-takesurvey-surveynotenabled' );
		}
	}

	/**
	 * Add the output for the actual survey.
	 * This is done by adding a survey tag as wikitext, which then get's rendered.
	 *
	 * @since 0.1
	 *
	 * @param string $subPage
	 */
	protected function displaySurvey( $subPage ) {
		$this->displayNavigation( array(
			$this->msg( 'survey-navigation-edit', $subPage )->parse(),
			$this->msg( 'survey-navigation-stats', $subPage )->parse(),
			$this->msg( 'survey-navigation-list' )->parse()
		) );

		$out = $this->getOutput();
		$user = $this->getUser();
		if ( method_exists( $out, 'addWikiTextAsInterface' ) ) {
			// MW 1.32+
			$out->addWikiTextAsInterface( Xml::element(
				'survey',
				array(
					'name' => $subPage,
					'require-enabled' => $user->isAllowed( 'surveyadmin' ) ? '0' : '1',
					'cookie' => 'no'
				),
					$this->msg( 'surveys-takesurvey-loading' )->text()
			) );
		} else {
			$out->addWikiText( Xml::element(
				'survey',
				array(
					'name' => $subPage,
					'require-enabled' => $user->isAllowed( 'surveyadmin' ) ? '0' : '1',
					'cookie' => 'no'
				),
					$this->msg( 'surveys-takesurvey-loading' )->text()
			) );
		}
	}

	protected function getGroupName() {
		return 'other';
	}
}
