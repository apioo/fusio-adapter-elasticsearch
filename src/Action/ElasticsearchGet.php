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

use Elastic\Elasticsearch\Response\Elasticsearch;
use Fusio\Engine\ContextInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use PSX\Http\Environment\HttpResponseInterface;
use PSX\Http\Exception\InternalServerErrorException;

/**
 * ElasticsearchGet
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/gpl-3.0
 * @link    https://www.fusio-project.org/
 */
class ElasticsearchGet extends ElasticsearchAbstract
{
    public function getName(): string
    {
        return 'Elasticsearch-Get';
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): HttpResponseInterface
    {
        $connection = $this->getConnection($configuration);
        $index = $this->getIndex($configuration);

        $params = [
            'index' => $index,
            'id'    => $request->get('id'),
        ];

        $response = $connection->get($params);

        if (!$response instanceof Elasticsearch) {
            throw new InternalServerErrorException('Connection returned an invalid response');
        }

        $body = ['_id' => $response['_id']] + $response['_source'];

        return $this->response->build(200, [], $body);
    }
}
