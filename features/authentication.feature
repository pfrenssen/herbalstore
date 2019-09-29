Feature: User authentication
  In order to protect the integrity of the website
  As a product owner
  I want to make sure users with various roles can only access pages they are authorized to

  Scenario: Anonymous user can see the user login page
    Given I am not logged in
    When I visit "user"
    Then I should see the text "Inloggen"
    And I should see the text "Uw wachtwoord opnieuw instellen"
    And I should see the text "Gebruikersnaam"
    And I should see the text "Wachtwoord"
    But I should not see the text "Nieuw account aanmaken"
    And I should not see the text "Uitloggen"
    And I should not see the text "Profiel bekijken"

  Scenario: Unneeded default pages in Drupal are disabled
    Given I am not logged in
    When I go to "node"
    And the response status code should be 404

  Scenario Outline: Anonymous user can access public pages
    Given I am not logged in
    Then I visit "<path>"

    Examples:
      | path                |
      | /                   |
      | user/login          |
      | user/password       |

  Scenario Outline: Anonymous user cannot access restricted pages
    Given I am not logged in
    When I go to "<path>"
    Then I should see the heading "Geen toegang"
    And I should see the text "U heeft geen toegangsrechten voor deze pagina."
    And the response status code should be 403

    Examples:
      | path                         |
      | admin                        |
      | admin/config                 |
      | admin/content                |
      | admin/people                 |
      | admin/structure              |
      | admin/structure/views        |
      | node/add                     |
      | node/add/page                |
      | user/register                |

  @api
  Scenario Outline: Authenticated user can access pages they are authorized to
    Given I am logged in as a user with the "authenticated" role
    Then I visit "<path>"

    Examples:
      | path                       |
      | user                       |

  @api
  Scenario Outline: Authenticated user cannot access site administration
    Given I am logged in as a user with the "authenticated" role
    When I go to "<path>"
    Then the response status code should be 403

    Examples:
      | path                                                           |
      | admin                                                          |
      | admin/config                                                   |
      | admin/content                                                  |
      | admin/people                                                   |
      | admin/structure                                                |
      | admin/structure/views                                          |
      | node/add                                                       |


#  @api
#  Scenario Outline: Administrator can access pages they are authorized to
#    Given I am logged in as a user with the "administrator" role
#    Then I visit "<path>"
#
#    Examples:
#      | path                         |
#      | admin/config/search/redirect |
#      | collections                  |
#
#  @api
#  Scenario Outline: Administrator cannot access pages intended for site building and development
#    Given I am logged in as a user with the "administrator" role
#    When I go to "<path>"
#    Then I should get an access denied error
#
#    Examples:
#      | path                               |
#      | admin                              |
#      | admin/config                       |
#      | admin/content                      |
#      | admin/content/rdf                  |
#      | admin/people                       |
#      | admin/structure                    |
#      | node                               |
#      | node/add                           |
#      | node/add/custom_page               |
#      | node/add/discussion                |
#      | node/add/document                  |
#      | node/add/event                     |
#      | node/add/news                      |
#      | rdf_entity/add                     |
#      | rdf_entity/add/asset_distribution  |
#      | rdf_entity/add/asset_release       |
#      | rdf_entity/add/collection          |
#      | rdf_entity/add/contact_information |
#      | rdf_entity/add/licence             |
#      | rdf_entity/add/owner               |
#      | rdf_entity/add/solution            |
