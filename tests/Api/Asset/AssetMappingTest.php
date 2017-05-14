<?php

/*
 * This file is part of the puli/manager package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\Manager\Tests\Api\Asset;

use PHPUnit_Framework_TestCase;
use Puli\Manager\Api\Asset\AssetMapping;
use Rhumsaa\Uuid\Uuid;

/**
 * @since  1.0
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class AssetMappingTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $mapping = new AssetMapping('/blog/public', 'local', '/blog');

        $this->assertSame('/blog/public', $mapping->getGlob());
        $this->assertSame('local', $mapping->getServerName());
        $this->assertSame('/blog', $mapping->getServerPath());
    }

    public function testCreateWithUuid()
    {
        $mapping = new AssetMapping('/blog/public', 'local', '/blog');

        $this->assertSame('/blog/public', $mapping->getGlob());
        $this->assertSame('local', $mapping->getServerName());
        $this->assertSame('/blog', $mapping->getServerPath());
    }

    public function testCreateNormalizesServerPath()
    {
        $mapping = new AssetMapping('/blog/public', 'local', 'blog/');

        $this->assertSame('/blog', $mapping->getServerPath());
    }

    public function testCreateWithEmptyServerPath()
    {
        $mapping = new AssetMapping('/blog/public', 'local', '');

        $this->assertSame('/', $mapping->getServerPath());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfRepositoryPathNull()
    {
        new AssetMapping(null, 'local', 'blog');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfRepositoryPathEmpty()
    {
        new AssetMapping('', 'local', 'blog');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfRepositoryPathNoString()
    {
        new AssetMapping(1234, 'local', 'blog');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfServerNameNull()
    {
        new AssetMapping('/blog/public', null, 'blog');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfServerNameEmpty()
    {
        new AssetMapping('/blog/public', '', 'blog');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfServerNameNoString()
    {
        new AssetMapping('/blog/public', 1234, 'blog');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfJServerPathNull()
    {
        new AssetMapping('/blog/public', 'local', null);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfServerPathNoString()
    {
        new AssetMapping('/blog/public', 'local', 1234);
    }
}
