<?php
declare(strict_types=1);
/**
 */
namespace CommerceLeague\ActiveCampaign\MessageQueue\Contact;

use CommerceLeague\ActiveCampaign\MessageQueue\EntityIdAwareInterface;
use CommerceLeague\ActiveCampaign\MessageQueue\EntityIdAwareTrait;
use CommerceLeague\ActiveCampaign\MessageQueue\SerializedRequestAwareInterface;
use CommerceLeague\ActiveCampaign\MessageQueue\SerializedRequestAwareTrait;

/**
 * Class CreateUpdateConsumerMessage
 */
class CreateUpdateMessage implements
    EntityIdAwareInterface,
    SerializedRequestAwareInterface
{
    use SerializedRequestAwareTrait;
    use EntityIdAwareTrait;
}
