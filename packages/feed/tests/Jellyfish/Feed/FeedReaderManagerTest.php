<?php

namespace Jellyfish\Feed;

use Codeception\Test\Unit;
use Jellyfish\Feed\Exception\FeedReaderNotFoundException;

class FeedReaderManagerTest extends Unit
{
    /**
     * @var \Jellyfish\Feed\FeedReaderManagerInterface
     */
    protected $feedReaderManager;

    /**
     * @var \Jellyfish\Feed\FeedReaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $feedReaderMock;

    /**
     * @return void
     */
    protected function _before(): void
    {
        $this->feedReaderMock = $this->getMockBuilder(FeedReaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->feedReaderManager = new FeedReaderManager();
    }

    /**
     * @return void
     */
    public function testGetNonExistingFeedReader(): void
    {
        try {
            $this->feedReaderManager->getFeederReader('test');
            $this->fail();
        } catch (FeedReaderNotFoundException $e) {
        }
    }

    /**
     * @return void
     */
    public function testSetAndGetFeedReader(): void
    {
        $this->feedReaderManager->setFeedReader('test', $this->feedReaderMock);

        $this->assertEquals($this->feedReaderMock, $this->feedReaderManager->getFeederReader('test'));
    }

    /**
     * @return void
     */
    public function testUnsetNonExistingFeedReader(): void
    {
        try {
            $this->feedReaderManager->unsetFeedReader('test');
            $this->fail();
        } catch (FeedReaderNotFoundException $e) {
        }
    }

    /**
     * @return void
     *
     * @throws \ReflectionException
     */
    public function testSetAndUnsetFeedReader(): void
    {
        $identifier = 'test';

        $this->feedReaderManager->setFeedReader($identifier, $this->feedReaderMock);
        $this->assertTrue($this->feedReaderManager->existsFeedReader($identifier));

        $this->feedReaderManager->unsetFeedReader($identifier);
        $this->assertFalse($this->feedReaderManager->existsFeedReader($identifier));
    }

    /**
     * @return void
     */
    public function testReadFromNotExistingFeedReader(): void
    {
        $identifier = 'test';

        try {
            $this->feedReaderManager->readFromFeedReader($identifier);
            $this->fail();
        } catch (FeedReaderNotFoundException $e) {
        }
    }

    /**
     * @return void
     */
    public function testReadFromFeedReader(): void
    {
        $identifier = 'test';
        $feedContent = '{"name": "FooFeed", ...}';

        $this->feedReaderMock->expects($this->atLeastOnce())
            ->method('read')
            ->willReturn($feedContent);

        $this->feedReaderManager->setFeedReader($identifier, $this->feedReaderMock);
        $this->assertTrue($this->feedReaderManager->existsFeedReader($identifier));

        $this->assertEquals($feedContent, $this->feedReaderManager->readFromFeedReader($identifier));
    }
}
