/**
 * JavasSript for the Survey MediaWiki extension.
 * @see https://secure.wikimedia.org/wikipedia/mediawiki/wiki/Extension:Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

(function( $, survey ) {
	
	function getCookieName( options ) {
		return ( typeof options.id !== 'undefined' ) ?
			'survey-id-' + options.id
			: 'survey-name-' + options.name
	}
	
	function shouldShowSurvey( options ) {
		var shouldShow = $.cookie( getCookieName( options ) ) != 'done';
		survey.log( 'shouldShowSurvey ' + getCookieName( options ) + ': ' + shouldShow.toString() );
		return shouldShow;
	}
	
	function onSurveyShown( options ) {
		var expirySeconds = ( typeof options.expiry !== 'undefined' ) ? options.expiry : 60 * 60 * 24 * 30;
		var date = new Date();
		date.setTime( date.getTime() + expirySeconds * 1000 );
		$.cookie( getCookieName( options ), 'done', { 'expires': date, 'path': '/' } );
	}
	
	function initTag( $tag ) {
		var options = {};
		
		if ( $tag.attr( 'survey-data-id' ) ) {
			options['id'] = $tag.attr( 'survey-data-id' );
		} else if ( $tag.attr( 'survey-data-name' ) ) {
			options['name'] = $tag.attr( 'survey-data-name' );
		}
		else {
			// TODO
			return;
		}
		
		if ( $tag.attr( 'survey-data-cookie' ) === 'no' ) {
			$tag.mwSurvey( options );
		}
		else {
			if ( shouldShowSurvey( options ) ) {
				options['onShow'] = function( eventArgs ) {
					onSurveyShown( options );
				};
				
				$tag.mwSurvey( options );
			}
		}
	}
	
	$( document ).ready( function() {
	
		$( '.surveytag' ).each( function( index, domElement ) {
			initTag( $( domElement ) );
		} );
	
	} );
	
})( jQuery, window.survey );