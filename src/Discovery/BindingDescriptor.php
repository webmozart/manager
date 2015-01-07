<?php

/*
 * This file is part of the puli/repository-manager package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\RepositoryManager\Discovery;

use InvalidArgumentException;
use Puli\Discovery\Api\Binding\NoSuchParameterException;
use Puli\RepositoryManager\Assert\Assertion;
use Puli\RepositoryManager\Discovery\Store\BindingTypeStore;
use Puli\RepositoryManager\Package\Package;
use Puli\RepositoryManager\Util\DistinguishedName;
use Rhumsaa\Uuid\Uuid;

/**
 * Describes a resource binding.
 *
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @see    ResourceBinding
 */
class BindingDescriptor
{
    /**
     * @var Uuid
     */
    private $uuid;

    /**
     * @var string
     */
    private $query;

    /**
     * @var string
     */
    private $language;

    /**
     * @var string
     */
    private $typeName;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var int
     */
    private $state = BindingState::UNLOADED;

    /**
     * Creates a new binding descriptor with a generated UUID.
     *
     * The UUID is generated based on the given parameters.
     *
     * @param string $query      The query for the resources of the binding.
     * @param string $typeName   The name of the binding type.
     * @param array  $parameters The values of the binding parameters.
     * @param string $language   The language of the query.
     *
     * @return static The created binding descriptor.
     *
     * @see ResourceBinding
     */
    public static function create($query, $typeName, array $parameters = array(), $language = 'glob')
    {
        Assertion::query($query);
        Assertion::typeName($typeName);
        Assertion::language($language);
        Assertion::allParameterName(array_keys($parameters));
        Assertion::allParameterValue($parameters);

        $dn = new DistinguishedName(array(
            'q' => $query,
            'l' => $language,
            't' => $typeName,
        ));

        foreach ($parameters as $parameter => $value) {
            // Attribute values must be strings
            $dn->add('p-'.$parameter, serialize($value));
        }

        $uuid = Uuid::uuid5(Uuid::NAMESPACE_X500, $dn->toString());

        return new static($uuid, $query, $typeName, $parameters, $language);
    }

    /**
     * Creates a new binding descriptor.
     *
     * @param Uuid   $uuid       The UUID of the binding.
     * @param string $query      The query for the resources of the binding.
     * @param string $typeName   The name of the binding type.
     * @param array  $parameters The values of the binding parameters.
     * @param string $language   The language of the query.
     *
     * @throws InvalidArgumentException If any of the arguments is invalid.
     *
     * @see ResourceBinding
     */
    public function __construct(Uuid $uuid, $query, $typeName, array $parameters = array(), $language = 'glob')
    {
        Assertion::query($query);
        Assertion::typeName($typeName);
        Assertion::language($language);
        Assertion::allParameterName(array_keys($parameters));
        Assertion::allParameterValue($parameters);

        $this->uuid = $uuid;
        $this->query = $query;
        $this->language = $language;
        $this->typeName = $typeName;
        $this->parameters = $parameters;
    }

    /**
     * Returns the UUID of the binding.
     *
     * @return Uuid The universally unique ID.
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Returns the query for the resources of the binding.
     *
     * @return string The resource query.
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Returns the language of the query.
     *
     * @return string The query language.
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Returns the name of the binding type.
     *
     * @return string The type name.
     */
    public function getTypeName()
    {
        return $this->typeName;
    }

    /**
     * Returns the values of the binding parameters.
     *
     * @return array The parameter values.
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Returns whether the descriptor has any parameter values set.
     *
     * @return bool Returns `true` if any parameter values are set.
     */
    public function hasParameters()
    {
        return count($this->parameters) > 0;
    }

    /**
     * Returns the value of a specific binding parameter.
     *
     * @param string $name The name of the binding parameter.
     *
     * @return mixed The parameter value.
     *
     * @throws NoSuchParameterException If the parameter does not exist.
     */
    public function getParameter($name)
    {
        if (!isset($this->parameters[$name])) {
            throw new NoSuchParameterException(sprintf(
                'The parameter "%s" does not exist.',
                $name
            ));
        }

        return $this->parameters[$name];
    }

    /**
     * Returns whether the descriptor contains a value for a binding parameter.
     *
     * @param string $name The name of the binding parameter.
     *
     * @return bool Returns `true` if a value is set for the parameter.
     */
    public function hasParameter($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Returns the state of the binding.
     *
     * @return int One of the {@link BindingState} constants.
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * Sets the state of the binding.
     *
     * @param int $state One of the {@link BindingState} constants.
     */
    public function setState($state)
    {
        Assertion::choice($state, BindingState::all(), 'The value "%s" is not a valid binding state.');

        $this->state = $state;
    }

    /**
     * Refreshes the state of the binding.
     *
     * @param Package          $package   The package that contains the binding.
     * @param BindingTypeStore $typeStore The store with the defined types.
     */
    public function refreshState(Package $package, BindingTypeStore $typeStore)
    {
        $this->state = BindingState::detect($this, $package, $typeStore);
    }

    /**
     * Returns whether the binding is not loaded.
     *
     * @return bool Returns `true` if the state is {@link BindingState::UNLOADED}.
     *
     * @see BindingState::UNLOADED
     */
    public function isUnloaded()
    {
        return BindingState::UNLOADED === $this->state;
    }

    /**
     * Returns whether the binding is enabled.
     *
     * @return bool Returns `true` if the state is {@link BindingState::ENABLED}.
     *
     * @see BindingState::ENABLED
     */
    public function isEnabled()
    {
        return BindingState::ENABLED === $this->state;
    }

    /**
     * Returns whether the binding is disabled.
     *
     * @return bool Returns `true` if the state is {@link BindingState::DISABLED}.
     *
     * @see BindingState::DISABLED
     */
    public function isDisabled()
    {
        return BindingState::DISABLED === $this->state;
    }

    /**
     * Returns whether the binding is neither enabled nor disabled.
     *
     * @return bool Returns `true` if the state is {@link BindingState::UNDECIDED}.
     *
     * @see BindingState::UNDECIDED
     */
    public function isUndecided()
    {
        return BindingState::UNDECIDED === $this->state;
    }

    /**
     * Returns whether the binding is held back.
     *
     * @return bool Returns `true` if the state is {@link BindingState::HELD_BACK}.
     *
     * @see BindingState::HELD_BACK
     */
    public function isHeldBack()
    {
        return BindingState::HELD_BACK === $this->state;
    }

    /**
     * Returns whether the binding is ignored.
     *
     * @return bool Returns `true` if the state is {@link BindingState::IGNORED}.
     *
     * @see BindingState::IGNORED
     */
    public function isIgnored()
    {
        return BindingState::IGNORED === $this->state;
    }
}
