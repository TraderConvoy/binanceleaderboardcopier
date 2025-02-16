<?php
session_start();
include("../libraries/config.inc.php");
if($_SESSION['admin']==""){
header("location:index.php");
}

 function generateRandomString($length = 15, $abc = "0123456789abcdefghijklmnopqrstuvwxyz") //NEVER CHANGE $abc values
{
    return substr(str_shuffle($abc), 0, $length);
}
function GetSQLValueString($theValue, $theType) 
{	
  $theValue = trim($theValue);	
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

   
  $theValue = str_replace("'","\'","$theValue");

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "Null";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "Null";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "Null";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "Null";
      break;
  }
  return $theValue;
}

$link = mysqli_connect($config['server'], $config['dbusername'], $config['dbpass']);

if (mysqli_connect_errno())
  {
  echo "ERROR Failed to connect to MySQL: " . mysqli_connect_error();
  }
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

/*
mysqli_select_db($link, "binancefutures");

$query =" select UNIX_TIMESTAMP(current_timestamp()) as currenttimestamp from dual";
 if(! $link ) {
            die('RESPONSE_ERROR Could not connect: ' . mysqli_error());
         }
		 
	         $result = mysqli_query($link, $query) or die(mysqli_error($link));
			 

   while($row = mysqli_fetch_assoc($result)) {
				extract($row);
			}
			echo $currenttimestamp; */
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <style type='text/css'>
@keyframes zoomEffect {
0% {
color:white;
background:lightgreen;
 
}
50% {
color:magenta;
 background:purple;
}
100% {
color:grey;
background:coral; 
}
}
.infinitelooop
{
	animation: 11s ease 0s normal none infinite running zoomEffect;
}
	</style>
<script type='text/javascript'>
  var TOTALTRADES=-1;
  var GLOBALVPSSTATUS = '';
  var OP_BUY=1; var OP_SELL =-1;
  var GLOBALSL = 21; var GLOBALTP = 7;
  var GLOBALPRICE = 0;
  var GLOBALPOSITIONHASH = '';
  
  var globalsymbolname = '';
  var globaldigitdecimalplace=1;
  var globalstoploss = 0;
  var globaltakeprofit = 0;
  var globalordermode = 0;
  var globalquotesrefreshinterval=5000;
  
  var cumuusd = parseFloat(0);
  var cumudd = parseFloat(0);
 
  var globalpostionpopup;
  
  
  
  
  function addCommas(nStr)
{
    nStr += '';
    x = nStr.split('.');
    x1 = x[0];
    x2 = x.length > 1 ? '.' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
    }
    return x1 + x2;
}
  function removeCommas(nStr)
{
  return nStr.replace(new RegExp(',', 'g'),"")
}


</script>
    <title>Admin Dashboard</title>
<style type='text/css'>


.slidecontainer {
  width: 100%;
}

.slider {
  -webkit-appearance: none;
  width: 100%;
  height: 25px;
  background: #d3d3d3;
  outline: none;
  opacity: 0.7;
  -webkit-transition: .2s;
  transition: opacity .2s;
}

.slider:hover {
  opacity: 1;
}

.slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 25px;
  height: 25px;
  background: #4CAF50;
  cursor: pointer;
}

.slider::-moz-range-thumb {
  width: 25px;
  height: 25px;
  background: #4CAF50;
  cursor: pointer;
}
 
table tbody tr:hover {
       background-color: lightblue;
       cursor: pointer;
   }
 
   table{ text-align:center;zoom:0.92;}
   
#tbl_coins_length
{
 display:none !important;
}
#tbl_coins_info
{
 display:none !important;
}
#tbl_coins_paginate
{
 display:none !important;
}
</style>
    <!-- Custom fonts for this template-->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link
        href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i"
        rel="stylesheet">

    <!-- Custom styles for this template-->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="../js_css/backend/vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
	

</head>

<body id="page-top">

<script type='text/javascript'>

function calcwinperpos()
{
	 var entry = GLOBALPRICE;
	 var ordertype = globalordermode;
	 var sl = $('#inputstoploss').val();
	 var tp = $('#inputtakeprofit').val();
	 var amnt = $('#rangeamount').val();
	 var lev = $('#rangeleverage').val();
/* __possize __leverage __estimatedprofit __estimatedloss */

$('#__possize').html('Position Size: <br>'+addCommas(parseInt(amnt))+' USDT');
$('#__leverage').html('Leverage: <br>'+parseInt(lev) +' x');

var esprofit= 0;
var esloss = 0;

var buytpestimated =0;
var buyslestimated =0;

var selltpestimated =0;
var sellslestimated =0;

if( ordertype==OP_BUY )
{

/* estimated profit */
if(  parseFloat(tp)>parseFloat(GLOBALPRICE))	{
 var reversedentry = 	parseFloat(entry)-parseFloat(tp)	;
 var _100per100 = 	parseFloat(tp);
 var perc = 	(parseFloat(reversedentry)*parseFloat(100)/parseFloat(tp))*parseFloat(lev);		
 var profitinusd = ((parseFloat(perc)*parseFloat(amnt)) /parseFloat(100));	
 profitinusd =-Math.abs(parseFloat(profitinusd));
 buytpestimated=( Math.abs(profitinusd));
 esprofit=parseFloat(buytpestimated)*parseFloat(2);
 $('#inputtakeprofit').css('background','white');
}
else
{
	$('#inputtakeprofit').css('background','coral');
}
/* end estimated profit */		

/* estimated loss */
if(  parseFloat(sl)<parseFloat(GLOBALPRICE))	{
var reversedentry = 	parseFloat(entry)-parseFloat(sl)	;
var _100per100 = 	parseFloat(sl);
var perc = 	(parseFloat(reversedentry)*parseFloat(100)/parseFloat(sl))*parseFloat(lev);		
var profitinusd = ((parseFloat(perc)*parseFloat(amnt)) /parseFloat(100));	
buyslestimated =-Math.abs(parseFloat(profitinusd));
 esloss=parseFloat(buyslestimated)*parseFloat(1);	
  $('#inputstoploss').css('background','white');
}
else
{
	$('#inputstoploss').css('background','coral');
}
/* end estimated loss */	
							
}

if( ordertype==OP_SELL)
{
	
/* estimated profit */
if(  parseFloat(tp)<parseFloat(GLOBALPRICE))	{
var reversedentry = 	parseFloat(entry)-parseFloat(tp)	;
var _100per100 = 	parseFloat(tp);
var perc = 	(parseFloat(reversedentry)*parseFloat(100)/parseFloat(tp))*parseFloat(lev);		
var profitinusd = ((parseFloat(perc)*parseFloat(amnt)) /parseFloat(100));	
profitinusd =-Math.abs(parseFloat(profitinusd));
selltpestimated =Math.abs(parseFloat(profitinusd));
 esprofit=parseFloat(selltpestimated)*parseFloat(1);
   $('#inputtakeprofit').css('background','white');
}
else
{
	$('#inputtakeprofit').css('background','coral');
}
 
/* end estimated profit */	

/* estimated loss */
if(  parseFloat(sl)>parseFloat(GLOBALPRICE))	{
var reversedentry = 	parseFloat(entry)-parseFloat(sl)	;
var _100per100 = 	parseFloat(sl);
var perc = 	(parseFloat(reversedentry)*parseFloat(100)/parseFloat(sl))*parseFloat(lev);		
var profitinusd = ((parseFloat(perc)*parseFloat(amnt)) /parseFloat(100));	
sellslestimated =-Math.abs(parseFloat(profitinusd));
esloss=parseFloat(sellslestimated)*parseFloat(2);		
 $('#inputstoploss').css('background','white');
}
/* end estimated loss */
		else
{
	$('#inputstoploss').css('background','coral');
}						
}


$('#__estimatedprofit').html('Estimated Profit: <br>'+addCommas(parseInt(esprofit)) +' USDT');
$('#__estimatedloss').html('Estimated Loss: <br>'+addCommas(parseInt(esloss)) +' USDT');
}

