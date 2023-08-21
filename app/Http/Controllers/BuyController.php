<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BuyController extends Controller
{

    /**
     * Buy Product
     * */
    public function buy(Request $request){

        $error_code = 200;
        $message = "Successfully";
        $result = [];
        $success = true;

        try {

            $user_id = $request->input('user_id');
            $product = $request->input('product') ;
            $count = $request->input('count');

            if( empty( $user_id ) || empty( $product )  || empty( $count ) ){
                $error_code = 400;
                $message = "Required user_id, product and count fields.";   
            }

            // Get User Info
            $user = DB::table('users')->where("id","=",$user_id)->first();

            if( empty( $user ) ){
                
                $error_code = 400;
                $message = "Invalid product";    

            } else if( $error_code ==  200 ) {

                switch( $product ){
                    case 'cookie':
                            $per_cookie_price = 1;
                            $total_price = $count * $per_cookie_price;

                            $walletService = new WalletService;
                            $wallet_response = $walletService->withdraw( $user_id, $total_price );
                            
                            if( !$wallet_response['success'] ) {
                                $error_code = 400;
                                $message = $wallet_response['message'];   
                                Log::info("withdraw_error|user_id|".$user_id."|anount|".$total_price.'|msg|'.$message);  
                            } else {
                                $result = array(
                                    "cookie" => $count,
                                    "total_price" => $total_price,
                                    "wallet_balance" => ( $user->wallet - $total_price )
                                );

                                $message = $wallet_response['message'];  
                                Log::info("withdraw|user_id|".$user_id."|anount|".$total_price);  
                            }

                        break;
                    default :
                            $error_code = 400;
                            $message = "Invalid product";    
                        break;
                }

            }


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
