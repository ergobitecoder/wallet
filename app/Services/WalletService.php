<?php

namespace App\Services;
use Illuminate\Support\Facades\DB;

class WalletService
{

    /**
     * Add Money to Wallet
     * */
    public function deposit( $id ='', $amount = 0 )
    {
        try {

            // Get User Info
            $user = DB::table('users')->where("id","=",$id)->first();

            if( empty( $user ) ){
               return [ 'success' => false, 'message' => "User not valid" ]; 
            }

            // Check Validation
            if( $amount < 3 ||  $amount > 100 ){
                 return [ 'success' => false, 'message' => "Users can add a minimum of $3 and a maximum of $100 to their wallet in a single operation" ]; 
            }

            // Update Amount
            $data = DB::table("users")->where("id","=",$id)->increment('wallet',$amount);

            $wallet = ($user->wallet + $amount);
            return [
                'success' => true , 
                'message' => 'Total amount in wallet $'.$wallet.' are available.',
                'data' => array( 'wallet' =>  $wallet)
            ];

        } catch (\Throwable $th) {
            throw $th;
            return [ 'success' => false, 'message' => $th ];
        }
    }


    /**
     * Withdraw Money to Wallet
     * */
    public function withdraw( $id ='', $amount = 0 )
    {
        try {

            // Get User Info
            $user = DB::table('users')->where("id","=",$id)->first();

            if( empty( $user ) ){
               return [ 'success' => false, 'message' => "User not valid" ]; 
            }

            $data = DB::table("users")->where( "id","=",$id )->where( "wallet",">=",$amount )->decrement('wallet',$amount);

            if( $data ){
                return [ 'success' => true, 'message' => "Successfully withdraw" ];
            } else {
                return [ 'success' => true, 'message' => "Insufficient balance" ];
            }

        } catch (\Throwable $th) {
            throw $th;
            return [ 'success' => false, 'message' => $th ];
        }
    }

        
}
