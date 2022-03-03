<?php
/*
 * Fusio
 * A web-application to create dynamically RESTful APIs
 *
 * Copyright (C) 2015-2022 Christoph Kappestein <christoph.kappestein@gmail.com>
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

use Fusio\Engine\ContextInterface;
use Fusio\Engine\Exception\ConfigurationException;
use Fusio\Engine\ParametersInterface;
use Fusio\Engine\RequestInterface;
use PSX\Http\Environment\HttpResponseInterface;

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

        $index = $configuration->get('index');
        if (empty($index)) {
            throw new ConfigurationException('No index provided');
        }

        $match = array_filter([
            'query' => $request->get('query'),
            'analyzer' => $request->get('analyzer'),
            'auto_generate_synonyms_phrase_query' => $request->get('auto_generate_synonyms_phrase_query'),
            'fuzziness' => $request->get('fuzziness'),
            'max_expansions' => $request->get('max_expansions'),
            'prefix_length' => $request->get('prefix_length'),
            'fuzzy_transpositions' => $request->get('fuzzy_transpositions'),
            'fuzzy_rewrite' => $request->get('fuzzy_rewrite'),
            'lenient' => $request->get('lenient'),
            'operator' => $request->get('operator'),
            'minimum_should_match' => $request->get('minimum_should_match'),
            'zero_terms_query' => $request->get('zero_terms_query'),
        ], function($value){
            return $value !== null;
        });

        $params = [
            'index' => $index,
            'body'  => [
                'query' => [
                    'match' => $match,
                ]
            ]
        ];

        $response = $connection->search($params);

        return $this->response->build(200, [], $response);
    }
}
