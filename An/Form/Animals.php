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
     * __construct
     *
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
    }

    
    /**
     * Get the element HTML
     *
     * @return string|\Dom\Template
     */
    public function show()
    {
        $t = $this->getTemplate();

        $this->decorateElement($t);


        return $t;
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
        <select name="typeId" class="form-control input-sm animals-type-id" var="typeId">
          <option value="0">-- Select Animal --</option>
        </select>
      </div>
      <div class="col-sm-2">
        <input type="text" class="form-control input-sm animals-value" placeholder="Animals Treated" name="value" value="0" var="value"/>
      </div>
      <div class="col-sm-6">
        <button type="button" class="btn btn-danger btn-xs noblock animals-del" var="del" title="Remove this animal type from your list"><i class="fa fa-minus"></i></button>
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
      <button type="button" class="btn btn-primary btn-xs noblock animals-add" var="add" title="Add a new animal type to your list"><i class="fa fa-plus"></i></button> &nbsp; <span class="small">Click the add button to save your selection</span>
    </div>
  </div>
</div>
HTML;
        return \Dom\Loader::load($xhtml);
    }
    
    
    
}