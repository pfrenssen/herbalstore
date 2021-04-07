Feature: Homepage
  In order to present an overview of my herbal store
  As the shop owner
  I want to show an introduction and some important information on the homepage

  @smoke
  Scenario: Viewing the homepage
    Given I am not logged in
    And I am on the homepage
    Then I should see the heading "Gezondheidswinkel"
    And I should see the heading "Jouw gezondheidswinkel in het hartje van Zonhoven"
    And I should see the heading "Enkele sfeerfoto's"
    And I should see 1 hero image
    And I should see 8 gallery images
