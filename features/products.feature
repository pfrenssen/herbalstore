Feature: Products
  To show my available products
  As the shop owner
  I want to have a products page

  @smoke
  Scenario: Choose a product from the products page
    Given I am not logged in
    When I am on the homepage
    And I click "Aanbod"
    Then I should see the product category facets
    And I should see 24 product cards
    And I should see the pager
    And I should see the heading "Hoe bestellen"

    # Check that we can open the product category facets.
    And I should see the link "Voedingssupplementen"
    But I should not see the link "Vitaminen"
    When I click "Voedingssupplementen"
    Then I should see the link "Vitaminen"
    # Check that we can close the product category facets again.
    When I click "Voedingssupplementen"
    Then I should not see the link "Vitaminen"

    When I click "Details"
    Then I should see the product heading
    And I should see the breadcrumbs
    And I should see the link "Online bestellen"
    And I should see the product image
    And I should see the heading "Hoe bestellen"
