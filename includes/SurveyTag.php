<?php

/**
 * Class to render survey tags.
 * 
 * @since 0.1
 * 
 * @file SurveyTag.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3+
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
	
	protected $contents;
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param array $args
	 * @param string|null $contents
	 */
	public function __construct( array $args, $contents = null ) {
		$this->parameters = $args;
		
		$args = filter_var_array( $args, $this->getSurveyParameters() );
		
		if ( is_array( $args ) ) {
			$this->parameters = array();
			
			foreach ( $args as $name => $value ) {
				if ( !is_null( $value ) && $value !== false ) {
					$this->parameters['survey-data-' . $name] = $value;
				}
			}
			
			$this->parameters['class'] = 'surveytag';
		}
		else {
			throw new MWException( 'Invalid parameters for survey tag.' );
		}
	}
	
	/**
	 * Renrder the survey div.
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
			$po /* ParserOutput */ = $parser->getOutput();
			
			// For backward compatibility with MW < 1.17.
//			if ( is_callable( array( $po, 'addModules' ) ) ) {
//				$po->addModules( 'ext.survey.jquery' );
//			}
//			else {
				SurveyCompat::addResourceModules( $po, 'ext.survey.jquery' );
//			}
		}
		
		return Html::element(
			'span',
			$this->parameters,
			$this->contents
		);
	}
	
	/**
	 * 
	 * 
	 * @since 0.1
	 * 
	 * @param array $args
	 * 
	 * @return array
	 */
	protected function getSurveyParameters() {
		return array(
			'id' => array( 'filter' => FILTER_VALIDATE_INT, 'options' => array( 'min_range' => 1 ) ),
			'name' => array(),
		);
	}

}
