<?php

/**
 * ------------------------------------------------------------------------------------------------
 * HidePrefix, a MediaWiki extension for hiding prefix in links and page titles.
 * Copyright (C) 2012 Van de Bugger.
 *
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU Affero General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License along with this
 * program.  If not, see <https://www.gnu.org/licenses/>.
 * ------------------------------------------------------------------------------------------------
 */

use CirrusSearch\Search\ArrayCirrusSearchResult as SearchResults;
use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;

class HidePrefix {

	/**
	 * Hide prefix in links.
	 *
	 * @param LinkRenderer $linkRenderer
	 * @param LinkTarget $target
	 * @param string &$text
	 * @param array &$extraAttribs
	 * @param string &$query
	 * @param string &$ret
	 * @return bool
	 */
	public static function onHtmlPageLinkRendererBegin( LinkRenderer $linkRenderer,
		LinkTarget $target,
		&$text,
		&$extraAttribs,
		&$query,
		&$ret ) {
		if ( !isset( $text ) ) {
			$text = $target->getText();
			return true;
		}

			$html = HtmlArmor::getHtml( $text );
			$title = Title::newFromText( $html );
			$targetTitle = Title::newFromLinkTarget( $target );

		if ( $title !== null && $targetTitle && $title->getPrefixedText() === $targetTitle->getPrefixedText() ) {
			$text = $target->getText();
		}
			return true;
	}

	/**
	 * Hide prefix in page title.
	 *
	 * @param OutputPage &$out
	 * @param Skin &$sk
	 */
	public static function onBeforePageDisplay( &$out, &$sk ) {
		if ( !$out->isArticle() ) {
			return;
		}

		$title = $out->getTitle();
		if ( !$title instanceof Title ) {
			return;
		}

		// result example 'prefix:title', split it to use title
		$titleWithPrefix = $title->getPrefixedText();
		$titleParts = explode( ':', $titleWithPrefix );

		// double check $pageTitle from $out - should contain title of given page
		$pageTitle = trim( $out->getPageTitle() );
		if ( count( $titleParts ) > 1 ) {
			$titleWithoutPrefix = trim( $titleParts[1] );
		} else {
			$titleWithoutPrefix = trim( $titleParts[0] );
		}

		if ( ( $pageTitle === $titleWithoutPrefix ) ||
		( strpos( $pageTitle, $titleWithoutPrefix ) ) ) {
			$out->setPageTitle( $title->getText() );
		}
	}

	/**
	 * Hide prefix in search results.
	 *
	 * @param Title &$title
	 * @param string &$titleSnippet
	 * @param SearchResult $result
	 * @param string $terms
	 * @param SpecialSearch $specialSearch
	 * @param array &$query
	 * @param array &$attributes
	 */
	public static function onShowSearchHitTitle( Title &$title,
		&$titleSnippet,
		SearchResults $result,
		$terms,
		SpecialSearch $specialSearch,
		array &$query,
		array &$attributes ) {
		global $wgHidePrefixInSearchResults;

		if ( $wgHidePrefixInSearchResults ) {
			$title = $result->getTitle();

			if ( !$title instanceof Title ) {
				return;
			}

			// Get the full title with prefix and split it
			$titleWithPrefix = $title->getPrefixedText();
			$titleWithoutPrefix = explode( ':', $titleWithPrefix, 2 );

			// Ensure the prefix exists and update the search result title
			if ( count( $titleWithoutPrefix ) > 1 ) {
				// Set the new title for the search result
				$titleSnippet = $titleWithoutPrefix[1];
			}
		}
	}
}
