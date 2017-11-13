<?php
namespace An\Db;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Value extends \Tk\Db\Map\Model
{
    
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
     * @var string
     */
    public $value = '';

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
    private $type = null;

    /**
     * @var \App\Db\Placement
     */
    private $placement = null;



    /**
     * Course constructor.
     */
    public function __construct()
    {
        $this->modified = \Tk\Date::create();
        $this->created = \Tk\Date::create();
    }

    /**
     *
     */
    public function save()
    {
        parent::save();
    }

    /**
     * @return null|Type|\Tk\Db\Map\Model|\Tk\Db\ModelInterface
     */
    public function getType()
    {
        if (!$this->type) {
            $this->type = TypeMap::create()->find($this->typeId);
        }
        return $this->type;
    }

    /**
     * @return \App\Db\Placement|null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface
     */
    public function getPlacement()
    {
        if (!$this->placement) {
            $this->placement = \App\Db\PlacementMap::create()->find($this->placementId);
        }
        return $this->placement;
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();

        if ((int)$this->typeId <= 0) {
            $errors['typeId'] = 'Invalid Type ID';
        }
        if ((int)$this->placementId <= 0) {
            $errors['placementId'] = 'Invalid Placement ID';
        }
        if (!$this->name) {
            $errors['name'] = 'Please enter a valid course name';
        }

        return $errors;
    }
}