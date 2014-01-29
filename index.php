
<html>
  <head>
    <title>Divergent Series Calculator</title>
    <style>
      body{
        font-family:"Georgia";
        text-align:center;
      }
      #esapagne{
        position:relative;
        width:21em;
        margin:auto;
      }
      #sigma{
        font-size:5em;
      }
      #infinity{
        font-size:3em;
        position:relative;
        top:20px;
      }
      #numberwang{
        font-weight:bold;
        padding:5px
      }
      #show{
        font-size:2em;
        padding:10px
      }
      #show span:first-child{
        font-size:1.3em;
      }
      .answer{
        font-size:1.5em;
      }
      .unknown{
        color:#ff0000;
      }
      .x2{
        font-size:0.7em;
      }
      input{
        text-align:center;
      }
      sup{
        font-size:0.75em;
        position:relative;
        bottom:0.3em;
      }
      input#n{
        width:1.3em;
        font-weight:bold;
      }
      input#parsinator{
        position:absolute;
        right:0em;
        top:7em;
        width:10em;
        font-weight:bold;
      }
    </style>
  </head>
  <body>
    <div id = "esapagne">
      <div id = "infinity">&#8734;</div>
      <div id = "sigma">&#8721;</div>
      <div id = "numberwang">n = <input value = "0" type = "text" id = "n" /></div>
      <input value = "(-1)^n" type = "text" id = "parsinator"/>
      <input value = "Sum" type = "button" id = "calculate" onclick="calculate()" />
    </div>
    <div id = "show"></div>
    <script type="text/javascript">
      if(window.location.href.split("?").length > 1){
        document.getElementById("parsinator").value = window.location.href.split("?")[1];
        if(window.location.href.split("?").length > 2){
          document.getElementById("n").value = window.location.href.split("?")[2];
        }
      }
      var tries = 10//number of x2s before give up
      var sumlength = 10//number of terms to dislplay in each series
      var minzero = 3//number of 0s needed at the end to assume infinite 0s
      var shift = 0;
      var iteration = 0;
      function calculate()
      {
        var sum = 0;
        var sumnum = "unknown";
        var series = [];
        var zerostreak = 0;
        var iterbegin = (parseInt(document.getElementById("n").value) || 0);
        if(iterbegin != document.getElementById("n").value){
          document.getElementById("n").value = 0;
        }
        iteration = iterbegin;
        var parse = document.getElementById("parsinator").value.toLowerCase().replace(/\s+/g, '');
        parse = parse.replace(/[^0-9.()*eÏ€xn\+\-\^\/]/g, "");
        if(parse != document.getElementById("parsinator").value.toLowerCase().replace(/\s+/g, '')){
          document.getElementById("parsinator").value = document.getElementById("parsinator").value.replace(/[^\s+0-9.()*eÏ€xn\+\-\^\/]/gi, "");
        }
        if(!(parse == "(-1)^n" && iterbegin == 0)){
          var add = "";
          if(iterbegin != 0){
            add = "?" + iterbegin;
          }
          window.history.pushState("", "", "./divergo.php?" + parse + add);
        }
        else if(window.location.href.split("?").length > 1){
          window.history.pushState("", "", "./divergo.php");
        }
        series[0] = [];
        for(var i = iteration; i < iterbegin + sumlength; i++){
          iteration = i;
          series[0].push(solve(parse));
          if(series[0][i] == 0){
            zerostreak++;
          }
          else{
            zerostreak = 0;
          }
        }
        if(series[0][series[0].length - 1] == 0 && zerostreak >= minzero){
          sumnum = 0;
          for(var i = 0; i < series[0].length; i++){
            sum += series[0][i];
          }
        }
        else{
          for(var i = 0; i < tries; i++){
            zerostreak = 0;
            series[i + 1] = [series[i][0]];
            for(var j = 1; j < series[i].length; j++){
              series[i + 1].push(series[i][j] + series[i][j - 1]);
              if(series[i + 1][j] == 0){
                zerostreak++;
              }
              else{
                zerostreak = 0;
              }
            }
            if(series[i + 1][series[i + 1].length - 1] == 0 && zerostreak >= minzero){
              sumnum = i + 1;
              for(var j = 0; j < series[i + 1].length; j++){
                sum += series[i + 1][j];
              }
              break;
            }
          }
        }
        var seriesstring = "";
        for(var i = 0; i < series.length; i++){
          seriesstring += "<span>";
          for(var j = 0; j < series[i].length; j++){
            if(series[i][j] < 0){
              seriesstring += "- ";
            }
            else if(j != 0){
              seriesstring += "+ ";
            }
            seriesstring += Math.abs(series[i][j]) + " ";
          }
          seriesstring += ". . .</span><br />";
          if(sumnum == "unknown"){
            seriesstring += '<span class="unknown answer">Unknown</span>';
            break;
          }
          if(i < series.length - 1){
            seriesstring += '<span class="x2">x 2 = </span>';
          }
        }
        if(sumnum != "unknown"){
          var pow = "<sup>" + sumnum + "</sup> ";
          if(sumnum == 1){
            pow = " ";
          }
          var beginning = sum + " / 2" + pow;
          if(sumnum == 0){
            beginning = "";
          }
          seriesstring += beginning + '= <span class="answer">' + sum * Math.pow(0.5, sumnum) + '</span>';
        }
        document.getElementById("show").innerHTML = seriesstring;
      }
      function operate(x, op, y){
        switch(op){
          case "+":
            return x + y;
          case "-":
            return x - y;
          case "*":
            return x * y;
          case "/":
            return x / y;
          case "^":
            return Math.pow(x, y);
        }
      }
      function solve(x){
        var sum = 0;
        var num = 0;
        var op = "+";
        for(var i = 0; i < x.length; i++){
          switch(x.charAt(i)){
            case "(":
              sum = operate(sum, op, solve(x.substr(i + 1)));
              i += shift + 1;
              op = "*";
              break;
            case "+":
              op = "+";
              break;
            case "-":
              op = "-";
              break;
            case "*":
            case "x":
              op = "*";
              break;
            case "/":
              op = "/";
              break;
            case "^":
              op = "^";
              break;
            case ")":
              shift = i;
              return sum;
            case "e":
              sum = operate(sum, op, Math.E);
              op = "*";
              break;
            case "Ï€":
              sum = operate(sum, op, Math.PI);
              op = "*";
              break;
            case "n":
              sum = operate(sum, op, iteration);
              op = "*";
              break;
            default:
              num = x.substr(i).split(/[()*eÏ€xn\+\-\^\/]/)[0];
              i += num.length - 1;
              num = (parseFloat(num) || 0);
              sum = operate(sum, op, num);
              op = "*";
          }
        }
        shift = i;
        return sum;
      }
    </script>
  </body>
</html>