<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License version 3.0
 * that is bundled with this package in the file LICENSE.md
 *
 * @author    Webkul IN
 * @copyright Since 2010 Webkul
 * @license   https://opensource.org/license/osl-3-0-php Open Software License version 3.0
 */

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;

class CustomerTest extends TestCase
{
    private $dbMock;

    protected function setUp(): void
    {
        parent::setUp();

        Configuration::set('PS_LANG_DEFAULT', 1);
        Configuration::set('PS_CUSTOMER_GROUP', 3);
        Configuration::set('PS_GUEST_GROUP', 2);
        Configuration::set('PS_UNIDENTIFIED_GROUP', 1);
        Configuration::set('PS_PASSWD_TIME_FRONT', 360);

        $this->dbMock = $this->createMock(Db::class);
        $this->dbMock->method('escape')->willReturnArgument(0);
        $ref = new ReflectionProperty(Db::class, 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $this->dbMock);

        $this->resetStaticCaches();
        Cache::resetAll();
    }

    protected function tearDown(): void
    {
        $ref = new ReflectionProperty(Db::class, 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, null);

        $ref = new ReflectionProperty(Context::class, 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, null);

        ObjectModel::$updateResult = true;
        Group::setFeatureActive(true);

        unset(Customer::$definition['fields']['phone']['required']);

        $this->resetStaticCaches();

        Configuration::resetAll();
        Cache::resetAll();

        parent::tearDown();
    }

    private function resetStaticCaches(): void
    {
        $ref = new ReflectionProperty(Customer::class, '_defaultGroupId');
        $ref->setAccessible(true);
        $ref->setValue(null, []);

        $ref = new ReflectionProperty(Customer::class, '_customerHasAddress');
        $ref->setAccessible(true);
        $ref->setValue(null, []);

        $ref = new ReflectionProperty(Customer::class, '_customer_groups');
        $ref->setAccessible(true);
        $ref->setValue(null, []);
    }

    private function setUpContext(): void
    {
        $ctx = new Context();
        $ctx->shop = new class {
            public int $id = 1;
            public int $id_shop_group = 1;
            public function getGroup() {
                return new class { public bool $share_order = false; };
            }
        };
        $ctx->language = new class { public int $id = 1; };
        $ctx->controller = null;
        $ctx->cart = null;

        $ref = new ReflectionProperty(Context::class, 'instance');
        $ref->setAccessible(true);
        $ref->setValue(null, $ctx);
    }

    // ── $definition ────────────────────────────────────────────────────────────

    public function testDefinitionTableIsCustomer(): void
    {
        $this->assertSame('customer', Customer::$definition['table']);
    }

    public function testDefinitionPrimaryIsIdCustomer(): void
    {
        $this->assertSame('id_customer', Customer::$definition['primary']);
    }

    public function testDefinitionHasRequiredFields(): void
    {
        $fields = Customer::$definition['fields'];
        $this->assertTrue($fields['lastname']['required']);
        $this->assertTrue($fields['firstname']['required']);
        $this->assertTrue($fields['email']['required']);
        $this->assertTrue($fields['passwd']['required']);
    }

    public function testDefinitionFieldTypesAreValid(): void
    {
        $validTypes = [
            ObjectModel::TYPE_INT, ObjectModel::TYPE_BOOL, ObjectModel::TYPE_STRING,
            ObjectModel::TYPE_FLOAT, ObjectModel::TYPE_DATE, ObjectModel::TYPE_HTML,
            ObjectModel::TYPE_NOTHING,
        ];
        foreach (Customer::$definition['fields'] as $field => $spec) {
            $this->assertContains($spec['type'], $validTypes, "Field '$field' has invalid type");
        }
    }

    // ── Constructor ────────────────────────────────────────────────────────────

    public function testConstructorSetsDefaultGroupFromConfiguration(): void
    {
        Configuration::set('PS_CUSTOMER_GROUP', 5);
        $customer = new Customer();
        $this->assertSame(5, $customer->id_default_group);
    }

    public function testConstructorWithNoIdHasNullId(): void
    {
        $customer = new Customer();
        $this->assertNull($customer->id);
    }

    public function testConstructorWithIdRetainsId(): void
    {
        $customer = new Customer(42);
        $this->assertSame(42, $customer->id);
    }

