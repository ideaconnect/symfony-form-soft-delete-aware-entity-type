<?php

declare(strict_types=1);

namespace App\Tests\Behat;

use App\Entity\DeletableEntity;
use App\Entity\EntityWithDeletableRelation;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Doctrine\ORM\EntityManagerInterface;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\WebDriverBy;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Panther\Client;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class FormContext implements Context
{
    private Client $client;
    /** @var KernelInterface */
    private $kernel;

    private $reference;

    /** @var Response|null */
    private $response;

    public function __construct(KernelInterface $kernel, protected EntityManagerInterface $em)
    {
        $this->kernel = $kernel;
        $this->reference = [];
    }

    /**
     * @Given DeletableEntity :named is soft-deleted
     */
    public function deletableentityIsSoftDeleted($named)
    {
        $this->em->remove($this->reference[$named]);
        $this->em->flush();
    }

    /**
     * @When I enter :arg1
     */
    public function iEnter($arg1)
    {
        $this->client = Client::createChromeClient();
        $this->client->request('GET', 'http://localhost:8000'.$arg1);
    }

    /**
     * @When I enter :arg1 with referenced entity :name
     */
    public function iEnterWithId($arg1, $name)
    {
        $this->client = Client::createChromeClient();
        $this->client->request('GET', 'http://localhost:8000'.$arg1.$this->reference[$name]->getId());
    }

    /**
     * @Then Offer both :arg1 and :arg2
     */
    public function offerBothAnd($arg1, $arg2)
    {
        $crawler = $this->client->waitFor('#entity_with_deletable_relation_relatedDeletableEntity');
        $selectOptions = $crawler->filter('#entity_with_deletable_relation_relatedDeletableEntity option');

        $texts = [];
        /* @var RemoteWebElement */
        foreach ($selectOptions as $option) {
            $texts[$option->getAttribute('value')] = $option->getText();
        }

        $ref1 = $this->reference[$arg1];
        $ref2 = $this->reference[$arg2];

        if (!isset($texts[$ref1->getId()]) || !isset($texts[$ref2->getId()])) {
            throw new \Exception('Required id not present.');
        }

        if (!\in_array($texts[$ref1->getId()], $texts, true) || !\in_array($texts[$ref1->getId()], $texts, true)) {
            throw new \Exception('Required text not present.');
        }
    }

    /**
     * @Then Offer only :named
     */
    public function offerOnly($named)
    {
        $crawler = $this->client->waitFor('#entity_with_deletable_relation_relatedDeletableEntity');
        $selectOptions = $crawler->filter('#entity_with_deletable_relation_relatedDeletableEntity option');

        /* @var RemoteWebElement */
        foreach ($selectOptions as $option) {
            if ($option->getText() !== $named && 'placeholder' !== $option->getText()) {
                throw new \Exception('Invalid option in select box.');
            }
        }
    }

    /**
     * @AfterScenario
     */
    public function quit(AfterScenarioScope $scope)
    {
        $this->client->close();
        $this->client->quit();
    }

    /**
     * @Then Select box in the response should display DeletableEntity :name
     */
    public function selectBoxInTheResponseShouldDisplayDeletableentity($name)
    {
        $crawler = $this->client->waitFor('#entity_with_deletable_relation_relatedDeletableEntity');
        $id = $this->reference[$name]->getId();

        $test = $crawler->findElement(WebDriverBy::id('entity_with_deletable_relation_relatedDeletableEntity'))
            ->findElement(WebDriverBy::cssSelector('option:checked'))
        ;

        if (\strval($id) !== $test->getAttribute('value')) {
            throw new \Exception('Invalid option in select box.');
        }
    }

    /**
     * @Then Select box in the response should display placeholder
     */
    public function selectBoxInTheResponseShouldDisplayPlaceholder()
    {
        $crawler = $this->client->waitFor('#entity_with_deletable_relation_relatedDeletableEntity');
        $select = $crawler->filter('#entity_with_deletable_relation_relatedDeletableEntity');
        /* @var RemoteWebElement */
        if ('' !== $select->getAttribute('value')) {
            throw new \Exception('Invalid value of placeholder or something else selected.');
        }
    }

    /**
     * @Given That we have a DeletableEntity :named
     */
    public function thatWeHaveADeletableentity($named)
    {
        $deletable = new DeletableEntity();
        $deletable->setName($named);
        $this->em->persist($deletable);
        $this->em->flush();
        $this->reference[$named] = $deletable;
    }

    /**
     * @Given That we have a EntityWithDeletableRelation called :named related to DeletableEntity :related
     */
    public function thatWeHaveAEntitywithdeletablerelationCalled($named, $related)
    {
        $child = new EntityWithDeletableRelation();
        $child->setName($named);
        $child->setRelatedDeletableEntity($this->reference[$related]);

        $this->em->persist($child);
        $this->em->flush();
    }
}
