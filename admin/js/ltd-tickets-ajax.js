var imports = {
    products: [],
    venues: [],
    categories: []
};


(function ($) {
	'use strict';

	function buildNotice(str, type) {
	    var html = $("<div class='inline-notice notice-" + type + "' />");
	    html.append(str);
	    return html;
	}

	function buildTableHeader(title, description, css) {
	    var html = $("<div class='import-table-header " + css + "' />");
	    html.append("<h3>" + title + "</h3>");
	    html.append("<p>" + description + "</p>");
	    return html;
	}
	function buildTableBody() {
	    return $("<div class='import-table-body' />");
	}

	function buildSuggested(data, suggested, uid) {
	    var sel = $("<select id='" + uid + "' />");
	    var blank = $("<option />");
	    blank.html("No Match - Create new product");
	    sel.append(blank)
	    $.each(data, function (i, product) {
	        var opt = $("<option />");
	        opt.html(product.name);
	        opt.val(product.id);
	        if (product.id == suggested) {
	            opt.attr("selected", "selected");
	        }
	        sel.append(opt);
	    });
	    return sel;
	}

	function LevinshteinDistance(a, b) {
	    // Check for values
	    if (a == b) return 0;
	    if (a.length == 0) return b.length;
	    if (b.length == 0) return a.length;

	    var matrix = [];
	    // increment along the first column of each row
	    var i;
	    for (i = 0; i <= b.length; i++) {
	        matrix[i] = [i];
	    }

	    // increment each column in the first row
	    var j;
	    for (j = 0; j <= a.length; j++) {
	        matrix[0][j] = j;
	    }

	    // Fill in the rest of the matrix
	    for (i = 1; i <= b.length; i++) {
	        for (j = 1; j <= a.length; j++) {
	            if (b.charAt(i - 1) == a.charAt(j - 1)) {
	                matrix[i][j] = matrix[i - 1][j - 1];
	            } else {
	                matrix[i][j] = Math.min(matrix[i - 1][j - 1] + 1, // substitution
                                            Math.min(matrix[i][j - 1] + 1, // insertion
                                                     matrix[i - 1][j] + 1)); // deletion
	            }
	        }
	    }
	    return matrix[b.length][a.length];
	}



	$(function() {
		$("input[name='ImportProducts'], input[name='ImportVenues'], input[name='ImportAll'], input[name='ImportCategories']").click(function() {
			var trigger= $(this);
			trigger.siblings('.spinner').addClass("is-active");
		});
		$("input[name='FetchUpdateProducts']").click(function (e) {
		    e.preventDefault();
		    var trigger= $(this);
		    trigger.siblings('.spinner').addClass("is-active");
		    $.getJSON(
				ukdsAjaxHandler.ajaxurl,
				{
				    action: 'ukds-fetch-products',
				    nonce: ukdsAjaxHandler.nonce,
                    api: -1
				},
                function (response) {
                    trigger.siblings('.spinner').removeClass("is-active");
                    var sel = $('[data-ui="FetchProducts"]');
                    if (response.success) {
                        sel.addClass("active");
                        $("[data-ui='HideOnFetchProducts']").addClass("hidden");

                        sel.children(".import-table-body").empty();
                        var tbl = $("<table class='dataTable no-footer' />");
                        tbl.append("<thead><tr><th>Product Name</th></tr></thead>");
                        tbl.append("<tbody>");
                        var m = 0;
                        for (var i = 0; i < response.products.length; i++) {

                            var field = $("<fieldset />");
                            var label = $("<label for='update-product-" + response.products[i].id + "'>");
                            var input = $("<input type='checkbox' id='update-product-" + response.products[i].id + "' name='update-product-" + response.products[i].id + "' value='1' />");
                            var span = $("<span>" + response.products[i].name + "</span>");
                            label.append(input);
                            label.append(span);
                            field.append(label);
                            var tr = $("<tr />");
                            var td = $("<td />");
                            td.append(field);
                            tr.append(td);
                            tbl.append(tr);
                        }
                      
                        tbl.append("</tbody>");
                        sel.children(".import-table-body").append(tbl);
                        tbl.dataTable({
                            "scrollY": "350px",
                            "scrollCollapse": true,
                            "paging": false
                        });
                        $("[name='ltd_settings']").on("submit", function (e) {
                            var form = this;
                            var params = tbl.$('input').serializeArray();
                            $.each(params, function () {
                                if (!$.contains(document, form[this.name])) {
                                    $(form).append(
                                       $('<input>')
                                          .attr('type', 'hidden')
                                          .attr('name', this.name)
                                          .val(this.value)
                                    );
                                }
                            });
                        })
                    }
                }
            );
		})
		$("input[name='FetchProducts']").click(function(e) {
			e.preventDefault();
			var trigger= $(this);
			trigger.siblings('.spinner').addClass("is-active");
			$.getJSON(
				ukdsAjaxHandler.ajaxurl,
				{
					action: 'ukds-fetch-products',
					nonce: ukdsAjaxHandler.nonce
				},
				function( response ) {
					trigger.siblings('.spinner').removeClass("is-active");
					var sel = $('[data-ui="FetchProducts"]');
					if (response.success) {
						sel.addClass("active");
						sel.find("h3").html(response.products.length + " Products")
						$("[data-ui='HideOnFetchProducts']").addClass("hidden");

						sel.children(".import-table-body").empty();
						var tbl = $("<table class='dataTable no-footer' />");
						tbl.append("<thead><tr><th>Product Name</th></tr></thead>");
						tbl.append("<tbody>");
						var m = 0;
						for (var i = 0; i < response.products.length; i++) {

						    var alreadyImported = false;
						    $.each(imports.products, function (p) {
						        if (imports.products[p].product_id == response.products[i].id) {
						            alreadyImported = true;
						            m++;
						            return false;
						        }
						    })
						    if (alreadyImported) continue;

							var field = $("<fieldset />");
							var label = $("<label for='product-" + response.products[i].id + "'>");
							var input = $("<input type='checkbox' id='product-" + response.products[i].id + "' name='product-" + response.products[i].id + "' value='1' />");
							var span = $("<span>" + response.products[i].name + "</span>");
							label.append(input);
							label.append(span);
							field.append(label);
							var tr = $("<tr />");
							var td = $("<td />");
							td.append(field);
							tr.append(td);
							tbl.append(tr);
						}
						if (i == m) {
						    sel.html(buildNotice("All Products have already been imported!", "success"));
						} else {
						    tbl.append("</tbody>");
						    sel.children(".import-table-body").append(tbl);
						    tbl.dataTable({
						        "scrollY": "350px",
						        "scrollCollapse": true,
						        "paging": false
						    });
						    $("[name='ltd_settings']").on("submit", function (e) {
						        var form = this;
						        var params = tbl.$('input').serializeArray();
						        $.each(params, function () {
						            if (!$.contains(document, form[this.name])) {
						                $(form).append(
                                           $('<input>')
                                              .attr('type', 'hidden')
                                              .attr('name', this.name)
                                              .val(this.value)
                                        );
						            }
						        });
						    })
						}
						
					}
				}
			);
		});
		$("input[name='FetchUpdateVenues']").click(function (e) {
		    e.preventDefault();
		    var trigger = $(this);
		    trigger.siblings('.spinner').addClass("is-active");
		    $.getJSON(
            ukdsAjaxHandler.ajaxurl,
            {
                action: 'ukds-fetch-venues',
                nonce: ukdsAjaxHandler.nonce,
                api: -1
            },
            function (response) {
                trigger.siblings('.spinner').removeClass("is-active");
                var sel = $('[data-ui="FetchVenues"]');
                if (response.success) {
                    sel.addClass("active");
                    $("[data-ui='HideOnFetchVenues']").addClass("hidden");

                    sel.children(".import-table-body").empty();
                    var tbl = $("<table class='dataTable no-footer' />");
                    tbl.append("<thead><tr><th>Venue Name</th></tr></thead>");
                    tbl.append("<tbody>");
                    for (var i = 0; i < response.venues.length; i++) {

                        var field = $("<fieldset />");
                        var label = $("<label for='update-venue-" + response.venues[i].id + "'>");
                        var input = $("<input type='checkbox' id='update-venue-" + response.venues[i].id + "' name='update-venue-" + response.venues[i].id + "' value='1' />");
                        var span = $("<span>" + response.venues[i].name + "</span>");
                        label.append(input);
                        label.append(span);
                        field.append(label);
                        var tr = $("<tr />");
                        var td = $("<td />");
                        td.append(field);
                        tr.append(td);
                        tbl.append(tr);
                    }

                    tbl.append("</tbody>");
                    sel.children(".import-table-body").append(tbl);
                    tbl.dataTable({
                        "scrollY": "350px",
                        "scrollCollapse": true,
                        "paging": false
                    });
                    $("[name='ltd_settings']").on("submit", function (e) {
                        var form = this;
                        var params = tbl.$('input').serializeArray();
                        $.each(params, function () {
                            if (!$.contains(document, form[this.name])) {
                                $(form).append(
                                   $('<input>')
                                      .attr('type', 'hidden')
                                      .attr('name', this.name)
                                      .val(this.value)
                                );
                            }
                        });
                    })
                }
            }
            );
		})
		$("input[name='FetchVenues']").click(function(e) {
			e.preventDefault();
			var trigger= $(this);
			trigger.siblings('.spinner').addClass("is-active");
			$.getJSON(
				ukdsAjaxHandler.ajaxurl,
				{
					action: 'ukds-fetch-venues',
					nonce: ukdsAjaxHandler.nonce
				},
				function( response ) {
					trigger.siblings('.spinner').removeClass("is-active");
					var sel = $('[data-ui="FetchVenues"]');
					if (response.success) {
						sel.addClass("active");
						sel.find("h3").html(response.venues.length + " Venues")
						$("[data-ui='HideOnFetchVenues']").addClass("hidden");

						sel.children(".import-table-body").empty();
						var tbl = $("<table class='dataTable no-footer' />");
						tbl.append("<thead><tr><th>Venue Name</th></tr></thead>");
						tbl.append("<tbody>");

						var m = 0;
						for (var i = 0; i < response.venues.length; i++) {

						    var alreadyImported = false;
						    $.each(imports.venues, function (v) {
						        if (imports.venues[v].venue_id == response.venues[i].id) {
						            alreadyImported = true;
						            m++;
						            return false;
						        }
						    })
						    if (alreadyImported) continue;

						    var field = $("<fieldset />");
							var label = $("<label for='venue-" + response.venues[i].id + "'>");
							var input = $("<input type='checkbox' id='venue-" + response.venues[i].id + "' name='venue-" + response.venues[i].id + "' value='1' />");
							var span = $("<span>" + response.venues[i].name + "</span>");
							label.append(input);
							label.append(span);
							field.append(label);
							var tr = $("<tr />");
							var td = $("<td />");
							td.append(field);
							tr.append(td);
							tbl.append(tr);
						}
						if (i == m) {
						    sel.html(buildNotice("All Venues have already been imported!", "success"));
						} else {
						    tbl.append("</tbody>");
						    sel.children(".import-table-body").append(tbl);
						    tbl.dataTable({
						        "scrollY": "350px",
						        "scrollCollapse": true,
						        "paging": false
						    });
						    $("[name='ltd_settings']").on("submit", function (e) {
						        var form = this;
						        var params = tbl.$('input').serializeArray();
						        $.each(params, function () {
						            if (!$.contains(document, form[this.name])) {
						                $(form).append(
                                           $('<input>')
                                              .attr('type', 'hidden')
                                              .attr('name', this.name)
                                              .val(this.value)
                                        );
						            }
						        });
						    })

						}
					}
				}
			);
		});

		$("[ukds-ui='SelFetchProducts']").each(function () {
		    var scope = $(this);
		    if (imports['products'].length > 0) {
		        scope.siblings(".spinner").remove();
		        populateDropdown(imports['products']);
		    } else {
		        $.getJSON(
                    ukdsAjaxHandler.ajaxurl,
                    {
                        action: 'ukds-fetch-products',
                        nonce: ukdsAjaxHandler.nonce,
                        api: 0
                    },
                    function (data) {
                        if (data.success) {
                            imports['products'] = data['products'];
                            scope.siblings(".spinner").remove();
                            populateDropdown(data.products)

                        } else {
                            
                        }

                    }
                );
		    }
            
		    var populateDropdown = function (data) {
		        scope.empty();
		        scope.append("<option value=''>Select a show</option>");
		        $.each(data, function (i) {
		            scope.append("<option value='" + data[i].id + "'>" + data[i].name + "</option>");
		        })
            }
		});

		$("[ukds-ui='SelFetchProducts']").change(function () {
		    $("[ukds-ui='SelFetchProducts-sync']").text($(this).val());
		    if ($(this).val() != "") {
		        $("#ProductShortcodeArea").slideDown();
		    } else {
		        $("#ProductShortcodeArea").slideUp();
		    }
		})

		$("[ukds-ui='imported']").each(function () {
		    var scope = $(this);
		    switch (scope.attr("ukds-type")) {
		        case "products" :
                case "venues" :
		            $.getJSON(
                        ukdsAjaxHandler.ajaxurl,
                        {
                            action: 'ukds-fetch-' + scope.attr("ukds-type"),
                            nonce: ukdsAjaxHandler.nonce,
                            api: -1
                        },
                        function (data) {
                            if (data.success) {
                                imports[scope.attr("ukds-type")] = data[scope.attr("ukds-type")];

                                scope.children("span").text(data[scope.attr("ukds-type")].length);
                                scope.children(".spinner").remove();

                                if (data[scope.attr("ukds-type")].length == 0) return false;

                                var ul = $("<ul style='display:none'/>");
                                $.each(imports[scope.attr("ukds-type")], function (i,item) {
                                    var li = $("<li><a href='" + item.permalink + "' target='_blank'>" + item.name + "</a></li>");
                                    ul.append(li);
                                });
                                scope.after(ul);
                                scope.on("click", function () {
                                    ul.slideToggle();
                                })
                                scope.append(" <span class='toggler'>+</span>");
                            } else {
                                scope.parent(".inline-notice").removeClass("notice-info").addClass("notice-error").html("Unable to check imported " + scope.attr('ukds-type') + ". Please try refreshing the page.");
                            }

                        }
                    );
		            break;
		        case "categories":

		            break;
		    }
		})
		
	});

})( jQuery );
