<?php
require_once dirname(__FILE__) . '/../../../core/php/core.inc.php';
include_file('core', 'authentification', 'php');
if (!isConnect()) {
    include_file('desktop', '404', 'php');
    die();
}
?>
<form class="form-horizontal">
    <fieldset>
        <div class="form-group">
            <label class="col-lg-2 control-label">Adresse IP de mochad :</label>
            <div class="col-lg-3">
                <input class="configKey form-control" data-l1key="mochadHost" />
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">Port de mochad </label>
            <div class="col-lg-3">
                <input class="configKey form-control" data-l1key="mochadPort" />
            </div>
        </div>
		<div class="form-group">
			<label class="col-lg-2 control-label">Ajouter automatiquement les périphériques détectée</label>
			<div class="col-lg-3">
				<input type="checkbox" class="configKey bootstrapSwitch" data-label-text="{{Automatique}}" data-l1key="autoAddDevice"/>
			</div>
		</div>
    </fieldset>
</form>