<?php
namespace Wandu\Router;

use Psr\Http\Message\ServerRequestInterface;
use Wandu\Router\Contracts\LoaderInterface;
use Wandu\Router\Contracts\ResponsifierInterface;
use Wandu\Router\Contracts\Route as RouteContract;

class Route implements RouteContract
{
    /** @var string */
    protected $className;

    /** @var string */
    protected $methodName;

    /** @var array */
    protected $middlewares;

    /** @var string */
    protected $domains;

    /**
     * @param string $className
     * @param string $methodName
     * @param array $middlewares
     * @param array $domains
     */
    public function __construct($className, $methodName, array $middlewares = [], array $domains = [])
    {
        $this->className = $className;
        $this->methodName = $methodName;
        $this->middlewares = $middlewares;
        $this->domains = $domains;
    }

    /**
     * {@inheritdoc}
     */
    public function middleware($middlewares, $overwrite = false): RouteContract
    {
        if (is_string($middlewares)) {
            $middlewares = [$middlewares];
        }
        $this->middlewares = $overwrite
            ? $middlewares
            : array_merge($this->middlewares, $middlewares);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function domains($domains): RouteContract
    {
        if (is_string($domains)) {
            $domains = [$domains];
        }
        $this->domains = $domains;
        return $this;
    }

    /**
     * @return array
     */
    public function getDomains()
    {
        return $this->domains;
    }
    
    /**
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @param \Wandu\Router\Contracts\LoaderInterface|null $loader
     * @param \Wandu\Router\Contracts\ResponsifierInterface|null $responsifier
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function execute(
        ServerRequestInterface $request,
        LoaderInterface $loader = null,
        ResponsifierInterface $responsifier = null
    ) {
        $pipeline = new RouteExecutor($loader, $responsifier);
        return $pipeline->execute($request, $this->className, $this->methodName, $this->middlewares);
    }
}
