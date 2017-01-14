<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2016 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Elasticsearch\ClientBuilder;
use Fusio\Engine\ConnectionInterface;
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;

/**
 * Elasticsearch
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/agpl-3.0
 * @link    http://fusio-project.org
 */
class Elasticsearch implements ConnectionInterface
{
    public function getName()
    {
        return 'Elasticsearch';
    }

    /**
     * @param \Fusio\Engine\ParametersInterface $config
     * @return \Elasticsearch\Client
     */
    public function getConnection(ParametersInterface $config)
    {
        $builder = ClientBuilder::create();

        $hosts = $config->get('hosts');
        if (!empty($hosts)) {
            $builder->setHosts(explode(',', $hosts));
        }

        return $builder->build();
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory)
    {
        $builder->add($elementFactory->newInput('hosts', 'Hosts', 'text', 'Comma separated list of elasticsearch hosts i.e. <code>192.168.1.1:9200,192.168.1.2</code>'));
    }
}
