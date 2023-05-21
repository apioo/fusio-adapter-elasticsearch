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

namespace Fusio\Adapter\Elasticsearch\Generator;

use Fusio\Adapter\Elasticsearch\Action\ElasticsearchDelete;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchGet;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchUpdate;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchGetAll;
use Fusio\Engine\ConnectorInterface;
use Fusio\Engine\Factory\Resolver\PhpClass;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\Generator\ProviderInterface;
use Fusio\Engine\Generator\SetupInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Model\Backend\Action;
use Fusio\Model\Backend\ActionConfig;
use Fusio\Model\Backend\Operation;
use Fusio\Model\Backend\OperationParameters;
use Fusio\Model\Backend\OperationSchema;
use Fusio\Model\Backend\Schema;
use Fusio\Model\Backend\SchemaSource;

/**
 * ElasticsearchDocument
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class ElasticsearchDocument implements ProviderInterface
{
    private ConnectorInterface $connector;

    public function __construct(ConnectorInterface $connector)
    {
        $this->connector = $connector;
    }

    public function getName(): string
    {
        return 'Elasticsearch-Document';
    }

    public function setup(SetupInterface $setup, string $basePath, ParametersInterface $configuration): void
    {
        $setup->addAction($this->makeGetAllAction($configuration));
        $setup->addAction($this->makeGetAction($configuration));
        $setup->addAction($this->makeUpdateAction($configuration));
        $setup->addAction($this->makeDeleteAction($configuration));

        $setup->addOperation($this->makeGetAllOperation());
        $setup->addOperation($this->makeGetOperation());
        $setup->addOperation($this->makeUpdateOperation());
        $setup->addOperation($this->makeDeleteOperation());
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newConnection('connection', 'Connection', 'The elasticsearch connection which should be used'));
        $builder->add($elementFactory->newInput('index', 'Index', 'text', 'Name of the index'));
    }

    private function makeGetAllAction(ParametersInterface $configuration): Action
    {
        $action = new Action();
        $action->setName('Elasticsearch_GetAll');
        $action->setClass(ElasticsearchGetAll::class);
        $action->setEngine(PhpClass::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'index' => $configuration->get('index'),
        ]));
        return $action;
    }

    private function makeGetAction(ParametersInterface $configuration): Action
    {
        $action = new Action();
        $action->setName('Elasticsearch_Get');
        $action->setClass(ElasticsearchGet::class);
        $action->setEngine(PhpClass::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'index' => $configuration->get('index'),
        ]));
        return $action;
    }

    private function makeUpdateAction(ParametersInterface $configuration): Action
    {
        $action = new Action();
        $action->setName('Elasticsearch_Update');
        $action->setClass(ElasticsearchUpdate::class);
        $action->setEngine(PhpClass::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'index' => $configuration->get('index'),
        ]));
        return $action;
    }

    private function makeDeleteAction(ParametersInterface $configuration): Action
    {
        $action = new Action();
        $action->setName('Elasticsearch_Delete');
        $action->setClass(ElasticsearchDelete::class);
        $action->setEngine(PhpClass::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'index' => $configuration->get('index'),
        ]));
        return $action;
    }

    private function makeGetAllOperation(): Operation
    {
        $querySchema = new OperationSchema();
        $querySchema->setType('string');

        $startIndexSchema = new OperationSchema();
        $startIndexSchema->setType('integer');

        $parameters = new OperationParameters();
        $parameters->put('query', $querySchema);
        $parameters->put('startIndex', $startIndexSchema);

        $operation = new Operation();
        $operation->setName('getAll');
        $operation->setDescription('Returns a collection of documents');
        $operation->setHttpMethod('GET');
        $operation->setHttpPath('/');
        $operation->setHttpCode(200);
        $operation->setParameters($parameters);
        $operation->setOutgoing('Passthru');
        return $operation;
    }

    private function makeGetOperation(): Operation
    {
        $operation = new Operation();
        $operation->setName('get');
        $operation->setDescription('Returns a single document');
        $operation->setHttpMethod('GET');
        $operation->setHttpPath('/:id');
        $operation->setHttpCode(200);
        $operation->setOutgoing('Passthru');
        return $operation;
    }

    private function makeUpdateOperation(): Operation
    {
        $operation = new Operation();
        $operation->setName('index');
        $operation->setDescription('Updates an existing document');
        $operation->setHttpMethod('PUT');
        $operation->setHttpPath('/:id');
        $operation->setHttpCode(200);
        $operation->setIncoming('Passthru');
        $operation->setOutgoing('Passthru');
        return $operation;
    }

    private function makeDeleteOperation(): Operation
    {
        $operation = new Operation();
        $operation->setName('delete');
        $operation->setDescription('Deletes an existing document');
        $operation->setHttpMethod('DELETE');
        $operation->setHttpPath('/:id');
        $operation->setHttpCode(200);
        $operation->setOutgoing('Passthru');
        return $operation;
    }
}
