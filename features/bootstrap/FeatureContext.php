<?php

/**
 * @file
 * Contains custom step definitions for the Herbal Store project.
 */

declare(strict_types = 1);

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines generic step definitions.
 */
class FeatureContext extends RawDrupalContext {

  /**
   * Checks that a 403 Access Denied error occurred.
   *
   * @Then I should get an access denied error
   */
  public function assertAccessDenied(): void {
    $this->assertSession()->statusCodeEquals(403);
  }

  /**
   * Counts the hero images.
   *
   * @Then I should see :count hero image(s)
   */
  public function assertHeroImageCount(int $count): void {
    $page = $this->getSession()->getPage();
    $elements = $page->findAll('css', '.hero-image img');
    $elements_count = count($elements);
    if (count($elements) != $count) {
      throw new \Exception("Expected $count hero images but found $elements_count");
    }
  }

  /**
   * Counts the gallery images.
   *
   * @Then I should see :count gallery image(s)
   */
  public function assertGalleryImageCount(int $count): void {
    $page = $this->getSession()->getPage();
    $elements = $page->findAll('css', '.photoswipe-gallery a.photoswipe img');
    $elements_count = count($elements);
    if (count($elements) != $count) {
      throw new \Exception("Expected $count gallery images but found $elements_count");
    }
  }

  /**
   * Checks if the product category facets are visible on the page.
   *
   * @Then I should see the product category facets
   */
  public function assertProductCategoryFacetsPresent(): void {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', '.block-facet--links .facet-item a');
    if (empty($element)) {
      throw new \Exception("Could not find the product category facet block on the page");
    }
  }

  /**
   * Counts the product cards.
   *
   * @Then I should see :count product card(s)
   */
  public function assertProductCardCount(int $count): void {
    $page = $this->getSession()->getPage();
    $elements = $page->findAll('css', '.product .card');
    $elements_count = count($elements);
    if ($elements_count != $count) {
      throw new \Exception("Expected $count product cards but found $elements_count");
    }
  }

  /**
   * Checks if the pager is visible on the page.
   *
   * @Then I should see the pager
   */
  public function assertPagerPresent(): void {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', 'ul.pagination');
    if (empty($element)) {
      throw new \Exception("Could not find the pager on the page");
    }
  }

  /**
   * Checks if the product heading is visible on the page.
   *
   * @Then I should see the product heading
   */
  public function assertProductHeadingPresent(): void {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', '.product .product-content h2');
    if (empty($element)) {
      throw new \Exception("Could not find the product heading on the page");
    }
  }

  /**
   * Checks if the breadcrumbs are visible on the page.
   *
   * @Then I should see the breadcrumbs
   */
  public function assertBreadcrumbsPresent(): void {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', 'ol.breadcrumb');
    if (empty($element)) {
      throw new \Exception("Could not find the breadcrumbs on the page");
    }
  }

  /**
   * Checks if the product image is visible on the page.
   *
   * @Then I should see the product image
   */
  public function assertProductImagePresent(): void {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', '.product-image img');
    if (empty($element)) {
      throw new \Exception("Could not find the product image on the page");
    }
  }

  /**
   * Checks if the profile picture is visible on the page.
   *
   * @Then I should see the profile picture
   */
  public function assertProfilePicturePresent(): void {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', '.profile img');
    if (empty($element)) {
      throw new \Exception("Could not find the profile picture on the page");
    }
  }

  /**
   * Checks if the map is visible on the page.
   *
   * @Then I should see a map
   */
  public function assertMapPresent(): void {
    $page = $this->getSession()->getPage();
    $element = $page->find('css', '.contact-map iframe');
    if (empty($element)) {
      throw new \Exception("Could not find a map on the page");
    }
  }

}
