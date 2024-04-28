<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateUserController extends Controller
{
    //
    public function index(){
        $password = "test";
        $array = [
            'PL5088025',
            'PL5088027',
            'PL5088028',
            'PL5088030',
            'PL5088032',
            'PL5088034',
            'PL5088036',
            'PL5088037',
            'PL5088038',
            'PL5088039',
            'PL5088044',
            'PL5088049',
            'PL5088050',
            'PL5088052',
            'PL5088054',
            'PL5088059',
            'PL5088060',
            'PL5088064',
            'PL5088070',
            'PL5088072',
            'PL5088073',
            'PL5089003',
            'PL5089005',
            'PL5090001',
            'PL5091001',
            'PL5092001',
            'PL5092002',
            'PL5092004',
            'PL5092005',
            'PL5094002',
            'PL5096001',
            'PL5096003',
            'PL5097001',
            'PL5098002',
            'PL5098004',
            'PL5099001',
            'PL5099002',
            'PL5A01007',
            'PL5A01008',
            'PL5A02002',
            'PL5A04003',
            'PL5A04004',
            'PL5A05004',
            'PL5A06002',
            'PL5A06004',
            'PL5A06006',
            'PL5A08002',
            'PL5A08009',
            'PL5A08012',
            'PL5A08013',
            'PL5A10001',
            'PL5A10005',
            'PL5A11002',
            'PL5A12004',
            'PL5A12007',
            'PL5A12008',
            'PL5A14001',
            'PL5A14003',
            'PL5A17010',
            'PL5A19005',
            'PL5A20003',
            'PL5A20004',
            'PL6088047',
            'PL6088074',
            'PL6091004',
            'PL6097004',
            'PL6A00008',
            'PL6A08001',
            'PL6A08003',
            'PL6A08006',
            'PL6A10002',
            'PL6A12001',
            'PL6A12003',
            'PL6A12006',
            'PL6A13003',
            'PL6A13005',
            'PL6A15003',
            'PL6A15005',
            'PL6A15006',
            'PL6A15007',
            'PL6A15009',
            'PL6A16002',
            'PL6A16003',
            'PL6A16004',
            'PL6A16005',
            'PL6A16008',
            'PL6A17004',
            'PL6A17006',
            'PL6A17007',
            'PL6A17008',
            'PL6A18001',
            'PL6A18003',
            'PL6A18004',
            'PL6A18008',
            'PL6A19002',
            'PL6A20001',
            'PL6A20002',

        ];

        foreach($array as $value){
            $hashed = Hash::make($value);
            echo $hashed."<br />";

        }
        exit();
    }
}
