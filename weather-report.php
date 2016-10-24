<?php
$weather_sql = "SELECT id, visibility, cloudiness, fall, wind_direction, wind_speed, temperature, 
DATE_FORMAT(probe_date,'%H:%i') as time, DATE_FORMAT(probe_date,'%Y-%m-%d') as date, probe_date 
FROM weather where DATE(probe_date) >= CURDATE()-3
and probe_date < NOW() order by probe_date asc";
$today = date("Y-m-d");
?>
<script src="chart/src/node_modules/chart.js/dist/Chart.js"></script>

<table><tr><td>
<canvas id="myChart" width="800" height="250"></canvas>
</td></tr></table>
<?php
  include_once 'dbconnect.php';	
  $conn = new mysqli(DBHOST,DBUSER,DBPASS,DBNAME);
   
  if ($result = $conn->query($weather_sql)) {
	$licz = 1;  
	$label_val = "";
	$date_out ="";
	$time = "";
	$date_tmp = "";
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
	  if ( $time != $row['time'] ) {	
		  if ($label_val!="") {
			  $label_val.= ", ";
			  $temp_val.= ", ";
			  $bg_color.= ", ";
			  $bor_color.= ", ";
			  $temp2_val.= ", ";
			  $temp3_val.= ", ";
			  $date_out.=", ";
		  }
		  $label_val.= '"'.$row['time'].'"';
		  $temp_val.= $row['temperature'];
		  $bg_color.= "'rgba(54, 162, 235, 0.2)'";
		  $bor_color.= "'rgba(54, 162, 235, 1)'";
		  $licz++;
		  $time = $row['time'];
		  
		  if ($date_tmp!=$row['date']) {
			$date_out.= '"'.$row['date'].'"';
			$date_tmp = $row['date'];
		  } else {
			$date_out.= "''";  
		  } 
	    
	    $sql2 = "SELECT visibility FROM weather where probe_date = '".$row['probe_date']."' ";
	    if ($result2 = $conn->query($sql2)) {
		  // Fetch one and one row
		  $xx = 0;
		  while ($row2 = $result2->fetch_array(MYSQLI_ASSOC)) {
			$temp2_val.= $row2['visibility'];
			$xx++;
		  }
		  if ($xx==0) $temp2_val.= 0;
		  // Free result set
		  mysqli_free_result($result2);
		} else {
				echo "<br/>Blad: ". mysqli_errno($conn) . " " . mysqli_error($conn);
		}

	    $sql3 = "SELECT cloudiness FROM weather where probe_date = '".$row['probe_date']."' ";
	    if ($result3 = $conn->query($sql3)) {
		  // Fetch one and one row
		  $xx = 0;
		  while ($row3 = $result3->fetch_array(MYSQLI_ASSOC)) {
			$temp3_val.= $row3['cloudiness'];
			$xx++;
		  }
		  if ($xx==0) $temp3_val.= 0;
		  // Free result set
		  mysqli_free_result($result3);
		} else {
				echo "<br/>Blad: ". mysqli_errno($conn) . " " . mysqli_error($conn);
		}

	    
	  }
	}
  }
        
?>
<script>
	
var ctx = document.getElementById("myChart").getContext("2d");
//ctx.canvas.width = 800;
//ctx.canvas.height = 250;

/*
var data = {
	    labels: [<?php echo $label_val; ?>],
        datasets: [{
            label: '# temperatura ',
            data: [<?php echo $temp_val; ?>],
            backgroundColor: [<?php echo $bg_color; ?>],
            borderColor: [<?php echo $bor_color; ?>],
            borderWidth: 1
        }
    ]
};
*/
        
var data = {
    labels: [<?php echo $date_out; ?>],
    datasets: [
        {
            label: "# temperatura",
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(75,192,192,0.4)",
            borderColor: "rgba(75,192,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10,
            data: [<?php echo $temp_val; ?>],
            spanGaps: false,
        },
        {
            label: "# widoczność",
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(75,75,192,0.4)",
            borderColor: "rgba(75,75,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,75,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,75,192,1)",
            pointHoverBorderColor: "rgba(220,75,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10,
            data: [<?php echo $temp2_val; ?>],
            spanGaps: false,
        },
        {
            label: "# zachmurzenie",
            fill: false,
            lineTension: 0.1,
            backgroundColor: "rgba(192,75,192,0.4)",
            borderColor: "rgba(192,75,192,1)",
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(192,75,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(192,75,192,1)",
            pointHoverBorderColor: "rgba(75,75,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10,
            data: [<?php echo $temp3_val; ?>],
            spanGaps: false,
        }
    ]
};

window.myBar = new Chart(ctx, {
    type: 'line',
    data: data,
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                },
                scaleLabel: {
        display: true,
        labelString: 'temperatura °C'
      }
            }]
        }
    },
    scaleLabel: {
		   display: true,
		   labelString: "wykres"
	},
    animation: false,
    legend: {display: false}
    
});


</script>

