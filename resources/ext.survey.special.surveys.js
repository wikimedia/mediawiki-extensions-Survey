/**
 * JavaScript for the Survey MediaWiki extension.
 *
 * @see https://secure.wikimedia.org/wikipedia/mediawiki/wiki/Extension:Survey
 *
 * @license GNU GPL v3 or later
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

( function () {
	$( function () {

		function deleteSurvey( options, successCallback, failCallback ) {
			$.post(
				mw.config.get( 'wgScriptPath' ) + '/api.php',
				{
					action: 'deletesurvey',
					format: 'json',
					ids: options.id,
					token: options.token
				},
				function ( data ) {
					if ( data.success ) {
						successCallback();
					} else {
						failCallback( mw.msg( 'surveys-special-delete-failed' ) );
					}
				}
			);
		}

		$( '.survey-delete' ).on( 'click', function () {
			var $this = $( this );

			if ( confirm( mw.msg( 'surveys-special-confirm-delete' ) ) ) {
				deleteSurvey(
					{
						id: $this.attr( 'data-survey-id' ),
						token: $this.attr( 'data-survey-token' )
					},
					function () {
						$this.closest( 'tr' ).slideUp( 'slow', function () {
							$( this ).remove();
						} );
					},
					function ( error ) {
						alert( error );
					}
				);
			}
			return false;
		} );

	} );

}() );
