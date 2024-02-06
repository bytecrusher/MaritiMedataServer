
/**
 *
 * @author: Guntmar Höche
 * @license: TBD
 * TODO: for every Board its own canvas.
 * TODO: settings options for canvas to define, witch channels will be display.
 */

const myChart = null;

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

// Chart view. Runs on click on "Charts" tab, and collects data to show
$(document).ready(async function(){
  var backgroundColor = null;
  var borderColor = null;
  var hoverBackgroundColor = 'rgba(0, 100, 0, 1)';
  var hoverBorderColor = 'rgba(0, 100, 0, 1)';
  var varSensorId = null;
  InitialSetupChart();
  // TODO: check if every channel will be displayed. I think only the first one will display in the chart.

  for (let i in gaugesArrayHelperBig) {
    varSensorId = gaugesArrayHelperBig[i]["sensorId"];
    typId = gaugesArrayHelperBig[i]["typId"];
    sensorname = gaugesArrayHelperBig[i]["NameOfSensors"];
    sensorChannel = gaugesArrayHelperBig[i]["channelNr"];
    var randomColor = gaugesArrayHelperBig[i]["ChartColor"];
    var backgroundColor = randomColor;
    var borderColor = randomColor;
    addDataToChart(varSensorId, 200, varSensorId, backgroundColor, borderColor, hoverBackgroundColor, hoverBorderColor, sensorname, sensorChannel);
  }
  addLabelsToChart(varSensorId, 200, varSensorId, backgroundColor, borderColor, hoverBackgroundColor, hoverBorderColor);
});

function addDataToChart(varSensorId, varMaxValues, varLabel, varBackgroundColor, varBorderColor, varHoverBackgroundColor, varHoverBorderColor, sensorname, sensorChannel) {
  if (varSensorId != null) {
    // TODO: make more efficient: call get function with the channel name and receive only these channels
    $.getJSON('api/getSensorDataSet.php', { sensorId:varSensorId, maxValues:varMaxValues}, async function(data, textStatus, jqXHR){
      var id = [];
      var value1 = [];
      for(var i in data) {
        id.push("id " + data[i].id);
        if (sensorChannel == 0) {
          value1.push(data[i].value1);
        } else if (sensorChannel == 1) {
          value1.push(data[i].value2);
        } else if (sensorChannel == 2) {
          value1.push(data[i].value3);
        } else if (sensorChannel == 3) {
          value1.push(data[i].value4);
        }
      }

      const data1 = window.myChart.data;
      const dsColor = varBackgroundColor;
      const newDataset = {
        label: sensorname,
        backgroundColor: dsColor,
        borderColor: dsColor,
        data: value1,
      };
      window.myChart.data.datasets.push(newDataset);
      window.myChart.update();
    })
    .done(function () {
      //alert('Request done!');
    })
    .fail(function (jqxhr,settings,ex) {
      //alert('failed (addDataToChart), ' + varSensorId + ", " + ex);
      console.log('failed (addDataToChart), ' + varSensorId + ", for: " + varLabel + ", " + ex);
      //console.log(data);
    });
  }
}

function addLabelsToChart(varSensorId, varMaxValues, varLabel, varBackgroundColor, varBorderColor, varHoverBackgroundColor, varHoverBorderColor) {
  if (varSensorId != null) {
    $.getJSON('api/getSensorDataSet.php', { sensorId:varSensorId, maxValues:varMaxValues}, async function(data, textStatus, jqXHR){
      var val_time = [];
      for(var i in data) {
        val_time.push(data[i].val_time);
      }
      window.myChart.data.labels = val_time;
      window.myChart.update();
    })
    .done(function () {
    })
    .fail(function (jqxhr,settings,ex) {
      alert('failed (addLabelsToChart), '+ ex);
    });
  }
}


function InitialSetupChart(varSensorId, varmaxValues, varLabel, varBackgroundColor, varBorderColor, varHoverBackgroundColor, varHoverBorderColor) {
  const ctx = document.getElementById('mycanvas');
  window.myChart = new Chart(ctx, {
    type: 'line',
    data: {
        /*labels: ['Red', 'Blue', 'Yellow', 'Green', 'Purple', 'Orange'],
        datasets: [{
            label: '# of Votes',
            data: [12, 19, 3, 5, 2, 3],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255, 99, 132, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]*/
    },
    /*options: {
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }*/
  });
}
