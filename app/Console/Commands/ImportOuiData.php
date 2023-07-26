<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportOuiData extends Command
{
    protected $signature = 'import:oui';
    protected $description = 'Import the latest IEEE OUI data into the database.';

    // Add any additional properties or methods you need for the import process.

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $url = 'http://standards-oui.ieee.org/oui/oui.csv';

        $response = Http::get($url);

        if ($response->ok()) {
            $csvData = $response->getBody();

            $lines = explode(PHP_EOL, $csvData);
            $header = str_getcsv(array_shift($lines));

            $data = [];
            foreach ($lines as $line) {
                $values = str_getcsv($line);

                // Check if the number of elements in the row matches the number of elements in the header
                if (count($values) !== count($header)) {
                    continue; // Skip incomplete rows
                }

                $row = array_combine($header, $values);

                // Extract the MAC prefix from the Assignment field
                $macPrefix = strtoupper(preg_replace('/[^0-9A-F]/', '', $row['Assignment']));
                if (strlen($macPrefix) !== 6) {
                    continue; // Skip invalid MAC prefixes
                }

                $data[] = [
                    'mac_prefix' => substr($macPrefix, 0, 6),
                    'vendor' => $row['Organization Name'] ?: 'Unknown Vendor',
                    'address' => $row['Organization Address'] ?: 'Unknown Address',
                ];

                // Insert data in batches of 1000 records
                if (count($data) === 1000) {
                    $this->insertBatch($data);
                    $data = [];
                }
            }

            // Insert any remaining records
            if (!empty($data)) {
                $this->insertBatch($data);
            }

            $this->info('OUI data imported successfully.');
        } else {
            $this->error('Failed to fetch the OUI data.');
        }
    }

    private function insertBatch($data)
    {
        // Now, insert the data into the database using batch insert
        DB::table('ouis')->insert($data);
    }

}
