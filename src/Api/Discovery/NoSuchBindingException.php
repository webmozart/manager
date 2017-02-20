<?php
/*
 * This file is part of the puli/manager package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Puli\Manager\Api\Discovery;
use Exception;
use Puli\Discovery\Api\Binding\Binding;
use RuntimeException;

/**
 * Thrown when a binding was not found.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class NoSuchBindingException extends RuntimeException
{
    /**
     * Creates an exception for when a Binding that was not found.
     *
     * @param Binding        $binding
     * @param Exception|null $cause The exception that caused this exception.
     *
     * @return static The created exception.
     */
    public static function forBinding(Binding $binding, Exception $cause = null)
    {
        return new static(
            sprintf('The binding for type "%s" does not exist.', $binding->getTypeName()),
            0,
            $cause
        );
    }
}