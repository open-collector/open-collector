  var jsInputs = ['variable_1','variable_2'];


  for (j=0;j<jsInputs.length;j++){
    var x = document.getElementById(jsInputs[j]);
    for(i=0; i<getdata_columns.length; i++){
      var option = document.createElement("option");
      option.text = getdata_columns[i];
      x.add(option);    
    }    
  }