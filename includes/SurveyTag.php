<?php

use MediaWiki\Html\Html;

/**
 * Class to render survey tags.
 *
 * @since 0.1
 *
 * @file SurveyTag.php
 * @ingroup Survey
 *
 * @license GPL-2.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SurveyTag {
	/**
	 * List of survey parameters.
	 *
	 * @since 0.1
	 *
	 * @var array
	 */
	protected $parameters;

	/**
	 * List of survey contents.
	 *
	 * @since 0.1
	 *
	 * @var string
	 */
	protected $contents;

	/**
	 * Constructor.
	 *
	 * @since 0.1
	 *
	 * @param array $args
	 * @param string|null $contents
	 * @throws MWException
	 */
	public function __construct( array $args, $contents = null ) {
		$this->parameters = $args;
		$this->contents = $contents;

		$args = filter_var_array( $args, $this->getSurveyParameters() );

		if ( is_array( $args ) ) {
			$this->parameters = [];

			foreach ( $args as $name => $value ) {
				if ( $value !== null && $value !== false ) {
					$this->parameters['survey-data-' . $name] = $value;
				}
			}

			$user = RequestContext::getMain()->getUser();

			$this->parameters['class'] = 'surveytag';
			$this->parameters['survey-data-token'] =
				$user->getEditToken( serialize( [ 'submitsurvey', $user->getName() ] ) );
		} else {
			throw new MWException( 'Invalid parameters for survey tag.' );
		}
	}

	/**
	 * Render the survey div.
	 *
	 * @since 0.1
	 *
	 * @param Parser $parser
	 *
	 * @return string
	 */
	public function render( Parser $parser ) {
		static $loadedJs = false;

		if ( !$loadedJs ) {
			$parser->getOutput()->addModules( [ 'ext.survey.tag' ] );
			$parser->getOutput()->setJsConfigVar( 'wgSurveyDebug', SurveySettings::get( 'JSDebug' ) );
		}

		return Html::element(
			'span',
			$this->parameters,
			$this->contents
		);
	}

	/**
	 * @since 0.1
	 *
	 * @return array
	 */
	protected function getSurveyParameters() {
		return [
			'id' => [ 'filter' => FILTER_VALIDATE_INT, 'options' => [ 'min_range' => 1 ] ],
			'name' => [],
			'cookie' => [],
			'title' => [],
			'require-enabled' => [
				'filter' => FILTER_VALIDATE_INT,
				'options' => [
					'min_range' => 0, 'max_range' => 1
				]
			],
			'expiry' => [
				'filter' => FILTER_VALIDATE_INT,
				'options' => [
					'min_range' => 0
				]
			],
			'min-pages' => [
				'filter' => FILTER_VALIDATE_INT,
				'options' => [
					'min_range' => 0
				]
			],
			'ratio' => [
				'filter' => FILTER_VALIDATE_INT,
				'options' => [
					'min_range' => 0,
					'max_range' => 100
				]
			],
		];
	}

}
