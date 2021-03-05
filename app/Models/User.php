<?php

namespace IXP\Models;

/*
 * Copyright (C) 2009 - 2020 Internet Neutral Exchange Association Company Limited By Guarantee.
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

use Eloquent;
use Illuminate\Database\Eloquent\{
    Builder,
    Model
};

/**
 * IXP\Models\User
 *
 * @property int $id
 * @property int|null $custid
 * @property string|null $name
 * @property string|null $username
 * @property string|null $password
 * @property string|null $email
 * @property string|null $authorisedMobile
 * @property int|null $uid
 * @property int|null $privs
 * @property int|null $disabled
 * @property string|null $lastupdated
 * @property int|null $lastupdatedby
 * @property string|null $creator
 * @property string|null $created
 * @property int|null $peeringdb_id
 * @property mixed|null $extra_attributes
 * @property-read \IXP\Models\Customer|null $customer
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereAuthorisedMobile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereCreator($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereCustid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereDisabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereExtraAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereLastupdated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereLastupdatedby($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User wherePeeringdbId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User wherePrivs($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\IXP\Models\User whereUsername($value)
 * @mixin \Eloquent
 */
class User extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'user';

    /**
     * The storage format of the model's date columns.
     *
     * @var string
     */
    protected $dateFormat = 'Y-m-d';

    const AUTH_PUBLIC    = 0;
    const AUTH_CUSTUSER  = 1;
    const AUTH_CUSTADMIN = 2;
    const AUTH_SUPERUSER = 3;

    public static $PRIVILEGES = [
        User::AUTH_CUSTUSER  => 'CUSTUSER',
        User::AUTH_CUSTADMIN => 'CUSTADMIN',
        User::AUTH_SUPERUSER => 'SUPERUSER',
    ];

    public static $PRIVILEGES_ALL = [
        User::AUTH_PUBLIC    => 'PUBLIC',
        User::AUTH_CUSTUSER  => 'CUSTUSER',
        User::AUTH_CUSTADMIN => 'CUSTADMIN',
        User::AUTH_SUPERUSER => 'SUPERUSER',
    ];

    public static $PRIVILEGES_TEXT = [
        User::AUTH_CUSTUSER  => 'Customer User',
        User::AUTH_CUSTADMIN => 'Customer Administrator',
        User::AUTH_SUPERUSER => 'Superuser',
    ];

    public static $PRIVILEGES_TEXT_ALL = [
        User::AUTH_PUBLIC    => 'Public / Non-User',
        User::AUTH_CUSTUSER  => 'Customer User',
        User::AUTH_CUSTADMIN => 'Customer Administrator',
        User::AUTH_SUPERUSER => 'Superuser',
    ];

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo('IXP\Models\Customer', 'custid');
    }

}
