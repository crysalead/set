<?php
namespace set\spec\suite;

use set\Set;
use Exception;
use BadFunctionCallException;

describe("Set", function() {

    describe("::merge()", function() {

        it("merges two arrays", function() {

            $result = Set::merge(['foo'], ['bar']);
            expect($result)->toBe(['foo', 'bar']);

        });

        it("merges an array with an empty array", function() {

            $result = Set::merge(['foo'], []);
            expect($result)->toBe(['foo']);

        });

        it("merges arrays recursively", function() {

            $a = ['users' => ['joe' => ['id' => 1, 'password' => 'may the force']], 'gun'];
            $b = ['users' => ['bob' => ['id' => 2, 'password' => 'be with you']], 'ice-cream'];
            $result = Set::merge($a, $b);
            expect($result)->toBe([
                'users' => [
                    'joe' => ['id' => 1, 'password' => 'may the force'],
                    'bob' => ['id' => 2, 'password' => 'be with you']
                ],
                'gun',
                'ice-cream'
            ]);

        });

        it("throws an exception with not enough parameters", function() {

            $closure = function() {
                Set::merge();
            };
            expect($closure)->toThrow(new BadFunctionCallException("Not enough parameters"));

            $closure = function() {
                Set::merge([]);
            };
            expect($closure)->toThrow(new BadFunctionCallException("Not enough parameters"));

        });

    });

    describe("::slice()", function() {

        it("slices two arrays", function() {
            $data = ['key1' => 'val1', 'key2' => 'val2', 'key3' => 'val3'];
            list($kept, $removed) = Set::slice($data, ['key3']);
            expect($removed)->toBe(['key3' => 'val3']);
            expect($kept)->toBe(['key1' => 'val1', 'key2' => 'val2']);
        });

    });

    describe("::flatten()", function() {

        $this->expanded = [
            [
                'Post' => ['id' => '1', 'author_id' => '1', 'title' => 'First Post'],
                'Author' => ['id' => '1', 'user' => 'nate', 'password' => 'foo']
            ],
            [
                'Post' => [ 'id' => '2', 'author_id' => '5', 'title' => 'Second Post'],
                'Author' => ['id' => '5', 'user' => 'jeff', 'password' => null]
            ]
        ];

        $this->flattened = [
            '0.Post.id' => '1',
            '0.Post.author_id' => '1',
            '0.Post.title' => 'First Post',
            '0.Author.id' => '1',
            '0.Author.user' => 'nate',
            '0.Author.password' => 'foo',
            '1.Post.id' => '2',
            '1.Post.author_id' => '5',
            '1.Post.title' => 'Second Post',
            '1.Author.id' => '5',
            '1.Author.user' => 'jeff',
            '1.Author.password' => null
        ];

        it("flattens", function() {

            $result = Set::flatten($this->expanded);
            expect($result)->toBe($this->flattened);

        });

        it("expands", function() {

            $result = Set::expand($this->flattened);
            expect($result)->toBe($this->expanded);

        });

    });

    describe("::normalize()", function() {

        it("normalizes arrays", function() {

            $result = Set::normalize(['one', 'two', 'three']);
            $expected = ['one' => null, 'two' => null, 'three' => null];
            expect($result)->toBe($expected);

            $result = Set::normalize(['one' => ['a', 'b', 'c' => 'd'], 'two' => 2, 'three']);
            $expected = ['one' => ['a', 'b', 'c' => 'd'], 'two' => 2, 'three' => null];
            expect($result)->toBe($expected);

        });

        it("throws with non-normalizable array", function() {

            $closure = function() {
                Set::normalize([['a', 'b', 'c' => 'd'], 'two' => 2, 'three']);
            };
            expect($closure)->toThrow(new Exception("Invalid array format, a value can't be normalized"));

        });

    });

});
