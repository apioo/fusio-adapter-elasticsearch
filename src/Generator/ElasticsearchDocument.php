<?php
/*
 * Fusio - Self-Hosted API Management for Builders.
 * For the current version and information visit <https://www.fusio-project.org/>
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
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

namespace Fusio\Adapter\Elasticsearch\Generator;

use Fusio\Adapter\Elasticsearch\Action\ElasticsearchDelete;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchGet;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchGetAll;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchUpdate;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\Generator\ProviderInterface;
use Fusio\Engine\Generator\SetupInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\Schema\SchemaBuilder;
use Fusio\Engine\Schema\SchemaName;
use Fusio\Model\Backend\ActionConfig;
use Fusio\Model\Backend\ActionCreate;
use Fusio\Model\Backend\OperationCreate;
use Fusio\Model\Backend\SchemaCreate;

/**
 * ElasticsearchDocument
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class ElasticsearchDocument implements ProviderInterface
{
    private const SCHEMA_GET_ALL = 'Elasticsearch_GetAll';
    private const ACTION_GET_ALL = 'Elasticsearch_GetAll';
    private const ACTION_GET = 'Elasticsearch_Get';
    private const ACTION_UPDATE = 'Elasticsearch_Update';
    private const ACTION_DELETE = 'Elasticsearch_Delete';

    public function getName(): string
    {
        return 'Elasticsearch-Document';
    }

    public function setup(SetupInterface $setup, ParametersInterface $configuration): void
    {
        $setup->addSchema($this->makeGetAllSchema());

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

    private function makeGetAllSchema(): SchemaCreate
    {
        $schema = new SchemaCreate();
        $schema->setName(self::SCHEMA_GET_ALL);
        $schema->setSource(SchemaBuilder::makeCollectionResponse(self::SCHEMA_GET_ALL, null));
        return $schema;
    }

    private function makeGetAllAction(ParametersInterface $configuration): ActionCreate
    {
        $action = new ActionCreate();
        $action->setName(self::ACTION_GET_ALL);
        $action->setClass(ElasticsearchGetAll::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'index' => $configuration->get('index'),
        ]));
        return $action;
    }

    private function makeGetAction(ParametersInterface $configuration): ActionCreate
    {
        $action = new ActionCreate();
        $action->setName(self::ACTION_GET);
        $action->setClass(ElasticsearchGet::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'index' => $configuration->get('index'),
        ]));
        return $action;
    }

    private function makeUpdateAction(ParametersInterface $configuration): ActionCreate
    {
        $action = new ActionCreate();
        $action->setName(self::ACTION_UPDATE);
        $action->setClass(ElasticsearchUpdate::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'index' => $configuration->get('index'),
        ]));
        return $action;
    }

    private function makeDeleteAction(ParametersInterface $configuration): ActionCreate
    {
        $action = new ActionCreate();
        $action->setName(self::ACTION_DELETE);
        $action->setClass(ElasticsearchDelete::class);
        $action->setConfig(ActionConfig::fromArray([
            'connection' => $configuration->get('connection'),
            'index' => $configuration->get('index'),
        ]));
        return $action;
    }

    private function makeGetAllOperation(): OperationCreate
    {
        $operation = new OperationCreate();
        $operation->setName('getAll');
        $operation->setDescription('Returns a collection of documents');
        $operation->setHttpMethod('GET');
        $operation->setHttpPath('/');
        $operation->setHttpCode(200);
        $operation->setParameters(SchemaBuilder::makeCollectionParameters());
        $operation->setOutgoing(self::SCHEMA_GET_ALL);
        $operation->setAction(self::ACTION_GET_ALL);
        return $operation;
    }

    private function makeGetOperation(): OperationCreate
    {
        $operation = new OperationCreate();
        $operation->setName('get');
        $operation->setDescription('Returns a single document');
        $operation->setHttpMethod('GET');
        $operation->setHttpPath('/:id');
        $operation->setHttpCode(200);
        $operation->setOutgoing(SchemaName::PASSTHRU);
        $operation->setAction(self::ACTION_GET);
        return $operation;
    }

    private function makeUpdateOperation(): OperationCreate
    {
        $operation = new OperationCreate();
        $operation->setName('index');
        $operation->setDescription('Updates an existing document');
        $operation->setHttpMethod('PUT');
        $operation->setHttpPath('/:id');
        $operation->setHttpCode(200);
        $operation->setIncoming(SchemaName::PASSTHRU);
        $operation->setOutgoing(SchemaName::MESSAGE);
        $operation->setAction(self::ACTION_UPDATE);
        return $operation;
    }

    private function makeDeleteOperation(): OperationCreate
    {
        $operation = new OperationCreate();
        $operation->setName('delete');
        $operation->setDescription('Deletes an existing document');
        $operation->setHttpMethod('DELETE');
        $operation->setHttpPath('/:id');
        $operation->setHttpCode(200);
        $operation->setOutgoing(SchemaName::MESSAGE);
        $operation->setAction(self::ACTION_DELETE);
        return $operation;
    }
}
