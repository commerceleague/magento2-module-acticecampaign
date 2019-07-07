<?php
/**
 */

namespace CommerceLeague\ActiveCampaign\Test\Unit\Helper;

use CommerceLeague\ActiveCampaign\Helper\Config;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    /**
     * @var MockObject|ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var Config
     */
    protected $config;

    protected function setUp()
    {
        $this->scopeConfig = $this->createPartialMock(
            ScopeConfigInterface::class,
            ['getValue', 'isSetFlag']
        );

        $objectManager = new ObjectManager($this);

        $this->config = $objectManager->getObject(
            Config::class,
            [
                'scopeConfig' => $this->scopeConfig
            ]
        );
    }

    public function testIsEnabledFalse()
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('activecampaign/general/enabled')
            ->willReturn(false);

        $this->assertFalse($this->config->isEnabled());
    }

    public function testIsEnabledTrue()
    {
        $this->scopeConfig->expects($this->once())
            ->method('isSetFlag')
            ->with('activecampaign/general/enabled')
            ->willReturn(true);

        $this->assertTrue($this->config->isEnabled());
    }

    public function testGetApiUrl()
    {
        $apiUrl = 'http://example.com';

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('activecampaign/general/api_url')
            ->willReturn($apiUrl);

        $this->assertEquals($apiUrl, $this->config->getApiUrl());
    }

    public function testGetApiToken()
    {
        $apiToken = 'API_TOKEN';

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('activecampaign/general/api_token')
            ->willReturn($apiToken);

        $this->assertEquals($apiToken, $this->config->getApiToken());
    }

    public function testGetConnectionId()
    {
        $connectionId = '123';

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('activecampaign/general/connection_id')
            ->willReturn($connectionId);

        $this->assertEquals($connectionId, $this->config->getConnectionId());
    }

    public function getAbandonedCartExportAfter()
    {
        $exportAfter = 15;

        $this->scopeConfig->expects($this->once())
            ->method('getValue')
            ->with('activecampaign/abandoned_cart/export_after')
            ->willReturn($exportAfter);

        $this->assertEquals($exportAfter, $this->config->getAbandonedCartExportAfter());
    }
}
