<?php
namespace An\Db;


use Bs\Db\Traits\OrderByTrait;
use Bs\Db\Traits\TimestampTrait;
use Uni\Db\Traits\CourseTrait;

/**
 * @author Michael Mifsud <http://www.tropotek.com/>
 * @see http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Type extends \Tk\Db\Map\Model
{
    use TimestampTrait;
    use CourseTrait;
    use OrderByTrait;
    
    /**
     * @var int
     */
    public $id = 0;

    /**
     * @var int
     */
    public $courseId = 0;

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
     * constructor.
     */
    public function __construct()
    {
        $this->_TimestampTrait();
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
     * @return Type
     */
    public function setName(string $name): Type
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Type
     */
    public function setDescription(string $description): Type
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @param int $min
     * @return Type
     */
    public function setMin(int $min): Type
    {
        $this->min = $min;
        return $this;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @param int $max
     * @return Type
     */
    public function setMax(int $max): Type
    {
        $this->max = $max;
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
     * @return Type
     */
    public function setNotes(string $notes): Type
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
        $errors = $this->validateCourseId($errors);

        if (!$this->name) {
            $errors['name'] = 'Please enter a valid name';
        }

        return $errors;
    }
}