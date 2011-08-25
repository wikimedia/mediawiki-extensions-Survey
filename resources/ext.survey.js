/**
 * JavasSript for the Survey MediaWiki extension.
 * @see https://secure.wikimedia.org/wikipedia/mediawiki/wiki/Extension:Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

window.survey = new( function() {
	
	this.log = function( message ) {
		if ( true /* window.wgSurveyDebug */ ) { // TODO
			if ( typeof mediaWiki === 'undefined' ) {
				if ( typeof console !== 'undefined' ) {
					console.log( 'Survey: ' + message );
				}
			}
			else {
				return mediaWiki.log.call( mediaWiki.log, 'Survey: ' + message );
			}
		}
	}
	
	this.msg = function() {
		if ( typeof mediaWiki === 'undefined' ) {
			message = window.wgSurveyMessages[arguments[0]];
			
			for ( var i = arguments.length - 1; i > 0; i-- ) {
				message = message.replace( '$' + i, arguments[i] );
			}
			
			return message;
		}
		else {
			return mediaWiki.msg.apply( mediaWiki.msg, arguments );
		}
	}
	
} )();


