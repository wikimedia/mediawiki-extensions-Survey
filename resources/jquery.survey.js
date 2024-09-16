/**
 * JavaScript for the Survey MediaWiki extension.
 *
 * @param survey
 * @see https://secure.wikimedia.org/wikipedia/mediawiki/wiki/Extension:Survey
 *
 * @license GNU GPL v3 or later
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

$.browser = {};
( function ( survey ) {

	$.browser.msie = false;
	$.browser.version = 0;
	if (navigator.userAgent.match(/MSIE ([0-9]+)\./)) {
		$.browser.msie = true;
		$.browser.version = RegExp.$1;
	}

	$.fn.mwSurvey = function ( setOptions ) {

		var _this = this;
		this.options = setOptions;

		this.inputs = [];

		this.identifier = null;
		this.identifierType = null;

		this.getSurveyData = function ( options, callback ) {
			var requestArgs = {
				action: 'query',
				list: 'surveys',
				format: 'json',
				suincquestions: 1,
				suprops: '*'
			};

			if ( options.requireEnabled ) {
				requestArgs.suenabled = 1;
			}

			requestArgs[ 'su' + this.identifierType + 's' ] = this.identifier;

			$.getJSON(
				mw.config.get( 'wgScriptPath' ) + '/api.php',
				requestArgs,
				function ( data ) {
					if ( data.surveys ) {
						callback( data.surveys );
					} else if ( data.error ) {
						debugger;
					// TODO
					} else {
						debugger;
					// TODO
					}
				}
			);
		};

		this.getQuestionInput = function ( question ) {
			survey.log( 'getQuestionInput: ' + question.id );
			var type = survey.question.type,
				$input,
				id = 'question-input-' + question.id,
				answers,
				i;

			switch ( question.type ) {
				case type.TEXT:
					$input = $( '<input>' ).attr( {
						id: id,
						class: 'question-input survey-text'
					} );
					break;
				case type.NUMBER:
					$input = $( '<input>' ).numeric().attr( {
						id: id,
						class: 'question-input survey-number',
						size: 7
					} );
					break;
				case type.SELECT:
					answers = {};

					for ( i in question.answers ) {
						answers[ question.answers[ i ] ] = question.answers[ i ];
					}

					$input = survey.htmlSelect( answers, 0, {
						id: id,
						class: 'question-input survey-select'
					} );
					break;
				case type.RADIO:
					answers = {};

					for ( i in question.answers ) {
						answers[ question.answers[ i ] ] = question.answers[ i ];
					}

					$input = survey.htmlRadio(
						answers,
						null,
						id,
						{
							id: id,
							class: 'question-input survey-radio'
						}
					);
					break;
				case type.TEXTAREA:
					$input = $( '<textarea>' ).attr( {
						id: id,
						class: 'question-input survey-textarea',
						cols: 80,
						rows: 2
					} );
					break;
				case type.CHECK:
					$input = $( '<input>' ).attr( {
						id: id,
						type: 'checkbox',
						class: 'question-input survey-check'
					} );
					break;
				default:
					$input = $( '<input>' ).attr( {
						id: id,
						class: 'question-input survey-text'
					} );
			}

			var $q;

			$input.data( 'question-id', question.id );

			this.inputs.push( { input: $input, type: question.type } );

			$q = $( '<div>' ).attr( 'class', 'survey-question' ).html( $input );

			if ( question.type === type.CHECK ) {
				$q.prepend( $( '<label>' ).text( question.text ).attr( 'for', id ) );
			} else {
				$q.prepend( $( '<p>' ).text( question.text ).attr( 'class', 'question-text' ) );
			}

			return $q;
		};

		this.getSurveyQuestion = function ( question ) {
			if ( survey.question.typeHasAnswers( question.type ) &&
			question.answers.length === 0 ) {
				survey.log( 'getSurveyQuestion: no answers for: ' + question.id );
				return '';
			} else {
				return this.getQuestionInput( question );
			}
		};

		this.getSurveyQuestions = function ( questions ) {
			var $questions = $( '<div>' );

			for ( var i in questions ) {
				$questions.append( this.getSurveyQuestion( questions[ i ] ) );
			}

			return $questions;
		};

		this.getAnswers = function () {
			var answers = [];

			for ( var i in this.inputs ) {
				var $input = this.inputs[ i ].input,
					id = $input.data( 'question-id' ),
					value;

				if ( this.inputs[ i ].type === survey.question.type.RADIO ) {
					value = $( 'input:radio[name=question-input-' + id + ']:checked' ).val();
				} else {
					value = $input.val();
				}

				answers.push( {
					text: value,
					question_id: id
				} );
			}

			return JSON.stringify( answers );
		};

		this.submitSurvey = function ( surveyId, callback ) {
			var requestArgs = {
				action: 'submitsurvey',
				format: 'json',
				token: $( this ).attr( 'survey-data-token' ),
				answers: this.getAnswers( surveyId )
			};

			requestArgs[ this.identifierType ] = this.identifier;

			( new mw.Api() ).postWithToken( 'csrf', requestArgs )
				.done ( function () {
					callback();
				// TODO
				}
			);

		};

		this.doCompletion = function () {
			$.fancybox.close();
		};

		this.showCompletion = function ( surveyData ) {
			var $div;
			$div = $( '#survey-' + surveyData.id );

			$div.html( $( '<p>' ).text( surveyData.thanks ) );

			$div.append( $( '<button>' )
				.button( { label: mw.msg( 'survey-jquery-finish' ) } )
				.click( this.doCompletion )
			);
		};

		this.getSurveyBody = function ( surveyData ) {
			var $survey;
			$survey = $( '<div>' );

			$survey.append( $( '<h1>' ).text( surveyData.title ) );

			$survey.append( $( '<p>' ).text( surveyData.header ) );

			$survey.append( this.getSurveyQuestions( surveyData.questions ) );

			var submissionButton = $( '<button>' )
				.button( { label: mw.msg( 'survey-jquery-submit' ) } )
				.click( function () {
					var $this = $( this );
					$this.button( 'disable' );

					if ( true /* isValid */ ) {
						_this.submitSurvey(
							surveyData.id,
							function () {
								if ( surveyData.thanks === '' ) {
									_this.doCompletion();
								} else {
									_this.showCompletion( surveyData );
								}
							}
						);
					} else {
					// TODO

						$this.button( 'enable' );
					}
				} );

			$survey.append( submissionButton );

			$survey.append( $( '<p>' ).text( surveyData.footer ) );

			return $survey;
		};

		this.initSurvey = function ( surveyData ) {
			var $div,
				$link;
			$div = $( '<div>' ).attr( {
				style: 'display:none'
			} ).html( $( '<div>' ).attr( { id: 'survey-' + surveyData.id } ).html( this.getSurveyBody( surveyData ) ) );

			$link = $( '<a>' ).attr( {
				href: '#survey-' + surveyData.id
			} ).html( $div );

			$( this ).html( $link );

			$link.fancybox( {
				// 'width': '75%',
				// 'height': '75%',
				autoScale: false,
				transitionIn: 'none',
				transitionOut: 'none',
				type: 'inline',
				hideOnOverlayClick: false,
				autoDimensions: true
			} );

			$link.trigger( 'click' );

			if ( typeof this.options.onShow === 'function' ) {
				this.options.onShow( { id: surveyData.id } );
			}
		};

		this.init = function () {
			var $this = $( this );

			if ( $this.attr( 'survey-data-id' ) ) {
				this.identifier = $this.attr( 'survey-data-id' );
				this.identifierType = 'id';
			} else if ( $this.attr( 'survey-data-name' ) ) {
				this.identifier = $this.attr( 'survey-data-name' );
				this.identifierType = 'name';
			}

			if ( this.identifier !== null ) {
				this.getSurveyData(
					{
						requireEnabled: $this.attr( 'survey-data-require-enabled' ) !== '0'
					},
					function ( surveyData ) {
						if ( surveyData.hasOwnProperty( 0 ) ) {
							_this.initSurvey( surveyData[ 0 ] );
						} else {
							$this.text( mw.msg( 'survey-jquery-load-failed' ) );
						}
					}
				);
			}

		};

		this.init();

	};
}( window.survey ) );
