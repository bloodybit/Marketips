
  <script type="text/javascript">
    Parse.initialize("P60EfTUuOZoeZyD2qSLpOrc8DWwUk2YjEqU2HY1R", "JlgM5RtBO3nYKd9YqvFho8Su9xppRmwcRxRpaeIV");

    var Reading = Parse.Object.extend("Reading");
    var reading = new Reading();
    reading.pageLoaded = "/";

    reading.set("title", "Home");

    reading.save();

  </script>
