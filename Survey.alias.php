<?php

/**
 * Aliases for the special pages of the Survey extension.
 *
 * @since 0.1
 *
 * @file Survey.alias.php
 * @ingroup Survey
 *
 * @licence GNU GPL v3+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */

$specialPageAliases = array();

/** English (English) */
$specialPageAliases['en'] = array(
	'EditSurvey' => array( 'EditSurvey', 'Survey' ),
	'Surveys' => array( 'Surveys' ),
	'SurveyStats' => array( 'SurveyStats', 'SurveyStatistics' ),
	'TakeSurvey' => array( 'TakeSurvey' ),
);

/** German (Deutsch) */
$specialPageAliases['de'] = array(
	'EditSurvey' => array( 'Umfrage_bearbeiten' ),
	'Surveys' => array( 'Umfragen' ),
	'SurveyStats' => array( 'Umfragestatistiken' ),
	'TakeSurvey' => array( 'Umfrage_beantworten' ),
);

/** Swiss German (Alemannisch) */
$specialPageAliases['gsw'] = array(
	'EditSurvey' => array( 'Umfroog_bearbeite' ),
	'Surveys' => array( 'Umfrooge' ),
	'SurveyStats' => array( 'Umfroogstatistike' ),
	'TakeSurvey' => array( 'Umfroog_beantworte' ),
);

/** Interlingua (Interlingua) */
$specialPageAliases['ia'] = array(
	'EditSurvey' => array( 'Modificar_questionario', 'Questionario' ),
	'Surveys' => array( 'Questionarios' ),
	'SurveyStats' => array( 'Statisticas_de_questionarios' ),
	'TakeSurvey' => array( 'Responder_a_questionario' ),
);

/** Japanese (日本語) */
$specialPageAliases['ja'] = array(
	'EditSurvey' => array( '編集アンケート' ),
	'Surveys' => array( 'アンケート' ),
	'SurveyStats' => array( 'アンケート統計情報' ),
);

/** Luxembourgish (Lëtzebuergesch) */
$specialPageAliases['lb'] = array(
	'EditSurvey' => array( 'Ëmfro_änneren' ),
	'Surveys' => array( 'Ëmfroen' ),
	'SurveyStats' => array( 'Statistike_vun_Ëmfroen' ),
	'TakeSurvey' => array( 'Bei_der_Ëmfro_matmaachen' ),
);

/** Macedonian (Македонски) */
$specialPageAliases['mk'] = array(
	'EditSurvey' => array( 'УредиАнкета', 'Анкета' ),
	'Surveys' => array( 'Анкети' ),
	'SurveyStats' => array( 'СтатистикиЗаАнкети' ),
	'TakeSurvey' => array( 'ПополниАнкета' ),
);

/** Norwegian Bokmål (‪Norsk (bokmål)‬) */
$specialPageAliases['nb'] = array(
	'EditSurvey' => array( 'Rediger_undersøkelse' ),
	'Surveys' => array( 'Undersøkelser' ),
	'SurveyStats' => array( 'Undersøkelsesstatistikk' ),
	'TakeSurvey' => array( 'Ta_undersøkelse' ),
);

/** Dutch (Nederlands) */
$specialPageAliases['nl'] = array(
	'EditSurvey' => array( 'Bewerkingsvragenlijst' ),
	'Surveys' => array( 'Vragenlijsten' ),
	'SurveyStats' => array( 'Vragenlijstresultaten' ),
	'TakeSurvey' => array( 'VragenlijstBeantwoorden' ),
);

/** Simplified Chinese (‪中文(简体)‬) */
$specialPageAliases['zh-hans'] = array(
	'Surveys' => array( '问卷' ),
	'TakeSurvey' => array( '做问卷' ),
);