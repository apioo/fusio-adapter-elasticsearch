<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2023 Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Fusio\Adapter\Elasticsearch\Action;

use Elastic\Elasticsearch\Client;
use Fusio\Engine\ActionAbstract;
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;

/**
 * ElasticsearchAbstract
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/gpl-3.0
 * @link    https://www.fusio-project.org/
 */
abstract class ElasticsearchAbstract extends ActionAbstract
{
    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newConnection('connection', 'Connection', 'The Elasticsearch connection which should be used'));
        $builder->add($elementFactory->newInput('index', 'Index', 'text', 'The index'));
    }

    protected function getConnection(ParametersInterface $configuration): Client
    {
        $connection = $this->connector->getConnection($configuration->get('connection'));
        if (!$connection instanceof Client) {
            throw new ConfigurationException('Given connection must be a Elasticsearch connection');
        }

        return $connection;
    }

    protected function getIndex(ParametersInterface $configuration): string
    {
        $index = $configuration->get('index');
        if (empty($index)) {
            throw new ConfigurationException('No index provided');
        }

        return $index;
    }
}
