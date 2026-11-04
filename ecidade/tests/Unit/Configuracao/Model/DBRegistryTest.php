<?php

namespace Tests\Unit\Configuracao\Model;

use DBRegistry;
use Tests\TestCase;

class DBRegistryTest extends TestCase
{
    public function testShouldAddSingleArray()
    {
        $items = array(
            'item1' => 'item1',
            'item2' => 'item2',
            'item3' => 'item3'
        );

        DBRegistry::add('items', $items);
        $itemsFromRegistry = DBRegistry::get('items');
        self::assertNotNull($itemsFromRegistry);

        foreach ($items as $key => $item) {
            self::assertEquals($item, $itemsFromRegistry[$key]);
        }
    }

    public function testShouldVerifyIfRegistryHasItem()
    {
        DBRegistry::add('item1', 'item1');
        self::assertTrue(DBRegistry::has('item1'));
    }

    public function testShouldAddObject()
    {
        $object = (object)array(
            'prop1' => 'prop1'
        );

        DBRegistry::add('object', $object);
        $objectFromRegistry = DBRegistry::get('object');

        self::assertEquals($objectFromRegistry->prop1, 'prop1');
    }

    public function testShouldAddItemsToArray()
    {
        $item1 = (object)array(
            'item1Key' => 'item1Value'
        );

        $item2 = (object)array(
            'item2Key' => 'item2Value'
        );

        DBRegistry::addToArray('items', $item1);
        DBRegistry::addToArray('items', $item2);

        $itemsFromRegistry = DBRegistry::get('items');

        self::assertEquals($itemsFromRegistry[0], $item1);
        self::assertEquals($itemsFromRegistry[1], $item2);
    }

    public function testShouldAddItemsToArrayInSomeIndex()
    {
        $item1 = (object)array(
            'item1Key' => 'item1Value'
        );

        $item2 = (object)array(
            'item2Key' => 'item2Value'
        );

        DBRegistry::addToArray('items', $item1, 'x');
        DBRegistry::addToArray('items', $item2, 'y');

        $itemsFromRegistry = DBRegistry::get('items');

        self::assertEquals($itemsFromRegistry['x'], $item1);
        self::assertEquals($itemsFromRegistry['y'], $item2);
    }

    public function testShouldRemoveItem()
    {
        $item = (object)array(
            'item1' => 'item1'
        );

        DBRegistry::add('item', $item);
        self::assertNotNull(DBRegistry::get('item'));

        DBRegistry::remove('item');
        self::assertNull(DBRegistry::get('item'));
    }

    public function testShouldCountItemsInRegistry()
    {
        DBRegistry::add('item1', 'item1');
        DBRegistry::add('item2', 'item2');
        DBRegistry::add('item3', 'item3');

        self::assertEquals(3, DBRegistry::getTotalItens());
    }

    public function testShouldReturnAllItems()
    {
        DBRegistry::add('item1', 'item1');
        DBRegistry::add('item2', 'item2');
        DBRegistry::add('item3', 'item3');
        DBRegistry::add('item4', 'item4');

        $allItemsFromRegistry = DBRegistry::all();
        self::assertEquals(4, sizeof($allItemsFromRegistry));
    }

    public function testShouldReturnNullWhenIndexNotExists()
    {
        $item = DBRegistry::get('NotFoundItem');
        self::assertNull($item);
        self::assertEmpty($item);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        DBRegistry::clean();
    }
}














