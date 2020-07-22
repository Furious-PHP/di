## DI Container

Very simple PSR-11 DI Container for PHP 7.4+

[![Latest Version](https://img.shields.io/github/release/Furious-PHP/di.svg?style=flat-square)](https://github.com/Furious-PHP/di/releases)
[![Build Status](https://scrutinizer-ci.com/g/Furious-PHP/di/badges/build.png?b=master)](https://scrutinizer-ci.com/g/Furious-PHP/di/build-status/master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/Furious-PHP/di/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![Quality Score](https://img.shields.io/scrutinizer/g/Furious-PHP/di.svg?style=flat-square)](https://scrutinizer-ci.com/g/Furious-PHP/di)
[![Maintainability](https://api.codeclimate.com/v1/badges/71ecfc66e6100d3ffa0d/maintainability)](https://codeclimate.com/github/Furious-PHP/di/maintainability)
[![Total Downloads](https://poser.pugx.org/furious/container/downloads)](https://packagist.org/packages/furious/container)
[![Monthly Downloads](https://poser.pugx.org/furious/container/d/monthly.png)](https://packagist.org/packages/furious/container)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

Install:

    composer require furious/container

Use:

    $container = new Container();
    
    // get
    
    $container->get('key');
    
    // has
    
    $container->has('key');
    
    // put integers
    $container->set(1, 100);
    $container->put(2, 200); // use set or put
    
    // put string
    $container->set('some string', 'some value');
    
    // put array
    $container->set('config', [
        'debug' => true,
        'some key' => 'some value'
    ]);
    
    // put clojure
    // return SomeClass instance
    $container->set(SomeClass::class, function (ContainerInterface $container) {
        return new SomeClass();
    });
    
    // put object
    $container->set(SomeAnotherClass::class, new SomeAnotherClass('value', 'value'));
    
    // autowiring
    
    class A
    {
        public function __construct(B $b)
        {
        }
    }
    
    class B
    {
    
    }
    
    $container->get(A::class);
    
    // Factories
    
    class Factory
    {
        public function __invoke(ContainerInterface $container)
        {
            return ExampleClass(
                $container->get('key'),
                $container->get('key'),
                $container->get('key'),
                $container->get('key')
            );
        }
    }
    
    $container->set(ExampleClass::class, function (ContainerInterface $container) {
        return (new Factory())($container);
    });
    
    // Interfaces
    
    interface SomeInterface
    {
    
    }
    
    class SomeClass implements SomeInterface
    {
        
    }
    
    $container->get(SomeInterface::class, function (ContainerInterface $container) {
        return $container->get(SomeClass::class);
    });