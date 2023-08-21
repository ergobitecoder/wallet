<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\WalletService;
use Illuminate\Support\Facades\Log;


class WalletController extends Controller
{

    /**
     * Add Money to Wallet
     * */
    public function deposit(Request $request){

        $error_code = "200";
        $message = "Successfully";
        $result = [];
        $success = true;

        try {

            $user_id = $request->input('user_id');
            $amount = $request->input('amount');
            
            // Check Required Fields
            if( empty( $user_id ) || empty( $amount ) ){
                
                $error_code = "400";
                $message = "Required Userid and amount";
                $success = false;

            } else {
                
                // Call Wallet Service
                $walletService = new WalletService;
                $wallet_response = $walletService->deposit( $user_id, $amount );

                if( ! $wallet_response['success'] ){
                    $error_code = "400";
                    $message = $wallet_response['message'];
                    $success = false;
                    Log::info("wallet-error|user_id|".$user_id."|anount|".$amount."|".$message);
                } else {
                    $message = $wallet_response['message'];
                    $result = $wallet_response['data'];
                    Log::info("wallet|user_id|".$user_id."|anount|".$amount);

                }
            }


            // Return Response
            $response = ["error" => array("code" => $error_code, "message" => $message), "result" => $result, "success" => $success];

            return response()->json($response, $error_code);

        } catch (\Throwable $th) {

            throw $th;
            $error_code = "400";
            $success = false;
            $response = ["error" => array("code" => $error_code, "message" => $th), "result" => $result, "success" => $success];

            return response()->json($response, $error_code);
        }  
    }

}
