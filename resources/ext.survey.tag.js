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
		return options.cookie || shouldShow;
	}
	
	function onSurveyDone( options ) {
		if ( options.cookie ) {
			var date = new Date();
			date.setTime( date.getTime() + options.expiry * 1000 );
			$.cookie( getCookieName( options ), 'done', { 'expires': date, 'path': '/' } );
			survey.log( 'wrote done to cookie ' + getCookieName( options ) );
		}
	}
	
	function initTag( $tag ) {
		var ratioAttr = $tag.attr( 'survey-data-ratio' );
		var expiryAttr = $tag.attr( 'survey-data-expiry' );
		
		var options = {
			'ratio': typeof ratioAttr === 'undefined' ? 1 : parseFloat( ratioAttr ) / 100,
			'cookie': $tag.attr( 'survey-data-cookie' ) !== 'no',
			'expiry': typeof expiryAttr === 'undefined' ? 60 * 60 * 24 * 30 : expiryAttr
		};
		
		if ( $tag.attr( 'survey-data-id' ) ) {
			options.id = $tag.attr( 'survey-data-id' );
		} else if ( $tag.attr( 'survey-data-name' ) ) {
			options.name = $tag.attr( 'survey-data-name' );
		}
		else {
			// TODO
			return;
		}
		
		var rand = Math.random();
		survey.log( rand + ' < ' + options.ratio );
		
		if ( rand < options.ratio ) {
			if ( shouldShowSurvey( options ) ) {
				options['onShow'] = function( eventArgs ) {
					onSurveyDone( options );
				};
				
				$tag.mwSurvey( options );
			}
		}
		else {
			onSurveyDone( options );
		}
	}
	
	$( document ).ready( function() {
	
		$( '.surveytag' ).each( function( index, domElement ) {
			initTag( $( domElement ) );
		} );
	
	} );
	
})( jQuery, window.survey );