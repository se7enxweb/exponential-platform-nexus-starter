<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\Form\Type;

use Netgen\Bundle\IbexaFormsBundle\Form\Type\UrlType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType as CoreUrlType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints as Assert;

final class UrlTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        self::assertInstanceOf(AbstractType::class, new UrlType());
    }

    public function testItAddsFormFields(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
            ->getMock();

        $invocation = 0;
        $formBuilder->expects(self::exactly(2))
            ->method('add')
            ->willReturnCallback(static function (string $name, ?string $type = null, array $options = []) use (&$invocation, $formBuilder) {
                if ($invocation === 0) {
                    self::assertSame('url', $name);
                    self::assertSame(CoreUrlType::class, $type);
                    self::assertArrayHasKey('constraints', $options);
                    self::assertInstanceOf(Assert\Url::class, $options['constraints']);
                } elseif ($invocation === 1) {
                    self::assertSame('text', $name);
                    self::assertSame(TextType::class, $type);
                }
                ++$invocation;

                return $formBuilder;
            });

        $url = new UrlType();
        $url->buildForm($formBuilder, []);
    }
}
