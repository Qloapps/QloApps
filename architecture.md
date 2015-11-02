# Making PrestaShop Unit Testable
*An adventure in dependency breaking - guidelines and proposals*

We have tasked ourselves with an interesting challenge:
to make one of the core business functions in PrestaShop unit testable. The function we chose, `Cart::getOrderTotal`, performs price computations in the shopping cart - it's the one that tells your customers how much they're going to pay. That's kind of useful.

We chose to focus on this function because it is large and complex enough to demonstrate a number of different techniques.

This post shows our progress and leads to a proposal for a new software architecture for PrestaShop. The new architecture is intended to be lightweight and safe.

## Where we Started From
*The issues with `Cart::getOrderTotal`*

`Cart::getOrderTotal` is a ~300 lines long instance method. It makes it hard to reason about.

You cannot instantiate a `Cart` object to test the `getOrderTotal` method because the constructor indirectly accesses the database.

To make things even more interesting, the method makes static calls to methods on at least three different classes: `Configuration`, `Address`, `Product`.

A large number of execution paths are conditioned on the value of different global variables that `getOrderTotal` accesses implicitly.

## Our Goal

We set out to write a **meaningful** unit test on `Cart::getOrderTotal`.

Before we dive into the changes we had to make, here is a preview of the end result - one of the unit tests we were able to write:

```php
public function test_getOrderTotal_Round_Line_When_No_Tax()
{
    $this->setRoundType(Order::ROUND_LINE);

    // Add 3 products each with (pre-tax) price 10.125
    $this->productPriceCalculator->addFakeProduct(new FakeProduct(3, 10.125));
    // Add 1 product with (pre-tax) price 10.125
    $this->productPriceCalculator->addFakeProduct(new FakeProduct(1, 10.125));

    $orderTotal = $this->cart->getOrderTotal(false, Cart::ONLY_PRODUCTS, $this->productPriceCalculator->getProducts());
    $this->assertEquals(40.51, $orderTotal);
}
```
This test simulates adding 4 products to the cart and checks that `getOrderTotal` produces the expected total before tax.

The test does not make any access to the database.

## The Techniques we Used and When to Use Them

In this chapter we try to put a name on different techniques we have used and extract guidelines about the cases in which we believe they are appropriate.

### Application Singleton Injection
This technique is one of the least intrusive. The problem it tries to solve is: there is a static access to an application singleton deeply nested in some code that we do not care about for the purpose of our test.

In our case the `Db` class was accessed indirectly upon instantiation of a `Cart` object though a sequence of calls leading to a static access to `Group` that performs actions that have nothing to do with the computation of the total of the Cart.

We could have gone deep into the call stack and broken the dependency to the database there, directly in `Group`.

But it might have been dangerous: we would have had to change code that we did not intend to test.

So to keep things simple and not lose focus on `getOrderTotal` we just mocked the whole `Db` instance, injecting it into the application singleton with an ad-hoc static method that was added to `Db`:

```php
public function setupDatabaseMock()
{
    $this->database = Phake::mock('Db');
    Db::setInstanceForTesting($this->database);
}
```

And `setInstanceForTesting` is straightforward:
```php
class Db
{
    /*
        ...
     */

    /**
     * @param $test_db Db
     * Unit testing purpose only
     */
    public static function setInstanceForTesting($test_db)
    {
        self::$instance[0] = $test_db;
    }

    /*
        ...
     */
}
```

Note the *ForTesting* suffix: it warns developers not to use this in production code. This is only a test helper.

The exact same technique is used to inject a fake context into the `Context` application singleton.

The boilerplate code for this is factored out into the [`UnitTestCase::setup`](tests/TestCase/UnitTestCase.php) method so that the next tests are easier to write.

Great, now we can instantiate a `Cart` object (`new` it)!

### ServiceLocator Based Injection

This technique solves the same type of issues as the Application Singleton Injection does. It breaks dependencies. The primary difference is that we use it in the code that we are working on. Not deep into the dependency chain.

The goal is to make the previously hidden dependency very visible and to have full control over the component.

Ideally, when we discover a hidden dependency in the code under test, we should find a way to make the dependency trickle down from the top layers of the application right into the code under test.

This is not always possible (when no predictable execution path leads to the code under test for instance), so a temporary solution is is to use a ServiceLocator.

It looks like this:

```php
public function getOrderTotal($with_taxes = true, $type = Cart::BOTH, $products = null, $id_carrier = null, $use_cache = true)
{
    // Dependencies

    $address_factory    = Adapter_ServiceLocator::get('Adapter_AddressFactory');
    $price_calculator   = Adapter_ServiceLocator::get('Adapter_ProductPriceCalculator');
    $configuration      = Adapter_ServiceLocator::get('Core_Business_Configuration');

    // Code...
}
```

On the unit test side, setting up the mocked objects follow this pattern:

```php
$this->container = new Core_Foundation_IoC_Container;
Adapter_ServiceLocator::setServiceContainerInstance($this->container);

$addressFactory = Phake::mock('Adapter_AddressFactory');
$address = new Address;
$address->id = 1;

Phake::when($addressFactory)->findOrCreate()->thenReturn($address);

$this->container->bind('Adapter_AddressFactory', $addressFactory);
```

What's happening here is:

