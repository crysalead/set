<?php
namespace spec\set;

use set\Set;
use BadFunctionCallException;

describe("Set", function() {

    describe("merge", function() {

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

        it("returns an exception with not enough parameters", function() {

            $closure = function() {
                Set::merge();
            };
            expect($closure)->toThrow(new BadFunctionCallException());

            $closure = function() {
                Set::merge([]);
            };
            expect($closure)->toThrow(new BadFunctionCallException());

        });

    });

});

describe("Set", function() {

    describe("slice", function() {

        it("slices two arrays", function() {
            $data = ['key1' => 'val1', 'key2' => 'val2', 'key3' => 'val3'];
            list($kept, $removed) = Set::slice($data, ['key3']);
            expect($removed)->toBe(['key3' => 'val3']);
            expect($kept)->toBe(['key1' => 'val1', 'key2' => 'val2']);
        });

    });

});

describe("Set", function() {

    describe("flatten", function() {

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

        describe("Set::flatten", function() {

            it("flattens", function() {

                $result = Set::flatten($this->expanded);
                expect($result)->toBe($this->flattened);

            });

        });

        describe("Set::expand", function() {

            it("expands", function() {

                $result = Set::expand($this->flattened);
                expect($result)->toBe($this->expanded);

            });

        });

    });

});
