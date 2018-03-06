<?php
namespace An\Db;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Type extends \Tk\Db\Map\Model
{
    
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $profileId = 0;

    /**
     * @var string
     */
    public $name = '';

    /**
     * @var string
     */
    public $description = '';

    /**
     * @var int
     */
    public $min = 0;

    /**
     * @var int
     */
    public $max = 0;

    /**
     * @var string
     */
    public $notes = '';

    /**
     * @var \DateTime
     */
    public $modified = null;

    /**
     * @var int
     */
    public $orderBy = 0;

    /**
     * @var \DateTime
     */
    public $created = null;

    /**
     * @var \App\Db\Profile
     */
    private $profile = null;



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
     * @return \App\Db\Profile|null|\Tk\Db\Map\Model|\Tk\Db\ModelInterface
     * @throws \Tk\Db\Exception
     */
    public function getProfile()
    {
        if (!$this->profile) {
            $this->profile = \App\Db\ProfileMap::create()->find($this->profileId);
        }
        return $this->profile;
    }

    /**
     * @return array
     */
    public function validate()
    {
        $errors = array();

        if ((int)$this->profileId <= 0) {
            $errors['profileId'] = 'Invalid Profile ID';
        }
        if (!$this->name) {
            $errors['name'] = 'Please enter a valid course name';
        }

        return $errors;
    }
}