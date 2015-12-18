function showDays(str) {
    if (str.length == 0) {
      /*
       * If the field is empty i clean the graph (deleting it)
       */
        document.getElementById('hint').innerHTML = "";
        return;
    } else {
      /*
       * Otherwise I send an ajax request (or better an xml (asincrhonous request))
       * In order to collect the data via php api
       */
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                datas = xmlhttp.responseText;

                /*
                 * I send data in Json object, so i have top convert it
                 */
                arr = JSON.parse(datas);

                //Append the chart in order to show the data
                $( "#hint" ).append("<canvas id='updating-chart' width='1080' height='500'></canvas>");
                $( "#hint" ).append('<p>Last ' + arr['period'] + ' Days');


                var labels = arr['labels'].split(',');
                var data = arr['data'].split(',');


                var canvas = document.getElementById('updating-chart'), // nel canvas upadating chart vado a mettere i data
                    ctx = canvas.getContext('2d'),
                    startingData = {
                        labels: labels, //Set labels
                        datasets: [
                            {
                                fillColor: "rgba(231,104,102,0.2)",
                                strokeColor: "#e76866",
                                pointColor: "#e76866",
                                pointStrokeColor: "#e76866",
                                data: data //set data
                            }
                        ]
                    };

                // Reduce the animation steps for demo clarity.
                var myLiveChart = new Chart(ctx).Line(startingData, {animationSteps: 15}); //Apply all the setting to myLiveChart Variable

            }
        };
        xmlhttp.open("GET", "/sites/all/modules/custom/analytics01/infoTraffic.php?q=" + str, true); //Real XML requests
        xmlhttp.send();
    }
}
