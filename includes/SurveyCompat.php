<?php

/**
 * Compatibility methods needed for Survey to work with older
 * versions of MediaWiki.
 * 
 * @since 0.1
 * 
 * @file SurveyCompat.php
 * @ingroup Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

class SurveyCompat {
	
	/**
	 * Add the resources for a resource loader module.
	 * This is needed for MediaWiki < 1.17.
	 * 
	 * @since 0.1 
	 * 
	 * @param OuputPage $out
	 * @param array $modules
	 */
	public static function addResourceModules( OuputPage $out, array $modules ) {
		global $egSurveyScriptPath, $egSurveyMessages;
		static $addedJsMessages = false;
		
		foreach ( $modules as $module ) {
			switch ( $module ) {
				case 'ext.survey.special':
					$out->addHeadItem(
						$module,
						Html::linkedScript( $egSurveyScriptPath . 'ext.survey.special.survey.js' )
					);
					break;
			}
			
			if ( array_key_exists( $module, $egSurveyMessages ) ) {
				if ( !$addedJsMessages ) {
					$out->addInlineScript( 'var wgSurveyMessages = [];' );
					
					$addedJsMessages = true;
				}
				
				$messages = array();
			
				foreach ( $egSurveyMessages[$module] as $msg ) {
					$messages[$msg] = wfMsgNoTrans( $msg );
				}
			
				$out->addInlineScript( 'Array.push.apply( wgSurveyMessages, ' . FormatJson::encode( $messages ) . ');' );	
			}
		}
	}
	
}