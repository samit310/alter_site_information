<?php

namespace Drupal\alter_site_information\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Path\PathValidatorInterface;
use Drupal\Core\Routing\RequestContext;
use Drupal\path_alias\AliasManagerInterface;
use Drupal\system\Form\SiteInformationForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class SiteApiSiteInformationForm.
 */
class SiteApiSiteInformationForm extends SiteInformationForm {

  /**
   * The messenger.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * Constructs a SiteInformationForm object.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The factory for configuration objects.
   * @param \Drupal\path_alias\AliasManagerInterface $alias_manager
   *   The path alias manager.
   * @param \Drupal\Core\Path\PathValidatorInterface $path_validator
   *   The path validator.
   * @param \Drupal\Core\Routing\RequestContext $request_context
   *   The request context.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
    AliasManagerInterface $alias_manager,
    PathValidatorInterface $path_validator,
    RequestContext $request_context,
    MessengerInterface $messenger) {
    parent::__construct($config_factory, $alias_manager, $path_validator, $request_context);
    $this->messenger = $messenger;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('path_alias.manager'),
      $container->get('path.validator'),
      $container->get('router.request_context'),
      $container->get('messenger')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $site_config = $this->config('system.site');

    $form['site_api_key'] = [
      '#type' => 'details',
      '#title' => t('Site API Key'),
      '#open' => TRUE,
    ];
    $form['site_api_key']['api_key'] = [
      '#type' => 'textfield',
      '#title' => t('API Key'),
      '#default_value' => $site_config->get('siteapikey') ?? $this->t('No API Key yet'),
      '#description' => $this->t('The API key used to provides the JSON representation of a given node with the content type "page"'),
      '#required' => TRUE,
    ];

    /*
     * Update Submit button text.
     * Inherit ['actions']['submit'] Defined int ConfigFormBase.php.
     */
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Update Configuration'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('api_key') == $this->t('No API Key yet')) {
      $form_state->setErrorByName('api_key', $this->t("No API Key yet"));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Save siteapikey configuration.
    $this->config('system.site')
      ->set('siteapikey', $form_state->getValue('api_key'))
      ->save();

    // Display success message to user.
    $this->messenger->addMessage($this->t("The Site API Key has been saved with %api_key", ['%api_key' => $form_state->getValue('api_key')]));

    parent::submitForm($form, $form_state);
  }

}
