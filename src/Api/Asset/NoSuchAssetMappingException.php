<?php

/*
 * This file is part of the puli/manager package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Manager\Api\Asset;

use Exception;
use Rhumsaa\Uuid\Uuid;

/**
 * Thrown when an asset mapping was not found.
 *
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class NoSuchAssetMappingException extends Exception
{
    /**
     * Creates an exception for a AssetMapping that was not found.
     *
     * @param AssetMapping   $mapping
     * @param Exception|null $cause The exception that caused this exception.
     *
     * @return static The created exception.
     */
    public static function forMapping(AssetMapping $mapping, Exception $cause = null)
    {
        return new static(sprintf(
            'The asset mapping (glob: "%s" and serverName: "%s") does not exist.',
            $mapping->getGlob(), $mapping->getServerName()
        ), 0, $cause);
    }
}
