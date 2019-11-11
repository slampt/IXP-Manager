<?php

namespace IXP\Http\Requests\Customer;

/*
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Auth, D2EM;

use Entities\{
    Customer    as CustomerEntity
};

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;


/**
 * Customer Store Request
 * @author     Barry O'Donovan <barry@islandbridgenetworks.ie>
 * @author     Yann Robin <yann@islandbridgenetworks.ie>
 * @category   Customers
 * @copyright  Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GNU GPL V2.0
 */
class Add extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // middleware ensures superuser access only so always authorised here:
        return Auth::guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $validateCommonDetails = [
            'id'                    => 'nullable|integer|exists:Entities\Customer,id',
            'name'                  => 'required|string|max:255',
            'shortname'             => 'required|string|max:30|regex:/[a-z0-9]+/|unique:Entities\Customer,shortname',
            'corpwww'               => 'nullable|url|max:255',
            'abbreviatedName'       => 'required|string|max:30',
        ];

        $validateOtherDetails = [
            'autsys'                => 'required|int|min:1|unique:Entities\Customer',
            'maxprefixes'           => 'nullable|int|min:0',
            'peeringemail'          => 'email',
            'peeringmacro'          => 'nullable|string|max:255',
            'peeringmacrov6'        => 'nullable|string|max:255',
            'peeringpolicy'         => 'string|in:' . implode( ',', array_keys( CustomerEntity::$PEERING_POLICIES ) ),
            'irrdb'                 => 'nullable|integer|exists:Entities\IRRDBConfig,id',
            'nocphone'              => 'nullable|string|max:255',
            'noc24hphone'           => 'nullable|string|max:255',
            'nocemail'              => 'email|max:255',
            'nochours'              => 'nullable|string|in:' . implode( ',', array_keys( CustomerEntity::$NOC_HOURS ) ),
            'nocwww'                => 'nullable|url|max:255',
        ];

        return $this->input( 'type' ) == CustomerEntity::TYPE_ASSOCIATE  ? $validateCommonDetails : array_merge( $validateCommonDetails, $validateOtherDetails ) ;
    }

    public function messages()
    {
        return [
            'autsys.required' => 'AS Number is required',
            'autsys.int' => 'AS Number must be a number',
            'autsys.unique' => 'AS Number is already taken',
        ];
    }
}