<?php

/**
 * Static class for hooks handled by the Survey extension.
 *
 * @since 0.1
 *
 * @file Survey.hooks.php
 * @ingroup Survey
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class SurveyHooks {

	/**
	 * Register the survey tag extension when the parser initializes.
	 *
	 * @since 0.1
	 *
	 * @param Parser $parser
	 *
	 * @return boolean
	 */
	public static function onParserFirstCallInit( Parser &$parser ) {
		$parser->setHook( 'survey', __CLASS__ . '::onSurveyRender' );
		return true;
	}

	/**
	 * Render the survey tag.
	 *
	 * @since 0.1
	 *
	 * @param mixed $input
	 * @param array $args
	 * @param Parser $parser
	 * @param PPFrame $frame
	 * @return string
	 */
	public static function onSurveyRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		$tag = new SurveyTag( $args, $input );
		return $tag->render( $parser );
	}

	/**
	 * Schema update to set up the needed database tables.
	 *
	 * @since 0.1
	 *
	 * @param DatabaseUpdater $updater
	 *
	 * @return boolean
	 */
	public static function onSchemaUpdate( DatabaseUpdater $updater ) {
		$updater->addExtensionUpdate( array(
			'addTable',
			'surveys',
			__DIR__ . '/sql/Survey.sql',
			true
		) );

		$updater->addExtensionUpdate( array(
			'addIndex',
			'surveys',
			'surveys_survey_title',
			__DIR__ . '/sql/AddMissingIndexes.sql',
			true
		) );

		return true;
	}

	/**
	 * Hook to add PHPUnit test cases.
	 *
	 * @since 0.1
	 *
	 * @param array $files
	 *
	 * @return boolean
	 */
	public static function registerUnitTests( array &$files ) {
		$testDir = dirname( __FILE__ ) . '/test/';

		$files[] = $testDir . 'SurveyQuestionTest.php';

		return true;
	}

	/**
	 * Hook to insert things into article headers.
	 *
	 * @since 0.1
	 *
	 * @param Article &$article
	 * @param boolean $outputDone
	 * @param boolean $useParserCache
	 *
	 * @return boolean
	 */
	public static function onArticleViewHeader( Article &$article, &$outputDone, &$useParserCache ) {
		if ( !Survey::has( array( 'enabled' => 1 ) ) ) {
			return true;
		}

		$user = $article->getContext()->getUser();
		$surveys = Survey::select(
			array(
				'id', 'namespaces', 'ratio', 'expiry', 'min_pages'
			),
			array(
				'enabled' => 1,
				'user_type' => Survey::getTypesForUser( $user )
			)
		);

		/**
		 * @var Survey $survey
		 */
		foreach ( $surveys as $survey ) {
			if ( count( $survey->getField( 'namespaces' ) ) == 0 ) {
				$nsValid = true;
			}
			else {
				$nsValid = in_array( $article->getTitle()->getNamespace(), $survey->getField( 'namespaces' ) );
			}

			if ( $nsValid ) {
				global $wgOut;
				if ( method_exists( $wgOut, 'addWikiTextAsInterface' ) ) {
					// MW 1.32+
					$wgOut->addWikiTextAsInterface( Xml::element(
						'survey',
						array(
							'id' => $survey->getId(),
							'ratio' => $survey->getField( 'ratio' ),
							'expiry' => $survey->getField( 'expiry' ),
							'min-pages' => $survey->getField( 'min_pages' ),
						)
					) );
				} else {
					$wgOut->addWikiText( Xml::element(
						'survey',
						array(
							'id' => $survey->getId(),
							'ratio' => $survey->getField( 'ratio' ),
							'expiry' => $survey->getField( 'expiry' ),
							'min-pages' => $survey->getField( 'min_pages' ),
						)
					) );
				}
			}
		}

		return true;
	}

	/**
	 * Adds a link to Admin Links page.
	 *
	 * @since 0.1
	 *
	 * @param ALTree $admin_links_tree
	 *
	 * @return boolean
	 */
	public static function addToAdminLinks( &$admin_links_tree ) {
		$section = new ALSection( 'Survey' );
		$row = new ALRow( 'smw' );
	    $row->addItem( AlItem::newFromSpecialPage( 'Surveys' ) );
		$section->addRow( $row );
		$admin_links_tree->addSection( $section, 'Survey' );
	    return true;
	}

}
