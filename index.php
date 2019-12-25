<!DOCTYPE html>
<html>
<head>

<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Licenta</title>
<link rel="shortcut icon" type="image/png" href="photos/tab_icon.ico"/>
<link rel="stylesheet" href="bower_components/chartist/dist/chartist.min.css">
<link rel="stylesheet" type="text/css" href="style.css">
<link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">
<link href="https://use.fontawesome.com/releases/v5.0.7/css/all.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css?family=Farro&display=swap" rel="stylesheet">
<?php include('information_retrieval.php') ?>

</head>

<body>

<div id="main_page">

  <div class="navbar">
   <a href="index.php">Refresh</a>
   <a href="#5hours">Weather</a>
   <a href="#graphsandcharts">Charts</a>
   <a href="#emailandmore">Email Forecast</a>

   <div id="creator_info"><i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp Radu Nitescu </div>

  </div>

  <div class="col-container">
    <div class="polaroid">
      <img id="current_weather" src="photos/sun.jpg" alt="Weather_right_now" style="width:100%; padding-top:7%;">
      <div class="container">
        <p id="current_weather_text" class="texty"></p>
      </div>
    </div>


    <div class="col container graph"  style="margin-left:13%;">
      <div class="ct-chart ct-golden-section" id="chart1"></div>
      <p class="texty" id="chart1-text"> Temperature </p>
    </div>
  </div>

</div>


<a id="5hours"> <h2 class="texty">Weather for the last 10 minutes</h2> </a>

<table id="table">

         <tr>
             <th>Hour</th>
             <th>Temperature (°C)</th>
             <th>Pressure (mmHg)</th>
             <th>Humidity (%)</th>
         </tr>

         <tr>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
         </tr>

         <tr>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
         </tr>

         <tr>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
         </tr>

         <tr>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
         </tr>

         <tr>
             <td></td>
             <td></td>
             <td></td>
             <td></td>
         </tr>

     </table>

<script type="text/javascript">
    var data_js = JSON.parse('<?php echo $all_months?>');
    var max_temp_dict = {}
    var min_temp_dict = {}
    var medium_temp_dict = {}
    for(var key in data_js){
      if(data_js[key].length == 0)
        continue;
      max_temp_dict[key] = -40;
      min_temp_dict[key] = 40;
      medium_temp_dict[key] = 0;
      for (var i = 0; i < data_js[key].length; i++)
      {
        if(max_temp_dict[key] < data_js[key][i]["temperature"])
          max_temp_dict[key] = data_js[key][i]["temperature"];
        if(min_temp_dict[key] > data_js[key][i]["temperature"])
          min_temp_dict[key] = data_js[key][i]["temperature"];
        medium_temp_dict[key] += data_js[key][i]["temperature"]
      }
      medium_temp_dict[key] /= data_js[key].length;
    }
</script>

<script>
var table_data = JSON.parse('<?php echo $last_5_hours?>');
table = document.getElementById("table");
for( var i = 0; i < table.rows.length; i++ )
{
  table.rows[i+1].cells[0].innerHTML = table_data[i]["hour"]
  table.rows[i+1].cells[1].innerHTML = table_data[i]["temperature"]
  table.rows[i+1].cells[2].innerHTML = table_data[i]["pressure"]
  table.rows[i+1].cells[3].innerHTML = table_data[i]["humidity"]
}
</script>

<script src="bower_components/chartist/dist/chartist.min.js"></script>

<a id="graphsandcharts">
<div class="col-container" style="margin-left:5%;">
  <div class="col graph">
    <div class="ct-chart ct-golden-section" id="chart2"></div>
    <p id="chart2-text" class="texty"></p>
  </div>
  <div class="col graph">
    <div class="ct-chart ct-golden-section" id="chart3"></div>
    <p id="chart3-text" class="texty"></p>
  </div>
  <div class="col graph">
    <div class="ct-chart ct-golden-section" id="chart4"></div>
    <p id="chart4-text" class="texty"></p>
  </div>
</div>


<div class="col-container" style="margin-left:5%;">
  <div class="col graph">
    <div class="ct-chart ct-golden-section" id="chart5"></div>
    <p id="chart5-text" class="texty">Average <span id="spanTemp"> temperature (°C)</span> and <span id="spanHum"> humidity (%)</span> for all recorded months</p>
  </div>

  <div class="col graph">
      <form id="form_email" action="/emailsender.php" method="post" class="texty" style="padding-top: 10%; text-align:left;">
        First name:<br>
        <input type="text" name="firstname" value="John">
        <br>
        Last name:<br>
        <input type="text" name="lastname" value="Smith">
        <br>
        <input type="radio" name="gender" value="male" checked> Male
        <input type="radio" name="gender" value="female"> Female <br>
        Email:</br>
        <input type="email" name="email" value="user@email.com">
        <br><br>
        <input type="submit" value="Submit">
        <input type="reset" value="Reset">
      </form>
  </div>

