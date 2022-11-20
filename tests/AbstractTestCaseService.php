<?php

namespace App\Tests;

use App\Entity\Connection;
use App\Kernel;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionClass;
use ReflectionException;
use ReflectionMethod;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Throwable;

class AbstractTestCaseService extends KernelTestCase
{
    /* @var Kernel */
    protected Kernel $app;

    /**
     * @var EntityManagerInterface
     */
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine')->getManager();
    }

    /**
     * Set access public for select method
     *
     * @param $service
     * @param $nameMethod
     *
     * @return ReflectionMethod
     * @throws ReflectionException
     */
    protected function setPublicMethod($service, $nameMethod): ReflectionMethod
    {
        $reflection = new ReflectionClass($service);

        $method = $reflection->getMethod($nameMethod);
        $method->setAccessible(true);

        return $method;
    }

    protected function getMockWithMethods($className, $valuesWithMethod): MockObject
    {
        $mock = $this->getMockBuilder($className)
            ->onlyMethods(array_keys($valuesWithMethod))
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($valuesWithMethod as $method => $value) {
            if ($value instanceof Throwable) {
                $mock->expects($this->once())->method($method)->willThrowException($value);
            } else {
                $mock->expects($this->once())->method($method)->willReturn($value);
            }
        }

        return $mock;
    }
}
