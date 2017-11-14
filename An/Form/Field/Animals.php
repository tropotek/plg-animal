<?php
namespace An\Form\Field;


/**
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2015 Michael Mifsud
 */
class Animals extends \Tk\Form\Field\Iface
{
    /**
     * @var \An\Db\Type[]|\Tk\Db\Map\ArrayObject
     */
    private $typeList = null;

    /**
     * @var \App\Db\Placement
     */
    public $placement = null;


    /**
     * __construct
     *
     * @param string $name
     * @param \An\Db\Type[]|\Tk\Db\Map\ArrayObject $typeList
     * @param \App\Db\Placement $placement
     * @throws \Tk\Exception
     */
    public function __construct($name, $typeList, $placement)
    {
        parent::__construct($name);
        $this->typeList = $typeList;
        $this->placement = $placement;
        if (!$this->placement || !$this->placement->id) {
            throw new \Tk\Exception('Invalid PlacementId');
        }
    }


    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function show()
    {
        $template = $this->getTemplate();

        //$this->decorateElement($template);

        $template->appendJsUrl(\Tk\Uri::create(\An\Plugin::getInstance()->getPluginPath().'/An/Form/Field/jquery.animalField.js'));

        // Setup the javascript template
        $repeat = $template->getRepeat('row');
        $repeat->addCss('row', 'animals-row-template hide');
        $repeat->setAttr('valueId', 'name', $this->getName().'-valueId[]');
        $repeat->setAttr('typeId', 'name', $this->getName().'-typeId[]');
        $repeat->setAttr('value', 'name', $this->getName().'-value[]');
        $template->setAttr('typeId', 'name', $this->getName().'-typeId-add');
        $template->setAttr('value', 'name', $this->getName().'-value-add');

        /** @var \Dom\Form\Select $selEl */
        $selEl = $repeat->getForm()->getFormElement('typeId');
        /** @var \Dom\Form\Select $selEl2 */
        $selEl2 = $template->getForm()->getFormElement('typeId-add');
        /** @var \An\Db\Type $type */
        foreach ($this->typeList as $type) {
            $selEl->appendOption($type->name, $type->id);
            $selEl2->appendOption($type->name, $type->id);
        }
        $repeat->appendRepeat();





        $list = $this->getValue();
        if (is_array($list)) {
            foreach ($list as $v) {
                $repeat = $template->getRepeat('row');
                $repeat->setAttr('valueId', 'name', $this->getName().'-valueId[]');
                $repeat->setAttr('typeId', 'name', $this->getName().'-typeId[]');
                $repeat->setAttr('value', 'name', $this->getName().'-value[]');
                /** @var \Dom\Form\Select $selEl */
                $selEl = $repeat->getForm()->getFormElement('typeId');
                /** @var \An\Db\Type $type */
                foreach ($this->typeList as $type) {
                    $selEl->appendOption($type->name, $type->id);
                }
                $selEl->setValue($v['typeId']);
                $repeat->getForm()->getFormElement('valueId')->setValue($v['valueId']);
                $repeat->getForm()->getFormElement('value')->setValue($v['value']);

                $repeat->appendRepeat();
            }
        }







        return $template;
    }

    /**
     * makeTemplate
     *
     * @return \Dom\Template
     */
    public function __makeTemplate()
    {

        $xhtml = <<<HTML
<div class="tk-animals-field" var="field">

  <div class="animals-input-block">
    <div class="row animals-input" repeat="row" var="row">
      <input type="hidden" name="valueId" value="0" var="valueId"/>
      <div class="col-sm-4">
        <select name="typeId" class="form-control input-sm animals-type-id" var="typeId" style="padding: 0;">
          <option value="0">-- Select Animal --</option>
        </select>
      </div>
      <div class="col-sm-2">
        <input type="text" class="form-control input-sm animals-value" placeholder="Animals Treated" name="value" value="0" var="value"/>
      </div>
      <div class="col-sm-6">
        <button type="button" class="btn btn-danger btn-xs animals-del" var="del" title="Remove this animal type from your list"><i class="fa fa-minus"></i></button>
      </div>
    </div>
  </div>

  <div><hr/></div>
  
  <div class="row animals-input-add">
    <div class="col-sm-4">
      <select name="typeId-add" class="form-control input-sm animals-type-id-add" var="typeId">
        <option value="">-- Select Animal --</option>
      </select>
    </div>
    <div class="col-sm-2">
      <input type="text" class="form-control input-sm animals-value-add" placeholder="Animals Treated" name="value-add" value="0" var="value"/>
    </div>
    <div class="col-sm-6">
      <button type="button" class="btn btn-primary btn-xs animals-add" var="add" title="Add a new animal type to your list"><i class="fa fa-plus"></i></button>
    </div>
  </div>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }



}
