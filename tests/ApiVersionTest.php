<?php
namespace think\tests;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use think\Request;
use think\Route;

class ApiVersionTest extends TestCase
{
    use InteractsWithApp;

    /** @var Route */
    protected $route;

    protected function setUp(): void
    {
        $this->prepareApp();
        $this->route = new Route($this->app);
    }

    protected function tearDown(): void
    {
        m::close();
    }

    protected function makeRequest($path, $method = 'GET', $version = null)
    {
        $request = m::mock(Request::class)->makePartial();
        $request->shouldReceive('host')->andReturn('localhost');
        $request->shouldReceive('pathinfo')->andReturn($path);
        $request->shouldReceive('url')->andReturn('/' . $path);
        $request->shouldReceive('method')->andReturn(strtoupper($method));
        $request->shouldReceive('header')->with('Api-Version')->andReturn($version);

        return $request;
    }

    public function testApiVersionFromHeader()
    {
        $this->route->group('api', function () {
            $this->route->get('products', function () {
                return 'v1 products';
            })->version('1.0');

            $this->route->get('products', function () {
                return 'v2 products';
            })->version('2.0');
        });

        // 测试请求头版本1.0
        $request  = $this->makeRequest('api/products', 'GET', '1.0');
        $response = $this->route->dispatch($request);
        $this->assertEquals('v1 products', $response->getContent());

        // 测试请求头版本2.0
        $request  = $this->makeRequest('api/products', 'GET', '2.0');
        $response = $this->route->dispatch($request);
        $this->assertEquals('v2 products', $response->getContent());

        // 测试无效版本
        $this->expectException('\\think\\exception\\HttpException');
        $request = $this->makeRequest('api/products', 'GET', '3.0');
        $this->route->dispatch($request);
    }

}
