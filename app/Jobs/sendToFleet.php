<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Order;
use App\Model\Courier;

class sendToFleet implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  protected $order_id, $is_courier;


  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($order_id, $is_courier)
  {
    $this->order_id = $order_id;
    $this->is_courier = $is_courier;
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {

      $order = Order::find($this->order_id);

      $pickups = collect([]);
      $task_items = collect([]);
      foreach ($order->orderitems as $oI) {
        $tI = [
          "id" => $oI->id,
          "name" => $oI->name,
          "price" => $oI->price,
          "quantity" => $oI->quantity,
        ];

        $task_items->push($tI);
      }
      
        $newPickup = [
          "name" => $order->restaurant->name,
          "address" => $order->restaurant->address,
          "contact" => $order->restaurant->phone,
          "latitude" => $order->restaurant->latitude,
          "longitude" => $order->restaurant->longitude,
          "task_items" =>  $task_items->toArray(),
        ];
      

      $pickups->push($newPickup);
      
      $sendData = [
        "api_key" => 'd3104c3bca76379aacbd55e102a8f16a',
        "express" => 'true',
        "id" => $order->id,
        "orderstatus_id" =>4,
        "unique_order_id" => $order->unique_order_id,
        "restaurant_name" => $order->restaurant->name,
        "pickup_name" => $order->restaurant->name,
        "pickup_lat" => $order->restaurant->latitude,
        "pickup_lng" => $order->restaurant->longitude,
        "pickup_contact" => $order->restaurant->phone,
        "drop_lat" => $order->latitude,
        "drop_lng" =>  $order->longitude,
        "pickup_dimension" => "single",
        "pickups" => $pickups->toArray(),
        "customer_name" => $order->user->name,
        "customer_phone" => $order->user->phone,
        "customer_address" => $order->address,
        "order_total" => $order->total,
        "pickup_amount" => $order->payment_mode == 'COD' ?  $order->store_total : NULL,
        "order_comment" => $order->order_comment,
        "order_cod" => $order->payable,
        "pickup_address" =>  $pickups[0]['address'] ?? NULL,
        "payment_mode" => $order->payment_mode,
        "delivery_charge" => $order->delivery_charge,
        "drop_delivery_charge" => $order->delivery_charge,
        "extra_bonus" => $order->tip_amount ?? NULL,
        'is_any_store' => 0,
        'any_store_status' => 0,
        'orderitems' => $order->orderitems->toArray(), 
        'created_at' => $order->created_at,
        'time_diff' => \Carbon\Carbon::now()->diffInSeconds($order->created_at),
        'custom_team_id' => $order->restaurant->howin_fleet_team_id ?? NULL,
      ];
    
    $fields_string = http_build_query($sendData);
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://fleet.howincloud.com/api/v1/zeato/post-order-fleet',
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

    curl_close($curl);
  }
}
