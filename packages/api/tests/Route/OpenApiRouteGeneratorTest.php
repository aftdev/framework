<?php

namespace AftDevTest\Api\Route;

use AftDev\Api\Route\OpenApiRouteGenerator;
use AftDev\Test\TestCase;
use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Illuminate\Support\LazyCollection;

/**
 * @internal
 * @covers \AftDev\Api\Route\OpenApiRouteGenerator
 */
final class OpenApiRouteGeneratorTest extends TestCase
{
    public function testRoutesWithoutCache()
    {
        $generator = new OpenApiRouteGenerator();
        $routes = $generator->getRoutes($this->getOpenApi());

        $this->assertIsArray($routes);
    }

    public function testGenerator()
    {
        $generator = new OpenApiRouteGenerator();
        $collection = new LazyCollection(fn () => $generator->generateRoutes($this->getOpenApi()));

        $found = [];
        foreach ($collection as $i) {
            $found[$i['uri']][$i['method']] = $i['handler'];
        }

        $expected = [
            '/api/companies' => [
                'get' => 'App\Controller\Companies@index',
                'post' => 'App\Controller\Companies@create',
                'options' => 'App\Controller\Companies@options',
                'head' => 'App\Controller\Companies@head',
                'trace' => 'App\Controller\Companies@trace',
            ],
            '/api/companies/{companyId}' => [
                'get' => 'App\Controller\Companies@show',
                'put' => 'App\Controller\Companies@update',
                'delete' => 'App\Controller\Companies@delete',
            ],
            '/api/companies/{companyId}/employees' => [
                'get' => 'App\Controller\Employees@indexByCompany',
            ],
            '/api/companies/{companyId}/employees/{employeeId}' => [
                'get' => 'App\Controller\Employees@showByCompany',
            ],
            '/api/companies/{companyId}/employees/{employeeId}/salary' => [
                'get' => 'App\Controller\Salaries@indexByCompanyEmployee',
            ],
            '/api/companies/{companyId}/employees/{employeeId}/salary/{category}' => [
                'get' => 'App\Controller\Salaries@showByCompanyEmployeeAndCategory',
            ],
            '/api/test/param/{intParam}/{stringParam}/{optionalParam}' => [
                'get' => 'App\Controller\Params@showByTestAndIntParamStringParamOptionalParam',
            ],
        ];

        $this->assertEquals($expected, $found);

        // Test Parameters
        $testParams = $collection
            ->where('uri', '/api/test/param/{intParam}/{stringParam}/{optionalParam}')
            ->firstWhere('method', 'get')
        ;
        $this->assertEquals([
            'intParam' => [
                'required' => true,
                'schema' => [
                    'type' => 'integer',
                    'format' => 'int32',
                ],
            ],
            'numberParam' => [
                'required' => true,
                'schema' => [
                    'type' => 'number',
                    'format' => 'double',
                ],
            ],
            'dateParam' => [
                'required' => true,
                'schema' => [
                    'type' => 'string',
                    'format' => 'date',
                ],
            ],
            'optionalParam' => [
                'required' => false,
                'schema' => [
                    'type' => 'string',
                    'format' => null,
                ],
            ],
        ], $testParams['params']);
    }

    protected function getOpenApi(): OpenApi
    {
        return Reader::readFromYamlFile(realpath(__DIR__.'/../specs/routes.yaml'));
    }
}
