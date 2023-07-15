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

namespace Fusio\Adapter\Elasticsearch\Connection;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Response\Elasticsearch as ElasticsearchResponse;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Fusio\Engine\Connection\PingableInterface;
use Fusio\Engine\ConnectionAbstract;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;

/**
 * Elasticsearch
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @link    https://www.fusio-project.org/
 */
class Elasticsearch extends ConnectionAbstract implements PingableInterface
{
    public function getName(): string
    {
        return 'Elasticsearch';
    }

    public function getConnection(ParametersInterface $config): Client
    {
        $builder = ClientBuilder::create();

        $hosts = $config->get('host');
        if (is_array($hosts)) {
            $builder->setHosts($hosts);
        }

        $username = $config->get('username');
        $password = $config->get('password');
        if (!empty($username) && !empty($password)) {
            $builder->setBasicAuthentication($username, $password);
        }

        if ($config->get('no_verify')) {
            $builder->setSSLVerification(false);
        }

        return $builder->build();
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        $builder->add($elementFactory->newCollection('host', 'Host', 'text', 'List of elasticsearch hosts i.e. <code>https://192.168.1.1:9200</code>'));
        $builder->add($elementFactory->newInput('username', 'Username', 'text', 'Optional the username'));
        $builder->add($elementFactory->newInput('password', 'Password', 'password', 'Optional the password'));
        $builder->add($elementFactory->newCheckbox('no_verify', 'No-SSL-Verify', 'Optional whether to ignore SSL verification'));
    }

    public function ping(mixed $connection): bool
    {
        if (!$connection instanceof Client) {
            return false;
        }

        try {
            $result = $connection->ping();
            if ($result instanceof ElasticsearchResponse) {
                return $result->asBool();
            } else {
                return false;
            }
        } catch (NoNodeAvailableException $e) {
            return false;
        }
    }
}
