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

	public function newHooksInstance() {
		return new Hooks(
			$this->getServiceContainer()->getMainConfig(),
			$this->getServiceContainer()->getSpecialPageFactory(),
			$this->getServiceContainer()->getUserOptionsLookup(),
			null
		);
	}

	public static function provideOnBeforePageDisplay() {
		return [
			'no prefix' => [ 'Main Page', 'Main Page', 'Main Page' ],
			'with prefix' => [ 'Category:Main Page', 'Main Page', 'Main Page' ],
			'special with prefix' => [ 'Special:ListFiles', 'ListFiles', 'ListFiles' ],
			'special no prefix' => [ 'Special:Watchlist', 'Watchlist', 'Watchlist' ],
		];
	}

	/**
	 * @dataProvider provideOnBeforePageDisplay
	 * @covers HidePrefix::onBeforePageDisplay
	 */
	public function testOnBeforePageDisplay( $pagename, $expectedTitle, $pageTitle ) {
		$t = Title::newFromText( $pagename );
		$t->setContentModel( CONTENT_MODEL_WIKITEXT );
		$skin = new SkinTemplate();
		$output = $this->createMock( OutputPage::class );
		$output->method( 'getTitle' )->willReturn( $t );
		$output->method( 'isArticle' )->willReturn( true );
		$output->method( 'getPageTitle' )->willReturn( $pageTitle );
		$output->expects( $this->once() )->method( 'setPageTitle' )->with( $expectedTitle );

		HidePrefix::onBeforePageDisplay( $output, $skin );
	}
}
