/**
 * JavasSript for the Survey MediaWiki extension.
 * @see https://secure.wikimedia.org/wikipedia/mediawiki/wiki/Extension:Survey
 * 
 * @licence GNU GPL v3 or later
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

( function ( $ ) { $.fn.mwSurvey = function( options ) {
	
	var _this = this;
	
	this.getSurveyData = function( options, callback ) {
		var requestArgs = {
			'action': 'query',
			'list': 'surveys',
			'format': 'json',
			'suincquestions': 1,
			'suenabled': 1,
			'suprops': '*'
		};
		
		requestArgs = $.extend( requestArgs, options.requestArgs )
		
		$.getJSON(
			wgScriptPath + '/api.php',
			requestArgs,
			function( data ) {
				if ( data.surveys ) {
					callback( data.surveys );
				}
				else if ( data.error ) {
					debugger;
					// TODO
				}
				else {
					debugger;
					// TODO
				}
			}
		);
	};
	
	this.getQuestionInput = function( question ) {
		survey.log( 'getQuestionInput: ' + question.id );
		
		var type = survey.question.type;
		
		var $input;
		
		switch ( question.type ) {
			case type.TEXT: default:
				$input = $( '<input />' ).attr( {
					'id': 'survey-question-' + question.id,
					'class': 'survey-question survey-text'
				} );
				break;
			case type.NUMBER:
				$input = $( '<input />' ).numeric().attr( {
					'id': 'survey-question-' + question.id,
					'class': 'survey-question survey-number',
					'size': 7
				} );
				break;
			case type.SELECT:
				$input = survey.htmlSelect( question.answers, 0, { 
					'id': 'survey-question-' + question.id,
					'class': 'survey-question survey-select'
				} );
				break;
			case type.RADIO:
				// TODO
				$input = $( '<input />' ).attr( {
					'id': 'survey-question-' + question.id,
					'class': 'survey-question'
				} );
				break;
			case type.TEXTAREA:
				$input = $( '<textarea />' ).attr( {
					'id': 'survey-question-' + question.id,
					'class': 'survey-question survey-textarea',
					'cols': 80,
					'rows': 2
				} );
				break;
		}
		
		return $input;
	};
	
	this.getSurveyQuestion = function( question ) {
		$q = $( '<div />' );
		
		$q.append( $( '<p />' ).text( question.text ) );
		
		$q.append( this.getQuestionInput( question ) )
		
		return $q;
	};
	
	this.getSurveyQuestions = function( questions ) {
		$questions = $( '<div />' );
		
		for ( i in questions ) {
			$questions.append( this.getSurveyQuestion( questions[i] ) );
		}
		
		return $questions;
	};
	
	this.getAnswers = function( surveyId ) {
		var answers = [];
		
		
		
		return JSON.stringify( answers );
	};
	
	this.submitSurvey = function( surveyId, callback ) {
		$.post(
			wgScriptPath + '/api.php',
			{
				'action': 'submitsurvey',
				'format': 'json',
				'ids': options.id,
				'token': options.token,
				'answers': this.getAnswers( surveyId )
			},
			function( data ) {
				callback();
				// TODO
			}	
		);
	};
	
	this.doCompletion = function() {
		$.fancybox.close();
	};
	
	this.showCompletion = function( surveyData ) {
		$div = $( '#survey-' + surveyData.id );
		
		$div.html( $( '<p />' ).text( surveyData.thanks ) );
		
		$div.append( $( '<button />' )
			.button( { label: mw.msg( 'survey-jquery-finish' ) } )
			.click( this.doCompletion )
		);
	};
	
	this.getSurveyBody = function( surveyData ) {
		$survey = $( '<div />' );
		
		$survey.append( $( '<h1 />' ).text( surveyData.name ) );
		
		$survey.append( $( '<p />' ).text( surveyData.header ) );
		
		$survey.append( this.getSurveyQuestions( surveyData.questions ) );
		
		var submissionButton = $( '<button />' )
			.button( { label: mw.msg( 'survey-jquery-submit' ) } )
			.click( function() {
				var $this = $( this ); 
				$this.button( 'disable' );
				
				if ( true /* isValid */ ) {
					_this.submitSurvey(
						surveyData.id,
						function() {
							if ( surveyData.thanks == '' ) {
								_this.doCompletion();
							}
							else {
								_this.showCompletion( surveyData );
							}
						}
					);
				}
				else {
					// TODO
					
					$this.button( 'enable' );
				}
			} );
		
		$survey.append( submissionButton );
		
		$survey.append( $( '<p />' ).text( surveyData.footer ) );
		
		return $survey;
	};
	
	this.initSurvey = function( surveyData ) {
		$div = $( '<div />' ).attr( {
			'style': 'display:none'
		} ).html( $( '<div />' ).attr( { 'id': 'survey-' + surveyData.id } ).html( this.getSurveyBody( surveyData ) ) );
		
		$link = $( '<a />' ).attr( {
			'href': '#survey-' + surveyData.id,
		} ).html( $div );
		
		$( this ).html( $link );
		
		$link.fancybox( {
//			'width'         : '75%',
//			'height'        : '75%',
			'autoScale'     : false,
			'transitionIn'  : 'none',
			'transitionOut' : 'none',
			'type'          : 'inline',
			'hideOnOverlayClick': false,
			'autoDimensions': true
		} );
		
		$link.click();
	};
	
	this.init = function() {
		var $this = $( this );
		var identifier = false;
		var type;
		
		if ( $this.attr( 'survey-data-id' ) ) {
			identifier = $this.attr( 'survey-data-id' );
			type = 'suids';
		}
		else if ( $this.attr( 'survey-data-name' ) ) {
			identifier = $this.attr( 'survey-data-name' );
			type = 'sunames';
		}
		
		if ( identifier !== false ) {
			var requestArgs = {};
			requestArgs[type] = identifier;
			
			this.getSurveyData(
				{
					'requestArgs': requestArgs
				},
				function( surveyData ) {
					for ( i in surveyData ) {
						_this.initSurvey( surveyData[i] );
					}
				}
			);
		}
	};
	
	this.init();
	
}; } )( jQuery );

(function( $ ) { $( document ).ready( function() {
	
	$( '.surveytag' ).each( function( index, domElement ) {
		$( domElement ).mwSurvey();
	} );
	
} ); })( jQuery );