<?php

declare(strict_types=1);

namespace IDCT\SymfonyFormSoftDeleteAwareEntityType;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Gedmo\Mapping\Annotation\SoftDeleteable as AnnotationSoftDeleteable;
use Gedmo\SoftDeleteable\SoftDeleteable;
use Symfony\Bridge\Doctrine\Form\ChoiceList\IdReader as ChoiceListIdReader;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * Extends default ID reader which is repsponsible for calling of the `getId` method
 * which may trigger an exception when soft deletion is turned on.
 *
 * Suspends the softdeleteable filter, loads the data, but for an actually removed
 * entity it does not return it.
 */
class SoftDeleteAwareIdReader extends ChoiceListIdReader
{
    private const FILTER_NAME = 'softdeleteable';

    /**
     * @todo Sadly had to overload the constructor here due to lack of access to
     * ObjectManager, using decoration instead of inheritance would not help me out
     * either...
     */
    public function __construct(
        private readonly ObjectManager $om,
        private readonly ClassMetadata $classMetadata,
    ) {
        parent::__construct($om, $classMetadata);
    }

    /**
     * Returns the ID value for an object.
     *
     * This method assumes that the object has a single-column ID.
     */
    public function getIdValue(?object $object = null): string
    {
        /* If object is empty/null no point in checking anything further, yes:
        this is duplicated within parent getIdValue, but there is no point in
        even checking if object is an instance of SoftDeletable and we need to
        do this before calling of the method from the parent class. */

        if (!$object) {
            return '';
        }

        /* Our piece of code: here we execute only for entities which implement
        the SoftDeletable interface from Doctrine Extensions. */
        if ($object instanceof SoftDeleteable) {
            // TODO get the entity builder in a nicer way...
            /* Here we need to get a handle to the entity manager which handles
            the requested entity, sadly a bit the round way at this point ... */
            $repository = $this->om->getRepository(\get_class($object));
            $em = $repository->createQueryBuilder('a')->getEntityManager();

            // Let's check if our filter is even enabled first
            $filters = $em->getFilters();
            if ($filters->isEnabled(static::FILTER_NAME)) {
                // if so: suspend it for a moment
                $filters->suspend(static::FILTER_NAME);

                // since softdeleteable is suspeneded this will not fail
                $this->om->initializeObject($object);

                // we can turn the filter back on
                $filters->restore(static::FILTER_NAME);

                // looking for the SoftDeleteable annotation/attribute...
                $realClass = ClassUtils::getRealClass(\get_class($object));
                $attrs = (new \ReflectionClass($realClass))->getAttributes(AnnotationSoftDeleteable::class);
                foreach ($attrs as $attr) {
                    /* and checking which fields defined if object is actually
                    deleted, usually something like `deletedAt`. */

                    $args = $attr->getArguments();
                    $field = $args['fieldName'];

                    /* if that field has a value it means that object is actually
                    already (soft) deleted */
                    $propertyAccessor = PropertyAccess::createPropertyAccessor();
                    $value = $propertyAccessor->getValue($object, $field);
                    if (null !== $value) {
                        return '';
                    }
                }
            }
        }

        // standard path of operations from Symfony
        return parent::getIdValue($object);
    }
}
