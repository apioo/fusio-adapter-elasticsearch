<?php
/*
 * Fusio is an open source API management platform which helps to create innovative API solutions.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Fusio\Adapter\Elasticsearch\Tests\Connection;

use Elastic\Elasticsearch\Client;
use Fusio\Adapter\Elasticsearch\Connection\Elasticsearch;
use Fusio\Adapter\Elasticsearch\Tests\ElasticsearchTestCase;
use Fusio\Engine\Parameters;

/**
 * ElasticsearchTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class ElasticsearchTest extends ElasticsearchTestCase
{
    public function testGetConnection()
    {
        /** @var Elasticsearch $connectionFactory */
        $connectionFactory = $this->getConnectionFactory()->factory(Elasticsearch::class);

        $config = new Parameters([
            'host' => ['https://127.0.0.1:9200'],
            'username' => 'elastic',
            'password' => 'changeme',
            'no_verify' => true,
        ]);

        $connection = $connectionFactory->getConnection($config);

        $this->assertInstanceOf(Client::class, $connection);
    }

    public function testPing()
    {
        /** @var Elasticsearch $connectionFactory */
        $connectionFactory = $this->getConnectionFactory()->factory(Elasticsearch::class);

        $config = new Parameters([
            'host' => ['https://127.0.0.1:9200'],
            'username' => 'elastic',
            'password' => 'changeme',
            'no_verify' => true,
        ]);

        $connection = $connectionFactory->getConnection($config);

        $this->assertTrue($connectionFactory->ping($connection));
    }
}
