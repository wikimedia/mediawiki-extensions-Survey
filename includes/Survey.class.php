<?php

/**
 * Simple Survey object class.
 * 
 * @since 0.1
 * 
 * @file Survey.class.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class Survey {
	
	protected $id;
	protected $name;
	protected $enabled;
	
	public static function newFromDBResult() {
		
	}
	
	/**
	 * Constructor.
	 * 
	 * @since 0.1
	 * 
	 * @param integer|null $id
	 * @param string $name
	 * @param boolean $enabled
	 */
	public function __construct( $id, $name, $enabled ) {
		
	}
	
}