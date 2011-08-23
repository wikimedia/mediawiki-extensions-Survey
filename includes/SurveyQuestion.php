<?php

/**
 * Simple survey question object class.
 * 
 * @since 0.1
 * 
 * @file SurveyQuestion.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SurveyQuestion {
	
	protected $id;
	protected $text;
	protected $type;
	protected $required;
	protected $answers;
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param integer|null $id
	 * @param string $text
	 * @param integer $type
	 * @param boolean $required
	 * @param array $answers
	 */
	public function __construct( $id, $text, $type, $required, array $answers = array() ) {
		
	}
	
}