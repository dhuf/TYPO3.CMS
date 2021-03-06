<?php
declare(strict_types = 1);

namespace TYPO3\CMS\Frontend\Tests\Unit\DataProcessing;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Exception\SiteNotFoundException;
use TYPO3\CMS\Core\Routing\SiteMatcher;
use TYPO3\CMS\Core\Site\Entity\Site;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\DataProcessing\SiteProcessor;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Testcase
 */
class SiteProcessorTest extends UnitTestCase
{

    /**
     * @test
     */
    public function siteIsRetrieved(): void
    {
        $processorConfiguration = ['as' => 'variable'];
        $mockedContentObjectRenderer = $this->getAccessibleMock(ContentObjectRenderer::class, ['stdWrapValue'], [], '', false);
        $mockedContentObjectRenderer->expects($this->any())->method('stdWrapValue')->with('as', $processorConfiguration, 'site')->willReturn('variable');

        $site = new Site('site123', 123, []);

        $subject = $this->getAccessibleMock(SiteProcessor::class, ['getCurrentSite'], []);
        $subject->expects($this->any())->method('getCurrentSite')->willReturn($site);

        $processedData = $subject->process($mockedContentObjectRenderer, [], $processorConfiguration, []);

        $this->assertEquals($site, $processedData['variable']);
    }

    /**
     * @test
     */
    public function nullIsProvidedIfSiteCouldNotBeRetrieved(): void
    {
        $processorConfiguration = ['as' => 'variable'];
        $mockedContentObjectRenderer = $this->getAccessibleMock(ContentObjectRenderer::class, ['stdWrapValue'], [], '', false);
        $mockedContentObjectRenderer->expects($this->any())->method('stdWrapValue')->with('as', $processorConfiguration, 'site')->willReturn('variable');

        $matcherMock = $this->getMockBuilder(SiteMatcher::class)->disableOriginalConstructor()->getMock();
        $matcherMock->expects($this->any())->method('matchByPageId')->willThrowException(new SiteNotFoundException('message', 1550670118));

        $subject = $this->getAccessibleMock(SiteProcessor::class, ['getMatcher', 'getCurrentPageId'], []);
        $subject->expects($this->any())->method('getMatcher')->willReturn($matcherMock);
        $subject->expects($this->any())->method('getCurrentPageId')->willReturn(1);

        $processedData = $subject->process($mockedContentObjectRenderer, [], $processorConfiguration, []);

        $this->assertNull($processedData['variable']);
    }
}
