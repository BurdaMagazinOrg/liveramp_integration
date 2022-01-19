<?php

namespace Drupal\liveramp_integration\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;

/**
 * Class LiveRampIntegrationConfigForm.
 *
 * @package Drupal\liveramp_integration\Form
 */
class LiveRampIntegrationConfigForm extends ConfigFormBase {

  /**
   * Configuration Id.
   *
   * @var string
   */
  static protected $configId = 'liveramp_integration.configuration';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'liveramp_integration_configuration';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [static::$configId];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config(self::$configId);

    $appId   = $config->get('app_id');
    $vendors = $config->get('vendor_ids');

    if (empty($form_state->get('num_vendors'))) {
      $num = !empty($vendors) ? count($vendors) : 1;
      $form_state->set('num_vendors', $num);
    }

    $num = $form_state->get('num_vendors');

    $form['consent'] = [
      '#tree' => TRUE,
    ];

    $form['consent']['field_app_id'] = [
      '#title'         => $this->t('Application ID'),
      '#type'          => 'textfield',
      '#required'      => TRUE,
      '#default_value' => $appId ?? '',
      '#description'   => $this->t('Save the form with valid application ID and list of available vendors will appear.'),
    ];

    $form['consent']['field_vendor_id'] = [
      '#title'      => $this->t('Vendor IDs'),
      '#type'       => 'fieldgroup',
      '#attributes' => ['id' => 'field_vendor_id-fieldset-wrapper'],
    ];

    for ($i = 0; $i < $num; $i++) {
      $form['consent']['field_vendor_id']['values'][$i] = [
        '#type'          => 'number',
        '#required'      => TRUE,
        '#default_value' => $vendors[$i] ?? 0,
      ];
    }

    // Action buttons.
    $form['consent']['field_vendor_id']['actions'] = [
      '#type' => 'actions',
    ];

    $form['consent']['field_vendor_id']['actions']['add'] = [
      '#type'                    => 'submit',
      '#value'                   => $this->t('Add more'),
      '#button_type'             => 'secondary',
      '#submit'                  => ['::addMore'],
      '#limit_validation_errors' => [],
      '#ajax'                    => [
        'callback' => [$this, 'fieldCallback'],
        'wrapper'  => 'field_vendor_id-fieldset-wrapper',
      ],
    ];

    $form['consent']['field_vendor_id']['actions']['remove'] = [
      '#type'                    => 'submit',
      '#value'                   => $this->t('Remove last'),
      '#button_type'             => 'secondary',
      '#submit'                  => ['::removeLast'],
      '#limit_validation_errors' => [],
      '#ajax'                    => [
        'callback' => [$this, 'fieldCallback'],
        'wrapper'  => 'field_vendor_id-fieldset-wrapper',
      ],
    ];

    // Show vendors list after user set application ID.
    if (!empty($appId)) {
      $form['consent']['script'] = [
        '#attached' => ['library' => ['liveramp_integration/liveramp_integration.vendors']],
        '#app_id'   => $appId,
      ];

      $form['consent']['vendors']['list'] = [
        '#title'  => $this->t('Vendors list'),
        '#type'   => 'details',
        '#prefix' => '<div id="liveramp_integration-vendors">',
        '#suffix' => '</div>',
      ];
    }

    $form['consent']['async_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use script async attribute.'),
      '#default_value' => $config->get('async_mode'),
    ];

    $form['consent']['defer_mode'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Use script defer attribute.'),
      '#default_value' => $config->get('defer_mode'),
    ];

    $form['consent']['actions']['submit'] = [
      '#type'        => 'submit',
      '#value'       => $this->t('Save configuration'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);
    $config = $this->config(static::$configId);

    $formData = $form_state->getValue('consent');

    $config->set('app_id', $formData['field_app_id']);
    $config->set('vendor_ids', $formData['field_vendor_id']['values']);
    $config->set('async_mode', $formData['async_mode']);
    $config->set('defer_mode', $formData['defer_mode']);

    $config->save();
  }

  /**
   * Callback.
   *
   * @param array $form
   *   Form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   *
   * @return mixed
   */
  public function fieldCallback(array &$form, FormStateInterface $form_state) {
    return $form['consent']['field_vendor_id'];
  }

  /**
   * Add more.
   *
   * @param array $form
   *   Form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   */
  public function addMore(array &$form, FormStateInterface $form_state) {
    $num = $form_state->get('num_vendors');
    $form_state->set('num_vendors', ++$num);
    $form_state->setRebuild();
  }

  /**
   * Remove last.
   *
   * @param array $form
   *   Form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   Form state.
   */
  public function removeLast(array &$form, FormStateInterface $form_state) {
    $num = $form_state->get('num_vendors');
    if ($num > 1) {
      $form_state->set('num_vendors', --$num);
      $form_state->setRebuild();
    }
  }

}
