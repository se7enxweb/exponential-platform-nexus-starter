<?php

declare(strict_types=1);

namespace Netgen\Bundle\IbexaFormsBundle\Tests\Form\Type\FieldType;

use Netgen\Bundle\IbexaFormsBundle\Form\Type\FieldType\UserCreateType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Constraints;

final class UserCreateTypeTest extends TestCase
{
    public function testItExtendsAbstractType(): void
    {
        $userCreateType = new UserCreateType();
        self::assertInstanceOf(AbstractType::class, $userCreateType);
    }

    public function testBuildForm(): void
    {
        $formBuilder = $this->getMockBuilder(FormBuilder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['add'])
            ->getMock();

        $emailOptions = [
            'label' => 'E-mail address',
            'constraints' => [
                new Constraints\NotBlank(),
                new Constraints\Email(),
            ],
        ];

        $usernameOptions = [
            'label' => 'Username',
            'constraints' => [
                new Constraints\NotBlank(),
            ],
        ];

        $passwordOptions = [
            'type' => PasswordType::class,
            'invalid_message' => 'Both passwords must match.',
            'options' => [
                'constraints' => [
                    new Constraints\NotBlank(),
                ],
            ],
            'first_options' => [
                'label' => 'Password',
            ],
            'second_options' => [
                'label' => 'Repeat password',
            ],
        ];

        $formBuilder->expects(self::exactly(3))
            ->method('add')
            ->willReturnCallback(static function ($name, $type, $options = []) use ($formBuilder, $emailOptions, $usernameOptions, $passwordOptions) {
                static $callCount = 0;

                if ($callCount === 0) {
                    self::assertSame('email', $name);
                    self::assertSame(EmailType::class, $type);
                    self::assertEquals($emailOptions, $options);
                } elseif ($callCount === 1) {
                    self::assertSame('username', $name);
                    self::assertSame(TextType::class, $type);
                    self::assertEquals($usernameOptions, $options);
                } elseif ($callCount === 2) {
                    self::assertSame('password', $name);
                    self::assertSame(RepeatedType::class, $type);
                    self::assertEquals($passwordOptions, $options);
                }
                ++$callCount;

                return $formBuilder;
            });

        $userCreateType = new UserCreateType();
        $userCreateType->buildForm($formBuilder, []);
    }
}
