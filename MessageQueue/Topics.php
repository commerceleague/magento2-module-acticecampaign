<?php
declare(strict_types=1);
/**
 */

namespace CommerceLeague\ActiveCampaign\MessageQueue;

/**
 * Class Topics
 */
class Topics
{
    public const CUSTOMER_CONTACT_EXPORT = 'activecampaign.customer.export.contact';
    public const CUSTOMER_CUSTOMER_EXPORT = 'activecampaign.customer.export.customer';
    public const NEWSLETTER_CONTACT_EXPORT = 'activecampaign.newsletter.export.contact';
    public const SALES_ORDER_EXPORT = 'activecampaign.sales.export.order';
}