1. We create a Dependency Injection Container instance specifically for our tests
2. We tell the ServiceLocator to fetch dependencies from our container
3. We mock our dependency (`Adapter_AddressFactory` here)
4. We bind the mocked dependency to the ServiceContainer instance
5. In the code under test, the ServiceLocator only talks with our test container and pulls the dependencies we crafted from there

#### But what's this whole `Adapter_*` thing about?

The classes in the `Adapter_` namespace (in the `Adapter` directory) make a bridge between the legacy code (the thing we're trying to write tests on) and the cleaner code (the new things we're going to write, which will *have tests from the start*).

Code from the non-legacy part should only call legacy code through an adapter. Adapters should eventually disappear and be replaced with new, non-legacy code.

Code in an adapter might:
- wrap calls to code in the legacy part
- call new, non-legacy code

As a first step, an Adapter can just use the same code as the legacy part it replaces.

For instance our `Adapter_AddressFactory` class:

```php
<?php

class Adapter_AddressFactory
{
    public function findOrCreate($id_address = null, $with_geoloc = false)
    {
        return call_user_func_array(array('Address', 'initialize'), func_get_args());
    }

    public function addressExists($id_address)
    {
        return Address::addressExists($id_address);
    }
}
```

It just forwards the calls to existing legacy code.

We replaced a line in `Cart` from:

```php
$address = Address::initialize($id_address, true);
```

to:

```php
$address = $address_factory->findOrCreate($id_address, true);
```

But the same exact code as before is called, which is as safe as can be.

#### Next Steps

Now that our dependencies are safely exposed we have a solid starting point for further work:

if `Adapter_AddressFactory::findOrCreate` does strange things we're not comfortable with, and if we have strong unit tests on the code that uses the adapter, then we can start delegating work from `Adapter_AddressFactory` to non-legacy code instead of using the copy-pasted logic from the legacy code.

# A Proposal for a New Software Architecture

Code falls into one of the following 3 categories
```
PrestaShop/
    |-- Core/
    |    |-- Foundation/
    |    └-- Business/
    |-- Adapter/
    └-- *Legacy*/
```

Where `Legacy` is not itself a directory but represents any file or directory in the current file tree that is not in `Core` or `Adapter`.

The purpose of each directory and the interactions between code in the 3 categories are described in the sections below.

## Core

`Core` is for code **covered by unit tests**.

No new class may be added to the `Core` directory if it doesn't have unit tests.

**No global access** to variables (or even constants!) is allowed inside the `Core` directory. This also means, no `ServiceLocator` here.
Dependencies of below-top-level components should be injected by an application layer that is higher up in the layer hierarchy.

The handling of a typical HTTP request might look something like this:

- `index.php`
    - loads the system configuration...
    - creates a dependency injection container...
    - binds general purpose services like the database to the dependency injection container...
    - `new`s a `Dispatcher`, passing the container to it
- the `Dispatcher` creates the `Controller` it needs, passing the dependency injection container to it
- the `Controller`
    - gets the dependencies it needs from the dependency injection container...
    - delegates its work to services (services that it gets from the container or `new`s)...
    - sends the response!

As a general rule, in she schema above, the `Controller` is the last application layer which has access to the dependency injection container.

Inside the `Core` directory, `Business` is for business logic (e.g. how do I compute the tax for a product?), while `Foundation` is reserved for general purpose logic (e.g. dependency injection, database interaction). In theory it should be possible to extract any components in `Foundation` to their own composer packages so that other projects could use them.

**Code inside `Core` is not allowed to call code inside `Legacy`**.

## Adapter

`Adapter` is a layer between `Core` and `Legacy`.

Inside `Adapter`, code may call any code from either `Core`, `Adapter` or `Legacy`.

The `Adapter` file hierarchy should eventually go away, so developers are encouraged to use `Core` code as much as possible when writing adapters.

Adapters should be used when we want to break a dependency in the code under test and when breaking the dependency cleanly is too risky.

The life cycle of an adapter is roughly as follows:

- **Debugging / Refactoring Stage**
    1. Create `Adapter_SomeService` to break a static dependency to `SomeService` in the code you're working on
        - Adapter should be minimal at first - [it might just forward a call to some legacy function](https://github.com/djfm/PrestaShop/commit/42db57be45c299259a28a247db6c62267d3fb671). 
    2. Use `Adapter_ServiceLocator::get('Adapter_SomeService')` in the code under test to retrieve an instance of the service we depend on
    3. Write tests, refactor...
- **Architecture Improvement Stage**: With time and experience, the precise responsibilities of `Adapter_SomeService` have been clearly identified and tested
    1. Write a new `Core_SomeService` class inside the `Core` file hierarchy, test it thoroughly
    2. Delegate work from `Adapter_SomeService` to `Core_SomeService`, keeping the `Adapter_ServiceLocator::get('Adapter_SomeService')` calls in the code where proper dependency injection is still too hard to do. Propagate the `Core_SomeService` dependency in a better way where possible.
- **Adapter Removal**: At some point, if enough code has been cleaned up, the `Adapter_SomeService` might not be needed any more
    1. Remove all calls to `Adapter_ServiceLocator::get('Adapter_SomeService')` and replace them with proper dependency injection
    2. Delete `Adapter_SomeService`.
    3. Profit!

## Legacy

`Legacy` code is any code that is not inside `Core` or `Adapter`.

Our goal is to gradually move the code from `Legacy` towards `Core`.

As an intermediate step, code in `Legacy` is expected to make heavy use of the `Adapter_ServiceLocator` to break dependencies.
