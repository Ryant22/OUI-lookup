<?php

namespace Tests\Feature;

use App\Models\Oui;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OuiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSingleMACLookup()
    {
        // Assuming there is a record in the 'ouis' table with the specified MAC prefix and vendor
        $mac = '00:0A:95:9D:68:16';
        $vendor = 'Vendor ABC';
        $this->insertOuiData(substr(preg_replace('/[^0-9A-Fa-f]/', '', $mac), 0, 6), $vendor);

        $response = $this->getJson('/api/lookup/' . $mac);

        $response->assertStatus(200);
        $response->assertJson([
            'mac' => $mac,
            'vendor' => $vendor,
        ]);
    }

    public function testMultipleMACLookup()
    {
        // Assuming there are records in the 'ouis' table with the specified MAC prefixes and vendors
        $macAddresses = [
            '00:0A:95:9D:68:16',
            '1C:1B:0D:EA:C0:00',
            '74:D4:35:7A:54:41',
        ];

        foreach ($macAddresses as $mac => $vendor) {
            $this->insertOuiData(substr(preg_replace('/[^0-9A-Fa-f]/', '', $mac), 0, 6), 'Vendor ' . $vendor);
        }

        $response = $this->postJson('/api/lookup', ['mac_addresses' => $macAddresses]);

        $response->assertStatus(200);
        $response->assertJsonCount(count($macAddresses));
    }

    private function insertOuiData($mac, $vendor)
    {
        Oui::create([
            'mac_prefix' => substr($mac, 0, 8),
            'vendor' => $vendor,
        ]);
    }
}
