<?php

/**
 * File defining the settings for the Survey extension.
 * More info can be found at http://www.mediawiki.org/wiki/Extension:Survey#Settings
 *
 * NOTICE:
 * Changing one of these settings can be done by assigning to $egSurveySettings,
 * AFTER the inclusion of the extension itself.
 *
 * @since 0.1
 *
 * @file Survey.settings.php
 * @ingroup Survey
 *
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
class SurveySettings {

	protected static function getDefaultSettings() {
		return array(
			'defaultEnabled' => false,
			'defaultUserType' => Survey::$USER_ALL,
			'defaultNamespaces' => array(),
			'defaultRatio' => 100,
			'defaultExpiry' => 60 * 60 * 24 * 30
		);
	}

	public static function getSettings() {
		static $settings = false;

		if ( $settings === false ) {
			$settings = array_merge(
				self::getDefaultSettings(),
				$GLOBALS['egSurveySettings']
			);
		}

		return $settings;
	}

	public static function get( $settingName ) {
		$settings = self::getSettings();
		return array_key_exists( $settingName, $settings ) ? $settings[$settingName] : null;
	}

}
