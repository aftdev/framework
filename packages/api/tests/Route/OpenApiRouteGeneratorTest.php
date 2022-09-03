<?php

declare(strict_types=1);

namespace AftDevTest\Api\Route;

use AftDev\Api\Route\OpenApiRouteGenerator;
use AftDev\Api\Route\ParamTranslatorInterface;
use AftDev\Test\TestCase;
use cebe\openapi\Reader;
use cebe\openapi\spec\OpenApi;
use Illuminate\Support\LazyCollection;
use Prophecy\Argument;

/**
 * @internal
 *
 * @covers \AftDev\Api\Route\OpenApiRouteGenerator
 * @covers \AftDev\Api\Route\Route
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
            $found[$i->uri][$i->method] = $i->handler;
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
            '/api/test/param/{intParam}/{stringParam}/{dateParam}/{optionalParam}' => [
                'get' => 'App\Controller\Params@showByTestAndIntParamStringParamDateParamOptionalParam',
            ],
        ];

        $this->assertEquals($expected, $found);
    }

    public function testRouteParamTranslation()
    {
        $paramTranslator = $this->prophesize(ParamTranslatorInterface::class);
        $paramTranslator->translate(Argument::cetera())->willReturn('/{:translated}');

        $generator = new OpenApiRouteGenerator(
            paramTranslator: $paramTranslator->reveal(),
        );

        $collection = $generator->generateRoutes($this->getOpenApi());
        $found = [];
        foreach ($collection as $i) {
            if (!in_array($i->uri, $found)) {
                $found[] = $i->uri;
            }
        }

        $expected = [
            '/api/companies',
            '/api/companies/{:translated}',
            '/api/companies/{:translated}/employees',
            '/api/companies/{:translated}/employees/{:translated}',
            '/api/companies/{:translated}/employees/{:translated}/salary',
            '/api/companies/{:translated}/employees/{:translated}/salary/{:translated}',
            '/api/test/param/{:translated}/{:translated}/{:translated}/{:translated}',
        ];

        $this->assertEquals($expected, $found);
    }

    protected function getOpenApi(): OpenApi
    {
        return Reader::readFromYamlFile(realpath(__DIR__.'/../specs/routes.yaml'));
    }
}
