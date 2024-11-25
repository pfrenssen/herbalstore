Feature: User authentication
  In order to protect the integrity of the website
  As a product owner
  I want to make sure users with various roles can only access pages they are authorized to

  @smoke
  Scenario: Anonymous user can see the user login page
    Given I am not logged in
    When I visit "user"
    Then I should see the text "Inloggen"
    And I should see the text "Uw wachtwoord opnieuw instellen"
    And I should see the text "E-mailadres"
    And I should see the text "Wachtwoord"
    But I should not see the text "Nieuw account aanmaken"
    And I should not see the text "Uitloggen"
    And I should not see the text "Profiel bekijken"

  @smoke
  Scenario: Unneeded default pages in Drupal are disabled
    Given I am not logged in
    When I go to "node"
    And the response status code should be 404

  @smoke
  Scenario Outline: Anonymous user can access public pages
    Given I am not logged in
    Then I visit "<path>"

    Examples:
      | path                                                 |
      | /                                                    |
      | /nl/aanbod                                           |
      | /nl/aanbod?f[0]=categorie%3A10                       |
      | /nl/aanbod/voeding/superfoods/biotona-acai-superfood |
      | /nl/contact                                          |
      | /nl/over-ons                                         |
      | /nl/over-ons                                         |
      | user/login                                           |
      | user/password                                        |

  @smoke
  Scenario Outline: Anonymous user cannot access restricted pages
    Given I am not logged in
    When I go to "<path>"
    Then I should see the heading "Geen toegang"
    And I should see the text "U heeft geen toegangsrechten voor deze pagina."
    And I should get an access denied error

    Examples:
      | path                  |
      | admin                 |
      | admin/config          |
      | admin/content         |
      | admin/people          |
      | admin/structure       |
      | admin/structure/views |
      | node/add              |
      | node/add/page         |
      | node/add/product      |
      | user/register         |

  @api
  Scenario Outline: Authenticated user can access pages they are authorized to
    Given I am logged in as a user with the "authenticated" role
    Then I visit "<path>"

    Examples:
      | path |
      | user |

  @api
  Scenario Outline: Authenticated user cannot access site administration
    Given I am logged in as a user with the "authenticated" role
    When I go to "<path>"
    Then I should get an access denied error

    Examples:
      | path                  |
      | admin                 |
      | admin/config          |
      | admin/content         |
      | admin/people          |
      | admin/structure       |
      | admin/structure/views |
      | node/add              |


  @api
  Scenario Outline: Administrator can access pages they are authorized to
    Given I am logged in as a user with the "site_administrator" role
    Then I visit "<path>"

    Examples:
      | path                                                        |
      | admin/content                                               |
      | admin/people                                                |
      | admin/structure                                             |
      | admin/structure/menu                                        |
      | admin/structure/taxonomy                                    |
      | admin/structure/taxonomy/manage/product_categories/overview |
      | node/add/product                                            |

  @api
  Scenario Outline: Administrator cannot access pages intended for site building and development
    Given I am logged in as a user with the "site_administrator" role
    When I go to "<path>"
    Then I should get an access denied error

    Examples:
      | path                                 |
      | admin/appearance                     |
      | admin/config                         |
      | admin/config/content/formats         |
      | admin/config/development/logging     |
      | admin/config/development/performance |
      | admin/config/people/accounts         |
      | admin/config/regional/settings       |
      | admin/config/search/path             |
      | admin/modules                        |
      | admin/people/permissions             |
      | admin/people/roles                   |
      | admin/reports                        |
      | admin/structure/block                |
      | admin/structure/display-modes        |
      | admin/structure/ds                   |
      | admin/structure/page_manager         |
      | admin/structure/types                |
      | admin/structure/views                |