</script>
    <!-- Page Wrapper -->
    <div id="wrapper">

        <!-- Sidebar -->
        <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

            <!-- Sidebar - Brand -->
            <a class="sidebar-brand d-flex align-items-center justify-content-center" href="main.php">
                <div class="sidebar-brand-icon rotate-n-15">
                    <i class="fas fa-laugh-wink"></i>
                </div>
                <div class="sidebar-brand-text mx-3">Binance <sup>LeaderBoard Copier</sup></div>
            </a>

            <!-- Divider -->
            <hr class="sidebar-divider my-0">

            <!-- Nav Item - Dashboard -->
            <li class="nav-item active">
                <a class="nav-link" href="main.php">
                    <i class="fas fa-fw fa-pen"></i>
                    <span>Dashboard</span></a>
            </li>

            <!-- Divider -->
            <hr class="sidebar-divider">

            <!-- Heading -->
            <div class="sidebar-heading">
                Panel
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
            <li class="nav-item">
                <a class="nav-link" href="tradeshistory.php">
                    <i class="fas fa-fw fa-reply"></i>
                    <span>Trades History</span></a>
            </li>
 
             <!-- <li class="nav-item">
                <a class="nav-link" href="tables.html">
                    <i class="fas fa-fw fa-broadcast-tower"></i>
                    <span>Broadcast Message</span></a>
            </li>-->
			
			              <li class="nav-item">
                <a class="nav-link" href="tradablepairs.php">
                    <i class="fas fa-fw fa-coins"></i>
                    <span>Tradable Pairs</span></a>
            </li>
			
	
 
			
            <!-- Divider -->
         
            <!-- Heading -->
            <div class="sidebar-heading">
                Settings
            </div>

            <!-- Nav Item - Pages Collapse Menu -->
 

            <!-- Nav Item - Charts -->
			            <li class="nav-item">
                <a class="nav-link" href="manageclients.php">
                    <i class="fas fa-fw fa-user"></i>
                    <span>Create API</span></a>
            </li>
			
            <li class="nav-item">
                <a class="nav-link" href="profileandsecurity.php">
                    <i class="fas fa-fw fa-hammer"></i>
                    <span>Profile and Security</span></a>
            </li>

            <!-- Nav Item - Tables -->
            <li class="nav-item ">
                <a class="nav-link" href="globalparameters.php">
                    <i class="fas fa-fw fa-question-circle"></i>
                    <span>Global Parameters</span></a>
            </li>

            <li class="nav-item ">
                <a class="nav-link infinitelooop"  href="donation.php">
                    <i class="fas fa-heart 	" style = 'zoom:1.3;'></i>
                    <span style='text-transform:uppercase;'>Donate</span></a>
            </li>
			
            <li class="nav-item ">
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-fw fa-door-closed"></i>
                    <span>Logout</span></a>
            </li>
 
            <!-- Divider -->
            <hr class="sidebar-divider d-none d-md-block">

            <!-- Sidebar Toggler (Sidebar) -->
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>

      

        </ul>
        <!-- End of Sidebar -->

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

                <!-- Topbar -->
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                    <!-- Sidebar Toggle (Topbar) -->
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                        <i class="fa fa-bars"></i>
                    </button>

                    <!-- Topbar Search -->
 
                    <!-- Topbar Navbar -->
                    <ul class="navbar-nav ml-auto">

                        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
                        <li class="nav-item dropdown no-arrow d-sm-none">
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
                            <!-- Dropdown - Messages -->
                            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                                aria-labelledby="searchDropdown">
                                <form class="form-inline mr-auto w-100 navbar-search">
                                    <div class="input-group">
                                        <input type="text" class="form-control bg-light border-0 small"
                                            placeholder="Search for..." aria-label="Search"
                                            aria-describedby="basic-addon2">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="button">
                                                <i class="fas fa-search fa-sm"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </li>

                        <!-- Nav Item - Alerts -->
 

                        <!-- Nav Item - Messages -->
                      

                       

                        <!-- Nav Item - User Information -->
                        <li class="nav-item dropdown no-arrow">
                            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="mr-2 d-none d-lg-inline text-gray-600 small">Admin</span>
                                <img class="img-profile rounded-circle"
                                    src="img/undraw_profile.svg">
                            </a>
                            <!-- Dropdown - User Information -->
                            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                                aria-labelledby="userDropdown">
                               
                              
                              
                            
                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">
                                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                                    Logout
                                </a>
                            </div>
                        </li>

                    </ul>

                </nav>
                <!-- End of Topbar -->

                <!-- Begin Page Content -->
                <div class="container-fluid">

                    <!-- Page Heading -->
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1  id = 'vpsanddashboard' class="h3 mb-0 text-gray-800">Dashboard</h1>
                        
                    </div>

                    <!-- Content Row -->
 

                    <!-- Content Row -->

 

                    <!-- Content Row -->
                     <div class="container-fluid">
					     <div class="card shadow mb-4">
                        <div class="card-header py-3">
						  <h6 class="m-0 font-weight-bold text-primary">
						   Active Positions
						  </h6>
                            <h6 class="m-0 font-weight-bold text-primary"><button style='display:none;zoom:0.8;width:40%;' type="button" class="btn btn-success" onclick='broadcastposition();'>Broadcast</button>&nbsp;&nbsp;<hr style='visibility:hidden;'></h6>
                         </div>
                        <div class="card-body">
                            <div class="table-responsive" id='populateactivepositions'>
                           
                            </div>

                        </div>
						
                    </div>
					</div>

                </div>
                <!-- /.container-fluid -->

            </div>
            <!-- End of Main Content -->

            <!-- Footer -->
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>My Telegram: &nbsp;&nbsp;<?php 
						 $link = mysqli_connect($config['server'], $config['dbusername'], $config['dbpass']);

if (mysqli_connect_errno())
  {
  echo "ERROR Failed to connect to MySQL: " . mysqli_connect_error();
  }
 
// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

 
mysqli_select_db($link, "binancefutures");

$query = "select param_value as cpy from global_parameters where param_name = 'param_copyright';";	
$resultquery = mysqli_query($link, $query) or die(mysqli_error($link));
		  while($rowquery = mysqli_fetch_assoc($resultquery)) {
			  extract($rowquery);
		  }
		  echo $cpy;
						 ?></span>
                    </div>
                </div>
            </footer>
            <!-- End of Footer -->

        </div>
        <!-- End of Content Wrapper -->

    </div>
    <!-- End of Page Wrapper -->

    <!-- Scroll to Top Button-->
    <a class="scroll-to-top rounded" href="#page-top">
        <i class="fas fa-angle-up"></i>
    </a>

    <!-- Logout Modal-->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Ready to Leave?</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Select "Logout" below if you are ready to end your current session.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a class="btn btn-primary" href="logout.php">Logout</a>
                </div>
            </div>
        </div>
    </div>

		    <div class="modal fade" id="addnewcoinmodal" style='overflow-y: visible;' tabindex="-1" role="dialog" aria-labelledby="addnewcoinlabel"
        aria-hidden="true">
		
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addnewcoinlabel">BroadCast a new position &nbsp;&nbsp;&nbsp;<button onclick="populatescreener(); $('#cryptoscreener').modal('show');" type="button" class="btn btn-info">Crypto Screener</button></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
		           <div class="card-body">
				   
                            <div class="table-responsive" id='populateactivecoins'>
                           
                            </div>
							 
							
							
							
							<!-- TradingView Widget BEGIN -->
<div class="tradingview-widget-container">
  <div id="tradingview_576df"></div>
  <div class="tradingview-widget-copyright">
  
  <script type="text/javascript" src="https://s3.tradingview.com/tv.js"></script> 
 
  <script type="text/javascript">

  var countDecimals = function(value) {
    if (Math.floor(value) !== value)
        return value.toString().split(".")[1].length || 0;
    return 0;
}

  function globalsym (a)
  { 
	  globalsymbolname=a;
	   
  }
  
    function  globaldecimal(a)
  { 
	  globaldigitdecimalplace=a;
	   
  }
  
  
 function _opennewposition()
 {
	  $('#inputstoploss').css('background','white');
	  $('#inputtakeprofit').css('background','white');
    //document.getElementById("confirmbuysell").disabled = true;
	
	//alert(GLOBALPRICE);
	//return false;
	
 

	if(globalordermode == OP_BUY)
	{
		 
		if( parseFloat($('#inputstoploss').val()) != parseFloat(0))
		{
			if( parseFloat($('#inputstoploss').val())  > GLOBALPRICE)
			{
				$('#inputstoploss').val(0); $('#inputstoploss').focus(); return false;
			}
		}
		
		if( parseFloat($('#inputtakeprofit').val()) != parseFloat(0))
		{
			if( parseFloat($('#inputtakeprofit').val())  < GLOBALPRICE)
			{
				$('#inputtakeprofit').val(0); $('#inputtakeprofit').focus(); return false;
			}
		}		
	}

	if(globalordermode == OP_SELL)
	{
	
		
		if( parseFloat($('#inputstoploss').val()) != parseFloat(0))
		{
			if( parseFloat($('#inputstoploss').val())  < GLOBALPRICE)
			{	 
				$('#inputstoploss').val(0); $('#inputstoploss').focus(); return false;
			}
		}
		
		if( parseFloat($('#inputtakeprofit').val()) != parseFloat(0))
		{
			if( parseFloat($('#inputtakeprofit').val())  > GLOBALPRICE)
			{
				$('#inputtakeprofit').val(0); $('#inputtakeprofit').focus(); return false;
			}
		}		
		
		
		
	}
	 
	
 
	var _sym =globalsymbolname;
	var ordermode= globalordermode;
	var sl =$('#inputstoploss').val();
	var tp =$('#inputtakeprofit').val();
	var amnt =$('#rangeamount').val();
	var lvg =$('#rangeleverage').val();
	
	var estprofit = $('#__estimatedprofit').html();
	var estloss   = $('#__estimatedloss').html();
 
		 $.ajax({

     type: "GET",
     url: 'ajax/listener.php?actionmode=opennewposition&sym='+_sym+'&ordermode='+ordermode+'&sl='+sl+'&tp='+tp+'&amnt='+amnt+'&lvg='+lvg+'&estprofit='+estprofit+'&estloss='+estloss,
     data: theurl, // appears as $_GET['id'] @ your backend side
     success: function(data) {


 
     $('#addnewcoinmodal').modal('hide');

	   
populateactivetrades();		 
     }

   });	
   
   
 

   
	
 } 
  </script>
