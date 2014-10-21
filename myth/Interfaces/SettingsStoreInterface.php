<?php

namespace Myth\Interfaces;

/**
 * Provides the required structure for a Settings
 * provider that stores and saves settings used by the
 * application.
 *
 * Class SettingsInterface
 * @package Myth\Interfaces
 */
interface SettingsStoreInterface {

    /**
     * Inserts/Replaces a single setting item.
     *
     * @param $key
     * @param null $value
     * @param string $group
     */
    public function save($key, $value=null, $group='app');

    //--------------------------------------------------------------------

    /**
     * Retrieves a single item.
     *
     * @param $key
     * @param string $group
     * @return mixed
     */
    public function get($key, $group='app');

    //--------------------------------------------------------------------

    /**
     * Deletes a single item.
     *
     * @param $key
     * @param $group
     * @return mixed
     */
    public function delete($key, $group='app');

    //--------------------------------------------------------------------

    /**
     * Searches the store for any items with $field = $value.
     *
     * @param $field
     * @param $value
     * @return mixed
     */
    public function findBy($field, $value);

    //--------------------------------------------------------------------

    /**
     * Retrieves all items in the store either globally or for a single group.
     *
     * @param string $group
     * @return mixed
     */
    public function all($group=null);

    //--------------------------------------------------------------------

}