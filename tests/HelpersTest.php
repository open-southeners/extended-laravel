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
use OpenSoutheners\ExtendedLaravel\Helpers;
use OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\Post;
use OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\User;
use OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\UuidModel;
use PHPUnit\Framework\TestCase;
use stdClass;

class HelpersTest extends TestCase
{
    public function test_model_from(): void
    {
        $this->assertIsString(Helpers::modelFrom('Post', true, 'OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\\'));
        $this->assertIsString(Helpers::modelFrom('post', true, 'OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\\'));
        $this->assertTrue(Helpers::modelFrom('post', false, 'OpenSoutheners\ExtendedLaravel\Tests\Fixtures\Models\\') instanceof Post);
    }

    public function test_is_model(): void
    {
        $this->assertFalse(Helpers::isModel(Exception::class));
        $this->assertFalse(Helpers::isModel(HasAttributes::class));
        $this->assertFalse(Helpers::isModel(Model::class));
        $this->assertTrue(Helpers::isModel(Post::class));
        $this->assertTrue(Helpers::isModel(User::class));
        $this->assertFalse(Helpers::isModel(null));
        $this->assertFalse(Helpers::isModel(''));
        $this->assertFalse(Helpers::isModel(new stdClass));
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

        $this->assertTrue(Helpers::instanceFrom($post, Post::class) instanceof Post);
        $this->assertTrue(Helpers::instanceFrom($user, User::class) instanceof User);
        $this->assertTrue(Helpers::instanceFrom(new User(['id' => 2]), User::class) instanceof User);

        /** @var \OpenSoutheners\ExtendedPhp\Tests\Fixtures\Models\User $user */
        $user = Helpers::instanceFrom(new User(['id' => 2]), User::class, ['*'], ['post'], true);

        $this->assertTrue($user instanceof User);
        $this->assertTrue($user->relationLoaded('post'));
    }

    public function test_instance_from_with_non_existing_class_throws_exception()
    {
        $this->expectException(ModelNotFoundException::class);
        Helpers::instanceFrom(1, 'App\Post');
    }

    public function test_instance_from_a_non_model_class_sent_as_key_throws_exception()
    {
        $maybeModel = new Exception;

        $this->expectException(ModelNotFoundException::class);
        Helpers::instanceFrom($maybeModel, Post::class);
    }

    public function test_instance_from_an_unexisting_model_returns_null()
    {
        $post = new Post(['id' => 1]);

        $this->mockConnectionForModel($post, 'SQLite', function ($connection) {
            $connection->shouldReceive('select')->andReturn([]);
            $connection->shouldReceive('find')->with(4)->andReturn(null);
        });

        $this->assertNull(Helpers::instanceFrom(4, Post::class));
    }

    public function test_instance_from_with_different_classes_throws_exception()
    {
        $this->expectException(ModelNotFoundException::class);
        Helpers::instanceFrom(new Exception, Post::class);
    }

    public function test_key_from()
    {
        $model = new Post(['id' => 1]);

        $modelKey = Helpers::keyFrom($model);
        $this->assertIsNumeric($modelKey);
        $this->assertEquals(1, $modelKey);

        $modelKey = Helpers::keyFrom('122');
        $this->assertIsNumeric($modelKey);
        $this->assertEquals(122, $modelKey);

        $model = new UuidModel(['uuid' => '7c3a3e74-b602-4e0a-8003-bd7faeefde3d']);

        $modelKey = Helpers::keyFrom($model);
        $this->assertIsNotNumeric($modelKey);
        $this->assertIsString($modelKey);
        $this->assertEquals('7c3a3e74-b602-4e0a-8003-bd7faeefde3d', $modelKey);

        $modelKey = Helpers::keyFrom($model->uuid);
        $this->assertIsNotNumeric($modelKey);
        $this->assertIsString($modelKey);
        $this->assertEquals('7c3a3e74-b602-4e0a-8003-bd7faeefde3d', $modelKey);

        $myClass = new Exception;
        $modelKey = Helpers::keyFrom($myClass);
        $this->assertNull($modelKey);
    }

    public function test_query_from()
    {
        $model = new Post(['id' => 1]);

        $this->mockConnectionForModel($model, 'SQLite');

        $this->assertTrue(Helpers::queryFrom($model) instanceof Builder);
        $this->assertFalse(Helpers::queryFrom($model) instanceof Model);
        $this->assertTrue(Helpers::queryFrom(Post::class) instanceof Builder);
        $this->assertTrue(Helpers::queryFrom(Post::query()) instanceof Builder);
        $this->assertTrue(Helpers::queryFrom((new BaseBuilder($this->mockConnection('SQLite')))->where('id', 2)) instanceof BaseBuilder);

        $modelQuery = Post::query()->whereKey(1);

        $this->assertNotEquals(Helpers::queryFrom($modelQuery)->toSql(), $modelQuery->toSql());
    }

    public function test_query_from_with_raw_class_returns_false()
    {
        $this->assertFalse(Helpers::queryFrom((new Exception)));
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
