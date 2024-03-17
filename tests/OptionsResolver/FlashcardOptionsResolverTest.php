<?php

declare(strict_types=1);

namespace App\Tests\OptionsResolver;

use App\Entity\Unit;
use App\OptionsResolver\FlashcardOptionsResolver;
use App\Repository\UnitRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class FlashcardOptionsResolverTest extends KernelTestCase
{
    public function testConfigureFront(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureFront(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(\array_key_exists('front', $result));
    }

    public function testConfigureFrontRequired(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureFront(true);

        $front = 'front text';

        $result = $resolver->resolve(['front' => $front]);

        $this->assertSame($front, $result['front']);
    }

    public function testConfigureFrontRequiredWithMissingParameter(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureFront(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureFrontInvalidData(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureFront(true);

        $front = 123;

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['front' => $front]);
    }

    public function testConfigureBack(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureBack(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(\array_key_exists('back', $result));
    }

    public function testConfigureBackRequired(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureBack(true);

        $back = 'back text';

        $result = $resolver->resolve(['back' => $back]);

        $this->assertSame($back, $result['back']);
    }

    public function testConfigureBackRequiredWithMissingParameter(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureBack(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureBackInvalidData(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureBack(true);

        $back = 123;

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['back' => $back]);
    }

    public function testConfigureDetails(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureDetails(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(\array_key_exists('details', $result));
    }

    public function testConfigureDetailsRequired(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureDetails(true);

        $details = 'details text';

        $result = $resolver->resolve(['details' => $details]);

        $this->assertSame($details, $result['details']);
    }

    public function testConfigureDetailsRequiredWithMissingParameter(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureDetails(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureDetailsInvalidData(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureDetails(true);

        $details = 123;

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['details' => $details]);
    }

    public function testConfigureDetailsNullConversion(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureDetails(true);

        $details = '   ';

        $result = $resolver->resolve(['details' => $details]);

        $this->assertNull($result['details']);
    }

    public function testConfigureUnit(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureUnit(false);

        $result = $resolver->resolve([]);

        $this->assertFalse(\array_key_exists('unit', $result));
    }

    public function testConfigureUnitRequired(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureUnit(true);

        /** @var Unit $unit */
        $unit = self::getContainer()->get(UnitRepository::class)->find(1);

        $result = $resolver->resolve(['unit' => $unit->getId()]);

        $this->assertSame($unit, $result['unit']);
    }

    public function testConfigureUnitRequiredWithMissingParameter(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureUnit(true);

        $this->expectException(MissingOptionsException::class);

        $result = $resolver->resolve([]);
    }

    public function testConfigureUnitInvalidData(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureUnit(true);

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['unit' => 'Hello World']);
    }

    public function testConfigureUnitUnknownUnit(): void
    {
        /** @var FlashcardOptionsResolver $resolver */
        $resolver = self::getContainer()->get(FlashcardOptionsResolver::class);
        $resolver->configureUnit(true);

        $this->expectException(InvalidOptionsException::class);

        $result = $resolver->resolve(['unit' => -1]);
    }
}
