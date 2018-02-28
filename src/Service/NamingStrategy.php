<?php


namespace App\Service;

class NamingStrategy implements \Doctrine\ORM\Mapping\NamingStrategy
{
    /**
     * @var integer
     */
    private $case;

    /**
     * Underscore naming strategy construct.
     *
     * @param integer $case CASE_LOWER | CASE_UPPER
     */
    public function __construct($case = CASE_LOWER)
    {
        $this->case = $case;
    }

    /**
     * @return integer CASE_LOWER | CASE_UPPER
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Sets string case CASE_LOWER | CASE_UPPER.
     * Alphabetic characters converted to lowercase or uppercase.
     * 
     * @param integer $case
     *
     * @return void
     */
    public function setCase($case)
    {
        $this->case = $case;
    }

    /**
     * {@inheritdoc}
     */
    public function classToTableName($className)
    {
        if (strpos($className, '\\') !== false) {
            $className = substr($className, strrpos($className, '\\') + 1);
        }

        return 'nina_' . $this->underscore($className);
    }

    /**
     * {@inheritdoc}
     */
    public function classToJoinTableName($className)
    {
        if (strpos($className, '\\') !== false) {
            $className = substr($className, strrpos($className, '\\') + 1);
        }

        return $this->underscore($className);
    }

    /**
     * {@inheritdoc}
     */
    public function propertyToColumnName($propertyName, $className = null)
    {
        return $this->underscore($propertyName);
    }

    /**
     * {@inheritdoc}
     */
    public function embeddedFieldToColumnName($propertyName, $embeddedColumnName, $className = null, $embeddedClassName = null)
    {
        return $this->underscore($propertyName).'_'.$embeddedColumnName;
    }

    /**
     * {@inheritdoc}
     */
    public function referenceColumnName()
    {
        return $this->case === CASE_UPPER ?  'ID' : 'id';
    }

    /**
     * {@inheritdoc}
     */
    public function joinColumnName($propertyName, $className = null)
    {
        return $this->underscore($propertyName) . '_' . $this->referenceColumnName();
    }

    /**
     * {@inheritdoc}
     */
    public function joinTableName($sourceEntity, $targetEntity, $propertyName = null)
    {
        return 'nina_' . $this->classToJoinTableName($sourceEntity) . '_' . $this->classToJoinTableName($targetEntity);
    }
    
    /**
     * {@inheritdoc}
     */
    public function joinKeyColumnName($entityName, $referencedColumnName = null)
    {
        return $this->classToTableName($entityName) . '_' .
                ($referencedColumnName ?: $this->referenceColumnName());
    }
    
    /**
     * @param string $string
     *
     * @return string
     */
    private function underscore($string)
    {
        $string = preg_replace('/(?<=[a-z])([A-Z])/', '_$1', $string);

        if ($this->case === CASE_UPPER) {
            return strtoupper($string);
        }

        return strtolower($string);
    }
}