</div>
  </div>
<!-- TradingView Widget END -->
 

<hr style='visibility:hidden;'>
							    <button onclick='buybtn(globaldigitdecimalplace);'id='buycrypto' name='buycrypto' style='width:45%;float:left;'type="button" class="btn btn-success">BUY</button>
							    <button onclick='sellbtn(globaldigitdecimalplace)' id='sellcrypto' name='sellcrypto' style='width:45%;float:right;' type="button" class="btn btn-danger">SELL</button>
							    
                                    <div class='sltpdiv' style='width:100%;display:block;'>
																 <div style='width:100%;display:block;margin-top:11.9% !important;'>
							    <label style='width:23%;display:inline-block;' for="inputstoploss"><b style='color:red;'>Stop Loss</b></label>
                                <input type="number"    style='width:48%;display:inline-block;' class="form-control" min='0' id="inputstoploss" name='inputstoploss' placeholder="Stop Loss" >
								<span  style='width:23%;display:none;margin-left:1.2%;'class="badgestoploss">0 pip</span>
								</div>
							    <div style='width:100%;display:block;margin-top:2.5%!important;'>
								<label  style='width:23%;display:inline-block;'for="inputtakeprofit"><b style='color:green;'>Take Profit</b></label>
                                <input type="number"    style='width:48%;display:inline-block;' class="form-control" id="inputtakeprofit" min='0' name='inputtakeprofit' placeholder="Take Profit" >
								<span  style='width:23%;display:none;margin-left:1.2%;'class="badgetakeprofit">0 pip</span>
                               </div>	
                               <hr style = 'visibility:hidden;'>
							    <p class='rangeamountlbl'>Amount: 100 USDT</p>
  <input type="range" min="50" max="5000" value="100" class="slider" step='50' id="rangeamount">
  <hr>
   <p class='rangeleveragelbl'>Leverage: 2x</p>
  <input type="range" min="1" max="5" value="2" class="slider" step='1' id="rangeleverage">
  
							    <ul class="list-group list-group-horizontal" style='text-align:center;color:black;font-weight:bolder;width:100%;'>

  <li class="list-group-item" style='font-size:x-small;width:25%;background:lightgrey;' id='__possize'></li>
  <li class="list-group-item" style='font-size:x-small;width:25%;background:lightgrey;' id='__leverage'></li>
  <li class="list-group-item" class='approxprofit' style='font-size:x-small;width:25%;background:lightgrey;' id='__estimatedprofit'></li>
  <li class="list-group-item" class='approxloss'   style='font-size:x-small;width:25%;background:lightgrey;' id='__estimatedloss'></li>
  
</ul>
							   </div>									
							   <button id='confirmbuysell' name='confirmbuysell' onclick='_opennewposition()' style='width:100%;margin-top:3%;'  type="button" class="btn btn-primary">Confirm</button>
						 
                             
                             
							
                      
 
				
				</div>
 
            </div>
        </div>
    </div>
	</div>
	
	
	
	
	
	
	
	<!-- ------------------------720 lal 908 -->
			    <div class="modal fade" id="cryptoscreener" style='' tabindex="-1" role="dialog" aria-labelledby="cryptoscreen"
        aria-hidden="true">
		
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cryptoscreen">Crypto Screener</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body" id='cryptoscreenercontainer'>
		           <div class="card-body">
	 	
                                <div class="table-responsive" id="populatescreener">
                                        <table style=" cursor: pointer;" class="table table-bordered" id="populatescreenertbl" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>Symbol</th>
                                                    <th>1 m <sup style="zoom: 0.86;"></sup></th>
                                                    <th>5 m <sup style="zoom: 0.86;"></sup></th>
                                                    <th>15 m <sup style="zoom: 0.86;"></sup></th>
                                                    <th>30 m <sup style="zoom: 0.86;"></sup></th>
                                                    <th>1 h<sup style="zoom: 0.86;"></sup></th>
                                                    <th>2 h <sup style="zoom: 0.86;"></sup></th>
                                                    <th>4 h <sup style="zoom: 0.86;"></sup></th>
                                                    <th>6 h <sup style="zoom: 0.86;"></sup></th>
                                                    <th>12 h <sup style="zoom: 0.86;"></sup></th>
                                                    <th>1 D <sup style="zoom: 0.86;"></sup></th>
                                                    <th>3 D <sup style="zoom: 0.86;"></sup></th>
                                                    <th>1 W <sup style="zoom: 0.86;"></sup></th>
                                                    <th>1 M <sup style="zoom: 0.86;"></sup></th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                        </table>
                                    </div>
									 
					   
 
				
				</div>
 
            </div>
        </div>
    </div>
	</div>
	
	
	
	
	
			    <div class="modal fade" id="closeposition" style='' tabindex="-1" role="dialog" aria-labelledby="closepos"
        aria-hidden="true">
		
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="closepos">Close Position and Broadcast to Telegram <br><sup style='color:coral;'>This action is irreversible</sup></h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
		           <div class="card-body">
                  
				  <table id='tbl_closeposition' style='zoom:1.3;width:100%;text-align:center;  border: 1px solid black;'>
				   <tr style='width:100%;outline: thin solid;'>
				   <td style='width:50%;text-align:left;' class=''>Position HashCode:</td>
				   <td style='width:50%;text-align:left;' class='close_dialogue_orderid'></td>
				   </tr>	
				   <tr style='width:100%;outline: thin solid;'>
				   <td style='width:50%;text-align:left;' class=''>Position opened since:</td>
				   <td style='width:50%;text-align:left;' class='close_dialogue_date'></td>
				   </tr>
				   <tr style='width:100%;outline: thin solid;'>
				   <td style='width:50%;text-align:left;' class=''>Position Symbol:</td>
				   <td style='width:50%;text-align:left;' class='close_dialogue_symbol'></td>
				   </tr>
				    <tr style='width:100%;outline: thin solid;'>
				   <td style='width:50%;text-align:left;' class=''>Position Amount:</td>
				   <td style='width:50%;text-align:left;' class='close_dialogue_amount'></td>
				   </tr>
				    <tr style='width:100%;outline: thin solid;'>
				   <td style='width:50%;text-align:left;' class=''>Position Type:</td>
				   <td style='width:50%;text-align:left;' class='close_dialogue_type'></td>
				   </tr>
				    <tr style='width:100%;outline: thin solid;'>
				   <td style='width:50%;text-align:left;' class=''>Position Leverage:</td>
				   <td style='width:50%;text-align:left;' class='close_dialogue_leverage'></td>
				   </tr>
				    <tr style='width:100%;outline: thin solid;'>
				   <td style='width:50%;text-align:left;' class=''>Position Entry:</td>
				   <td style='width:50%;text-align:left;' class='close_dialogue_entry'></td>
				   </tr>
				    <tr style='width:100%;outline: thin solid;'>
				   <td style='width:50%;text-align:left;' class=''>Market Mark Price:</td>
				   <td style='width:50%;text-align:left;' class='close_dialogue_markprice'></td>
				   </tr>
				    <tr style='width:100%;outline: thin solid;'>
				   <td style='width:50%;text-align:left;' class=''>Position Profit:</td>
				   <td style='width:50%;text-align:left;' class='close_dialogue_profit'></td>
				   </tr>								   
				  </table>
 
				
				</div>
 
            </div>
			        <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <a  id='close_pos_conf' class="btn btn-primary" onclick= 'closepositionconfirmed();'>Close Trade</a>
                </div>
				
        </div>
    </div>
	</div>
    <!-- Bootstrap core JavaScript-->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Core plugin JavaScript-->
    <script src="vendor/jquery-easing/jquery.easing.min.js"></script>

    <!-- Custom scripts for all pages-->
    <script src="js/sb-admin-2.min.js"></script>

    <!-- Page level plugins -->
    <script src="vendor/chart.js/Chart.min.js"></script>
	<script src="../js_css/backend/vendor/datatables/jquery.dataTables.js"></script>

    <!-- Page level custom scripts -->
   <!-- <script src="js/demo/chart-area-demo.js"></script>
    <script src="js/demo/chart-pie-demo.js"></script> -->
<script>

