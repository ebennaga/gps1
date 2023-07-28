<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="refresh" content="1800">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">

    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <link rel="stylesheet" type="text/css" href="{{asset('css/leaflet.css') }}"/>

    <link rel="stylesheet" type="text/css" href="{{asset('css/leaflet.contextmenu.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/leaflet.draw.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/tabs.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/tabstyles.css') }}"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/skysatu.css') }}?v=1.1"/>
    <link rel="stylesheet" type="text/css" href="{{asset('css/leaflet-measure.css') }}"/>
    <link href="//db.onlinewebfonts.com/c/860c3ec7bbc5da3e97233ccecafe512e?family=Circular+Std+Book" rel="stylesheet" type="text/css"/>
    <style>
        body {
            font-family: Roboto, 'Segoe UI', Tahoma, sans-serif;
            background: #a1cded !important;
        }

        .input-modal {
            width: 70%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            resize: vertical;
        }

        .input-modal-text {
            width: 100%;
            margin-bottom: 10px;
        }

        input[type=submit] {
            background-color: #2CC185;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }


        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 50px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0, 0, 0); /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
            text-align: center;
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
        }

        /* The Close Button */
        .close {
            opacity: 0.6;
            float: right;
            font-size: 14px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .content-wrap {
            font-family: Arial, Helvetica, sans-serif;
            text-transform: capitalize;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .inner-table-row.selected {
            background-color: rgb(37, 96, 204);
            color: #fff;
        }

        #myProgress {
          width: 100%;
          background-color: #ddd;
        }

        #myBar {
          width: 1%;
          height: 30px;
          background-color: #4CAF50;
        }


    </style>
</head>
<body style="height: inherit !important;">
<div id="googleMap"></div>
<div id="googleMapHistory" style="display: none"></div>
<div id="speedCount" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div
                    style="text-align: center;"><h1 class="modal-title">Enter the Number of Points to Be Calculated</h1>
                </div>
            </div>
            <div class="modal-body">
                <form class="addPoints" id="addPoints" method="get" href="">
                    <input type="text" class="speedPolyline input-modal" placeholder="In Nautical Knots"
                           id="speedPolyline">
                    <input type="submit" class="btn btn-default" value="Submit" id="submitPoint">
                </form>
            </div>
        </div>

    </div>
</div>
<div  class="leaflet-top leaflet-right" id="box" style="display: none">
    <div class="leaflet-control-measure leaflet-bar leaflet-control" aria-haspopup="true">
        <div class="leaflet-control-measure-interaction js-interaction">
            <div class="js-measuringprompt"><h3 style="font-size: 11px">Expected Time Remaining</h3>
                <div class="js-results results" id="totalETR">
                </div>
            </div>
        </div>
    </div>
</div>
<div  class="leaflet-top leaflet-right" id="averageSpeedTime" style="display: none">
    <div class="leaflet-control-measure leaflet-bar leaflet-control" aria-haspopup="true">
        <div class="leaflet-control-measure-interaction js-interaction">
            <div class="js-measuringprompt"><h3 style="font-size: 11px" id="titleAverage"></h3>
                <div class="js-results results" id="totalAverage">
                </div>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-success alert-dismissible" id="myDIV" style="position: absolute;
top: 1%; width: 100%; height: 99%; z-index: 6; background-color: #000; opacity: 0.8; filter: alpha(opacity=80); text-align: center; display: none;">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
   <img src="/images/loder.gif" alt="loading" width="90" height="88" style="margin-top: 18%">
</div>

<div id="floating-panel">
    <div class="close">
        <i class="fa fa-angle-double-left"></i>
    </div>
    <section>
        <div class="tabs tabs-style-linebox">
            <nav>
                <ul style="position: fixed; z-index: 1; background-color: #fff; width: 268px; height: 60px;">
                    <li id="tab-track" class="tab-current"><a href="#section-linebox-1"><span style="Arial, Helvetica, sans-serif;">Track</span></a></li>
                    <li id="tab-history"><a href="#section-linebox-2"><span style="font-family:Arial, Helvetica, sans-serif;">History</span></a></li>
                </ul>
            </nav>
            <div class="content-wrap">
                <section id="section-linebox-1" class="content-current">
                    <table class="fixed_headers" id="tracking_table">
                        <thead style="font-weight: 500;">
                        <tr style="position: fixed; background-color: #fff; width: 253px; z-index: 3;">
                            <!--<td>
                                <button class="startPoint" style="font-family:Arial, Helvetica, sans-serif;">Start Point</button>
                                <button class="stopDrawing" style="font-family:Arial, Helvetica, sans-serif;" >Stop Drawing</button>
                            </td>
{{--                            <td>--}}
{{--                                <button class="resetPoint">Reset Point</button>--}}
{{--                            </td>--}}-->
                        </tr>

                        <tr style="margin-top: 5px;">
                            <td style="min-width: 30px;"><input id="checkAll" type="checkbox" checked="checked"/></td>
                            <td>Name</td>
                            <td>Last Update</td>
                            <td>Speed (knots)</td>
                        </tr>
                        </thead>
                        <tbody id="shipData" style="font-weight: 500;">
                        </tbody>
                    </table>
                </section>

                <section id="section-linebox-2">
                    <!--<div id="myProgress">
  <div id="myBar">Loading</div>
