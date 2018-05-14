<?php
/*
 * Copyright (C) 2009-2018 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */

namespace IXP\Http\Controllers;

use App, Auth, D2EM;

use Entities\{
    Customer            as CustomerEntity,
    Infrastructure      as InfrastructureEntity,
    IXP                 as IXPEntity,
    PhysicalInterface   as PhysicalInterfaceEntity,
    Switcher            as SwitchEntity,
    VirtualInterface    as VirtualInterfaceEntity,
    Vlan                as VlanEntity,
    VlanInterface       as VlanInterfaceEntity
};

use Repositories\Vlan as VlanRepository;

use Illuminate\Http\{
    Request
};

use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use IXP\Http\Requests\StatisticsRequest;
use IXP\Services\Grapher\Graph;

use IXP\Services\Grapher\Graph\{
    Customer as CustomerGraph
};

use IXP\Utils\View\Alert\Alert;
use IXP\Utils\View\Alert\Container as AlertContainer;

use Illuminate\Auth\Access\AuthorizationException;


/**
 * Statistics Controller
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @category   Statistics
 * @copyright  Copyright (C) 2009-2017 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class StatisticsController extends Controller
{

    /**
     * Process and update request parameters for standard graph attributes: period, category, protocol, type.
     *
     * These are safe for use from the request.
     *
     * @param StatisticsRequest $r
     */
    private function processGraphParams( StatisticsRequest $r ) {
        $r->period   = Graph::processParameterPeriod(   $r->input( 'period',   '' ) );
        $r->category = Graph::processParameterCategory( $r->input( 'category', '' ) );
        $r->protocol = Graph::processParameterProtocol( $r->input( 'protocol', '' ) );
        $r->type     = Graph::processParameterType(     $r->input( 'type',     '' ) );
    }


    /**
     * Show overall IXP graphs
     *
     * @param string $category Category of graph to show (e.g. bits / pkts)
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \IXP_Exception
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     * @throws \Doctrine\ORM\ORMException
     */
    public function ixp( string $category = Graph::CATEGORY_BITS ){
        $ixp      = D2EM::getRepository( IXPEntity::class )->getDefault();
        $grapher  = App::make('IXP\Services\Grapher');
        $category = Graph::processParameterCategory( $category, true );

        $graph = $grapher->ixp( $ixp )->setType( Graph::TYPE_PNG )->setProtocol( Graph::PROTOCOL_ALL )->setCategory( $category );
        $graph->authorise();

        return view( 'statistics/ixp' )->with([
            'graph'    => $graph,
            'category' => $category,
        ]);
    }

    /**
     * Show IXP infrastructure graphs
     *
     * @param int $infraid ID of the infrastructure to show the graph of
     * @param string $category Category of graph to show (e.g. bits / pkts)
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws
     */
    public function infrastructure( int $infraid = 0, string $category = Graph::CATEGORY_BITS ){
        /** @var InfrastructureEntity[] $eInfras */
        $eInfras  = D2EM::getRepository( InfrastructureEntity::class )->findBy( [], [ 'name' => 'ASC' ] );
        $grapher  = App::make('IXP\Services\Grapher');
        $category = Graph::processParameterCategory( $category, true );

        $infras = [];
        foreach( $eInfras as $i ) {
            $infras[ $i->getId() ] = $i->getName();
        }

        $infraid  = isset( $infras[ $infraid ] ) ? $infraid : array_keys( $infras )[0];
        /** @var InfrastructureEntity $infra */
        $infra    = D2EM::getRepository( InfrastructureEntity::class )->find( $infraid );
        $graph    = $grapher->infrastructure( $infra )->setType( Graph::TYPE_PNG )->setProtocol( Graph::PROTOCOL_ALL )->setCategory( $category );

        $graph->authorise();

        return view( 'statistics/infrastructure' )->with([
            'infras'   => $infras,
            'infraid'  => $infraid,
            'infra'    => $infra,
            'graph'    => $graph,
            'category' => $category,
        ]);
    }

    /**
     * Show Vlan (sflow) graphs
     *
     * @param int $vlanid ID of the VLAN to show the graph of
     * @param string $protocol IPv4/6
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws
     */
    public function vlan( int $vlanid = 0, string $protocol = Graph::PROTOCOL_IPV4, string $category = Graph::CATEGORY_BITS ){
        /** @var VlanEntity[] $eVlans */
        $eVlans   = D2EM::getRepository( VlanEntity::class )->getAndCache( VlanRepository::TYPE_NORMAL, 'name', false );
        $grapher  = App::make('IXP\Services\Grapher');
        $protocol = Graph::processParameterRealProtocol( $protocol );
        $category = Graph::processParameterCategory( $category, true );

        $vlans = [];
        foreach( $eVlans as $v ) {
            // we really only want 'public' VLANs
            if( $v->getPeeringManager() || $v->getPeeringMatrix() ) {
                $vlans[ $v->getId() ] = $v->getName();
            }
        }

        $vlanid  = isset( $vlans[ $vlanid ] ) ? $vlanid : array_keys( $vlans )[0];
        /** @var VlanEntity $vlan */
        $vlan     = D2EM::getRepository( VlanEntity::class )->find( $vlanid );
        $graph    = $grapher->vlan( $vlan )->setType( Graph::TYPE_PNG )->setProtocol( $protocol )
                        ->setCategory( $category );

        $graph->authorise();

        return view( 'statistics/vlan' )->with([
            'vlans'    => $vlans,
            'vlanid'   => $vlanid,
            'vlan'     => $vlan,
            'graph'    => $graph,
            'protocol' => $protocol,
            'category' => $category,
        ]);
    }

    /**
     * Show IXP switch graphs
     *
     * @param int $switchid ID of the switch to show the graph of
     * @param string $category Category of graph to show (e.g. bits / pkts)
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws
     */
    public function switch( int $switchid = 0, string $category = Graph::CATEGORY_BITS ){
        /** @var SwitchEntity[] $eSwitches */
        $eSwitches = D2EM::getRepository( SwitchEntity::class )->getAndCache( true, SwitchEntity::TYPE_SWITCH );
        $grapher = App::make('IXP\Services\Grapher');
        $category = Graph::processParameterCategory( $category, true );

        $switches = [];
        foreach( $eSwitches as $s ) {
            $switches[ $s->getId() ] = $s->getName();
        }

        $switchid = isset( $switches[ $switchid ] ) ? $switchid : array_keys( $switches )[0];
        /** @var SwitchEntity $switch */
        $switch   = D2EM::getRepository( SwitchEntity::class )->find( $switchid );
        $graph    = $grapher->switch( $switch )->setType( Graph::TYPE_PNG )->setProtocol( Graph::PROTOCOL_ALL )->setCategory( $category );

        $graph->authorise();

        return view( 'statistics/switch' )->with([
            'switches'  => $switches,
            'switchid'  => $switchid,
            'switch'    => $switch,
            'graph'     => $graph,
            'category'  => $category,
        ]);
    }


    /**
     * Show IXP trunk graphs
     *
     * @param string $trunkid ID of the trunk to show the graph of
     * @param string $category Category of graph to show (e.g. bits / pkts)
     *
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws
     */
    public function trunk( string $trunkid = null, string $category = Graph::CATEGORY_BITS ){
        if( !is_array( config('grapher.backends.mrtg.trunks') ) || !count( config('grapher.backends.mrtg.trunks') ) ) {
            AlertContainer::push(
                "Trunk graphs have not been configured. Please see <a target='_blank' href=\"http://docs.ixpmanager.org/features/grapher/\">this documentation</a> for instructions.",
                Alert::DANGER
            );
            return redirect('');
        }

        $grapher = App::make('IXP\Services\Grapher');

        // get the available graphs
        $images = [];
        $graphs = [];
        foreach( config('grapher.backends.mrtg.trunks') as $g ) {
            $images[]           = $g['name'];
            $graphs[$g['name']] = $g['title'];
        }

        if( !in_array( $trunkid, $images ) ) {
            $trunkid = $images[ 0 ];
        }

        $graph = $grapher->trunk( $trunkid )->setType( Graph::TYPE_PNG )->setProtocol( Graph::PROTOCOL_ALL )->setCategory( Graph::CATEGORY_BITS );
        $graph->authorise();

        return view( 'statistics/trunk' )->with([
            'graphs'    => $graphs,
            'trunkid'   => $trunkid,
            'graph'     => $graph,
            'category'  => $category,
        ]);
    }



    /**
     * Display all member graphs
     *
     * @param StatisticsRequest $r
     *
     * @return View|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     *
     * @throws
     */
    public function members( StatisticsRequest $r ) {

        if( !CustomerGraph::authorisedForAllCustomers() ) {
            AlertContainer::push( "You are not authorised to view all member graphs.", Alert::DANGER );
            return redirect('');
        }

        $grapher = App::make('IXP\Services\Grapher');
        $this->processGraphParams($r);

        // do we have an infrastructure or vlan?

        $vlan = $infra = false;
        if( $r->input( 'infra' ) ) {
            /** @var InfrastructureEntity $infra */
            if( $infra = D2EM::getRepository(InfrastructureEntity::class) ->find($r->input('infra')) ) {
                $targets = D2EM::getRepository( VirtualInterfaceEntity::class )->getObjectsForInfrastructure( $infra );
            } else {
                $targets = D2EM::getRepository( CustomerEntity::class )->getCurrentActive( false, true, false );
            }
            $r->protocol = Graph::PROTOCOL_ALL;
        } else if( $r->input( 'vlan' ) && ( $vlan = D2EM::getRepository(VlanEntity::class)->find($r->input('vlan')) ) ) {
            /** @var VlanEntity $vlan */
            if( !in_array( $r->protocol, Graph::PROTOCOLS_REAL ) ) {
                $r->protocol = Graph::PROTOCOL_IPV4;
            }
            $targets = D2EM::getRepository( VlanInterfaceEntity::class )->getObjectsForVlan( $vlan, false, $r->protocol );
        } else {
            $targets = [];
        }

        $graphs = [];
        foreach( $targets as $t ) {
            if( $infra ) {
                if( $t->isGraphable() ) {
                    $g = $grapher->virtint( $t );
                }
            } else if( $vlan ) {
                $g = $grapher->vlanint( $t );
            } else {
                if( $t->isGraphable() ) {
                    $g = $grapher->customer( $t );
                }
            }

            /** @var Graph $g */
            $g->setType(     Graph::TYPE_PNG )
                ->setProtocol( $r->protocol   )
                ->setCategory( $r->category   )
                ->setPeriod(   $r->period     );

            $g->authorise();
            $graphs[] = $g;
        }

        return view( 'statistics/members' )->with([
            'graph'         => $graphs[0] ?? false,  // sample graph as all types/protocols/categories/periods will be the same
            'graphs'        => $graphs,
            'r'             => $r,
            'infras'        => D2EM::getRepository( InfrastructureEntity::class )->getNames(),
            'infra'         => $infra ?? false,
            'vlans'         => D2EM::getRepository( VlanEntity::class )->getNames(),
            'vlan'          => $vlan ?? false,
        ]);
    }


    /**
     * Display all graphs for a member
     *
     * @param StatisticsRequest   $r
     * @param integer             $id ID of the member
     *
     * @return RedirectResponse|View
     *
     * @throws
     */
    public function member( StatisticsRequest $r, int $id = null ) {

        if( $id === null && Auth::check() ) {
            $id = Auth::user()->getCustomer()->getId();
        }

        /** @var CustomerEntity $c */
        if( !$id || !( $c = D2EM::getRepository( CustomerEntity::class )->find( $id ) ) ){
            abort( 404, 'Customer not found' );
        }

        $grapher = App::make('IXP\Services\Grapher');


        // if the customer is authorised, then so too are all of their virtual and physical interfaces:
        try {
            $grapher->customer( $c )->authorise();
        } catch( AuthorizationException $e ) {
            AlertContainer::push( "You are not authorised to view this member's graphs.", Alert::DANGER );
            return redirect()->back();
        }

        if( !$c->hasInterfacesConnectedOrInQuarantine() ) {
            AlertContainer::push(
                "This customer has no graphable interfaces (i.e. no physical interfaces in quarantine or connected)",
                Alert::WARNING
            );
            return redirect()->back();
        }

        return view( 'statistics/member' )->with([
            "c"                     => $c,
            "grapher"               => $grapher,
            "category"              => Graph::processParameterCategory( $r->input( 'category' ) ),
            "period"                => Graph::processParameterPeriod( $r->input( 'period' ) ),
        ]);
    }

    /**
     * Display Aggregate/LAG/Port for all periods (day/week/month/year)
     *
     * @param   StatisticsRequest     $r
     * @param   string                $type       type
     * @param   integer               $typeid     ID of type
     * @return  View
     *
     * @throws
     */
    public function memberDrilldown( StatisticsRequest $r, string $type, int $typeid ) {
        /** @var CustomerEntity $c */
        switch( strtolower( $type ) ) {
            case 'agg':

                if( !( $c = D2EM::getRepository( CustomerEntity::class )->find( $typeid ) ) ){
                    abort( 404, 'Unknown customer' );
                }
                $graph = App::make('IXP\Services\Grapher')->customer( $c );
                break;

            case 'vi':
                /** @var VirtualInterfaceEntity $vi */
                if( !( $vi = D2EM::getRepository( VirtualInterfaceEntity::class )->find( $typeid ) ) ) {
                    abort( 404, 'Unknown virtual interface' );
                }
                $c = $vi->getCustomer();
                $graph = App::make('IXP\Services\Grapher')->virtint( $vi );
                break;

            case 'pi':
                /** @var PhysicalInterfaceEntity $pi */
                if( !( $pi = D2EM::getRepository( PhysicalInterfaceEntity::class )->find( $typeid ) ) ) {
                    abort( 404, 'Unknown physical interface' );
                }
                $c = $pi->getVirtualInterface()->getCustomer();
                $graph = App::make('IXP\Services\Grapher')->physint( $pi );

                break;

            default:
                abort( 404, 'Unknown graph type' );
        }

        /** @var Graph $graph */
        $graph->setCategory( Graph::processParameterCategory( $r->input( 'category' ) ) );
        $graph->authorise();

        return view( 'statistics/member-drilldown' )->with([
            'c'     => $c,
            'graph' => $graph,
        ]);
    }

    /**
     * Show latency graphs
     *
     * @param Request $r
     * @param int $vliid
     * @param string $protocol
     * @return $this|RedirectResponse
     * @throws \IXP\Exceptions\Services\Grapher\ParameterException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function latency( Request $r, int $vliid, string $protocol ){
        /** @var VlanInterfaceEntity $vli */
        if( !( $vli = D2EM::getRepository( VlanInterfaceEntity::class )->find( $vliid ) ) ){
            abort( 404, 'Unknown VLAN interface' );
        }

        $protocol = Graph::processParameterProtocol( $protocol );

        $graph = App::make('IXP\Services\Grapher')->latency( $vli )->setProtocol( $protocol );
        $graph->authorise();

        $fnEnabled = 'get' . ucfirst( $protocol ) . 'enabled';
        $fnCanping = 'get' . ucfirst( $protocol ) . 'canping';
        $fnAddress = 'get' . ucfirst( $protocol ) . 'Address';

        if( !$vli->$fnEnabled() || !$vli->$fnCanping() ) {
            AlertContainer::push(
                "Protocol or ping not enabled on the requested interface",
                Alert::WARNING
            );
            return redirect()->to( route( "statistics@member" ), [ "id" => $vli->getVirtualInterface()->getCustomer()->getId() ] );
        }

        return view( 'statistics/latency' )->with([
            'c'         => $vli->getVirtualInterface()->getCustomer(),
            'vli'       => $vli,
            'ip'        => $vli->$fnAddress()->getAddress(),
            'protocol'  => $protocol,
            'graph'     => $graph,
        ]);
    }
}
