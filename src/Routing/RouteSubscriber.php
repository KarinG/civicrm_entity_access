<?php

namespace Drupal\civicrm_entity_access\Routing;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * Names of enabled CiviCRM entities.
   *
   * @var array
   */
  protected $civicrm_entities_enabled;

  /**
   * RouteSubscriber constructor.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $config = $config_factory->get('civicrm_entity.settings');
    $this->civicrm_entities_enabled = $config->get('enabled_entity_types') ?: [];
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    foreach ($this->civicrm_entities_enabled as $entity_name) {

      // Deny access to the usual Drupal entity CRUD routes for CiviCRM entities.
      foreach (['canonical', 'add_form', 'edit_form', 'delete_form'] as $crud_name) {
        $route_name = implode('.', ['entity', $entity_name, $crud_name]);
        if ($route = $collection->get($route_name)) {
          $route->setRequirement('_access', 'FALSE');
        }
      }
    }
  }

}
