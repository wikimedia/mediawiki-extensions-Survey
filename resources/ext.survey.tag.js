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
			'ssurvey-id-' + options.id
			: 'ssurvey-name-' + options.name
	}
	
	function shouldShowSurvey( options ) {
		if ( !options.cookie ) {
			return true;
		}
		else {
			var cookie = getCookie( options );
			return ( options.pages === 0 && cookie !== 'done' )
				|| ( options.pages !== 0 && parseInt( cookie ) >= options.pages );
		}
	}
	
	function getCookie( options ) {
		var cookie = $.cookie( getCookieName( options ) );
		survey.log( 'read "' + cookie + '" from cookie ' + getCookieName( options ) );
		return cookie;
	}
	
	function setCookie( options, cookieValue ) {
		if ( options.cookie ) {
			var date = new Date();
			date.setTime( date.getTime() + options.expiry * 1000 );
			$.cookie( getCookieName( options ), cookieValue, { 'expires': date, 'path': '/' } );
			survey.log( 'wrote "' + cookieValue + '" to cookie ' + getCookieName( options ) );
		}
	}
	
	function hasCookie( options ) {
		return getCookie( options ) !== null;
	}
	
	function winsLottery( options ) {
		var rand = Math.random();
		survey.log( 'doLottery: ' + rand + ' < ' + options.ratio );
		return rand < options.ratio;
	}
	
	function initTag( $tag ) {
		var ratioAttr = $tag.attr( 'survey-data-ratio' );
		var expiryAttr = $tag.attr( 'survey-data-expiry' );
		var pagesAttr = $tag.attr( 'survey-data-min-pages' );
		
		var options = {
			'ratio': typeof ratioAttr === 'undefined' ? 1 : parseFloat( ratioAttr ) / 100,
			'cookie': $tag.attr( 'survey-data-cookie' ) !== 'no',
			'expiry': typeof expiryAttr === 'undefined' ? 60 * 60 * 24 * 30 : 60 * 60 * 24 * 30,//parseInt( expiryAttr ),
			'pages': typeof pagesAttr === 'undefined' ? 0 : parseInt( pagesAttr )
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
		
		if ( hasCookie( options ) || winsLottery( options ) ) {
			if ( shouldShowSurvey( options ) ) {
				$tag.mwSurvey( options );
				setCookie( options, 'done' );
			}
			else if ( options.pages !== 0 ) {
				var nr = parseInt( getCookie( options ) );
				setCookie( options, ( isNaN( nr ) ? 0 : nr ) + 1 )
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