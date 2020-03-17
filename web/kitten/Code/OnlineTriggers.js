

/*

for saving the files

function google_keys(key_type){
  switch(key_type){
    case "researcher":
      //resume here
      break;
  }
}

*/


function online_save(experiment_id,
                     participant_id,
<<<<<<< HEAD
                     completion_code,
                     prehashed_code,
=======
>>>>>>> 94b84c7904574e43669bbc18777b7c6e9dc17b0f
                     encrypted_data,
                     save_script_url,
                     after_function){

  data = {
<<<<<<< HEAD
    completion_code: completion_code,
    encrypted_data:  encrypted_data,
    experiment_id:   experiment_id,
    participant_id:  participant_id,
    prehashed_code:  prehashed_code
=======
    participant_id: participant_id,
    experiment_id:  experiment_id,
    encrypted_data: encrypted_data,
>>>>>>> 94b84c7904574e43669bbc18777b7c6e9dc17b0f
  };
  $.ajax({
    type: 'POST',
    url: save_script_url, //"https://script.google.com/macros/s/AKfycbyuUWN7Jc1j62OuUh1JrJFuHn7e2VXLZdZ9FJs4dvwX_D6JI7M7/exec",
    data: data,
    crossDomain: true,
    timeout: 120000,
    success:function(result){
      //as it stands, this will never happen as Collector doesn't allow posts to it.
      after_function();
    }
  })
  .catch(function(error){
    after_function();
  });
}


var ParseGSX = (function() {

  var _defaultCallback = function(data) {
    console.log(data);
  };

  var _parseRawData = function(res) {
    var finalData = [];
    res.feed.entry.forEach(function(entry){
      var parsedObject = {};
      for (var key in entry) {
        if (key.substring(0,4) === "gsx$") {
          parsedObject[key.slice(4)] = entry[key]["$t"];
        }
      }
      finalData.push(parsedObject);
    });
    var processGSXData = _defaultCallback;
    processGSXData(finalData);
  };

  var parseGSX = function(spreadsheetID, callback) {
    var url = "https://spreadsheets.google.com/feeds/list/" + spreadsheetID + "/od6/public/values?alt=json";
    var ajax = $.ajax(url);
    if (callback) { _defaultCallback = callback; }
    $.when(ajax).then(_parseRawData);
  };

  return { parseGSX: parseGSX };

})();
