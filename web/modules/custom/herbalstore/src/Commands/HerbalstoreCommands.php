<?php

declare(strict_types = 1);

namespace Drupal\herbalstore\Commands;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Entity\EntityStorageException;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileInterface;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Drupal\user\UserInterface;
use Drush\Commands\DrushCommands;

/**
 * Drush commands for the Herbalstore project.
 */
class HerbalstoreCommands extends DrushCommands {

  /**
   * The product parameters that are expected to be present in the JSON file.
   */
  protected const PRODUCT_PARAMETERS = [
    'title' => 'string',
    'image' => 'string',
    'category' => 'array',
    'price' => 'string',
    'description' => 'string',
  ];

  /**
   * The path where to save imported product images.
   */
  protected const PRODUCT_IMAGE_DESTINATION = 'public://imported-product-images';

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

  /**
   * Constructs a HerbalstoreCommands object.
   *
   * @param EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\File\FileSystemInterface $fileSystem
   *   The file system.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, FileSystemInterface $fileSystem) {
    parent::__construct();

    $this->entityTypeManager = $entityTypeManager;
    $this->fileSystem = $fileSystem;
  }

  /**
   * Imports products from a JSON file.
   *
   * @param string $file
   *   The path to the JSON file to import.
   *
   * @return int
   *   The exit code.
   *
   * @option $author The name of the user to set as the author of the imported
   *   products.
   *
   * @usage herbalstore:import-products ./products.json
   *   Imports the products from the given JSON file.
   *
   *   The file should consist of an array of objects with the following
   *   properties:
   *   - title: a string containing the product title.
   *   - image: a string containing the URL of the product image.
   *   - category: an array of strings containing the parent categories.
   *   - price: a string containing the product price.
   *   - description: the product description in HTML format.
   *
   * @command herbalstore:import-products
   */
  public function commandName(string $file, $options = ['author' => self::REQ]): int {

    $cwd = $this->getConfig()->getContext('environment')->get('env.cwd', getcwd());
    $path = realpath($cwd . DIRECTORY_SEPARATOR . $file);
    if (!$path || !is_file($path) || !is_readable($path)) {
      $this->logger()->error('File ' . $file . ' could not be read.');
      return self::EXIT_FAILURE;
    }

    $contents = file_get_contents($path);
    $products = json_decode($contents);
    if (json_last_error() !== JSON_ERROR_NONE) {
      $this->logger()->error('File ' . $file . ' does not contain valid JSON.');
      return self::EXIT_FAILURE;
    }

    if (!is_array($products)) {
      $this->logger()->error('File ' . $file . ' should be a JSON array containing objects with parameters ' . implode(', ', array_keys(self::PRODUCT_PARAMETERS)));
      return self::EXIT_FAILURE;
    }

    // Retrieve the node storage handler.
    try {
      /** @var \Drupal\node\NodeStorageInterface $node_storage */
      $node_storage = $this->entityTypeManager->getStorage('node');
    }
    catch (InvalidPluginDefinitionException $e) {
      $this->logger()->error('The entity type definition of the Node entity is invalid.');
      return self::EXIT_FAILURE;
    }
    catch (PluginNotFoundException $e) {
      $this->logger()->error('The Node module is not enabled.');
      return self::EXIT_FAILURE;
    }

    // Retrieve the taxonomy term storage handler.
    try {
      /** @var \Drupal\taxonomy\TermStorageInterface $term_storage */
      $term_storage = $this->entityTypeManager->getStorage('taxonomy_term');
    }
    catch (InvalidPluginDefinitionException $e) {
      $this->logger()->error('The entity type definition of the Taxonomy Term entity is invalid.');
      return self::EXIT_FAILURE;
    }
    catch (PluginNotFoundException $e) {
      $this->logger()->error('The Taxonomy module is not enabled.');
      return self::EXIT_FAILURE;
    }

    // Retrieve the author ID.
    $author_id = NULL;
    if (!empty($options['author']) && ($author = user_load_by_name($options['author'])) && $author instanceof UserInterface) {
      $author_id = $author->id();
    }

    // Ensure the folder for the imported images exists and is writeable.
    $destination_directory = self::PRODUCT_IMAGE_DESTINATION;
    if (!$this->fileSystem->prepareDirectory($destination_directory, FileSystemInterface::MODIFY_PERMISSIONS | FileSystemInterface::CREATE_DIRECTORY)) {
      $this->logger()->error(sprintf('The folder to import images does not exists or is not writeable: %s', $destination_directory));
      return self::EXIT_FAILURE;
    }

    foreach ($products as $product) {
      // Validate that the product object has the right data.
      if (
        !is_object($product)
        || !array_reduce(array_keys(self::PRODUCT_PARAMETERS), function (bool $carry, string $key) use ($product) {
          return $carry && (
            isset($product->$key)
            && gettype($product->$key) === self::PRODUCT_PARAMETERS[$key]
          );
        }, TRUE)
      ) {
        $this->logger()->error('File ' . $file . ' should be a JSON array containing objects with parameters ' . implode(', ', array_keys(self::PRODUCT_PARAMETERS)));
        return self::EXIT_FAILURE;
      }

      // Check if the product has already been imported.
      $title = $this->sanitizeString($product->title);
      $existing = $node_storage->loadByProperties(['title' => $title]);
      if (!empty($existing)) {
        $this->logger()->warning(sprintf('Product "%s" already exists. Skipping.', $title));
        continue;
      }

      // Find the right category, or create it if it doesn't exist.
      $category_tid = 0;
      foreach ($product->category as $category) {
        if (!is_string($category)) {
          $this->logger()->error(sprintf('Invalid category for product "%s". Aborting.', $title));
          return self::EXIT_FAILURE;
        }
        $category = $this->sanitizeString($category);

        $available_terms = array_filter($term_storage->loadTree('product_categories', $category_tid, 1, FALSE), function (object $term) use ($category): bool {
          return $term->name === $category;
        });

        if (count($available_terms) > 1) {
          $this->logger()->error(sprintf('Found duplicate category "%s". Aborting.', $category));
          return self::EXIT_FAILURE;
        }

        if (empty($available_terms)) {
          // Category doesn't exist yet. Create it.
          $values = [
            'name' => $category,
            'vid' => 'product_categories',
            'parent' => ['target_id' => $category_tid],
          ];
          $term = $term_storage->create($values);
          if (empty($term) || !$term instanceof TermInterface) {
            $this->logger()->error(sprintf('Error occurred creating category "%s". Aborting.', $category));
            return self::EXIT_FAILURE;
          }
          try {
            $term->save();
          } catch (EntityStorageException $e) {
            $this->logger()->error(sprintf('Error occurred saving category "%s". Aborting.', $category));
            return self::EXIT_FAILURE;
          }
          $category_tid = $term->id();
        }
        else {
          $term = reset($available_terms);
          $category_tid = $term->tid;
        }
      }

      // Import image.
      $image_url = $this->sanitizeString($product->image);
      $image = file_get_contents($image_url);
      if (!$image) {
        $this->logger()->error(sprintf('Could not download image for product "%s". Aborting.', $title));
        return self::EXIT_FAILURE;
      }
      $parts = parse_url($image_url);
      $filename = basename($parts['path']);
      $destination = $this->fileSystem->getDestinationFilename('public://imported-product-images/' . $filename, FileSystemInterface::EXISTS_RENAME);

      $file = file_save_data($image, $destination);
      if (!$file || !$file instanceof FileInterface) {
        $this->logger()->error(sprintf('Could not save image for product "%s". Aborting.', $title));
        return self::EXIT_FAILURE;
      }

      $values = [
        'title' => $title,
        'type' => 'product',
        'field_body' => [
          'value' => $description = check_markup($this->sanitizeString($product->description), 'basic_text'),
          'format' => 'basic_text',
        ],
        'field_price' => strtr($this->sanitizeString($product->price), ',', '.'),
        'field_category' => ['target_id' => $category_tid],
        'field_product_image' => ['target_id' => $file->id()],
      ];

      if ($author_id) {
        $values['uid'] = $author_id;
      }

      $entity = $node_storage->create($values);
      if (!$entity instanceof NodeInterface) {
        $this->logger()->error(sprintf('Product "%s" could not be created. Aborting.', $title));
        return self::EXIT_FAILURE;
      }
      try {
        $entity->save();
      }
      catch (EntityStorageException $e) {
        $this->logger()->error(sprintf('Product "%s" could not be saved. Aborting.', $title));
        return self::EXIT_FAILURE;
      }

      $this->logger()->notice(sprintf('Product "%s" imported successfully.', $title));
    }

    return self::EXIT_SUCCESS;
  }

  /**
   * Sanitizes the given string.
   *
   * @param string $string
   *   The string to sanitize.
   *
   * @return string
   *   The sanitized string.
   */
  protected function sanitizeString(string $string): string {
    return trim($string);
  }

}
