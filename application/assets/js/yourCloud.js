var Your_cloud = {
	user_configuration: {
		sort_type: 1,
	},
	base_url: 'localhost',
	dropzone_file_template: '<div class="row upload-item"><div class="col-12 action text-right"><button data-dz-remove class="btn btn-cancel">Anuluj</button></div><div class="col-2 icon"><img src="" alt=""></div><div class="col-10 text-right file-details"><div class="row"><div data-dz-name class="col-12 text-right">Nazwa pliku</div><div data-dz-size class="col-12 text-right">6.5MB</div></div></div><div class="w-100"><div class="progress"><div data-dz-uploadprogress class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div></div></div></div>',
	object_template: '<div class="col-3 col-sm-2 col-md-2 col-lg-2 col-xl-1 mb-3 object-container"><div class="drive-object" data-id="" data-name="" data-type="" data-clicked="false"></div></div>',
	object_property_template: '<div class="row property"><div class="col-5 property-name">Typ</div><div class="col-7 property-value">Folder</div></div>',
	add_object: null,
	get_object_properties: null,
	refresh_objects: null,
	objects_container: '#drive-content',
	sort_functions: {
		selected: 'asc',
		asc: function(a, b)
		{
			if(a['name'] < b['name'])
			{
				return -1;
			}
			else
			{
				return 1;
			}
		},
		desc: function(a, b)
		{
			if(a['name'] > b['name'])
			{
				return -1;
			}
			else
			{
				return 1;
			}
		}
	},
	callbacks: {
		object_click: null,
		object_create: null,
		object_rename: null,
		object_delete: null,
		object_share: null,
		sorting_select: null,
		//folder_create: null
	},
	popout: null,
};


Your_cloud.popout = function(msg)
{
	$('#modal-popout .modal-body').html(msg);
	$('#modal-popout').modal('show');
}

Your_cloud.add_object = function(object_data)
{
	// object_data = $.parseJSON(object_data);
	// if(typeof object_data === 'undefined')
	// {
	// 	alert('Błędne dane w add_object');
	// 	return FALSE;
	// }

	var object = $(Your_cloud.object_template).find('.drive-object');

	// if(typeof object_data.img_src === 'undefined') return FALSE;

	// if(typeof object_data.img_alt === 'undefined') return FALSE;

	// if(typeof object_data.name === 'undefined') return FALSE;

	// if(typeof object_data.id === 'undefined') return FALSE;

	// if(typeof object_data.type === 'undefined') return FALSE;

	object.append('<img src="' + object_data.icon_src + '" alt="' + object_data.icon_alt + '">');
	object.append('<p>' + object_data.name + '</p>');

	object.attr('data-type', object_data.type);
	object.attr('data-name', object_data.name);
	object.attr('data-id', object_data.id);
	object.attr('data-owner', object_data.owner_id);
	object.attr('data-sharing', object_data.sharing);

	if(typeof object_data.shared !== 'undefined')
	{
		object.attr('data-shared', object_data.shared);
	}

	$(Your_cloud.objects_container).append(object.parent());
}

Your_cloud.refresh_objects = function() 
{
	$(Your_cloud.objects_container).html('');

	$.get('', {action: 'get_all_objects'}).done(function(data)
	{
		data = JSON.parse(data);
	
		if(typeof data === 'undefined')
		{
			alert('Błędne dane z get_folders');
			return FALSE;
		}
	
		var files = $.map(data['files'], function(value, index) {
	    	return [value];
		});
	
		var folders = $.map(data['folders'], function(value, index) {
	    	return [value];
		});
	
		var sort_func = Your_cloud.sort_functions.selected;
	
		files.sort(Your_cloud.sort_functions[sort_func]);
		folders.sort(Your_cloud.sort_functions[sort_func]);
	
		$.each(folders, function(key, value) {
			Your_cloud.add_object(value);
		});
	
		$.each(files, function(key, value) {
			Your_cloud.add_object(value);
		});

		$('[data-sort-function]').removeClass('active');
		$('[data-sort-function="'+ sort_func +'"]').addClass('active');
	});

	
}

