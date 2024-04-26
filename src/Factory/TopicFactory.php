<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Topic;
use App\Repository\TopicRepository;
use Zenstruck\Foundry\ModelFactory;
use Zenstruck\Foundry\Proxy;
use Zenstruck\Foundry\RepositoryProxy;

/**
 * @extends ModelFactory<Topic>
 *
 * @method Topic|Proxy create(array|callable $attributes = [])
 * @method static Topic|Proxy createOne(array $attributes = [])
 * @method static Topic|Proxy find(object|array|mixed $criteria)
 * @method static Topic|Proxy findOrCreate(array $attributes)
 * @method static Topic|Proxy first(string $sortedField = 'id')
 * @method static Topic|Proxy last(string $sortedField = 'id')
 * @method static Topic|Proxy random(array $attributes = [])
 * @method static Topic|Proxy randomOrCreate(array $attributes = [])
 * @method static TopicRepository|RepositoryProxy repository()
 * @method static Topic[]|Proxy[] all()
 * @method static Topic[]|Proxy[] createMany(int $number, array|callable $attributes = [])
 * @method static Topic[]|Proxy[] createSequence(iterable|callable $sequence)
 * @method static Topic[]|Proxy[] findBy(array $attributes)
 * @method static Topic[]|Proxy[] randomRange(int $min, int $max, array $attributes = [])
 * @method static Topic[]|Proxy[] randomSet(int $number, array $attributes = [])
 *
 * @phpstan-method        Proxy<Topic> create(array|callable $attributes = [])
 * @phpstan-method static Proxy<Topic> createOne(array $attributes = [])
 * @phpstan-method static Proxy<Topic> find(object|array|mixed $criteria)
 * @phpstan-method static Proxy<Topic> findOrCreate(array $attributes)
 * @phpstan-method static Proxy<Topic> first(string $sortedField = 'id')
 * @phpstan-method static Proxy<Topic> last(string $sortedField = 'id')
 * @phpstan-method static Proxy<Topic> random(array $attributes = [])
 * @phpstan-method static Proxy<Topic> randomOrCreate(array $attributes = [])
 * @phpstan-method static RepositoryProxy<Topic> repository()
 * @phpstan-method static list<Proxy<Topic>> all()
 * @phpstan-method static list<Proxy<Topic>> createMany(int $number, array|callable $attributes = [])
 * @phpstan-method static list<Proxy<Topic>> createSequence(iterable|callable $sequence)
 * @phpstan-method static list<Proxy<Topic>> findBy(array $attributes)
 * @phpstan-method static list<Proxy<Topic>> randomRange(int $min, int $max, array $attributes = [])
 * @phpstan-method static list<Proxy<Topic>> randomSet(int $number, array $attributes = [])
 */
final class TopicFactory extends ModelFactory
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
            'author' => UserFactory::random(),
            'description' => self::faker()->text(300),
            'favorite' => self::faker()->boolean(25),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): self
    {
        return $this;
        // ->afterInstantiate(function(Topic $topic): void {})
    }

    protected static function getClass(): string
    {
        return Topic::class;
    }
}
