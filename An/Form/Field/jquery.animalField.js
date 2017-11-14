/*
 * Plugin: animalField
 * Version: 1.0
 * Date: 11/05/17
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @link http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 * @source http://stefangabos.ro/jquery/jquery-plugin-boilerplate-revisited/
 */

/**
 * TODO: Change every instance of "animalField" to the name of your plugin!
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('#element').animalField({'foo': 'bar'});
 *
 *     // call a public method
 *     $('#element').data('animalField').foo_public_method();
 *
 *     // get the value of a property
 *     $('#element').data('animalField').settings.foo;
 *   
 *   });
 * </code>
 */
;(function($) {
  var animalField = function(element, options) {
    var plugin = this;
    plugin.settings = {};
    var $element = $(element);

    // plugin settings
    var defaults = {
      foo: 'bar',
      onFoo: function() {}
    };

    // plugin vars
    var form = $(element).closest('form');
    var inputBlock = $element.find('.animals-input-block');
    var inputAddBlockTpl = $element.find('.animals-row-template').detach().removeClass('hide').removeClass('animals-row-template');

    // constructor method
    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, options);


      $element.find('.animals-add').on('click', function(e) {
        var srcBlock = $(this).closest('.animals-input-add');
        srcBlock.find('.error').removeClass('error');

        // validate the fields
        var typeId = srcBlock.find('.animals-type-id-add').val();
        if (typeId === '' || typeId.match(/^[0-9]+$/) === null || parseInt(typeId) <= 0) {
          srcBlock.find('.animals-type-id-add').addClass('error');
        }
        var value = srcBlock.find('.animals-value-add').val();
        if (value === '' || value.match(/^[0-9]+$/) === null || parseInt(value) <= 0) {
          srcBlock.find('.animals-value-add').addClass('error');
        }

        if (srcBlock.find('.error').length > 0) {
          return;
        }

        var row = inputAddBlockTpl.clone();
        row.find('.animals-del').on('click', function (e1) {
          $(this).closest('.animals-input').remove();
        });
        row.find('.animals-type-id').val(parseInt(typeId, 10));
        row.find('.animals-value').val(parseInt(value, 10));
        srcBlock.find('.animals-type-id-add').val('');
        srcBlock.find('.animals-value-add').val('0');
        row.find('input, select, button').removeAttr('disabled');
        inputBlock.append(row);
      });


      $element.find('.animals-del').on('click', function (e1) {
        $(this).closest('.animals-input').remove();
      });

      $element.on('submit', function(e) {
        if (parseInt(field.find('.animals-value-add').val(), 10) > 0) {
          var b = confirm('Woops! looks like you may have forgotten to add you last animal entry. Continue?');
          if (!b && typeof uOff !== 'undefined') {
//            uOff();
            //form.unbind('submit');
            //e.preventDefault();
            return false;
          }
        }
        return true;
      });







    };  // END init()

    // private methods
    //var foo_private_method = function() { };

    // public methods

    /**
     *
     * @param b bool
     */
    plugin.enable = function(b) {
      if (b === false) {
        // TODO: disable all controls
      } else {
        // TODO: enable all controls
      }
    };

    // call the "constructor" method
    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.animalField = function(options) {
    return this.each(function() {
      if (undefined === $(this).data('animalField')) {
        var plugin = new animalField(this, options);
        $(this).data('animalField', plugin);
      }
    });
  }

})(jQuery);

jQuery(function($) {
  $('.tk-animals-field').animalField({

  });
});
