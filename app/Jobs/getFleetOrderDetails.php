<?php

namespace App\Jobs;

use App\Model\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class getFleetOrderDetails implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
  protected $order_id;
  /**
   * Create a new job instance.
   *
   * @return void
   */
  public function __construct($order_id)
  {
    $this->order_id = $order_id;
    // dd($order_id);
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle()
  {
    $order = Order::where('id', $this->order_id)->first();
    $curl = curl_init();
    $fields_string = http_build_query(['id' => $order->id]);
    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://fleet.howincloud.com/api/v1/zeato/get-order-details',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>  $fields_string,
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/x-www-form-urlencoded'
      ),
    ));
    $response = curl_exec($curl);
    $data = json_decode($response);
    if ($data == 'true') {
      if ($order->order_status_id != $data->order_data->orderstatus_id) {
        $order->order_status_id =  $data->order_data->orderstatus_id;
        $order->agent_name = $data->order_data->agent_name;
        $order->agent_phone = $data->order_data->agent_contact;
        $order->agent_image = $data->order_data->agent_image;
        $order->save();
      }
    }

    curl_close($curl);
  }
}
