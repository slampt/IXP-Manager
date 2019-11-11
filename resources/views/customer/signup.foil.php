<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@register' )?>">Signup</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Register an account</li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-sm-12">

        <?= Former::open()->method( 'POST' )
            ->id( "form" )
            ->action( route('customer@add' ) )
            ->customWidthClass( 'col-sm-6' )

        ?>


        <div id="instructions-alert" class="alert alert-info" style="display: none;">
            Official <b>IXP Manager</b> documentation for adding / editing customers can be found at <a href="http://docs.ixpmanager.org/usage/customers/">http://docs.ixpmanager.org/</a>.
        </div>

        <div class="well">
            <p>
                <b>Prepopulate this form from PeeringDB by entering the network ASN here:</b>
            </p>

            <div class="form-group col-sm-3">
                <input type="text" class="form-control" id="asn-search">
            </div>

            <div class="btn-group">
                <span class="btn btn-primary" id="btn-populate" style="margin-left: 15px" href="">
                    Populate
                </span>
            </div>
        </div>



        <div class="col-md-6">

            <h3>Customer Details</h3>
            <hr>

            <?= Former::text( 'name' )
                ->label( 'Name' )
                ->placeholder( "Acme Intermet Access" );
            ?>

            <?= Former::text( 'shortname' )
                ->label( 'Short Name' )
                ->placeholder( "acme" );
            ?>

            <?= Former::url( 'corpwww' )
                ->label( 'Corporate Website' )
                ->placeholder( 'http://www.example.com/' );
            ?>

            <?= Former::text( 'abbreviatedName' )
                ->label( 'Abbreviated Name' )
                ->placeholder( "Acme" );
            ?>
        </div>


        <div class="col-md-6 full-member-details" style="<?=
        old( 'type' ) == Entities\Customer::TYPE_ASSOCIATE || ( $t->cust && $t->cust->isTypeAssociate() ) ? 'display: none;' : ''
        ?>">

            <h3>Peering Details</h3>
            <hr>

            <?= Former::number( 'autsys' )
                ->label( 'AS Number' )
                ->placeholder('65500')
                ->blockHelp( 'The AS Number is just the integer value without any AS prefix, etc.' );
            ?>

            <?= Former::number( 'maxprefixes' )
                ->label( 'Max Prefixes' )
                ->placeholder('250');
            ?>

            <?= Former::email( 'peeringemail' )
                ->label( 'Email' )
                ->placeholder( "peering@example.com" );
            ?>

            <?= Former::text( 'peeringmacro' )
                ->label( 'IPv4 Peering Macro' )
                ->placeholder( "AS-ACME-EXAMPLE" );
            ?>

            <?= Former::text( 'peeringmacrov6' )
                ->label( 'IPv6 Peering Macro' )
                ->placeholder( "AS-ACME-V6-EXAMPLE" );
            ?>

            <?= Former::select( 'peeringpolicy' )
                ->label( 'Peering Policy' )
                ->fromQuery( \Entities\Customer::$PEERING_POLICIES )
                ->placeholder( 'Choose a Peering Policy' )
                ->addClass( 'chzn-select' );
            ?>

            <?= Former::select( 'irrdb' )
                ->label( 'IRRDB Source' )
                ->placeholder( 'Choose a IRRDB Source' )
                ->fromQuery( $t->irrdbs, 'source' )
                ->addClass( 'chzn-select-deselect' );
            ?>

            <?= Former::checkbox( 'activepeeringmatrix' )
                ->label( '&nbsp;' )
                ->text( 'Active Peering Matrix' )
                ->value( 1 );
            ?>

        </div>

        <div style="clear: both"></div>

        <div class="full-member-details" style="<?=
        old( 'type' ) == Entities\Customer::TYPE_ASSOCIATE || ( $t->cust && $t->cust->isTypeAssociate() ) ? 'display: none;' : ''
        ?>">

            <div class="col-md-6">
                <h3>NOC Details</h3>
                <hr>
                <?= Former::phone( 'nocphone' )
                    ->label( 'Phone' )
                    ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) );
                ?>

                <?= Former::phone( 'noc24hphone' )
                    ->label( '24h Phone' )
                    ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) );
                ?>

                <?= Former::email( 'nocemail' )
                    ->label( 'Email' )
                    ->placeholder( 'noc@example.com' );
                ?>

                <?= Former::select( 'nochours' )
                    ->label( 'Hours' )
                    ->fromQuery( \Entities\Customer::$NOC_HOURS )
                    ->placeholder( 'Choose NOC Hours' )
                    ->addClass( 'chzn-select' );
                ?>

                <?= Former::url( 'nocwww' )
                    ->label( 'Website' )
                    ->placeholder( 'http://www.noc.example.com/' );
                ?>

            </div>
        </div>


        <div style="clear: both"></div>
        <br/>

        <?= Former::actions( Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( route( 'customer@list' ) )
        );
        ?>

        <?= Former::close() ?>

    </div>

</div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'customer/js/signup' ); ?>
<?php $this->append() ?>