function disablebuysellhidesltp()
{
 
	
	$("#buycrypto").css("opacity","1");
    $("#sellcrypto").css("opacity","1");
	$('#inputstoploss').val(0);
	$('#inputtakeprofit').val(0);
	$('.sltpdiv').fadeOut();	
	document.getElementById("confirmbuysell").disabled = true;
	$("#confirmbuysell").css("opacity","0.6");
	
}
$(document).ready(function()
{
	var rangeamt = document.getElementById("rangeamount");
    var rangelvg = document.getElementById("rangeleverage");


rangeamt.oninput = function() {
  $('.rangeamountlbl').html('Amount: ' +this.value + ' USDT');
  
   
  calcwinperpos();
}

rangelvg.oninput = function() {
   $('.rangeleveragelbl').html('Leverage: ' +this.value+'x');
     calcwinperpos();
}

	  populateactivetrades();
	  populateactivecoins();
	  
	  /* Do Not Delete : later to use auto trading api
	  var popup =  window.open("https://www.binance.com/en/futures-activity/leaderboard/user?uid=15F8F40F6A8E7CB6F7D2602CD320430A&tradeType=PERPETUAL", "binancepopup", "width=500,height=500");
      setTimeout(function(){console.log(popup.document.getElementsByTagName("BODY")[0].innerHTML );}, 10000); 
      End Do Not Delete */
});

function populateactivecoins()
{
	theurl = '';
	 
	 $.ajax({

     type: "GET",
     url: 'ajax/getlistoftradablepairs2.php',
     data: theurl, // appears as $_GET['id'] @ your backend side
     success: function(data) {
           // data is ur summary
          $('#populateactivecoins').html(data);
		 
		  
		  var table = $('#tbl_coins').DataTable({
			     "aaSorting": [],
			     "pageLength": 5,
 "bSort" : false
});

		  
		 
     }

   });	
	
}
	function populateactivetrades()
{
	 
  
	theurl = '';
	 
	 $.ajax({

     type: "GET",
     url: 'ajax/getlistoftrades.php',
     data: theurl, // appears as $_GET['id'] @ your backend side
     success: function(data) {
           // data is ur summary
          $('#populateactivepositions').html(data);
		 
		  
		  var table = $('#tbl_openedpositions').DataTable({
			   responsive: true,
			  "aaSorting": [],
			  "pageLength" : 25,
   'aoColumnDefs': [{
        'bSortable': false,
        'aTargets': [-1,-2,-3,-4,-5,-6] /* 1st one, start by the right */
    }]
});

		  
		 
     }

   });
}
   
   
   		(function($) {
    $(document).ready(function() {
		setInterval(function(){ 
		 
		  $( ".tradesymbol" ).find('.thesymbol').each(function( index ) {
         var symname = $(this).html();
		 var nearestmarkeprice = $(this).closest( "tr" ).find( ".trademarkeprice" );
		 var liquidation = $(this).closest( "tr" ).find( ".liqid_p" );
		 var nearestleverage = $(this).closest( "tr" ).find( ".trade_leverage" );
		 var nearestliquidation = $(this).closest( "tr" ).find( ".lqd_level" );
		 var nearestamt = $(this).closest( "tr" ).find( ".trd_amnt" );
		 var nearesttradeprofitloss = $(this).closest( "tr" ).find( ".tradeprofitloss" );
		 var nearesttradetype= $(this).closest( "tr" ).find( ".tradetype" );
		 var nearesttradeentry= $(this).closest( "tr" ).find( ".tradentry" );
		 var coindigits= $(this).closest( "tr" ).find( ".coin_digits" );
		 	theurl = '';
	 
if( window.navigator.onLine)
{
		 $.ajax({

     type: "GET",
     url: 'https://api.binance.com/api/v3/ticker/price?symbol='+symname,
     data: theurl, // appears as $_GET['id'] @ your backend side
     success: function(data) {
		 var pricedata=data.price;
		 var profit =0;
		   pricedata = parseFloat(pricedata);
		  
             $(nearestmarkeprice).html(addCommas(parseFloat(pricedata).toFixed(parseInt($(coindigits).val()))));
             
			  if($(nearesttradetype).html().indexOf("BUY") >= 0)
			 {
				 
			 var entry =  parseFloat(removeCommas($(nearesttradeentry).html()));
			 var price =  parseFloat(removeCommas($(nearestmarkeprice).html()));
			 var nearest_amt =  parseFloat(removeCommas($(nearestamt).html()));
			  
 
			  
			  if ( parseFloat(price) >=parseFloat(entry))
			  {
			 
                                var reversedentry = 	parseFloat(entry)-parseFloat(price)	;
                                var _100per100 = 	parseFloat(price);
                                var perc = 	(parseFloat(reversedentry)*parseFloat(100)/parseFloat(price))*parseFloat($(nearestleverage).html());		
                                
                                var profitinusd = ((parseFloat(perc)*parseFloat(nearest_amt)) /parseFloat(100));	
                                profitinusd =-Math.abs(parseFloat(profitinusd));
								profitinusd=parseFloat(2)*parseFloat(( Math.abs(profitinusd)));
								cumuusd = parseFloat(cumuusd)+Math.abs(parseFloat(profitinusd));	
                                profitpercentage = 	((parseFloat(profitinusd) *parseFloat(100))/		parseFloat(nearest_amt));
									cumudd = parseFloat(cumudd)+Math.abs(parseFloat(profitpercentage));
								
			 $(nearesttradeprofitloss).css("color","lightgreen");
			  }
			  
			  else
			  {
                                var reversedentry = 	parseFloat(entry)-parseFloat(price)	;
                                var _100per100 = 	parseFloat(price);
                                var perc = 	(parseFloat(reversedentry)*parseFloat(100)/parseFloat(price))*parseFloat($(nearestleverage).html());		
                                
                                var profitinusd = ((parseFloat(perc)*parseFloat(nearest_amt)) /parseFloat(100));	
                                profitinusd =parseFloat(1)*parseFloat((-Math.abs(parseFloat(profitinusd))));
								cumuusd = parseFloat(cumuusd)-Math.abs(parseFloat(profitinusd))
                                profitpercentage = 	((parseFloat(profitinusd) *parseFloat(100))/		parseFloat(nearest_amt));	
									cumudd = parseFloat(cumudd)-Math.abs(parseFloat(profitpercentage));
                                $(nearesttradeprofitloss).css("color","coral");								
					   
			  }
			 
			 // if(parseInt(profitpercentage)<=parseInt(-90)) code for vps
			 
			 
			 var _entry =     ($(nearesttradeentry).html());
			 var _leverage =  ($(nearestleverage).html());
			 
			 
			 _entry=parseFloat(removeCommas(_entry));
			 _leverage=parseFloat(removeCommas(_leverage));
			 
			 liq__price= parseInt(1)/parseInt(_leverage);
			 liq__price= parseFloat(_entry) * parseFloat(liq__price);
			 liq__price = parseFloat(_entry)- parseFloat(liq__price);
			 liq__price = parseFloat(liq__price).toFixed(parseInt($(coindigits).val()));
			 liq__price=addCommas(liq__price);
			  
			// // // // $(nearestliquidation).html('<i>Liquidation Price</i><br><b> approx: ' +addCommas(liq__price)+'</b>');
			 
			 
			 
			 $(nearesttradeprofitloss).html( addCommas(parseFloat(profitinusd).toFixed(2)) + '<i>$</i><br>' +addCommas(parseFloat(profitpercentage).toFixed(2)) +'<i> %</i> ');	
			 
			 }
           

 


			  if($(nearesttradetype).html().indexOf("SELL") >= 0)
			 {
				 
			 var entry =  parseFloat(removeCommas($(nearesttradeentry).html()));
			 var price =  parseFloat(removeCommas($(nearestmarkeprice).html()));
			 var nearest_amt =  parseFloat(removeCommas($(nearestamt).html()));
			  
			  if ( parseFloat(entry)  >= parseFloat(price))
			  {
				  
                                var reversedentry = 	parseFloat(price)-parseFloat(entry)	;
                                var _100per100 = 	parseFloat(entry);
                                var perc = 	(parseFloat(reversedentry)*parseFloat(100)/parseFloat(entry))*parseFloat($(nearestleverage).html());		
                                
                                var profitinusd = ((parseFloat(perc)*parseFloat(nearest_amt)) /parseFloat(100));	
                                profitinusd =-Math.abs(parseFloat(profitinusd));
                                profitinusd =parseFloat(2)*parseFloat((Math.abs(parseFloat(profitinusd))));
								cumuusd = parseFloat(cumuusd)+Math.abs(parseFloat(profitinusd))	
                                profitpercentage = 	((parseFloat(profitinusd) *parseFloat(100))/		parseFloat(nearest_amt));	
cumudd = parseFloat(cumudd)+Math.abs(parseFloat(profitpercentage));										
			 $(nearesttradeprofitloss).css("color","lightgreen");
			  }
			  
			  else
			  {
                                var reversedentry = 	parseFloat(price)-parseFloat(entry)	;
                                var _100per100 = 	parseFloat(entry);
                                var perc = 	(parseFloat(reversedentry)*parseFloat(100)/parseFloat(entry))*parseFloat($(nearestleverage).html());		
                                
                                var profitinusd = ((parseFloat(perc)*parseFloat(nearest_amt)) /parseFloat(100));	
                                profitinusd =parseFloat(1)*parseFloat((-Math.abs(parseFloat(profitinusd))));
								cumuusd = parseFloat(cumuusd)-Math.abs(parseFloat(profitinusd))
                                profitpercentage = 	((parseFloat(profitinusd) *parseFloat(100))/		parseFloat(nearest_amt));	
									cumudd = parseFloat(cumudd)-Math.abs(parseFloat(profitpercentage));
                                $(nearesttradeprofitloss).css("color","coral");								
				   
			  }
			 
			 
			 //if(parseInt(profitpercentage)<=parseInt(-90)) code for vps
			
			 var _entry =     ($(nearesttradeentry).html());
			 var _leverage =  ($(nearestleverage).html());
			 
			 _entry=parseFloat(removeCommas(_entry));
			 _leverage=parseFloat(removeCommas(_leverage));
			 
			 liq__price= parseInt(1)/parseInt(_leverage);
			 liq__price= parseFloat(_entry) * parseFloat(liq__price);
			 liq__price = parseFloat(_entry)+ parseFloat(liq__price);
		     liq__price = parseFloat(liq__price).toFixed(parseInt($(coindigits).val()));
			 liq__price=addCommas(liq__price);
			// // // // $(nearestliquidation).html('<i>Liquidation Price</i> <b> approx: ' +liq__price+'</b>');
			 
			 $(nearesttradeprofitloss).html( addCommas(parseFloat(profitinusd).toFixed(2)) + '<i>$</i><br>' +addCommas(parseFloat(profitpercentage).toFixed(2)) +'<i> %</i> ');		
			 
			 }
			 
			 
 

			 
			 
     }

   });
}

 // END OF ITERATION



 	  
});
  if(parseInt(cumuusd)>=0)
  {
  $('.cumu_usd').html(''+addCommas(parseFloat(cumuusd).toFixed(2))+' USDT');
  $('.cumu_usd').css("color","lightgreen");
  }
  if(parseInt(cumuusd)<0)
  {
  $('.cumu_usd').html(''+addCommas(parseFloat(cumuusd).toFixed(2))+' USDT');
  $('.cumu_usd').css("color","coral"); 
  }
  
   if(parseInt(cumudd)>=0)
  {
  $('.cumu_dd').html(''+addCommas(parseFloat(-cumudd).toFixed(2))+' %');
  $('.cumu_dd').css("color","lightgreen");
  }
  if(parseInt(cumudd)<0)
  {
  $('.cumu_dd').html(''+addCommas(parseFloat(-cumudd).toFixed(2))+' %');
  $('.cumu_dd').css("color","coral"); 
  }
  
cumuusd=0;	
cumudd=0;


		}, globalquotesrefreshinterval);
  
    });
})(jQuery);


   
 
 function closeposition(_this)
 {
	 globalpostionpopup = _this;
	 var isexist = $(_this).closest( "tr" ).find( ".tradeprofitloss" ).html().toString();
	 var thislen = isexist.toString().length;
	 
	 if( parseInt(thislen)>parseInt(1))
	 {
	 $('#closeposition').modal('show');
	 
	 }
 
 
	 
setTimeout(function(){ 
 try {
  var trdamt = $('tr.'+GLOBALPOSITIONHASH).find('td.tradeamountdisp').find('.trd_amnt').html();
  $('.close_dialogue_amount').html( (trdamt) + ' USDT');
  
  var trdtype = $('tr.'+GLOBALPOSITIONHASH).find('td.tradetype').find('.trdtypebold').html();
  $('.close_dialogue_type').html(trdtype);
  
  
  var trdleverage = $('tr.'+GLOBALPOSITIONHASH).find('td.tradelvg').find('.trade_leverage').html();
  $('.close_dialogue_leverage').html(trdleverage+'x');
  
  var posentry = $('tr.'+GLOBALPOSITIONHASH).find('td.tradentry').html();
  $('.close_dialogue_entry').html( (posentry)+ ' USDT');
  
    var posmarkprice = $('tr.'+GLOBALPOSITIONHASH).find('td.trademarkeprice').html();
  $('.close_dialogue_markprice').html( (posmarkprice)+ ' USDT');
  
  var posprofitloss = $('tr.'+GLOBALPOSITIONHASH).find('td.tradeprofitloss').html().toString().substr(0, $('tr.'+GLOBALPOSITIONHASH).find('td.tradeprofitloss').html().toString().indexOf('<'));
  $('.close_dialogue_profit').html( (posprofitloss) + ' USDT');
  
  
  
 var posdate = $('tr.'+GLOBALPOSITIONHASH).find('td.trd__date').find('.b_date_').html();
  $('.close_dialogue_date').html(posdate);
  
 var possymbol = $('tr.'+GLOBALPOSITIONHASH).find('td.tradesymbol').find('.thesymbol').html();
  $('.close_dialogue_symbol').html(possymbol);  
  
   var posorderid = GLOBALPOSITIONHASH;
  $('.close_dialogue_orderid').html(posorderid);  
  }
catch(err) {
  
}
  
}, 250);
	 
 }

