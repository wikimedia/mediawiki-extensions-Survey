<?php

use MediaWiki\Html\Html;

/**
 * Administration interface for a survey.
 *
 * @since 0.1
 *
 * @file SurveyQuestionField.php
 * @ingroup Survey
 *
 * @license GPL-3.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SurveyQuestionField extends HTMLFormField {
	/**
	 * @param string $value
	 * @return string
	 */
	public function getInputHTML( $value ) {
		$attribs = [
			'class' => 'survey-question-data'
		];

		foreach ( $this->mParams['options'] as $name => $value ) {
			if ( is_bool( $value ) ) {
				$value = $value ? '1' : '0';
			} elseif ( is_object( $value ) || is_array( $value ) ) {
				$value = FormatJson::encode( $value );
			}

			$attribs['data-' . $name] = $value;
		}

		return Html::element(
			'div',
			$attribs
		);
	}
}
