'use strict';

var woovr_selected = false;

jQuery(document).ready(function($) {
  //$('.woovr-variations').closest('.variations_form').find('.variations').hide();

  // check default
  $(document).on('found_variation', function(e, t) {
    // Keep the visual active state (and the Option 1/2 grouping) in sync
    // with whichever variation is actually selected. This used to be
    // gated behind !woovr_selected (only ran once, before any manual
    // pick), which left stale .active classes on both Option 1 and
    // Option 2 at once as soon as a pill selection crossed from one
    // group to the other (e.g. Mini Welcome Box -> No Welcome Box).
    $('.woovr-variation').removeClass('active');
    $('.woovr-variations .option').removeClass('active');
    $('.woovr-variation[data-id="' + t.variation_id + '"]').addClass('active').
        find('input[type="radio"]').
        prop('checked', true);
    $('.woovr-variation[data-id="' + t.variation_id + '"]').parents('.option').eq(0).addClass('active');
    update_first_pay_date(t.variation_id);

    if (!woovr_selected) {
      // default for html select
      $('.woovr-variation-select').val(t.variation_id).trigger('change');
    }
  });

  $('.woovr-variations').each(function() {
    if ($(this).hasClass('woovr-variations-ddslick')) {
      var _variations = $(this);

      _variations.find('select').ddslick({
        width: '300px',
        onSelected: function(data) {
          var _selected = $(data.original[0].children[data.selectedIndex]);

          woovr_do_select(_selected, _variations);
        },
      });
    }
  });

  $('.woovr-variation-radio').on('click', function() {
    var _this = $(this);
    var _variations = _this.closest('.woovr-variations');

    woovr_do_select(_this, _variations);
	jQuery('.woovr-variation').removeClass('active');
	jQuery('.woovr-variations .option').removeClass('active');
    _this.addClass('active').find('input[type="radio"]').prop('checked', true);
	_this.parents('.option').eq(0).addClass('active');
	update_first_pay_date(_this.attr('data-id'));
  });

  $('.woovr-variation-select').on('change', function() {
    var _this = $(this);
    var _variations = _this.closest('.woovr-variations');
    var _selected = $('option:selected', this);

    woovr_do_select(_selected, _variations);

    _this.closest('.woovr-variation').
        find('.woovr-variation-image').
        html('<img src="' + _selected.attr('data-imagesrc') + '"/>');
    _this.closest('.woovr-variation').
        find('.woovr-variation-price').
        html(_selected.attr('data-pricehtml'));
  });
  
  // Option 1/2 headers used to look like a checkbox/radio (a circle icon)
  // but only Option 1's was actually clickable, and clicking it silently
  // picked Mini Welcome Box as a side effect - confusing since it looked
  // selectable but wasn't really a control for choosing between the two.
  // Now the headers are purely an expand/collapse toggle for the long
  // description text (chevron icon, CSS handles the rotation/visibility
  // via .expanded); actually picking an option happens by clicking the
  // pill that now lives inside each box (see site-modernize.js), which
  // auto-expands its own box via the .active class already kept in sync
  // above.
  jQuery('.woovr-variations .option_header').on('click', function () {
    jQuery(this).closest('.option').toggleClass('expanded');
  });
});

function woovr_do_select(selected, variations) {
  var attrs = jQuery.parseJSON(selected.attr('data-attrs'));

  // set as selected
  woovr_selected = true;

  for (var key in attrs) {
    jQuery('select[name="' + key + '"]').val(attrs[key]).trigger('change');
  }

  jQuery(document).trigger('woovr_selected', [selected, variations]);
}

function update_first_pay_date(id) {
	// SHOW SHIPPING DATE
	jQuery('.next_box').show();
	jQuery('.next_box .next_shipping_date').hide();
	jQuery('.next_box .next_shipping_date.date_'+id).show();
	hide_suboptions();
}

function hide_suboptions() {
	if (jQuery('.woovr-variations .option').eq(0).hasClass('active'))
		jQuery('.woovr-variations .option').eq(0).find('.woovr-variation').show();
	else
		jQuery('.woovr-variations .option').eq(0).find('.woovr-variation').hide();
}