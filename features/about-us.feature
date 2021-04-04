Feature: About us
  To give the visitor some information about the people working in the herbal store
  As the shop owner
  I want to have an about us page

  @smoke
  Scenario: Visit the about us page
    Given I am not logged in
    When I am on the homepage
    And I click "Over ons"
    Then I should see 1 hero image
    And I should see the profile picture
    And I should see the heading "Rob Motmans"
    And I should see 4 gallery images
