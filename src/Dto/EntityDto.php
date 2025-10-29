<?php

namespace EasyCorp\Bundle\EasyAdminBundle\Dto;

use Doctrine\ORM\Mapping\AssociationMapping;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\FieldMapping;
use EasyCorp\Bundle\EasyAdminBundle\Collection\ActionCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\PropertyAccess\Exception\UninitializedPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @author Javier Eguiluz <javier.eguiluz@gmail.com>
 *
 * @template TEntity of object = object
 */
final class EntityDto
{
    private bool $isAccessible = true;
    private mixed $primaryKeyValue = null;
    private ?FieldCollection $fields = null;
    private ?ActionCollection $actions = null;

    /**
     * @param class-string<TEntity>  $fqcn
     * @param ClassMetadata<TEntity> $metadata
     * @param TEntity|null           $entityInstance
     */
    public function __construct(
        private readonly string $fqcn,
        private readonly ClassMetadata $metadata,
        private readonly string|Expression|null $permission = null,
        private ?object $entityInstance = null,
    ) {
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @return class-string<TEntity>
     */
    public function getFqcn(): string
    {
        return $this->fqcn;
    }

    public function getName(): string
    {
        return basename(str_replace('\\', '/', $this->fqcn));
    }

    public function toString(): string
    {
        if (null === $this->entityInstance) {
            return '';
        }

        if (method_exists($this->entityInstance, '__toString')) {
            return (string) $this->entityInstance;
        }

        return sprintf('%s #%s', $this->getName(), substr($this->getPrimaryKeyValueAsString(), 0, 16));
    }

    /**
     * @phpstan-return TEntity|null
     */
    public function getInstance(): ?object
    {
        return $this->entityInstance;
    }

    public function getPrimaryKeyValue(): mixed
    {
        if (null === $this->entityInstance) {
            return null;
        }

        if (null !== $this->primaryKeyValue) {
            return $this->primaryKeyValue;
        }

        $propertyAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->enableExceptionOnInvalidIndex()
            ->getPropertyAccessor();

        try {
            $primaryKeyValue = $propertyAccessor->getValue($this->instance, $this->metadata->getSingleIdentifierFieldName());
        } catch (UninitializedPropertyException $exception) {
            $primaryKeyValue = null;
        }

        return $this->primaryKeyValue = $primaryKeyValue;
    }

    public function getPrimaryKeyValueAsString(): string
    {
        return (string) $this->getPrimaryKeyValue();
    }

    public function getPermission(): string|Expression|null
    {
        return $this->permission;
    }

    public function isAccessible(): bool
    {
        return $this->isAccessible;
    }

    public function markAsInaccessible(): void
    {
        $this->isAccessible = false;
        $this->entityInstance = null;
        $this->fields = null;
    }

    public function getFields(): ?FieldCollection
    {
        return $this->fields;
    }

    public function setFields(FieldCollection $fields): void
    {
        $this->fields = $fields;
    }

    public function setActions(ActionCollection $actions): void
    {
        $this->actions = $actions;
    }

    public function getActions(): ActionCollection
    {
        return $this->actions;
    }

    public function getClassMetadata(): ClassMetadata
    {
        return $this->metadata;
    }

    public function getPropertyMetadata(string $propertyName): KeyValueStore
    {
        if (isset($this->metadata->fieldMappings[$propertyName])) {
            /** @var FieldMapping|array $fieldMapping */
            /** @phpstan-ignore-next-line */
            $fieldMapping = $this->metadata->fieldMappings[$propertyName];

            // Doctrine ORM 2.x returns an array and Doctrine ORM 3.x returns a FieldMapping object
            if ($fieldMapping instanceof FieldMapping) {
                $fieldMapping = (array) $fieldMapping;
            }

            return KeyValueStore::new($fieldMapping);
        }

        if ($this->metadata->hasAssociation($propertyName)) {
            /** @var AssociationMapping|array $associationMapping */
            /** @phpstan-ignore-next-line */
            $associationMapping = $this->metadata->associationMappings[$propertyName];

            // Doctrine ORM 2.x returns an array and Doctrine ORM 3.x returns an AssociationMapping object
            if ($associationMapping instanceof AssociationMapping) {
                // Doctrine ORM 3.x doesn't include the 'type' key that tells the type of association
                // recreate that key to keep the code compatible with both versions
                $associationType = $associationMapping->type();

                $associationMapping = (array) $associationMapping;
                $associationMapping['type'] = $associationType;
            }

            return KeyValueStore::new($associationMapping);
        }

        throw new \InvalidArgumentException(sprintf('The "%s" field does not exist in the "%s" entity.', $propertyName, $this->getFqcn()));
    }

    public function hasProperty(string $propertyName): bool
    {
        return isset($this->metadata->fieldMappings[$propertyName])
            || $this->metadata->hasAssociation($propertyName);
    }

    public function isAssociation(string $propertyName): bool
    {
        if ($this->metadata->hasAssociation($propertyName)) {
            return true;
        }

        if (!str_contains($propertyName, '.')) {
            return false;
        }

        $propertyNameParts = explode('.', $propertyName, 2);

        return !isset($this->metadata->embeddedClasses[$propertyNameParts[0]]);
    }

    /**
     * @param TEntity|null $newEntityInstance
     */
    public function setInstance(?object $newEntityInstance): void
    {
        if (null !== $this->entityInstance && null !== $newEntityInstance && !$newEntityInstance instanceof $this->fqcn) {
            throw new \InvalidArgumentException(sprintf('The new entity instance must be of the same type as the previous instance (original instance: "%s", new instance: "%s").', $this->fqcn, $newEntityInstance::class));
        }

        $this->entityInstance = $newEntityInstance;
        $this->primaryKeyValue = null;
    }

    /**
     * @param TEntity $newEntityInstance
     */
    public function newWithInstance(object $newEntityInstance): self
    {
        if (null !== $this->entityInstance && !$newEntityInstance instanceof $this->fqcn) {
            throw new \InvalidArgumentException(sprintf('The new entity instance must be of the same type as the previous instance (original instance: "%s", new instance: "%s").', $this->fqcn, $newEntityInstance::class));
        }

        return new self($this->fqcn, $this->metadata, $this->permission, $newEntityInstance);
    }
}
