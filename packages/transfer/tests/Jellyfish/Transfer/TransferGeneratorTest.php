<?php

declare(strict_types=1);

namespace Jellyfish\Transfer;

use Codeception\Test\Unit;
use Jellyfish\Transfer\Definition\ClassDefinitionInterface;
use Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface;
use Jellyfish\Transfer\Generator\ClassGeneratorInterface;

class TransferGeneratorTest extends Unit
{
    /**
     * @var \Jellyfish\Transfer\TransferGeneratorInterface
     */
    protected TransferGeneratorInterface $transferGenerator;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinitionMapLoaderInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $classDefinitionMapLoaderMock;

    /**
     * @var \Jellyfish\Transfer\Generator\ClassGeneratorInterface[]|\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected ?array $classGeneratorMocks = null;

    /**
     * @var \Jellyfish\Transfer\Definition\ClassDefinition[]\PHPUnit\Framework\MockObject\MockObject[]
     */
    protected ?array $classDefinitionMapMock = null;

    /**
     * @return void
     */
    protected function _before(): void
    {
        parent::_before();

        $this->classDefinitionMapMock = [
            $this->classDefinitionMapLoaderMock = $this->getMockBuilder(ClassDefinitionInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->classDefinitionMapLoaderMock = $this->getMockBuilder(ClassDefinitionMapLoaderInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->classGeneratorMocks = [
            $this->getMockBuilder(ClassGeneratorInterface::class)
                ->disableOriginalConstructor()
                ->getMock(),
        ];

        $this->transferGenerator = new TransferGenerator(
            $this->classDefinitionMapLoaderMock,
            $this->classGeneratorMocks
        );
    }

    /**
     * @return void
     */
    public function testGenerate(): void
    {
        $this->classDefinitionMapLoaderMock->expects(static::atLeastOnce())
            ->method('load')
            ->willReturn($this->classDefinitionMapMock);

        $this->classGeneratorMocks[0]->expects(static::atLeastOnce())
            ->method('generate')
            ->with($this->classDefinitionMapMock[0])
            ->willReturn($this->classGeneratorMocks[0]);

        static::assertEquals(
            $this->transferGenerator,
            $this->transferGenerator->generate()
        );
    }
}
