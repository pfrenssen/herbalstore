<?php

declare(strict_types = 1);

namespace Drupal\herbalstore\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\link\Plugin\Field\FieldFormatter\LinkFormatter;

/**
 * Plugin implementation of the 'link' formatter.
 *
 * @FieldFormatter(
 *   id = "herbalstore_custom_text_link",
 *   label = @Translation("Custom text link"),
 *   field_types = {
 *     "link"
 *   }
 * )
 */
class CustomTextLinkFormatter extends LinkFormatter {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    foreach ($items as &$item) {
      $item->title = $this->t('Order online');
      $item->_attributes = ['class' => ['btn', 'btn-primary']];
    }

    return parent::viewElements($items, $langcode);
  }

}
