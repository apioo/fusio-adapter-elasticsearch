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
use Fusio\Engine\Form\BuilderInterface;
use Fusio\Engine\Form\ElementFactoryInterface;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use PSX\Http\Environment\HttpResponseInterface;
use PSX\Http\Exception as StatusCode;
use PSX\Http\Exception\InternalServerErrorException;

/**
 * ElasticsearchSearch
 *
 * @author  Christoph Kappestein <christoph.kappestein@gmail.com>
 * @license http://www.gnu.org/licenses/gpl-3.0
 * @link    https://www.fusio-project.org/
 */
class ElasticsearchSearch extends ElasticsearchAbstract
{
    public function getName(): string
    {
        return 'Elasticsearch-Search';
    }

    public function handle(RequestInterface $request, ParametersInterface $configuration, ContextInterface $context): HttpResponseInterface
    {
        $connection = $this->getConnection($configuration);
        $index = $this->getIndex($configuration);

        $size = $configuration->get('size') ?: 16;
        $from = (int) $request->get('startIndex');

        $body = [
            'size' => $size,
            'from' => $from,
        ];

        $match = $this->getMatch($request);
        if (count($match) > 0) {
            $body['query'] = [
                'match' => $match
            ];
        }

        $response = $connection->search([
            'index' => $index,
            'body' => $body,
        ]);

        if (!$response instanceof Elasticsearch) {
            throw new InternalServerErrorException('Connection returned an invalid response');
        }

        $totalCount = $response['hits']['total']['value'] ?? 0;

        $data = [];
        foreach ($response['hits']['hits'] as $hit) {
            $data[] = ['_id' => $hit['_id']] + $hit['_source'];
        }

        return $this->response->build(200, [], [
            'totalResults' => $totalCount,
            'itemsPerPage' => $size,
            'startIndex'   => $from,
            'entry'        => $data,
        ]);
    }

    public function configure(BuilderInterface $builder, ElementFactoryInterface $elementFactory): void
    {
        parent::configure($builder, $elementFactory);

        $builder->add($elementFactory->newInput('sort', 'Sort', 'text', 'The sort column'));
        $builder->add($elementFactory->newInput('size', 'Size', 'number', 'The default size of the result (default is 16)'));
    }

    private function getMatch(RequestInterface $request): array
    {
        $query = $request->get('query');
        if (empty($query)) {
            return [];
        } elseif (!is_array($query)) {
            throw new StatusCode\BadRequestException('The query parameter must contain a field which you want to search i.e. ?query[my_field]=value');
        } else {
            return $query;
        }
    }
}
