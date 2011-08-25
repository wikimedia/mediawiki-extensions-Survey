<?php

/**
 * Base special page for special pages in the Survey extension,
 * taking care of some common stuff and providing compatibility helpers.
 * 
 * @since 0.1
 * 
 * @file SpecialSurveyPage.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
abstract class SpecialSurveyPage extends SpecialPage {
	
	/**
	 * @see SpecialPage::getDescription
	 * 
	 * @since 0.1
	 */
	public function getDescription() {
		return wfMsg( 'special-' . strtolower( $this->getName() ) );
	}
	
	/**
	 * Sets headers - this should be called from the execute() method of all derived classes!
	 * 
	 * @since 0.1
	 */
	public function setHeaders() {
		$out = $this->getOutput();
		$out->setArticleRelated( false );
		$out->setRobotPolicy( 'noindex,nofollow' );
		$out->setPageTitle( $this->getDescription() );
	}	
	
	/**
	 * Main method.
	 * 
	 * @since 0.1
	 * 
	 * @param string $arg
	 */
	public function execute( $subPage ) {
		global $wgUser;
		
		$this->setHeaders();
		$this->outputHeader();
		
		// If the user is authorized, display the page, if not, show an error.
		if ( !$this->userCanExecute( $wgUser ) ) {
			$this->displayRestrictionError();
			return;
		}
	}
	
	/**
	 * Get the OutputPage being used for this instance.
	 * This overrides the getOutput method of Specialpage added in MediaWiki 1.18,
	 * and returns $wgOut for older versions.
	 *
	 * @since 0.1
	 *
	 * @return OutputPage
	 */
	public function getOutput() {
		return version_compare( $GLOBALS['wgVersion'], '1.18', '>=' ) ? parent::getOutput() : $GLOBALS['wgOut'];
	}
	
	/**
	 * Add resource loader modules or use fallback code for
	 * earlier versions of MediaWiki.
	 * 
	 * @since 0.1
	 * 
	 * @param string|array $modules
	 */
	public function addModules( $modules ) {
		$out = $this->getOutput();
		$modules = (array)$modules;
		
		// For backward compatibility with MW < 1.17.
		if ( is_callable( array( $out, 'addModules' ) ) ) {
			$out->addModules( $modules );
		}
		else {
			SurveyCompat::addResourceModules( $out, $modules );
		}
	}
	
}
