(function( $ ) {
	'use strict';
	

	 function ukdsSetTabHash() {
		var conf = $( "[name='ltd_settings']" );
		if ( conf.length ) {
			var currentUrl = conf.attr( 'action' ).split( '#' )[ 0 ];
			conf.attr( 'action', currentUrl + window.location.hash );
		}
	}
	 $(window).on('hashchange', ukdsSetTabHash);

	 function setPartnerBox() {
	     $("#partner.ukdstab").find(".partner-option").hide();
	     var box = $("[name='ltd-tickets[config_partner_type]']:checked").val();
	     $("#partner-" + box).show();
	 }

	 
	$(function() {
		
        $( '.ltd-tickets-color-picker' ).wpColorPicker();

       

        $("[name='ltd-tickets[config_partner_type]']").on("click", setPartnerBox); setPartnerBox();

		$('#ukds-tabs').find('a').click(function() {
			$( '#ukds-tabs' ).find( 'a' ).removeClass( 'nav-tab-active' );
			$( '.ukdstab' ).removeClass( 'active' );
			var id = $( this ).attr( 'id' ).replace( '-tab', '' );
			$( '#' + id ).addClass( 'active' );
			$( this ).addClass( 'nav-tab-active' );
		});

		var activeTab = window.location.hash.replace( '#top#', '' );
		if ( activeTab === '' || activeTab === '#_=_' ) {
			activeTab = $( '.ukdstab' ).attr( 'id' );
		}

		$( '#' + activeTab ).addClass( 'active' );
		$( '#' + activeTab + '-tab' ).addClass( 'nav-tab-active' );
	    $('.nav-tab-active').click();


		$(".ranger").rangeslider({
		    polyfill: false,
		    onSlide: function (position, value) {
		        $(".redirection-explanations").find(".description").hide();
		        $(".description[data-value='" + value + "']").show();
		    }
		});


		$("[ukds-ui='stringsync']").each(function () {
		    var scope = $(this);

		    var updater = function () {
		        var str = scope.attr("ukds-format");
		        var val = $(this).val();
		        val = (val != "" ? val : scope.attr("ukds-default"));
		        val = (scope.attr("ukds-noencode") == "1" ? val : encodeURI(val));
		        str = str.replace("$1", val);
		        scope.text(str);
		    }

		    $($(this).attr("ukds-sync")).on("keyup", updater)
		    $($(this).attr("ukds-sync")).on("blur", updater)
		});


		var colourDisable = function () {
		    if ($(this).val() != "") {
		        $(this).siblings(".button-style").addClass("admin-disabled");
		    } else {
		        $(this).siblings(".button-style").removeClass("admin-disabled");
		    }
		}
		$("#ltd-tickets-style_primary_button_css_class, #ltd-tickets-style_secondary_button_css_class").on("keyup", colourDisable).each(colourDisable);



		var toggleChecked = function (target) {
		    if (target.attr("ukds-toggle-val") ? (target.attr("ukds-toggle-val") == $(target.attr('ukds-toggle-checked') + ':checked').val()) : $(target.attr('ukds-toggle-checked')).is(':checked') ) {
		        (target.attr('ukds-state') == "1" ? target.hide() : target.show());
		    } else {
		        (target.attr('ukds-state') == "1" ? target.show() : target.hide());
		    }

		}

		$("[ukds-toggle-checked]").each(function () {
		    var scope = $(this);
		    $(scope.attr('ukds-toggle-checked')).on("change", function () {
		        toggleChecked(scope);
		    });
		    toggleChecked(scope);
		})

		$("input[name='ltd-tickets[product_update_content]']").on("change", function (e) {
		    if ($(this).is(":checked")) {
		        if (confirm("Are you sure? Any changes you make to product descriptions will be lost every time the plugin synchronises!")) {

		        } else {
		            $(this).prop('checked', false)
		        }
		    }
		});

	});

})( jQuery );
