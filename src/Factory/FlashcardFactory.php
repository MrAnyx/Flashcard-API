<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Flashcard;
use App\Repository\FlashcardRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Flashcard>
 *
 * @method Flashcard|Proxy create(array|callable $attributes = [])
 * @method static Flashcard|Proxy createOne(array $attributes = [])
 * @method static Flashcard|Proxy find(object|array|mixed $criteria)
 * @method static Flashcard|Proxy findOrCreate(array $attributes)
 * @method static Flashcard|Proxy first(string $sortedField = 'id')
 * @method static Flashcard|Proxy last(string $sortedField = 'id')
 * @method static Flashcard|Proxy random(array $attributes = [])
 * @method static Flashcard|Proxy randomOrCreate(array $attributes = [])
 * @method static FlashcardRepository|RepositoryProxy repository()
 * @method static Flashcard[]|Proxy[] all()
 * @method static Flashcard[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Flashcard[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Flashcard[]|Proxy[] findBy(array $attributes)
 * @method static Flashcard[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Flashcard[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<Flashcard> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<Flashcard> createOne(array $attributes = [])
 * @phpstan-method static Proxy<Flashcard> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<Flashcard> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<Flashcard> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<Flashcard> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<Flashcard> random(array $attributes = [])
 * @phpstan-method static Proxy<Flashcard> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<Flashcard> repository()
 * @phpstan-method static list<Proxy<Flashcard>> all()
 * @phpstan-method static list<Proxy<Flashcard>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<Flashcard>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<Flashcard>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<Flashcard>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<Flashcard>> randomSet(int $number, array $attributes = [])
 */
final class FlashcardFactory extends ModelFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function getDefaults(): array
    {
        return [
            'front' => self::faker()->text(255),
            'back' => self::faker()->text(255),
            'details' => self::faker()->text(1000),
            'unit' => UnitFactory::random(),
            'favorite' => self::faker()->boolean(25),
            'help' => self::faker()->text(255),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this;
        // ->afterInstantiate(function(Flashcard $flashcard): void {})
    }

    protected static function getClass(): string
    {
        return Flashcard::class;
    }
}
