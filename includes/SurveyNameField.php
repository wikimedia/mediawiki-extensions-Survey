<?php

use MediaWiki\Html\Html;

/**
 * Administration interface for a survey.
 *
 * @since 0.1
 *
 * @file SpecialNameField.php
 * @ingroup Survey
 *
 * @license GPL-3.0-or-later
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SurveyNameField extends HTMLFormField {
	/**
	 * @param string $value
	 * @return string
	 */
	public function getInputHTML( $value ) {
		return Html::element(
			'span',
			[
				'style' => $this->mParams['style']
			],
			$value
		);
	}
}
