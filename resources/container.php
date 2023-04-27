<?php

use Fusio\Adapter\Cli\Action\CliProcessor;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchDelete;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchGet;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchIndex;
use Fusio\Adapter\Elasticsearch\Action\ElasticsearchSearch;
use Fusio\Adapter\Elasticsearch\Connection\Elasticsearch;
use Fusio\Adapter\Elasticsearch\Generator\ElasticsearchDocument;
use Fusio\Engine\Adapter\ServiceBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container) {
    $services = ServiceBuilder::build($container);
    $services->set(Elasticsearch::class);
    $services->set(ElasticsearchDelete::class);
    $services->set(ElasticsearchGet::class);
    $services->set(ElasticsearchIndex::class);
    $services->set(ElasticsearchSearch::class);
    $services->set(ElasticsearchDocument::class);
};
