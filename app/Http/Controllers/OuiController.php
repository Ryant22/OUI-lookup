<?php

namespace App\Http\Controllers;

use App\Models\Oui;
use Illuminate\Http\Request;

class OuiController extends Controller
{
    public function lookupSingleMAC($mac)
    {
        $vendor = $this->lookupVendor($mac);
        return response()->json(['mac' => $mac, 'vendor' => $vendor]);
    }

    public function lookupMultipleMACs(Request $request)
    {
        $macAddresses = $request->input('mac_addresses');

        $results = [];
        foreach ($macAddresses as $mac) {
            $vendor = $this->lookupVendor($mac);
            $results[] = ['mac' => $mac, 'vendor' => $vendor];
        }

        return response()->json($results);
    }

    private function lookupVendor($mac)
    {
        $macPrefix = substr(preg_replace('/[^0-9A-Fa-f]/', '', $mac), 0, 6);

        // Check if the second character is '2', '6', 'A', or 'E' (randomised MAC)
        if (in_array(strtoupper(substr($macPrefix, 1, 1)), ['2', '6', 'A', 'E'])) {
            return 'Randomised MAC';
        }

        $oui = Oui::where('mac_prefix', '=', $macPrefix)->first();
        return $oui ? $oui->vendor : 'Unknown';
    }

}
