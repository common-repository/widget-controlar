// JavaScript Document

jQuery(document).ready(function() {
	/*----------SELECT FROM HOME, 404 & SEARCH----------*/
	jQuery("select.list.general option:selected").addClass("selected");
	jQuery("select.list.general option").live("click", function() {
		if(jQuery(this).hasClass("selected") == true) {
			jQuery(this).removeClass("selected").attr({"selected":false});
		} else {
			jQuery(this).addClass("selected").attr({"selected":true});
			var generalcopy = jQuery(this).clone();
		}
		jQuery("select.list.general option.selected").attr({"selected":true});
		if(jQuery(this).hasClass("selected") == true) {
			jQuery(this).closest("select").prepend(generalcopy);
			jQuery(this).remove();	
		}
	});
	/*----------SELECT CATEGORY OPTION RELATED POSTS WHEN CLICK ON CATEGORY OPTION----------*/
	jQuery("select.list.category option").live("click", function() {
		var value = jQuery(this).attr("value");
		if(jQuery(this).hasClass("selected") == true) {
			jQuery(this).removeClass("selected").attr({"selected":false});
		} else {
			jQuery(this).addClass("selected").attr({"selected":true});
			var catcopy = jQuery(this).clone();
		}
		jQuery("select.list.category option.selected").attr({"selected":true});
		if(jQuery(this).hasClass("selected") == true) {
			jQuery(this).closest("div.widgets_controller_box").find("select.list.posts option").each(function() {
				if(jQuery(this).attr("title") == value) {
					jQuery(this).addClass("selected").attr({"selected":true});
					var postcopy = jQuery(this).clone();
					jQuery(this).closest("select").prepend(postcopy);
					jQuery(this).remove();				
				}
			});
		} else {
			jQuery(this).closest("div.widgets_controller_box").find("select.list.posts option").each(function() {
				if(jQuery(this).attr("title") == value) {
					jQuery(this).removeClass("selected").attr({"selected":false});
				}
			});			
		}
		if(jQuery(this).hasClass("selected") == true) {
			jQuery(this).closest("select").prepend(catcopy);
			jQuery(this).remove();	
		}
	});
	/*----------SELECT POSTS OPTION----------*/
	jQuery("select.list.posts option").live("click", function() {
		if(jQuery(this).hasClass("selected") == true) {
			jQuery(this).removeClass("selected").attr({"selected":false});
		} else {
			jQuery(this).addClass("selected").attr({"selected":true});
			var postscopy = jQuery(this).clone();
		}
		jQuery("select.list.posts option.selected").attr({"selected":true});
		if(jQuery(this).hasClass("selected") == true) {
			jQuery(this).closest("select").prepend(postscopy);
			jQuery(this).remove();	
		}
	});
	/*----------SELECT PAGES OPTION----------*/
	jQuery("select.list.pages option").live("click", function() {
		if(jQuery(this).hasClass("selected") == true) {
			jQuery(this).removeClass("selected").attr({"selected":false});
		} else {
			jQuery(this).addClass("selected").attr({"selected":true});
			var pagescopy = jQuery(this).clone();
		}
		jQuery("select.list.pages option.selected").attr({"selected":true});
		if(jQuery(this).hasClass("selected") == true) {
			jQuery(this).closest("select").prepend(pagescopy);
			jQuery(this).remove();	
		}
	});
	/*----------ACTIVE MAIN CHECKBOX----------*/
	jQuery("input.checkbox.widgets_controller").live("change", function() {
		if(jQuery(this).attr("checked") == "checked") {
			jQuery(this).closest("p").next("div.widgets_controller_box").removeClass("none");
		} else {
			jQuery(this).closest("p").next("div.widgets_controller_box").addClass("none");
			jQuery(this).closest("p").next("div.widgets_controller_box").find("input.checkbox.activecategory").attr({"checked":false});
			jQuery(this).closest("p").next("div.widgets_controller_box").find("input.checkbox.activepages").attr({"checked":false});
			jQuery(this).closest("p").next("div.widgets_controller_box").find("select.list.category").addClass("none");
			jQuery(this).closest("p").next("div.widgets_controller_box").find("select.list.posts").addClass("none");
			jQuery(this).closest("p").next("div.widgets_controller_box").find("select.list.pages").addClass("none");
		}
	});
	/*----------ACTIVE CATEGORY CHECKBOX----------*/
	jQuery("input.checkbox.activecategory").live("change", function() {
		var current_ele = jQuery(this);
		if(jQuery(this).attr("checked") == "checked") {
			jQuery(this).next("label").after('<img class="loading" src="'+PLUGINPATH+'/img/loading.gif" />');
			var current_cat = jQuery(this).attr("title");
			var current_posts = jQuery(this).attr("newtitle");
			jQuery.post ( ajaxurl, {
				action : 'myajax-submit',
				data:"getcategory-"+current_cat+'-'+current_posts
			}, function( data ) {
				var data = data.split("|||");
				jQuery(current_ele).closest("div.widgets_controller_box").find("select.list.category").html(data[0]);
				jQuery(current_ele).closest("div.widgets_controller_box").find("select.list.posts").html(data[1]);
				jQuery(current_ele).closest("div.widgets_controller_box").find("select.list.category").removeClass("none");
				jQuery(current_ele).closest("div.widgets_controller_box").find("select.list.posts").removeClass("none");
				jQuery(current_ele).closest("div.widgets_controller_box").find("img.loading").remove();
				jQuery("select.list.category option:selected").addClass("selected");
				jQuery("select.list.category option.selected").each(function() {
					var copy = jQuery(this).clone();
					jQuery(this).closest("select").prepend(copy);
					jQuery(this).remove();
				});
				jQuery("select.list.posts option:selected").addClass("selected");
				jQuery("select.list.posts option.selected").each(function() {
					var copy = jQuery(this).clone();
					jQuery(this).closest("select").prepend(copy);
					jQuery(this).remove();
				});
			});
		} else {
			jQuery(this).closest("div.widgets_controller_box").find("select.list.category").addClass("none");
			jQuery(this).closest("div.widgets_controller_box").find("select.list.posts").addClass("none");
		}
	});
	/*----------ACTIVE PAGES CHECKBOX----------*/
	jQuery("input.checkbox.activepages").live("change", function() {
		var current_ele = jQuery(this);
		if(jQuery(this).attr("checked") == "checked") {
			jQuery(this).next("label").after('<img class="loading" src="'+PLUGINPATH+'/img/loading.gif" />');
			var current_pages = jQuery(this).attr("title");
			jQuery.post ( ajaxurl, {
				action : 'myajax-submit',
				data:"getpages-"+current_pages
			}, function( data ) {
				jQuery(current_ele).closest("div.widgets_controller_box").find("select.list.pages").html(data);
				jQuery(current_ele).closest("div.widgets_controller_box").find("select.list.pages").removeClass("none");
				jQuery(current_ele).closest("div.widgets_controller_box").find("img.loading").remove();
				jQuery("select.list.pages option:selected").addClass("selected");
				jQuery("select.list.pages option.selected").each(function() {
					var copy = jQuery(this).clone();
					jQuery(this).closest("select").prepend(copy);
					jQuery(this).remove();
				});
			});
		} else {
			jQuery(this).closest("div.widgets_controller_box").find("select.list.pages").addClass("none");
		}
	});
	/*----------ON PAGE LOAD----------*/
	jQuery("input.checkbox.widgets_controller").trigger("change");
	jQuery("input.checkbox.activecategory").attr({"checked":false});
	jQuery("input.checkbox.activepages").attr({"checked":false});
});