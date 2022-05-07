<?php

namespace Drupal\alter_site_information\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class AlterSiteInformation.
 *
 * Event Subscriber to alter system.site_information_settings route.
 */
class AlterSiteInformation extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    if ($route = $collection->get('system.site_information_settings')) {
      $route->setDefault('_form', 'Drupal\alter_site_information\Form\SiteApiSiteInformationForm');
    }
  }

}
