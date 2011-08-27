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
	 * @param OuputPage|ParserOutput $out
	 * @param array|string $modules
	 */
	public static function addResourceModules( $out, $modules ) {
		global $egSurveyScriptPath, $egSurveyMessages;
		static $addedJsMessages = false, $addedBaseJs = false;
		
		$modules = (array)$modules;
		
		if ( !$addedBaseJs ) {
			$out->addHeadItem(
				Html::linkedScript( $egSurveyScriptPath . '/ext.survey.js' ),
				'ext.survey'
			);
			
			$addedBaseJs = true;
		}
		
		foreach ( $modules as $module ) {
			switch ( $module ) {
				case 'ext.survey.special':
					$out->addHeadItem(
						Html::linkedScript( $egSurveyScriptPath . '/ext.survey.special.survey.js' ),
						$module
					);
					break;
				case 'ext.survey.jquery':
					$out->addHeadItem(
						Html::linkedStyle( $egSurveyScriptPath . '/fancybox/jquery.fancybox-1.3.4.css' ) .
						Html::linkedScript( $egSurveyScriptPath . '/fancybox/jquery.fancybox-1.3.4.js' ) .
						Html::linkedScript( $egSurveyScriptPath . '/jquery.survey.js' ),
						$module
					);
					break;
			}
			
			if ( array_key_exists( $module, $egSurveyMessages ) ) {
				if ( !$addedJsMessages ) {
					$out->addHeadItem( Html::inlineScript( 'var wgSurveyMessages = [];' ), uniqid() );
					
					$addedJsMessages = true;
				}
				
				$messages = array();
			
				foreach ( $egSurveyMessages[$module] as $msg ) {
					$messages[$msg] = wfMsgNoTrans( $msg );
				}
			
				$out->addHeadItem( Html::inlineScript(
					'Array.push.apply( wgSurveyMessages, ' . FormatJson::encode( $messages ) . ');'
				), uniqid() );	
			}
		}
	}
	
}