;
if (typeof AC ==="undefined") {AC = function() {}};
if (typeof AC.Crud ==="undefined") {AC.Crud = function() {}};

AC.Crud.List = function() {

	var infoHandler		= AC.Core.Alert.show;
	var warningHandler	= AC.Core.Alert.warning;
	var errorHandler	= AC.Core.Alert.error;

	var searchTimer;
	var afterUpdateViewCallback;
	
	var viewTypeClasses = 'list grid';

	return {

		init: function() {
			$('form.ACCrudList tbody tr').live('click', AC.Crud.List.trHandler);
			$('form.ACCrudList tbody tr').live('dblclick', AC.Crud.List.editHandler);
            
            $('form.ACCrudList thead a.listView').live('click', AC.Crud.List.activateListView);
            $('form.ACCrudList thead a.gridView').live('click', AC.Crud.List.activateGridView);
            $('form.ACCrudList thead a.filterToggle').live('click', AC.Crud.List.toggleFilters);

			$('form.ACCrudList input.id').live('click', AC.Crud.List.checkboxHandler);
			$('form.ACCrudList input.toggleSelect').live('click', AC.Crud.List.toggleSelectHandler);
			$('form.ACCrudList td.toolbar a.new').live('click', AC.Crud.List.newHandler);
			$('form.ACCrudList td.toolbar a.delete').live('click', AC.Crud.List.multipleDeleteHandler);
			$('form.ACCrudList td.buttons a.edit').live('click', AC.Crud.List.editHandler);
			$('form.ACCrudList td.buttons a.delete').live('click', AC.Crud.List.deleteHandler);

			$('form.ACCrudList td.buttons a.prev').live('click', AC.Crud.List.prevHandler);
			$('form.ACCrudList td.buttons a.next').live('click', AC.Crud.List.nextHandler);
            $('form.ACCrudList td.buttons a.page').live('click', AC.Crud.List.pageHandler);
			$('form.ACCrudList td.buttons select.pageSize').live('change', AC.Crud.List.pageSizeHandler);
			$('form.ACCrudList th a.order').live('click', AC.Crud.List.orderHandler);
			$('form.ACCrudList th select.filter').live('change', AC.Crud.List.filterSelectHandler);
			$('form.ACCrudList th input[name=\'view[search]\']').live('keypress', AC.Crud.List.searchBoxHandler);
			$('form.ACCrudList th input[name=\'view[search]\']').live('search', AC.Crud.List.searchBoxHandler);
			$('form.ACCrudList th a.search').live('click', AC.Crud.List.searchButtonHandler);

			$('form.ACCrudList').bind('result', function(events, data) {
				//console.log(data);
			});

			AC.Crud.List.initMove();
		},

		initMove: function() {
			if ($('form.ACCrudList tbody td.sort').length) {
				$('form.ACCrudList table tbody').tableDnD({
					onDrop: AC.Crud.List.onSortHandler,
					serializeParamName: 'id',
					serializeRegexp: /[^\-]*$/,
					dragHandle: '.sort'
				});
			}
		},

		trHandler: function(e) {
			e.stopPropagation();
			//e.preventDefault();
			var row = $(this);
			var checkbox = row.find('input[type=checkbox]');
			checkbox.attr('checked', !checkbox.attr('checked'));
			AC.Crud.List.checkboxHandler.call(checkbox, e);
			//return false;
		},

		checkboxHandler: function(e) {
			e.stopPropagation();
			var form = $(this).parents('form');
			var data = form.serializeArray();
			form.find('td.toolbar .delete').removeClass('show');
			form.find('input.toggleSelect').attr('checked', false);
			var count = 0;
			for (elm in data) {
				if (data[elm].name == 'id[]') {
					form.find('input.toggleSelect').css('opacity', 0.45);
					form.find('input.toggleSelect').attr('checked', true);
					form.find('td.toolbar .delete').addClass('show');
					count++;
				}
			}
			if (count == $('input.id').length || count == 0) {
				form.find('input.toggleSelect').css('opacity', 1);
			}
			AC.Crud.List.updateCheckRows(this);
		},

		toggleSelectHandler: function() {
			var form = $(this).parents('form');
			var data = form.serializeArray();

			var count = 0;
			for (elm in data) {
				if (data[elm].name == 'id[]') {
					count++;
				}
			}

			form.find('input.toggleSelect').css('opacity', 1);
			if (count > 0) {
				form.find('input.id').attr('checked', false);
				form.find('td.toolbar .delete').removeClass('show');
			} else {
				form.find('input.id').attr('checked', true);
				form.find('td.toolbar .delete').addClass('show');
			}
			AC.Crud.List.updateCheckRows(this);
		},
			
		updateCheckRows: function(node) {
			var form = $(node).parents('form');
			form.find('input.id').each(function() {
				if ($(this).is(':checked')) {
					$(this).parents('tr').addClass('checked');
				} else {
					$(this).parents('tr').removeClass('checked');
				}
			});
		},

		newHandler: function() {
			window.location.href = window.location.pathname + '?new';
		},

		editHandler: function(e) {
			e.stopPropagation();
			if ($(this)[0].nodeName == 'TR' || $(this)[0].nodeName == 'tr') {
				var row = $(this);
			} else {
				var row = $(this).parents('tr');
			}
			var id = row.find('input[type=checkbox]').attr('value');
			var form = $(this).parents('form');

			window.location.href = window.location.pathname + '?edit=' + id;
		},

		multipleDeleteHandler: function(e, id) {
			id = id || false;
			var form = $(this).parents('form');
			var self = this;

			form.find('input.operation').val('delete');

			var options = {
				operation	: 'delete',
				crudId		: form.attr('id')
			};
			var url = form.attr('action') + "?" + $.param(options);
			var data = form.serializeArray();

			var count = 0;
			for (elm in data) {
				if (data[elm].name == 'id[]') {
					if (id !== false && data[elm].value != id) {
						delete data[elm];
					} else {
						count++;
					}
				}
			}

			if (id !== false && count == 0) {
				data.push({
					name: 'id[]',
					value: id
				});
				count = 1;
			}

			if (count > 0 && confirm(i18n.confirmDelete + ' (' + count + ' item/items)')) {
				$.post(url, data, function(response) {
					if (response.operation === 'delete' && response.success === true) {
						for (elm in data) {
							if (data[elm].name == 'id[]') {
								form.find('input.id[value=' + data[elm].value + ']').parents('tr').fadeOut(function() {
									$(this).remove();
								});
							}
						}
						AC.Core.Alert.flash(response.message);
						form.find('td.toolbar .button.multiple').removeClass('show');
					} else {
						errorHandler('Not all records could be deleted');
						AC.Crud.List.updateView(self);
					}
				}, 'json').error(function(jqXHR, message, exception) {
					$('body').removeClass('loading');
					errorHandler(i18n.requestError + ' (' + exception + ')');
				});
			}
		},

		onSortHandler: function(table, row) {
			var form = $(table).parents('form');

			form.find('input.operation').val('sort');

			var options = {
				operation	: 'sort',
				crudId		: form.attr('id')
			};
			var url = form.attr('action') + "?" + $.param(options);
			var data = $(table).tableDnDSerialize();

			// Add CSRF token
			data = data + '&_token=' + form.find('input[name=\'_token\']').val();

			// Add sort fieldname
			data = data + '&field=' + form.find('td.sort:eq(0)').attr('data-field');

			$.post(url, data, function(response) {
				if (response.operation === 'sort' && response.success === true) {
					AC.Core.Alert.flash(response.message);
				}
			}, 'json').error(function(jqXHR, message, exception) {
				$('body').removeClass('loading');
				errorHandler(i18n.requestError + ' (' + exception + ')');
			});
		},

		deleteHandler: function(e) {
			e.stopPropagation();
			var self = this;
			var row = $(this).parents('tr');
			var id = row.find('input[type=checkbox]').attr('value');
			AC.Crud.List.multipleDeleteHandler.call(self, e, id);
		},

		prevHandler: function(e) {
			if ($(this).hasClass('disabled')) {
				return;
			}
			var form = $(this).parents('form');
			var $page = form.find('input[name=\'view[page]\']');
			$page.val(parseInt($page.val()) - 1);
			AC.Crud.List.updateView(this);
		},

		nextHandler: function(e) {
			if ($(this).hasClass('disabled')) {
				return;
			}
			var form = $(this).parents('form');
			var $page = form.find('input[name=\'view[page]\']');
			$page.val(parseInt($page.val()) + 1);
			AC.Crud.List.updateView(this);
		},
                
        pageHandler: function(e) {
            if ($(this).hasClass('active')) {
				return;
			}
			var form = $(this).parents('form');
			var $page = form.find('input[name=\'view[page]\']');
			$page.val(parseInt($(this).text()));
			AC.Crud.List.updateView(this);
        },
		
		pageSizeHandler: function(e) {
			AC.Crud.List.resetPage(this);
			AC.Crud.List.updateView(this);
		},

		orderHandler: function(e) {
			var form = $(this).parents('form');
			var $orderBy = form.find('input[name=\'view[orderBy]\']');
			var $orderDir = form.find('input[name=\'view[orderDir]\']');
			$orderBy.val($(this).attr('data-orderBy'));
			$orderDir.val($(this).attr('data-orderDir'));
			AC.Crud.List.resetPage(this);
			AC.Crud.List.updateView(this);
		},

		filterSelectHandler: function(e) {
			AC.Crud.List.resetPage(this);
			AC.Crud.List.updateView(this);
		},

		searchBoxHandler: function(e) {
			var self = this;
			if (e.type == 'search' && !$(this).val()) {
				AC.Crud.List.searchButtonHandler.call(this, e, focus);
			} else if (e.which && e.which == 13) {
				e.preventDefault();
				AC.Crud.List.searchButtonHandler.call(this, e, focus);
			} else {
				clearTimeout(searchTimer);
				searchTimer = setTimeout(function() {
					AC.Crud.List.searchButtonHandler.call(self, e, focus);
				}, 1000);
			}
		},

		searchButtonHandler: function(e, c) {
			clearTimeout(searchTimer);
			AC.Crud.List.resetPage(this);
			AC.Crud.List.updateView(this, c);
		},
			
		resetPage: function(node) {
			var form = $(node).parents('form');
			var $page = form.find('input[name=\'view[page]\']');
			$page.val(1);
		},

		updateView: function(node, c) {
			var form = $(node).parents('form');
			var data = form.serializeArray();

			var count = 0;
			for (var elm in data) {
				if (data[elm].name.substr(0, 5) !== 'view[') {
					delete data[elm];
				}
			}

			form.find('tbody').animate({
				opacity: 0
			}, 'fast');

			data.push({
				name: 'output',
				value: 'table'
			});
			var url = document.location.href;

			$.get(url, data, function(response) {
				form.html($(response).find('form.ACCrudList').html());
				form.find('tbody').css({opacity: 1});
				AC.Crud.List.initMove();
				if (typeof c == 'function') {
					c();
				}
				if (typeof afterUpdateViewCallback == 'function') {
					afterUpdateViewCallback();
				}
				$.scrollTo(form, 800, { axis: 'y', offset: -70 });
			}, 'html').error(function(jqXHR, message, exception) {
				$('body').removeClass('loading');
				errorHandler(i18n.requestError + ' (' + exception + ')');
			});
		},

		afterUpdateView: function(callback) {
			afterUpdateViewCallback = callback;
		},
                
        activateListView: function(e) {
			var form = $(this).parents('form');
			AC.Crud.List.activateView(this, 'list', form);
        },
                
        activateGridView: function(e) {
			var form = $(this).parents('form');
			AC.Crud.List.activateView(this, 'grid', form);
        },
				
		activateView: function(node, view, form) {
//			form.find('table').removeClass(viewTypeClasses).addClass(view);
			form.find('input[name=\'view[viewType]\']').val( view );
			AC.Crud.List.updateView(node);
		},
                
        toggleFilters: function(e) {
			var form = $(this).parents('form');
			
			form.find('tr.filters').toggleClass('visible');
			form.find('input[name=\'view[filterVisible]\']').val( $('tr.filters').hasClass('visible') ? '1' : '0' );
        }

	};
}();

$(document).ready(function() {
	AC.Crud.List.init();
});

/**
 * jQuery.ScrollTo - Easy element scrolling using jQuery.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 5/25/2009
 * @author Ariel Flesler
 * @version 1.4.2
 *
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 */
;(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);