function closepositionconfirmed()
{
	
	document.getElementById("close_pos_conf").disabled = true;
	var cumuposinfo = '<b><i>We have Manually Closed this position</i></b>';
	var table = document.getElementById('tbl_closeposition');
	var rowLength = table.rows.length;

for(var i=0; i<rowLength; i+=1){
  var row = table.rows[i];

  //your code goes here, looping over every row.
  //cells are accessed as easy

  var cellLength = row.cells.length;
  cumuposinfo=cumuposinfo+"[BRK]";
  for(var y=0; y<cellLength; y+=1){
    var cell = row.cells[y];

    if(y==0)
	{
		 cumuposinfo = cumuposinfo +"[BRK]<b>"+($(cell).html().toString())+"</b> ";
	}
	else{
		 cumuposinfo = cumuposinfo +"[BRK]<i><u>"+($(cell).html().toString())+"</u></i> ";
	}
    
  }
}
cumuposinfo= cumuposinfo+'[BRK][BRK]for more info and statistics  please visit your webportal. ';

var positionhash = $('.close_dialogue_orderid').html();
var markprice = parseFloat(removeCommas($('.close_dialogue_markprice').html()));
var tradeprofit = $('.close_dialogue_profit').html().toString().replace("USDT","");
var tradeprofit =removeCommas(tradeprofit);
 

 
		 		 $.ajax({

     type: "GET",
     url: 'ajax/listener.php?actionmode=closeposition&positionhash='+positionhash+'&tradeprofit='+tradeprofit+'&markprice='+markprice+'&text='+cumuposinfo,
     data: theurl, // appears as $_GET['id'] @ your backend side
     success: function(data) {
		 
      populateactivetrades();	
	  $('#closeposition').modal('hide');	
		 
     }

   });



}


function broadcastposition()
{
    //$('#coinabbr').val('');
	//$('#coinname').val('');
	//$('#img-upload').removeAttr('src');
	//$('#fileInput').val('');
$('#addnewcoinmodal').modal('show');
setTimeout(function(){
$('.sltpdiv').fadeOut();	
$('#tradingview_576df').fadeOut();	
//$('#coinabbr').focus();
$('#tbl_coins_filter').find('input').focus();

$("#buycrypto").css("opacity","0.6");
$("#sellcrypto").css("opacity","0.6");

document.getElementById("buycrypto").disabled = true;
document.getElementById("sellcrypto").disabled = true;
document.getElementById("confirmbuysell").disabled = true;


$("#confirmbuysell").css("opacity","0.6");
    	 
 }, 800);	
	
}

function displaytradingview(abr)
{
	
	setTimeout(function(){ $('#buycrypto').focus();}, 400);
	
	document.getElementById("buycrypto").disabled = false;
    document.getElementById("sellcrypto").disabled = false;

	 $('#tradingview_576df').fadeIn();	
	$("#buycrypto").css("opacity","1");
$("#sellcrypto").css("opacity","1");
	  var width = document.getElementById('tbl_coins_filter').offsetWidth;
	  var height=500;
  new TradingView.widget(
  {
  "width": width,
  "height": height,
  "symbol": abr+"USDT",
  "interval": "D",
  "timezone": "Etc/UTC",
  "theme": "light",
  "style": "1",
  "locale": "en",
  "toolbar_bg": "#f1f3f6",
  "enable_publishing": false,
  "allow_symbol_change": true,
  "container_id": "tradingview_576df"
}
  );
}

