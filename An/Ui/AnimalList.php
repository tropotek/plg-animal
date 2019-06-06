<?php
namespace An\Ui;

use Dom\Template;


/**
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2016 Michael Mifsud
 */
class AnimalList extends \Dom\Renderer\Renderer
{
    /**
     * @var \An\Db\Value[]
     */
    protected $list = null;


    /**
     * @param \An\Db\Value[] $list
     */
    public function __construct($list)
    {
        $this->list = $list;
    }

    /**
     * @param \An\Db\Value[] $list
     * @return AnimalList
     */
    public static function create($list)
    {
        $obj = new static($list);
        return $obj;
    }

    /**
     * @return \Dom\Template
     * @throws \Tk\Db\Exception
     */
    public function show()
    {
        $template = $this->getTemplate();
        if (!$this->list || !count($this->list)) return $template;

        foreach ($this->list as $val) {
            $row = $template->getRepeat('row');
            $row->insertText('name', $val->name);
            $row->insertText('size', $val->value);
            $row->appendRepeat();
            $template->setVisible('table');
        }

        return $template;
    }

    /**
     * DomTemplate magic method
     *
     * @return Template
     */
    public function __makeTemplate()
    {
        $xhtml = <<<HTML
<div class="animal-view">   
  
  <table class="table table-striped" choice="table">
    <tr var="row" repeat="row">
      <td clas="key" var="name"></td>
      <td var="size">0</td>
    </tr>
  </table>
  
</div>
HTML;

        return \Dom\Loader::load($xhtml);
    }

}
