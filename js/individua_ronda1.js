var Comet = Class.create();
  Comet.prototype = {
    lanzada: 0,
    url: 'class/listeners/listener_inicio_concurso.php',
    noerror: true,
    initialize: function() { },
    connect: function(){
      this.ajax = new Ajax.Request(this.url, {
        method: 'get',
        parameters: { 'lanzada' : this.lanzada , 
                      'ID_CONCURSO': document.getElementById("ID_CONCURSO").value,
                      'ID_RONDA': document.getElementById("ID_RONDA").value},
        onSuccess: function(transport) {
          // handle the server response
          var response = transport.responseText.evalJSON();
          this.comet.lanzada = response['lanzada'];
          this.comet.handleResponse(response);
          this.comet.noerror = true;
        },
        onComplete: function(transport) {
          // send a new ajax request when this request is finished
          if (!this.comet.noerror){
            // if a connection problem occurs, try to reconnect each 5 seconds
            setTimeout(function(){ comet.connect() }, 1000); 
          }
          else{
            console.log("connect");
            this.comet.connect();
          }
          this.comet.noerror = false;
        }
      });

      this.ajax.comet = this;
    },
    disconnect: function(){},
    handleResponse: function(response){
      console.log(response);
    }
  }
var comet = new Comet();
comet.connect();