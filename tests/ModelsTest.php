<?php

namespace OpenSoutheners\ExtendedLaravel\Tests;

use Exception;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Mockery as m;
use OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\Post;
use OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\User;
use OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\UuidModel;
use PHPUnit\Framework\TestCase;
use stdClass;

use function OpenSoutheners\ExtendedLaravel\Models\instance_from;
use function OpenSoutheners\ExtendedLaravel\Models\is_model;
use function OpenSoutheners\ExtendedLaravel\Models\key_from;
use function OpenSoutheners\ExtendedLaravel\Models\model_from;
use function OpenSoutheners\ExtendedLaravel\Models\query_from;

class ModelsTest extends TestCase
{
    public function test_model_from(): void
    {
        $this->assertIsString(model_from('Post', true, 'OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\\'));
        $this->assertIsString(model_from('post', true, 'OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\\'));
        $this->assertTrue(model_from('post', false, 'OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\\') instanceof Post);
    }

    public function test_is_model(): void
    {
        $this->assertFalse(is_model(Exception::class));
        $this->assertFalse(is_model(HasAttributes::class));
        $this->assertFalse(is_model(Model::class));
        $this->assertTrue(is_model(Post::class));
        $this->assertTrue(is_model(User::class));
        $this->assertFalse(is_model(null));
        $this->assertFalse(is_model(''));
        $this->assertFalse(is_model(new stdClass));
    }

    public function test_instance_from(): void
    {
        $post = new Post(['id' => 1]);
        $user = new User(['id' => 2]);

        $this->mockConnectionForModel($post, 'SQLite', function ($connection) {
            $connection->shouldReceive('select')->andReturn(['id' => 1]);
            $connection->shouldReceive('find')->with(1)->andReturn(['id' => 1]);
        });

        $this->mockConnectionForModel($user, 'SQLite', function ($connection) {
            $connection->shouldReceive('select')->andReturn(['id' => 2]);

            $user = new User(['id' => 2]);
            $user->setRelation('post', new Post(['id' => 6]));

            $connection->shouldReceive('find')->with(2)->andReturn($user);
        });

        $this->assertTrue(instance_from($post, Post::class) instanceof Post);
        $this->assertTrue(instance_from($user, User::class) instanceof User);
        $this->assertTrue(instance_from(new User(['id' => 2]), User::class) instanceof User);

        /** @var \OpenSoutheners\ExtendedPhp\Tests\Fixtures\Models\User $user */
        $user = instance_from(new User(['id' => 2]), User::class, ['*'], ['post'], true);

        $this->assertTrue($user instanceof User);
        $this->assertTrue($user->relationLoaded('post'));
    }

    public function test_instance_from_with_non_existing_class_throws_exception()
    {
        $this->expectException(ModelNotFoundException::class);
        instance_from(1, 'App\Post');
    }

    public function test_instance_from_a_non_model_class_sent_as_key_throws_exception()
    {
        $maybeModel = new Exception;

        $this->expectException(ModelNotFoundException::class);
        instance_from($maybeModel, Post::class);
    }

    public function test_instance_from_an_unexisting_model_returns_null()
    {
        $post = new Post(['id' => 1]);

        $this->mockConnectionForModel($post, 'SQLite', function ($connection) {
            $connection->shouldReceive('select')->andReturn([]);
            $connection->shouldReceive('find')->with(4)->andReturn(null);
        });

        $this->assertNull(instance_from(4, Post::class));
    }

    public function test_instance_from_with_different_classes_throws_exception()
    {
        $this->expectException(ModelNotFoundException::class);
        instance_from(new Exception, Post::class);
    }

    public function test_key_from()
    {
        $model = new Post(['id' => 1]);

        $modelKey = key_from($model);
        $this->assertIsNumeric($modelKey);
        $this->assertEquals(1, $modelKey);

        $modelKey = key_from('122');
        $this->assertIsNumeric($modelKey);
        $this->assertEquals(122, $modelKey);

        $model = new UuidModel(['uuid' => '7c3a3e74-b602-4e0a-8003-bd7faeefde3d']);

        $modelKey = key_from($model);
        $this->assertIsNotNumeric($modelKey);
        $this->assertIsString($modelKey);
        $this->assertEquals('7c3a3e74-b602-4e0a-8003-bd7faeefde3d', $modelKey);

        $modelKey = key_from($model->uuid);
        $this->assertIsNotNumeric($modelKey);
        $this->assertIsString($modelKey);
        $this->assertEquals('7c3a3e74-b602-4e0a-8003-bd7faeefde3d', $modelKey);

        $myClass = new Exception;
        $modelKey = key_from($myClass);
        $this->assertNull($modelKey);
    }

    public function test_query_from()
    {
        $model = new Post(['id' => 1]);

        $this->mockConnectionForModel($model, 'SQLite');

        $this->assertTrue(query_from($model) instanceof Builder);
        $this->assertFalse(query_from($model) instanceof Model);
        $this->assertTrue(query_from(Post::class) instanceof Builder);
        $this->assertTrue(query_from(Post::query()) instanceof Builder);
        $this->assertTrue(query_from((new BaseBuilder($this->mockConnection('SQLite')))->where('id', 2)) instanceof BaseBuilder);

        $modelQuery = Post::query()->whereKey(1);

        $this->assertNotEquals(query_from($modelQuery)->toSql(), $modelQuery->toSql());
    }

    public function test_query_from_with_raw_class_returns_false()
    {
        $this->assertFalse(query_from((new Exception)));
    }

    /**
     * Mock database connection.
     *
     * @param  string  $database
     * @return \Illuminate\Database\ConnectionInterface
     */
    protected function mockConnection($database)
    {
        $grammarClass = 'Illuminate\Database\Query\Grammars\\'.$database.'Grammar';
        $processorClass = 'Illuminate\Database\Query\Processors\\'.$database.'Processor';
        $grammar = new $grammarClass;
        $processor = new $processorClass;
        $connection = m::mock(ConnectionInterface::class, ['getQueryGrammar' => $grammar, 'getPostProcessor' => $processor]);
        $connection->shouldReceive('query')->andReturnUsing(function () use ($connection, $grammar, $processor) {
            return new BaseBuilder($connection, $grammar, $processor);
        });
        $connection->shouldReceive('getDatabaseName')->andReturn('database');
        $connection->shouldReceive('getName')->andReturn('sqlite');

        return $connection;
    }

    /**
     * Mock model database connection resolver.
     *
     * @param  string|object  $model
     * @param  string  $database
     * @param  callable|null  $callback
     * @return void
     */
    protected function mockConnectionForModel($model, $database, $callback = null)
    {
        $connection = $this->mockConnection($database);

        if ($callback && is_callable($callback)) {
            $callback($connection);
        }

        $resolver = m::mock(ConnectionResolverInterface::class, ['connection' => $connection]);
        $class = get_class($model);
        $class::setConnectionResolver($resolver);
    }
}
