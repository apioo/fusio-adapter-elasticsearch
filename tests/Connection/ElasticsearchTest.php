<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Elasticsearch\Client;
use Fusio\Adapter\Elasticsearch\Connection\Elasticsearch;
use Fusio\Engine\Form\Builder;
use Fusio\Engine\Form\Container;
use Fusio\Engine\Form\Element\Input;
use Fusio\Engine\Parameters;
use Fusio\Engine\Test\EngineTestCaseTrait;

/**
 * ElasticsearchTest
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class ElasticsearchTest extends \PHPUnit_Framework_TestCase
{
    use EngineTestCaseTrait;

    public function testGetConnection()
    {
        /** @var Elasticsearch $connection */
        $connection = $this->getConnectionFactory()->factory(Elasticsearch::class);

        $config = new Parameters([
            'hosts' => '127.0.0.1',
        ]);

        $client = $connection->getConnection($config);

        $this->assertInstanceOf(Client::class, $client);
    }

    public function testConfigure()
    {
        $connection = $this->getConnectionFactory()->factory(Elasticsearch::class);
        $builder    = new Builder();
        $factory    = $this->getFormElementFactory();

        $connection->configure($builder, $factory);

        $this->assertInstanceOf(Container::class, $builder->getForm());

        $elements = $builder->getForm()->getProperty('element');
        $this->assertEquals(1, count($elements));
        $this->assertInstanceOf(Input::class, $elements[0]);
    }
}
