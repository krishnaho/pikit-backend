<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;
use App\Model\Courier;
use App\Models\Restaurant;


class saveBatchToFleet implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $restaurant;
  protected $update;


  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($restaurant, $update = null)
  {
    $this->restaurant = $restaurant;
    $this->update = $update;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {

    $sendData = [
        'name' => $this->restaurant['name'],
        'latitude' => $this->restaurant['latitude'] ?? '0000',
        'longitude' => $this->restaurant['longitude'] ?? '0000',
        'radius' => $this->restaurant['delivery_radius'] ?? '0',
    ];

    if ($this->update == true) {
        $sendData['id'] = $this->restaurant['howin_fleet_team_id'];
    }
 
    $fields_string = http_build_query($sendData);
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://fleet.howincloud.com/api/v1/zeato/add-team-fleet',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $fields_string,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));

    $response = curl_exec($curl);
    $resData = json_decode($response, true);
    Restaurant::where('id', $this->restaurant['id'])->update(['howin_fleet_team_id' => $resData['data']['id']]);
    curl_close($curl);
  }
}
