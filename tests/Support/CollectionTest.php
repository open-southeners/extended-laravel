<?php

namespace OpenSoutheners\ExtendedLaravel\Tests\Support;

use Illuminate\Support\Collection;
use Workbench\App\Models\Post;
use Workbench\App\Models\User;
use OpenSoutheners\ExtendedLaravel\Tests\TestCase;

class CollectionTest extends TestCase
{
    public function test_collection_of_models_to_csv()
    {
        $postOne = new Post(['title' => 'Hello world', 'content' => 'Lorem ipsum dolor']);
        $postTwo = new Post(['title' => 'Hola mundo', 'content' => 'Lorem ipsum']);

        $posts = Collection::make([$postOne, $postTwo]);

        $this->assertEquals(
            "title,content\nHello world,Lorem ipsum dolor\nHola mundo,Lorem ipsum",
            $posts->toCsv()
        );
    }

    public function test_collection_of_arrays_to_csv()
    {
        $collection = Collection::make([
            ['hello' => 'world', 'foo' => 'bar'],
            ['hello' => 'test', 'foo' => 'yes'],
            ['another' => 'world', 'content' => 'Lorem ipsum dolor'],
        ]);

        $this->assertEquals(
            "hello,foo,another,content\nworld,bar,,\ntest,yes,,\n,,world,Lorem ipsum dolor",
            $collection->toCsv()
        );
    }

    public function test_collection_of_users_to_string_using_template_join()
    {
        $userOne = new User(['name' => 'Ruben Robles', 'email' => 'ruben@example.com']);
        $userTwo = new User(['name' => 'Taylor Otwell', 'email' => 'taylor@laravel.com']);

        $users = Collection::make([$userOne, $userTwo]);

        $this->assertEquals(
            "Ruben Robles (ruben@example.com), Taylor Otwell (taylor@laravel.com)",
            $users->templateJoin(':name (:email)', ', ')
        );
    }
}
