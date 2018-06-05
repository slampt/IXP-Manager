<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix.'@store' ) )
        ->customWidthClass( 'col-sm-6' )
    ?>

    <div class="col-sm-12">
        <div class="col-sm-6">
            <?= Former::text( 'name' )
                ->label( 'Name' )
                ->blockHelp( "" );
            ?>

            <?= Former::text( 'hostname' )
                ->label( 'Hostname' )
                ->disabled( $t->data[ 'params'][ 'addBySnmp'] ? true : false )
                ->blockHelp( "" );
            ?>

            <?php if( $t->data[ 'params'][ 'addBySnmp'] ): ?>
                <?= Former::hidden( 'hostname' ) ?>
            <?php endif; ?>

            <?= Former::select( 'cabinetid' )
                ->label( 'Rack' )
                ->fromQuery( $t->data[ 'params'][ 'cabinets'], 'name' )
                ->placeholder( 'Choose a cabinet' )
                ->addClass( 'chzn-select' );
            ?>

            <?= Former::select( 'infrastructure' )
                ->label( 'Infrastructure' )
                ->fromQuery( $t->data[ 'params'][ 'infra'], 'name' )
                ->placeholder( 'Choose a cabinet' )
                ->addClass( 'chzn-select' );
            ?>
        </div>

        <div class="col-sm-6">
            <?= Former::text( 'snmppasswd' )
                ->label( 'SNMP Community' )
                ->disabled( $t->data[ 'params'][ 'addBySnmp'] ? true : false )
                ->blockHelp( "" );
            ?>

            <?php if( $t->data[ 'params'][ 'addBySnmp'] ): ?>
                <?= Former::hidden( 'snmppasswd' ) ?>
            <?php endif; ?>

            <?= Former::select( 'vendorid' )
                ->label( 'Vendor' )
                ->fromQuery( $t->data[ 'params'][ 'vendors'], 'name' )
                ->placeholder( 'Choose a vendor' )
                ->addClass( 'chzn-select' );
            ?>

            <?= Former::text( 'model' )
                ->label( 'Model' )
                ->blockHelp( "" );
            ?>

            <?= Former::checkbox( 'active' )
                ->label( '&nbsp;' )
                ->text( 'Active' )
                ->value( 1 )
                ->check()
                ->blockHelp( "" );
            ?>
        </div>
    </div>

    <div style="clear: both"></div>
    <br/>

    <div class="col-sm-12">
        <div class="col-sm-6">

            <fieldset>
                <legend>Management Configuration:</legend>
                <?= Former::text( 'ipv4addr' )
                    ->label( 'IPv4 Address' )
                    ->blockHelp( "" );
                ?>

                <?= Former::text( 'ipv6addr' )
                    ->label( 'IPv6 Address' )
                    ->blockHelp( "" );
                ?>

                <?= Former::text( 'mgmt_mac_address' )
                    ->label( 'Mgmt MAC Address' )
                    ->blockHelp( "" );
                ?>
            </fieldset>

        </div>

        <div class="col-sm-6">

            <fieldset>
                <legend>Layer 3 Configuration:</legend>
                <?= Former::text( 'asn' )
                    ->label( 'ASN' )
                    ->blockHelp( "" );
                ?>

                <?= Former::text( 'loopback_ip' )
                    ->label( 'Loopback IP' )
                    ->blockHelp( "" );
                ?>

                <?= Former::text( 'loopback_name' )
                    ->label( 'Loopback Name' )
                    ->blockHelp( "" );
                ?>
            </fieldset>

        </div>
    </div>

    <div style="clear: both"></div>
    <br/>

    <div class="form-group">

        <label for="notes" class="control-label control-label col-lg-1 col-sm-4 ">Notes</label>
        <div class="col-sm-8">

            <ul class="nav nav-tabs">
                <li role="presentation" class="active">
                    <a class="tab-link-body-note" href="#body">Notes</a>
                </li>
                <li role="presentation">
                    <a class="tab-link-preview-note" href="#preview">Preview</a>
                </li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="body">

                    <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?= $t->data[ 'params'][ 'notes' ] ?? '' ?></textarea>
                </div>
                <div role="tabpanel" class="tab-pane" id="preview">
                    <div class="well well-preview" style="background: rgb(255,255,255);">
                        Loading...
                    </div>
                </div>
            </div>

            <br><br>
        </div>

    </div>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
        Former::default_link( 'Cancel' )->href( route( $t->feParams->route_prefix.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' ),
        Former::default_link( $t->data[ 'params'][ 'addBySnmp'] ? "Manual / Non-SNMP Add" : "Add by SNMP" )->href( route( $t->data[ 'params'][ 'addBySnmp'] ? $t->feParams->route_prefix.'@add' : $t->feParams->route_prefix.'@pre-add-by-snmp' ) )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::hidden( 'add_by_snnp' )
        ->value( $t->data[ 'params'][ 'addBySnmp'] ? true : false )
    ?>

    <?= Former::close() ?>

</div>