Your_cloud.callbacks.object_click = function(e)
{
	var obj = $(e.target);

	if(obj.hasClass('drive-object'))
	{
		// OK
	}
	else if(obj.parent().hasClass('drive-object'))
	{
		obj = obj.parent();
	}
	else
	{
		return;
	}

	function set_property_object(name, value)
	{
		var property = $(Your_cloud.object_property_template);

		property.find('.property-name').html(name);
		property.find('.property-value').html(value);

		$('.preview-properties').append(property);
	}

	if($(obj).attr('data-clicked') == 'true')
	{
		if(obj.data('type') == '2')
		{
			var new_url = '/' + $(obj).data('name');
		}
		else
		{
			var new_url = '?action=download_file&id=' + $(obj).data('id');
		}

		window.location.href += new_url;
	}
	else
	{
		e.preventDefault();

		// Object backlight
		$('.drive-object').removeClass('active');

		$(obj).addClass('active');

		$.get('', {action: 'object_properties', id: $(obj).data('id')}).always(function(data) {
			var obj_props = JSON.parse(data);

			$('.preview-properties').html('');

			$('.preview-img').find('img').attr('src', obj_props['icon-src']);
			$('.preview-img').find('img').attr('alt', obj_props['icon-alt']);

			// Show manage menu
			$('.manage-menu').css('display', 'block');

			$.each(obj_props, function(name, value) {
				if((name == 'icon-src') || (name == 'icon-alt'))
				{
					return true;
				}

				

				set_property_object(name, value, '.preview-properties');
			});


		});

		$('.drive-object').attr('data-clicked', 'false');
		$(obj).attr('data-clicked', 'true');
	}
}

Your_cloud.callbacks.object_create = function(e)
{
	var type = $(this).data('create');
	var modal_id = '#corner-menu-modal-'+ type;

	var name = encodeURI($(modal_id +' input').val());

	$.get('', {action: 'create_'+ type, name: name}).always(function(data) {
		if(data == 'success')
		{
			Your_cloud.refresh_objects();
			$(modal_id +' input').val('');
			$(modal_id).modal('hide');
		}
		else
		{
			$(modal_id +' .message').html(data);
		}
	});
}

Your_cloud.callbacks.object_rename = function()
{
	var id = $('.drive-object.active').data('id');
	var type = $('.drive-object.active').data('type');
	var name = $('.drive-object.active').data('name');

	$('#modal-rename').modal('show');

	$('#modal-rename button[data-action="yes"]').unbind();

	$('#modal-rename input').val(name);

	$('#modal-rename button[data-action="yes"]').click(function() {
		name = $('#modal-rename input').val();

		$.get('', {action: 'rename_object', id: id, 'new_name': name}).always(function(data) {
			if(data == 'success')
			{
				$('#modal-rename').modal('hide');

				Your_cloud.refresh_objects();
			}
			else
			{
				$('#modal-rename .message').html(data);
			}
		})
	});
}

Your_cloud.callbacks.object_share = function()
{
	var id = $('.drive-object.active').data('id');
	var type = $('.drive-object.active').data('type');
	var shared = $('.drive-object.active').data('shared');

	$('#modal-share').modal('show');

	$('#modal-share button[data-action="yes"]').unbind();

	$('#modal-share button[data-action="yes"]').click(function() {
		user_id = $('#modal-share input').val();

		$.get('/api/share_object', {object_id: id, user_id: user_id}).always(function(data) {
			if(data == 'success')
			{
				$('#modal-share').modal('hide');

			}
			else if(data != 'error')
			{
				$('#modal-share').modal('hide');
				Your_cloud.popout(data);
			}
			else
			{
				$('#modal-share').modal('hide');
			}

			$('#modal-share input').val('');
		})
	});
}

Your_cloud.callbacks.object_delete = function()
{
	var id = $('.drive-object.active').data('id');
	var type = $('.drive-object.active').data('type');
	var name = $('.drive-object.active').data('name');

	$('#modal-confirm').modal('show');

	$('#modal-confirm button[data-action="yes"]').unbind();

	$('#modal-confirm button[data-action="yes"]').click(function() {
		
		$.get('', {action: 'delete_object', id: id}).always(function(data) {
			$('#modal-confirm').modal('hide');

			Your_cloud.refresh_objects();
		})
	});
}

Your_cloud.callbacks.sorting_select = function(e) 
{
	var target = $(e.target);

	if(target.is('i'))
	{
		target = target.parent();
	}

	Your_cloud.sort_functions.selected = target.data('sort-function');
	Your_cloud.refresh_objects();
}


$('#drive-content').click(Your_cloud.callbacks.object_click);


Your_cloud.refresh_objects();
//Your_cloud.add_object('{"icon_src":"test","icon_alt":"test","id":1,"type":1,"name":"lul"}');

$('.corner-menu-modal button[data-create]').click(Your_cloud.callbacks.object_create);

$('.manage-menu').click(function(e)
{
	var action = $(e.target).data('action');
	if(typeof Your_cloud.callbacks['object_'+action] === 'undefined')
	{
		return;
	}

	Your_cloud.callbacks['object_'+action]();
});

$('#sort-button .dropdown-menu').click(Your_cloud.callbacks.sorting_select);
