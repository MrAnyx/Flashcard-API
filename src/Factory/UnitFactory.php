<?php

namespace App\Factory;

use App\Entity\Unit;
use Zenstruck\Foundry\Proxy;
use App\Repository\UnitRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Unit>
 *
 * @method Unit|Proxy create(array|callable $attributes = [])
 * @method static Unit|Proxy createOne(array $attributes = [])
 * @method static Unit|Proxy find(object|array|mixed $criteria)
 * @method static Unit|Proxy findOrCreate(array $attributes)
 * @method static Unit|Proxy first(string $sortedField = 'id')
 * @method static Unit|Proxy last(string $sortedField = 'id')
 * @method static Unit|Proxy random(array $attributes = [])
 * @method static Unit|Proxy randomOrCreate(array $attributes = [])
 * @method static UnitRepository|RepositoryProxy repository()
 * @method static Unit[]|Proxy[] all()
 * @method static Unit[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Unit[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Unit[]|Proxy[] findBy(array $attributes)
 * @method static Unit[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Unit[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<Unit> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<Unit> createOne(array $attributes = [])
 * @phpstan-method static Proxy<Unit> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<Unit> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<Unit> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<Unit> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<Unit> random(array $attributes = [])
 * @phpstan-method static Proxy<Unit> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<Unit> repository()
 * @phpstan-method static list<Proxy<Unit>> all()
 * @phpstan-method static list<Proxy<Unit>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<Unit>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<Unit>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<Unit>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<Unit>> randomSet(int $number, array $attributes = [])
 */
final class UnitFactory extends ModelFactory
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
            'name' => self::faker()->text(35),
            'topic' => TopicFactory::random(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this;
        // ->afterInstantiate(function(Unit $unit): void {})
    }

    protected static function getClass(): string
    {
        return Unit::class;
    }
}
