<?php
if (!isConnect('admin')) {
    throw new Exception('{{401 - Accès non autorisé}}');
}
sendVarToJS('eqType', 'mochad');
$eqLogics = eqLogic::byType('mochad');
?>
	
<div class="row row-overflow">
    <div class="col-md-2">
        <div class="bs-sidebar">
            <ul id="ul_eqLogic" class="nav nav-list bs-sidenav">
                <a class="btn btn-default  btn-sm tooltips" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" id="BusMoniteur" title="Bus Moniteur" ><i class="fa fa-archive"></i> {{Bus Monitor}}</a>
                <a class="btn btn-default eqLogicAction" style="width : 100%;margin-top : 5px;margin-bottom: 5px;" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter un équipement}}</a>
                <li class="filter" style="margin-bottom: 5px;"><input class="filter form-control input-sm" placeholder="{{Rechercher}}" style="width: 100%"/></li>
                <?php
                foreach (eqLogic::byType('mochad') as $eqLogic) {
					echo '<li class="cursor li_eqLogic" data-eqLogic_id="' . $eqLogic->getId() . '"><a>' . $eqLogic->getHumanName(true) . '</a></li>';
                }
                ?>
            </ul>
        </div>
    </div>
	<div class="col-lg-10 col-md-9 col-sm-8 eqLogicThumbnailDisplay" style="border-left: solid 1px #EEE; padding-left: 25px;">
        <legend>{{Mes Equipements}}</legend>
		<div class="eqLogicThumbnailContainer">
			<div class="cursor eqLogicAction" data-action="add" style="background-color : #ffffff; " >
				<center>
					<i class="fa fa-plus-circle" style="font-size : 7em;color:#94ca02;"></i>
				</center>
				<span style="font-size : 1.1em;position:relative; word-break: break-all;white-space: pre-wrap;word-wrap: break-word;color:#94ca02">
					<center>Ajouter</center>
				</span>
			</div>
			<?php
			if (count($eqLogics) == 0) {
				echo "<br/><br/><br/><center><span style='color:#767676;font-size:1.2em;font-weight: bold;'>{{Vous n'avez pas encore d'équipement, cliquez sur Ajouter pour commencer}}</span></center>";
			} else {
			?>
				<?php
				foreach ($eqLogics as $eqLogic) {
					echo '<div class="eqLogicDisplayCard cursor" data-eqLogic_id="' . $eqLogic->getId() . '" style="background-color : #ffffff; height : 200px;margin-bottom : 10px;padding : 5px;border-radius: 2px;width : 160px;margin-left : 10px;" >';
					echo "<center>";
					echo '<img src="plugins/mochad/doc/images/mochad_icon.png" height="105" width="95" />';
					echo "</center>";
					echo '<span style="font-size : 1.1em;position:relative; top : 15px;word-break: break-all;white-space: pre-wrap;word-wrap: break-word;"><center>' . $eqLogic->getHumanName(true, true) . '</center></span>';
					echo '</div>';
				}
				?>
			<?php } ?>
		</div>
    </div>
    
   <div class="col-md-10 eqLogic" style="border-left: solid 1px #EEE; padding-left: 25px;display: none;">
        <?php
        $cron = cron::byId(config::byKey('BusMonitor', 'mochad'));
        if (is_object($cron) && $cron->getState() != 'run') {
            echo '<div class="alert alert-danger" >{{Attention le démon Mochad n\'est pas en marche. Vérifiez pourquoi. </div>';
        }
        ?>
        <form class="form-horizontal">
            <fieldset>
				<legend>
          			<i class="fa fa-arrow-circle-left eqLogicAction cursor" data-action="returnToThumbnailDisplay"></i> {{Général}}  
					<i class='fa fa-cogs eqLogicAction pull-right cursor expertModeVisible' data-action='configure'></i>
					<a class="btn btn-default btn-xs pull-right expertModeVisible eqLogicAction" data-action="copy"><i class="fa fa-copy"></i>{{Dupliquer}}</a>
		  		</legend>
                <div class="form-group">
                    <label class="col-md-3 control-label">{{Nom de l'équipement X10}}</label>
                    <div class="col-md-3">
                        <input type="text" class="eqLogicAttr form-control" data-l1key="id" style="display : none;" />
                        <input type="text" class="eqLogicAttr form-control" data-l1key="name" placeholder="{{Nom de l'équipement X10}}"/>
                    </div>
                </div>
                <div class="form-group" id="UniteEquipement">
                    <label class="col-sm-2 control-label" >{{Unite de l'équipement}}</label>
                    <div class="col-lg-3">
                        <select id="Unite" class="eqLogicAttr form-control">
							<option value="A">A</option>
							<option value="B">B</option>
							<option value="C">C</option>
							<option value="D">D</option>
							<option value="E">E</option>
							<option value="F">F</option>
							<option value="G">G</option>
							<option value="H">H</option>
							<option value="I">I</option>
							<option value="J">J</option>
							<option value="K">K</option>
							<option value="L">L</option>
							<option value="M">M</option>
							<option value="N">N</option>
							<option value="O">O</option>
							<option value="P">P</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="CodeEquipement">
                    <label class="col-sm-2 control-label" >{{Code de l'équipement}}</label>
                    <div class="col-lg-3">
                        <select id="Code" class="eqLogicAttr form-control">
							<option value="">--</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
							<option value="6">6</option>
							<option value="7">7</option>
							<option value="8">8</option>
							<option value="9">9</option>
							<option value="10">10</option>
							<option value="11">11</option>
							<option value="12">12</option>
							<option value="13">13</option>
							<option value="14">14</option>
							<option value="15">15</option>
							<option value="16">16</option>
                        </select>
                    </div>
                </div>
                <div class="form-group" id="AdresseEquipement">
                    <label class="col-sm-2 control-label" >{{Adresse}}</label>
                    <div class="col-lg-3">
                        <input class="eqLogicAttr form-control" data-l1key="logicalId"/>
                    </div>
                </div>
                <div class="form-group">
                   <label class="col-sm-2 control-label" >{{Objet parent}}</label>
                    <div class="col-md-3">
                        <select id="sel_object" class="eqLogicAttr form-control" data-l1key="object_id">
                            <?php
                            foreach (object::all() as $object) {
                                echo '<option value="' . $object->getId() . '">' . $object->getName() . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                   <label class="col-sm-2 control-label" >{{Catégorie}}</label>
                    <div class="col-md-8">
                        <?php
                        foreach (jeedom::getConfiguration('eqLogic:category') as $key => $value) {
                            echo '<label class="checkbox-inline">';
                            echo '<input type="checkbox" class="eqLogicAttr" data-l1key="category" data-l2key="' . $key . '" />' . $value['name'];
                            echo '</label>';
                        }
                        ?>

                    </div>
                </div>
					<div class="form-group">
						<label class="col-sm-2 control-label" ></label>
						<div class="col-sm-9">
							<label>{{Activer}}</label>
							<input type="checkbox" class="eqLogicAttr" data-label-text="{{Activer}}" data-l1key="isEnable" checked/>
							<label>{{Visible}}</label>
							<input type="checkbox" class="eqLogicAttr" data-label-text="{{Visible}}" data-l1key="isVisible" checked/>
						</div>
					</div>
            </fieldset> 
        </form>

        <legend>{{Commandes}}</legend>
        <a class="btn btn-success btn-sm cmdAction" data-action="add"><i class="fa fa-plus-circle"></i> {{Ajouter une commande X10}}</a><br/><br/>
        <table id="table_cmd" class="table table-bordered table-condensed">
            <thead>
                <tr>
                    <th style="width: 30%;">{{Nom}}</th>
                    <th style="width: 10%;">{{Type}}</th>
                    <th style="width: 10%;">{{Commande}}</th>
                    <th style="width: 22%;">{{Paramètre}}</th>
                    <th style="width: 8%;"></th>
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>

        <form class="form-horizontal">
            <fieldset>
                <div class="form-actions">
                    <a class="btn btn-danger eqLogicAction" data-action="remove"><i class="fa fa-minus-circle"></i> {{Supprimer}}</a>
                    <a class="btn btn-success eqLogicAction" data-action="save"><i class="fa fa-check-circle"></i> {{Sauvegarder}}</a>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<?php 
include_file('desktop', 'mochad', 'js', 'mochad');
include_file('core', 'plugin.template', 'js'); 
?>
