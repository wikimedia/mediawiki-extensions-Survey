/**
 * JavaScript for the Survey MediaWiki extension.
 *
 * @param survey
 * @see https://secure.wikimedia.org/wikipedia/mediawiki/wiki/Extension:Survey
 *
 * @license GNU GPL v3 or later
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

( function ( survey ) {

	function getCookieName( options ) {
		return ( typeof options.id !== 'undefined' ) ?
			'survey-id-' + options.id :
			'survey-name-' + options.name;
	}

	function getCookie( options ) {
		var cookie = mw.cookie.get( getCookieName( options ) );
		survey.log( 'read "' + cookie + '" from cookie ' + getCookieName( options ) );
		return cookie;
	}

	function setCookie( options, cookieValue ) {
		var cookieName = getCookieName( options );
		mw.cookie.set( cookieName, cookieValue, { expires: options.expiry, path: '/' } );
		survey.log( 'wrote "' + cookieValue + '" to cookie ' + cookieName );
	}

	function hasCookie( options ) {
		return getCookie( options ) !== null;
	}

	function winsLottery( options ) {
		var rand = Math.random();
		survey.log( 'doLottery: ' + rand + ' < ' + options.ratio );
		return rand < options.ratio;
	}

	function initCookieSurvey( options, $tag ) {
		if ( hasCookie( options ) || options.ratio === 1 || winsLottery( options ) ) {
			var cookie = getCookie( options );

			if ( cookie !== 'done' ) {
				if ( ( options.pages === 0 || parseInt( cookie, 10 ) >= options.pages ) ) {
					$tag.mwSurvey( options );
					setCookie( options, 'done' );
				} else if ( options.pages !== 0 ) {
					var nr = parseInt( getCookie( options ), 10 );
					setCookie( options, ( isNaN( nr ) ? 0 : nr ) + 1 );
				}
			}
		} else {
			setCookie( options, 'done' );
		}
	}

	function initTag( $tag ) {
		var ratioAttr = $tag.attr( 'survey-data-ratio' ),
			expiryAttr = $tag.attr( 'survey-data-expiry' ),
			pagesAttr = $tag.attr( 'survey-data-min-pages' ),
			options = {
				ratio: typeof ratioAttr === 'undefined' ? 1 : parseFloat( ratioAttr ) / 100,
				cookie: $tag.attr( 'survey-data-cookie' ) !== 'no',
				expiry: typeof expiryAttr === 'undefined' ? 60 * 60 * 24 * 30 : parseInt( expiryAttr, 10 ),
				pages: typeof pagesAttr === 'undefined' ? 0 : parseInt( pagesAttr, 10 )
			};

		if ( $tag.attr( 'survey-data-id' ) ) {
			options.id = $tag.attr( 'survey-data-id' );
		} else if ( $tag.attr( 'survey-data-name' ) ) {
			options.name = $tag.attr( 'survey-data-name' );
		} else {
			// TODO
			return;
		}

		if ( options.cookie ) {
			initCookieSurvey( options, $tag );
		} else {
			$tag.mwSurvey( options );
		}
	}

	$( function () {

		$( '.surveytag' ).each( function ( index, domElement ) {
			initTag( $( domElement ) );
		} );

	} );

}( window.survey ) );
