<?php

namespace Oro\Bundle\SegmentBundle\Tests\Unit\Grid;

use Oro\Bundle\SegmentBundle\Grid\ConfigurationProvider;
use Oro\Bundle\SegmentBundle\Tests\Unit\SegmentDefinitionTestCase;

class ConfigurationProviderTest extends SegmentDefinitionTestCase
{
    const TEST_GRID_NAME       = 'test';
    const TEST_ENTITY          = 'AcmeBundle:UserEntity';
    const TEST_IDENTIFIER_NAME = 'id';
    const TEST_IDENTIFIER      = 32;

    /** @var ConfigurationProvider */
    protected $provider;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $doctrine;

    public function setUp()
    {
        $this->doctrine = $this->getDoctrine(
            [self::TEST_ENTITY => []],
            [self::TEST_ENTITY => [self::TEST_IDENTIFIER_NAME]]
        );
        $this->provider = new ConfigurationProvider($this->getFunctionProvider(), $this->doctrine);
    }

    public function tearDown()
    {
        unset($this->provider);
    }

    public function testIsApplicable()
    {
        $this->assertTrue($this->provider->isApplicable('oro_segment_grid_2'));
        $this->assertFalse($this->provider->isApplicable('oro_report_grid_2'));
    }

    public function testGetConfiguration()
    {
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())->method('find')->with(2)
            ->will($this->returnValue($this->getSegment()));

        $this->doctrine->expects($this->once())->method('getRepository')->with('OroSegmentBundle:Segment')
            ->will($this->returnValue($repository));
        $result = $this->provider->getConfiguration('oro_segment_grid_2');
        $this->assertInstanceOf('Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration', $result);
    }

    /**
     * @dataProvider definitionProvider
     *
     * @param mixed $definition
     * @param bool  $expectedResult
     */
    public function testIsConfigurationValid($definition, $expectedResult)
    {
        $repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()->getMock();
        $repository->expects($this->once())->method('find')->with(2)
            ->will($this->returnValue($this->getSegment(false, $definition)));

        $this->doctrine->expects($this->once())->method('getRepository')->with('OroSegmentBundle:Segment')
            ->will($this->returnValue($repository));
        $result = $this->provider->isConfigurationValid('oro_segment_grid_2');
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @return array
     */
    public function definitionProvider()
    {
        return [
            'valid'     => [$this->getDefaultDefinition(), true],
            'not valid' => [['empty array'], false]
        ];
    }
}
