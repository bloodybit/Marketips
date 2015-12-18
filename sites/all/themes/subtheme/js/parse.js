jQuery(function($) {
  // Debug flag
  var debugMode = false;

  // Default time delay before checking location
  var callBackTime = 100;

  // # px before tracking a reader
  var readerLocation = 250;

  // Set some flags for tracking & execution
  var timer = 0;
  var scroller = false;
  var endContent = false;
  var didComplete = false;

  // Set some time variables to calculate reading time
  var startTime = new Date();
  var beginning = startTime.getTime();
  var totalTime = 0;

  // Get some information about the current page
  var pageTitle = document.title;

  // Track the article load
  if (!debugMode) {
      var Reading = Parse.Object.extend("Reading");

      var reading = new Reading();
      reading.set({pageLoaded: window.location.pathname});
      reading.set({ user: Drupal.settings.parse.uid.toString()});
      reading.set({title: Drupal.settings.parse.title});
      reading.save(null, {
        success:function(reading){
          if(Drupal.settings.parse.debug){
            console.log('New Reading created with objectId: ' + reading.id);
          }
        },
        error: function(reading, error){
          if(Drupal.settings.parse.debug){
            alert('Failed to create new object, with error code: ' + error.message);
          }
        }
      });
  } else {
      alert('The page has loaded. Woohoo.');
  }

  // Check the location and track user
  function trackLocation() {
      bottom = $(window).height() + $(window).scrollTop();
      height = $(document).height();

      // If user starts to scroll send an event
      if (bottom > readerLocation && !scroller) {
          currentTime = new Date();
          scrollStart = currentTime.getTime();
          timeToScroll = Math.round((scrollStart - beginning) / 1000);
          if (!debugMode) {
            reading.set({startReading: timeToScroll});
            reading.save(null, {
              success:function(reading){
                if(Drupal.settings.parse.debug){
                  console.log('Object reading ' + reading.id + " read in " + timeToScroll);
                }
              },
              error: function(reading, error){
                if(Drupal.settings.parse.debug){
                  alert('Failed to create new object, with error code: ' + error.message);
                }
              }
            });
          } else {
              alert('started reading ' + timeToScroll);
          }
          scroller = true;
      }

      // If user has hit the bottom of the content send an event (la segnalazione parte un po' prima ad essere sinceri)
      if (bottom >= $('#block-system-main').scrollTop() + $('#block-system-main').innerHeight() && !endContent) {
          currentTime = new Date();
          contentScrollEnd = currentTime.getTime();
          timeToContentEnd = Math.round((contentScrollEnd - scrollStart) / 1000);
          if (!debugMode) {
              reading.set({readArticle: timeToContentEnd});
              reading.save(null, {
                success:function(reading){
                  if(Drupal.settings.parse.debug){
                    console.log('Object bottom ' + reading.id + " read in " + timeToContentEnd);
                  }
                },
                error: function(reading, error){
                  if(Drupal.settings.parse.debug){
                    alert('Failed to create new object, with error code: ' + error.message);
                  }
                }
              });
          } else {
              alert('end content section '+timeToContentEnd);
          }
          endContent = true;
      }

      // If user has hit the bottom of page send an event
      if (bottom >= height && !didComplete) {
          currentTime = new Date();
          end = currentTime.getTime();
          totalTime = Math.round((end - scrollStart) / 1000);
          if (!debugMode) {
              if (totalTime < 60) {
                  reading.set({type: 'Scanner'});
                  reading.save(null, {
                    success:function(reading){
                      if(Drupal.settings.parse.debug){
                        console.log('Object bottom ' + reading.id + " type read Scanner");
                      }
                    },
                    error: function(reading, error){
                      if(Drupal.settings.parse.debug){
                        alert('Failed to create new object, with error code: ' + error.message);
                      }
                    }
                  });
              } else {
                  reading.set({type: 'Reader'});
                  reading.save(null, {
                    success:function(reading){
                      if(Drupal.settings.parse.debug){
                        console.log('Object bottom ' + reading.id + " type read Reader");
                      }
                    },
                    error: function(reading, error){
                      if(Drupal.settings.parse.debug){
                        alert('Failed to create new object, with error code: ' + error.message);
                      }
                    }
                  });
              }
              reading.set({pageBottom: totalTime});
              reading.save(null, {
                success:function(reading){
                  if(Drupal.settings.parse.debug){
                    console.log('Object bottom ' + reading.id + " read in " + totalTime);
                  }
                },
                error: function(reading, error){
                  if(Drupal.settings.parse.debug){
                    alert('Failed to create new object, with error code: ' + error.message);
                  }
                }
              });
          } else {
              alert('bottom of page '+totalTime);
          }
          didComplete = true;
      }
  }

  // Track the scrolling and track location
  $(window).scroll(function() {
    if (timer) {
        clearTimeout(timer);
    }

    // Use a buffer so we don't call trackLocation too often.
    timer = setTimeout(trackLocation, callBackTime);
  });
});
