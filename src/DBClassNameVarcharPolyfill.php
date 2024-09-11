<?php

// Note that this namespace MUST start with a capitial letter, otherwise it will not be autoloaded
// properly and there will be a fatal interaction between the Silverstripe ClassLoader and the
// composer class loader
namespace Emtekentnz\VarcharPolyfill;

use SilverStripe\Core\ClassInfo;
use SilverStripe\Core\Config\Config;
use SilverStripe\ORM\DataObject;
use SilverStripe\ORM\FieldType\DBVarchar;

/**
 * This class is copy-pasted from
 * https://github.com/silverstripe/silverstripe-framework/blob/5/src/ORM/FieldType/DBClassNameTrait.php
 */
class DBClassNameVarcharPolyfill extends DBVarchar
{
    /**
     * Base classname of class to enumerate.
     * If 'DataObject' then all classes are included.
     * If empty, then the baseClass of the parent object will be used
     *
     * @var string|null
     */
    protected $baseClass = null;

    /**
     * Parent object
     *
     * @var DataObject|null
     */
    protected $record = null;

    private static $index = true;

    /**
     * Create a new DBClassName field
     *
     * @param string      $name      Name of field
     * @param string|null $baseClass Optional base class to limit selections
     * @param array       $options   Optional parameters for this DBField instance
     */
    public function __construct($name = null, $baseClass = null, $options = [])
    {
        $this->setBaseClass($baseClass);
        parent::__construct($name, null, null, $options);
    }

    /**
     * Get the base dataclass for the list of subclasses
     *
     * @return string
     */
    public function getBaseClass()
    {
        // Use explicit base class
        if ($this->baseClass) {
            return $this->baseClass;
        }
        // Default to the basename of the record
        $schema = DataObject::getSchema();
        if ($this->record) {
            return $schema->baseDataClass($this->record);
        }
        // During dev/build only the table is assigned
        $tableClass = $schema->tableClass($this->getTable());
        if ($tableClass && ($baseClass = $schema->baseDataClass($tableClass))) {
            return $baseClass;
        }
        // Fallback to global default
        return DataObject::class;
    }

    /**
     * Get the base name of the current class
     * Useful as a non-fully qualified CSS Class name in templates.
     *
     * @return string|null
     */
    public function getShortName()
    {
        $value = $this->getValue();
        if (empty($value) || !ClassInfo::exists($value)) {
            return null;
        }
        return ClassInfo::shortName($value);
    }

    /**
     * Assign the base class
     *
     * @param string $baseClass
     * @return $this
     */
    public function setBaseClass($baseClass)
    {
        $this->baseClass = $baseClass;
        return $this;
    }

    /**
     * Get list of classnames that should be selectable
     *
     * @return array
     */
    public function getEnum()
    {
        $classNames = ClassInfo::subclassesFor($this->getBaseClass());
        $dataobject = strtolower(DataObject::class);
        unset($classNames[$dataobject]);
        return array_values($classNames ?? []);
    }

    public function setValue($value, $record = null, $markChanged = true)
    {
        parent::setValue($value, $record, $markChanged);

        if ($record instanceof DataObject) {
            $this->record = $record;
        }

        return $this;
    }

    private function getDefaultClassName()
    {
        // Allow classes to set default class
        $baseClass = $this->getBaseClass();
        $defaultClass = Config::inst()->get($baseClass, 'default_classname');
        if ($defaultClass &&  class_exists($defaultClass ?? '')) {
            return $defaultClass;
        }

        // Fallback to first option
        $subClassNames = $this->getEnum();
        return reset($subClassNames);
    }
}
