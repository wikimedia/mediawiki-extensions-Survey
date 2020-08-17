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
	 * @param null|string $subPage
	 * @return bool|void
	 */
	public function execute( $subPage ) {
		if ( !parent::execute( $subPage ) ) {
			return;
		}

		if ( is_null( $subPage ) || trim( $subPage ) === '' ) {
			$this->getOutput()->redirect( SpecialPage::getTitleFor( 'Surveys' )->getLocalURL() );
		} else {
			$subPage = trim( $subPage );

			if ( Survey::has( array( 'name' => $subPage ) ) ) {
				$survey = Survey::newFromName( $subPage );

				$this->displayNavigation( array(
					$this->msg( 'survey-navigation-edit', $survey->getField( 'name' ) )->parse(),
					$this->msg( 'survey-navigation-take', $survey->getField( 'name' ) )->parse(),
					$this->msg( 'survey-navigation-list' )->parse()
				) );

				$this->displayStats( $survey );
			}
			else {
				$this->showError( 'surveys-surveystats-nosuchsurvey' );
			}
		}
	}

	/**
	 * Display the statistics that go with the survey.
	 *
	 * @since 0.1
	 *
	 * @param Survey $survey
	 */
	protected function displayStats( Survey $survey ) {
		$this->displaySummary( $this->getSummaryData( $survey ) );

		if ( count( $survey->getQuestions() ) > 0 ) {
			$this->displayQuestions( $survey );
		}
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

		// Give grep a chance to find the usages: surveys-surveystats-enabled, surveys-surveystats-disabled
		$stats['name'] = $survey->getField( 'name' );
		$stats['title'] = $survey->getField( 'title' );
		$stats['status'] = $this->msg( 'surveys-surveystats-' . ( $survey->getField( 'enabled' ) ? 'enabled' : 'disabled' ) )->text();
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

			// Give grep a chance to find the usages:
			// surveys-surveystats-name, surveys-surveystats-title, surveys-surveystats-status,
			// surveys-surveystats-questioncount, surveys-surveystats-submissioncount
			$out->addHTML( Html::element(
				'th',
				array( 'class' => 'survey-stat-name' ),
					$this->msg( 'surveys-surveystats-' . $stat )->text()
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

	/**
	 * Displays a table with the surveys questions and some summary stats about them.
	 *
	 * @since 0.1
	 *
	 * @param Survey $survey
	 */
	protected function displayQuestions( Survey $survey ) {
		$out = $this->getOutput();

		$out->addHTML( '<h2>' . $this->msg( 'surveys-surveystats-questions' )->escaped() . '</h2>' );
		$out->addHTML( Html::openElement( 'table', array( 'class' => 'wikitable sortable survey-questions' ) ) );
		$out->addHTML(
			'<thead><tr>' .
				'<th>' . $this->msg( 'surveys-surveystats-question-nr' )->escaped() . '</th>' .
				'<th>' . $this->msg( 'surveys-surveystats-question-type' )->escaped() . '</th>' .
				'<th class="unsortable">' . $this->msg( 'surveys-surveystats-question-text' )->escaped() . '</th>' .
				'<th>' . $this->msg( 'surveys-surveystats-question-answercount' )->escaped() . '</th>' .
				'<th class="unsortable">' . $this->msg( 'surveys-surveystats-question-answers' )->escaped() . '</th>' .
			'</tr></thead>'
		);
		$out->addHTML( '<tbody>' );

		/**
		 * @var SurveyQuestion $question
		 */
		foreach ( $survey->getQuestions() as $question ) {
			$this->displayQuestionStats( $question );
		}

		$out->addHTML( '</tbody>' );
		$out->addHTML( Html::closeElement( 'table' ) );
	}

	/**
	 * Adds a table row with the summary stats for the provided question.
	 *
	 * @since 0.1
	 *
	 * @param SurveyQuestion $question
	 */
	protected function displayQuestionStats( SurveyQuestion $question ) {
		static $qNr = 0;

		$out = $this->getOutput();

		$out->addHTML( '<tr>' );
		$out->addHTML( Html::element(
			'td',
			array( 'data-sort-value' => ++$qNr ),
				$this->msg( 'surveys-surveystats-question-nr-format', $qNr )->text()
		) );

		// For grep: getTypeMessage returns any one of the following messages:
		// survey-question-type-text, survey-question-type-number, survey-question-type-select,
		// survey-question-type-radio, survey-question-type-textarea, survey-question-type-check
		$out->addHTML( Html::element(
			'td',
			array(),
				$this->msg( SurveyQuestion::getTypeMessage( $question->getField( 'type' ) ) )->text()
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

		$out->addHTML( Html::rawElement(
			'td',
			array(),
			$this->getAnswerList( $question )
		) );

		$out->addHTML( '</tr>' );
	}

	/**
	 * Get a list of most provided answers for the question.
	 *
	 * @since 0.1
	 *
	 * @param SurveyQuestion $question
	 *
	 * @return string
	 */
	protected function getAnswerList( SurveyQuestion $question ) {
		if ( $question->isRestrictiveType() ) {
			$list = '<ul>';

			$answers = array();
			$answerTranslations = array();

			if ( $question->getField( 'type' ) == SurveyQuestion::$TYPE_CHECK ) {
				$possibilities = array( '0', '1' );
				$answerTranslations['0'] = $this->msg( 'surveys-surveystats-unchecked' )->text();
				$answerTranslations['1'] = $this->msg( 'surveys-surveystats-checked' )->text();
			}
			else {
				$possibilities = $question->getField( 'answers' );
			}

			foreach ( $possibilities as $answer ) {
				$answers[$answer] = SurveyAnswer::count( array( 'text' => $answer ) );
			}

			asort( $answers, SORT_NUMERIC );

			foreach ( array_reverse( $answers ) as $answer => $answerCount ) {
				if ( array_key_exists( $answer, $answerTranslations ) ) {
					$answer = $answerTranslations[$answer];
				}

				$list .= Html::element(
					'li',
					array(),
					$this->msg( 'surveys-surveystats-question-answer', $answer )->numParams( $answerCount )->text()
				);
			}

			return $list . '</ul>';
		}
		else {
			return '';
		}
	}

	protected function getGroupName() {
		return 'other';
	}
}
