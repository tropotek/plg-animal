/*
 * Plugin: animalSelect
 * Version: 1.0
 * Date: 11/05/17
 *
 * @author Michael Mifsud <info@tropotek.com>
 * @see http://www.tropotek.com/
 * @license Copyright 2007 Michael Mifsud
 * @source http://stefangabos.ro/jquery/jquery-plugin-boilerplate-revisited/
 */

/**
 * TODO: Change every instance of "animalSelect" to the name of your plugin!
 *
 * <code>
 *   $(document).ready(function() {
 *     // attach the plugin to an element
 *     $('#element').animalSelect({'foo': 'bar'});
 *
 *     // call a public method
 *     $('#element').data('animalSelect').foo_public_method();
 *
 *     // get the value of a property
 *     $('#element').data('animalSelect').settings.foo;
 *   
 *   });
 * </code>
 */
;(function($) {
  var animalSelect = function(element, options) {
    var plugin = this;
    plugin.settings = {};
    var $element = $(element);

    // plugin settings
    var defaults = {
      templateSelect : '.animal-input-row.animal-input-add',
      onCreateRow: function(newRow) {}     // this = row
    };

    // plugin vars
    var template = null;

    // constructor method
    plugin.init = function() {
      plugin.settings = $.extend({}, defaults, $element.data(), options);
      template = $element.find(plugin.settings.templateSelect).clone();

      $element.on('click', '.animals-del', function (e1) {
        $(this).closest('.animal-input-row').remove();
      });

      $element.on('click', '.animals-add', function(e) {
        var row = $(this).closest('.animal-input-row');
        $element.find('.has-error').removeClass('has-error');

        // validate the fields
        var value = row.find('.animals-value').val();
        if (value === '' || value.match(/^[0-9]+$/) === null || parseInt(value) <= 0) {
          row.find('.animals-value').parent().addClass('has-error').focus();
        }
        var typeId = row.find('.animals-type-id').val();
        if (typeId === '' || typeId.match(/^[0-9]+$/) === null || parseInt(typeId) <= 0) {
          row.find('.animals-type-id').parent().addClass('has-error').focus();
        }

        if ($element.find('.has-error').length > 0) {
          return;
        }

        row.find('.animals-del').removeClass('hide').show();
        row.find('.animals-add').removeClass('hide').hide();
        var newRow = template.clone();
        newRow.find('.animals-del').hide();
        newRow.find('.animals-add').show();
        newRow.find('input, select, button').removeAttr('disabled');
        row.after(newRow);
        plugin.settings.onCreateRow.apply(row, [newRow]);

      });

    };  // END init()

    /**
     * @returns {*|HTMLElement}
     */
    plugin.getElement = function () {
      return $element;
    };

    plugin.init();
  };

  // add the plugin to the jQuery.fn object
  $.fn.animalSelect = function(options) {
    return this.each(function() {
      if (undefined === $(this).data('animalSelect')) {
        var plugin = new animalSelect(this, options);
        $(this).data('animalSelect', plugin);
      }
    });
  }

})(jQuery);

