# This file contains a user story for demonstration only.
# Learn how to get started with Behat and BDD on Behat's website:
# http://behat.org/en/latest/quick_start.html

Feature:
    In order to prove that SoftDeleteAwareEntityType is not not crashing on soft-deleted relations
    As a form user
    I run scenarios which are to pretend form usage

    Scenario: Form should display EntityWithDeletableRelation values even if related DeletableEntity is soft-deleted, but with placeholder in select box.
        Given That we have a DeletableEntity "Parent"
        And That we have a DeletableEntity "Other parent"
        And That we have a EntityWithDeletableRelation called "Child" related to DeletableEntity "Parent"
        And DeletableEntity "Parent" is soft-deleted
        When I enter '/form/test/1'
        Then Select box in the response should display placeholder
        And Offer only "Other parent"

    Scenario: Form should display EntityWithDeletableRelation with the DeletableEntity selected when it is not soft-deleted.
        Given That we have a DeletableEntity "NotDeletedParent"
        And That we have a DeletableEntity "Other parent"
        And That we have a EntityWithDeletableRelation called "Child" related to DeletableEntity "NotDeletedParent"
        When I enter '/form/test/2'
        Then Select box in the response should display DeletableEntity "NotDeletedParent"
        And Offer both "NotDeletedParent" and "Other parent"
