<?php

declare(strict_types=1);

namespace Drupal\herbalstore\Plugin\Field\FieldFormatter;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Url;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of the 'Taxonomy breadcrumb' field formatter.
 *
 * @FieldFormatter(
 *   id = "herbalstore_taxonomy_breadcrumb",
 *   label = @Translation("Taxonomy breadcrumb"),
 *   field_types = {
 *     "entity_reference",
 *   }
 * )
 */
class TaxonomyBreadcrumbFormatter extends FormatterBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new TaxonomyBreadcrumbFormatter.
   *
   * @param string $plugin_id
   *   The plugin_id for the formatter.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the formatter is associated.
   * @param array $settings
   *   The formatter settings.
   * @param string $label
   *   The formatter label display setting.
   * @param string $view_mode
   *   The view mode.
   * @param array $third_party_settings
   *   Third party settings.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, $label, $view_mode, array $third_party_settings, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $label, $view_mode, $third_party_settings);

    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['label'],
      $configuration['view_mode'],
      $configuration['third_party_settings'],
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $element = [
      '#theme' => 'item_list__taxonomy_breadcrumb',
      '#list_type' => 'ol',
      '#items' => [],
      '#attributes' => ['class' => 'breadcrumb'],
      '#wrapper_attributes' => ['class' => 'category-subcategory'],
    ];

    /** @var \Drupal\Core\Field\Plugin\Field\FieldType\EntityReferenceItem $item */
    $item = $items->first();
    /** @var \Drupal\taxonomy\TermInterface $term */
    $term = $item->entity;
    if ($term instanceof TermInterface) {
      /** @var \Drupal\taxonomy\TermStorageInterface $term_storage */
      $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
      $parents = $term_storage->loadAllParents($term->id());
      foreach ($parents as $parent) {
        $url = Url::fromRoute('view.products.page_1', [], [
          'query' => [
            'f' => ['categorie:' . $parent->id()],
          ],
        ]);
        $link = [
          '#wrapper_attributes' => ['class' => ['breadcrumb-item']],
          '#title' => $parent->label(),
          '#type' => 'link',
          '#url' => $url,
        ];
        array_unshift($element['#items'], $link);
      }
    }

    // Prepend the products and home links.
    $crumbs = ['Products' => 'view.products.page_1', 'Home' => '<front>'];
    foreach ($crumbs as $title => $route) {
      $link = [
        '#wrapper_attributes' => ['class' => ['breadcrumb-item']],
        // @phpcs:ignore
        '#title' => $this->t($title),
        '#type' => 'link',
        '#url' => Url::fromRoute($route),
      ];
      array_unshift($element['#items'], $link);
    }

    return $element;
  }

}
