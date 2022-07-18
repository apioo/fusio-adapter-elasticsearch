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

namespace Fusio\Adapter\Elasticsearch\Connection;

use Elastic\Elasticsearch\Client;
use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Fusio\Engine\Connection\PingableInterface;
use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;

/**
 * Elasticsearch
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    https://www.fusio-project.org/
 */
class Elasticsearch implements ConnectionInterface, PingableInterface
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
            return $connection->ping()->asBool();
        } catch (NoNodeAvailableException $e) {
            return false;
        }
    }
}
