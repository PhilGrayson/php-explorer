# PHP Explorer
A vim plugin for intelligently jumping into class and function declarations. PHP Explorer understands use statements, parameter typehinting and method calls.

## Usage
* `<Leader>gt` : *Go To* a class or function declaration under the cursor.

## Install
Assuming [Pathogen](https://github.com/tpope/vim-pathogen)
```
    cd ~/.vim/bundle
    git clone https://github.com/philgrayson/php-explorer.git
```
## Assumptions
* PHP 5.3 or greater is installed
* Contains a `vendor/autoload.php` file that registers the project's autoloader. See [Composer] (https://getcomposer.org/)
* The project is written in PHP 5.3 or greater


## TODO
* Fix *Go To* function calls working by accident. The same local variable defined in multiple methods confuses the resolve.
```
    class A {
      function A (A $a) {
          $a->method();
      }

      function B(B $a) {
          $a->method();
      }
    }
```

* Allow *Go To* to work over mutli-line expressions, for example, functionB() in;
```
    $this->field->functionA()
                ->functionB();
```

* Trigger a phpunit test under the cursor. Perhaps `<leader>rt` *Run Test*. What about data providers?
```
    public function testPatience()
    {
        $this->assertEqual(42, rand(0, 100));
    }
```

* Add Submlime Text bindings

* Handle functions from traits, recursing traits if necessary.
