<?php
namespace An\Db;


use App\Db\Traits\PlacementTrait;
use Bs\Db\Traits\TimestampTrait;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Value extends \Tk\Db\Map\Model
{
    use TimestampTrait;
    use PlacementTrait;
    
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $typeId = 0;

    /**
     * @var int
     */
    public $placementId = 0;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var int
     */
    public $value = 0;

    /**
     * @var string
     */
    public $notes = '';

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var \DateTime
     */
    public $created = null;

    /**
     * @var Type
     */
    private $_type = null;


    /**
     * constructor.
     */
    public function __construct()
    {
        $this->_TimestampTrait();
    }

    /**
     * @param \App\Db\Placement $placement
     * @param \An\Db\Type $type
     * @param int $value
     * @param string $notes
     * @return Value
     */
    public static function create($placement, $type, $value, $notes = '')
    {
        $obj = new self();
        $obj->setPlacementId($placement->getId());
        $obj->setTypeId($type->getId());
        $obj->setName($type->getName());
        $obj->setNotes($notes);
        $obj->setValue((int)$value);
        return $obj;
    }


    /**
     * @return null|Type|\Tk\Db\Map\Model|\Tk\Db\ModelInterface
     * @throws \Exception
     */
    public function getType()
    {
        if (!$this->_type) {
            $this->_type = TypeMap::create()->find($this->getTypeId());
        }
        return $this->_type;
    }

    /**
     * @return int
     */
    public function getTypeId(): int
    {
        return $this->typeId;
    }

    /**
     * @param int $typeId
     * @return Value
     */
    public function setTypeId(int $typeId): Value
    {
        $this->typeId = $typeId;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Value
     */
    public function setName(string $name): Value
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     * @return Value
     */
    public function setValue(int $value): Value
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return string
     */
    public function getNotes(): string
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     * @return Value
     */
    public function setNotes(string $notes): Value
    {
        $this->notes = $notes;
        return $this;
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();
        $errors = $this->validatePlacementId($errors);

        if ((int)$this->getTypeId() <= 0) {
            $errors['typeId'] = 'Invalid Type ID';
        }
        if (!$this->getName()) {
            $errors['name'] = 'Please enter a valid name';
        }

        return $errors;
    }
}