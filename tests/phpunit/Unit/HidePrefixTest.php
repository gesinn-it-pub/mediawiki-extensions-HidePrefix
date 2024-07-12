<?php

use MediaWiki\Linker\LinkRenderer;
use MediaWiki\Linker\LinkTarget;
use PHPUnit\Framework\TestCase;

class HidePrefixTest extends TestCase {

	/**
	 * @covers HidePrefix::onHtmlPageLinkRendererBegin
	 */
	public function testOnHtmlPageLinkRendererBeginTextNotSet() {
		$linkRenderer = $this->createMock( LinkRenderer::class );
		$target = $this->createMock( LinkTarget::class );
		$target->expects( $this->any() )
		   ->method( 'getText' )
		   ->willReturn( 'TargetPage' );

		// Simulate input parameters
		$text = null;
		$extraAttribs = [];
		$query = '';
		$ret = '';

		// Call the method
		$result = HidePrefix::onHtmlPageLinkRendererBegin(
			$linkRenderer,
			$target,
			$text,
			$extraAttribs,
			$query,
			$ret
		);

		// Assert the result
		$this->assertTrue( $result );
		$this->assertSame( 'TargetPage', $text );
	}

	/**
	 * @covers HidePrefix::onHtmlPageLinkRendererBegin
	 */
	public function testOnHtmlPageLinkRendererBeginTextAlreadySet() {
		$linkRenderer = $this->createMock( LinkRenderer::class );
		$target = $this->createMock( LinkTarget::class );
		$target->expects( $this->once() )
			   ->method( 'getText' )
			   ->willReturn( 'TargetPage' );

		$text = 'CurrentPage';
		$extraAttribs = [];
		$query = '';
		$ret = '';

		$result = HidePrefix::onHtmlPageLinkRendererBegin(
			$linkRenderer,
			$target,
			$text,
			$extraAttribs,
			$query,
			$ret
		);

		$this->assertTrue( $result );
		$this->assertNotSame( 'TargetPage', $text );
	}

	/**
	 * @covers HidePrefix::onHtmlPageLinkRendererBegin
	 */
	public function testOnHtmlPageLinkRendererBeginTitlesMatch() {
		$linkRenderer = $this->createMock( LinkRenderer::class );
		$target = $this->createMock( LinkTarget::class );
		$target->expects( $this->any() )
			   ->method( 'getText' )
			   ->willReturn( 'TargetPage' );

		$text = $target->getText();
		$extraAttribs = [];
		$query = '';
		$ret = '';

		// Mock Title objects
		$mockTitle = $this->createMock( Title::class );
		$mockTitle->expects( $this->any() )
				  ->method( 'getPrefixedText' )
				  ->willReturn( 'TargetPage' );

		$titleFromText = $mockTitle;
		$titleFromLinkTarget = $mockTitle;

		$result = HidePrefix::onHtmlPageLinkRendererBegin(
			$linkRenderer,
			$target,
			$text,
			$extraAttribs,
			$query,
			$ret
		);

		$this->assertTrue( $result );
		$this->assertSame( $text, $mockTitle->getPrefixedText() );
	}

	/**
	 * @covers HidePrefix::onBeforePageDisplay
	 */
	public function testOnBeforePageDisplay() {
		// Create mock OutputPage and Skin objects
		$outMock = $this->getMockBuilder( 'OutputPage' )
						->disableOriginalConstructor()
						->getMock();

		// Create mock Skin object
		$skMock = $this->getMockBuilder( 'Skin' )
					   ->disableOriginalConstructor()
					   ->getMock();

		// Example page title with prefix
		$pageTitleWithPrefix = 'Prefix:Example_Title';
		$pageTitleWithoutPrefix = 'Example_Title';

		// Create a mock Title object
		$titleMock = $this->createMock( Title::class );
		$titleMock->method( 'getPrefixedText' )->willReturn( $pageTitleWithPrefix );
		$titleMock->method( 'getText' )->willReturn( $pageTitleWithoutPrefix );

		$outMock->expects( $this->any() )
				->method( 'getTitle' )
				->willReturn( $titleMock );

		$title = $outMock->getTitle();

		$outMock->expects( $this->any() )
				->method( 'getPageTitle' )
				->willReturn( $pageTitleWithPrefix );

		// Assert that pageTitle is with prefix
		$this->assertSame( $title->getPrefixedText(), $outMock->getPageTitle() );

		$outMock->expects( $this->any() )
				->method( 'setPageTitle' )
				->willReturn( $pageTitleWithoutPrefix );
		$pageTitle = $outMock->setPageTitle( $pageTitleWithoutPrefix );

		// Get the full title with prefix and split it
		$titleWithPrefix = $title->getPrefixedText();
		$titleWithoutPrefix = explode( ':', $titleWithPrefix, 2 );

		// Assert that pageTitle is without prefix
		$this->assertSame( $titleWithoutPrefix[1], $pageTitle );
	}

	/**
	 * @covers HidePrefix::onBeforePageDisplay
	 */
	public function testOnBeforePageDisplayWhenTitleIsMissing() {
		// Create necessary mocks or stubs
		$outMock = $this->getMockBuilder( 'OutputPage' )
						->disableOriginalConstructor()
						->getMock();
		$skMock = $this->createMock( 'Skin' );

		// Mock getTitle() to return a mock of Title with a specific behavior
		$titleMock = $this->createMock( 'Title' );
		$outMock->expects( $this->once() )
				->method( 'getTitle' )
				->willReturn( $titleMock );

		$title = $outMock->getTitle();

		// Mock getPageTitle() to return an empty string or null
		$outMock->expects( $this->once() )
				->method( 'getPageTitle' )
				->willReturn( null );

		// Call the method under test
		HidePrefix::onBeforePageDisplay( $outMock, $skMock );

		$this->assertSame( $title->getPrefixedText(), $outMock->getPageTitle() );
	}
}
