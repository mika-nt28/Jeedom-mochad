$('#BusMoniteur').on('click', function() {
    $('#md_modal').dialog({
		title: "{{Bus Moniteur}}",
		height: 600,
		width: 550});
    $('#md_modal').load('index.php?v=d&modal=mochad.busmoniteur&plugin=mochad&type=mochad').dialog('open');
});
$(function() {	
	$('#Unite').change(function() {
		$('.eqLogicAttr[data-l1key=logicalId]').val($(this).val()+'-'+$('#Code').val())
		}); 
	$('#Code').change(function() {
		$('.eqLogicAttr[data-l1key=logicalId]').val($('#Unite').val()+'-'+$(this).val())
		}); 
	$('.eqLogicAttr[data-l1key=logicalId]').change(function() {
		if  ($(this).val().indexOf(":")==-1)
			{
			var selectsValue=$(this).val().split('-');
			if (selectsValue[0]!=undefined && selectsValue[1]!=undefined)
				{
				$('#Unite option[value="'+selectsValue[0]+'"]').prop('selected', true);
				$('#Code option[value="'+selectsValue[1]+'"]').prop('selected', true);
				}
			}
		});
	$('#table_cmd tbody').delegate('.cmd .cmdAttr[data-l1key=configuration][data-l2key=MochadCommandeType]', 'change', function() {
		switch($(this).val())
			{
			case "rfsec":
				$("#UniteEquipement").hide();
				$("#CodeEquipement").hide();
				$("#AdresseEquipement").show();
				break;
			default:
				$("#UniteEquipement").show();
				$("#CodeEquipement").show();
				$("#AdresseEquipement").hide();
				break;
			}
	});
	/*$('#table_cmd tbody').delegate('.cmd .cmdAttr[data-l1key=configuration][data-l2key=MochadCommande]', 'change', function() {
		switch($(this).val())
			{
			case "on-off":
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('other');
				break;
			case "on":
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('other');
				break;
			case "off":
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('other');
				break;
			case "dim":
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('other');
				break;
			case "bright":
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('other');
				break;
			case "xdim":
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('slider');
				break;
			case "all_lights_on":
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('other');
				break;
			case "all_lights_off":
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('other');
				break;
			case "all_units_off":
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('other');
				break;
			default:
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=type]').val('action');
				$(this).closest('.cmd').find('.cmdAttr[data-l1key=subType]').val('other');
				break;
			};
		}); */
	$('#table_cmd tbody').delegate('.cmd .cmdAttr[data-l1key=configuration][data-l2key=MochadCommandeType]', 'change', function() {

		if ($(this).val() == "pl")
			{
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=MochadCommande]').html('');
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=MochadCommande]')
				.append($('<option value="on-off">')
					.text('{{on|off}}'))
				.append($('<option value="on">')
					.text('{{on}}'))
				.append($('<option value="off">')
					.text('{{off}}'))
				.append($('<option value="dim">')
					.text('{{dim}}'))
				.append($('<option value="bright">')
					.text('{{bright}}'))
				.append($('<option value="xdim">')
					.text('{{xdim}}'))
				.append($('<option value="all_lights_on">')
					.text('{{all_lights_on}}'))		
				.append($('<option value="all_lights_off">')
					.text('{{all_lights_off}}'))		
				.append($('<option value="all_units_off">')
					.text('{{all_units_off}}'));
			}
		else
			{	
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=MochadCommande]').html('');
			$(this).closest('.cmd').find('.cmdAttr[data-l1key=configuration][data-l2key=MochadCommande]')
				.append($('<option value="on-off">')
					.text('{{on|off}}'))
				.append($('<option value="on">')
					.text('{{on}}'))
				.append($('<option value="off">')
					.text('{{off}}'))
				.append($('<option value="dim">')
					.text('{{dim}}'))
				.append($('<option value="bright">')
					.text('{{bright}}'));
			}
		}); 
	});