function buybtn(_digits)
{
	

$('.rangeamountlbl').html('Amount: 100 USDT');	
$('.rangeleveragelbl').html('Leverage: 2x');
	
$('#rangeamount').val(100);	
$('#rangeleverage').val(2);
	 
	globalordermode = OP_BUY;
	document.getElementById("confirmbuysell").disabled = false;
	$('#confirmbuysell').css("opacity","1");
	var _step=1;
	if(globaldigitdecimalplace==0) {_step=10;}
	if(globaldigitdecimalplace==1) {_step=1;}
	if(globaldigitdecimalplace==2) {_step=0.1;}
	if(globaldigitdecimalplace==3) {_step=0.01;}
	if(globaldigitdecimalplace==4) {_step=0.001;}
	if(globaldigitdecimalplace==5) {_step=0.0001;}
	if(globaldigitdecimalplace==6) {_step=0.00001;}
	if(globaldigitdecimalplace==7) {_step=0.000001;}
	if(globaldigitdecimalplace==8) {_step=0.0000001;}
	if(globaldigitdecimalplace==9) {_step=0.00000001;}
	if(globaldigitdecimalplace==10){_step=0.000000001;}
	if(globaldigitdecimalplace==11){_step=0.0000000001;}
	if(globaldigitdecimalplace==12){_step=0.00000000001;}
	if(globaldigitdecimalplace==13){_step=0.000000000001;}
	if(globaldigitdecimalplace==14){_step=0.0000000000001;}
	if(globaldigitdecimalplace==15){_step=0.00000000000001;}
	 
	$("#inputstoploss").attr('step', _step);
	$("#inputtakeprofit").attr('step', _step);
	
	
	
		 		 $.ajax({

     type: "GET",
     url: 'https://api.binance.com/api/v3/ticker/price?symbol='+globalsymbolname+"USDT",
     data: theurl, // appears as $_GET['id'] @ your backend side
     success: function(data) {
		 
      var pr = data.price;
	  GLOBALPRICE = data.price;
	  var prtp = parseInt(GLOBALPRICE)+parseInt(GLOBALTP)*parseFloat(pr)/parseInt(100);
	  var prsl = parseInt(GLOBALPRICE)-parseInt(GLOBALSL)*parseFloat(pr)/parseInt(100);
	  
	  globalstoploss = prsl;
	  globaltakeprofit = prtp;
	   $('.badgestoploss').html(GLOBALSL+' pips');
	   $('.badgetakeprofit').html(GLOBALTP+' pips');
	  if(parseInt(globaldigitdecimalplace) ==parseInt(1) || parseInt(globaldigitdecimalplace) ==parseInt(2))
	  {		  
	  prtp = prtp.toFixed(parseInt(2));
      prsl = prsl.toFixed(parseInt(2));
	  }
else
{ 
      prtp = prtp.toFixed(parseInt(globaldigitdecimalplace));
      prsl = prsl.toFixed(parseInt(globaldigitdecimalplace));
}	  
	
    // $("#inputstoploss").attr('max', prsl);	
     $("#inputtakeprofit").attr('min', 0);	
	
     $("#inputstoploss").attr('min',   0);	
   
	
	
	
//	$("#inputstoploss").val(prsl);
//	$("#inputtakeprofit").val(prtp);
		 
     }

   });
   
   
	
	setTimeout(function(){ $('#confirmbuysell').focus();}, 400);
	$('.sltpdiv').fadeIn();
    $('#sellcrypto').css("opacity","0.4");
	 $('#buycrypto').css("opacity","1");
	 
	 		 $.ajax({

     type: "GET",
     url: 'https://api.binance.com/api/v3/ticker/price?symbol='+globalsymbolname+"USDT",
     data: theurl, // appears as $_GET['id'] @ your backend side
     success: function(data) {
	
		 
     }

   });
	 
 setTimeout(function(){ calcwinperpos(); }, 1200);	 
	 
	 
	  
}
function sellbtn(_digits)
{
	
	$('.rangeamountlbl').html('Amount: 100 USDT');	
$('.rangeleveragelbl').html('Leverage: 2x');
	
$('#rangeamount').val(100);	
$('#rangeleverage').val(2);


	globalordermode = OP_SELL;
	document.getElementById("confirmbuysell").disabled = false;
	$('#confirmbuysell').css("opacity","1");
	var _step=1;
	if(globaldigitdecimalplace==0) {_step=10;}
	if(globaldigitdecimalplace==1) {_step=1;}
	if(globaldigitdecimalplace==2) {_step=0.1;}
	if(globaldigitdecimalplace==3) {_step=0.01;}
	if(globaldigitdecimalplace==4) {_step=0.001;}
	if(globaldigitdecimalplace==5) {_step=0.0001;}
	if(globaldigitdecimalplace==6) {_step=0.00001;}
	if(globaldigitdecimalplace==7) {_step=0.000001;}
	if(globaldigitdecimalplace==8) {_step=0.0000001;}
	if(globaldigitdecimalplace==9) {_step=0.00000001;}
	if(globaldigitdecimalplace==10){_step=0.000000001;}
	if(globaldigitdecimalplace==11){_step=0.0000000001;}
	if(globaldigitdecimalplace==12){_step=0.00000000001;}
	if(globaldigitdecimalplace==13){_step=0.000000000001;}
	if(globaldigitdecimalplace==14){_step=0.0000000000001;}
	if(globaldigitdecimalplace==15){_step=0.00000000000001;}
	 
	$("#inputstoploss").attr('step', _step);
	$("#inputtakeprofit").attr('step', _step);
	
		 		 $.ajax({

     type: "GET",
     url: 'https://api.binance.com/api/v3/ticker/price?symbol='+globalsymbolname+"USDT",
     data: theurl, // appears as $_GET['id'] @ your backend side
     success: function(data) {
		 
      var pr = data.price;
	  GLOBALPRICE = data.price;
	  var prsl =   parseInt(GLOBALPRICE)-parseInt(GLOBALTP)*parseFloat(pr)/parseInt(100); 
	  var prtp =   parseInt(GLOBALPRICE)+parseInt(GLOBALSL)*parseFloat(pr)/parseInt(100);
	  
	  globalstoploss = prsl;
	  globaltakeprofit = prtp;
	  
	  
	   	  $('.badgestoploss').html(GLOBALSL+' pips');
	       $('.badgetakeprofit').html(GLOBALTP+' pips');
	  
	  if(parseInt(globaldigitdecimalplace) ==parseInt(1) || parseInt(globaldigitdecimalplace) ==parseInt(2))
	  {		  
	  prtp = prtp.toFixed(parseInt(2));
      prsl = prsl.toFixed(parseInt(2));
	  }
else
{ 
      prtp = prtp.toFixed(parseInt(globaldigitdecimalplace));
      prsl = prsl.toFixed(parseInt(globaldigitdecimalplace));
}	  
	 
  //   $("#inputstoploss").attr('min', prsl);	
   //  $("#inputtakeprofit").attr('max', prtp);	
	
	 //    $("#inputstoploss").attr('max',   +1000000000);	
         $("#inputtakeprofit").attr('min', 0);	
         $("#inputstoploss").attr('min', 0);	
	
//	$("#inputstoploss").val(prtp);
//	$("#inputtakeprofit").val(prsl);
		 
     }

   });
   
   
	
	setTimeout(function(){ $('#confirmbuysell').focus();}, 400);
	$('.sltpdiv').fadeIn();
    $('#sellcrypto').css("opacity","1");
	 $('#buycrypto').css("opacity","0.4");
	 
	 		 $.ajax({

     type: "GET",
     url: 'https://api.binance.com/api/v3/ticker/price?symbol='+globalsymbolname+"USDT",
     data: theurl, // appears as $_GET['id'] @ your backend side
     success: function(data) {
	
		 
     }

   });
	  setTimeout(function(){ calcwinperpos(); }, 1200);
}


document.getElementById('inputstoploss').oninput = function () {
        var max = parseFloat(this.max);
		var min = parseFloat(this.min);

        if (parseFloat(this.value) > max) {
            this.value = max; 
		}
			  

        if (parseFloat(this.value) < min) {
            this.value = min; 
		}
			calcwinperpos();
        }
 
document.getElementById('inputtakeprofit').oninput = function () {
        var max = parseFloat(this.max);
		var min = parseFloat(this.min);

        if (parseFloat(this.value) > max) {
            this.value = max; 
		}
			  

        if (parseFloat(this.value) < min) {
            this.value = min; 
		}
			calcwinperpos();
        }
 
	
	

 $(document).on('keyup mouseup ', '#inputstoploss', function() {    

   if(globalordermode== OP_BUY)
   {	   
       var currvalue = ($(this).val());
       currvalue=parseFloat(currvalue);
	   var newpips =  parseFloat(parseFloat(globalstoploss) - parseFloat(currvalue));
	   newpips =newpips.toFixed(globaldigitdecimalplace);
	    
	       if( parseInt(globaldigitdecimalplace)>0)
		   {
			   newpips = parseInt(GLOBALSL)+ parseFloat(newpips) * parseFloat((Math.pow(10, globaldigitdecimalplace)));
		   }
   
  
    $('.badgestoploss').html(parseInt(newpips)+' pips'); 
   
   
   }
    if(globalordermode== OP_SELL)
   {	   
       var currvalue = ($(this).val());
       currvalue=parseFloat(currvalue);
	   var newpips =  parseFloat(  parseFloat(currvalue)-parseFloat(globalstoploss));
	   newpips =newpips.toFixed(globaldigitdecimalplace);
	    
	       if( parseInt(globaldigitdecimalplace)>0)
		   {
			   newpips = parseInt(GLOBALSL)+ parseFloat(newpips) * parseFloat((Math.pow(10, globaldigitdecimalplace)));
		   }
   
  
   $('.badgestoploss').html(parseInt(newpips)+' pips'); 
   
   
   }  
  
  
   
});

 $(document).on('keyup mouseup', '#inputtakeprofit', function() {  

   if(globalordermode== OP_BUY)
   {	   
       var currvalue = ($(this).val());
       currvalue=parseFloat(currvalue);
	   var newpips =  parseFloat(parseFloat(currvalue)-parseFloat(globaltakeprofit));
	   newpips =newpips.toFixed(globaldigitdecimalplace);
	    
	       if( parseInt(globaldigitdecimalplace)>0)
		   {
			   newpips = parseInt(GLOBALTP)+ parseFloat(newpips) * parseFloat((Math.pow(10, globaldigitdecimalplace)));
		   }
   
  
   $('.badgetakeprofit').html(parseInt(newpips)+' pips'); 
   
   
   
   }
   
     if(globalordermode== OP_SELL)
   {	   
       var currvalue = ($(this).val());
       currvalue=parseFloat(currvalue);
	   var newpips =  parseFloat(parseFloat(globaltakeprofit)-parseFloat(currvalue));
	   newpips =newpips.toFixed(globaldigitdecimalplace);
	    
	       if( parseInt(globaldigitdecimalplace)>0)
		   {
			   newpips = parseInt(GLOBALTP)+ parseFloat(newpips) * parseFloat((Math.pow(10, globaldigitdecimalplace)));
		   }
   
  
   $('.badgetakeprofit').html(parseInt(newpips)+' pips'); 
   
   
   } 
   
});



 



