<?php
declare(strict_types=1);
/**
 */
namespace CommerceLeague\ActiveCampaign\Gateway;

use CommerceLeague\ActiveCampaign\Helper\Config as ConfigHelper;
use CommerceLeague\ActiveCampaignApi\Api\AbandonedCartApiResourceInterface;
use CommerceLeague\ActiveCampaignApi\Api\ConnectionApiResourceInterface;
use CommerceLeague\ActiveCampaignApi\Api\ContactApiResourceInterface;
use CommerceLeague\ActiveCampaignApi\Api\CustomerApiResourceInterface;
use CommerceLeague\ActiveCampaignApi\Api\OrderApiResourceInterface;
use CommerceLeague\ActiveCampaignApi\ClientBuilder;
use CommerceLeague\ActiveCampaignApi\CommonClientInterface;
use Http\Adapter\Guzzle6\Client as GuzzleClient;
use Http\Factory\Guzzle\RequestFactory as GuzzleRequestFactory;
use Http\Factory\Guzzle\StreamFactory as GuzzleStreamFactory;

/**
 * Class Client
 */
class Client
{
    /**
     * @var ConfigHelper
     */
    private $configHelper;

    /**
     * @param ConfigHelper $configHelper
     */
    public function __construct(ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * @return CommonClientInterface
     */
    private function getCommonClient(): CommonClientInterface
    {
        $url = $this->configHelper->getApiUrl();
        $token = $this->configHelper->getApiToken();

        $clientBuilder = new ClientBuilder();
        $clientBuilder->setHttpClient(new GuzzleClient());
        $clientBuilder->setRequestFactory(new GuzzleRequestFactory());
        $clientBuilder->setStreamFactory(new GuzzleStreamFactory());

        return $clientBuilder->buildCommonClient($url, $token);
    }

    /**
     * @return AbandonedCartApiResourceInterface
     */
    public function getAbandonedCartApi(): AbandonedCartApiResourceInterface
    {
        return $this->getCommonClient()->getAbandonedCartApi();
    }

    /**
     * @return ConnectionApiResourceInterface
     */
    public function getConnectionApi(): ConnectionApiResourceInterface
    {
        return $this->getCommonClient()->getConnectionApi();
    }

    /**
     * @return ContactApiResourceInterface
     */
    public function getContactApi(): ContactApiResourceInterface
    {
        return $this->getCommonClient()->getContactApi();
    }

    /**
     * @return CustomerApiResourceInterface
     */
    public function getCustomerApi(): CustomerApiResourceInterface
    {
        return $this->getCommonClient()->getCustomerApi();
    }

    /**
     * @return OrderApiResourceInterface
     */
    public function getOrderApi(): OrderApiResourceInterface
    {
        return $this->getCommonClient()->getOrderApi();
    }
}
