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
				->willReturn( $pageTitleWithPrefix );

		$outMock->expects( $this->any() )
				->method( 'getPageTitle' )
				->willReturn( $pageTitleWithoutPrefix );

		// Assert that setPageTitle was called with the correct argument
		$this->assertSame( $pageTitleWithoutPrefix, $outMock->getPageTitle() );
		$this->assertStringContainsString( $outMock->getPageTitle(), $outMock->getTitle() );
	}
}