setInterval(function(){ 

 

if($('#closeposition').is(':visible'))
{
	 
	closeposition (globalpostionpopup);
}
 }, 1000);
 
 

 

setInterval(function(){ 


		 $.ajax({

     type: "GET",
     url: 'ajax/listener.php?actionmode=countopenpositions',
     data: '', // appears as $_GET['id'] @ your backend side
     success: function(data) {


 
 if(parseInt(data) != parseInt(TOTALTRADES))
	   {
		   TOTALTRADES = parseInt(data);
		   populateactivetrades();	
	   }	 
     }

   });	
   
   


   


 
 
 }, 500);
 
   
 /*setInterval(function()
 {
	 		 $.ajax({

     type: "GET",
     url: 'ajax/listener.php?actionmode=getvpsstatus',
     data: '', // appears as $_GET['id'] @ your backend side
     success: function(data) {

 
 
 if( data ==  GLOBALVPSSTATUS )
	   {
		   	$('#vpsanddashboard').html('Dashboard <p style="color:coral;zoom:0.8;"><u><i><b>Warning: VPS INACTIVE</u></i></b></p>');
		   
		  
	   }	
else{
 $('#vpsanddashboard').html('Dashboard <p style="color:lightgreen;zoom:0.8;"><u><i><b>VPS ACTIVE</u></i></b></p>');
 GLOBALVPSSTATUS = data;
}	   
     }

   });	
   
 },1500); */
 
	  
   
 

