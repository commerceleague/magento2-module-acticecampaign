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
    public const GUEST_CUSTOMER_EXPORT = 'activecampaign.customer.export.guest.customer';
    public const NEWSLETTER_CONTACT_EXPORT = 'activecampaign.newsletter.export.contact';
    public const SALES_ORDER_EXPORT = 'activecampaign.sales.export.order';
    public const QUOTE_ABANDONED_CART_EXPORT = 'activecampaign.quote.export.abandoned_cart';
    public const ASSIGN_CONTACT_TO_LIST = 'activecampaign.customer.assign.contact.to.list';
    public const ASSIGN_NEWSLETTER_SUBSCRIBER_TO_LIST = 'activecampaign.newsletter.assign.subscriber.to.list';
    public const TAG_NEWSLETTER_SUBSCRIBER = 'activecampaign.newsletter.tag.subscriber';
}
