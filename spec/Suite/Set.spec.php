<?php
namespace Lead\Set\Spec\Suite;

use Lead\Set\Set;
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

        it("keeps keys for associative arrays", function() {

            $a = [];
            $b = [2 => 'test2'];
            $result = Set::merge($a, $b);
            expect($result)->toBe([
                2 => "test2"
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

    describe("::extend()", function() {

        it("extends two array", function() {

            $result = Set::extend(['foo'], ['bar']);
            expect($result)->toBe(['bar']);

        });

        it("extends an array with an empty array", function() {

            $result = Set::extend(['foo'], []);
            expect($result)->toBe(['foo']);

        });

        it("extends arrays recursively", function() {

            $a = ['users' => ['joe' => ['id' => 1, 'password' => 'may the force']], 'gun'];
            $b = ['users' => ['bob' => ['id' => 2, 'password' => 'be with you']], 'ice-cream'];
            $result = Set::extend($a, $b);
            expect($result)->toBe([
                'users' => [
                    'joe' => ['id' => 1, 'password' => 'may the force'],
                    'bob' => ['id' => 2, 'password' => 'be with you']
                ],
                'ice-cream'
            ]);

        });

        it("throws an exception with not enough parameters", function() {

            $closure = function() {
                Set::extend();
            };
            expect($closure)->toThrow(new BadFunctionCallException("Not enough parameters"));

            $closure = function() {
                Set::extend([]);
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

    describe("with some data", function() {

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

        describe("::flatten()", function() {

            it("flattens", function() {

                $result = Set::flatten($this->expanded);
                expect($result)->toBe($this->flattened);

            });

            it("supports the affix option", function() {

                $actual = Set::flatten(['some' => ['children' => ['very' => ['children' => ['deep' => ['children' => ['prop' => true ]]]]]]], [
                  'affix' => 'children'
                ]);
                $expected = ['some.very.deep.prop' => true];

                expect($expected)->toBe($actual);

            });

        });

        describe("::expand()", function() {

            it("expands", function() {

                $result = Set::expand($this->flattened);
                expect($result)->toBe($this->expanded);

            });

            it("supports the affix option", function() {

                $actual = Set::expand(['some.very.deep.prop' => true], ['affix' => 'children' ]);

                $expected = ['some' => ['children' => ['very' => ['children' => ['deep' => ['children' => ['prop' => true ]]]]]]];

                expect($expected)->toBe($actual);

            });
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
