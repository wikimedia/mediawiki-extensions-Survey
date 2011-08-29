/**
 * JavasSript for the Survey MediaWiki extension.
 * @see https://secure.wikimedia.org/wikipedia/mediawiki/wiki/Extension:Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */
(function( $, mw ) { $( document ).ready( function() {

	var _this = this;

	var questionTypes = {};
	
	var $table = null;
	var newQuestionNr = 0;
	var questionNr = 0;
	
	function addQuestion( question ) {
		var $tr = $( '<tr />' ).attr( {
			'class': 'mw-htmlform-field-SurveyQuestionField'
		} );
		
		// TODO: defaulting
		
		$tr.append( $( '<td />' ).attr( { 'class': 'mw-label' } ).html(
			$( '<label />' ).text( mw.msg( 'survey-question-label-nr', ++questionNr ) )
		) );
		
		$tr.append( $( '<td />' ).attr( { 'class': 'mw-input' } ).html(
			getQuestionInput( question )
		) );
		
		$table.append( $tr );
	};
	
	function getQuestionInput( question ) {
		var $input = $( '<div />' ).attr( { 'border': '1px' } );
		
		var $text = $( '<input />' ).attr( {
			'type': 'text',
			'id': 'survey-question-' + question.id
		} );
		
		var $type = survey.htmlSelect( questionTypes, question.type );
		
		var $required = $( '<input />' ).attr( {
			'type': 'checkbox',
			'id': 'survey-required-' + question.id
		} ).append( $( '<label />' ) ).attr( {
			'for': 'survey-required-' + question.id
		} ).text( mw.msg( 'survey-special-label-required' ) );
		
		// TODO
		
		$input.append( $text.prepend( $( '<p />' ).text( mw.msg( 'survey-special-label-text' ) ) ).append( '<br />' ) );
		$input.append( $type.prepend( $( '<label />' ).text( mw.msg( 'survey-special-label-type' ) ) ) );
		$input.append( $required );
		
		
		return $input;
	};
	
	function removeQuestion( question ) {
		
	};
	
	function onAddQuestionRequest() {
		addQuestion( {
			'text': $( '#survey-add-question-text' ).text(),
			'id': 'new-' + newQuestionNr++
		} );
		$( '#survey-add-question-text' ).text( '' );
	};
	
	function initTypes() {
		var types = [ 'text', 'number', 'select', 'radio' ];
		for ( type in types ) {
			questionTypes[survey.msg( 'survey-question-type-' + types[type] )] = type;
		}
	};
	
	function setup() {
		initTypes();
		
		$table = $( '#survey-add-question-text' ).closest( 'tbody' );
		
		$( '#survey-add-question-text' ).keypress( function( event ) { 
			if ( event.which == '13' ) {
				event.preventDefault();
				_this.onAddQuestionRequest();
			}
		} );
		
		$( '#survey-add-question-button' ).click( _this.onAddQuestionRequest ).button();
		
		$( '.survey-question-data' ).each( function( index, domElement ) {
			$this = $( domElement );
			
			addQuestion( {
				'text': $this.attr( 'data-text' ),
				'default': $this.attr( 'data-default' ),
				'required': $this.attr( 'data-required' ),
				'id': $this.attr( 'data-id' ),
				'type': $this.attr( 'data-type' ),
			} );
		} );
		
		$( '.mw-htmlform-submit' ).button();
	};
	
	setup();
	
} ); })( jQuery, window.mediaWiki );