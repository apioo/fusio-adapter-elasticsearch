<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Adapter\Elasticsearch\Tests\Connection;

use Elastic\Elasticsearch\Client;
use Fusio\Adapter\Elasticsearch\Connection\Elasticsearch;
use Fusio\Engine\Parameters;
use Fusio\Engine\Test\EngineTestCaseTrait;
use PHPUnit\Framework\TestCase;

/**
 * ElasticsearchTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class ElasticsearchTest extends TestCase
{
    use EngineTestCaseTrait;

    public function testGetConnection()
    {
        /** @var Elasticsearch $connectionFactory */
        $connectionFactory = $this->getConnectionFactory()->factory(Elasticsearch::class);

        $config = new Parameters([
            'host' => ['127.0.0.1'],
        ]);

        $connection = $connectionFactory->getConnection($config);

        $this->assertInstanceOf(Client::class, $connection);
    }

    public function testPing()
    {
        /** @var Elasticsearch $connectionFactory */
        $connectionFactory = $this->getConnectionFactory()->factory(Elasticsearch::class);

        $config = new Parameters([
            'host' => ['127.0.0.1'],
        ]);

        $connection = $connectionFactory->getConnection($config);

        $this->assertTrue($connectionFactory->ping($connection));
    }
}