</script>
	  <script>
	  function sendcoinscreener(a)
	  {
		   $('#cryptoscreener').modal('hide');
		
		   
		   setTimeout(function(){      $('#tbl_coins_filter').find('input').focus(); }, 250);
		   setTimeout(function(){      $('#tbl_coins_filter').find('input').val(a);  $('#tbl_coins_filter').find('input').trigger("input"); }, 500);
           
		
            

	  }
            $(document).ready(function () {
				 
                
            });

            function populatescreener() {
				        $('#populatescreenertbl').dataTable().fnClearTable();
                        $('#populatescreenertbl').dataTable().fnDestroy();
						
			  table = $("#populatescreenertbl").DataTable({
                    responsive: true,
                    aaSorting: [],
                    pageLength: 1000,
                });
				            $.ajax({
                type: "GET",
                url: "ajax/cryptoscreenerapi.php?actionmode=getcoins",
                data: "", // appears as $_GET['id'] @ your backend side
                success: function (data) {
                    var totalcoins = parseInt(data);

                    for (i = 0; i < parseInt(totalcoins); i++) {
                        $.ajax({
                            type: "GET",
                            url: "ajax/cryptoscreenerapi.php?actionmode=getsinglecoin&limit=" + parseInt(i),
                            data: "", // appears as $_GET['id'] @ your backend side
                            success: function (coinsdata) {
                                /* 1d */
                                $.ajax({
                                    type: "GET",
                                    url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=1m&limit=1",
                                    data: "", // appears as $_GET['id'] @ your backend side
                                    success: function (data1m) {
                                        /* 3d */

                                        $.ajax({
                                            type: "GET",
                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=5m&limit=1",
                                            data: "", // appears as $_GET['id'] @ your backend side
                                            success: function (data5m) {
                                                $.ajax({
                                                    type: "GET",
                                                    url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=15m&limit=1",
                                                    data: "", // appears as $_GET['id'] @ your backend side
                                                    success: function (data15m) {
                                                        /* all code here */

                                                        $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=30m&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data30m) {
																
																
                                                        $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=1h&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data1h) {
															  $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=2h&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data2h) {
																
																    $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=4h&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data4h) {
																
																										    $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=6h&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data6h) {
																
																										    $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=12h&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data12h) {
																
																										    $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=1d&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data1d) {
																															    $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=3d&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data3d) {
																															    $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=1w&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data1w) {
																
																										    $.ajax({
                                                            type: "GET",
                                                            url: "https://api.binance.com/api/v1/klines?symbol=" + coinsdata.trim().toString().toUpperCase() + "USDT&interval=1M&limit=1",
                                                            data: "", // appears as $_GET['id'] @ your backend side
                                                            success: function (data1month) {
																
											setTimeout(function(){
				                                                var open1minute = parseFloat(data1m[0][1]);
                                                                var open5minutes = parseFloat(data5m[0][1]);
                                                                var open15minutes = parseFloat(data15m[0][1]);
                                                                var open30minutes = parseFloat(data30m[0][1]);
															    var open1hour = parseFloat(data1h[0][1]);
                                                                var open2hours = parseFloat(data2h[0][1]);
                                                                var open4hours = parseFloat(data4h[0][1]);
																var open6hours = parseFloat(data6h[0][1]);
																var open12hours = parseFloat(data12h[0][1]);
																var open1day = parseFloat(data1d[0][1]);
																var open3days = parseFloat(data3d[0][1]);
																var open1week = parseFloat(data1w[0][1]);
																var open1month = parseFloat(data1month[0][1]);

                                                                var close1minute = parseFloat(data1m[0][4]);
                                                                var close5minutes = parseFloat(data5m[0][4]);
                                                                var close15minutes = parseFloat(data15m[0][4]);
                                                                var close30minutes = parseFloat(data30m[0][4]);
														        var close1hour = parseFloat(data1h[0][4]);
                                                                var close2hours = parseFloat(data2h[0][4]);
                                                                var close4hours = parseFloat(data4h[0][4]);
                                                                var close6hours = parseFloat(data6h[0][4]);
                                                                var close12hours = parseFloat(data12h[0][4]);
                                                                var close1day = parseFloat(data1d[0][4]);
                                                                var close3days = parseFloat(data3d[0][4]);
                                                                var close1week = parseFloat(data1w[0][4]);
                                                                var close1month = parseFloat(data1month[0][4]);

																
                                                                var daychangeper_1minute = 0;
                                                                var daychangeper_5minutes = 0;
                                                                var daychangeper_15minutes = 0;
                                                                var daychangeper_30minutes = 0;
                                                                var changeper_1hour = 0;
                                                                var changeper_2hours = 0;
                                                                var changeper_4hours = 0;																
                                                                var changeper_6hours = 0;	
                                                                var changeper_12hours = 0;	
                                                                var changeper_1day = 0;	
                                                                var changeper_3days = 0;	
                                                                var changeper_1week = 0;	
                                                                var changeper_1month = 0;	
																
                                                                daychangeper_1minute = (parseFloat(close1minute) * parseFloat(100)) / parseFloat(open1minute);
                                                                daychangeper_1minute = parseInt(daychangeper_1minute);

                                                                if (parseInt(daychangeper_1minute) >= parseInt(100)) {
                                                                    daychangeper_1minute = parseInt(daychangeper_1minute) - parseInt(100);
                                                                } else {
                                                                    daychangeper_1minute = -(parseInt(100) - parseInt(daychangeper_1minute));
                                                                }


                                                                daychangeper_5minutes = (parseFloat(close5minutes) * parseFloat(100)) / parseFloat(open5minutes);
                                                                daychangeper_5minutes = parseInt(daychangeper_5minutes);

                                                                if (parseInt(daychangeper_5minutes) >= parseInt(100)) {
                                                                    daychangeper_5minutes = parseInt(daychangeper_5minutes) - parseInt(100);
                                                                } else {
                                                                    daychangeper_5minutes = -(parseInt(100) - parseInt(daychangeper_5minutes));
                                                                }
																
														

                                                                daychangeper_15minutes = (parseFloat(close15minutes) * parseFloat(100)) / parseFloat(open15minutes);
                                                                daychangeper_15minutes = parseInt(daychangeper_15minutes);

                                                                if (parseInt(daychangeper_15minutes) >= parseInt(100)) {
                                                                    daychangeper_15minutes = parseInt(daychangeper_15minutes) - parseInt(100);
                                                                } else {
                                                                    daychangeper_15minutes = -(parseInt(100) - parseInt(daychangeper_15minutes));
                                                                }
																
																
													            daychangeper_30minutes = (parseFloat(close30minutes) * parseFloat(100)) / parseFloat(open30minutes);
                                                                daychangeper_30minutes = parseInt(daychangeper_30minutes);

                                                                if (parseInt(daychangeper_30minutes) >= parseInt(100)) {
                                                                    daychangeper_30minutes = parseInt(daychangeper_30minutes) - parseInt(100);
                                                                } else {
                                                                    daychangeper_30minutes = -(parseInt(100) - parseInt(daychangeper_30minutes));
                                                                }
																
														        changeper_1hour = (parseFloat(close1hour) * parseFloat(100)) / parseFloat(open1hour);
                                                                changeper_1hour = parseInt(changeper_1hour);

                                                                if (parseInt(changeper_1hour) >= parseInt(100)) {
                                                                    changeper_1hour = parseInt(changeper_1hour) - parseInt(100);
                                                                } else {
                                                                    changeper_1hour = -(parseInt(100) - parseInt(changeper_1hour));
																}
																								
														        changeper_2hours = (parseFloat(close2hours) * parseFloat(100)) / parseFloat(open2hours);
                                                                changeper_2hours = parseInt(changeper_2hours);

                                                                if (parseInt(changeper_2hours) >= parseInt(100)) {
                                                                    changeper_2hours = parseInt(changeper_2hours) - parseInt(100);
                                                                } else {
                                                                    changeper_2hours = -(parseInt(100) - parseInt(changeper_2hours));																
																}
	

														        changeper_4hours = (parseFloat(close4hours) * parseFloat(100)) / parseFloat(open4hours);
                                                                changeper_4hours = parseInt(changeper_4hours);

                                                                if (parseInt(changeper_4hours) >= parseInt(100)) {
                                                                    changeper_4hours = parseInt(changeper_4hours) - parseInt(100);
                                                                } else {
                                                                    changeper_4hours = -(parseInt(100) - parseInt(changeper_4hours));	
																}
									
									
									
									     changeper_6hours = (parseFloat(close6hours) * parseFloat(100)) / parseFloat(open6hours);
                                                                changeper_6hours = parseInt(changeper_6hours);

                                                                if (parseInt(changeper_6hours) >= parseInt(100)) {
                                                                    changeper_6hours = parseInt(changeper_6hours) - parseInt(100);
                                                                } else {
                                                                    changeper_6hours = -(parseInt(100) - parseInt(changeper_6hours));	
																}
																
																
																
																     changeper_12hours = (parseFloat(close12hours) * parseFloat(100)) / parseFloat(open12hours);
                                                                changeper_12hours = parseInt(changeper_12hours);

                                                                if (parseInt(changeper_12hours) >= parseInt(100)) {
                                                                    changeper_12hours = parseInt(changeper_12hours) - parseInt(100);
                                                                } else {
                                                                    changeper_12hours = -(parseInt(100) - parseInt(changeper_12hours));	
																}
																
																     changeper_1day = (parseFloat(close1day) * parseFloat(100)) / parseFloat(open1day);
                                                                changeper_1day = parseInt(changeper_1day);

                                                                if (parseInt(changeper_1day) >= parseInt(100)) {
                                                                    changeper_1day = parseInt(changeper_1day) - parseInt(100);
                                                                } else {
                                                                    changeper_1day = -(parseInt(100) - parseInt(changeper_1day));	
																}
																
																     changeper_3days = (parseFloat(close3days) * parseFloat(100)) / parseFloat(open3days);
                                                                changeper_3days = parseInt(changeper_3days);

                                                                if (parseInt(changeper_3days) >= parseInt(100)) {
                                                                    changeper_3days = parseInt(changeper_3days) - parseInt(100);
                                                                } else {
                                                                    changeper_3days = -(parseInt(100) - parseInt(changeper_3days));	
																}
																
																     changeper_1week = (parseFloat(close1week) * parseFloat(100)) / parseFloat(open1week);
                                                                changeper_1week = parseInt(changeper_1week);

                                                                if (parseInt(changeper_1week) >= parseInt(100)) {
                                                                    changeper_1week = parseInt(changeper_1week) - parseInt(100);
                                                                } else {
                                                                    changeper_1week = -(parseInt(100) - parseInt(changeper_1week));	
																}
																
																     changeper_1month = (parseFloat(close1month) * parseFloat(100)) / parseFloat(open1month);
                                                                changeper_1month = parseInt(changeper_1month);

                                                                if (parseInt(changeper_1month) >= parseInt(100)) {
                                                                    changeper_1month = parseInt(changeper_1month) - parseInt(100);
                                                                } else {
                                                                    changeper_1month = -(parseInt(100) - parseInt(changeper_1month));	
																}
																

                                                     
													 		var style1min;
																	if( parseInt(daychangeper_1minute)>=0)
																	{
																		style1min= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style1min= "   style = 'color:coral;'";
																			
																	}
																	
																		var style5mins;
																	if( parseInt(daychangeper_5minutes)>=0)
																	{
																		style5mins= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style5mins= "   style = 'color:coral;'";
																			
																	}
																	
																		var style15mins;
																	if( parseInt(daychangeper_15minutes)>=0)
																	{
																		style15mins= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style15mins= "   style = 'color:coral;'";
																			
																	}
																	
																		var style30mins;
																	if( parseInt(daychangeper_30minutes)>=0)
																	{
																		style30mins= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style30mins= "   style = 'color:coral;'";
																			
																	}
																	
																	
																    var style1hour;
																	if( parseInt(changeper_1hour)>=0)
																	{
																		style1hour= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style1hour= "   style = 'color:coral;'";
																			
																	}
																	
																																			var style2hours;
																	if( parseInt(changeper_2hours)>=0)
																	{
																		style2hours= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style2hours= "   style = 'color:coral;'";
																			
																	}
																	
																	var style4hours;
																	if( parseInt(changeper_4hours)>=0)
																	{
																		style4hours= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style4hours= "   style = 'color:coral;'";
																			
																	}



																	var style6hours;
																	if( parseInt(changeper_6hours)>=0)
																	{
																		style6hours= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style6hours= "   style = 'color:coral;'";
																			
																	}

																	var style12hours;
																	if( parseInt(changeper_12hours)>=0)
																	{
																		style12hours= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style12hours= "   style = 'color:coral;'";
																			
																	}

																	var style1day;
																	if( parseInt(changeper_1day)>=0)
																	{
																		style1day= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style1day= "   style = 'color:coral;'";
																			
																	}

																	var style3days;
																	if( parseInt(changeper_3days)>=0)
																	{
																		style3days= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style3days= "   style = 'color:coral;'";
																			
																	}

																	var style1week;
																	if( parseInt(changeper_1week)>=0)
																	{
																		style1week= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style1week= "   style = 'color:coral;'";
																			
																	}

																	var style1month;
																	if( parseInt(changeper_1month)>=0)
																	{
																		style1month= "   style = 'color:lightgreen;'";
																	}
																	else{
																			style1month= "   style = 'color:coral;'";
																			
																	}																	
																	
                                                        var add_row = table.row.add(["<b"+""+" onclick=sendcoinscreener(\""+coinsdata.trim().toString() +"\");><u style='zoom:1.2'>" + coinsdata.trim().toString() + "</u></b>", "<b"+style1min+">"+daychangeper_1minute+"</b>", "<b"+style5mins+">"+daychangeper_5minutes+"</b>", "<b"+style15mins+">"+daychangeper_15minutes+"</b>", "<b"+style30mins+">"+daychangeper_30minutes+"</b>", "<b"+style1hour+">"+changeper_1hour+"</b>", "<b"+style2hours+">"+changeper_2hours+"</b>", "<b"+style4hours+">"+changeper_4hours+"</b>", "<b"+style4hours+">"+changeper_6hours+"</b>", "<b"+style6hours+">"+changeper_12hours+"</b>", "<b"+style12hours+">"+changeper_1day+"</b>", "<b"+style1day+">"+changeper_3days+"</b>", "<b"+style3days+">"+changeper_1week+"</b>", "<b"+style1month+">"+changeper_1month+"</b>"]).draw(true);
                                                               
													 
												}, 20000);

                                                            },
                                                        });

                                                            },
                                                        });
											

                                                            },
                                                        });
																
											

                                                            },
                                                        });

                                                            },
                                                        });

                                                            },
                                                        });

                                                            },
                                                        });
														
														
                                                             
                                                            },
                                                        });
														
                                                             
                                                            },
                                                        });
														
														
                                                             
                                                            },
                                                        });

                                                        /* end all code here */
                                                    },
                                                });
                                            },
                                        });
                                    },
                                });
                            },
                        });
                    }
					 
                },
            });
              
			 
            }


        </script>
<!--	<script src="https://cdn.datatables.net/responsive/2.2.7/js/dataTables.responsive.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.7/css/responsive.dataTables.min.css">-->
	
</body>

</html>