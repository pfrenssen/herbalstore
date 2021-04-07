Feature: Contact
  In order to inform clients about the location of the shop
  As the shop owner
  I want to have a contact page

  @smoke
  Scenario: Visit the contact page
    Given I am not logged in
    When I am on the homepage
    And I click "Contact"
    And I should see the heading "Adres"
    And I should see the heading "Telefoonnummer"
    And I should see the heading "Email"
    And I should see the heading "Openingsuren"
    And I should see the link "gezondheidzonnedauw@gmail.com"
    And I should see a map
    And I should see 1 hero image