</div>-->

                    <div style="text-align: center; margin-bottom: 7px; position: fixed; background-color: #fff; width: 253px;">
                        <input type="text" class="datepicker startDate">&nbsp;
                        <input type="text" class="datepicker endDate">
                        <button id="setDate"><i class="fa fa-search"></i>&nbsp;Search</button>
                        <button id="downloadCSV"><i class="fa fa-download"></i></button>
                    </div>

                   <table class="fixed_headers history" id="history_table" style="margin-top: 60px;">
                        <thead style="font-weight: 550; font-family: Arial, Helvetica, sans-serif;">
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        </thead>
                        <tbody id='historyShipData' style="font-weight: 550; font-family: Arial, Helvetica, sans-serif;">
                        </tbody>
                    </table>
                </section>
            </div><!-- /content -->
        </div><!-- /tabs -->
    </section>
</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.2/jquery.min.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="{{ asset('js/bootstrap.min.js')}}"></script>
<script src="{{ asset('js/sweetalert2.all.min.js')}}"></script>
<script src="{{ asset('js/MeasureTool.min.js') }}"></script>
<script src="{{ asset('js/cbpFWTabs.js') }}"></script>
<script src="{{ asset('js/jquery-dateFormat.min.js') }}"></script>
<script src="{{ asset('js/leaflet.js') }}"></script>
<script src="{{ asset('js/leaflet.rotatedMarker.js') }}"></script>
<script src="{{ asset('js/leaflet.contextmenu.js') }}"></script>
<script src="{{ asset('js/leaflet.draw.js') }}"></script>
<script src="{{ asset('js/leaflet-providers.js') }}"></script>

@if (Auth::user()->timezone == 'UTC-12') 
<script src="{{ asset('js/customleafletutcmin12.js') }}?v={{  now()  }}"></script>

@elseif (Auth::user()->timezone == 'UTC-11') 
<script src="{{ asset('js/customleafletutcmin11.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-10')
<script src="{{ asset('js/customleafletutcmin10.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-09')
<script src="{{ asset('js/customleafletutcmin09.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-08')
<script src="{{ asset('js/customleafletutcmin08.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-07')
<script src="{{ asset('js/customleafletutcmin07.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-06')
<script src="{{ asset('js/customleafletutcmin06.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-05')
<script src="{{ asset('js/customleafletutcmin05.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-04')
<script src="{{ asset('js/customleafletutcmin04.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-03')
<script src="{{ asset('js/customleafletutcmin03.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-02')
<script src="{{ asset('js/customleafletutcmin02.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC-01')
<script src="{{ asset('js/customleafletutcmin01.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC')
<script src="{{ asset('js/customleafletutc.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+01')
<script src="{{ asset('js/customleafletutcplus01.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+02')
<script src="{{ asset('js/customleafletutcplus02.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+03')
<script src="{{ asset('js/customleafletutcplus03.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+04')
<script src="{{ asset('js/customleafletutcplus04.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+05')
<script src="{{ asset('js/customleafletutcplus05.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+06')
<script src="{{ asset('js/customleafletutcplus06.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+07')
<script src="{{ asset('js/customleafletutcplus07.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+08')
<script src="{{ asset('js/customleafletutcplus08.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+09')
<script src="{{ asset('js/customleafletutcplus09.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+10')
<script src="{{ asset('js/customleafletutcplus10.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+11')
<script src="{{ asset('js/customleafletutcplus11.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == 'UTC+12')
<script src="{{ asset('js/customleafletutcplus12.js') }}?v={{  now()  }}"></script>   

@elseif (Auth::user()->timezone == '') 
<script src="{{ asset('js/customleaflet.js') }}?v={{  now()  }}"></script>  

@endif

<script src="https://kit.fontawesome.com/59ba4e0c1b.js"></script>


</body>
</html>