    public function testConstructorSetsPhoneRequiredWhenConfigEnabled(): void
    {
        Configuration::set('PS_ONE_PHONE_AT_LEAST', true);
        new Customer();
        $this->assertTrue(Customer::$definition['fields']['phone']['required']);
    }

    public function testConstructorDoesNotSetPhoneRequiredWhenConfigDisabled(): void
    {
        Configuration::set('PS_ONE_PHONE_AT_LEAST', false);
        new Customer();
        $this->assertArrayNotHasKey('required', Customer::$definition['fields']['phone']);
    }

    // ── validateFields ──────────────────────────────────────────────────────────

    public function testValidateFieldsReturnsTrueWhenWebserviceValidationNotSet(): void
    {
        $customer = new Customer();
        $this->assertTrue($customer->validateFields(false, false));
    }

    public function testValidateFieldsReturnsFalseWhenPhoneMissingAndRequired(): void
    {
        Configuration::set('PS_ONE_PHONE_AT_LEAST', true);
        $customer = new Customer();
        $customer->webservice_validation = true;
        $customer->phone = '';

        $this->assertFalse($customer->validateFields(false, false));
    }

    public function testValidateFieldsReturnsFalseWhenPhoneFormatIsInvalid(): void
    {
        Configuration::set('PS_ONE_PHONE_AT_LEAST', true);
        $customer = new Customer();
        $customer->webservice_validation = true;
        $customer->phone = 'not-a-phone';

        $this->assertFalse($customer->validateFields(false, false));
    }

    public function testValidateFieldsThrowsWhenDieIsTrueAndPhoneMissing(): void
    {
        Configuration::set('PS_ONE_PHONE_AT_LEAST', true);
        $customer = new Customer();
        $customer->webservice_validation = true;
        $customer->phone = '';

        $this->expectException(PrestaShopException::class);
        $customer->validateFields(true, false);
    }

    public function testValidateFieldsReturnsErrorMessageWhenErrorReturnTrueAndPhoneMissing(): void
    {
        Configuration::set('PS_ONE_PHONE_AT_LEAST', true);
        $customer = new Customer();
        $customer->webservice_validation = true;
        $customer->phone = '';

        $result = $customer->validateFields(false, true);
        $this->assertNotEmpty($result);
        $this->assertIsString($result);
    }

    // ── add() ───────────────────────────────────────────────────────────────────

    public function testAddReturnsFalseWhenGuestCheckoutDisabled(): void
    {
        $this->setUpContext();
        Configuration::set('PS_GUEST_CHECKOUT_ENABLED', false);

        $customer = new Customer();
        $customer->is_guest = 1;

        $this->assertFalse($customer->add());
    }

    public function testAddSetsShopIdFromContextWhenNotSet(): void
    {
        $this->setUpContext();
        Configuration::set('PS_GUEST_CHECKOUT_ENABLED', true);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('Insert_ID')->willReturn(1);
        $this->dbMock->method('delete')->willReturn(true);

        $customer = new Customer();
        $customer->is_guest = 0;
        $customer->add();

        $this->assertSame(1, $customer->id_shop);
    }

    public function testAddSetsGuestGroupWhenCustomerIsGuest(): void
    {
        $this->setUpContext();
        Configuration::set('PS_GUEST_CHECKOUT_ENABLED', true);
        Configuration::set('PS_GUEST_GROUP', 2);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('Insert_ID')->willReturn(1);
        $this->dbMock->method('delete')->willReturn(true);

        $customer = new Customer();
        $customer->is_guest = 1;
        $customer->add();

        $this->assertSame(2, $customer->id_default_group);
    }

    public function testAddSetsBirthdayFromYearsMonthsDaysWhenYearsNotEmpty(): void
    {
        $this->setUpContext();
        Configuration::set('PS_GUEST_CHECKOUT_ENABLED', true);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('Insert_ID')->willReturn(1);
        $this->dbMock->method('delete')->willReturn(true);

        $customer = new Customer();
        $customer->is_guest = 0;
        $customer->years  = 1990;
        $customer->months = 6;
        $customer->days   = 15;
        $customer->add();

        $this->assertSame('1990-6-15', $customer->birthday);
    }

    public function testAddSetsSecureKeyAs32CharHexString(): void
    {
        $this->setUpContext();
        Configuration::set('PS_GUEST_CHECKOUT_ENABLED', true);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('Insert_ID')->willReturn(1);
        $this->dbMock->method('delete')->willReturn(true);

        $customer = new Customer();
        $customer->is_guest = 0;
        $customer->add();

        $this->assertNotEmpty($customer->secure_key);
        $this->assertSame(32, strlen($customer->secure_key));
    }

