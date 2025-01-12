Feature: Header and footer
  To ensure important information and navigation is visible on every page
  As the shop owner
  I want to have a header and footer

  @smoke
  Scenario Outline: Follow the header links
    Given I am not logged in
    When I am on the homepage
    Then I should see the link "<link>" in the header
    When I click "<link>"
    Then the url should match "<path>"

    # Inspect the header.
    And I should see the link "Zonnedauw" in the header
    And I should see the link "Home" in the header
    And I should see the link "Aanbod" in the header
    And I should see the link "Over ons" in the header
    And I should see the link "Contact" in the header

    # Inspect the footer.
    And I should see the text "Heuvenstraat 66" in the footer
    And I should see the text "3520 Zonhoven" in the footer
    And I should see the text "+32 11 81 37 67" in the footer
    And I should see the link "Email ons" in the footer

    Examples:
      | link      | path         |
      | Zonnedauw | /nl          |
      | Home      | /nl          |
      | Aanbod    | /nl/aanbod   |
      | Over ons  | /nl/over-ons |
      | Contact   | /nl/contact  |
