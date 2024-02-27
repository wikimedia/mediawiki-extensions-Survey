/**
 * JavaScript for the Survey MediaWiki extension.
 *
 * @see https://secure.wikimedia.org/wikipedia/mediawiki/wiki/Extension:Survey
 *
 * @license GNU GPL v3 or later
 * @author Jeroen De Dauw <jeroendedauw at gmail dot com>
 */

window.survey = new ( function ( survey ) {
	this.log = function ( message ) {
		if ( mw.config.get( 'wgSurveyDebug' ) ) {
			if ( typeof mw === 'undefined' ) {
				if ( typeof console !== 'undefined' ) {
					console.log( 'Survey: ' + message );
				}
			} else {
				return mw.log.call( mw.log, 'Survey: ' + message );
			}
		}
	};

	this.msg = function () {
		var message;
		if ( typeof mw === 'undefined' ) {
			message = window.wgSurveyMessages[ arguments[ 0 ] ];

			for ( var i = arguments.length - 1; i > 0; i-- ) {
				message = message.replace( '$' + i, arguments[ i ] );
			}

			return message;
		} else {
			return mw.msg.apply( mw.msg, arguments );
		}
	};

	this.htmlSelect = function ( options, value, attributes, onChangeCallback ) {
		var message,
			$select;
		$select = $( '<select>' ).attr( attributes );

		for ( message in options ) {
			var attribs = { value: options[ message ] };

			if ( value === options[ message ] ) {
				attribs.selected = 'selected';
			}

			$select.append( $( '<option>' ).text( message ).attr( attribs ) );
		}

		if ( typeof onChangeCallback !== 'undefined' ) {
			$select.on( 'change', function () {
				onChangeCallback( $( this ).val() );
			} );
		}

		return $select;
	};

	this.htmlRadio = function ( options, value, name, attributes ) {
		var $radio = $( '<div>' ).attr( attributes ),
			message;
		$radio.html( '' );

		for ( message in options ) {
			var itemValue = options[ message ],
				id = name + itemValue,
				$input;

			$input = $( '<input>' ).attr( {
				id: id,
				type: 'radio',
				name: name,
				value: itemValue
			} );

			if ( value === options[ message ] ) {
				$input.attr( 'checked', 'checked' );
			}

			$radio.append( $input );
			$radio.append( $( '<label>' ).attr( 'for', id ).text( message ) );
			$radio.append( $( '<br>' ) );
		}

		return $radio;
	};

	this.question = new ( function () {

		this.type = {
			TEXT: 0,
			NUMBER: 1,
			SELECT: 2,
			RADIO: 3,
			TEXTAREA: 4,
			CHECK: 5
		};


		this.typeHasAnswers = function ( t ) {
			console.log('t', window.survey, t);
			return $.inArray(
				t, [ window.survey.question.type.RADIO, window.survey.question.type.SELECT ]
			) !== -1;
		};

		this.getTypeSelector = function ( value, attributes, onChangeCallback ) {
			var options = [],
				msg,
				types = {
					text: survey.question.type.TEXT,
					number: survey.question.type.NUMBER,
					select: survey.question.type.SELECT,
					radio: survey.question.type.RADIO,
					textarea: survey.question.type.TEXTAREA,
					check: survey.question.type.CHECK
				};

			for ( msg in types ) {
				// Messages that can be used here:
				// * survey-question-type-text
				// * survey-question-type-number
				// * survey-question-type-select
				// * survey-question-type-radio
				// * survey-question-type-textarea
				// * survey-question-type-check
				options[ survey.msg( 'survey-question-type-' + msg ) ] = types[ msg ];
			}

			return survey.htmlSelect( options, parseInt( value ), attributes, onChangeCallback );
		};
	})();
	console.log('this', this.question);
})(window.survey);
