<?php


if(isset($_REQUEST['rep']) && !empty($_REQUEST['rep']) )
{
    if($_REQUEST['rep'] == 'order'){

        $tempdata = $objReport->showAllOrders();

        foreach($tempdata as $temp)
        {
            $datas[] = array('Order Name'=>$temp['quote_name'],'Amount'=>$temp['amount'],'Quantity'=>$temp['quantity'],'Order generated by'=>$temp['order_user'],'Supplier'=>$temp['bid_user'],'Order Date'=>$temp['date_added'] );
        }
        $obj = new ITFExport($datas);
        $obj->download();
    } elseif ($_REQUEST['rep'] == 'transaction') {
        $tempdata = $objReport->showAllTransactions();

        foreach($tempdata as $temp)
        {
           $datas[] = array('Order Id'=>$temp['id'],'Transaction Id'=>$temp['txn_id'],'Transaction Type'=>$temp['txn_type'],'Payment Amount'=>$temp['payment_amount'],'Quantity'=>'1','Payment User'=>$temp['first_name'],'Payer Email'=>$temp['payer_email'],'Currency'=>$temp['mc_currency'],'Payment Date'=>$temp['date_added'],'Expiry Date'=>$temp['exp_date'],'Plan Id'=>$temp['plan_id'],'Plan Name'=>$temp['plan_name'],'Plan Duration'=>$temp['plan_duration'],'Payment Status'=>$temp['payment_status'] );        }
        $obj = new ITFExport($datas);
        $obj->download();
    

} elseif ($_REQUEST['rep'] == 'member') {
        $tempdata = $objReport->showAllTransactions();

        foreach($tempdata as $temp)
        {
            $datas[] = array('Order Id'=>$temp['order_id'],'Transaction Id'=>$temp['txn_id'],'Transaction Type'=>$temp['txn_type'],'Payment Amount'=>$temp['payment_amount'],'Quantity'=>$temp['num_cart_items'],'Payment User'=>$temp['order_user'],'Payer Email'=>$temp['payer_email'],'Currency'=>$temp['mc_currency'],'Payment Date'=>$temp['date_added'],'Order Date'=>$temp['order_date'],'IPN Track ID'=>$temp['ipn_track_id'],'Payment Status'=>$temp['payment_status'] );
        }
        $obj = new ITFExport($datas);
        $obj->download();
    }



else{
    flash('Something went wrong !','2');

}}
?>
