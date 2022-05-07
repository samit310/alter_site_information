<?php

namespace Drupal\alter_site_information\Controller;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Session\AccountInterface;
use Drupal\node\NodeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class ApiPageJson.
 *
 * Returns nodes to JSON Format.
 */
class ApiPageJson extends ControllerBase {

  /**
   * The event dispatcher service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Constructs a new ApiPageJson instance.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->configFactory = $config_factory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory')
    );
  }

  /**
   * Checks access for a specific request.
   *
   * @param string $siteapikey
   *   The Site API Key.
   * @param \Drupal\node\NodeInterface $node
   *   The node belongs to Page content type.
   * @param \Drupal\Core\Session\AccountInterface $account
   *   Run access checks for this account.
   *
   * @return \Drupal\Core\Access\AccessResult|\Drupal\Core\Access\AccessResultAllowed|\Drupal\Core\Access\AccessResultNeutral
   *   THe boolean value.
   */
  public function access($siteapikey, NodeInterface $node, AccountInterface $account) {
    $allow_access = FALSE;
    $site_config = $this->config('system.site');
    /*
     * The API key should be match.
     * The node object should be page type.
     */
    if ($site_config->get('siteapikey') == $siteapikey && in_array($node->bundle(), [
      'page',
      'article',
    ])) {
      $allow_access = TRUE;
    }
    return AccessResult::allowedIf($allow_access);
  }

  /**
   * The pageJson.
   *
   * @param string $siteapikey
   *   The Site API Key.
   * @param \Drupal\node\NodeInterface $node
   *   The node belongs to Page content type.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *   Node in Json format.
   */
  public function pageJson($siteapikey, NodeInterface $node): JsonResponse {
    return new JsonResponse([
      'data' => [
        'id' => $node->id(),
        'title' => $node->label(),
        'body' => $node->body->value,
      ],
      'code' => 200,
      'message' => $this->t('success'),
    ]);
  }

}
