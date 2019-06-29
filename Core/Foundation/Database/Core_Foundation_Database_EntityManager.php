<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 	PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2017 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class Core_Foundation_Database_EntityManager
{
    private $db;
    private $configuration;

    private $entityMetaData = array();

    public function __construct(
        Core_Foundation_Database_DatabaseInterface $db,
        Core_Business_ConfigurationInterface $configuration
    ) {
        $this->db = $db;
        $this->configuration = $configuration;
    }

    /**
     * Return current database object used
     * @return Core_Foundation_Database_DatabaseInterface
     */
    public function getDatabase()
    {
        return $this->db;
    }

    /**
     * Return current repository used
     * @param $className
     * @return mixed
     */
    public function getRepository($className)
    {
        if (is_callable(array($className, 'getRepositoryClassName'))) {
            $repositoryClass = call_user_func(array($className, 'getRepositoryClassName'));
        } else {
            $repositoryClass = null;
        }

        if (!$repositoryClass) {
            $repositoryClass = 'Core_Foundation_Database_EntityRepository';
        }

        $repository = new $repositoryClass(
            $this,
            $this->configuration->get('_DB_PREFIX_'),
            $this->getEntityMetaData($className)
        );

        return $repository;
    }

    /**
     * Return entity's meta data
     * @param $className
     * @return mixed
     * @throws Adapter_Exception
     */
    public function getEntityMetaData($className)
    {
        if (!array_key_exists($className, $this->entityMetaData)) {
            $metaDataRetriever = new Adapter_EntityMetaDataRetriever;
            $this->entityMetaData[$className] = $metaDataRetriever->getEntityMetaData($className);
        }

        return $this->entityMetaData[$className];
    }

    /**
     * Flush entity to DB
     * @param Core_Foundation_Database_EntityInterface $entity
     * @return $this
     */
    public function save(Core_Foundation_Database_EntityInterface $entity)
    {
        $entity->save();
        return $this;
    }

    /**
     * DElete entity from DB
     * @param Core_Foundation_Database_EntityInterface $entity
     * @return $this
     */
    public function delete(Core_Foundation_Database_EntityInterface $entity)
    {
        $entity->delete();
        return $this;
    }
}
