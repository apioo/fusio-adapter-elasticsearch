<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Elasticsearch\Tests;

use Fusio\Adapter\Elasticsearch\Action\ElasticsearchDelete;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchGet;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchUpdate;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchGetAll;
use Fusio\Adapter\Elasticsearch\Connection\Elasticsearch;
use Fusio\Adapter\Elasticsearch\Generator\ElasticsearchDocument;
use Fusio\Engine\Action\Runtime;
use Fusio\Engine\ConnectorInterface;
use Fusio\Engine\Test\EngineTestCaseTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;

/**
 * ElasticsearchTestCase
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
abstract class ElasticsearchTestCase extends TestCase
{
    use EngineTestCaseTrait;

    protected function configure(Runtime $runtime, Container $container): void
    {
        $container->set(Elasticsearch::class, new Elasticsearch());
        $container->set(ElasticsearchDelete::class, new ElasticsearchDelete($runtime));
        $container->set(ElasticsearchGet::class, new ElasticsearchGet($runtime));
        $container->set(ElasticsearchUpdate::class, new ElasticsearchUpdate($runtime));
        $container->set(ElasticsearchGetAll::class, new ElasticsearchGetAll($runtime));
        $container->set(ElasticsearchDocument::class, new ElasticsearchDocument($container->get(ConnectorInterface::class)));
    }
}