    public function testAddSetsNewsletterDateAddWhenNewsletterTrueAndDateInvalid(): void
    {
        $this->setUpContext();
        Configuration::set('PS_GUEST_CHECKOUT_ENABLED', true);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('Insert_ID')->willReturn(1);
        $this->dbMock->method('delete')->willReturn(true);

        $customer = new Customer();
        $customer->is_guest = 0;
        $customer->newsletter = true;
        $customer->newsletter_date_add = '';
        $customer->add();

        $this->assertNotEmpty($customer->newsletter_date_add);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}/', $customer->newsletter_date_add);
    }

    // ── isGuest / isLogged ─────────────────────────────────────────────────────

    public function testIsGuestReturnsTrueWhenIsGuestIsOne(): void
    {
        $customer = new Customer();
        $customer->is_guest = 1;
        $this->assertTrue($customer->isGuest());
    }

    public function testIsGuestReturnsFalseWhenIsGuestIsZero(): void
    {
        $customer = new Customer();
        $customer->is_guest = 0;
        $this->assertFalse($customer->isGuest());
    }

    public function testIsLoggedReturnsFalseWhenGuestAndWithGuestFalse(): void
    {
        $customer = new Customer();
        $customer->is_guest = 1;
        $customer->logged   = 1;
        $this->assertFalse($customer->isLogged(false));
    }

    public function testIsLoggedReturnsFalseWhenNotLogged(): void
    {
        $customer = new Customer();
        $customer->logged   = 0;
        $customer->is_guest = 0;
        $this->assertFalse($customer->isLogged());
    }

    public function testIsLoggedReturnsTrueWhenPasswordMatchesViaCache(): void
    {
        $customerId = 7;
        $passwd = Tools::encrypt('mypassword');
        Cache::store('Customer::checkPassword' . $customerId . '-' . $passwd, true);

        $customer = new Customer();
        $customer->logged   = 1;
        $customer->id       = $customerId;
        $customer->passwd   = $passwd;
        $customer->is_guest = 0;

        $this->assertTrue($customer->isLogged());
    }

    public function testIsLoggedReturnsFalseWhenPasswordDoesNotMatch(): void
    {
        $customerId = 8;
        $passwd = Tools::encrypt('wrongpassword');
        Cache::store('Customer::checkPassword' . $customerId . '-' . $passwd, false);

        $customer = new Customer();
        $customer->logged   = 1;
        $customer->id       = $customerId;
        $customer->passwd   = $passwd;
        $customer->is_guest = 0;

        $this->assertFalse($customer->isLogged());
    }

    public function testIsLoggedReturnsTrueForGuestWhenWithGuestTrue(): void
    {
        $customerId = 5;
        $passwd = Tools::encrypt('secret');
        Cache::store('Customer::checkPassword' . $customerId . '-' . $passwd, true);

        $customer = new Customer();
        $customer->is_guest = 1;
        $customer->logged   = 1;
        $customer->id       = $customerId;
        $customer->passwd   = $passwd;

        $this->assertTrue($customer->isLogged(true));
    }

    // ── transformToCustomer ────────────────────────────────────────────────────

    public function testTransformToCustomerReturnsFalseWhenNotGuest(): void
    {
        $customer = new Customer();
        $customer->is_guest = 0;

        $this->assertFalse($customer->transformToCustomer(1, 'ValidPass1'));
    }

    public function testTransformToCustomerReturnsFalseWhenPasswordInvalid(): void
    {
        $customer = new Customer();
        $customer->is_guest = 1;

        $this->assertFalse($customer->transformToCustomer(1, 'x'));
    }

    public function testTransformToCustomerSetsIsGuestToZero(): void
    {
        $this->dbMock->method('delete')->willReturn(true);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('executeS')->willReturn([]);

        $customer = new Customer();
        $customer->is_guest = 1;
        $customer->id = 5;
        $customer->transformToCustomer(1, 'ValidPass1');

        $this->assertSame(0, $customer->is_guest);
    }

    public function testTransformToCustomerSetsEncryptedPassword(): void
    {
        $this->dbMock->method('delete')->willReturn(true);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('executeS')->willReturn([]);

        $customer = new Customer();
        $customer->is_guest = 1;
        $customer->id = 5;
        $customer->transformToCustomer(1, 'newpass12');

        $this->assertSame(Tools::encrypt('newpass12'), $customer->passwd);
    }

    public function testTransformToCustomerReturnsTrueOnSuccess(): void
    {
        $this->dbMock->method('delete')->willReturn(true);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('executeS')->willReturn([]);

        $customer = new Customer();
        $customer->is_guest  = 1;
        $customer->id        = 5;
        $customer->firstname = 'John';
        $customer->lastname  = 'Doe';
        $customer->email     = 'john@example.com';

        $this->assertTrue($customer->transformToCustomer(1, 'ValidPass1'));
    }

    public function testTransformToCustomerReturnsFalseWhenUpdateFails(): void
    {
        $this->dbMock->method('delete')->willReturn(true);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('executeS')->willReturn([]);
        ObjectModel::$updateResult = false;

        $customer = new Customer();
        $customer->is_guest = 1;
        $customer->id = 10;

        $this->assertFalse($customer->transformToCustomer(1, 'ValidPass1'));
    }

    public function testTransformToCustomerGeneratesPasswordWhenNoneGiven(): void
    {
        $this->dbMock->method('delete')->willReturn(true);
        $this->dbMock->method('insert')->willReturn(true);
        $this->dbMock->method('executeS')->willReturn([]);

        $customer = new Customer();
        $customer->is_guest = 1;
        $customer->id = 5;
        $customer->transformToCustomer(1, null);

        $this->assertNotEmpty($customer->passwd);
    }

    // ── setWsPasswd ────────────────────────────────────────────────────────────

    public function testSetWsPasswdEncryptsPasswordWhenIdIsZero(): void
    {
        $customer = new Customer();
        $customer->id     = 0;
        $customer->passwd = '';
        $customer->setWsPasswd('mypassword');

        $this->assertSame(Tools::encrypt('mypassword'), $customer->passwd);
    }

    public function testSetWsPasswdEncryptsPasswordWhenDifferentFromCurrent(): void
    {
        $customer = new Customer();
        $customer->id     = 5;
        $customer->passwd = Tools::encrypt('oldpass');
        $customer->setWsPasswd('newpass');

        $this->assertSame(Tools::encrypt('newpass'), $customer->passwd);
    }

    public function testSetWsPasswdDoesNotChangePasswordWhenSameAsStored(): void
    {
        $existing = Tools::encrypt('samepass');
        $customer = new Customer();
        $customer->id     = 5;
        $customer->passwd = $existing;
        $customer->setWsPasswd($existing);

        $this->assertSame($existing, $customer->passwd);
    }

    public function testSetWsPasswdReturnsTrue(): void
    {
        $customer = new Customer();
        $customer->id     = 0;
        $customer->passwd = '';

        $this->assertTrue($customer->setWsPasswd('anypassword'));
    }

    // ── customerExists ─────────────────────────────────────────────────────────

    public function testCustomerExistsReturnsFalseForInvalidEmail(): void
    {
        $this->assertFalse(Customer::customerExists('not-an-email'));
    }

    public function testCustomerExistsReturnsFalseWhenNoRecord(): void
    {
        $this->dbMock->method('getValue')->willReturn(false);
        $this->assertFalse(Customer::customerExists('user@example.com'));
    }

    public function testCustomerExistsReturnsTrueWhenRecordFound(): void
    {
        $this->dbMock->method('getValue')->willReturn('5');
        $this->assertTrue(Customer::customerExists('user@example.com'));
    }

    public function testCustomerExistsReturnsIdWhenReturnIdIsTrue(): void
    {
        $this->dbMock->method('getValue')->willReturn('5');
        $this->assertSame(5, Customer::customerExists('user@example.com', true));
    }

    public function testCustomerExistsQueriesDbWithEmailAndGuestFilter(): void
    {
        $this->dbMock
            ->expects($this->once())
            ->method('getValue')
            ->with($this->logicalAnd(
                $this->stringContains('user@example.com'),
                $this->stringContains('is_guest')
            ))
            ->willReturn(false);

        Customer::customerExists('user@example.com', false, true);
    }

    // ── customerHasAddress ────────────────────────────────────────────────────

    public function testCustomerHasAddressReturnsTrueWhenRecordFound(): void
    {
        $this->dbMock->method('getValue')->willReturn('3');
        $this->assertTrue(Customer::customerHasAddress(1, 3));
    }

    public function testCustomerHasAddressReturnsFalseWhenNoRecord(): void
    {
        $this->dbMock->method('getValue')->willReturn(false);
        $this->assertFalse(Customer::customerHasAddress(1, 99));
    }

    public function testCustomerHasAddressCachesResultOnSecondCall(): void
    {
        $this->dbMock->expects($this->once())->method('getValue')->willReturn('3');

        Customer::customerHasAddress(1, 3);
        Customer::customerHasAddress(1, 3); // served from cache
    }

    // ── resetAddressCache ──────────────────────────────────────────────────────

    #[DoesNotPerformAssertions]
    public function testResetAddressCacheOnMissingKeyDoesNotThrow(): void
    {
        Customer::resetAddressCache(999, 999);
    }

    public function testResetAddressCacheForcesDbCallOnNextLookup(): void
    {
        $this->dbMock->method('getValue')->willReturn('3', false);

        Customer::customerHasAddress(1, 3); // stores in cache
        Customer::resetAddressCache(1, 3);  // clears it
        $result = Customer::customerHasAddress(1, 3); // hits DB again — second call returns false

        $this->assertFalse($result);
    }

    // ── checkPassword ──────────────────────────────────────────────────────────

    public function testCheckPasswordReturnsTrueWhenPasswordMatches(): void
    {
        $passwd = Tools::encrypt('secret');
        $this->dbMock->method('getValue')->willReturn('5');
        $this->assertTrue(Customer::checkPassword(5, $passwd));
    }

    public function testCheckPasswordReturnsFalseWhenNoRecord(): void
    {
        $passwd = Tools::encrypt('wrong');
        $this->dbMock->method('getValue')->willReturn(false);
        $this->assertFalse(Customer::checkPassword(5, $passwd));
    }

    public function testCheckPasswordUsesCacheOnSecondCall(): void
    {
        $passwd = Tools::encrypt('mysecret');
        $this->dbMock->expects($this->once())->method('getValue')->willReturn('5');

        Customer::checkPassword(5, $passwd);
        Customer::checkPassword(5, $passwd);
    }

    // ── isBanned ──────────────────────────────────────────────────────────────

    public function testIsBannedReturnsTrueForInvalidId(): void
    {
        $this->assertTrue(Customer::isBanned(0));
    }

    public function testIsBannedReturnsTrueWhenCustomerNotActiveOrDeleted(): void
    {
        $this->dbMock->method('getRow')->willReturn(false);
        $this->assertTrue(Customer::isBanned(1));
    }

    public function testIsBannedReturnsFalseWhenCustomerActive(): void
    {
        $this->dbMock->method('getRow')->willReturn(['id_customer' => 1]);
        $this->assertFalse(Customer::isBanned(1));
    }

    public function testIsBannedUsesCacheOnSecondCall(): void
    {
        $this->dbMock->expects($this->once())->method('getRow')->willReturn(['id_customer' => 1]);
        Customer::isBanned(1);
        Customer::isBanned(1);
    }

    // ── getCustomers ───────────────────────────────────────────────────────────

    public function testGetCustomersReturnsArrayFromDb(): void
    {
        $this->dbMock->method('executeS')->willReturn([
            ['id_customer' => 1, 'email' => 'a@b.com'],
        ]);
        $result = Customer::getCustomers();
        $this->assertCount(1, $result);
    }

    public function testGetCustomersWithActiveFilterIncludesActiveInQuery(): void
    {
        $this->dbMock
            ->expects($this->once())
            ->method('executeS')
            ->with($this->logicalAnd(
                $this->stringContains('active'),
                $this->stringContains('customer')
            ))
            ->willReturn([]);

        Customer::getCustomers(true);
    }

    public function testGetCustomersWithDeletedFilterIncludesDeletedInQuery(): void
    {
        $this->dbMock
            ->expects($this->once())
            ->method('executeS')
            ->with($this->logicalAnd(
                $this->stringContains('deleted'),
                $this->stringContains('= 0')
            ))
            ->willReturn([]);

        Customer::getCustomers(null, false);
    }

    public function testGetCustomersWithHavingAddressJoinsAndFiltersIsNotNull(): void
    {
        $this->dbMock
            ->expects($this->once())
            ->method('executeS')
            ->with($this->logicalAnd(
                $this->stringContains('address'),
                $this->stringContains('IS NOT NULL')
            ))
            ->willReturn([]);

        Customer::getCustomers(null, null, true);
    }

    public function testGetCustomersWithoutAddressFiltersIsNull(): void
    {
        $this->dbMock
            ->expects($this->once())
            ->method('executeS')
            ->with($this->stringContains('IS NULL'))
            ->willReturn([]);

        Customer::getCustomers(null, null, false);
    }

    // ── searchByName ───────────────────────────────────────────────────────────

    public function testSearchByNameReturnsMatchingCustomers(): void
    {
        $this->dbMock->method('executeS')->willReturn([['id_customer' => 1]]);
        $result = Customer::searchByName('john');
        $this->assertIsArray($result);
    }

    public function testSearchByNameSkipDeletedAddsDeletedFilter(): void
    {
        $this->dbMock
            ->expects($this->once())
            ->method('executeS')
            ->with($this->logicalAnd(
                $this->stringContains('deleted'),
                $this->stringContains('0')
            ))
            ->willReturn([]);

        Customer::searchByName('john', null, true);
    }

    public function testSearchByNameWithLimitAppendsLimitClause(): void
    {
        $this->dbMock
            ->expects($this->once())
            ->method('executeS')
            ->with($this->logicalAnd(
                $this->stringContains('LIMIT'),
                $this->stringContains('10')
            ))
            ->willReturn([]);

        Customer::searchByName('john', 10);
    }

    public function testSearchByNameWithoutLimitHasNoLimitClause(): void
    {
        $this->dbMock
            ->expects($this->once())
            ->method('executeS')
            ->with($this->logicalNot($this->stringContains('LIMIT')))
            ->willReturn([]);

        Customer::searchByName('john');
    }

    // ── getGroupsStatic ────────────────────────────────────────────────────────

    public function testGetGroupsStaticReturnsCustomerGroupWhenGroupFeatureInactive(): void
    {
        Group::setFeatureActive(false);
        Configuration::set('PS_CUSTOMER_GROUP', 3);

        $this->assertSame([3], Customer::getGroupsStatic(1));
    }

    public function testGetGroupsStaticReturnsUnidentifiedGroupForIdZero(): void
    {
        Group::setFeatureActive(true);
        Configuration::set('PS_UNIDENTIFIED_GROUP', 1);

        $this->assertSame([1], Customer::getGroupsStatic(0));
    }

    public function testGetGroupsStaticReturnsGroupsFromDb(): void
    {
        Group::setFeatureActive(true);
        $this->dbMock->method('executeS')->willReturn([
            ['id_group' => 3],
            ['id_group' => 5],
        ]);

        $this->assertSame([3, 5], Customer::getGroupsStatic(1));
    }

    public function testGetGroupsStaticCachesResultOnSecondCall(): void
    {
        Group::setFeatureActive(true);
        $this->dbMock->expects($this->once())->method('executeS')->willReturn([['id_group' => 3]]);

        Customer::getGroupsStatic(1);
        Customer::getGroupsStatic(1);
    }

    // ── getGroups ──────────────────────────────────────────────────────────────

    public function testGetGroupsDelegatesToGetGroupsStatic(): void
    {
        Group::setFeatureActive(true);
        $this->dbMock->method('executeS')->willReturn([['id_group' => 3]]);

        $customer = new Customer();
        $customer->id = 1;

        $this->assertSame([3], $customer->getGroups());
    }

    // ── updateGroup / cleanGroups / addGroups ──────────────────────────────────

    public function testUpdateGroupWithNonEmptyListCleansAndAdds(): void
    {
        $this->dbMock->expects($this->once())->method('delete')->willReturn(true);
        $this->dbMock->expects($this->once())->method('insert')->willReturn(true);

        $customer = new Customer();
        $customer->id = 5;
        $customer->updateGroup([3]);
    }

    public function testUpdateGroupWithEmptyListAddsDefaultGroup(): void
    {
        // When the list is empty, updateGroup skips cleanGroups() and calls
        // addGroups([$id_default_group]) directly — so delete is NOT called.
        $this->dbMock->expects($this->never())->method('delete');
        $this->dbMock->expects($this->once())->method('insert')->willReturn(true);

        $customer = new Customer();
        $customer->id = 5;
        $customer->updateGroup([]);
    }

    public function testCleanGroupsCallsDbDelete(): void
    {
        $this->dbMock
            ->expects($this->once())
            ->method('delete')
            ->with('customer_group', $this->stringContains('5'))
            ->willReturn(true);

        $customer = new Customer();
        $customer->id = 5;
        $customer->cleanGroups();
    }

    public function testAddGroupsInsertsOneRowPerGroup(): void
    {
        $this->dbMock->expects($this->exactly(2))->method('insert')->willReturn(true);

        $customer = new Customer();
        $customer->id = 5;
        $customer->addGroups([3, 7]);
    }

    // ── getDefaultGroupId ──────────────────────────────────────────────────────

    public function testGetDefaultGroupIdReturnsCustomerGroupWhenGroupFeatureInactive(): void
    {
        Group::setFeatureActive(false);
        Configuration::set('PS_CUSTOMER_GROUP', 3);

        $this->assertSame(3, (int)Customer::getDefaultGroupId(1));
    }

    public function testGetDefaultGroupIdQueriesDbWhenGroupFeatureActive(): void
    {
        Group::setFeatureActive(true);
        $this->dbMock->method('getValue')->willReturn('5');

        $this->assertSame('5', Customer::getDefaultGroupId(1));
    }

    public function testGetDefaultGroupIdCachesResultOnSecondCall(): void
    {
        Group::setFeatureActive(true);
        $this->dbMock->expects($this->once())->method('getValue')->willReturn('5');

        Customer::getDefaultGroupId(1);
        Customer::getDefaultGroupId(1);
    }

    // ── customerIdExistsStatic ─────────────────────────────────────────────────

    public function testCustomerIdExistsStaticReturnsTrueWhenFound(): void
    {
        $this->dbMock->method('getValue')->willReturn('5');
        $this->assertSame(5, (int)Customer::customerIdExistsStatic(5));
    }

    public function testCustomerIdExistsStaticReturnsZeroWhenNotFound(): void
    {
        $this->dbMock->method('getValue')->willReturn(false);
        $this->assertSame(0, (int)Customer::customerIdExistsStatic(99));
    }

    public function testCustomerIdExistsStaticUsesCacheOnSecondCall(): void
    {
        $this->dbMock->expects($this->once())->method('getValue')->willReturn('5');
        Customer::customerIdExistsStatic(5);
        Customer::customerIdExistsStatic(5);
    }

    // ── getAddressesTotalById ──────────────────────────────────────────────────

    public function testGetAddressesTotalByIdReturnsCountFromDb(): void
    {
        $this->dbMock->method('getValue')->willReturn('3');
        $this->assertSame('3', Customer::getAddressesTotalById(1));
    }

    public function testGetAddressesTotalByIdReturnsZeroWhenNoAddresses(): void
    {
        $this->dbMock->method('getValue')->willReturn('0');
        $this->assertSame('0', Customer::getAddressesTotalById(1));
    }

    // ── getByEmail ─────────────────────────────────────────────────────────────

    public function testGetByEmailReturnsFalseWhenNoRecord(): void
    {
        $this->dbMock->method('getRow')->willReturn(false);
        $customer = new Customer();
        $this->assertFalse($customer->getByEmail('user@example.com'));
    }

    public function testGetByEmailPopulatesCustomerPropertiesFromRow(): void
    {
        $this->dbMock->method('getRow')->willReturn([
            'id_customer' => 5,
            'email'       => 'user@example.com',
            'firstname'   => 'John',
            'lastname'    => 'Doe',
        ]);
        $customer = new Customer();
        $result   = $customer->getByEmail('user@example.com');

        $this->assertSame(5, $result->id);
        $this->assertSame('user@example.com', $result->email);
        $this->assertSame('John', $result->firstname);
    }

    // ── isUsed (deprecated) ────────────────────────────────────────────────────

    public function testIsUsedReturnsFalse(): void
    {
        $customer = new Customer();
        $this->assertFalse($customer->isUsed());
    }

    // ── getLastCart ────────────────────────────────────────────────────────────

    public function testGetLastCartReturnsFalseWhenNoCartsExist(): void
    {
        $customer = new Customer();
        $customer->id = 1;
        $this->assertFalse($customer->getLastCart());
    }

    // ── getLastEmails / getLastConnections ────────────────────────────────────

    public function testGetLastEmailsReturnsEmptyArrayWhenNoId(): void
    {
        $customer = new Customer();
        $customer->id = 0;
        $this->assertSame([], $customer->getLastEmails());
    }

    public function testGetLastEmailsReturnsResultsFromDb(): void
    {
        $this->dbMock->method('executeS')->willReturn([['id_mail' => 1, 'subject' => 'Welcome']]);
        $customer = new Customer();
        $customer->id    = 1;
        $customer->email = 'user@example.com';
        $this->assertCount(1, $customer->getLastEmails());
    }

    public function testGetLastConnectionsReturnsEmptyArrayWhenNoId(): void
    {
        $customer = new Customer();
        $customer->id = 0;
        $this->assertSame([], $customer->getLastConnections());
    }

    public function testGetLastConnectionsReturnsResultsFromDb(): void
    {
        $this->dbMock->method('executeS')->willReturn([['id_connections' => 1]]);
        $customer = new Customer();
        $customer->id = 1;
        $this->assertCount(1, $customer->getLastConnections());
    }

    // ── getBoughtProducts ──────────────────────────────────────────────────────

    public function testGetBoughtProductsReturnsResultsFromDb(): void
    {
        $this->dbMock->method('executeS')->willReturn([['id_order' => 1]]);
        $customer = new Customer();
        $customer->id = 1;
        $this->assertCount(1, $customer->getBoughtProducts());
    }

    public function testGetBoughtProductsReturnsEmptyWhenNone(): void
    {
        $this->dbMock->method('executeS')->willReturn([]);
        $customer = new Customer();
        $customer->id = 1;
        $this->assertSame([], $customer->getBoughtProducts());
    }

    // ── searchByIp ─────────────────────────────────────────────────────────────

    public function testSearchByIpReturnsMatchingCustomers(): void
    {
        $this->dbMock->method('executeS')->willReturn([['id_customer' => 1]]);
        $this->assertCount(1, Customer::searchByIp('192.168.1.1'));
    }

    public function testSearchByIpReturnsEmptyArrayWhenNotFound(): void
    {
        $this->dbMock->method('executeS')->willReturn([]);
        $this->assertSame([], Customer::searchByIp('10.0.0.1'));
    }

    // ── getWsGroups / setWsGroups ──────────────────────────────────────────────

    public function testGetWsGroupsReturnsGroupsFromDb(): void
    {
        $this->dbMock->method('executeS')->willReturn([['id' => 3], ['id' => 5]]);
        $customer = new Customer();
        $customer->id = 1;
        $this->assertCount(2, $customer->getWsGroups());
    }

    public function testSetWsGroupsCleansAndInsertsNewGroups(): void
    {
        $this->dbMock->expects($this->once())->method('delete')->willReturn(true);
        $this->dbMock->expects($this->exactly(2))->method('insert')->willReturn(true);

        $customer = new Customer();
        $customer->id = 5;
        $result = $customer->setWsGroups([['id' => 3], ['id' => 5]]);

        $this->assertTrue($result);
    }

    // ── delete() ──────────────────────────────────────────────────────────────

    public function testDeleteReturnsTrueAndRunsCleanupQueries(): void
    {
        $this->setUpContext();
        $this->dbMock->method('executeS')->willReturn([]);
        $this->dbMock->method('execute')->willReturn(true);
        $this->dbMock->method('delete')->willReturn(true);

        $customer = new Customer();
        $customer->id = 5;

        $this->assertTrue($customer->delete());
    }

    public function testDeleteCallsExecuteToRemoveCustomerGroups(): void
    {
        $this->setUpContext();
        $this->dbMock->method('executeS')->willReturn([]);
        $this->dbMock->method('delete')->willReturn(true);

        $customerGroupDeleteCalled = false;
        $this->dbMock
            ->method('execute')
            ->willReturnCallback(function (string $sql) use (&$customerGroupDeleteCalled): bool {
                if (strpos($sql, 'customer_group') !== false) {
                    $customerGroupDeleteCalled = true;
                }
                return true;
            });

        $customer = new Customer();
        $customer->id = 5;
        $customer->delete();

        $this->assertTrue($customerGroupDeleteCalled, 'Expected execute() to be called with a query containing "customer_group"');
    }

    // ── getCustomersByEmail ────────────────────────────────────────────────────

    public function testGetCustomersByEmailReturnsMatchingCustomers(): void
    {
        $this->dbMock->method('executeS')->willReturn([['id_customer' => 1, 'email' => 'user@example.com']]);
        $result = Customer::getCustomersByEmail('user@example.com');
        $this->assertIsArray($result);
    }
}
