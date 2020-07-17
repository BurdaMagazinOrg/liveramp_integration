<?php

namespace Drupal\liveramp_integration\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Config\ConfigFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Class LiveRampIntegrationBlock.
 *
 * @Block(
 *   id = "liveramp_integration_block",
 *   admin_label = @Translation("LiveRamp GDPR consent block"),
 * )
 */
class LiveRampIntegrationBlock extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * Configuration.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $config;

  /**
   * LiveRampIntegrationBlock constructor.
   *
   * @param array $configuration
   *   Configuration.
   * @param string $plugin_id
   *   Plugin id.
   * @param string $plugin_definition
   *   Plugin definition.
   * @param \Drupal\Core\Config\ConfigFactory $configFactory
   *   Config factory.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ConfigFactory $configFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->config = $configFactory;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('config.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $config = $this->config->get('liveramp_integration.configuration');

    $build = [
      '#app_id'     => $config->get('app_id'),
      '#vendor_ids' => $config->get('vendor_ids'),
      '#theme'      => 'liveramp_integration_check_consent_block',
    ];

    return $build;
  }

}