function addCmdToTable(_cmd) {
    if (!isset(_cmd)) {
        var _cmd = {};
    }
    if (!isset(_cmd.configuration)) {
        _cmd.configuration = {};
    }
	var tr =$('<tr class="cmd" data-cmd_id="' + init(_cmd.id) + '">');
	tr.append($('<td>')
		.append($('<input class="cmdAttr form-control input-sm" data-l1key="id" style="display : none;">'))
		.append($('<input class="cmdAttr form-control input-sm" data-l1key="name" value="' + init(_cmd.name) + '">'))
		.append($('<input class="cmdAttr form-control input-sm" data-l1key="unite" value="" type="hidden">')));
	tr.append($('<td>')
		.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="MochadCommandeType">>')
			.append($('<option value="">')
				.text('{{Choisir le type}}'))
			.append($('<option value="pl">')
				.text('{{Power Line}}'))
			.append($('<option value="rf">')
				.text('{{Radio Frequency}}'))
			.append($('<option value="rfsec">')
				.text('{{Radio Frequency Security}}'))));
	tr.append($('<td>')
		.append($('<select class="cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="MochadCommande">')
			.append($('<option value="">')
				.text('{{Choisir votre commande}}'))
			.append($('<option value="on-off">')
				.text('{{on|off}}'))
			.append($('<option value="on">')
				.text('{{on}}'))
			.append($('<option value="off">')
				.text('{{off}}'))
			.append($('<option value="dim">')
				.text('{{dim}}'))
			.append($('<option value="bright">')
				.text('{{bright}}'))
			.append($('<option value="xdim">')
				.text('{{xdim}}'))
			.append($('<option value="all_lights_on">')
				.text('{{all_lights_on}}'))		
			.append($('<option value="all_lights_off">')
				.text('{{all_lights_off}}'))		
			.append($('<option value="all_units_off">')
				.text('{{all_units_off}}'))));
	tr.append($('<td>')
			.append($('<div style="width : 40%;display : inline-block;">')
			.append($('<span class="type" type="' + init(_cmd.type) + '">')
				.append(jeedom.cmd.availableType()))
			.append($('<span class="subType" subType="'+init(_cmd.subType)+'">')))
		.append($('<div style="width : 40%;display : inline-block;">')
			.append($('<span>')
				.append($('<input type="checkbox" class="cmdAttr bootstrapSwitch" data-size="mini" data-label-text="{{Historiser}}" data-l1key="isHistorized" checked/>')))
			.append($('</br>'))
			.append($('<span>')
				.append($('<input type="checkbox" class="cmdAttr bootstrapSwitch" data-size="mini" data-label-text="{{Afficher}}" data-l1key="isVisible" checked/>')))
			.append($('</br>'))
			.append($('<span>')
				.append($('<input type="checkbox" class="cmdAttr bootstrapSwitch" data-size="mini" data-label-text="{{Evénement}}" data-l1key="eventOnly" checked/>'))))
		.append($('<div style="display : inline-block;">')
			.append($('<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="minValue" placeholder="{{Min}}" title="{{Min}}" style="width : 40%;display : inline-block;">'))
			.append($('<input class="tooltips cmdAttr form-control input-sm" data-l1key="configuration" data-l2key="maxValue" placeholder="{{Max}}" title="{{Max}}" style="width : 40%;display : inline-block;">'))));
	var parmetre=$('<td>')
		.append($('<i class="fa fa-minus-circle pull-right cmdAction cursor" data-action="remove">'));
	if (is_numeric(_cmd.id)) {
		parmetre.append($('<a class="btn btn-default btn-xs cmdAction" data-action="test">')
			.append($('<i class="fa fa-rss">')
				.text('{{Tester}}')));
      
		parmetre.append($('<a class="btn btn-default btn-xs cmdAction expertModeVisible" data-action="configure">')
			.append($('<i class="fa fa-cogs">')));
		parmetre.append($('<a class="btn btn-default btn-xs cmdAction expertModeVisible tooltips" data-action="copy" title="{{Dupliquer}}">')
			.append($('<i class="fa fa-files-o">')));
	}
	tr.append(parmetre);
	$('#table_cmd tbody').append(tr);
	$('#table_cmd tbody tr:last').setValues(_cmd, '.cmdAttr');

	jeedom.cmd.changeType($('#table_cmd tbody tr:last'), init(_cmd.subType));
	initTooltips();
	}