</div>
<a id="emailandmore">


<script>
    var current_month = JSON.parse('<?php echo $current_month?>');
    document.getElementById("chart1-text").innerHTML = "Temperature for " + current_month + " in Celsius degrees (°C).";
    document.getElementById("chart2-text").innerHTML = "Humidity for " + current_month + " in %.";
    document.getElementById("chart3-text").innerHTML = "Percentage of cloudy (c) days over sunny (s) days in " + current_month + ".";
    document.getElementById("chart4-text").innerHTML = "Pressure for " + current_month + " in mmHg.";


    function get_graph_data_average(resolution, x_axis, w_property) {

      var daily_dict = {};
      for( var i = 0; i < data_js[resolution].length; i++)
      {
        day = data_js[resolution][i][x_axis];
        if(daily_dict.hasOwnProperty(day))
        {
          daily_dict[day][0] += data_js[resolution][i][w_property];
          daily_dict[day][1] += 1;
        }
        else
          daily_dict[day] = [data_js[resolution][i][w_property],1];
      }
      for(var key in daily_dict)
        daily_dict[key] = daily_dict[key][0] / daily_dict[key][1];
      return daily_dict;
    }
    var options = {
      width: "100%",
      height: "100%",
      labelInterpolationFnc: function(value) {
        return value
      }
    };

    var data = {
    labels: Object.keys(get_graph_data_average(current_month, "month_day", "temperature")),
    series: [Object.values(get_graph_data_average(current_month, "month_day", "temperature"))]
    };
    new Chartist.Line('#chart1', data, options);
    var data = {
    labels: Object.keys(get_graph_data_average(current_month, "month_day", "humidity")),
    series: [Object.values(get_graph_data_average(current_month, "month_day", "humidity"))]
    };
    new Chartist.Line('#chart2', data, options);

    var data = {
    labels: Object.keys(get_graph_data_average(current_month, "month_day", "pressure")),
    series: [Object.values(get_graph_data_average(current_month, "month_day", "pressure"))]
    };
    new Chartist.Line('#chart4', data, options);

    function get_data_light(current_month){
      var light_counter = {
        "cloudy": 0,
        "": 0,
        "sunny": 0
      }
      for( var i = 0 ; i < data_js[current_month].length; i++)
        if (data_js[current_month][i]["light_value"] == 0 )
          light_counter["cloudy"] += 1;
        else
          light_counter["sunny"] += 1;
      return light_counter
    }


    var data = {
    labels: Object.keys(get_data_light(current_month)),
    series: Object.values(get_data_light(current_month)),
    };
    var options = {
      labelInterpolationFnc: function(value) {
        return value[0]
      }
    };

    new Chartist.Pie('#chart3', data, options);

    function get_avg_year(w_property)
    {
      var year_dict = {}
      for (var i =0; i < Object.keys(data_js).length; i++){
        var month = Object.keys(data_js)[i]
        if (data_js[month].length == 0)
          continue
        var sum = 0;
        for (var j = 0; j < Object.values(get_graph_data_average(month, "month_day", w_property)).length; j++)
          sum += Object.values(get_graph_data_average(month, "month_day", w_property))[j]
        year_dict[i + 1] = sum / Object.values(get_graph_data_average(month, "month_day", w_property)).length
      }
      return year_dict
    }

    var data = {
    labels: Object.keys(get_avg_year("temperature")),
    series: [Object.values(get_avg_year("temperature")), Object.values(get_avg_year("humidity"))]
    };
    new Chartist.Bar('#chart5', data, options);

</script>





<script>
  var weather_now = JSON.parse('<?php echo $last_entry ?>');
  var weather_info = "The temperature is " + weather_now["temperature"] + " degrees, with " + weather_now["humidity"] + " % humidity and an atmosferic pressure of " + weather_now["pressure"] + " mmHg";
  var weather_photo = "photos/sun.jpg";
  if ( weather_now["temperature"] > 30 )
      weather_photo ="photos/hot.jpg";
  else if ( weather_now["humidity"] > 80 )
      weather_photo="photos/rain.jpg";
  else if ( weather_now["temperature"] < 0 )
    weather_photo = "photos/snow.jpg";
  else if ( weather_now["light_value"] < 80 && weather_now["seconds"] > 19800 && weather_now["seconds"] < 28800) 
    weather_photo = "photos/cloud.jpg";
  else if ( weather_now["seconds"] < 19800  || weather_now["seconds"] > 28800 ) 
    weather_photo = "photos/night.jpg";
  else
    weather_photo = "photos/sun.jpg";

  document.getElementById("current_weather").src = weather_photo;
  document.getElementById("current_weather_text").innerHTML = weather_info;


</script>

</body>
</html>
